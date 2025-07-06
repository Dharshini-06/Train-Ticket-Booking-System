<?php
  include('db.php');  // your DB connection

  $showDetails = false;
  $errorMsg = '';
  $rows = [];

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputName = trim($_POST['pass_name']);
    $inputEmail = trim($_POST['pass_email']);

    // Extract first name from input (assuming first word is first name)
    $firstName = explode(' ', $inputName)[0];

    // Prepare query to find bookings by first name and email (case-insensitive)
    $stmt = $mysqli->prepare("SELECT * FROM orrs_ticket WHERE LOWER(passenger_name) LIKE CONCAT('%', LOWER(?), '%') AND LOWER(email) = LOWER(?) AND status != 'cancelled' ORDER BY booking_date DESC");


    $stmt->bind_param("ss", $firstName, $inputEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if (count($rows) > 0) {
      $showDetails = true;
    } else {
      $errorMsg = "No booking found matching the provided name and email.";
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <?php include('assets/inc/head.php');?>

  <body>
    <?php include('assets/inc/navbar.php');?>
    <?php include('assets/inc/sidebar.php');?>

    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title">My Train</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="pass-dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">My Booked Train</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-sm-12">

            <div class="card">
              <div class="card-header">Enter your details to view booked trains</div>
              <div class="card-body">
                <form method="POST">
                  <div class="form-group">
                    <label for="pass_name">Full Name</label>
                    <input type="text" name="pass_name" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label for="pass_email">Email</label>
                    <input type="email" name="pass_email" class="form-control" required>
                  </div>
                  <button type="submit" class="btn btn-primary">Check My Booking</button>
                </form>

                <?php if ($errorMsg): ?>
                  <div class="alert alert-danger mt-2"><?php echo htmlspecialchars($errorMsg); ?></div>
                <?php endif; ?>
              </div>
            </div>

            <?php if ($showDetails): ?>
              <div class="card card-table mt-4">
                <div class="card-header">
                  Your Booked Train(s)
                </div>

                <div class="card-body">
                  <table class="table table-striped table-bordered table-hover table-fw-widget" id="table1">
                    <thead class="thead-dark">
                      <tr>
                        <th>Train Number</th>
                        <th>Route</th>
                        <th>Time</th>
                        <th>Date</th>
                        <th>Seat</th>
                        <th>Coach</th>
                        <th>Fare</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($rows as $row): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($row['train_number']); ?></td>
                          <td><?php echo htmlspecialchars($row['departure']); ?> → <?php echo htmlspecialchars($row['arrival']); ?></td>
                          <td><?php echo htmlspecialchars($row['journey_time']); ?></td>
                          <td><?php echo htmlspecialchars($row['journey_date']); ?></td>
                          <td><?php echo htmlspecialchars($row['seat_feature']); ?> (Seat <?php echo $row['seat_number']; ?>)</td>
                          <td><?php echo htmlspecialchars($row['coach_number'] ?? 'N/A'); ?></td>
                          <td>$<?php echo htmlspecialchars($row['fare']); ?></td>
                          <td>
  <form method="POST" action="cancel-ticket.php" onsubmit="return confirm('Are you sure you want to cancel this ticket?');">
    <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
    <input type="hidden" name="journey_date" value="<?php echo $row['journey_date']; ?>">
    <input type="hidden" name="fare" value="<?php echo $row['fare']; ?>">
    <input type="hidden" name="seat_feature" value="<?php echo $row['seat_feature']; ?>">
    <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
  </form>
  
</td>

                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            <?php endif; ?>

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
    <script src="assets/lib/datatables/jszip/jszip.min.js"></script>
    <script src="assets/lib/datatables/pdfmake/pdfmake.min.js"></script>
    <script src="assets/lib/datatables/pdfmake/vfs_fonts.js"></script>
    <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="assets/lib/datatables/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/lib/datatables/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

    <script>
      $(document).ready(function() {
        App.init();
        App.dataTables();
      });
    </script>
  </body>
</html>
