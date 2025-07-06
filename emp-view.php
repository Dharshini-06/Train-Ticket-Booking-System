<?php
session_start();

// Include database connection
$db_path = dirname(__DIR__) . "/db.php"; // Adjust path to root/db.php
if (file_exists($db_path)) {
    include($db_path);
} else {
    die("Error: Database file not found.");
}

$success = $err = null;

// Handle profile update
if (isset($_POST['update_profile'])) {
    if (isset($_GET['emp_id'])) {
        $emp_id = $_GET['emp_id'];
        $emp_fname = $_POST['emp_fname'];
        $emp_lname = $_POST['emp_lname'];
        $emp_nat_idno = $_POST['emp_nat_idno'];
        $emp_phone = $_POST['emp_phone'];
        $emp_addr = $_POST['emp_addr'];
        $emp_uname = $_POST['emp_uname'];
        $emp_email = $_POST['emp_email'];
        $emp_dept = $_POST['emp_dept'];
        $emp_pwd = sha1(md5($_POST['emp_pwd']));

        $query = "UPDATE orrs_employee SET emp_fname=?, emp_lname=?, emp_phone=?, emp_addr=?, emp_nat_idno=?, emp_uname=?, emp_email=?, emp_dept=?, emp_pwd=? WHERE emp_id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sssssssssi', $emp_fname, $emp_lname, $emp_phone, $emp_addr, $emp_nat_idno, $emp_uname, $emp_email, $emp_dept, $emp_pwd, $emp_id);
        $stmt->execute();

        if ($stmt) {
            $success = "Employee Account Updated";
        } else {
            $err = "Please Try Again Or Try Later";
        }
    } else {
        $err = "Employee ID not provided.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php'); ?>
<!--End Head-->
<body>
    <div class="be-wrapper be-fixed-sidebar">
        <!--Navigation Bar-->
        <?php include('assets/inc/navbar.php'); ?>
        <!--Sidebar-->
        <?php include('assets/inc/sidebar.php'); ?>

        <div class="be-content">
            <div class="page-head">
                <h2 class="page-head-title">View Employee</h2>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb page-head-nav">
                        <li class="breadcrumb-item"><a href="pass-dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Employee</a></li>
                        <li class="breadcrumb-item active">Manage</li>
                    </ol>
                </nav>
            </div>

            <?php if (isset($success)) { ?>
                <script>
                    setTimeout(function () {
                        swal("Success!", "<?php echo $success; ?>", "success");
                    }, 100);
                </script>
            <?php } ?>

            <?php if (isset($err)) { ?>
                <script>
                    setTimeout(function () {
                        swal("Failed!", "<?php echo $err; ?>", "error");
                    }, 100);
                </script>
            <?php } ?>

            <div class="main-content container-fluid">
                <?php
                if (isset($_GET['emp_id'])) {
                    $aid = $_GET['emp_id'];
                    $ret = "SELECT * FROM orrs_employee WHERE emp_id=?";
                    $stmt = $mysqli->prepare($ret);
                    $stmt->bind_param('i', $aid);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    while ($row = $res->fetch_object()) {
                ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-border-color card-border-color-success">
                                    <div class="card-header card-header-divider">Employee Profile<span class="card-subtitle">Fill All Details</span></div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <div class="form-group row">
                                                <label class="col-12 col-sm-3 col-form-label text-sm-right">First Name</label>
                                                <div class="col-12 col-sm-8 col-lg-6">
                                                    <input class="form-control" readonly name="emp_fname" value="<?php echo $row->emp_fname; ?>" type="text">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-12 col-sm-3 col-form-label text-sm-right">Last Name</label>
                                                <div class="col-12 col-sm-8 col-lg-6">
                                                    <input class="form-control" readonly name="emp_lname" value="<?php echo $row->emp_lname; ?>" type="text">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-12 col-sm-3 col-form-label text-sm-right">National ID Number</label>
                                                <div class="col-12 col-sm-8 col-lg-6">
                                                    <input class="form-control" readonly name="emp_nat_idno" value="<?php echo $row->emp_nat_idno; ?>" type="text">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-12 col-sm-3 col-form-label text-sm-right">Phone Number</label>
                                                <div class="col-12 col-sm-8 col-lg-6">
                                                    <input class="form-control" readonly name="emp_phone" value="<?php echo $row->emp_phone; ?>" type="text">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-12 col-sm-3 col-form-label text-sm-right">Address</label>
                                                <div class="col-12 col-sm-8 col-lg-6">
                                                    <input class="form-control" readonly name="emp_addr" value="<?php echo $row->emp_addr; ?>" type="text">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-12 col-sm-3 col-form-label text-sm-right">Department</label>
                                                <div class="col-12 col-sm-8 col-lg-6">
                                                    <input class="form-control" readonly name="emp_dept" value="<?php echo $row->emp_dept; ?>" type="text">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-12 col-sm-3 col-form-label text-sm-right">Email</label>
                                                <div class="col-12 col-sm-8 col-lg-6">
                                                    <input class="form-control" readonly name="emp_email" value="<?php echo $row->emp_email; ?>" type="email">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-12 col-sm-3 col-form-label text-sm-right">Username</label>
                                                <div class="col-12 col-sm-8 col-lg-6">
                                                    <input class="form-control" readonly name="emp_uname" value="<?php echo $row->emp_uname; ?>" type="text">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p style='color:red;'>Employee ID is not provided in the URL.</p>";
                }
                ?>
            </div>

            <!-- Footer -->
            <?php include('assets/inc/footer.php'); ?>
        </div>
    </div>

    <script src="assets/lib/jquery/jquery.min.js"></script>
    <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/lib/jquery-ui/jquery-ui.min.js"></script>
    <script src="assets/lib/jquery.nestable/jquery.nestable.js"></script>
    <script src="assets/lib/moment.js/min/moment.min.js"></script>
    <script src="assets/lib/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/lib/select2/js/select2.min.js"></script>
    <script src="assets/lib/select2/js/select2.full.min.js"></script>
    <script src="assets/lib/bootstrap-slider/bootstrap-slider.min.js"></script>
    <script src="assets/lib/bs-custom-file-input/bs-custom-file-input.js"></script>
    <script src="assets/js/swal.js"></script>

    <script>
        $(document).ready(function () {
            App.init();
            App.formElements();
        });
    </script>
</body>
</html>
