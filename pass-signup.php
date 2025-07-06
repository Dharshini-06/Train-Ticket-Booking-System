<?php
session_start();
include('db.php');

$err = '';
$success = '';

if (isset($_POST['pass_register'])) {
    $pass_fname = trim($_POST['pass_fname']);
    $pass_lname = trim($_POST['pass_lname']);
    $pass_email = trim($_POST['pass_email']);
    $pass_pwd_raw = $_POST['pass_pwd'];

    // Basic validation
    if (empty($pass_fname) || empty($pass_lname) || empty($pass_email) || empty($pass_pwd_raw)) {
        $err = "Please fill in all required fields.";
    } elseif (!filter_var($pass_email, FILTER_VALIDATE_EMAIL)) {
        $err = "Please enter a valid email address.";
    } else {
        // Hash password securely
        $pass_pwd = password_hash($_POST['pass_pwd'], PASSWORD_DEFAULT);


        // Prepare insert query
        $query = "INSERT INTO orrs_passenger (pass_fname, pass_lname, pass_email, pass_pwd) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);

        if (!$stmt) {
            $err = "Database error: " . $mysqli->error;
        } else {
            $stmt->bind_param('ssss', $pass_fname, $pass_lname, $pass_email, $pass_pwd);
            $exec = $stmt->execute();

            if ($exec) {
                $pass_id = $mysqli->insert_id;
                $_SESSION['pass_id'] = $pass_id;
                $_SESSION['pass_uname'] = $pass_fname; 
                $_SESSION['pass_email'] = $pass_email;

                $success = "Account created successfully! Redirecting to dashboard...";
                header("refresh:3;url=pass-dashboard.php");
            } else {
                $err = "Registration failed: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Passenger Registration - ORRS</title>
  <link rel="stylesheet" href="assets/lib/perfect-scrollbar/css/perfect-scrollbar.css" />
  <link rel="stylesheet" href="assets/lib/material-design-icons/css/material-design-iconic-font.min.css" />
  <link rel="stylesheet" href="assets/css/app.css" />
</head>
<body class="be-splash-screen">
  <div class="be-wrapper be-login">
    <div class="be-content">
      <div class="main-content container-fluid">
        <div class="splash-container">
          <div class="card card-border-color card-border-color-success">
            <div class="card-header">
              <img class="logo-img" src="assets/img/logo-xx.png" alt="logo" width="150" height="27" />
              <span class="splash-description">Passenger Registration Form</span>
            </div>
            <div class="card-body">

              <?php if ($success): ?>
                <script>
                  setTimeout(() => swal("Success!", "<?php echo $success; ?>", "success"), 100);
                </script>
              <?php endif; ?>

              <?php if ($err): ?>
                <script>
                  setTimeout(() => swal("Failed!", "<?php echo $err; ?>", "error"), 100);
                </script>
              <?php endif; ?>

              <form method="POST" novalidate>
                <div class="login-form">
                  <div class="form-group">
                    <input class="form-control" name="pass_fname" type="text" placeholder="First Name" autocomplete="off" required />
                  </div>
                  <div class="form-group">
                    <input class="form-control" name="pass_lname" type="text" placeholder="Last Name" autocomplete="off" required />
                  </div>
                  <div class="form-group">
                    <input class="form-control" name="pass_email" type="email" placeholder="Email Address" autocomplete="off" required />
                  </div>
                  <div class="form-group">
                    <input class="form-control" name="pass_pwd" type="password" placeholder="Password" required />
                  </div>
                  
                    <div class="col-6">
                      <input type="submit" name="pass_register" class="btn btn-outline-danger btn-xl" value="Register" />
                    </div>
                  </div>
                </div>
              </form>

            </div>
          </div>
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
    $(document).ready(() => App.init());
  </script>
</body>
</html>
