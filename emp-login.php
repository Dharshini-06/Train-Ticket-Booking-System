<?php
session_start();
include('db.php');

$success = $error = "";

if (isset($_POST['emp_login'])) {
    $admin_email = $_POST['admin_email'];
    $admin_pwd = sha1(md5($_POST['admin_pwd'])); // legacy hash matching your db

    $stmt = $mysqli->prepare("SELECT admin_id, admin_fname, admin_lname, admin_uname FROM orrs_admin WHERE admin_email = ? AND admin_pwd = ?");
    $stmt->bind_param('ss', $admin_email, $admin_pwd);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($admin_id, $admin_fname, $admin_lname, $admin_uname);
        $stmt->fetch();
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['admin_name'] = $admin_uname;
        $success = "Login Successful";
        header("refresh:1;url=emp-dashboard.php");
    } else {
        $error = "Access Denied. Check your credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/lib/bootstrap/dist/css/bootstrap.min.css">
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <!-- Inline Style for Background Image -->
  <style>
    /* Add background image to the entire body (which has the 'be-splash-screen' class) */
    body.be-splash-screen {
      background-image: url("admin/assets/img/360_F_1055396548_IJFVICX3EmAld9WVN9i8wuBtp97n3YuK.png") /* Replace with the correct image path */
      background-size: cover;
      background-position: center center;
      background-repeat: no-repeat;
      height: 100vh; /* Ensure the background covers the entire viewport height */
      display: flex;
      justify-content: center;
      align-items: center;
    }

    /* Optional: Styling for the login container to make it stand out from the background */
    .splash-container {
      background-color: rgba(255, 255, 255, 0.8); /* Optional: Semi-transparent white background */
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Styling for the login button */
    .login-submit input[type="submit"] {
      background-color: #d9534f;
      color: white;
      border: none;
    }
  </style>
</head>
<body class="be-splash-screen">
  <div class="be-wrapper be-login">
    <div class="be-content">
      <div class="main-content container-fluid">
        <div class="splash-container">
          <div class="card card-border-color card-border-color-danger">
            <div class="card-header">
              <img class="logo-img" src="assets/img/logo-xx.png" alt="logo" width="120" height="30">
              <span class="splash-description">Admin Login Panel</span>
            </div>
            <div class="card-body">

              <?php if ($error): ?>
                <script>swal("Failed", "<?= $error ?>", "error");</script>
              <?php elseif ($success): ?>
                <script>swal("Success", "<?= $success ?>", "success");</script>
              <?php endif; ?>

              <form method="POST">
                <div class="login-form">
                  <div class="form-group">
                    <input class="form-control" name="admin_email" type="text" placeholder="Email" required>
                  </div>
                  <div class="form-group">
                    <input class="form-control" name="admin_pwd" type="password" placeholder="Password" required>
                  </div>
                  <div class="form-group row login-tools">
                    <div class="col-6 login-remember">
                      <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="check1">
                        <label class="custom-control-label" for="check1">Remember Me</label>
                      </div>
                    </div>
                    <div class="col-6 login-forgot-password">
                      <a href="../pass-pwd-forgot.php">Forgot Password?</a>
                    </div>
                  </div>
                  <div class="form-group row login-submit">
                    <div class="col-12">
                      <input type="submit" name="emp_login" class="btn btn-danger btn-xl btn-block" value="Log In">
                    </div>
                  </div>
                </div>
              </form>

              <div class="splash-footer">
                Back <a href="../index.php">Home</a> | 
                <a href="admin-register.php">Register New Admin</a>
              </div>

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
