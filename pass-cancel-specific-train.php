<?php
session_start();
include('db.php');
include('assets/inc/checklogin.php');
check_login();

$pass_email = $_SESSION['email']; // Based on email stored during login

if (!isset($_GET['ticket_id']) || empty($_GET['ticket_id'])) {
    header("Location: pass-cancel-train.php?msg=" . urlencode("Invalid ticket ID."));
    exit;
}

$ticket_id = intval($_GET['ticket_id']);

// 1. Fetch ticket details
$query = "SELECT * FROM orrs_ticket WHERE id = ? AND email = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('is', $ticket_id, $pass_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: pass-cancel-train.php?msg=" . urlencode("Ticket not found or unauthorized."));
    exit;
}

$ticket = $result->fetch_assoc();
$stmt->close();

$fare = floatval($ticket['fare']);
$seat_feature = strtolower($ticket['seat_feature']);
$departure_time_str = $ticket['journey_time'];
$train_number = $ticket['train_number'];
$train_name = $ticket['departure'] . " to " . $ticket['arrival'];

// Convert departure time to timestamp (based on date + time)
$journey_datetime_str = $ticket['journey_date'] . ' ' . $departure_time_str;
$departure_time = strtotime($journey_datetime_str);

if (!$departure_time) {
    header("Location: pass-cancel-train.php?msg=" . urlencode("Invalid departure time data."));
    exit;
}

$current_time = time();
$hours_before_departure = ($departure_time - $current_time) / 3600;

// Cancellation logic
$cancel_fee = 0;
$refund_amount = 0;
$clerkage_charge = 20;

if ($seat_feature === "waitlist" || $seat_feature === "rac") {
    if ($hours_before_departure >= 0.5) {
        $refund_amount = max($fare - $clerkage_charge, 0);
    } else {
        $refund_amount = 0;
    }
} else {
    if ($hours_before_departure > 48) {
        $cancel_fee = 50;
    } elseif ($hours_before_departure > 12) {
        $cancel_fee = 0.25 * $fare;
    } elseif ($hours_before_departure > 4) {
        $cancel_fee = 0.5 * $fare;
    } else {
        $cancel_fee = $fare;
    }
    $refund_amount = max($fare - $cancel_fee, 0);
}

// 2. Delete ticket
$del_query = "DELETE FROM orrs_ticket WHERE id = ? AND email = ?";
$del_stmt = $mysqli->prepare($del_query);
$del_stmt->bind_param('is', $ticket_id, $pass_email);
$del_stmt->execute();

if ($del_stmt->affected_rows === 1) {
    $del_stmt->close();

    // 3. Update reserved_seats in train table
    $update_query = "UPDATE orrs_train SET reserved_seats = GREATEST(reserved_seats - 1, 0) WHERE number = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('s', $train_number);
    $update_stmt->execute();
    $update_stmt->close();

    // 4. Prepare refund message
    $refund_message = "Ticket for '{$train_name}' cancelled successfully.";
    if ($refund_amount > 0) {
        $refund_message .= " Refund Amount: $" . number_format($refund_amount, 2);
    } else {
        $refund_message .= " No refund applicable.";
    }

    header("Location: pass-cancel-train.php?msg=" . urlencode($refund_message));
    exit;
} else {
    $del_stmt->close();
    header("Location: pass-cancel-train.php?msg=" . urlencode("Failed to cancel ticket. Try again."));
    exit;
}
?>
