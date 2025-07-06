<?php
session_start();
include('../db.php');
include('assets/inc/checklogin.php');
check_login();

// Insert seat if form submitted
if (isset($_POST['submit'])) {
    $train_id = $_POST['train_id'];
    $seat_no = $_POST['seat_no'];
    $seat_type = $_POST['seat_type'];
    $price = $_POST['price'];
    $reserved = isset($_POST['reserved']) ? 1 : 0;

    $query = "INSERT INTO orrs_seats (train_id, seat_no, seat_type, price, reserved) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("issdi", $train_id, $seat_no, $seat_type, $price, $reserved);
    if ($stmt->execute()) {
        $msg = "Seat added successfully!";
    } else {
        $err = "Failed to add seat!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include('assets/inc/head.php'); ?>
<body>
<?php include('assets/inc/navbar.php'); ?>
<?php include('assets/inc/sidebar.php'); ?>
<div class="be-content">
    <div class="main-content container-fluid">
        <div class="row">
            <div class="col-sm-6 offset-sm-3">
                <div class="card">
                    <div class="card-header">Add Seat</div>
                    <div class="card-body">
                        <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
                        <?php if (isset($err)) echo "<div class='alert alert-danger'>$err</div>"; ?>

                        <form method="post">
                            <div class="form-group">
                                <label>Select Train</label>
                                <select name="train_id" class="form-control" required>
    <option value="">-- Select Train --</option>
    <?php
    $train_selected = isset($_GET['train_id']) ? $_GET['train_id'] : '';
    $result = $mysqli->query("SELECT id, name FROM orrs_train");
    while ($row = $result->fetch_assoc()) {
        $selected = ($row['id'] == $train_selected) ? 'selected' : '';
        echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
    }
    ?>
</select>

                            </div>
                            <div class="form-group">
                                <label>Seat No</label>
                                <input type="text" name="seat_no" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Seat Type</label>
                                <select name="seat_type" class="form-control" required>
                                    <option value="AC">AC</option>
                                    <option value="Non-AC">Non-AC</option>
                                    <option value="Sleeper">Sleeper</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" name="price" step="0.01" class="form-control" required>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" name="reserved" class="form-check-input" id="reservedCheck">
                                <label class="form-check-label" for="reservedCheck">Reserved</label>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Add Seat</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/lib/jquery/jquery.min.js"></script>
<script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
