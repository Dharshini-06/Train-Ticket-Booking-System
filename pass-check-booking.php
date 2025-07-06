<?php include('db.php'); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Check My Bookings</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <style>
    body {
      background: #f5f5f5;
      font-family: Arial, sans-serif;
    }

    .check-form {
      max-width: 450px;
      margin: 100px auto;
      padding: 30px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .check-form h2 {
      margin-bottom: 20px;
      text-align: center;
    }

    input[type="text"], input[type="email"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #007bff;
      color: white;
      font-size: 1rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background: #0056b3;
    }

    .error {
      color: red;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="check-form">
    <h2><span class="material-icons">search</span> Check Your Bookings</h2>
    <form action="pass-my-booked-train.php" method="POST">
      <input type="text" name="passenger_name" placeholder="Enter Your Name" required>
      <input type="email" name="passenger_email" placeholder="Enter Your Email" required>
      <button type="submit">View Booked Trains</button>
    </form>
  </div>
</body>
</html>
