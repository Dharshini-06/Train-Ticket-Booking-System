<?php
session_start();
include('db.php');
include('assets/inc/checklogin.php');
check_login();

$aid = $_SESSION['admin_id'];
$succ = $err = null;

if (isset($_POST['Update_profile_pic'])) {
    if (isset($_FILES['admin_dpic']) && $_FILES['admin_dpic']['error'] == 0) {
        $admin_dpic = basename($_FILES["admin_dpic"]["name"]);
        $target_dir = "assets/img/profile/";
        $target_file = $target_dir . $admin_dpic;

        // Move uploaded file
        if (move_uploaded_file($_FILES["admin_dpic"]["tmp_name"], $target_file)) {
            $query = "UPDATE orrs_admin SET admin_dpic = ? WHERE admin_id = ?";
            $stmt = $mysqli->prepare($query);

            if ($stmt) {
                $stmt->bind_param('si', $admin_dpic, $aid);
                $stmt->execute();
                $succ = "Profile Picture Updated";
            } else {
                $err = "SQL Error: " . $mysqli->error;
            }
        } else {
            $err = "Failed to upload image.";
        }
    } else {
        $err = "No file uploaded or upload error.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- Head -->
<?php include('assets/inc/head.php'); ?>
<!-- End Head -->

<body>
<div class="be-wrapper be-fixed-sidebar">
    <!-- Navigation Bar -->
    <?php include('assets/inc/navbar.php'); ?>
    <!-- Sidebar -->
    <?php include('assets/inc/sidebar.php'); ?>

    <div class="be-content">
        <div class="page-head">
            <h2 class="page-head-title">Profile</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb page-head-nav">
                    <li class="breadcrumb-item"><a href="emp-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Change Profile Photo</li>
                </ol>
            </nav>
        </div>

        <?php if ($succ): ?>
            <script>
                setTimeout(() => swal("Success!", "<?php echo $succ; ?>", "success"), 100);
            </script>
        <?php endif; ?>

        <?php if ($err): ?>
            <script>
                setTimeout(() => swal("Error!", "<?php echo $err; ?>", "error"), 100);
            </script>
        <?php endif; ?>

        <div class="main-content container-fluid">
            <?php
            $ret = "SELECT * FROM orrs_admin WHERE admin_id=?";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param('i', $aid);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_object()):
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-border-color card-border-color-success">
                            <div class="card-header card-header-divider">
                                Update Your Profile Photo
                                <span class="card-subtitle">Fill All Details</span>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <label class="col-12 col-sm-3 col-form-label text-sm-right">
                                            Select A New Profile Picture
                                        </label>
                                        <div class="col-12 col-sm-8 col-lg-6">
                                            <input type="file" name="admin_dpic" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-12 text-right">
                                            <button type="submit" name="Update_profile_pic" class="btn btn-success">Update Profile</button>
                                            <a href="emp-dashboard.php" class="btn btn-danger">Cancel</a>
                                        </div>
                                    </div>
                                </form>

                                <?php if (!empty($row->admin_dpic)): ?>
                                    <div class="form-group row">
                                        <div class="col-sm-12 text-center">
                                        <img src="assets/img/profile/<?php echo $row->admin_dpic; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">

                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/lib/jquery/jquery.min.js"></script>
    <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        $(document).ready(function () {
            App.init();
            App.formElements();
        });
    </script>
</body>
</html>
