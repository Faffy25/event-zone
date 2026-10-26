<?php
include('../Organizer/connection.php'); 

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$mobile = $_POST['mobile'];
$role_id = $_POST['role_id']; 

$sql = "INSERT INTO users (firstname, lastname, email, password, mobile, role_id)
VALUES ('$firstname', '$lastname', '$email', '$password', '$mobile', '$role_id')";

if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
    header("Location: login.html");
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>