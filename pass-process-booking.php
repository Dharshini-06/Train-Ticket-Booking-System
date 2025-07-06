<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

// Fare calculator with age discount
function calculateFare($seatType, $seatFeaturesJson, $age) {
    $features = json_decode($seatFeaturesJson, true);
    $fare = 0;

    foreach ($features as $feature) {
        if (isset($feature['type']) && strtolower($feature['type']) === strtolower($seatType)) {
            $fare = $feature['price'];
            break;
        }
    }

    if ($fare === 0) {
        die("Invalid seat type or fare not found.");
    }

    // Apply age-based discount
    if ($age <= 5) {
        return 0;
    } elseif ($age < 12) {
        return $fare * 0.5;
    } elseif ($age >= 60) {
        return $fare * 0.7;
    } else {
        return $fare;
    }
}

// Get and validate inputs
$passenger_name = trim($_POST['passenger_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$passenger_age = trim($_POST['passenger_age'] ?? '');
$train_id = trim($_POST['train_id'] ?? '');
$seat_feature = trim($_POST['seat_feature'] ?? '');
$journey_date = $_POST['journey_date'] ?? '';
$food_required = $_POST['food_required'] ?? 'no';
$seat_inputs = $_POST['seat_number'] ?? [];

if (!is_array($seat_inputs) || empty($seat_inputs)) {
    die("Please select at least one seat.");
}

if (
    empty($passenger_name) || empty($email) || empty($train_id) ||
    empty($seat_feature) || empty($journey_date) || $passenger_age === ''
) {
    die("All fields are required.");
}

if (!is_numeric($passenger_age) || $passenger_age < 0 || $passenger_age > 120) {
    die("Invalid age.");
}

// Fetch train details
// Fetch train details including total_seats
$stmt = mysqli_prepare($mysqli, "SELECT name, current, destination, time, seat_features, food_cost, total_seats FROM orrs_train WHERE number = ?");
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($mysqli));
}
mysqli_stmt_bind_param($stmt, "s", $train_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (!$result || mysqli_num_rows($result) === 0) {
    die("Invalid train number.");
}
$train = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Begin transaction to ensure atomicity
mysqli_begin_transaction($mysqli);

try {
    $total_fare = 0;
    $_SESSION['ticket'] = [];

    foreach ($seat_inputs as $seat_input) {
        $parts = explode('-', $seat_input);
        if (count($parts) !== 2) {
            throw new Exception("Invalid seat format: $seat_input");
        }
        $coach_number = $parts[0];
        $seat_number = $parts[1];

        // Lock seat row for update to prevent race condition
        $check_stmt = mysqli_prepare($mysqli, "SELECT is_booked FROM orrs_seat WHERE train_id = ? AND coach_number = ? AND seat_number = ? FOR UPDATE");
        mysqli_stmt_bind_param($check_stmt, "sss", $train_id, $coach_number, $seat_number);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_bind_result($check_stmt, $is_booked);
        mysqli_stmt_fetch($check_stmt);
        mysqli_stmt_close($check_stmt);

        if ($is_booked) {
            throw new Exception("Seat $seat_input is already booked. Please select another seat.");
        }

        // Calculate fare for this seat
        $fare = calculateFare($seat_feature, $train['seat_features'], $passenger_age);

        // Add food cost if selected
        if ($food_required === 'yes') {
            $fare += (float)$train['food_cost'];
        }

        $total_fare += $fare;

        // Insert ticket
        $insert_stmt = mysqli_prepare($mysqli, "INSERT INTO orrs_ticket (passenger_name, email, age, train_number, coach_number, seat_number, seat_feature, fare, departure, arrival, journey_time, journey_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$insert_stmt) {
            throw new Exception("Prepare insert failed: " . mysqli_error($mysqli));
        }
        mysqli_stmt_bind_param($insert_stmt, "ssissssdssss",
            $passenger_name,
            $email,
            $passenger_age,
            $train_id,
            $coach_number,
            $seat_number,
            $seat_feature,
            $fare,
            $train['current'],
            $train['destination'],
            $train['time'],
            $journey_date
        );
        if (!mysqli_stmt_execute($insert_stmt)) {
            throw new Exception("Failed booking seat $seat_input: " . mysqli_error($mysqli));
        }
        mysqli_stmt_close($insert_stmt);

        // Mark seat as booked
        $update_stmt = mysqli_prepare($mysqli, "UPDATE orrs_seat SET is_booked = 1 WHERE train_id = ? AND coach_number = ? AND seat_number = ?");
        if (!$update_stmt) {
            throw new Exception("Prepare update failed: " . mysqli_error($mysqli));
        }
        mysqli_stmt_bind_param($update_stmt, "sss", $train_id, $coach_number, $seat_number);
        if (!mysqli_stmt_execute($update_stmt)) {
            throw new Exception("Failed updating seat $seat_input: " . mysqli_error($mysqli));
        }
        mysqli_stmt_close($update_stmt);

        // Add seat booking info to session
        $_SESSION['ticket'][] = [
            'passenger_name' => $passenger_name,
            'email' => $email,
            'passenger_age' => $passenger_age,
            'train_name' => $train['name'],
            'train_number' => $train_id,
            'coach_number' => $coach_number,
            'seat_number' => $seat_number,
            'seat_feature' => $seat_feature,
            'fare' => $fare,
            'departure' => $train['current'],
            'arrival' => $train['destination'],
            'time' => $train['time'],
            'journey_date' => $journey_date,
            'booking_date' => date('Y-m-d')
        ];
    }

    $_SESSION['total_fare'] = $total_fare;
    // Recalculate reserved seats from orrs_seat
$reserved_seats_stmt = mysqli_prepare($mysqli, "SELECT COUNT(*) FROM orrs_seat WHERE train_id = ? AND is_booked = 1");
mysqli_stmt_bind_param($reserved_seats_stmt, "s", $train_id);
mysqli_stmt_execute($reserved_seats_stmt);
mysqli_stmt_bind_result($reserved_seats_stmt, $reserved_seats);
mysqli_stmt_fetch($reserved_seats_stmt);
mysqli_stmt_close($reserved_seats_stmt);

// Get total seats directly from orrs_seat
$total_seats_stmt = mysqli_prepare($mysqli, "SELECT COUNT(*) FROM orrs_seat WHERE train_id = ?");
mysqli_stmt_bind_param($total_seats_stmt, "s", $train_id);
mysqli_stmt_execute($total_seats_stmt);
mysqli_stmt_bind_result($total_seats_stmt, $total_seats);
mysqli_stmt_fetch($total_seats_stmt);
mysqli_stmt_close($total_seats_stmt);

// Calculate available seats
$available_seats = $total_seats - $reserved_seats;
if ($available_seats < 0) {
    $available_seats = 0;
}

// Update all 3 values
$update_seats_stmt = mysqli_prepare($mysqli, "UPDATE orrs_train SET reserved_seats = ?, available_seats = ?, total_seats = ? WHERE number = ?");
mysqli_stmt_bind_param($update_seats_stmt, "iiis", $reserved_seats, $available_seats, $total_seats, $train_id);
mysqli_stmt_execute($update_seats_stmt);
mysqli_stmt_close($update_seats_stmt);

    mysqli_commit($mysqli);
} catch (Exception $e) {
    // Rollback on any error
    mysqli_rollback($mysqli);
    die("Booking failed: " . $e->getMessage());
}

mysqli_close($mysqli);

// Redirect to confirmation page
header("Location: pass-confirm-ticket.php");
exit();
?>
