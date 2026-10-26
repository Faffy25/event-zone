<?php
session_start();
include('../Organizer/connection.php'); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];

    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $event_location = $_POST['event_location'];
    $category_id = $_POST['category_id'];
    $contact_name = $_POST['contact_person_name'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = $_POST['contact_phone'];

    $cover_image_url = "";
    $target_dir = "../event-zone/Event-Management/images/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (isset($_FILES["coverImage"]) && $_FILES["coverImage"]["error"] == UPLOAD_ERR_OK) {
        $coverImageName = uniqid() . "_" . basename($_FILES["coverImage"]["name"]);
        $target_file = $target_dir . $coverImageName;
        move_uploaded_file($_FILES["coverImage"]["tmp_name"], $target_file);
        $cover_image_url = $target_file;
    }

    $sql = "INSERT INTO events (organizer_user_id, event_name, event_description, event_location, 
            category_id, cover_image_url, contact_person_name, contact_email, 
            contact_phone, status, is_featured, start_datetime, end_datetime)
            VALUES ('$user_id', '$event_name', '$event_description', '$event_location', 
            '$category_id', '$cover_image_url', '$contact_name', '$contact_email', 
            '$contact_phone', 'upcoming', '0', '$start_datetime', '$end_datetime')";

    if ($conn->query($sql) === TRUE) {
        $event_id = $conn->insert_id;
        
        if (isset($_POST['ticket_type']) && is_array($_POST['ticket_type'])) {
            $ticket_types = $_POST['ticket_type'];
            $ticket_prices = $_POST['ticket_price'];
            $ticket_quantities = $_POST['ticket_total_quantity'];
            
            for ($i = 0; $i < count($ticket_types); $i++) {
                $type = $ticket_types[$i];
                $price = $ticket_prices[$i];
                $quantity = $ticket_quantities[$i];
                
                $sql_ticket = "INSERT INTO tickets (event_id, ticket_type, ticket_price, ticket_total_quantity, ticket_sold_quantity) 
                               VALUES ('$event_id', '$type', '$price', '$quantity', '0')";
                $conn->query($sql_ticket);
            }
        }
        
        echo "<script>alert('Event created successfully!'); window.location.href='Dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.history.back();</script>";
    }
    
    $conn->close();
} else {
    header("Location: create_event_form.php");
    exit();
}
?>