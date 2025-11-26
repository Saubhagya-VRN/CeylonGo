<?php
// payment.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
   <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <style>
    body {
      background-color: #f0f8f0; /* Light greenish background from tourist_dashboard */
    }
    
    .payment-container {
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .payment-summary {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.15);
      margin-bottom: 30px;
    }
    
    .payment-summary h3 {
      color: #2c5530;
      margin-bottom: 20px;
      font-size: 20px;
    }
    
    .summary-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      padding: 8px 0;
      border-bottom: 1px solid #e0e8e0;
    }
    
    .summary-item:last-child {
      border-bottom: none;
      font-weight: bold;
      font-size: 18px;
      color: #2c5530;
    }
    
    .payment-methods {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.15);
    }
    
    .payment-method {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      padding: 15px;
      border: 2px solid #e0e8e0;
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .payment-method:hover {
      border-color: #4a7c59;
      background-color: #f8fcf8;
    }
    
    .payment-method input[type="radio"] {
      margin-right: 15px;
    }
    
    .payment-method label {
      font-weight: 600;
      color: #2c5530;
      cursor: pointer;
    }
    
    .card-details {
      margin-top: 20px;
      padding: 20px;
      background: #f8fcf8;
      border-radius: 10px;
      border: 1px solid #e0e8e0;
    }
    
    .card-row {
      display: flex;
      gap: 15px;
      margin-bottom: 15px;
    }
    
    .card-row .form-group {
      flex: 1;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      color: #2c5530;
    }
    
    .form-group input {
      width: 100%;
      padding: 12px;
      border: 2px solid #e0e8e0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    
    .form-group input:focus {
      outline: none;
      border-color: #4a7c59;
      box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- Payment Section -->
  <section class="pay" style="background-image: url('../../public/images/pay.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; color: #fff; padding: 80px 20px; text-align: center;">
    <h1>Complete Your Payment</h1>
    <p>Secure payment gateway for your Sri Lankan adventure</p>
  </section>

  <section class="customize-trip">
    <div class="payment-container">
      <!-- Payment Summary -->
      <div class="payment-summary">
        <h3>Booking Summary</h3>
        <div class="summary-item">
          <span>Package:</span>
          <span>Cultural Experience in Sri Lanka</span>
        </div>
        <div class="summary-item">
          <span>Duration:</span>
          <span>5 days, 4 nights</span>
        </div>
        <div class="summary-item">
          <span>Number of Persons:</span>
          <span>2</span>
        </div>
        <div class="summary-item">
          <span>Travel Dates:</span>
          <span>Dec 15 - Dec 19, 2024</span>
        </div>
        <div class="summary-item">
          <span>Tour Guide:</span>
          <span>Yes</span>
        </div>
        <div class="summary-item">
          <span>Transport:</span>
          <span>Yes</span>
        </div>
        <div class="summary-item">
          <span>Total Amount:</span>
          <span>Rs. 50,000</span>
        </div>
      </div>

      <!-- Payment Methods -->
      <div class="payment-methods">
        <h3>Select Payment Method</h3>
        
        <div class="payment-method">
          <input type="radio" id="credit-card" name="payment-method" value="credit-card" checked>
          <label for="credit-card">Credit/Debit Card</label>
        </div>
        
        <div class="payment-method">
          <input type="radio" id="paypal" name="payment-method" value="paypal">
          <label for="paypal">PayPal</label>
        </div>
        
        <div class="payment-method">
          <input type="radio" id="bank-transfer" name="payment-method" value="bank-transfer">
          <label for="bank-transfer">Bank Transfer</label>
        </div>

        <!-- Credit Card Details -->
        <div class="card-details" id="card-details">
          <h4 style="color: #2c5530; margin-bottom: 15px;">Card Details</h4>
          
          <div class="form-group">
            <label for="card-number">Card Number</label>
            <input type="text" id="card-number" name="card-number" placeholder="1234 5678 9012 3456" maxlength="19">
          </div>
          
          <div class="card-row">
            <div class="form-group">
              <label for="expiry">Expiry Date</label>
              <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5">
            </div>
            <div class="form-group">
              <label for="cvv">CVV</label>
              <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
            </div>
          </div>
          
          <div class="form-group">
            <label for="card-name">Name on Card</label>
            <input type="text" id="card-name" name="card-name" placeholder="John Doe">
          </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 20px;">
          <button type="submit" class="btn" style="flex: 1;">Pay Rs. 50,000</button>
          <a href="javascript:history.back()" class="btn" style="flex: 1; background: #000; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center;">Back</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>
</body>
</html>
