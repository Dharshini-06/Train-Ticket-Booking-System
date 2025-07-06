<?php
session_start();
include('db.php');

if (isset($_POST['pass_login'])) {
    $pass_email = $_POST['pass_email'];
    $pass_pwd = $_POST['pass_pwd']; // plain password from form

    // Prepare statement to get user info from DB by email
    $sql = "SELECT pass_id, pass_pwd FROM orrs_passenger WHERE pass_email = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        die("SQL error: " . $mysqli->error);
    }

    $stmt->bind_param('s', $pass_email);
    $stmt->execute();
    $stmt->bind_result($pass_id, $stored_hash);
    $stmt->fetch();

    if ($pass_id) {
        // Verify password with password_verify()
        if (password_verify($pass_pwd, $stored_hash)) {
            session_regenerate_id(true); // prevent session fixation
            $_SESSION['pass_id'] = $pass_id;
            header("Location: pass-dashboard.php");
            exit();
        } else {
            $error = "Access Denied. Please Check Your Credentials";
        }
    } else {
        $error = "Access Denied. Please Check Your Credentials";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Passenger Login - Online Railway Reservation System</title>
    <link rel="stylesheet" href="assets/lib/perfect-scrollbar/css/perfect-scrollbar.css"/>
    <link rel="stylesheet" href="assets/lib/material-design-icons/css/material-design-iconic-font.min.css"/>
    <link rel="stylesheet" href="assets/css/app.css" type="text/css"/>
    <?php if (isset($error)) { ?>
    <script>
        setTimeout(function () {
            swal("Failed!", "<?php echo $error; ?>", "error");
        }, 100);
    </script>
    <?php } ?>
</head>
<body class="be-splash-screen" style="background: url('assets/img/pic04.jpg') no-repeat center center fixed; background-size: cover;">
    <div class="be-wrapper be-login">
        <div class="be-content">
            <div class="main-content container-fluid">
                <div class="splash-container">
                    <div class="card card-border-color card-border-color-success">
                        <div class="card-header">
                            <img class="logo-img" src="assets/img/logo-xx.png" alt="logo" width="100" height="27">
                            <span class="splash-description">Please enter your user information.</span>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="login-form">
                                    <div class="form-group">
                                        <input class="form-control" name="pass_email" type="email" placeholder="Email" autocomplete="off" required>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" name="pass_pwd" type="password" placeholder="Password" required>
                                    </div>
                                    <div class="form-group row login-tools">
                                        <div class="col-6 login-remember">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="check1">
                                                <label class="custom-control-label" for="check1">Remember Me</label>
                                            </div>
                                        </div>
                                        <div class="col-6 login-forgot-password"><a href="pass-pwd-forgot.php">Forgot Password?</a></div>
                                    </div>
                                    <div class="form-group row login-submit">
                                        <div class="col-6"><a class="btn btn-danger btn-xl" href="pass-signup.php">Register</a></div>
                                        <div class="col-6"><input type="submit" name="pass_login" class="btn btn-success btn-xl" value="Log In"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="splash-footer"><a href="index.php">Back Home</a></div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/lib/jquery/jquery.min.js"></script>
    <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/swal.js"></script>
    <script>
      $(document).ready(function(){
          App.init();
      });
    </script>
</body>
</html>
