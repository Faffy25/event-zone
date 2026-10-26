<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Organizer/login.php');
    exit();
}
$conn = mysqli_connect('localhost', 'root', '', 'event_zone');

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$event_id) {
    die("No event selected.");
}

$result = mysqli_query($conn, "SELECT * FROM events WHERE event_id = $event_id LIMIT 1");
$event = mysqli_fetch_assoc($result);
if (!$event) {
    die("Event not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['event_name'];
    $desc = $_POST['event_description'];
    $start = $_POST['start_datetime'];
    $end = $_POST['end_datetime'];
    $loc = $_POST['event_location'];

    $sql = "UPDATE events SET 
            event_name='" . mysqli_real_escape_string($conn, $name) . "', 
            event_description='" . mysqli_real_escape_string($conn, $desc) . "', 
            start_datetime='$start', 
            end_datetime='$end', 
            event_location='" . mysqli_real_escape_string($conn, $loc) . "' 
            WHERE event_id=$event_id";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Event updated successfully!'); window.location.href='events.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating event: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <style>
        body { 
            font-family: Poppins, sans-serif;
            background: #f4f4f4; 
            margin: 0; 
        }
        .container { 
            max-width: 500px; 
            margin: 40px auto; 
            background: #fff; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px #0001; 
            padding: 36px; 
        }
        h2 { 
            color: #333; 
            text-align: center; 
            margin-bottom: 24px; 
        }
        form { 
            display: flex; 
            flex-direction: column; 
            gap: 16px; 
        }
        label { 
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #555; 
        }
        input, textarea, select {
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 6px; 
            font-size: 1em; 
            background: #f7f7f7;
        }
        textarea { 
            height: 80px;
            resize: vertical;
        }
        button { 
            background-color: #CA8E82;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            width: 100%; 
        }
        button:hover { 
            font-weight: bold; 
        }
        .back-link { 
            display: block; 
            text-align: center; 
            margin-top: 18px; 
            color: #888; 
            text-decoration: none; 
        }
        .back-link:hover { 
            font-weight: bold;
            color: #CA8E82;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Event</h2>
        <form method="POST">
            <label for="event_name">Event Name:</label>
            <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>

            <label for="event_description">Description:</label>
            <textarea id="event_description" name="event_description" required><?php echo htmlspecialchars($event['event_description']); ?></textarea>

            <label for="start_datetime">Start Date/Time:</label>
            <input type="datetime-local" id="start_datetime" name="start_datetime" value="<?php echo date('Y-m-d\TH:i', strtotime($event['start_datetime'])); ?>" required>

            <label for="end_datetime">End Date/Time:</label>
            <input type="datetime-local" id="end_datetime" name="end_datetime" value="<?php echo date('Y-m-d\TH:i', strtotime($event['end_datetime'])); ?>" required>

            <label for="event_location">Location:</label>
            <input type="text" id="event_location" name="event_location" value="<?php echo htmlspecialchars($event['event_location']); ?>" required>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="upcoming" <?php echo ($event['status'] == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                <option value="active" <?php echo ($event['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="completed" <?php echo ($event['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="cancelled" <?php echo ($event['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>

            <button type="submit">Update Event</button>
        </form>
    </div>
</body>
</html>