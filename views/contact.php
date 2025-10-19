<?php
// contact.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Ceylon Go</title>
  <link rel="stylesheet" href="../public/css/common.css">
  <link rel="stylesheet" href="../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../public/css/tourist/footer.css">
  <style>
    body {
      background-color: #f0f8f0;
    }
    
    .contact-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    
    .contact-form {
      background: #ffffff;
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.15);
      border: 1px solid rgba(74, 124, 89, 0.1);
      margin-bottom: 40px;
    }
    
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    
    .form-group label {
      font-weight: 600;
      color: var(--color-primary);
      font-size: 14px;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 12px 16px;
      border: 2px solid #d0ddd0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: #fff;
      font-family: inherit;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--color-primary-600);
      box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
    }
    
    .form-group.full-width {
      grid-column: 1 / -1;
    }
    
    .submit-btn {
      background: var(--color-primary);
      color: #fff;
      border: none;
      padding: 14px 28px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
    }
    
    .submit-btn:hover {
      background: var(--color-primary-600);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(44, 85, 48, 0.3);
    }
    
    .contact-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }
    
    .contact-item {
      background: #ffffff;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.15);
      border: 1px solid rgba(74, 124, 89, 0.1);
      text-align: center;
    }
    
    .contact-item h3 {
      color: var(--color-primary);
      margin-bottom: 15px;
      font-size: 18px;
    }
    
    .contact-item p {
      margin: 8px 0;
      color: #666;
    }
    
    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .contact-container {
        padding: 20px 10px;
      }
      
      .contact-form {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body class="bg-app">
  <!-- Navbar -->
  <?php include 'index_navbar.php'; ?>

  <!-- Contact Section -->
  <section class="intro" style="background-image: url('../public/images/train.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; color: #fff; padding: 80px 20px; text-align: center;">
    <h1>Contact Us</h1>
    <p>Get in touch with us for any questions or assistance with your Sri Lankan adventure!</p>
  </section>

  <section style="padding: 60px 20px;">
    <div class="contact-container">
      <div class="contact-form">
        <h2 style="text-align: center; margin-bottom: 30px; color: var(--color-primary);">Send Us a Message</h2>
        
        <form method="POST" action="">
          <div class="form-grid">
            <div class="form-group">
              <label for="name">Full Name</label>
              <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">
            </div>

            <div class="form-group">
              <label for="subject">Subject</label>
              <select id="subject" name="subject" required>
                <option value="">Select subject</option>
                <option value="general">General Inquiry</option>
                <option value="booking">Booking Support</option>
                <option value="technical">Technical Support</option>
                <option value="partnership">Partnership</option>
                <option value="feedback">Feedback</option>
              </select>
            </div>
          </div>

          <div class="form-group full-width">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="6" placeholder="Please describe your inquiry or message..." required></textarea>
          </div>

          <button type="submit" class="submit-btn">Send Message</button>
        </form>
      </div>

      <h2 style="text-align: center; margin-bottom: 30px; color: var(--color-primary);">Contact Information</h2>
      <div class="contact-info">
        <div class="contact-item">
          <h3>Email</h3>
          <p>info@ceylongo.com</p>
          <p>support@ceylongo.com</p>
        </div>
        <div class="contact-item">
          <h3>Phone</h3>
          <p>+94 11 234 5678</p>
          <p>+94 11 234 5679</p>
        </div>
        <div class="contact-item">
          <h3>Address</h3>
          <p>123 Travel Street</p>
          <p>Colombo 01, Sri Lanka</p>
        </div>
        <div class="contact-item">
          <h3>Business Hours</h3>
          <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
          <p>Saturday: 9:00 AM - 4:00 PM</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'tourist/footer.php'; ?>
</body>
</html>
