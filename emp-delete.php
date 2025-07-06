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

// Fetch employee data before deletion
$stmt = $mysqli->prepare("SELECT emp_fname, emp_lname, emp_uname FROM orrs_employee WHERE emp_id = ?");
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$stmt->bind_result($fname, $lname, $uname);
$stmt->fetch();
$stmt->close();

// If POST request is made, proceed with deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete employee query
    $delete_stmt = $mysqli->prepare("DELETE FROM orrs_employee WHERE emp_id = ?");
    $delete_stmt->bind_param("i", $emp_id);

    if ($delete_stmt->execute()) {
        $success = "Employee " . htmlspecialchars($fname) . " " . htmlspecialchars($lname) . " (Username: " . htmlspecialchars($uname) . ") deleted successfully!";
        header("Refresh:2; url=emp-dash.php");  // Redirect to the employee dashboard after 2 seconds
    } else {
        $error = "Failed to delete employee.";
    }

    $delete_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Employee</title>
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
    .btn-danger {
      background-color: #e74c3c;
      border: none;
      border-radius: 10px;
      padding: 10px 20px;
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Delete Employee</h2>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php else: ?>
    <form method="POST">
      <div class="alert alert-warning text-center">
        Are you sure you want to delete the employee <strong><?= htmlspecialchars($fname) . " " . htmlspecialchars($lname) ?></strong> (Username: <?= htmlspecialchars($uname) ?>)?
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
        <a href="emp-dash.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  <?php endif; ?>
</div>

</body>
</html>
