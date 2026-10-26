<?php
session_start();
include('../Organizer/connection.php'); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, password, role_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password, $role_id);

        if ($stmt->fetch() && password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role_id;
             if ($role_id == 2) {
                    $_SESSION['organizer_id'] = $id;
                }

            switch ($role_id) {
                case 1:
                    header("Location: /event-zone/EVENT-MANAGEMENT/Goer/home.php");
                    break;
                case 2:
                    header("Location: /event-zone/EVENT-MANAGEMENT/Organizer/events.php");
                    break;
                case 3:
                    header("Location: ../Admin/index.php");
                    break;
                default:
                    session_destroy();
                    header("Location: login.html");
            }
            exit();
        } else {
            echo "Invalid email or password.";
        }

        $stmt->close();
    } else {
        echo "Please enter both email and password.";
    }

    $conn->close();
}
?>