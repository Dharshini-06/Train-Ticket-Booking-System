<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
if (!isset($_SESSION['email'])) {
  header("Location: pass-login.php?msg=" . urlencode("Please login first."));
  exit;
}

$passenger_email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cancel Booked Train</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    include('db.php');
     include('assets/inc/sidebar.php'); ?>
    <?php include('assets/inc/navbar.php'); ?>

    <div class="be-content">
        <div class="main-content container-fluid">
            <div class="row">
                <div class="col-12">
                    <h2 class="mt-4">Your Booked Trains</h2>

                    <!-- Flash Messages -->
                    <?php if (isset($_SESSION['success_msg'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['success_msg']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success_msg']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_msg'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['error_msg']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error_msg']); ?>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover align-middle text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Passenger Name</th>
                                        <th>Train No</th>
                                        <th>Seat No</th>
                                        <th>Seat Type</th>
                                        <th>Fare</th>
                                        <th>Departure</th>
                                        <th>Arrival</th>
                                        <th>Journey Time</th>
                                        <th>Journey Date</th>
                                        <th>Coach No</th>
                                        <th>Booking Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM orrs_ticket WHERE email = ?";
                                    $stmt = $mysqli->prepare($query);
                                    $stmt->bind_param('s', $passenger_email);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0):
                                        while ($row = $result->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['passenger_name']) ?></td>
                                        <td><?= htmlspecialchars($row['train_number']) ?></td>
                                        <td><?= htmlspecialchars($row['seat_number']) ?></td>
                                        <td><?= htmlspecialchars($row['seat_feature']) ?></td>
                                        <td>₹<?= htmlspecialchars($row['fare']) ?></td>
                                        <td><?= htmlspecialchars($row['departure']) ?></td>
                                        <td><?= htmlspecialchars($row['arrival']) ?></td>
                                        <td><?= htmlspecialchars($row['journey_time']) ?></td>
                                        <td><?= htmlspecialchars($row['journey_date']) ?></td>
                                        <td><?= htmlspecialchars($row['coach_number']) ?></td>
                                        <td><?= htmlspecialchars($row['booking_date']) ?></td>
                                        <td>
                                            <a href="pass-cancel-specific-train.php?id=<?= $row['id'] ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to cancel this booking?');">
                                                Cancel
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                        <tr><td colspan="12" class="text-center">No bookings found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div> <!-- card-body -->
                    </div> <!-- card -->
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
