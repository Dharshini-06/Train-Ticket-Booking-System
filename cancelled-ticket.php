<?php
include('db.php');

$query = "SELECT * FROM orrs_ticket WHERE status = 'Cancelled' ORDER BY journey_date DESC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<?php include('assets/inc/head.php'); ?>

<body>
  <?php include('assets/inc/navbar.php'); ?>
  <?php include('assets/inc/sidebar.php'); ?>

  <div class="be-content">
    <div class="page-head">
      <h2 class="page-head-title">Cancelled Tickets</h2>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb page-head-nav">
          <li class="breadcrumb-item"><a href="pass-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Cancelled Tickets</li>
        </ol>
      </nav>
    </div>

    <div class="main-content container-fluid">
      <div class="card card-table">
        <div class="card-header">Your Cancelled Ticket(s)</div>
        <div class="card-body">
          <table class="table table-striped table-bordered table-hover" id="table2">
            <thead class="thead-dark">
              <tr>
                <th>Train No</th>
                <th>Route</th>
                <th>Journey Date</th>
                <th>Time</th>
                <th>Seat</th>
                <th>Coach</th>
                <th>Original Fare</th>
                <th>Refund Amount</th>
                <th>Reason</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['train_number']); ?></td>
                  <td><?php echo htmlspecialchars($row['departure'] . " → " . $row['arrival']); ?></td>
                  <td><?php echo htmlspecialchars($row['journey_date']); ?></td>
                  <td><?php echo htmlspecialchars($row['journey_time']); ?></td>
                  <td><?php echo htmlspecialchars($row['seat_feature'] . " (Seat " . $row['seat_number'] . ")"); ?></td>
                  <td><?php echo htmlspecialchars($row['coach_number'] ?? 'N/A'); ?></td>
                  <td>$<?php echo htmlspecialchars($row['fare']); ?></td>
                  <td>$<?php echo htmlspecialchars($row['refund_amount']); ?></td>
                  <td><?php echo htmlspecialchars($row['cancellation_reason']); ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/lib/jquery/jquery.min.js"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/lib/datatables/datatables.net/js/jquery.dataTables.js"></script>
  <script src="assets/lib/datatables/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
  <script>
    $(document).ready(function() {
      $('#table2').DataTable();
    });
  </script>
</body>
</html>
