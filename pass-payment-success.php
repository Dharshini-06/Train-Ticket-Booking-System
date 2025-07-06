<?php
session_start();
include('assets/inc/checklogin.php');
check_login();

if (!isset($_SESSION['ticket']) || !is_array($_SESSION['ticket']) || empty($_SESSION['ticket']) || !isset($_SESSION['payment_status']) || $_SESSION['payment_status'] !== "Success") {
    echo "<script>alert('No payment found or payment not completed.'); window.location.href = 'pass-book-train.php';</script>";
    exit();
}

$tickets = $_SESSION['ticket'];
$payment_method = $_SESSION['payment_method'] ?? 'N/A';
$total_fare = $_SESSION['total_fare'] ?? 0.00;

// Optionally clear payment session data after displaying confirmation
unset($_SESSION['payment_status']);
unset($_SESSION['payment_method']);
unset($_SESSION['total_fare']);
unset($_SESSION['ticket']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Payment Successful</title>
    <link href="assets/lib/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: 
                linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                url('images/pngtree-photography-to-theme-railway-track-after-passing-train-on-railroad-image_15660549.png') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #1a1a1a;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .success-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            max-width: 700px;
            width: 100%;
            padding: 30px 40px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            text-align: center;
        }
        h2 {
            color: #0047bb;
            margin-bottom: 25px;
            font-weight: 700;
        }
        p {
            font-size: 17px;
            margin-bottom: 12px;
        }
        .btn-dashboard {
            background-color: #0047bb;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 40px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 30px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-dashboard:hover {
            background-color: #002f7a;
            color: white;
            text-decoration: none;
        }
        .details {
            text-align: left;
            margin-top: 20px;
            font-weight: 600;
            max-height: 300px;
            overflow-y: auto;
        }
        .ticket-block {
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .ticket-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .details span {
            font-weight: 400;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <h2>Payment Successful!</h2>
        <p>Thank you, <strong><?php echo htmlspecialchars($tickets[0]['passenger_name']); ?></strong>.</p>
        <p>Your payment via <strong><?php echo htmlspecialchars($payment_method); ?></strong> has been received.</p>
        <p>Your ticket(s) have been confirmed.</p>

        <div class="details">
            <?php foreach ($tickets as $index => $ticket): ?>
                <div class="ticket-block">
                    <p><strong>Train:</strong> <span><?php echo htmlspecialchars($ticket['train_name']); ?> (<?php echo htmlspecialchars($ticket['train_number']); ?>)</span></p>
                    <p><strong>Seat:</strong> <span><?php echo htmlspecialchars($ticket['coach_number'] . "-" . $ticket['seat_number']); ?> - <?php echo htmlspecialchars($ticket['seat_feature']); ?></span></p>
                    <p><strong>Fare Paid:</strong> <span>₹<?php echo number_format($ticket['fare'], 2); ?></span></p>
                    <p><strong>Date of Journey:</strong> <span><?php echo htmlspecialchars($ticket['journey_date']); ?></span></p>
                    <p><strong>Booking Date:</strong> <span><?php echo htmlspecialchars($ticket['booking_date']); ?></span></p>
                </div>
            <?php endforeach; ?>

            <p style="font-size:18px; font-weight:700;">Total Fare Paid: ₹<?php echo number_format($total_fare, 2); ?></p>
        </div>

        <a href="pass-dashboard.php" class="btn-dashboard">Back to Dashboard</a>
    </div>

    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
