<?php
session_start();
include('../db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit();
}

if (!isset($_GET['emp_id'])) {
    echo "No employee selected.";
    exit();
}

$emp_id = intval($_GET['emp_id']);
$success = $error = "";

// Fetch employee data
$stmt = $mysqli->prepare("SELECT emp_fname, emp_lname, emp_nat_idno, emp_phone, emp_addr, emp_uname, emp_email, emp_dept FROM orrs_employee WHERE emp_id = ?");
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$stmt->bind_result($fname, $lname, $natid, $phone, $addr, $uname, $email, $dept);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_fname = $_POST['fname'];
    $new_lname = $_POST['lname'];
    $new_natid = $_POST['natid'];
    $new_phone = $_POST['phone'];
    $new_addr = $_POST['addr'];
    $new_uname = $_POST['uname'];
    $new_email = $_POST['email'];
    $new_dept = $_POST['dept'];

    $update_stmt = $mysqli->prepare("UPDATE orrs_employee SET emp_fname = ?, emp_lname = ?, emp_nat_idno = ?, emp_phone = ?, emp_addr = ?, emp_uname = ?, emp_email = ?, emp_dept = ? WHERE emp_id = ?");
    $update_stmt->bind_param("ssssssssi", $new_fname, $new_lname, $new_natid, $new_phone, $new_addr, $new_uname, $new_email, $new_dept, $emp_id);

    if ($update_stmt->execute()) {
        $success = "Employee updated successfully!";
        header("Refresh:2; url=emp-dash.php");
    } else {
        $error = "Failed to update employee.";
    }

    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Employee</title>
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
    label {
      font-weight: 600;
      color: #34495e;
    }
    .form-control {
      border-radius: 10px;
      padding: 10px 12px;
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
    .alert {
      border-radius: 8px;
      font-size: 14px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Update Employee</h2>

  <?php if ($success): ?>
    <div class="alert alert-success text-center"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label>First Name</label>
      <input type="text" name="fname" class="form-control" value="<?= htmlspecialchars($fname) ?>" required>
    </div>
    <div class="mb-3">
      <label>Last Name</label>
      <input type="text" name="lname" class="form-control" value="<?= htmlspecialchars($lname) ?>" required>
    </div>
    <div class="mb-3">
      <label>National ID</label>
      <input type="text" name="natid" class="form-control" value="<?= htmlspecialchars($natid) ?>" required>
    </div>
    <div class="mb-3">
      <label>Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" required>
    </div>
    <div class="mb-3">
      <label>Address</label>
      <input type="text" name="addr" class="form-control" value="<?= htmlspecialchars($addr) ?>" required>
    </div>
    <div class="mb-3">
      <label>Username</label>
      <input type="text" name="uname" class="form-control" value="<?= htmlspecialchars($uname) ?>" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
    </div>
    <div class="mb-3">
      <label>Department</label>
      <input type="text" name="dept" class="form-control" value="<?= htmlspecialchars($dept) ?>" required>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-primary">Update</button>
      <a href="emp-dash.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

</body>
</html>
