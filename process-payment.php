<?php
session_start();
include('db.php');
include('assets/inc/checklogin.php');
check_login();

// Ensure ticket data exists
if (!isset($_SESSION['ticket'])) {
    echo "<script>alert('No booking found. Please book a ticket first.'); window.location.href='pass-book-train.php';</script>";
    exit();
}

$ticket = $_SESSION['ticket'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    
    // Basic validation
    if (!$payment_method) {
        $error = "Please select a payment method.";
    } else {
        // Here you would normally process the payment according to method
        // For demo, just simulate success and redirect to thank you or dashboard

        // Example: save payment info or update booking status in DB if needed

        $_SESSION['payment_status'] = "Success";
        $_SESSION['payment_method'] = $payment_method;

        header("Location: pass-payment-success.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Payment - Booking #<?php echo htmlspecialchars($ticket['train_number']); ?></title>
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
        .payment-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            max-width: 500px;
            width: 100%;
            padding: 30px 40px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }
        h3 {
            color: #0047bb;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }
        label.payment-option {
            display: block;
            padding: 12px 15px;
            border: 2px solid #0047bb;
            border-radius: 12px;
            margin-bottom: 15px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        label.payment-option:hover {
            background-color: #0047bb;
            color: white;
        }
        input[type="radio"] {
            display: none;
        }
        input[type="radio"]:checked + label.payment-option {
            background-color: #0047bb;
            color: white;
            border-color: #002f7a;
        }
        .qr-code {
            text-align: center;
            margin-top: 15px;
        }
        .btn-pay {
            background-color: #0047bb;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 40px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .btn-pay:hover {
            background-color: #002f7a;
        }
        .error-msg {
            color: red;
            margin-bottom: 15px;
            font-weight: 600;
            text-align: center;
        }
        .fare-info {
            font-size: 18px;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
        }
    </style>

    <script>
        function toggleQRCode() {
            var qrDiv = document.getElementById('qrCodeDiv');
            var qrRadio = document.getElementById('payment_qr');
            if(qrRadio.checked) {
                qrDiv.style.display = 'block';
            } else {
                qrDiv.style.display = 'none';
            }
        }
        window.onload = function() {
            toggleQRCode();
            // Add event listeners for radio buttons to toggle QR code
            var radios = document.querySelectorAll('input[name="payment_method"]');
            radios.forEach(function(radio){
                radio.addEventListener('change', toggleQRCode);
            });
        }
    </script>
</head>
<body>
    <div class="payment-card">
        <h3>Make Payment</h3>

        Total Amount: ₹<?php echo number_format($_SESSION['total_fare'] ?? 0, 2); ?>

        <?php if(!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="process-payment.php">
            <input type="radio" id="payment_card" name="payment_method" value="Card" <?php if(isset($payment_method) && $payment_method == 'Card') echo 'checked'; ?> />
            <label for="payment_card" class="payment-option">Credit/Debit Card</label>

            <input type="radio" id="payment_gpay" name="payment_method" value="Google Pay" <?php if(isset($payment_method) && $payment_method == 'Google Pay') echo 'checked'; ?> />
            <label for="payment_gpay" class="payment-option">Google Pay</label>

            <input type="radio" id="payment_paytm" name="payment_method" value="Paytm" <?php if(isset($payment_method) && $payment_method == 'Paytm') echo 'checked'; ?> />
            <label for="payment_paytm" class="payment-option">Paytm</label>

            <input type="radio" id="payment_qr" name="payment_method" value="QR Code" <?php if(isset($payment_method) && $payment_method == 'QR Code') echo 'checked'; ?> />
            <label for="payment_qr" class="payment-option">Pay via QR Code</label>

            <div id="qrCodeDiv" class="qr-code" style="display:none;">
                <p>Scan this QR code to pay:</p>
                <img src="images/1200px-QR_Code_Example.svg.png" alt="QR Code" width="180" height="180" />
                <!-- Replace sample-qr-code.png with your actual QR code image -->
            </div>

            <button type="submit" class="btn-pay">Proceed to Pay</button>
        </form>
    </div>

    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
