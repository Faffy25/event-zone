<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Organizer/login.php');
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'event_zone');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT roleName FROM roles r JOIN user_role ur ON ur.role_id = r.roleID WHERE ur.user_id = $user_id LIMIT 1");
$row = mysqli_fetch_assoc($result);
if (!$row || $row['roleName'] !== 'Admin') {
    die('Access denied. Admins only.');
}

if (!isset($_GET['id'])) {
    header('Location: events.php');
    exit();
}

$event_id = intval($_GET['id']);
mysqli_query($conn, "DELETE FROM events WHERE event_id = $event_id");

header('Location: events.php');
exit();