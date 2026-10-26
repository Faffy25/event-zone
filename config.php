<?php

$conn = new mysqli("localhost", "root", "", "event_zone");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>
