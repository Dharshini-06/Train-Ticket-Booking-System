<?php
session_start();
include('db.php');
include('assets/inc/checklogin.php');
check_login();

if (!isset($_SESSION['ticket']) || empty($_SESSION['ticket'])) {
    echo "<script>alert('No booking information found. Please book a ticket first.'); window.location.href = 'pass-book-train.php';</script>";
    exit();
}

$tickets = $_SESSION['ticket'];
$total_fare = $_SESSION['total_fare'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Booking Confirmation</title>
    <link href="assets/lib/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: 
                linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('images/pngtree-photography-to-theme-railway-track-after-passing-train-on-railroad-image_15660549.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
            color: white;
        }

        .confirmation-card {
            background-color: rgba(255, 255, 255, 0.95);
            color: #1a1a1a;
            max-width: 800px;
            width: 100%;
            border-radius: 15px;
            padding: 30px 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .confirmation-card h3 {
            color: #0047bb;
            margin-bottom: 30px;
            font-weight: 700;
            text-align: center;
        }

        .confirmation-card p {
            font-size: 17px;
            margin-bottom: 10px;
        }

        .ticket {
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .btn-group {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-custom {
            background-color: #0047bb;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #002f7a;
        }

        .btn-icon svg {
            margin-right: 8px;
            vertical-align: middle;
        }

        @media (max-width: 700px) {
            .confirmation-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <h3>Booking Confirmation</h3>

        <?php foreach ($tickets as $index => $ticket): ?>
            <div class="ticket">
                <p><strong>Passenger Name:</strong> <?= htmlspecialchars($ticket['passenger_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($ticket['email']) ?></p>
                <p><strong>Age:</strong> <?= htmlspecialchars($ticket['passenger_age']) ?></p>
                <p><strong>Train Name:</strong> <?= htmlspecialchars($ticket['train_name']) ?></p>
                <p><strong>Train Number (ID):</strong> <?= htmlspecialchars($ticket['train_number'] ?? 'N/A') ?></p>
                <p><strong>Coach Number:</strong> <?= htmlspecialchars($ticket['coach_number'] ?? 'Not Assigned') ?></p>
                <p><strong>Seat Number:</strong> <?= htmlspecialchars($ticket['seat_number']) ?></p>
                <p><strong>Seat Type:</strong> <?= htmlspecialchars($ticket['seat_feature']) ?></p>
                <p><strong>Fare:</strong> ₹<?= number_format($ticket['fare'], 2) ?></p>
                <p><strong>Departure Station:</strong> <?= htmlspecialchars($ticket['departure']) ?></p>
                <p><strong>Arrival Station:</strong> <?= htmlspecialchars($ticket['arrival']) ?></p>
                <p><strong>Date of Journey:</strong> <?= htmlspecialchars($ticket['journey_date']) ?></p>
                <p><strong>Booking Date:</strong> <?= htmlspecialchars($ticket['booking_date'] ?? 'N/A') ?></p>
            </div>
        <?php endforeach; ?>

        <h4><strong>Total Fare:</strong> ₹<?= number_format($total_fare, 2) ?></h4>

        <form method="POST" action="process-payment.php" class="btn-group">
            <button type="submit" class="btn-custom">Proceed to Pay</button>
            <a href="pass-dashboard.php" class="btn-custom btn-icon">
                <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 24 24" width="20" fill="white">
                    <path d="M0 0h24v24H0z" fill="none"/>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                </svg>
                Back to Dashboard
            </a>
        </form>
    </div>

    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
