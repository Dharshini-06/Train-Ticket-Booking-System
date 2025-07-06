<?php
session_start();
include('../db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit();
}

// Fetch employees from the database
$query = "SELECT emp_id, emp_fname, emp_lname, emp_uname FROM orrs_employee";
$result = mysqli_query($mysqli, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee List</title>
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
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
    .btn-danger {
      background-color: #e74c3c;
      border: none;
      border-radius: 10px;
      padding: 5px 15px;
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Employee List</h2>

  <table border="1">
    <tr>
      <th>ID</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Username</th>
      <th>Action</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= htmlspecialchars($row['emp_id']) ?></td>
        <td><?= htmlspecialchars($row['emp_fname']) ?></td>
        <td><?= htmlspecialchars($row['emp_lname']) ?></td>
        <td><?= htmlspecialchars($row['emp_uname']) ?></td>
        <td>
          <a href="delete_employee.php?emp_id=<?= $row['emp_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
        </td>
      </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>
