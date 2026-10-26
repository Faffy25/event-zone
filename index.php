<?php
session_start();
require_once 'Organizer/connection.php';

function getFeaturedEvents($conn) {
    $checkQuery = "SELECT COUNT(*) as count FROM events WHERE is_featured = 1 AND status = 'upcoming'";
    $checkResult = $conn->query($checkQuery);
    
    if (!$checkResult) {
        error_log("SQL Error checking featured events: " . $conn->error);
        $count = 0;
    } else {
        $row = $checkResult->fetch_assoc();
        $count = $row['count'];
    }
    
    if ($count == 0) {
        $query = "SELECT * FROM events WHERE status = 'upcoming' ORDER BY start_datetime ASC LIMIT 7";
    } else {
        $query = "SELECT * FROM events WHERE is_featured = 1 AND status = 'upcoming' ORDER BY start_datetime ASC LIMIT 7";
    }
    
    $result = $conn->query($query);
    
    if (!$result) {
        error_log("SQL Error in getFeaturedEvents: " . $conn->error);
        return [];
    }
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getEventImageUrl($cover_image_url) {
    $imageUrl = '';
    if (!empty($cover_image_url)) {
        if (filter_var($cover_image_url, FILTER_VALIDATE_URL)) {
            $imageUrl = $cover_image_url;
        } else {
            $imagePath = 'images/' . $cover_image_url;
            if (file_exists($imagePath)) {
                $imageUrl = $imagePath;
            } else {
                $imageUrl = htmlspecialchars($cover_image_url);
            }
        }
    } else {
        $imageUrl = 'projectImages/background.jpg';
    }
    return $imageUrl;
}

$featured_events = getFeaturedEvents($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventZone - Discover Amazing Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #CA8E82;
            --secondary-color: #FFE3E3;
            --accent-color: #734840FF;
            --dark-color: #292F36;
            --light-color: #F7FFF7;
            --text-dark: #292F36;
            --text-light: #6C757D;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: var(--text-dark);
            background-color: var(--light-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }
        
        .navbar {
            padding: 15px 0;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        
        .navbar.scrolled {
            padding: 10px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand i {
            margin-right: 10px;
            font-size: 1.2em;
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
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), #FF8E8E);
            color: white;
            text-align: center;
            padding: 120px 20px;
            margin-bottom: 0;
            position: relative;
            overflow: hidden;
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('projectImages/background.jpg') center/cover no-repeat;
            opacity: 0.15;
            z-index: 0;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
        }
        
        .search-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            max-width: 900px;
            margin: 0 auto;
            width: 90%;
            transition: all 0.3s ease;
        }
        
        .search-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .input-group-text {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 8px !important;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 107, 0.25);
        }

        .event-card {
            transition: all 0.3s ease;
            margin-bottom: 30px;
            height: 100%;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
            position: relative;
        }
        
        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .event-img-container {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .event-img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .event-card:hover .event-img {
            transform: scale(1.1);
        }
        
        .category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .event-date-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: calc(100% - 200px);
        }
        
        .card-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .card-text {
            color: var(--text-light);
            flex-grow: 1;
            margin-bottom: 15px;
        }
        
        .event-meta {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .event-meta i {
            margin-right: 5px;
            color: var(--primary-color);
        }
        
        .event-price {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .btn {
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #734840FF;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
  
        .section {
            padding: 80px 0;
        }
        
        .section-header {
            margin-bottom: 50px;
            text-align: center;
        }
        
        .section-header h2 {
            font-weight: 700;
            color: var(--dark-color);
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }
        
        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        .section-header p {
            color: var(--text-light);
            max-width: 600px;
            margin: 15px auto 0;
        }

        .feature-box {
            text-align: center;
            padding: 30px 20px;
            border-radius: 15px;
            transition: all 0.3s ease;
            height: 100%;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), #FF8E8E);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 20px;
        }
        
        .feature-box h4 {
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--dark-color);
        }
        
        .feature-box p {
            color: var(--text-light);
        }
 
        .cta-section {
            background: linear-gradient(135deg, var(--primary-color), #FF8E8E);
            color: white;
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('projectImages/cta-bg.jpg') center/cover no-repeat;
            opacity: 0.1;
            z-index: 0;
        }
        
        .cta-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .cta-text {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .cta-btn {
            background: white;
            color: var(--primary-color);
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
       
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 80px 0 30px;
        }
        
        .footer h5 {
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }
        
        .footer-contact i {
            margin-right: 10px;
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }
        
        .social-icons {
            margin-top: 20px;
        }
        
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }
        
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.8rem;
            }
            
            .section {
                padding: 60px 0;
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .search-box {
                padding: 20px;
            }
            
            .section-header h2 {
                font-size: 1.8rem;
            }
            
            .cta-title {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.8rem;
            }
            
            .navbar-brand {
                font-size: 1.5rem;
            }
            
            .btn {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }
        
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .slide-up {
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(50px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                    <img src="/event-zone/Event-Management/projectimages/eventZoneLogo.png" alt="Event Zone Logo" style="width: 150px; height: auto;">
               
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#events">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-lg-3 my-2 my-lg-0">
                        <a href="Organizer/register.html" class="btn btn-outline-primary">Sign Up</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a href="Organizer/login.html" class="btn btn-primary">Log In</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-content slide-up">
            <h1 class="hero-title">Discover Your Next <span class="text-white">Experience</span></h1>
            <p class="hero-subtitle">Find and book tickets for concerts, festivals, workshops and more amazing events</p>
            
        </div>
    </section>

    <section id="events" class="section bg-light">
        <div class="container">
            <div class="section-header">
                <h2>Featured Events</h2>
                <p>Check out our handpicked selection of upcoming events</p>
            </div>
            
            <div class="row">
                <?php 
                if (!empty($featured_events)) {
                    foreach ($featured_events as $event) { 
                        $minPrice = '0.00';
                        if (isset($event['event_id'])) {
                            $ticketQuery = "SELECT MIN(ticket_price) as min_price FROM tickets WHERE event_id = ?";
                            $stmt = $conn->prepare($ticketQuery);
                            if ($stmt) {
                                $stmt->bind_param("i", $event['event_id']);
                                if ($stmt->execute()) {
                                    $ticketResult = $stmt->get_result();
                                    if ($ticketResult) {
                                        $ticketData = $ticketResult->fetch_assoc();
                                        $minPrice = $ticketData ? number_format($ticketData['min_price'], 2) : '0.00';
                                    }
                                }
                                $stmt->close();
                            }
                        }
                         
                        $dateFormatted = 'Date not set';
                        $timeFormatted = 'Time not set';
                        $dayFormatted = '';
                        $monthFormatted = '';
                         
                        if (!empty($event['start_datetime'])) {
                            try {
                                $startDate = new DateTime($event['start_datetime']);
                                $dateFormatted = $startDate->format('M d, Y');
                                $dayFormatted = $startDate->format('d');
                                $monthFormatted = $startDate->format('M');
                                $timeFormatted = $startDate->format('h:i A');
                                 
                                if (!empty($event['end_datetime'])) {
                                    $endDate = new DateTime($event['end_datetime']);
                                    $timeFormatted .= ' - ' . $endDate->format('h:i A');
                                }
                            } catch (Exception $e) {
                            }
                        }
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card event-card h-100">
                        <div class="event-img-container">
                            <?php
                            $imageUrl = '';
                            if (!empty($event['cover_image_url'])) {
                                if (filter_var($event['cover_image_url'], FILTER_VALIDATE_URL)) {
                                    $imageUrl = $event['cover_image_url'];
                                } else {
                                    $imageUrl = htmlspecialchars($event['cover_image_url']);
                                }
                            } else {
                                $imageUrl = 'projectImages/background.jpg';
                            }
                            ?>
                            <img src="<?php echo $imageUrl; ?>" 
                                 class="event-img" 
                                 alt="<?php echo htmlspecialchars($event['event_name']); ?>"
                                 onerror="this.src='projectImages/background.jpg';">
                            <div class="event-date-badge">
                                <?php echo $dayFormatted; ?> <small><?php echo $monthFormatted; ?></small>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="event-meta">
                                <span><i class="far fa-clock"></i> <?php echo $timeFormatted; ?></span>
                                <span class="ms-3"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['event_location']); ?></span>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($event['event_name']); ?></h5>
                            <p class="card-text">
                                <?php 
                                $description = $event['event_description'] ?? 'No description available';
                                echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="event-price">
                                    K<?php echo $minPrice; ?>
                                </span>
                                <a href="Goer/eventDetails.php?id=<?php echo $event['event_id']; ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-ticket-alt"></i> Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                
                <?php } else { ?>
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info glass-card">
                        <i class="fas fa-info-circle me-2"></i> No featured events available at the moment. Please check back later.
                    </div>
                </div>
                <?php } ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="Goer/events.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-calendar-alt me-2"></i> View All Events
                </a>
            </div>
        </div>
    </section>

    <section id="features" class="section" style="background-color: rgba(255, 255, 255, 0.7);">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose EventZone?</h2>
                <p>We make event discovery and booking easy and enjoyable</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <h4>Easy Booking</h4>
                        <p>Quick and secure ticket booking process with multiple payment options.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4>Wide Selection</h4>
                        <p>Discover thousands of events across various categories and locations.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4>24/7 Support</h4>
                        <p>Our dedicated support team is always ready to assist you.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">Ready for Your Next Adventure?</h2>
            <p class="cta-text">Join thousands of event-goers who trust us for the best event experiences.</p>
            <a href="Goer/events.php" class="btn cta-btn">
                <i class="fas fa-event me-2"></i> Browse Events
            </a>
        </div>
    </section>

    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h5><i class="fas fa-calendar-alt me-2"></i> EventZone</h5>
                    <p class="mt-3">Your premier destination for discovering and booking the best events in town.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-6 mb-4 mb-lg-0">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#events">Events</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Contact Us</h5>
                    <ul class="footer-contact list-unstyled">
                        <li class="mb-2"><i class="fas fa-envelope"></i> info@eventzone.com</li>
                        <li class="mb-2"><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Event St, City, Country</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> EventZone. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 70,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        function animateOnScroll() {
            const elements = document.querySelectorAll('.feature-box, .event-card, .section-header');
            
            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.2;
                
                if (elementPosition < screenPosition) {
                    element.classList.add('fade-in');
                }
            });
        }
        
        window.addEventListener('scroll', animateOnScroll);
        window.addEventListener('load', animateOnScroll);
    </script>
</body>
</html>