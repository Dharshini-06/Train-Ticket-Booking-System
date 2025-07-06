<?php
session_start();
include('db.php');
include('assets/inc/checklogin.php');
check_login();

$pass_id = $_SESSION['pass_id'];

// First, get passenger details (including email)
$stmt = $mysqli->prepare("SELECT pass_fname, pass_lname, pass_email FROM orrs_passenger WHERE pass_id=?");
if (!$stmt) {
    die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
}

$stmt->bind_param('i', $pass_id);
$stmt->execute();
$passenger_result = $stmt->get_result();

if ($passenger = $passenger_result->fetch_object()) {
    $pass_email = $passenger->pass_email;
} else {
    echo "Passenger not found.";
    exit;
}

// Now get all tickets booked by this passenger's email
$tickets_stmt = $mysqli->prepare("SELECT * FROM orrs_ticket WHERE email=?");
$tickets_stmt->bind_param('s', $pass_email);
$tickets_stmt->execute();
$tickets_result = $tickets_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include('assets/inc/head.php'); ?>
</head>
<body>
<?php include('assets/inc/sidebar.php'); ?>
  <?php include('assets/inc/navbar.php'); ?>
  
  <div class="be-content">
    <div class="page-head">
      <h2 class="page-head-title">Train Ticket</h2>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb page-head-nav">
          <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="#">Tickets</a></li>
          <li class="breadcrumb-item active">Print</li>
        </ol>
      </nav>
    </div>

    <div class="main-content container-fluid">
      <div class="row">
        <div class="col-lg-12">

          <div id="printReceipt" class="invoice">
            <div class="row invoice-header">
              <div class="col-sm-7">
                <h3>Passenger Details</h3>
                <p>
                  <?php echo htmlspecialchars($passenger->pass_fname . ' ' . $passenger->pass_lname); ?><br>
                  Email: <?php echo htmlspecialchars($passenger->pass_email); ?><br>
                 
                </p>
              </div>
              <div class="col-sm-5 invoice-order">
                <span class="invoice-id">Train Tickets</span>
                <span class="invoice-date"><?php echo date("Y-m-d"); ?></span>
              </div>
            </div>

            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Train Number</th>
                  <th>Seat Number</th>
                  <th>Seat Feature</th>
                  <th>Fare</th>
                  <th>Departure</th>
                  <th>Arrival</th>
                  <th>Journey Time</th>
                  <th>Journey Date</th>
                  <th>Coach Number</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($ticket = $tickets_result->fetch_object()) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($ticket->train_number); ?></td>
                  <td><?php echo htmlspecialchars($ticket->seat_number); ?></td>
                  <td><?php echo htmlspecialchars($ticket->seat_feature); ?></td>
                  <td>$<?php echo number_format($ticket->fare, 2); ?></td>
                  <td><?php echo htmlspecialchars($ticket->departure); ?></td>
                  <td><?php echo htmlspecialchars($ticket->arrival); ?></td>
                  <td><?php echo htmlspecialchars($ticket->journey_time); ?></td>
                  <td><?php echo htmlspecialchars($ticket->journey_date); ?></td>
                  <td><?php echo htmlspecialchars($ticket->coach_number ?? 'N/A'); ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>

            <hr>
            <div class="row invoice-footer">
              <div class="col-lg-12">
                <button id="print" onclick="printContent('printReceipt');" class="btn btn-lg btn-primary">Print</button>
              </div>
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

<script>
function printContent(el) {
  var restorepage = document.body.innerHTML;
  var printcontent = document.getElementById(el).cloneNode(true);
  document.body.innerHTML = "";
  document.body.appendChild(printcontent);
  window.print();
  document.body.innerHTML = restorepage;
}
</script>
</body>
</html>
