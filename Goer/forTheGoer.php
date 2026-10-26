<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Zone - How It Works</title>
    <style>
        
        body {
            font-family: Poppins, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #F2D6CE;
            color: black;
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
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #CA8E82;
            margin-bottom: 40px;
        }
        p{
            color:black;
            font-size: xx-large;
            text-align: center;
            
        }
        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .step:nth-child(even) {
            flex-direction: row-reverse; 
            background-color: #f0f3f6;
        }
        .step-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #F2D6CE;
            margin-right: 25px;
            min-width: 50px; 
            text-align: center;
        }
        .step:nth-child(even) .step-number {
            margin-right: 0;
            margin-left: 25px;
        }
        .step-content {
            flex-grow: 1;
        }
        .step-content h2 {
            color: #CA8E82;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.8em;
        }
        .step-content p {
            color: black;
            font-size:x-large;
        }
        .cta-button {
            display: block;
            width: fit-content;
            margin: 40px auto 20px auto;
            padding: 15px 30px;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .cta-button:hover {
            color: #CA8E82;
            font-weight: bold;
            transition: font-weight 0.3s, color 0.3s;
        }

        
        @media (max-width: 768px) {
            .step {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .step:nth-child(even) {
                flex-direction: column;
            }
            .step-number {
                margin-right: 0;
                margin-bottom: 15px;
            }
            .step:nth-child(even) .step-number {
                margin-left: 0;
                margin-bottom: 15px;
            }
            .container {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>How Event Zone Works</h1>
        <p>Event Zone simplifies listing, buying and selling of event tickets in Zambia.</p>
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <h2>Browse The Events</h2>
            <p> Getting started with EventZone is simple! Follow these easy steps to find, register for, and enjoy your next event.
              </p>
        </div>
    </div>

        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <h2>Register or Buy Tickets</h2>
                <p>Found an event you love? Click on it to view details. If it's a free event, simply register. For paid events, select your ticket type and quantity, then proceed to our secure payment gateway</p>
            </div>
        </div>
    

        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <h2>Receive Confirmation</h2>
                <p>After successful registration or payment, you'll receive an instant confirmation email. This email will contain your registration details, ticket (if applicable), and important event information.</p>
            </div>
        </div>



        <div class="step">
            <div class="step-number">4</div>
            <div class="step-content">
                <h2>Attend The Event</h2>
                <p>On the day of your event, use our check-in tools for smooth attendee entry. Afterward, access comprehensive reports and analytics to understand your event's success, attendee demographics, and areas for improvement.</p>
            </div>
    </div>
    

        <a href="events.php" class="cta-button">Get Started with Event Zone Today!</a>
    
</body>
</html>
</html>