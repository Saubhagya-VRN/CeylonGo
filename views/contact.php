<?php
// contact.php
$base = defined('BASE_URL') ? BASE_URL : '/CeylonGo/public';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Ceylon Go</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base); ?>/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base); ?>/css/tourist/navbar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base); ?>/css/tourist/footer.css">
  <style>
    .contact-main {
      padding: 36px 20px 56px;
    }

    .contact-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .contact-info-heading {
      text-align: center;
      margin: 0 0 8px;
      color: var(--color-primary);
      font-size: clamp(1.35rem, 3vw, 1.6rem);
      font-weight: 700;
    }

    .contact-info-lead {
      text-align: center;
      margin: 0 0 28px;
      color: var(--color-muted);
      font-size: 1rem;
      line-height: 1.5;
      max-width: 36rem;
      margin-left: auto;
      margin-right: auto;
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

    .contact-item a {
      color: var(--color-primary-600, #4a7c59);
      text-decoration: none;
      font-weight: 500;
    }

    .contact-item a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .contact-container {
        padding: 0 12px;
      }
    }
  </style>
</head>
<body class="bg-app contact-page">
  <?php include 'index_navbar.php'; ?>

  <section class="contact-hero">
    <h1>Contact Us</h1>
    <p>Get in touch with us for any questions or assistance with your Sri Lankan adventure!</p>
  </section>

  <section class="contact-main">
    <div class="contact-container">
      <h2 class="contact-info-heading">Contact Information</h2>
      <p class="contact-info-lead">Reach us by email, phone, or visit during business hours—we’re happy to help.</p>
      <div class="contact-info">
        <div class="contact-item">
          <h3>Email</h3>
          <p><a href="mailto:info@ceylongo.com">info@ceylongo.com</a></p>
          <p><a href="mailto:support@ceylongo.com">support@ceylongo.com</a></p>
        </div>
        <div class="contact-item">
          <h3>Phone</h3>
          <p><a href="tel:+94112345678">+94 11 234 5678</a></p>
          <p><a href="tel:+94112345679">+94 11 234 5679</a></p>
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
          <p style="margin-top:10px;color:#888;font-size:13px;">Sunday: Closed</p>
        </div>
      </div>
    </div>
  </section>

  <?php include 'tourist/footer.php'; ?>
</body>
</html>
