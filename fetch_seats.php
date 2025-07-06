<?php
include('db.php'); // Or your DB connection file

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['train_id']) && isset($_POST['seat_feature'])) {
    $train_id = mysqli_real_escape_string($mysqli, $_POST['train_id']);
    $seat_feature = mysqli_real_escape_string($mysqli, $_POST['seat_feature']);

    // Get already booked seats
    $booked_query = "SELECT seat_number FROM tickets WHERE train_id = '$train_id'";
    $booked_res = mysqli_query($mysqli, $booked_query);

    $booked_seats = [];
    while ($row = mysqli_fetch_assoc($booked_res)) {
        $booked_seats[] = "'" . $row['seat_number'] . "'";
    }

    $booked_list = empty($booked_seats) ? "''" : implode(',', $booked_seats);

    // Get available seats
    $sql = "SELECT seat_number FROM orrs_seat 
            WHERE train_id = '$train_id' 
            AND seat_feature = '$seat_feature' 
            AND seat_number NOT IN ($booked_list) 
            ORDER BY seat_number";

    $result = mysqli_query($mysqli, $sql);

    $options = "";
    while ($row = mysqli_fetch_assoc($result)) {
        $options .= "<option value='" . $row['seat_number'] . "'>" . $row['seat_number'] . "</option>";
    }

    echo json_encode(["options" => $options]);
}
?>
