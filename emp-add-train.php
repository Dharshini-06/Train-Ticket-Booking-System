<?php
session_start();
include('db.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['emp_id'];

// Handle form submission to add a new train
if (isset($_POST['add_train'])) {
    $number = $_POST['number'];
    $name = $_POST['name'];
    $route = $_POST['route'];
    $current = $_POST['current'];
    $destination = $_POST['destination'];
    $time = $_POST['time'];
    $total_seats = intval($_POST['total_seats']);
    $food_cost = floatval($_POST['food_cost']);
    $ac_price = floatval($_POST['ac_price']);
    $non_ac_price = floatval($_POST['non_ac_price']);
    $sleeper_price = floatval($_POST['sleeper_price']);

    // For a new train, reserved seats start at 0
    $reserved_seats = 0;
    $available_seats = $total_seats;

    // Initially, passengers are 0 since this is a new train
    $passengers = 0;

    // Distribute total seats evenly across AC, Non-AC, and Sleeper
    $seats_per_type = floor($total_seats / 3);

    // Create seat features array
    $seat_features = [
        [
            "type" => "AC",
            "price" => $ac_price,
            "total" => $seats_per_type,
            "reserved" => 0, // No reservations yet
            "unreserved" => $seats_per_type
        ],
        [
            "type" => "Non-AC",
            "price" => $non_ac_price,
            "total" => $seats_per_type,
            "reserved" => 0,
            "unreserved" => $seats_per_type
        ],
        [
            "type" => "Sleeper",
            "price" => $sleeper_price,
            "total" => $seats_per_type,
            "reserved" => 0,
            "unreserved" => $seats_per_type
        ]
    ];

    // Adjust for any remaining seats due to division
    $remaining_seats = $total_seats - ($seats_per_type * 3);
    if ($remaining_seats > 0) {
        // Distribute remaining seats to Sleeper as a simple approach
        $seat_features[2]["total"] += $remaining_seats;
        $seat_features[2]["unreserved"] += $remaining_seats;
    }

    // Encode seat features as JSON
    $seat_features_json = json_encode($seat_features);

    // Insert the new train into the database
    $query = "INSERT INTO orrs_train (number, name, route, current, destination, time, passengers, food_cost, seat_features) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssssssids', $number, $name, $route, $current, $destination, $time, $passengers, $food_cost, $seat_features_json);
    $stmt->execute();

    if ($stmt) {
        $succ = "Train Added Successfully";
        // Redirect to emp-manage-train.php after successful addition
        header("Location: emp-manage-train.php");
        exit();
    } else {
        $err = "Failed to Add Train. Please Try Again.";
    }
    $stmt->close();
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
      <h2 class="page-head-title">Add New Train</h2>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb page-head-nav">
          <li class="breadcrumb-item"><a href="emp-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="emp-manage-train.php">Manage Trains</a></li>
          <li class="breadcrumb-item active">Add Train</li>
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

    <?php if (isset($err)) { ?>
      <script>
        setTimeout(function () {
          swal("Failed!", "<?php echo $err; ?>!", "error");
        }, 100);
      </script>
    <?php } ?>

    <div class="main-content container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <div class="card card-table">
            <div class="card-header">Add Train Details</div>
            <div class="card-body">
              <form method="POST">
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">Train Number</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control form-control-lg" name="number" placeholder="e.g., CA-007" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">Train Name</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control form-control-lg" name="name" placeholder="e.g., Black Water" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">Route</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control form-control-lg" name="route" placeholder="e.g., Chicago to Carbondale" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">Departure Station</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control form-control-lg" name="current" placeholder="e.g., Chicago" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">Arrival Station</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control form-control-lg" name="destination" placeholder="e.g., Carbondale" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">Departure Time</label>
                  <div class="col-sm-9">
                    <input type="time" class="form-control form-control-lg" name="time" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">Total Seats</label>
                  <div class="col-sm-9">
                    <input type="number" class="form-control form-control-lg" name="total_seats" placeholder="e.g., 215" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">Food Cost (₹)</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" class="form-control form-control-lg" name="food_cost" placeholder="e.g., 150.00" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">AC Price (₹)</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" class="form-control form-control-lg" name="ac_price" placeholder="e.g., 700.00" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">Non-AC Price (₹)</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" class="form-control form-control-lg" name="non_ac_price" placeholder="e.g., 250.00" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <label class="col-sm-3 col-form-label">Sleeper Price (₹)</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" class="form-control form-control-lg" name="sleeper_price" placeholder="e.g., 350.00" required>
                  </div>
                </div>
                <div class="form-group row mb-4">
                  <div class="col-sm-9 offset-sm-3">
                    <button type="submit" name="add_train" class="btn btn-primary btn-lg mr-3">Add Train</button>
                    <a href="emp-manage-train.php" class="btn btn-secondary btn-lg">Cancel</a>
                  </div>
                </div>
              </form>
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
  /* Form Styling */
  .form-group {
    margin-bottom: 2rem; /* Increased spacing between form rows */
  }
  .col-form-label {
    font-size: 16px; /* Larger font size for labels */
    line-height: 1.8; /* Better readability */
    padding-left: 15px; /* Reduced padding for better centering */
    padding-right: 15px; /* Reduced padding for better centering */
    text-align: center; /* Center the label text within the column */
  }
  .form-control-lg {
    font-size: 16px; /* Larger font size for inputs */
    padding: 10px 15px; /* More padding inside inputs */
  }
  .btn-lg {
    font-size: 16px; /* Larger font size for buttons */
    padding: 10px 20px; /* More padding for buttons */
  }
  .mr-3 {
    margin-right: 15px; /* Space between Add and Cancel buttons */
  }
</style>
</body>
</html>