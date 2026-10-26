.<?php
session_start();
include('../Organizer/connection.php');

if (!isset($_SESSION['organizer_id'])) {
    header('Location: login.html');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Event</title>
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

header {
    width: 100%;
}

.nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 15px 20px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.nav .logo img {
    width: 150px;
    height: auto;
    transition: transform 0.3s ease;
}

.nav .logo img:hover {
    transform: scale(1.05);
}

main.create-event {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 20px 0;
}

.container {
    max-width: 700px;
    width: 90%;
    margin: 0 auto;
    padding: 30px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #333;
    text-align: center;
    margin-bottom: 30px;
    font-size: 2rem;
    font-weight: 700;
}

h2 {
    color: #555;
    margin: 25px 0 15px;
    font-size: 1.3rem;
    font-weight: 600;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #555;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
    color: #333;
    background-color: #fff;
    box-sizing: border-box;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #CA8E82;
    box-shadow: 0 0 0 3px rgba(202, 142, 130, 0.2);
    outline: none;
}

.form-group textarea {
    min-height: 120px;
    resize: vertical;
}

.file-upload {
    position: relative;
    overflow: hidden;
    display: inline-block;
    width: 100%;
}

.file-upload input[type="file"] {
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-upload label {
    display: block;
    padding: 12px;
    background: #f0f0f0;
    border: 2px dashed #ccc;
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-upload label:hover {
    background: #e0e0e0;
    border-color: #CA8E82;
}

.file-upload:hover label {
    border-color: #CA8E82;
}

#ticket-section {
    margin-top: 20px;
    padding: 20px;
    border: 1px solid #eee;
    border-radius: 8px;
    background-color: #f9f9f9;
}

.ticket-group {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background-color: #fff;
    transition: all 0.3s ease;
}

.ticket-group:hover {
    border-color: #CA8E82;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.ticket-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 0.95rem;
    color: #555;
    font-weight: 500;
}

.ticket-group input {
    width: 100%;
    box-sizing: border-box;
    padding: 10px 12px;
    margin-bottom: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.ticket-group input:focus {
    border-color: #CA8E82;
    box-shadow: 0 0 0 3px rgba(202, 142, 130, 0.2);
}

#addTicket,
.remove-ticket-btn,
.form-group button[type="submit"] {
    width: auto; 
    max-width: 300px; 
    display: block;
    margin: 10px auto;
    border: none;
    border-radius: 8px;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

#addTicket {
    background: #CA8E82;
    color: white;
    font-size: 1rem;
    padding: 14px 25px; 
}

#addTicket:hover {
    background: #b37d72;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.remove-ticket-btn {
    background: #dc3545;
    color: white;
    margin-top: 10px;
    font-size: 0.9rem;
    padding: 8px 15px; 
}

.remove-ticket-btn:hover {
    background: #c82333;
    transform: translateY(-1px);
}


.form-group button[type="submit"] {
    background: #CA8E82;
    color: white;
    font-size: 1.1rem;
    margin-top: 25px;
    padding: 15px 30px; 
    font-weight: 700;
}

.form-group button[type="submit"]:hover {
    background: #b37d72;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    h1 {
        font-size: 1.8rem;
        margin-bottom: 25px;
    }

    h2 {
        font-size: 1.2rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 10px 12px;
    }

    .ticket-group {
        padding: 12px;
    }

    #addTicket,
    .form-group button[type="submit"] {
        max-width: 250px; 
    }
}

@media (max-width: 480px) {
    .container {
        padding: 15px;
        width: 95%;
    }

    h1 {
        font-size: 1.6rem;
    }

    #addTicket,
    .form-group button[type="submit"] {
        padding: 12px 20px;
        max-width: 90%;
    }
}
</style>
</head>
<body>

<main class="create-event">
    <div class="container">
        <h1>Create New Event</h1>
        <form action="createEvent.php" method="POST" enctype="multipart/form-data">
            <div class="form-group file-upload">
                <input type="file" name="coverImage" id="coverImage" accept="image/*" required>
                <label for="coverImage">
                   <span id="file-name">Upload Cover Image</span>
                </label>
            </div>

            <div class="form-group">
                <label for="event_name">Event Name</label>
                <input type="text" name="event_name" id="event_name" required placeholder>
            </div>
            <div class="form-group">
                <label for="event_description">Event Description</label>
                <textarea name="event_description" id="event_description" required placeholder="Provide a detailed description of your event."></textarea>
            </div>

            <div class="form-group">
                <label for="start_datetime">Event Start Date & Time</label>
                <input type="datetime-local" name="start_datetime" id="start_datetime" required>
            </div>
            <div class="form-group">
                <label for="end_datetime">Event End Date & Time</label>
                <input type="datetime-local" name="end_datetime" id="end_datetime" required>
            </div>

            <div class="form-group">
                <label for="event_location">Event Location</label>
                <input type="text" name="event_location" id="event_location" required placeholder>
            </div>
            <div class="form-group">
                <label for="category_id">Event Category</label>
                <select name="category_id" id="category_id" required>
                    <option value="" disabled selected>Select a category</option>
                    <option value="1">Concerts & Music</option>
                    <option value="2">Conferences & Business</option>
                    <option value="3">Workshops & Education</option>
                    <option value="4">Weddings & Celebrations</option>
                    <option value="5">Sports & Fitness</option>
                    <option value="6">Food & Drink</option>
                    <option value="7">Art & Culture</option>
                    <option value="8">Other</option>
                </select>
            </div>

            <h2>Ticket Details</h2>
            <div id="ticket-section">
                <div class="ticket-group" id="ticket-group-0">
                    <label for="ticket_type_0">Ticket Type</label>
                    <input type="text" name="ticket_type[]" id="ticket_type_0" placeholder="e.g...General Admission" required>

                    <label for="ticket_price_0">Ticket Price</label>
                    <input type="number" name="ticket_price[]" id="ticket_price_0" step="0.01" min="0" required>

                    <label for="ticket_total_quantity_0">Total Ticket Quantity</label>
                    <input type="number" name="ticket_total_quantity[]" id="ticket_total_quantity_0" min="1" required>
                </div>
            </div>
            <button type="button" id="addTicket">Add Another Ticket Type</button>

            <h2>Contact Information for this Event</h2>
            <div class="form-group">
                <label for="contact_person_name">Contact Person Name</label>
                <input type="text" name="contact_person_name" id="contact_person_name" required>
            </div>
            <div class="form-group">
                <label for="contact_email">Contact Email</label>
                <input type="email" name="contact_email" id="contact_email" required>
            </div>
            <div class="form-group">
                <label for="contact_phone">Contact Phone</label>
                <input type="tel" name="contact_phone" id="contact_phone" required>
            </div>

            <div class="form-group">
                <button type="submit">Create Event</button>
            </div>
        </form>
    </div>
</main>

<script>
    document.getElementById('coverImage').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Upload Cover Image';
        document.getElementById('file-name').textContent = fileName;
    });

    let ticketCount = 0; 

    document.getElementById('addTicket').addEventListener('click', function() {
        ticketCount++;
        const ticketSection = document.getElementById('ticket-section');
        const ticketGroup = document.createElement('div');
        ticketGroup.classList.add('ticket-group');
        ticketGroup.id = `ticket-group-${ticketCount}`;
        ticketGroup.innerHTML = `
            <label for="ticket_type_${ticketCount}">Ticket Type</label>
            <input type="text" name="ticket_type[]" id="ticket_type_${ticketCount}" required>

            <label for="ticket_price_${ticketCount}">Ticket Price</label>
            <input type="number" name="ticket_price[]" id="ticket_price_${ticketCount}" step="0.01" min="0" required>

            <label for="ticket_total_quantity_${ticketCount}">Total Ticket Quantity</label>
            <input type="number" name="ticket_total_quantity[]" id="ticket_total_quantity_${ticketCount}" min="1" required>

            <button type="button" class="remove-ticket-btn" data-ticket-id="${ticketCount}">Remove Ticket Type</button>
        `;
        ticketSection.appendChild(ticketGroup);

        ticketGroup.querySelector('.remove-ticket-btn').addEventListener('click', function() {
            ticketGroup.remove();
        });
    });

    document.getElementById('start_datetime').addEventListener('change', validateDates);
    document.getElementById('end_datetime').addEventListener('change', validateDates);

    function validateDates() {
        const startDateInput = document.getElementById('start_datetime');
        const endDateInput = document.getElementById('end_datetime');

        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (endDate < startDate) {
            alert('End date and time cannot be earlier than start date and time.');
            endDateInput.value = '';
        }
    }
</script>
</body>
</html>