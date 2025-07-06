<?php
include("db.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = intval($_POST['ticket_id']);
    $refund_amount = floatval($_POST['refund_amount']);
    $cancel_reason = trim($_POST['cancel_reason']);

    // Mark ticket as cancelled and record refund & reason
    $update_sql = "UPDATE orrs_tickets SET status = 'cancelled', refund_amount = ?, cancel_reason = ? WHERE ticket_id = ?";
    $stmt = mysqli_prepare($mysqli, $update_sql);
    mysqli_stmt_bind_param($stmt, "dsi", $refund_amount, $cancel_reason, $ticket_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        // Optional: Add refund record in payments/refunds table

        echo "<p>Ticket cancelled successfully. Refund amount: ₹" . number_format($refund_amount, 2) . "</p>";
        echo '<a href="pass-dashboard.php">Back to Dashboard</a>';
    } else {
        echo "<p>Failed to cancel ticket. Please try again.</p>";
    }
} else {
    echo "Invalid request.";
}
?>
