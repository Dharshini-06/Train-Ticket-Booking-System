<?php
session_start();
include('db.php');
include('assets/inc/checklogin.php');
check_login();

$passenger_id = $_SESSION['pass_id'];

// Validate input
if (!isset($_POST['train_id'], $_POST['class'], $_POST['seats'])) {
    die("<div class='error'>Missing booking data.</div>");
}

$train_id = intval($_POST['train_id']);
$class = $_POST['class'];
$seats_requested = intval($_POST['seats']);

// Fetch train data
$query = "SELECT * FROM orrs_train WHERE id=?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $train_id);
$stmt->execute();
$res = $stmt->get_result();
$train = $res->fetch_object();

if (!$train) {
    die("<div class='error'>Train not found.</div>");
}

// Decode seat features
$features = json_decode($train->seat_features, true);

// Locate selected class
$selected_class = null;
foreach ($features as $feature) {
    if ($feature['type'] === $class) {
        $selected_class = $feature;
        break;
    }
}

if (!$selected_class) {
    die("<div class='error'>Invalid class selected.</div>");
}

$available_seats = $selected_class['unreserved'];
$fare_per_seat = $selected_class['price'];

if ($available_seats < $seats_requested) {
    die("<div class='error'>Only $available_seats seats available in $class class.</div>");
}

// Update seat data
$new_reserved = $selected_class['reserved'] + $seats_requested;
$new_unreserved = $selected_class['unreserved'] - $seats_requested;

foreach ($features as &$f) {
    if ($f['type'] === $class) {
        $f['reserved'] = $new_reserved;
        $f['unreserved'] = $new_unreserved;
    }
}
$updated_features_json = json_encode($features);

// Update train record
$update = "UPDATE orrs_train SET seat_features=? WHERE id=?";
$update_stmt = $mysqli->prepare($update);
if (!$update_stmt) {
    die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
}

$update_stmt->bind_param('si', $updated_features_json, $train_id);
$update_stmt->execute();


// Insert booking record
$total_fare = $fare_per_seat * $seats_requested;
$booking_sql = "INSERT INTO orrs_booking (passenger_id, train_id, class, seats, fare, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
$booking_stmt = $mysqli->prepare($booking_sql);
$booking_stmt->bind_param('iisid', $passenger_id, $train_id, $class, $seats_requested, $total_fare);
$booking_stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Confirmation</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: #f8f9fa;
      padding: 40px;
    }
    .card {
      max-width: 600px;
      margin: auto;
      background: white;
      border: 1px solid #ddd;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #28a745;
      margin-bottom: 20px;
      text-align: center;
    }
    .details p {
      font-size: 16px;
      margin: 10px 0;
    }
    .btn {
      display: inline-block;
      margin-top: 25px;
      padding: 10px 20px;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: background 0.3s;
      text-align: center;
    }
    .btn:hover {
      background: #0056b3;
    }
    .error {
      color: red;
      font-weight: bold;
      padding: 20px;
      background: #ffe6e6;
      border: 1px solid #ff0000;
      border-radius: 8px;
      margin: 30px auto;
      max-width: 500px;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="card">
    <h2>Booking Confirmed</h2>
    <div class="details">
      <p><strong>Passenger ID:</strong> <?php echo $passenger_id; ?></p>
      <p><strong>Train:</strong> <?php echo $train->name . " ({$train->number})"; ?></p>
      <p><strong>Route:</strong> <?php echo $train->route . " (" . $train->current . " → " . $train->destination . ")"; ?></p>
      <p><strong>Class:</strong> <?php echo $class; ?></p>
      <p><strong>Seats Booked:</strong> <?php echo $seats_requested; ?></p>
      <p><strong>Fare per Seat:</strong> ₹<?php echo number_format($fare_per_seat, 2); ?></p>
      <p><strong>Total Fare:</strong> ₹<?php echo number_format($total_fare, 2); ?></p>
    </div>
    <div style="text-align: center;">
      <a href="pass-dashboard.php" class="btn">Return to Dashboard</a>
    </div>
  </div>

</body>
</html>
