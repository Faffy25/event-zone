<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Options</title>
    <style>  
        body {
            font-family: Poppins, sans-serif;
            background: #CA8E82;
            padding: 20px;
            display: flex;
            color: black;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            box-sizing: border-box;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: url('/event-zone/Event-Management/projectImages/background.jpg') center/cover no-repeat;
            opacity: 0.3;
            z-index: -1;
        }

        body.modal-open {
            overflow: hidden;
        }

        .checkout-container {
            max-width: 750px;
            width: 90%;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            z-index: 1;
        }
        h2 {
            color: black;
            font-size: 2.2em;
            margin-bottom: 20px;
        }

        .payment-option-buttons button {
            padding: 15px 25px;
            margin: 10px;
            border: none;
            border-radius: 25px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: white;
        }
        .payment-option-buttons button[name="pay_card"] {
            background-color: #CA8E82;
        }
        .payment-option-buttons button[name="pay_card"]:hover {
            background-color: #bb7f73;
        }
        .payment-option-buttons button[name="pay_mobile"] {
            background-color: #CA8E82;
        }
        .payment-option-buttons button[name="pay_mobile"]:hover {
            background-color: #bb7f73;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border: 1px solid #888;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: slideIn 0.3s ease-out;
            text-align: left;
        }
        .modal-content h3 {
            color: #333;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.8em;
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .modal-content p {
            font-size: 1.1em;
            margin-bottom: 15px;
            color: #555;
        }
        .modal-content p strong {
            color: #222;
        }
        .modal-content .payment-info {
            background-color: #e9f5e9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #d4edda;
            margin-top: 20px;
        }
        .modal-content .payment-info h4 {
            color: #333;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.3em;
        }

        .close-button {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 20px;
        }
        .close-button:hover,
        .close-button:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .card-form-group {
            margin-bottom: 15px;
        }
        .card-form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        .card-form-group input {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        .card-form-group input:focus {
            border-color: #CA8E82;
            outline: none;
            box-shadow: 0 0 5px rgba(202, 142, 130, 0.4);
        }

        .card-expiry-cvv {
            display: flex;
            gap: 15px;
        }
        .card-expiry-cvv .card-form-group {
            flex: 1;
        }

        .card-form-actions {
            text-align: center;
            margin-top: 25px;
        }
        .card-form-actions button {
            background-color: #CA8E82;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .card-form-actions button:hover {
            background-color: #bb7f73;
        }
        .confirm-ticket-btn {
            display: inline-block;
            background-color: #CA8E82;
            color: white;
            padding: 15px 30px;
            margin-top: 30px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }
        .confirm-ticket-btn:hover {
            background-color: #CA8E82;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h2>Select Payment Method</h2>

        <div class="payment-option-buttons">
            <button type="button" id="payCardButton" name="pay_card">Pay With Card</button>
            <button type="button" id="payMobileButton" name="pay_mobile">Pay With Mobile Money</button>
        </div>
    </div>

    <div id="mobileMoneyModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="mobileMoneyModalTitle">
        <div class="modal-content">
            <span class="close-button" aria-label="Close mobile money payment" data-close-modal="mobileMoneyModal">&times;</span>
            <h3 id="mobileMoneyModalTitle">Pay With Mobile Money</h3>
            <p>Please follow the instructions below to complete your payment:</p>
            <div class="payment-info">
                <h4>MTN Mobile Money</h4>
                <p><strong>Dial:</strong> *115#</p>
                <p><strong>Select:</strong> Pay Bill</p>
                <p><strong>Enter Business Name/Code:</strong> EVENTZONE</p>
                <p><strong>Enter Amount:</strong> <span id="mtnAmount"></span> (Amount will be provided)</p>
                <p><strong>Enter Reference:</strong> <span id="mobileRef"></span> (Your unique reference)</p>
                <p><strong>Confirm:</strong> Enter your PIN</p>
            </div>
            <div class="payment-info" style="margin-top: 15px;">
                <h4>Airtel Money</h4>
                <p><strong>Dial:</strong> *115#</p>
                <p><strong>Select:</strong> Make Payment</p>
                <p><strong>Select:</strong> Goods and Services</p>
                <p><strong>Enter Till Number:</strong> 123456</p>
                <p><strong>Enter Amount:</strong> <span id="airtelAmount"></span> (Amount will be provided)</p>
                <p><strong>Enter Reference:</strong> <span id="airtelRef"></span> (Your unique reference)</p>
                <p><strong>Confirm:</strong> Enter your PIN</p>
            </div>
            <p style="margin-top: 20px; font-size: 0.9em; color: #CA8E82">
                After payment, you will receive an SMS confirmation. Please allow a few minutes for your ticket to be processed.
            </p>
            <a href="/event-zone/Event-Management/Goer/confirm.php" class="confirm-ticket-btn">Download Ticket</a>
        </div>
    </div>

    <div id="cardModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="cardModalTitle">
        <div class="modal-content">
            <span class="close-button" aria-label="Close card payment" data-close-modal="cardModal">&times;</span>
            <h3 id="cardModalTitle">Pay With Card</h3>
            <p>Please enter your card details below to complete your payment:</p>

            <form id="cardPaymentForm">
                <div class="card-form-group">
                    <label for="cardNumber">Card Number</label>
                    <input type="text" id="cardNumber" name="cardNumber" placeholder="XXXX XXXX XXXX XXXX" required maxlength="19" pattern="[0-9\s]{13,19}" title="Credit card number (13-19 digits)">
                </div>
                <div class="card-form-group">
                    <label for="cardName">Name on Card</label>
                    <input type="text" id="cardName" name="cardName" placeholder="Full Name" required>
                </div>
                <div class="card-expiry-cvv">
                    <div class="card-form-group">
                        <label for="expiryDate">Expiry Date (MM/YY)</label>
                        <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY" required maxlength="5" pattern="(0[1-9]|1[0-2])\/?([0-9]{2})" title="Expiry date in MM/YY format">
                    </div>
                    <div class="card-form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" placeholder="123" required maxlength="4" pattern="[0-9]{3,4}" title="Security Code (3 or 4 digits)">
                    </div>
                </div>
                <div class="card-form-group">
                    <label for="billingAddress">Billing Address</label>
                    <input type="text" id="billingAddress" name="billingAddress" placeholder="Street Address" required>
                </div>
                <div class="card-form-group">
                    <label for="billingZip">Zip/Postal Code</label>
                    <input type="text" id="billingZip" name="billingZip" placeholder="XXXXX" required maxlength="10">
                </div>
                <div class="card-form-actions">
                    <button type="submit">Complete Payment</button>
                </div>
            </form>

        </div>
    </div>

    <script>
        const mobileMoneyModal = document.getElementById('mobileMoneyModal');
        const cardModal = document.getElementById('cardModal');
        const payCardButton = document.getElementById('payCardButton');
        const payMobileButton = document.getElementById('payMobileButton');
        const cardPaymentForm = document.getElementById('cardPaymentForm');

        function openModal(modal) {
            modal.classList.add('active');
            document.body.classList.add('modal-open');
            modal.querySelector('.close-button').focus();
        }

        function closeModal(modal) {
            modal.classList.remove('active');
            document.body.classList.remove('modal-open');
            if (modal === cardModal) {
                cardPaymentForm.reset();
            }
            if (modal === mobileMoneyModal) {
                payMobileButton.focus();
            } else if (modal === cardModal) {
                payCardButton.focus();
            }
        }

        function showMobileMoneyDetails() {
            const orderId = "EZ" + Date.now().toString().slice(-6);

            document.getElementById('mtnAmount').innerText = "[Amount]";
            document.getElementById('airtelAmount').innerText = "[Amount]";
            document.getElementById('mobileRef').innerText = orderId;
            document.getElementById('airtelRef').innerText = orderId;

            openModal(mobileMoneyModal);
        }

        function showCardPaymentDetails() {
            const cardNumberInput = document.getElementById('cardNumber');
            cardNumberInput.addEventListener('input', function () {
                this.value = formatCardNumber(this.value);
            });

            const expiryDateInput = document.getElementById('expiryDate');
            expiryDateInput.addEventListener('input', function () {
                if (this.value.length === 2 && !this.value.includes('/')) {
                    this.value += '/';
                }
            });

            openModal(cardModal);
        }

        function formatCardNumber(value) {
            const cleaned = value.replace(/\s/g, '');
            const match = cleaned.match(/.{1,4}/g);
            return match ? match.join(' ') : cleaned;
        }

        function handleCardPayment(event) {
            event.preventDefault();

            alert(`Card payment initiated successfully!\nRedirecting to confirmation page.`);

            window.location.href = "/event-zone/Event-Management/Goer/confirm.php";
        }

        payCardButton.addEventListener('click', showCardPaymentDetails);
        payMobileButton.addEventListener('click', showMobileMoneyDetails);

        document.querySelectorAll('.close-button').forEach(button => {
            button.addEventListener('click', function() {
                const modalId = this.dataset.closeModal;
                closeModal(document.getElementById(modalId));
            });
        });

        window.addEventListener('click', function(event) {
            if (event.target === mobileMoneyModal) {
                closeModal(mobileMoneyModal);
            }
            if (event.target === cardModal) {
                closeModal(cardModal);
            }
        });

        cardPaymentForm.addEventListener('submit', handleCardPayment);
    </script>
</body>
</html>