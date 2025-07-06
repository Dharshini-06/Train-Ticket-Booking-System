<?php
include("db.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access.");
}

if (!isset($_POST['ticket_id'])) {
    die("Ticket ID is missing.");
}

$ticket_id = intval($_POST['ticket_id']);

// Fetch ticket details
$stmt = mysqli_prepare($mysqli, "SELECT id, train_number, journey_date, fare, seat_feature, status FROM orrs_ticket WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $ticket_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Ticket not found.");
}

$ticket = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($ticket['status'] === 'cancelled') {
    die("Ticket already cancelled.");
}

$fare = floatval($ticket['fare']);
$journey_date = $ticket['journey_date'];
$seat_feature = $ticket['seat_feature'];

// Calculate time difference from now to journey date/time
$now = new DateTime();
$journeyDateTime = new DateTime($journey_date . " 00:00:00"); // Adjust time if you have exact journey time

$interval = $now->diff($journeyDateTime);
$hours_to_departure = ($interval->days * 24) + $interval->h + ($interval->i / 60);

if ($interval->invert === 1) {
    die("Cannot cancel after journey date.");
}

// Cancellation policy
$refund_amount = 0;
$clerkage_charge = 50; // Clerkage charge for waitlisted/RAC (adjust if needed)
$flat_fee = 100; // Flat fee for confirmed tickets cancelled > 48 hrs before departure

if (in_array(strtolower($seat_feature), ['waitlisted', 'rac'])) {
    if ($hours_to_departure * 60 >= 30) {
        $refund_amount = max(0, $fare - $clerkage_charge);
    } else {
        die("Cannot cancel waitlisted/RAC ticket less than 30 minutes before departure.");
    }
} else {
    if ($hours_to_departure > 48) {
        $refund_amount = max(0, $fare - $flat_fee);
    } elseif ($hours_to_departure > 12) {
        $refund_amount = $fare * 0.75; // 25% cancellation charge
    } elseif ($hours_to_departure > 4) {
        $refund_amount = $fare * 0.50; // 50% cancellation charge
    } else {
        die("Cannot cancel ticket less than 4 hours before departure.");
    }
}

// Update ticket status to cancelled and set refund amount
$stmt = mysqli_prepare($mysqli, "UPDATE orrs_ticket SET status = 'cancelled', refund_amount = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "di", $refund_amount, $ticket_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Update reserved seats count in orrs_train
$stmt_count = mysqli_prepare($mysqli, "SELECT COUNT(*) FROM orrs_ticket WHERE train_number = ? AND status != 'cancelled'");
mysqli_stmt_bind_param($stmt_count, "s", $ticket['train_number']);
mysqli_stmt_execute($stmt_count);
mysqli_stmt_bind_result($stmt_count, $reserved_count);
mysqli_stmt_fetch($stmt_count);
mysqli_stmt_close($stmt_count);

$stmt_update = mysqli_prepare($mysqli, "UPDATE orrs_train SET reserved_seats = ? WHERE number = ?");
mysqli_stmt_bind_param($stmt_update, "is", $reserved_count, $ticket['train_number']);
mysqli_stmt_execute($stmt_update);
mysqli_stmt_close($stmt_update);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cancellation Confirmation</title>
    <style>
   body {
    font-family: Arial, sans-serif;
    background-image: url('assets/img/3d-rendering-old-trains-run-260nw-2310164565.jpg'); /* <-- paste your image URL here */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    padding: 40px;
    margin: 0;
}

    .container {
        max-width: 600px;
        margin: auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    h2 {
        color: #d9534f;
        text-align: center;
    }
    p {
        font-size: 18px;
        margin: 10px 0;
    }
    a.button {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 24px;
        background-color: #007bff;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        text-align: center;
    }
    a.button:hover {
        background-color: #0056b3;
    }

        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 40px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h2 { color: #d9534f; }
        p { font-size: 18px; }
        a.button { display: inline-block; margin-top: 20px; padding: 12px 24px; background-color: #007bff; color: white; border-radius: 5px; text-decoration: none; }
        a.button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Ticket Cancelled Successfully</h2>
        <p><strong>Train Number:</strong> <?php echo htmlspecialchars($ticket['train_number']); ?></p>
        <p><strong>Journey Date:</strong> <?php echo htmlspecialchars($ticket['journey_date']); ?></p>
        <p><strong>Seat Feature:</strong> <?php echo htmlspecialchars($ticket['seat_feature']); ?></p>
        <p><strong>Original Fare:</strong> ₹<?php echo number_format($fare, 2); ?></p>
        <p><strong>Refund Amount:</strong> ₹<?php echo number_format($refund_amount, 2); ?></p>
        <p>Your refund will be processed within 5-7 days.</p>
        
        <a href="pass-my-booked-train.php" class="button">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
mysqli_close($mysqli);
?>
