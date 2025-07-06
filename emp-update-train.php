<?php
session_start();
include('db.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['emp_id'];

$train_data = null;

// Handle form submission to update train details
if (isset($_POST['update_train'])) {
  if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
      $err = "Error: Invalid or missing train ID.";
  } else {
      $id = intval($_GET['id']);
      $number = $_POST['number'];
      $name = $_POST['name'];
      $route = $_POST['route'];
      $current = $_POST['current'];
      $destination = $_POST['destination'];
      $time = $_POST['time'];
      $passengers = intval($_POST['passengers']);
      $food_cost = floatval($_POST['food_cost']);
      $ac_price = floatval($_POST['ac_price']);
      $non_ac_price = floatval($_POST['non_ac_price']);
      $sleeper_price = floatval($_POST['sleeper_price']);

      // Calculate seat data
      $reserved_seats = $passengers;
      $buffer_seats = ceil($reserved_seats * 0.10);
      $total_seats = $reserved_seats + $buffer_seats;

      $seat_features = [
          [
              "type" => "AC",
              "price" => $ac_price,
              "total" => floor($total_seats / 3),
              "reserved" => floor($reserved_seats / 3),
              "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)
          ],
          [
              "type" => "Non-AC",
              "price" => $non_ac_price,
              "total" => floor($total_seats / 3),
              "reserved" => floor($reserved_seats / 3),
              "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)
          ],
          [
              "type" => "Sleeper",
              "price" => $sleeper_price,
              "total" => floor($total_seats / 3),
              "reserved" => floor($reserved_seats / 3),
              "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)
          ]
      ];

      $seat_features_json = json_encode($seat_features);

      // Debug log to check seat features JSON
      error_log("Updating train ID $id with seat features: " . $seat_features_json);

      $query = "UPDATE orrs_train SET number=?, name=?, route=?, `current`=?, destination=?, time=?, passengers=?, food_cost=?, seat_features=? WHERE id=?";
      $stmt = $mysqli->prepare($query);

      if ($stmt === false) {
          $err = "Prepare failed: " . $mysqli->error;
      } else {
          $stmt->bind_param('ssssssidsi', $number, $name, $route, $current, $destination, $time, $passengers, $food_cost, $seat_features_json, $id);
          $stmt->execute();

          // Accept zero affected rows as success too
          if ($stmt->affected_rows >= 0) {
              $succ = "Train Details Updated Successfully";
          } else {
              $err = "Failed to Update Train ID $id or no changes made. Please Try Again.";
          }
          $stmt->close();
      }
  }
}

// Handle Cancel Train action
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    $adn = "DELETE FROM orrs_train WHERE id=?";
    $stmt = $mysqli->prepare($adn);
    if ($stmt === false) {
        $err = "Prepare failed: " . $mysqli->error;
    } else {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Train Cancelled Successfully";
            header("Location: emp-manage-train.php");
            exit();
        } else {
            $err = "Failed to Cancel Train ID $id. Please Try Again.";
        }
        $stmt->close();
    }
}

// Fetch train data to display
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id = intval($_GET['id']);
  
  // Prepare SQL query to fetch train data
  $query = "SELECT * FROM orrs_train WHERE id=?";
  $stmt = $mysqli->prepare($query);

  if ($stmt) {
      // Bind and execute the statement
      $stmt->bind_param('i', $id);
      $stmt->execute();
      $result = $stmt->get_result();

      // Fetch the train data
      if ($result->num_rows > 0) {
          $train_data = $result->fetch_object();
      } else {
          $err = "Train with ID $id not found.";
      }
      $stmt->close();
  } else {
      // Handle prepare failure
      $err = "Database error: " . $mysqli->error;
      error_log("Prepare failed for train fetch: " . $mysqli->error);
  }
} else {
  $err = "Invalid train ID.";
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
    <h2 class="page-head-title">Update Train</h2>
  </div>

  <?php if (isset($succ)) { ?>
    <script>
      setTimeout(function () {
        swal("Success!", "<?php echo $succ; ?>", "success");
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
    <?php if ($train_data): 
      $reserved_seats = $train_data->passengers;
      $buffer_seats = ceil($reserved_seats * 0.10);
      $total_seats = $reserved_seats + $buffer_seats;
      $available_seats = $buffer_seats;
      $seat_features = json_decode($train_data->seat_features, true);

      // Fallback if decoding fails or array is empty
      if (!is_array($seat_features) || empty($seat_features)) {
          $seat_features = [
              [
                  "type" => "AC",
                  "price" => 500,
                  "total" => floor($total_seats / 3),
                  "reserved" => floor($reserved_seats / 3),
                  "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)
              ],
              [
                  "type" => "Non-AC",
                  "price" => 200,
                  "total" => floor($total_seats / 3),
                  "reserved" => floor($reserved_seats / 3),
                  "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)
              ],
              [
                  "type" => "Sleeper",
                  "price" => 300,
                  "total" => floor($total_seats / 3),
                  "reserved" => floor($reserved_seats / 3),
                  "unreserved" => floor($total_seats / 3) - floor($reserved_seats / 3)
              ]
          ];
      }
      

      $ac_price = $seat_features[0]['price'] ?? 0;
      $non_ac_price = $seat_features[1]['price'] ?? 0;
      $sleeper_price = $seat_features[2]['price'] ?? 0;
      $food_cost = $train_data->food_cost ?? 100;
      ?>

      <?php
      $seat_features = json_decode($train_data->seat_features, true);
      
      // Prevent crash if decoding fails
      if (!is_array($seat_features)) {
          $seat_features = [];
      }
      
      $features = '<div class="seat-features-list">';
      foreach ($seat_features as $feature) {
          $type = htmlspecialchars($feature['type'] ?? 'Unknown');
          $price = isset($feature['price']) ? floatval($feature['price']) : 0;
          $total = $feature['total'] ?? 0;
          $reserved = $feature['reserved'] ?? 0;
          $unreserved = $feature['unreserved'] ?? 0;
      
          $features .= "<div class='mb-2 p-2 border rounded bg-light'>
              <strong>{$type}</strong><br>
              Adult: ₹" . number_format($price, 2) . "<br>
              Child: ₹" . number_format($price * 0.5, 2) . "<br>
              Senior: ₹" . number_format($price * 0.8, 2) . "<br>
              Total: {$total}<br>
              Reserved: {$reserved}<br>
              Unreserved: {$unreserved}</div>";
      }
      $features .= '</div>';
      ?>
      

    <div class="card card-table">
      <div class="card-header">Train Info</div>
      <div class="card-body table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Number</th>
              <th>Name</th>
              <th>Route</th>
              <th>Departure</th>
              <th>Arrival</th>
              <th>Time</th>
              <th>Food</th>
              <th>Passengers</th>
              <th>Seat Features</th>
              <th>Reserved</th>
              <th>Available</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?= $train_data->number; ?></td>
              <td><?= $train_data->name; ?></td>
              <td><?= $train_data->route; ?></td>
              <td><?= $train_data->current; ?></td>
              <td><?= $train_data->destination; ?></td>
              <td><?= $train_data->time; ?></td>
              <td>₹<?= number_format($food_cost, 2); ?></td>
              <td><?= $train_data->passengers; ?></td>
              <td><?= $features; ?></td>
              <td><?= $reserved_seats; ?></td>
              <td><?= $available_seats; ?></td>
              <td><?= $total_seats; ?></td>
              <td><a class="badge badge-danger" href="emp-update-train.php?cancel=<?= $train_data->id ?>" onclick="return confirm('Cancel this train?')">Cancel</a></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>


  <div class="card-header bg-primary text-white">
    <h3 class="mb-0">Update Train Details</h3>
  </div>
  <div class="card-body">

        <form method="POST">
          <div class="form-group">
            <label>Train Number</label>
            <input type="text" name="number" class="form-control" value="<?= $train_data->number; ?>" required>
          </div>
          <div class="form-group">
            <label>Train Name</label>
            <input type="text" name="name" class="form-control" value="<?= $train_data->name; ?>" required>
          </div>
          <div class="form-group">
            <label>Route</label>
            <input type="text" name="route" class="form-control" value="<?= $train_data->route; ?>" required>
          </div>
          <div class="form-group">
            <label>Departure Station</label>
            <input type="text" name="current" class="form-control" value="<?= $train_data->current; ?>" required>
          </div>
          <div class="form-group">
            <label>Arrival Station</label>
            <input type="text" name="destination" class="form-control" value="<?= $train_data->destination; ?>" required>
          </div>
          <div class="form-group">
            <label>Departure Time</label>
            <input type="time" name="time" class="form-control" value="<?= $train_data->time; ?>" required>
          </div>
          <div class="form-group">
            <label>Total Passengers</label>
            <input type="number" name="passengers" class="form-control" value="<?= $train_data->passengers; ?>" required>
          </div>
          <div class="form-group">
            <label>Food Cost (₹)</label>
            <input type="number" name="food_cost" step="0.01" class="form-control" value="<?= $food_cost; ?>" required>
          </div>
          <div class="form-group">
            <label>AC Price (₹)</label>
            <input type="number" name="ac_price" step="0.01" class="form-control" value="<?= $ac_price; ?>" required>
          </div>
          <div class="form-group">
            <label>Non-AC Price (₹)</label>
            <input type="number" name="non_ac_price" step="0.01" class="form-control" value="<?= $non_ac_price; ?>" required>
          </div>
          <div class="form-group">
            <label>Sleeper Price (₹)</label>
            <input type="number" name="sleeper_price" step="0.01" class="form-control" value="<?= $sleeper_price; ?>" required>
          </div>
          <div class="text-right">
            <button type="submit" name="update_train" class="btn btn-primary">Update Train</button>
          </div>
        </form>
      </div>
    </div>

    <?php else: ?>
      <div class="alert alert-danger">Train not found or invalid ID.</div>
    <?php endif; ?>
  </div>
</div>

<script src="assets/lib/jquery/jquery.min.js"></script>
<script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/lib/datatables/datatables.net/js/jquery.dataTables.js"></script>
<script src="assets/lib/datatables/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(document).ready(function(){
    App.init();
    App.dataTables();
  });
</script>
</body>
</html>
