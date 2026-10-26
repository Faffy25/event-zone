<?php
session_start();
include('../Organizer/connection.php');

if (!isset($_SESSION['organizer_id'])) {
    header('Location: login.html');
    exit();
}

$events = [];

$sql = "SELECT event_id, event_name, start_datetime, end_datetime, event_location, cover_image_url, status FROM events ORDER BY start_datetime DESC";

$result = $conn->query($sql);

if (!$result) {
    die("SQL query failed: " . $conn->error);
}

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Zone - Discover Events</title>

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
            inset: 0;
            background: url('/event-zone/Event-Management/projectImages/background.jpg') center/cover no-repeat;
            opacity: 0.3;
            z-index: -1;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .navbar {
            background-color: #ffffff;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: nowrap;
        }

        .navbar-brand a {
            font-size: 28px;
            font-weight: 700;
            color: #CA8E82;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 50px;
            width: auto;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }
        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        .mobile-menu-toggle {
            display: none;
        }

        .mobile-menu-toggle button {
            background: none;
            border: none;
            font-size: 28px;
            color: #CA8E82;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-nav-desktop {
            display: flex;
            gap: 30px;
            align-items: center;
            margin: 0 auto;
        }

        .navbar-nav-desktop a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            font-size: 17px;
            transition: color 0.3s ease, border-bottom 0.3s ease;
            padding-bottom: 5px;
        }

        .navbar-nav-desktop a:hover,
        .navbar-nav-desktop a.active {
            color: #CA8E82;
            border-bottom: 2px solid #CA8E82;
        }

        .navbar-auth-desktop {
            display: flex;
            gap: 15px;
        }

        .navbar-collapse-mobile {
            display: none;
            flex-direction: column;
            width: 100%;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            z-index: 999;
            transform: translateY(-100%);
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
            opacity: 0;
            pointer-events: none;
        }

        .navbar-collapse-mobile.active {
            display: flex;
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .navbar-nav-mobile {
            display: flex;
            margin: 0;
            gap: 15px;
            flex-direction: column;
            width: 100%;
            padding: 0 20px;
            align-items: flex-start;
        }

        .navbar-nav-mobile a {
            font-size: 18px;
            width: 100%;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            color: #555;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .navbar-nav-mobile a:last-child {
            border-bottom: none;
        }

        .navbar-nav-mobile a:hover {
            color: #CA8E82;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropbtn {
            background-color: transparent;
            color: #555;
            padding: 10px 0;
            font-size: 17px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dropbtn:hover {
            color: #CA8E82;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 180px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 8px;
            overflow: hidden;
            top: 100%;
            left: 0;
            padding: 5px 0;
        }

        .dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 16px;
            transition: background-color 0.3s ease;
            white-space: nowrap;
        }

        .dropdown-content a:hover {
            background-color: #E0C0B0;
            color: #CA8E82;
        }

        .navbar-nav-desktop .dropdown:hover .dropdown-content {
            display: block;
        }

        .navbar-nav-mobile .dropdown {
            width: 100%;
        }

        .navbar-nav-mobile .dropbtn {
            width: 100%;
            justify-content: space-between;
        }

        .navbar-nav-mobile .dropdown-content {
            position: static;
            box-shadow: none;
            min-width: unset;
            width: 100%;
            background-color: #F2D6CE;
            border-radius: 0;
            padding: 0;
            margin-top: 5px;
        }

        .navbar-nav-mobile .dropdown-content a {
            padding: 8px 30px;
            font-size: 16px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            text-align: center;
            white-space: nowrap;
        }

        .btn-logout {
            background-color: #CA8E82;
            color: #ffffff;
            border: 1px solid #CA8E82;
        }

        .btn-logout:hover {
            background-color: #B07B6C;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-dashboard {
            background-color: #CA8E82;
            color: #ffffff;
            border: 1px solid #CA8E82;
        }

        .btn-dashboard:hover {
            background-color: #B07B6C;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .navbar-auth-mobile {
            display: flex;
            margin-top: 20px;
            flex-direction: column;
            width: 100%;
            padding: 0 20px;
            gap: 10px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .navbar-auth-mobile .btn {
            width: 100%;
        }

        main {
            flex-grow: 1;
        }

        .hero-section {
            background-color: #F2D6CE;
            color: #6D4F46;
            padding: 80px 20px;
            text-align: center;
            border-bottom: 1px solid #E0C0B0;
            margin-bottom: 40px;
        }

        .hero-section h2 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            color: #CA8E82;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }

        .hero-section p {
            font-size: 1.3rem;
            max-width: 800px;
            margin: 0 auto 30px auto;
            line-height: 1.5;
            color: #6D4F46;
        }

        .hero-actions .btn {
            background-color: #6D4F46;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin: 0 15px;
            display: inline-block;
        }

        .hero-actions .btn:hover {
            background-color: #CA8E82;
            transform: translateY(-3px);
        }

        .featured-events-section {
            padding: 40px 0;
            margin-bottom: 40px;
            text-align: center;
        }
        .featured-events-section h2 {
            font-size: 2.5rem;
            color: #6D4F46;
            margin-bottom: 40px;
            position: relative;
            padding-bottom: 15px;
        }
        .featured-events-section h2::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #CA8E82;
            border-radius: 2px;
        }

        .event-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding-bottom: 30px;
        }
        .event-card {
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
        .event-card .event-actions {
            display: flex;
            justify-content: center;
            padding: 15px;
            border-top: 1px solid #eee;
            background-color: #fcfcfc;
        }
        .event-card .event-actions a.view-btn {
            background-color: #6D4F46;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .event-card .event-actions a.view-btn:hover {
            background-color: #CA8E82;
            transform: translateY(-2px);
        }
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
            background-color: #6D4F46;
            color: #f4f7f6;
            padding: 40px 0 20px 0;
            margin-top: auto;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .footer-col h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #ffffff;
        }

        .footer-col p, .footer-col ul {
            font-size: 14px;
            color: #F2D6CE;
        }

        .footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-col ul li {
            margin-bottom: 8px;
        }

        .footer-col ul li a {
            color: #F2D6CE;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-col ul li a:hover {
            color: #ffffff;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 13px;
            color: #D6B6A8;
        }

        @media (max-width: 992px) {
            .hero-section h2 {
                font-size: 3rem;
            }
            .hero-section p {
                font-size: 1.15rem;
            }
            .navbar-nav-desktop {
                gap: 20px;
            }
            .navbar-auth-desktop {
                gap: 10px;
            }
            .btn {
                padding: 8px 15px;
                font-size: 15px;
            }
            .event-list {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
            }
            .event-card img {
                height: 180px;
            }
        }

        @media (max-width: 768px) {
            .navbar .container {
                padding: 0 15px;
            }

            .navbar-nav-desktop,
            .navbar-auth-desktop {
                display: none;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .navbar-collapse-mobile {
            }

            .hero-section {
                padding: 60px 15px;
                margin-bottom: 30px;
            }
            .hero-section h2 {
                font-size: 2.5rem;
            }
            .hero-section p {
                font-size: 1rem;
            }
            .hero-actions .btn {
                padding: 12px 25px;
                font-size: 1rem;
                margin: 10px auto;
                display: block;
                width: 80%;
                max-width: 250px;
            }
            .features {
                grid-template-columns: 1fr;
            }
            .event-list {
                grid-template-columns: 1fr;
                padding: 0 15px;
            }
            .event-card {
                max-width: 400px;
                margin: 0 auto;
            }
            .featured-events-section h2 {
                font-size: 2rem;
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
            .navbar-brand img {
                height: 40px;
            }
            .mobile-menu-toggle button {
                font-size: 24px;
            }
            .hero-section h2 {
                font-size: 2rem;
            }
            .hero-section p {
                font-size: 0.9rem;
            }
            .event-card img {
                height: 160px;
            }
            .event-card h3 {
                font-size: 1.3rem;
            }
            .event-card p {
                font-size: 0.85rem;
            }
            .event-card .event-actions a.view-btn {
                font-size: 0.9rem;
                padding: 8px 15px;
            }
            .footer-col h3 {
                font-size: 18px;
            }
            .footer-col p, .footer-col ul {
                font-size: 13px;
            }
            .footer-bottom {
                font-size: 11px;
            } 
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="/event-zone/Event-Management/Organizer/index.php">
                    <img src="/event-zone/Event-Management/projectImages/eventZoneLogo.png" alt="Event Zone Logo" style="width: 150px; height: auto;">
                </a>
            </div>

            <div class="mobile-menu-toggle">
                <button id="menu-toggle" aria-label="Toggle navigation menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="navbar-nav-desktop">
                <a href="/event-zone/Event-Management/Organizer/index.php" class="active">Home</a>
                <div class="dropdown">
                    <button class="dropbtn">Events <i class="fas fa-caret-down"></i></button>
                    <div class="dropdown-content">
                        <a href="../Goer/events.php?category=concerts-music">Concerts & Music</a>
                        <a href="../Goer/events.php?category=conferences-business">Conferences & Business</a>
                        <a href="../Goer/events.php?category=workshops-education">Workshops & Education</a>
                        <a href="../Goer/events.php?category=weddings-celebrations">Weddings & Celebrations</a>
                        <a href="../Goer/events.php?category=sports-fitness">Sports & Fitness</a>
                        <a href="../Goer/events.php?category=food-drink">Food & Drink</a>
                        <a href="../Goer/events.php?category=art-culture">Art & Culture</a>
                        <a href="../Goer/events.php?category=other">Other</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn">How It Works</button>
                    <div class="dropdown-content">
                        <a href="/event-zone/Event-Management/Goer/forTheGoer.php">For the Goer</a>
                        <a href="/event-zone/Event-Management/Goer/forTheOrganizer.php">For the Organizer</a>
                    </div>
                </div>
            </nav>

            <div class="navbar-auth-desktop">
                <a href="/event-zone/Event-Management/Organizer/dashboard.php" class="btn btn-dashboard">Dashboard</a>
                <a href="/event-zone/Event-Management/Organizer/logout.php" class="btn btn-logout">Logout</a>
            </div>

            <div class="navbar-collapse-mobile" id="nav-menu-mobile">
                <nav class="navbar-nav-mobile">
                    <a href="/event-zone/Event-Management/Organizer/index.php">Home</a>
                    <div class="dropdown">
                        <button class="dropbtn">Events <i class="fas fa-caret-down"></i></button>
                        <div class="dropdown-content">
                            <a href="../Goer/events.php?category=concerts-music">Concerts & Music</a>
                            <a href="../Goer/events.php?category=conferences-business">Conferences & Business</a>
                            <a href="../Goer/events.php?category=workshops-education">Workshops & Education</a>
                            <a href="../Goer/events.php?category=weddings-celebrations">Weddings & Celebrations</a>
                            <a href="../Goer/events.php?category=sports-fitness">Sports & Fitness</a>
                            <a href="../Goer/events.php?category=food-drink">Food & Drink</a>
                            <a href="../Goer/events.php?category=art-culture">Art & Culture</a>
                            <a href="../Goer/events.php?category=other">Other</a>
                        </div>
                    </div>
                    <div class="dropdown">
                    <button class="dropbtn">How It Works </button>
                    <div class="dropdown-content">
                        <a href="/event-zone/Event-Management/Goer/forTheGoer.php">For the Goer</a>
                        <a href="/event-zone/Event-Management/Goer/forTheOrganizer.php">For the Organizer</a>
                    </div>
                </div>
                </nav>
                <div class="navbar-auth-mobile">
                    <a href="/event-zone/Event-Management/Organizer/dashboard.php" class="btn btn-dashboard">Dashboard</a>
                    <a href="/event-zone/Event-Management/Organizer/logout.php" class="btn btn-logout">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-section">
            <div class="container">
                <h2>One Zone, Endless Possibilities!</h2>
                <p>Discover, explore, and attend amazing events around you.</p>
                <div class="hero-actions">
                    <a href="/event-zone/Event-Management/Organizer/dashboard.php" class="btn">Dashboard</a>
                    <a href="/event-zone/Event-Management/Organizer/createEventForm.php" class="btn">Create an Event</a>
                </div>
            </div>
        </section>

        <section class="featured-events-section container">
            <div class="event-list" id="featuredEvents">
            <?php
                if (!empty($events)) {
                    foreach ($events as $event) {
                        $event_id = htmlspecialchars($event['event_id']);
                        $event_name = htmlspecialchars($event['event_name']);
                        $event_location = htmlspecialchars($event['event_location']);
                        $cover_image_url = htmlspecialchars($event['cover_image_url']);

                        $start_datetime = new DateTime($event['start_datetime']);
                        $formatted_datetime = $start_datetime->format("F j, Y, g:i a");

                        echo '
                        <div class="event-card">
                            <img src="' . $cover_image_url . '" alt="' . $event_name . ' Cover Image">
                            <div class="event-card-content">
                                <h3>' . $event_name . '</h3>
                                <p><strong>Date & Time:</strong> ' . $formatted_datetime . '</p>
                                <p><strong>Location:</strong> ' . $event_location . '</p>
                            </div>
                            <div class="event-actions">
                                <a href="/event-zone/Event-Management/Organizer/eventDetails.php?id=' . $event_id . '" class="view-btn">View Details</a>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p class="no-events">No upcoming events currently. Check back soon!</p>';
                }
                ?>
            </div>
        </section>
    </main>

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
                        <li><a href="/event-zone/Event-Management/Organizer/dashboard.php">Dashboard</a></li>
                        <li><a href="/event-zone/Event-Management/Organizer/logout.php">Log Out</a></li>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const menuToggle = document.getElementById('menu-toggle');
            const navMenu = document.getElementById('nav-menu-mobile');

            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    this.querySelector('i').classList.toggle('fa-bars');
                    this.querySelector('i').classList.toggle('fa-times');
                });
            }

            document.querySelectorAll('.dropbtn').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const dropdownContent = this.nextElementSibling;
                    if (dropdownContent && dropdownContent.classList.contains('dropdown-content')) {
                        document.querySelectorAll('.dropdown-content').forEach(otherContent => {
                            if (otherContent !== dropdownContent) {
                                otherContent.style.display = 'none';
                            }
                        });
                        dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
                    }
                });
            });

            document.addEventListener('click', function(event) {
                if (!event.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-content').forEach(content => {
                        content.style.display = 'none';
                    });
                }
            });
        });
    </script>
</body>
</html>