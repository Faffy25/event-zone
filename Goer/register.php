<?php
session_start();
require_once('../Organizer/connection.php');

$event_id_from_url = isset($_GET['event_id']) ? $_GET['event_id'] : 0;

$tickets = array();
$error_message = '';
$success_message = '';

function fetchTickets($conn, $event_id) {
    $tickets = array();
    if ($event_id) {
        $sql = "SELECT ticket_id, ticket_type, ticket_price FROM tickets WHERE event_id = $event_id";
        $result = $conn->query($sql);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $tickets[] = $row;
            }
        }
    }
    return $tickets;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $country = $_POST['country'];
    $ticket_id = $_POST['ticket_id'];
    $event_id = $_POST['event_id'];

    if ($event_id == 0) {
        $_SESSION['registration_error_message'] = "Error: Invalid event ID.";
        header("Location: events.php");
        exit();
    }

    $sql = "SELECT ticket_total_quantity, ticket_price FROM tickets WHERE ticket_id = $ticket_id AND event_id = $event_id";
    $result = $conn->query($sql);
    $ticket_data = $result->fetch_assoc();

    if (!$ticket_data || $ticket_data['ticket_total_quantity'] <= 0) {
        $_SESSION['registration_error_message'] = "Sorry, this ticket type is sold out or not found.";
        header("Location: register.php?event_id=" . $event_id);
        exit();
    }

    $event_price = $ticket_data['ticket_price'];

    $sql = "SELECT id FROM event_goers WHERE email = '$email' AND event_id = $event_id AND ticket_id = $ticket_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $_SESSION['registration_error_message'] = "You have already registered for this event and ticket type with this email.";
        header("Location: register.php?event_id=" . $event_id);
        exit();
    }

    $conn->begin_transaction();
    
    try {
        $sql = "INSERT INTO event_goers (full_name, email, phone_number, country, event_id, ticket_id) 
                VALUES ('$full_name', '$email', '$phone_number', '$country', $event_id, $ticket_id)";
        $conn->query($sql);
        $goer_id = $conn->insert_id;

        $qr_code = uniqid('qr_', true);
        $sql = "INSERT INTO registrations (event_goer_id, ticket_id, qr_code) 
                VALUES ($goer_id, $ticket_id, '$qr_code')";
        $conn->query($sql);

        $sql = "UPDATE tickets SET ticket_total_quantity = ticket_total_quantity - 1 WHERE ticket_id = $ticket_id";
        $conn->query($sql);

        $conn->commit();

        $_SESSION['registration_success_message'] = "Registration successful! Please select your payment method.";
        $_SESSION['goer_id'] = $goer_id;
        $_SESSION['payment_amount'] = $event_price;
        $_SESSION['show_payment_options'] = true;
        
        header("Location: register.php?event_id=" . $event_id);
        exit();
        
    } catch (Exception $e) {

        $conn->rollback();
        $_SESSION['registration_error_message'] = "Error registering for the event: " . $e->getMessage();
        header("Location: register.php?event_id=" . $event_id);
        exit();
    }
}

$tickets = fetchTickets($conn, $event_id_from_url);

if ($conn) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration - Event Zone</title>
    <style>
        :root {
            --primary-color: #CA8E82;
            --primary-hover: #79524AFF;
            --secondary-color: #79524AFF;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-gray: #f8f9fa;
            --dark-gray: #79524AFF;
            --border-color: #dee2e6;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            line-height: 1.6;
            color: #333;
            background-color: #f5f7ff;
            position: relative;
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
            margin: 0 auto;
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .registration-box {
            background: #ffffff;
            width: 100%;
            max-width: 500px;
            margin: 2rem auto;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .registration-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            position: relative;
            display: inline-block;
        }

        .form-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }
        select {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-gray);
            font-size: 0.95rem;
        
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-gray);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn {
            display: inline-block;
            font-weight: 500;
            color: #fff;
            text-align: center;
            background-color: var(--primary-color);
            border: 1px solid transparent;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: var(--border-radius);
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            width: 100%;
            text-transform: uppercase;
            font-weight: 600;
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .alert {
            position: relative;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            font-size: 0.9375rem;
            line-height: 1.5;
            display: flex;
            align-items: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        .alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border-color: #badbcc;
        }

        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }

        .alert-dismissible {
            padding-right: 3.5rem;
        }

        .close {
            position: absolute;
            top: 0;
            right: 0;
            padding: 1.25rem 1rem;
            color: inherit;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.15s ease-in-out;
        }

        .close:hover {
            opacity: 1;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1050;
            padding: 2rem 1rem;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(3px);
        }

        .modal.active {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            opacity: 1;
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: slideIn 0.3s ease-out;
        }

        .close-button {
            position: absolute;
            top: 1rem;
            right: 1.25rem;
            font-size: 1.75rem;
            color: #6c757d;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.25rem;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
            opacity: 0.7;
        }

        .close-button:hover {
            opacity: 1;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .payment-amount {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: center;
            border: 1px dashed #e0e6ff;
        }

        .payment-option-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .payment-method-btn {
            background: white;
            border: 2px dashed #e0e0e0;
            border-radius: 10px;
            padding: 1.5rem 1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            color: #495057;
            min-height: 160px;
            justify-content: center;
        }

        .payment-method-btn:hover {
            border-color: var(--primary-color);
            border-style: solid;
            box-shadow: 0 8px 20px rgba(74, 107, 255, 0.15);
            transform: translateY(-3px);
        }

        .card-form-group {
            margin-bottom: 1.25rem;
        }

        .card-expiry-cvv {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .card-logos {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .card-logo {
            height: 24px;
            width: auto;
            opacity: 0.5;
            transition: all 0.3s ease;
            filter: grayscale(100%);
        }

        .card-logo:hover {
            opacity: 1;
            filter: grayscale(0%);
        }

        .confirm-ticket-btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 15px 30px;
            margin-top: 30px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .confirm-ticket-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .payment-provider-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .payment-provider-logo {
            height: 40px;
            margin-right: 15px;
        }

        @media (max-width: 576px) {
            .container {
                padding: 1rem;
            }
            
            .registration-box {
                margin: 1rem auto;
                padding: 1.5rem;
            }
            
            .payment-option-buttons {
                grid-template-columns: 1fr;
            }
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="registration-box">
            <div class="form-header">
                <h2>Event Registration</h2>
                <p>Please fill in your details to complete the registration</p>
            </div>
            
            <?php if (isset($_SESSION['registration_success_message'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <?php echo $_SESSION['registration_success_message']; 
                    unset($_SESSION['registration_success_message']); ?>
                    <button type="button" class="close">&times;</button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['registration_error_message'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <?php echo $_SESSION['registration_error_message']; 
                    unset($_SESSION['registration_error_message']); ?>
                    <button type="button" class="close">&times;</button>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>?event_id=<?php echo $event_id_from_url; ?>" method="post" id="registrationForm">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required 
                           minlength="3" placeholder="Enter your full name" 
                           value="<?php echo isset($_POST['full_name']) ? $_POST['full_name'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required 
                           placeholder="Enter your email address"
                           value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                    <small class="form-text text-muted">The ticket will be sent to this Email</small>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" required 
                           placeholder="Enter your phone number"
                           value="<?php echo isset($_POST['phone_number']) ? $_POST['phone_number'] : ''; ?>">
                    
                </div>

                <div class="form-group">
                    <label for="country">Country</label>
                    <select class="form-control" id="country" name="country" required>

                    </select>
                </div>

                <?php if (!empty($tickets)): ?>
                    <label for="ticket_id">Select Ticket Type</label>
                    <select name="ticket_id" class="form-control" id="ticket_id" required>
                        
                        <?php foreach ($tickets as $ticket): ?>
                            <option value="<?php echo $ticket['ticket_id']; ?>">
                                <?php echo htmlspecialchars($ticket['ticket_type']) . " (ZMW " . number_format($ticket['ticket_price'], 2) . ")"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <div class="alert alert-danger">
                        No tickets available for this event at the moment.
                    </div>
                <?php endif; ?>

                <input type="hidden" name="event_id" value="<?php echo $event_id_from_url; ?>">

                <div class="form-group">
                    <button type="submit" class="btn" <?php echo empty($tickets) ? 'disabled' : ''; ?>>
                        Register Now
                    </button>
                </div>

            </form>
        </div>
    </div>

     <div id="paymentOptionsModal" class="modal">
        <div class="modal-content">
            <button type="button" class="close-button" data-close-modal="paymentOptionsModal">&times;</button>
            <h3>Select Payment Method</h3>
            <p style="text-align: center; margin-bottom: 1.5rem;">Choose your preferred payment method</p>
            
            <div class="payment-option-buttons">
                <button type="button" id="payCardButton" class="payment-method-btn">
                    <span>Credit/Debit Card</span>
                    <small>Visa, Mastercard, etc.</small>
                </button>
                <button type="button" id="payMobileButton" class="payment-method-btn">
                    <span>Mobile Money</span>
                    <small>MTN, Airtel, etc.</small>
                </button>
            </div>
        </div>
    </div>

    <div id="mobileMoneyModal" class="modal">
        <div class="modal-content">
            <button type="button" class="close-button" data-close-modal="mobileMoneyModal">&times;</button>
            <h3>Mobile Money Payment</h3>
            
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                Please follow the instructions below to complete your payment.
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <h4>MTN Mobile Money</h4>
                <p><strong>Dial:</strong> *115#</p>
                <p><strong>Select:</strong> Pay Bill</p>
                <p><strong>Business Name/Code:</strong> EVENTZONE</p>
                <p><strong>Amount:</strong> 
                    <span style="font-weight: bold;">
                        ZMW <?php echo isset($_SESSION['payment_amount']) ? number_format($_SESSION['payment_amount'], 2) : '0.00'; ?>
                    </span>
                </p>
                
            </div>
            
            <div>
                <h4>Airtel Money</h4>
                <p><strong>Dial:</strong> *115#</p>
                <p><strong>Enter Amount:</strong> 
                    <span id="airtelAmount">
                        <?php echo isset($_SESSION['payment_amount']) ? 'ZMW ' . number_format($_SESSION['payment_amount'], 2) : '[Amount]'; ?>
                    </span>
                </p>
               
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="/event-zone/Event-Management/Goer/confirm.php?goer_id=<?php echo isset($_SESSION['goer_id']) ? htmlspecialchars($_SESSION['goer_id']) : ''; ?>" 
                   class="confirm-ticket-btn">
                    Download Ticket
                </a>
            </div>
        </div>
    </div>

    <div id="cardModal" class="modal">
        <div class="modal-content">
            <button type="button" class="close-button" data-close-modal="cardModal">&times;</button>
            <h3>Card Payment</h3>
            
            <div class="payment-amount">
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <span style="color: #6c757d;">Amount to pay:</span>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);" id="cardPaymentAmount">
                        ZMW <?php echo isset($_SESSION['payment_amount']) ? number_format($_SESSION['payment_amount'], 2) : '0.00'; ?>
                    </div>
                </div>
            </div>
            
            <form id="cardPaymentForm">
                <div class="card-form-group">
                    <label for="cardNumber">Card Number</label>
                    <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456" 
                           pattern="[0-9\s]{13,19}" maxlength="19" required>
                </div>
                
                <div class="card-expiry-cvv">
                    <div class="card-form-group">
                        <label for="expiryDate">Expiry Date</label>
                        <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY" 
                               pattern="(0[1-9]|1[0-2])\/([0-9]{2})" maxlength="5" required>
                    </div>
                    <div class="card-form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" class="form-control" id="cvv" placeholder="123" 
                               pattern="[0-9]{3,4}" maxlength="4" required>
                    </div>
                </div>
                
                <div class="card-form-group">
                    <label for="billingAddress">Billing Address</label>
                    <input type="text" class="form-control" id="billingAddress" placeholder="Street Address" required>
                </div>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <button type="submit" class="btn">Complete Payment</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentOptionsModal = document.getElementById('paymentOptionsModal');
            const mobileMoneyModal = document.getElementById('mobileMoneyModal');
            const cardModal = document.getElementById('cardModal');
            const payCardButton = document.getElementById('payCardButton');
            const payMobileButton = document.getElementById('payMobileButton');
            const closeButtons = document.querySelectorAll('.close-button');
            const cardPaymentForm = document.getElementById('cardPaymentForm');
            const countrySelect = document.getElementById('country');
            
            function openModal(modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            function closeModal(modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
           
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modalId = this.dataset.closeModal;
                    closeModal(document.getElementById(modalId));
                });
            });
            
            window.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal')) {
                    closeModal(event.target);
                }
            });
            
            if (payCardButton) {
                payCardButton.addEventListener('click', function() {
                    closeModal(paymentOptionsModal);
                    openModal(cardModal);
                });
            }
            
            if (payMobileButton) {
                payMobileButton.addEventListener('click', function() {
                    closeModal(paymentOptionsModal);
                    openModal(mobileMoneyModal);

                    const ref = 'EVT' + Math.floor(1000 + Math.random() * 9000);
                    document.getElementById('mobileRef').textContent = ref;
                    document.getElementById('airtelRef').textContent = ref;
                });
            }

            if (cardPaymentForm) {

                const cardNumberInput = document.getElementById('cardNumber');
                if (cardNumberInput) {
                    cardNumberInput.addEventListener('input', function() {
                        this.value = this.value.replace(/\s+/g, '').replace(/(\d{4})/g, '$1 ').trim();
                    });
                }
                
                const expiryDateInput = document.getElementById('expiryDate');
                if (expiryDateInput) {
                    expiryDateInput.addEventListener('input', function() {
                        let value = this.value.replace(/\D/g, '');
                        if (value.length >= 2) {
                            value = value.substring(0, 2) + '/' + value.substring(2, 4);
                        }
                        this.value = value;
                    });
                }
          
                cardPaymentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
              
                    alert('Payment processed successfully!');
                    const goerId = <?php echo isset($_SESSION['goer_id']) ? json_encode($_SESSION['goer_id']) : 'null'; ?>;
                    if (goerId) {
                        window.location.href = `/event-zone/Event-Management/Goer/confirm.php?goer_id=${goerId}`;
                    }
                });
            }

            if (countrySelect) {
                fetch('https://restcountries.com/v3.1/all?fields=name')
                    .then(response => response.json())
                    .then(countries => {
                        countries.sort((a, b) => a.name.common.localeCompare(b.name.common));
                        countries.forEach(country => {
                            const option = document.createElement('option');
                            option.value = country.name.common;
                            option.textContent = country.name.common;
                            countrySelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading countries:', error);
                    });
            }
            
            <?php if (isset($_SESSION['show_payment_options']) && $_SESSION['show_payment_options']): ?>
                openModal(paymentOptionsModal);
                <?php unset($_SESSION['show_payment_options']); ?>
            <?php endif; ?>
        });
    </script> 
</body>
</html>