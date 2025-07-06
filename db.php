<?php
$servername = "127.0.0.1";
$username = "root";
$password = "admin"; // Default XAMPP password for root is empty
$database = "orrsphp";
$port = 3309; // Default XAMPP MySQL port

$mysqli = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
} 
?>