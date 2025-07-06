<?php
// Set session timeout to 1 hour
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();
include('db.php');
include('assets/inc/checklogin.php');

// Debug session ID and state
error_log("Session ID in emp-manage-train.php: " . session_id() . " at " . date('Y-m-d H:i:s'));
error_log("Before check_login - emp_id: " . ($_SESSION['emp_id'] ?? 'not set') . " at " . date('Y-m-d H:i:s'));

check_login();

// Debug session after check_login
$aid = $_SESSION['emp_id']?? null;

// Check for success message
if (isset($_SESSION['success'])) {
    $succ = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include('assets/inc/head.php'); ?>
<body>
<div class="be-wrapper be-fixed-sidebar">
  <?php include('assets/inc/navbar.php'); ?>
  <?php include('assets/inc/sidebar.php'); ?>

  <div class="be-content">
    <div class="page-head">
      <h2 class="page-head-title">Manage Trains</h2>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb page-head-nav">
          <li class="breadcrumb-item"><a href="emp-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Manage Trains</li>
        </ol>
      </nav>
    </div>

    <?php if (isset($succ)) { ?>
      <script>
        setTimeout(function () {
          swal("Success!", "<?php echo $succ; ?>!", "success");
        }, 100);
      </script>
    <?php } ?>

    <div class="main-content container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <div class="card card-table">
            <div class="card-header">Train List</div>
            <div class="card-body table-responsive">
              <?php
                $ret = "SELECT * FROM orrs_train";
                $stmt = $mysqli->prepare($ret);
                if ($stmt === false) {
                    die("Prepare failed: " . $mysqli->error);
                }
                $stmt->execute();
                $res = $stmt->get_result();
                error_log("Number of trains fetched: " . $res->num_rows . " at " . date('Y-m-d H:i:s'));
                if ($res->num_rows === 0) {
                    echo "<p>No trains found in the database.</p>";
                }
              ?>
              <table class="table table-striped table-bordered table-hover table-fw-widget" id="table1">
                <thead class="thead-dark">
                  <tr>
                    <th>Train Number</th>
                    <th>Train</th>
                    <th>Route</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Dep.Time</th>
                    <th>Food Cost</th>
                    <th>Reserved seats</th>
                    <th>Seat Features</th>
                    <!-- Removed Reserved Seats Column -->
                    <th>Available Seats</th>
                    <th>Total Seats</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    while ($row = $res->fetch_object()) {
                        $reserved_seats = $row->passengers;
                        $buffer_seats = ceil($reserved_seats * 0.10);
                        $total_seats = $reserved_seats + $buffer_seats;
                        $available_seats = $buffer_seats;

                        $seat_features = isset($row->seat_features) && !empty($row->seat_features) 
                            ? json_decode($row->seat_features, true) 
                            : [
                                ["type" => "AC", "adult_price" => 500.00, "child_price" => 250.00, "senior_price" => 400.00, "total" => floor($total_seats / 3), "reserved" => floor($reserved_seats / 3), "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)],
                                ["type" => "Non-AC", "adult_price" => 200.00, "child_price" => 100.00, "senior_price" => 160.00, "total" => floor($total_seats / 3), "reserved" => floor($reserved_seats / 3), "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)],
                                ["type" => "Sleeper", "adult_price" => 350.00, "child_price" => 150.00, "senior_price" => 240.00, "total" => floor($total_seats / 3), "reserved" => floor($reserved_seats / 3), "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)]
                            ];

                        $features = '<div class="seat-features-list">';
                        foreach ($seat_features as $feature) {
                            $adult_price = $feature['adult_price'] ?? $feature['price'] ?? 0;
                            $child_price = $feature['child_price'] ?? ($feature['price'] * 0.5 ?? 0);
                            $senior_price = $feature['senior_price'] ?? ($feature['price'] * 0.8 ?? 0);
                            $features .= "<div class='mb-2 p-2 border rounded bg-light'>
                                <strong>{$feature['type']}</strong><br>
                                Adult: ₹" . number_format($adult_price, 2) . "<br>
                                Child: ₹" . number_format($child_price, 2) . "<br>
                                Senior: ₹" . number_format($senior_price, 2) . "<br>
                                Total: {$feature['total']}<br>
                                Reserved: {$feature['reserved']}<br>
                                Unreserved: {$feature['unreserved']}</div>";
                        }
                        $features .= '</div>';

                        $food_cost = isset($row->food_cost) ? $row->food_cost : (
                            $row->fare <= 50 ? 80 : ($row->fare <= 100 ? 120 : ($row->fare <= 150 ? 150 : 200))
                        );
                  ?>
                  <tr>
                    <td><?= $row->number; ?></td>
                    <td><?= $row->name; ?></td>
                    <td><?= $row->route; ?></td>
                    <td><?= $row->current; ?></td>
                    <td><?= $row->destination; ?></td>
                    <td><?= $row->time; ?></td>
                    <td>₹<?= number_format($food_cost, 2); ?></td>
                    <td><?= $row->passengers; ?></td>
                    <td><?= $features; ?></td>
                    <!-- Removed Reserved Seats Data Column -->
                    <td><?= $available_seats; ?></td>
                    <td><?= $total_seats; ?></td>
                    <td class="center">
                      <a class="badge badge-primary mr-2" href="emp-update-train.php?id=<?= $row->id ?>">Update</a>
                      <a class="badge badge-danger" href="emp-update-train.php?cancel=<?= $row->id ?>" onclick="return confirm('Are you sure you want to cancel this train?')">Cancel Train</a>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="assets/lib/jquery/jquery.min.js"></script>
<script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/lib/datatables/datatables.net/js/jquery.dataTables.js"></script>
<script src="assets/lib/datatables/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    App.init();
    App.dataTables();
  });
</script>
<style>
  .seat-features-list {
    font-size: 16px;
    line-height: 1.8;
  }
  .seat-features-list div {
    margin-bottom: 15px;
  }
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  .table {
    min-width: 1200px;
  }
  th, td {
    white-space: nowrap;
    padding: 15px 20px;
    font-size: 16px;
  }
  tr {
    margin-bottom: 10px;
  }
  .badge {
    padding: 8px 12px;
    font-size: 14px;
  }
  .mr-2 {
    margin-right: 10px;
  }
</style>
</body>
</html>
