<?php
ob_start();
session_start();
include('db.php');
//date_default_timezone_set('Africa /Nairobi');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['pass_id'];

include('assets/inc/sidebar.php'); 
if (isset($_POST['Book_Train'])) {
    /*
    *We have already captured this passenger details....so no need of getting them again.     
    $pass_fname=$_POST['pass_fname'];
    $pass_lname = $_POST['pass_lname'];
    $pass_phone=$_POST['pass_phone'];
    $pass_addr=$_POST['pass_addr'];
    $pass_email=$_POST['pass_email'];
    $pass_uname=$_POST['pass_uname'];
    $pass_bday=$_POST['pass_bday'];
    //$pass_ocupation=$_POST['pass_occupation'];
    $pass_bio=($_POST['pass_bio']);
    //$passwordconf=md5($_POST['passwordconf']);
    //$date = date('d-m-Y h:i:s', time());
    */
    $pass_train_number = $_POST['pass_train_number'];
    $pass_train_name = $_POST['pass_train_name'];
    $pass_dep_station = $_POST['pass_dep_station'];
    $pass_dep_time = $_POST['pass_dep_time'];
    $pass_arr_station = $_POST['pass_arr_station'];
    $pass_train_fare = $_POST['pass_train_fare'];
    $pass_seat_number = $_POST['pass_seat_number']; // new seat number
    $pass_seat_feature = $_POST['pass_seat_feature']; // seat feature

    $query = "UPDATE orrs_passenger SET pass_train_number = ?, pass_train_name = ?, pass_dep_station = ?, pass_dep_time = ?, pass_arr_station = ?, pass_train_fare = ?, pass_seat_number = ?, pass_seat_feature = ? WHERE pass_id = ?";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        $err = "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    } else {
        $rc = $stmt->bind_param('ssssssisi', $pass_train_number, $pass_train_name, $pass_dep_station, $pass_dep_time, $pass_arr_station, $pass_train_fare, $pass_seat_number, $pass_seat_feature, $aid);
        if (!$rc) {
            $err = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $succ = "Reserved Train Please Proceed To Check Out";
            } else {
                $err = "Please Try Again Later";
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php'); ?>
<!--End Head-->

<body>
    <div class="be-wrapper be-fixed-sidebar ">
        <!--Navigation Bar-->
        <?php include('assets/inc/navbar.php'); ?>
        <!--End Navigation Bar-->

        <!--Sidebar-->
        <?php include('assets/inc/sidebar.php'); ?>
        <!--End Sidebar-->
        <div class="be-content">
            <div class="page-head">
                <h2 class="page-head-title">Book Train </h2>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb page-head-nav">
                        <li class="breadcrumb-item"><a href="pass-dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Book Train</a></li>
                        <li class="breadcrumb-item active">Reserve Seat</li>
                    </ol>
                </nav>
            </div>

            <?php if (isset($succ)) { ?>
                <script>
                    setTimeout(function() {
                        swal("Success!", "<?php echo $succ; ?>!", "success");
                    }, 100);
                </script>
            <?php } ?>

            <?php if (isset($err)) { ?>
                <script>
                    setTimeout(function() {
                        swal("Failed!", "<?php echo $err; ?>!", "error");
                    }, 100);
                </script>
            <?php } ?>

            <div class="main-content container-fluid">
                <?php
                $ret = "SELECT * FROM orrs_passenger WHERE pass_id=?";
                $stmt = $mysqli->prepare($ret);
                $stmt->bind_param('i', $aid);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_object()) {
                ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-border-color card-border-color-success">
                                <div class="card-header card-header-divider"><span class="card-subtitle">Fill All Details</span></div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="form-group row">
                                            <label class="col-12 col-sm-3 col-form-label text-sm-right">First Name</label>
                                            <div class="col-12 col-sm-8 col-lg-6">
                                                <input class="form-control" readonly name="pass_fname" value="<?php echo $row->pass_fname; ?>" type="text">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-12 col-sm-3 col-form-label text-sm-right">Last Name</label>
                                            <div class="col-12 col-sm-8 col-lg-6">
                                                <input class="form-control" readonly name="pass_lname" value="<?php echo $row->pass_lname; ?>" type="text">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-12 col-sm-3 col-form-label text-sm-right">Phone Number</label>
                                            <div class="col-12 col-sm-8 col-lg-6">
                                                <input class="form-control" readonly name="pass_phone" value="<?php echo $row->pass_phone; ?>" type="text">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-12 col-sm-3 col-form-label text-sm-right">Address</label>
                                            <div class="col-12 col-sm-8 col-lg-6">
                                                <input class="form-control" readonly name="pass_addr" value="<?php echo $row->pass_addr; ?>" type="text">
                                            </div>
                                        </div>

                                        <?php
                                        if (!isset($_POST['id'])) {
                                          // Silent fail or redirect
                                          header("Location: pass-book-train.php");
                                          exit();
                                      }
                                      $train_id = intval($_POST['id']);
                                      
                                        $ret = "SELECT * FROM orrs_train WHERE id=?";
                                        $stmt = $mysqli->prepare($ret);
                                        $stmt->bind_param('i', $id);
                                        $stmt->execute();
                                        $res = $stmt->get_result();

                                        if ($res->num_rows > 0) {
                                            while ($row = $res->fetch_object()) {
                                        ?>
                                        <?php ob_end_flush(); ?>
                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Train Number</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input class="form-control" readonly name="pass_train_number" value="<?php echo $row->number; ?>" type="text">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Train Name</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input class="form-control" readonly name="pass_train_name" value="<?php echo $row->name; ?>" type="text">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Departure</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input class="form-control" readonly name="pass_dep_station" value="<?php echo $row->current; ?>" type="text">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Arrival</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input class="form-control" readonly name="pass_arr_station" value="<?php echo $row->destination; ?>" type="text">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Departure Time</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input class="form-control" readonly name="pass_dep_time" value="<?php echo $row->time; ?>" type="text">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Train Fare</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input class="form-control" readonly name="pass_train_fare" value="<?php echo $row->fare; ?>" type="text">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right" for="seatFeature">Seat Feature</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <select class="form-control" name="pass_seat_feature" id="seatFeature" required>
                                                            <option value="">Select Feature</option>
                                                            <option value="AC Sleeper">AC Sleeper</option>
                                                            <option value="Non-AC Sleeper">Non-AC Sleeper</option>
                                                            <option value="AC Chair Car">AC Chair Car</option>
                                                            <option value="Non-AC Chair Car">Non-AC Chair Car</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right" for="seatNumber">Seat Number</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <select class="form-control" name="pass_seat_number" id="seatNumber" required>
                                                            <option value="">Select Seat Number</option>
                                                            <?php
                                                            for ($i = 1; $i <= 40; $i++) {
                                                                echo "<option value='$i'>Seat $i</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <div class="alert alert-warning text-center col-12" role="alert" style="font-weight:bold; font-size:1.2em; margin-top:20px;">
                                                Train information not found.
                                            </div>
                                        <?php
                                        }
                                        ?>

                                        <div class="col-sm-6">
                                            <p class="text-right">
                                                <input class="btn btn-space btn-outline-success" value="Book Train" name="Book_Train" type="submit">
                                                <button class="btn btn-space btn-outline-danger" type="button" onclick="window.history.back()">Cancel</button>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>

        </div>

    </div>

    <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
    <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="assets/js/app.js" type="text/javascript"></script>
    <script src="assets/lib/sweetalert/sweetalert.min.js" type="text/javascript"></script>
<script>
$(document).ready(function() {
// App initialization code
App.init();
});
</script>

</body> </html> 
