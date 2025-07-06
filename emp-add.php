<?php
session_start();
include('../db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit();
}

$success = $error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form input data
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $natid = $_POST['natid'];
    $phone = $_POST['phone'];
    $addr = $_POST['addr'];
    $uname = $_POST['uname'];
    $email = $_POST['email'];
    $dept = $_POST['dept'];

    // Prepare SQL query to insert new employee
    $stmt = $mysqli->prepare("INSERT INTO orrs_employee (emp_fname, emp_lname, emp_nat_idno, emp_phone, emp_addr, emp_uname, emp_email, emp_dept) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $fname, $lname, $natid, $phone, $addr, $uname, $email, $dept);

    if ($stmt->execute()) {
        $success = "Employee added successfully!";
        header("Refresh:2; url=emp-dash.php");  // Redirect to employee dashboard after 2 seconds
    } else {
        $error = "Failed to add employee.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Employee</title>
  <link rel="stylesheet" href="assets/lib/bootstrap/dist/css/bootstrap.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 650px;
      margin: 60px auto;
      padding: 40px;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
    }
    h2 {
      font-size: 28px;
      font-weight: bold;
      color: #2c3e50;
      margin-bottom: 25px;
      text-align: center;
    }
    .alert {
      border-radius: 8px;
      font-size: 14px;
      text-align: center;
    }
    label {
      font-weight: 600;
      color: #34495e;
    }
    .form-control {
      border-radius: 10px;
      padding: 10px 12px;
    }
    .mb-4 {
      margin-bottom: 24px;
    }
    .btn-primary {
      background-color: #007bff;
      border: none;
      border-radius: 10px;
      padding: 10px 20px;
      font-weight: 600;
    }
    .btn-secondary {
      background-color: #6c757d;
      border: none;
      border-radius: 10px;
      padding: 10px 20px;
      font-weight: 600;
      margin-left: 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Add New Employee</h2>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-4">
      <label>First Name</label>
      <input type="text" name="fname" class="form-control" required>
    </div>
    <div class="mb-4">
      <label>Last Name</label>
      <input type="text" name="lname" class="form-control" required>
    </div>
    <div class="mb-4">
      <label>National ID</label>
      <input type="text" name="natid" class="form-control" required>
    </div>
    <div class="mb-4">
      <label>Phone</label>
      <input type="text" name="phone" class="form-control" required>
    </div>
    <div class="mb-4">
      <label>Address</label>
      <input type="text" name="addr" class="form-control" required>
    </div>
    <div class="mb-4">
      <label>Username</label>
      <input type="text" name="uname" class="form-control" required>
    </div>
    <div class="mb-4">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-4">
      <label>Department</label>
      <input type="text" name="dept" class="form-control" required>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-primary">Add Employee</button>
      <a href="emp-dash.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

</body>
</html>
