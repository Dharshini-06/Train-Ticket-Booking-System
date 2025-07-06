<?php
// Fetch seat types for selected train
$train_id = $_GET['train_id'];
$qry = "SELECT seat_type, price_multiplier FROM train_seats WHERE train_id = ?";
$stmt = $mysqli->prepare($qry);
$stmt->bind_param("i", $train_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<form method="post" action="confirm_booking.php">
    <input type="hidden" name="train_id" value="<?php echo $train_id; ?>">
    
    <label for="seat_type">Select Seat Type:</label>
    <select name="seat_type" id="seat_type" required onchange="updateFare()">
        <option value="" disabled selected>Select type</option>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['seat_type']}' data-multiplier='{$row['price_multiplier']}'>{$row['seat_type']}</option>";
        }
        ?>
    </select>

    <p>Base Fare: ₹<span id="base_fare"><?php echo $base_fare; ?></span></p>
    <p>Total Fare: ₹<span id="total_fare">--</span></p>

    <input type="hidden" name="base_fare" value="<?php echo $base_fare; ?>">
    <input type="hidden" id="fare_hidden" name="final_fare" value="">

    <button type="submit">Confirm Booking</button>
</form>

<script>
function updateFare() {
    const baseFare = parseFloat(document.getElementById("base_fare").innerText);
    const seatSelect = document.getElementById("seat_type");
    const selectedOption = seatSelect.options[seatSelect.selectedIndex];
    const multiplier = parseFloat(selectedOption.dataset.multiplier);
    const finalFare = (baseFare * multiplier).toFixed(2);

    document.getElementById("total_fare").innerText = finalFare;
    document.getElementById("fare_hidden").value = finalFare;
}
</script>
