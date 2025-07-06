<?php
include("db.php");

if (!isset($_GET['train_id']) || !isset($_GET['feature'])) {
    http_response_code(400);
    echo "<option disabled>Missing parameters</option>";
    exit;
}

$train_id = $_GET['train_id'];
$feature = $_GET['feature'];

$stmt = mysqli_prepare($mysqli, "SELECT coach_number, seat_number, seat_feature, is_booked 
                                 FROM orrs_seat 
                                 WHERE train_id = ? AND seat_feature = ? 
                                 ORDER BY coach_number, seat_number");
mysqli_stmt_bind_param($stmt, "ss", $train_id, $feature);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$options = "";
$seenSeats = [];

while ($row = mysqli_fetch_assoc($result)) {
    $key = $row['coach_number'] . "-" . $row['seat_number'];

    if (in_array($key, $seenSeats)) {
        continue;  // skip duplicates
    }
    $seenSeats[] = $key;

    $text = "Coach " . htmlspecialchars($row['coach_number']) . " - Seat " . htmlspecialchars($row['seat_number']) . " (" . htmlspecialchars($row['seat_feature']) . ")";
    $disabled = $row['is_booked'] ? "disabled style='text-decoration: line-through; color: #888;'" : "";
    $options .= "<option value=\"$key\" $disabled>$text</option>";

    }

mysqli_stmt_close($stmt);
mysqli_close($mysqli);

echo $options;
?>
