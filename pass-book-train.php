<?php
include("db.php");
session_start();

if (!isset($_GET['train_id'])) {
    die("Train ID is missing.");
}

$train_id = trim($_GET['train_id']);
if (empty($train_id) || !preg_match('/^T\d+$/', $train_id)) {
    die("Invalid Train ID format.");
}

// Fetch train details
$stmt = mysqli_prepare($mysqli, "SELECT number, name, current, destination, time FROM orrs_train WHERE number = ?");
mysqli_stmt_bind_param($stmt, "s", $train_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Train not found.");
}

$train = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Train</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background:
                linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('images/360_F_1055396548_IJFVICX3EmAld9WVN9i8wuBtp97n3YuK.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .container {
            width: 600px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #007bff;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        input[readonly] {
            background-color: #f0f0f0;
        }

        button {
            margin-top: 25px;
            width: 100%;
            background-color: #007bff;
            color: #fff;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Book Train: <?php echo htmlspecialchars($train['name']); ?></h2>
        <form action="pass-process-booking.php" method="post">
            <input type="hidden" name="train_id" value="<?php echo htmlspecialchars($train['number']); ?>">

            <label>Passenger Name:</label>
            <input type="text" name="passenger_name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Passenger Age:</label>
            <input type="number" name="passenger_age" min="0" max="120" required>

            <label>Train Name:</label>
            <input type="text" value="<?php echo htmlspecialchars($train['name']); ?>" readonly>

            <label>Train Number:</label>
            <input type="text" value="<?php echo htmlspecialchars($train['number']); ?>" readonly>

            <label>Seat Feature:</label>
            <select name="seat_feature" id="seat_feature" required>
                <option value="">Select</option>
                <option value="AC">AC</option>
                <option value="Non-AC">Non-AC</option>
                <option value="Sleeper">Sleeper</option>
            </select>

            <label>Select Coach and Seats:</label>
            <select name="seat_number[]" id="seat_number" multiple required>
                <option disabled>Select a seat feature first</option>
            </select>
            <small>Hold Ctrl (or Cmd on Mac) to select multiple seats</small>

            <label>Do you want food service?</label>
            <select name="food_required" required>
                <option value="">Select</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <label>Fare (Calculated after submission):</label>
            <input type="text" value="Dynamic Fare" readonly>

            <label>Departure Station:</label>
            <input type="text" value="<?php echo htmlspecialchars($train['current']); ?>" readonly>

            <label>Arrival Station:</label>
            <input type="text" value="<?php echo htmlspecialchars($train['destination']); ?>" readonly>

            <label>Time:</label>
            <input type="text" value="<?php echo htmlspecialchars($train['time']); ?>" readonly>

            <label>Date of Journey:</label>
            <input type="date" name="journey_date" required>

            <button type="submit">Confirm Booking</button>
        </form>
    </div>

    <script>
    document.getElementById("seat_feature").addEventListener("change", function () {
        const feature = this.value;
        const trainId = "<?php echo htmlspecialchars($train['number']); ?>";

        if (feature) {
            fetch(`fetch-seats.php?train_id=${trainId}&feature=${feature}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("seat_number").innerHTML = data;
                })
                .catch(error => {
                    console.error("Error loading seats:", error);
                    document.getElementById("seat_number").innerHTML = "<option disabled>Error loading seats</option>";
                });
        } else {
            document.getElementById("seat_number").innerHTML = "<option disabled>Select a seat feature first</option>";
        }
    });
    </script>
</body>
</html>

<?php mysqli_close($mysqli); ?>
