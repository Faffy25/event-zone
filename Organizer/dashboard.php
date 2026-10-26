  <?php
session_start();
include('../Organizer/connection.php'); 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT event_id, event_name, start_datetime, end_datetime, event_location, cover_image_url, status 
        FROM events 
        WHERE organizer_user_id = '$user_id' 
        ORDER BY start_datetime DESC";
        
$result = $conn->query($sql);
$events = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - Event Zone</title>
    <style>
       body {
            font-family: Poppins, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f7f7f7;
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url(/event-zone/Event-Management/projectImages/background.jpg);
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            z-index: -1;
            opacity: 0.3;
            }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            flex-grow: 1;
        }

        h1, h2, h3, h4 {
            color: #444;
            font-weight: 600;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
            color: #CA8E82;
        }

        .header {
            background-color: #ffffff;
            padding: 1rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .header .logo img {
            height: 55px;
            transition: transform 0.3s ease;
        }

        .header .logo img:hover {
            transform: scale(1.08);
        }

        .header nav a {
            color: #6D4F46;
            text-decoration: none;
            margin-left: 25px;
            font-weight: 600;
            font-size: 1.05rem;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .header nav a:hover {
            color: #CA8E82;
            transform: translateY(-2px);
        }

        .dashboard-actions {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-bottom: 50px;
            padding-top: 20px;
        }

        .dashboard-actions .btn {
            text-decoration: none; 
            color: #fff; 
            background: #ca8f82; 
            padding: 14px 32px; 
            border-radius: 7px; 
            font-weight: bold; 
            font-size: 1.1em; 
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 10px rgba(202, 142, 130, 0.3);
        }

        .dashboard-actions .btn:hover {
            background-color: #6D4F46;
            transform: translateY(-3px);
        }

        .events-section h2 {
            text-align: center;
            color: #6D4F46;
            margin-bottom: 35px;
            padding-bottom: 15px;
            border-bottom: 2px solid #F2D6CE;
            font-size: 2rem;
        }

        .event-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            padding-bottom: 30px;
        }

        .event-card { 
            flex: 0 1 calc(33.333% -30px);
            min-width: 300px;
            max-width: 400px;
            background-color: #ffffff;
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .event-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .event-card-content {
            padding: 20px;
            flex-grow: 1;
        }

        .event-card h3 {
            margin-top: 0;
            margin-bottom: 12px;
            color: #CA8E82;
            font-size: 1.5rem;
            line-height: 1.3;
        }

        .event-card p {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 8px;
        }
        .event-card p strong {
            color: #333;
        }

        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 0.8em;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 5px;
        }
        .status.upcoming { background-color: #8C8C8C; }
        .status.active { background-color: #4CAF50; }
        .status.completed { background-color: #2196F3; }
        .status.cancelled { background-color: #F44336; }

        .event-stats {
            background-color: #FDF9F7;
            border-top: 1px solid #F2D6CE;
            margin-top: 15px;
            padding: 12px;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        .event-stats h4 {
            margin-top: 0;
            margin-bottom: 8px;
            color: #6D4F46;
            font-size: 1.05rem;
            border-bottom: 1px dotted #e0e0e0;
            padding-bottom: 5px;
        }
        .event-stats p {
            margin: 5px 0;
            color: #444;
        }
        .event-stats p strong {
            font-weight: 700;
            color: #CA8E82;
        }

        .event-card .event-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            padding: 15px;
            border-top: 1px solid #eee;
            background-color: #fcfcfc;
        }

        .event-card .event-actions a {
            flex: 1 1 auto;
            text-align: center;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            white-space: nowrap;
        }

        .event-card .event-actions a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .event-card .event-actions a.edit-btn {
            background-color: #6D4F46;
            color: white;
        }
        .event-card .event-actions a.edit-btn:hover { background-color: #5A4038; }

        .event-card .event-actions a.manage-attendance-btn {
            background-color: #6D4F46;
            color: white;
        }
        .event-card .event-actions a.manage-attendance-btn:hover { background-color: #5A4038; }

        .event-card .event-actions a.view-btn {
            background-color: #6D4F46;
            color: white;
            border: 1px solid #E0C0B0;
        }
        .event-card .event-actions a.view-btn:hover { background-color: #5A4038; }

        .event-card .event-actions a.delete-btn {
            background-color: #EF5350;
            color: white;
        }
        .event-card .event-actions a.delete-btn:hover { background-color: #D32F2F; }

        .no-events {
            text-align: center;
            padding: 60px;
            font-size: 1.2rem;
            color: #888;
            background-color: #fcfcfc;
            border-radius: 8px;
            margin-top: 30px;
            box-shadow: inset 0 1px 5px rgba(0,0,0,0.05);
        }

        .footer {
            background-color: #ffffff;
            color: #f4f7f6;
            padding: 40px 0 20px 0;
            margin-top: 50px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .footer .container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            background-color: transparent;
            box-shadow: none;
            border-radius: 0;
        }

        .footer-columns {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
        }

        .footer-col h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 18px;
            color: #CA8E82;
        }

        .footer-col p, .footer-col ul {
            font-size: 15px;
            color: #6D4F46;
        }

        .footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-col ul li {
            margin-bottom: 10px;
        }

        .footer-col ul li a {
            color: #6D4F46;
            text-decoration: none;
            transition: color 0.3s ease, transform 0.3s ease;
            display: inline-block;
        }

        .footer-col ul li a:hover {
            color: #CA8E82;
            transform: translateX(5px);
        }

        .footer-bottom {
            text-align: center;
            margin-top: 35px;
            padding-top: 20px;
            border-top: 1px solid rgba(109, 79, 70, 0.2);
            font-size: 14px;
            color: #6D4F46;
        }

        @media (max-width: 992px) {
            .header {
                padding: 1rem 1.5rem;
            }
            .header nav a {
                margin-left: 15px;
                font-size: 1rem;
            }
            .container {
                margin: 15px auto;
                padding: 15px;
            }
            h1 {
                font-size: 2rem;
            }
            .dashboard-actions .btn {
                padding: 12px 25px;
                font-size: 1rem;
            }
            .events-section h2 {
                font-size: 1.75rem;
            }
            .event-list {
                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                gap: 20px;
            }
            .event-card h3 {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 1rem;
                text-align: center;
            }
            .header nav {
                margin-top: 15px;
            }
            .header nav a {
                margin: 0 10px;
            }
            .dashboard-actions {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            .dashboard-actions .btn {
                width: 90%;
                max-width: 300px;
            }
            .event-list {
                grid-template-columns: 1fr;
                padding: 0 10px;
            }
            .event-card {
                margin: 0 auto;
                max-width: 400px;
            }
            .footer-columns {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .footer-col ul {
                margin-top: 10px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8rem;
            }
            .events-section h2 {
                font-size: 1.5rem;
            }
            .event-card-content {
                padding: 15px;
            }
            .event-card h3 {
                font-size: 1.2rem;
            }
            .event-card p {
                font-size: 0.85rem;
            }
            .event-stats {
                font-size: 0.8rem;
            }
            .event-card .event-actions a {
                font-size: 0.8rem;
                padding: 8px 10px;
            }
            .footer-col h3 {
                font-size: 18px;
            }
            .footer-col p, .footer-col ul {
                font-size: 13px;
            }
            .footer-bottom {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <a href="/dashboard.php">
            <img src="/event-zone/Event-Management/projectImages/eventZoneLogo.png" alt="Event Zone Logo" style="width: 150px; height: auto;">
            </a>
        </div>
        <nav>
            <a href="createEventForm.php">Create Event</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <h1>Organizer Dashboard</h1>

        <div class="dashboard-actions">
            <a href="createEventForm.php" class="btn">Create New Event</a>
        </div>

        <div class="events-section">
            <h2>Your Events</h2>
            <div class="event-list" id="organizerEventsList">
                <?php
                if (!empty($events)) {
                    foreach ($events as $event) {
                        $event_id = htmlspecialchars($event['event_id']);
                        $event_name = htmlspecialchars($event['event_name']);

                        $start_datetime = new DateTime($event['start_datetime']);
                        $end_datetime = new DateTime($event['end_datetime']);
                        $formatted_start = $start_datetime->format("F j, Y, g:i a");
                        $formatted_end = $end_datetime->format("F j, Y, g:i a");

                        $event_location = htmlspecialchars($event['event_location']);
                        $cover_image_url = htmlspecialchars($event['cover_image_url']);
                        $status_class = strtolower(htmlspecialchars($event['status']));

                        echo '
                        <div class="event-card">
                            <img src="' . $cover_image_url . '" alt="' . $event_name . ' Cover Image">
                            <div class="event-card-content">
                                <h3>' . $event_name . ' <span class="status ' . $status_class . '">' . htmlspecialchars($event['status']) . '</span></h3>
                                <p><strong>Start:</strong> ' . $formatted_start . '</p>
                                <p><strong>End:</strong> ' . $formatted_end . '</p>
                                <p><strong>Location:</strong> ' . $event_location . '</p>
                                
                            </div>
                            <div class="event-actions">
                                <a href="eventDetails.php?id=' . $event_id . '" class="view-btn">View Details</a>
                                <a href="editEvent.php?id=' . $event_id . '" class="edit-btn">Edit Event</a>
                                <a href="deleteEvent.php?id=' . $event_id . '" class="delete-btn" onclick="return confirm(\'Are you sure you want to delete this event? This action cannot be undone.\');">Delete</a>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p class="no-events">You haven\'t created any events yet. Click "Create New Event" to get started!</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-columns">
                <div class="footer-col">
                    <h3>EventZone</h3>
                    <p>Your ultimate partner in creating memorable event experiences.</p>
                </div>
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="/event-zone/Event-Management/Organizer/index.php">Home</a></li>
                        <li><a href="/event-zone/Event-Management/Organizer/events.php">Events</a></li>
                        <li><a href="/event-zone/Event-Management/Organizer/login.html">Login</a></li>
                        <li><a href="/event-zone/Event-Management/Organizer/register.html">Sign Up</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contact Us</h3>
                    <p>Email: info@eventzone.com</p>
                    <p>Phone: +260 976 678 87</p>
                    <p>Address: Eden University, Lusaka, Zambia</p>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; 2025 EventZone. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>

<?php
$conn->close();
?>