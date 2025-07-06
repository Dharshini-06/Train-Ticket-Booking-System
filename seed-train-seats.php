<?php
session_start();
include('../db.php'); // Adjust the path if needed

// Configuration: Number of seats and prices per type
$seat_config = [
    ['type' => 'AC', 'price' => 500, 'count' => 20],
    ['type' => 'Non-AC', 'price' => 200, 'count' => 30],
    ['type' => 'Sleeper', 'price' => 300, 'count' => 50]
];

// Fetch all trains
$train_query = "SELECT id FROM orrs_train";
$trains = $mysqli->query($train_query);

if ($trains->num_rows > 0) {
    while ($train = $trains->fetch_assoc()) {
        $train_id = $train['id'];

        // Check if seats already exist
        $check = $mysqli->prepare("SELECT COUNT(*) as count FROM orrs_seats WHERE train_id=?");
        $check->bind_param("i", $train_id);
        $check->execute();
        $result = $check->get_result()->fetch_assoc();
        $check->close();

        if ($result['count'] > 0) {
            echo "Skipping Train ID $train_id — seats already exist.<br>";
            continue;
        }

        // Insert seat records
        $insert = $mysqli->prepare("INSERT INTO orrs_seats (train_id, seat_type, price, reserved) VALUES (?, ?, ?, 0)");

        foreach ($seat_config as $seat) {
            for ($i = 0; $i < $seat['count']; $i++) {
                $insert->bind_param("isi", $train_id, $seat['type'], $seat['price']);
                $insert->execute();
            }
        }

        $insert->close();
        echo "Seeded Train ID $train_id with default seats.<br>";
    }
} else {
    echo "No trains found.";
}
?>
