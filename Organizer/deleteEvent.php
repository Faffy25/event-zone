<?php
session_start();
include('../Organizer/connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = "DELETE FROM events WHERE event_id = '$event_id' AND organizer_user_id = '$user_id'";

if ($conn->query($sql)) {
    echo "<script>alert('Event deleted successfully!'); window.location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('Error deleting event.'); window.location.href='dashboard.php';</script>";
}

$conn->close();
?>