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
$result = mysqli_query($conn, "SELECT r.roleName FROM users u JOIN user_role ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.roleID WHERE u.id = $user_id LIMIT 1");
$row = mysqli_fetch_assoc($result);

if (!$row || $row['roleName'] !== 'Admin') {
    die('Access denied. Admins only.');
}

if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit();
}

$delete_user_id = intval($_GET['id']);

if ($delete_user_id <= 0) {
    header('Location: users.php');
    exit();
}

$delete_query = "DELETE FROM users WHERE id = $delete_user_id";
$delete_result = mysqli_query($conn, $delete_query);

if ($delete_result) {

    header('Location: users.php');
} else {

    die('Error deleting user: ' . mysqli_error($conn));
}

mysqli_close($conn);
exit();
?>
