<?php
session_start();
include('db.php');
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['admin_id'];
?>

<!DOCTYPE html>
<html lang="en">
<?php include('assets/inc/head.php'); ?>

<body>
<?php include('assets/inc/sidebar.php'); ?>

<div class="be-wrapper be-fixed-sidebar">
    <?php include('assets/inc/navbar.php'); ?>
    <div class="be-wrapper be-fixed-sidebar"></div>
    

    <div class="be-content">
        <div class="page-head">
            <h2 class="page-head-title">Manage Tickets</h2>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb page-head-nav">
                    <li class="breadcrumb-item"><a href="emp-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Tickets</a></li>
                    <li class="breadcrumb-item active">Manage Tickets</li>
                </ol>
            </nav>
        </div>

        <div class="main-content container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table">
                        <div class="card-header">Tickets</div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered table-hover table-fw-widget" id="table1">
                                <thead class="thead-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Age</th>
                                    <th>Train No</th>
                                    <th>Seat No</th>
                                    <th>Seat Type</th>
                                    <th>Fare</th>
                                    <th>Route</th>
                                    <th>Journey Date</th>
                                    <th>Booking Date</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query = "SELECT passenger_name, email, age, train_number, seat_number, seat_feature, fare, 
                                                 departure, arrival, journey_date, booking_date, status 
                                          FROM orrs_ticket";
                                $stmt = $mysqli->prepare($query);

                                if ($stmt && $stmt->execute()) {
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['passenger_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                                            <td><?php echo htmlspecialchars($row['train_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['seat_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['seat_feature']); ?></td>
                                            <td><?php echo htmlspecialchars($row['fare']); ?></td>
                                            <td><?php echo htmlspecialchars($row['departure'] . " → " . $row['arrival']); ?></td>
                                            <td><?php echo htmlspecialchars($row['journey_date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                                            <td>
                                                <?php
                                                $status = strtolower(trim($row['status']));
                                                if ($status == 'cancelled' || $status == 'canceled') {
                                                    echo '<span class="badge badge-danger">Cancelled</span>';
                                                } elseif ($status == 'booked' || $status == 'active') {
                                                    echo '<span class="badge badge-success">Booked</span>';
                                                } else {
                                                    echo '<span class="badge badge-secondary">' . htmlspecialchars($row['status']) . '</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $stmt->close();
                                } else {
                                    echo "<tr><td colspan='11'>No ticket records found or query failed.</td></tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="assets/lib/jquery/jquery.min.js"></script>
<script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
<script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/lib/datatables/datatables.net/js/jquery.dataTables.js"></script>
<script src="assets/lib/datatables/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
<script src="assets/lib/datatables/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="assets/lib/datatables/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="assets/lib/datatables/jszip/jszip.min.js"></script>
<script src="assets/lib/datatables/pdfmake/pdfmake.min.js"></script>
<script src="assets/lib/datatables/pdfmake/vfs_fonts.js"></script>
<script src="assets/lib/datatables/datatables.net-buttons/js/buttons.colVis.min.js"></script>
<script src="assets/lib/datatables/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="assets/lib/datatables/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="assets/lib/datatables/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
<script src="assets/lib/datatables/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="assets/lib/datatables/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        App.init();
        App.dataTables();
    });
</script>
</body>
</html>
