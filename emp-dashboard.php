<?php
session_start();
include('db.php');
if (!isset($_SESSION['emp_login'])) {
  $_SESSION['emp_login'] = true;
$_SESSION['emp_name'] = $employee_name; 
    header("Location: emp-dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard | ORRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f5f6fa;
        }

        .wrapper {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            color: #ecf0f1;
            padding-top: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
        }

        .sidebar a {
            display: block;
            color: #bdc3c7;
            padding: 15px 20px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #34495e;
            color: #fff;
        }

        .main-content {
            flex: 1;
            padding: 20px 40px;
        }

        .top-bar {
            background: #fff;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .top-bar h3 {
            margin: 0;
            font-size: 22px;
            color: #2c3e50;
        }

        .card {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .card h4 {
            margin-bottom: 15px;
        }

        .stat {
            margin: 10px 0;
            font-size: 18px;
        }

        .logout {
            margin-top: 30px;
            text-align: center;
        }

        .logout a {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <h2>ORRS</h2>
        <a href="emp-dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="emp-manage-train.php"><i class="fas fa-train"></i> Trains</a>
        <a href="emp-manage-ticket.php"><i class="fas fa-ticket-alt"></i> Tickets</a>
        <a href="admin-manage-employee.php"><i class="fas fa-user-cog"></i> Employees</a>
        <div class="logout">
            <a href="emp-logout.php"><i class="fas fa-sign-out-alt"></i> Back home</a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h3>Welcome <?php echo $_SESSION['emp_name']; ?></h3>
        </div>

        <div class="card">
            <h4>Dashboard Overview</h4>
            <div class="stat"><i class="fas fa-train"></i> Total Trains: <strong>8</strong></div>
            <div class="stat"><i class="fas fa-ticket-alt"></i> Tickets Issued Today: <strong>120</strong></div>
            <div class="stat"><i class="fas fa-user-friends"></i> Employees: <strong>15</strong></div>
        </div>
    </div>
</div>
</body>
</html>
