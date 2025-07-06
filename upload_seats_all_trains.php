<?php
include('db.php'); // make sure this connects to your DB

$train_ids = ['T101', 'T102', 'T103', 'T104', 'T105', 'T106'];

$seat_types = [
    'AC' => 'A1',
    'Non-AC' => 'N1',
    'Sleeper' => 'S1'
];

$seats_per_coach = 10;

foreach ($train_ids as $train_id) {
    foreach ($seat_types as $feature => $coach) {
        for ($i = 1; $i <= $seats_per_coach; $i++) {
            $seat_number = $i;

            $sql = "INSERT INTO orrs_seat (train_id, coach_number, seat_number, seat_feature, is_booked) VALUES (?, ?, ?, ?, 0)";
            $stmt = $mysqli->prepare($sql);

            if (!$stmt) {
                // Show the SQL error
                die("❌ Prepare failed for train $train_id, coach $coach, seat $seat_number: " . $mysqli->error);
            }

            $stmt->bind_param("ssis", $train_id, $coach, $seat_number, $feature);
            $stmt->execute();
        }
    }
}

echo "✅ Seats uploaded successfully for T101 to T106.";
?>
