<?php
session_start();
include('db.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['pass_id'];
?>

<!DOCTYPE html>
<html lang="en">
<?php include('assets/inc/head.php'); ?>
<body>
>
<?php include('assets/inc/sidebar.php'); ?>
  <?php include('assets/inc/navbar.php'); ?>


  <div class="be-content">
    <div class="page-head">
      <h2 class="page-head-title">Available Trains</h2>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb page-head-nav">
          <li class="breadcrumb-item"><a href="pass-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="pass-all-trains.php">Trains</a></li>
          <li class="breadcrumb-item active">Available Trains</li>
        </ol>
      </nav>
    </div>

    <div class="main-content container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <div class="card card-table">
            <div class="card-header">Available Trains</div>
            <div class="card-body table-responsive">
              <table class="table table-striped table-bordered table-hover table-fw-widget" id="table1">
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Train Number</th>
                    <th>Train</th>
                    <th>Route</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Dep.Time</th>
                    <th>Food Cost</th>
                    <th>Seat Features</th>
                    <th>Reserved Seats</th>
                    <th>Available Seats</th>
                    <th>Total Seats</th>
                    <th>Action</th>

                  </tr>
                </thead>
                <tbody>
                <?php
                  $ret = "SELECT * FROM orrs_train";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  $cnt = 1;
                  while ($row = $res->fetch_object()) {
                      $train_id = $row->id;

                      // Calculate seat data using the same logic as emp-manage-train.php
                      $reserved_seats = $row->passengers;
                      $buffer_seats = ceil($reserved_seats * 0.10);
                      $total_seats = $reserved_seats + $buffer_seats;
                      $available_seats = $buffer_seats;

                      // Parse Seat Features from the database (stored as JSON)
                      $seat_features = [];
                      if (!empty($row->seat_features)) {
                          $decoded = json_decode($row->seat_features, true);
                          if (is_array($decoded)) {
                              $seat_features = $decoded;
                          }
                      }
                      

                      // Generate Seat Features display
                      // Calculate category-wise fare
                      $seat_details = '<div class="seat-features-list">';
                      foreach ($seat_features as $feature) {
                          $type = htmlspecialchars($feature['type'] ?? 'Unknown');
                          $price = isset($feature['price']) ? floatval($feature['price']) : 0;
                          $total = isset($feature['total']) ? intval($feature['total']) : 0;
                          $reserved = isset($feature['reserved']) ? intval($feature['reserved']) : 0;
                          $unreserved = isset($feature['unreserved']) ? intval($feature['unreserved']) : 0;
                      
                          // Calculate fare breakdown
                          $child_price = $price * 0.5;
                          $senior_price = $price * 0.8;
                      
                          // Render seat feature block
                          $seat_details .= "<div class='mb-2 p-2 border rounded bg-light'>
                              <strong>{$type}</strong><br>
                              Adult: ₹" . number_format($price, 2) . "<br>
                              Child: ₹" . number_format($child_price, 2) . "<br>
                              Senior: ₹" . number_format($senior_price, 2) . "<br>
                              Total: {$total}<br>
                              Reserved: {$reserved}<br>
                              Unreserved: {$unreserved}
                          </div>";
                      }
                      $seat_details .= '</div>';
                      


                      // Use the stored values for fares and food cost, matching emp-manage-train.php
                      $child_fare = isset($row->child_fare) ? $row->child_fare : ($row->fare * 0.5);
                      $adult_fare = isset($row->adult_fare) ? $row->adult_fare : $row->fare;
                      $senior_fare = isset($row->senior_fare) ? $row->senior_fare : ($row->fare * 0.8);
                      $food_cost = isset($row->food_cost) ? $row->food_cost : (
                          $row->fare <= 50 ? 80 : ($row->fare <= 100 ? 120 : ($row->fare <= 150 ? 150 : 200))
                      );
                ?>
                  <tr>
                    <td><?php echo $cnt; ?></td>
                    <td><?php echo $row->number; ?></td>
                    <td><?php echo $row->name; ?></td>
                    <td><?php echo $row->route; ?></td>
                    <td><?php echo $row->current; ?></td>
                    <td><?php echo $row->destination; ?></td>
                    <td><?php echo $row->time; ?></td>
                    
                    <td>₹<?php echo number_format($food_cost, 2); ?></td>
                    <td><?php echo $seat_details; ?></td>
                    <td><?php echo $reserved_seats; ?></td>
                    <td><?php echo $available_seats; ?></td>
                    <td><?php echo $total_seats; ?></td>
<td>
<a href="pass-book-train.php?train_id=<?php echo urlencode($row->number); ?>" class="btn btn-primary">Book</a>



</td>

                  </tr>
                <?php
                    $cnt++;
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