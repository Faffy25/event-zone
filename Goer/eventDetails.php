<?php
$conn = new mysqli("localhost", "root", "", "event_zone");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($event_id === 0) {
    echo "<script>alert('Invalid event ID.'); window.location.href='home.php';</script>";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<script>alert('Event not found.'); window.location.href='events.php';</script>";
    exit();
}

$event = $result->fetch_assoc();
$tickets = [];
$stmt2 = $conn->prepare("SELECT ticket_type, ticket_price, ticket_total_quantity FROM tickets WHERE event_id = ?");
$stmt2->bind_param("i", $event_id);
$stmt2->execute();
$tickets_result = $stmt2->get_result();
while ($row = $tickets_result->fetch_assoc()) {
    $tickets[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details: <?php echo htmlspecialchars($event['event_name']); ?></title>
    <style>
        body {
            font-family: Poppins, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: url('/event-zone/Event-Management/projectImages/background.jpg') center/cover no-repeat;
            opacity: 0.3;
            z-index: -1;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: 20px auto;
        }
        h1 {
            color: black;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2em;
        }
        .event-content {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        .event-image {
            flex: 1;
            min-width: 400px;
        }
        .event-image img {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            border-radius: 8px;
             box-shadow: 0 4px 10px 0 rgba(0, 0, 0, 0.2), 0 10px 20px 0 rgba(0, 0, 0, 0.19);
        }
        .event-details {
            flex: 1;
            padding: 10px;
            color: black;
            font-style: bold;
        }
        .event-description {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            line-height: 1.6;
            color: black;
            font-style: bold;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section p {
            margin: 8px 0;
        }
        p strong {
            color: #2c3e50;
            font-weight: 600;
        }
        .ticket-list {
            list-style-type: none;
            padding: 0;
            margin: 15px 0;
        }
        .ticket-list li {
            background-color: #f0f0f0;
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        .back-button, .buy-button {
            display: inline-block;
            width: 180px;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .back-button {
            background-color: #CA8E82;
            color: white;
        }
        .buy-button {
            background-color: #CA8E82;
            color: white;
        }
        .back-button:hover {
            background-color: #b57d72;
            transform: translateY(-2px);
        }
        .buy-button:hover {
            background-color:#b57d72(167, 13, 133, 0.87);
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .event-content {
                flex-direction: column;
            }
            .event-image {
                min-width: 100%;
                

            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($event['event_name']); ?></h1>
        
        <div class="event-content">
            <?php if (!empty($event['cover_image_url'])): ?>
            <div class="event-image">
                <img src="<?php echo htmlspecialchars($event['cover_image_url']); ?>" alt="Event Cover Image">
            </div>
            <?php endif; ?>
            
            <div class="event-details">
                <div class="event-description">
                    <?php echo nl2br(htmlspecialchars($event['event_description'])); ?>
                </div>
                
                <div class="info-section">
                    <p><strong>Date & Time:</strong> <?php 
                        echo (new DateTime($event['start_datetime']))->format("F j, Y, g:i a");
                        if (!empty($event['end_datetime'])) {
                            echo " - " . (new DateTime($event['end_datetime']))->format("F j, Y, g:i a");
                        }
                    ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($event['event_location']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($event['status']); ?></p>
                </div>
                
                <?php if (!empty($tickets)): ?>
                <div class="info-section">
                    <p><strong>Tickets:</strong></p>
                    <ul class="ticket-list">
                        <?php foreach ($tickets as $ticket): ?>
                            <li>
                                <span><?php echo htmlspecialchars($ticket['ticket_type']); ?></span>
                                <span>ZMW <?php echo number_format($ticket['ticket_price'], 2); ?></span>
                                <span>Available: <?php echo htmlspecialchars($ticket['ticket_total_quantity']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="button-group">
            <a href="home.php" class="back-button">Back to Home</a>
            <a href="/event-zone/Event-Management/Goer/register.php?event_id=<?php echo $event_id; ?>" class="buy-button">Register for this event</a>
        </div>
    </div>
</body>
</html> 