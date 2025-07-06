<?php
session_start();
include('db.php');

$success = $error = "";

if (isset($_POST['register_admin'])) {
    $fname = $_POST['admin_fname'];
    $lname = $_POST['admin_lname'];
    $email = $_POST['admin_email'];
    $uname = $_POST['admin_uname'];
    $pwd = sha1(md5($_POST['admin_pwd'])); // Legacy hash

    // Check if email already exists
    $check = $mysqli->prepare("SELECT admin_email FROM orrs_admin WHERE admin_email = ?");
    $check->bind_param('s', $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Admin with this email already exists.";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO orrs_admin (admin_fname, admin_lname, admin_email, admin_uname, admin_pwd) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $fname, $lname, $email, $uname, $pwd);
        if ($stmt->execute()) {
            $success = "Admin registered successfully.";
        } else {
            $error = "Something went wrong.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register Admin</title>
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/lib/bootstrap/dist/css/bootstrap.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body class="be-splash-screen">
<div class="be-wrapper be-login">
    <div class="be-content">
        <div class="main-content container-fluid">
            <div class="splash-container">
                <div class="card card-border-color card-border-color-primary">
                    <div class="card-header"><span class="splash-description">Admin Registration</span></div>
                    <div class="card-body">

                        <?php if ($error): ?>
                            <script>swal("Failed", "<?= $error ?>", "error");</script>
                        <?php elseif ($success): ?>
                            <script>swal("Success", "<?= $success ?>", "success");</script>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <input class="form-control" name="admin_fname" type="text" placeholder="First Name" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="admin_lname" type="text" placeholder="Last Name" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="admin_email" type="email" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="admin_uname" type="text" placeholder="Username" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="admin_pwd" type="password" placeholder="Password" required>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit" name="register_admin">Register Admin</button>
                            </div>
                        </form>
                        <div class="splash-footer">Back <a href="emp-login.php">Login</a></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/lib/jquery/jquery.min.js"></script>
<script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
