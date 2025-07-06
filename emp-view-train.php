<?php
session_start();
include('db.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['emp_id'];

if (!isset($_GET['id'])) {
    header("Location: emp-manage-train.php");
    exit();
}

$id = intval($_GET['id']);
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
      <h2 class="page-head-title">View Train Details</h2>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb page-head-nav">
          <li class="breadcrumb-item"><a href="emp-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="emp-manage-train.php">Manage Trains</a></li>
          <li class="breadcrumb-item active">View Train</li>
        </ol>
      </nav>
    </div>

    <div class="main-content container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <div class="card card-table">
            <div class="card-header">Train Details</div>
            <div class="card-body table-responsive">
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
                    <th>Total Passengers</th>
                    <th>Seat Features</th>
                    <th>Reserved Seats</th>
                    <th>Available Seats</th>
                    <th>Total Seats</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  $ret = "SELECT * FROM orrs_train WHERE id=?";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->bind_param('i', $id);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  if ($row = $res->fetch_object()) {
                      $reserved_seats = $row->passengers;
                      $buffer_seats = ceil($reserved_seats * 0.10);
                      $total_seats = $reserved_seats + $buffer_seats;
                      $available_seats = $buffer_seats;

                      // Parse Seat Features from the database (stored as JSON)
                      $decoded_features = json_decode($row->seat_features, true);
$seat_features = (is_array($decoded_features) && !empty($decoded_features)) 
    ? $decoded_features 
                          : [
                              ["type" => "AC", "price" => 500, "total" => floor($total_seats / 3), "reserved" => floor($reserved_seats / 3), "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)],
                              ["type" => "Non-AC", "price" => 200, "total" => floor($total_seats / 3), "reserved" => floor($reserved_seats / 3), "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)],
                              ["type" => "Sleeper", "price" => 300, "total" => floor($total_seats / 3), "reserved" => floor($reserved_seats / 3), "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)]
                          ];

                      // Generate Seat Features display with Child and Senior Citizen fares
                      $features = '<div class="seat-features-list">';
foreach ($seat_features as $feature) {
    $adult_price = isset($feature['price']) ? $feature['price'] : 0;
    $child_price = $adult_price * 0.5;
    $senior_price = $adult_price * 0.8;

    $total = isset($feature['total']) ? $feature['total'] : 0;
    $reserved = isset($feature['reserved']) ? $feature['reserved'] : 0;
    $unreserved = isset($feature['unreserved']) ? $feature['unreserved'] : 0;
    $type = isset($feature['type']) ? $feature['type'] : 'Unknown';

    $features .= "<div class='mb-2 p-2 border rounded bg-light'>
        <strong>{$type}</strong><br>
        Adult: ₹" . number_format($adult_price, 2) . "<br>
        Child: ₹" . number_format($child_price, 2) . "<br>
        Senior: ₹" . number_format($senior_price, 2) . "<br>
        Total: {$total}<br>
        Reserved: {$reserved}<br>
        Unreserved: {$unreserved}</div>";
}
$features .= '</div>';

                      // Use the stored value for food cost
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
                    <td><?= $reserved_seats; ?></td>
                    <td><?= $available_seats; ?></td>
                    <td><?= $total_seats; ?></td>
                  </tr>
                <?php } else { ?>
                  <tr>
                    <td colspan="12" class="text-center">Train not found.</td>
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
    font-size: 14px;
    line-height: 1.5;
  }
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  .table {
    min-width: 1000px; /* Ensures table is wide enough to prevent column squeezing */
  }
  th, td {
    white-space: nowrap; /* Prevents text wrapping in cells */
  }
</style>
</body>
</html>