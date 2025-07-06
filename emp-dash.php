<?php
session_start();
include('../db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit();
}

// Extended employee data query
$stmt = $mysqli->prepare("SELECT emp_id, emp_fname, emp_lname, emp_email, emp_phone, emp_nat_idno, emp_addr, emp_uname, emp_dept FROM orrs_employee");
if ($stmt === false) {
    die('MySQL Error: ' . $mysqli->error);
}
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($emp_id, $emp_fname, $emp_lname, $emp_email, $emp_phone, $emp_natid, $emp_addr, $emp_uname, $emp_dept);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/lib/bootstrap/dist/css/bootstrap.min.css">
  <style>
  body {
    background-image: url('../assets/img/dashboard-bg.jpg');
    background-color: #f0f4f8;
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .container {
    max-width: 1200px;
    background-color: #ffffff;
    padding: 40px;
    border-radius: 20px;
    margin: 50px auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }

  h2 {
    color: #004085;
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 20px;
  }

  h3 {
    color: #0056b3;
    font-size: 1.8rem;
    font-weight: 600;
    margin-top: 30px;
    margin-bottom: 20px;
  }

  nav a {
    color: #007bff;
    margin-right: 20px;
    font-weight: 500;
    text-decoration: none;
  }

  nav a:hover {
    text-decoration: underline;
  }

  table {
    background-color: white;
    color: #333;
    font-size: 0.95rem;
  }

  table th {
    background-color: #007bff;
    color: white;
    vertical-align: middle;
  }

  .btn-warning {
    background-color: #ffc107;
    color: black;
    border: none;
  }

  .btn-danger {
    background-color: #dc3545;
    border: none;
  }

  .btn-warning:hover, .btn-danger:hover {
    opacity: 0.9;
  }

  td, th {
    vertical-align: middle !important;
    text-align: center;
  }
</style>
</head>
<body>
  <div class="container">
    <h2>Welcome, <?= $_SESSION['admin_name']; ?></h2>
    <nav>
      <a href="emp-view.php">View Employees</a>
      <a href="emp-add.php">Add Employee</a>
      <a href="admin-logout.php">Logout</a>
    </nav>

    <h3>Employee Management</h3>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>National ID</th>
          <th>Address</th>
          <th>Username</th>
          <th>Department</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($stmt->fetch()): ?>
        <tr>
          <td><?= htmlspecialchars($emp_fname) ?></td>
          <td><?= htmlspecialchars($emp_lname) ?></td>
          <td><?= htmlspecialchars($emp_email) ?></td>
          <td><?= htmlspecialchars($emp_phone) ?></td>
          <td><?= htmlspecialchars($emp_natid) ?></td>
          <td><?= htmlspecialchars($emp_addr) ?></td>
          <td><?= htmlspecialchars($emp_uname) ?></td>
          <td><?= htmlspecialchars($emp_dept) ?></td>
          <td>
            <a href="emp-update.php?emp_id=<?= $emp_id ?>" class="btn btn-warning">Update</a>
            <a href="emp-delete.php?emp_id=<?= $emp_id ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <script src="assets/lib/jquery/jquery.min.js"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
