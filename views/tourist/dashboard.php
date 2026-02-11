<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist';
$user_name = $is_logged_in ? ($_SESSION['user_name'] ?? 'Tourist') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ceylon Go</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/common.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/footer.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/dashboard.css">
</head>
<body class="dashboard-page">
    <?php include __DIR__ . '/header.php'; ?>

    <nav class="dashboard-package-bar">
        <a href="/CeylonGo/public/tourist/recommended-packages?category=region" class="package-bar-item">Region <i class="fa-solid fa-chevron-down"></i></a>
        <a href="/CeylonGo/public/tourist/recommended-packages?category=solo" class="package-bar-item">Solo <i class="fa-solid fa-chevron-down"></i></a>
        <a href="/CeylonGo/public/tourist/recommended-packages?category=honeymoon" class="package-bar-item">Honeymoon <i class="fa-solid fa-chevron-down"></i></a>
        <a href="/CeylonGo/public/tourist/recommended-packages?category=family" class="package-bar-item">Family <i class="fa-solid fa-chevron-down"></i></a>
        <a href="/CeylonGo/public/tourist/recommended-packages?category=group" class="package-bar-item">Group Departure <i class="fa-solid fa-chevron-down"></i></a>
        <a href="/CeylonGo/public/tourist/recommended-packages?category=one-day" class="package-bar-item">One Day <i class="fa-solid fa-chevron-down"></i></a>
    </nav>

    <main class="dashboard-main">
        <section class="dashboard-hero">
            <video class="dashboard-hero-video" autoplay muted loop playsinline>
                <source src="/CeylonGo/public/images/lanka.mp4" type="video/mp4">
            </video>
            <div class="dashboard-hero-overlay"></div>
            <div class="dashboard-hero-content">
                <?php if ($is_logged_in): ?>
                    <h1 class="dashboard-hero-title">Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
                    <p class="dashboard-hero-tagline">Ready to plan your perfect trip to Sri Lanka?</p>
                <?php else: ?>
                    <h1 class="dashboard-hero-title">Plan Your Perfect Trip to Sri Lanka</h1>
                    <p class="dashboard-hero-tagline">Explore the beauty of Sri Lanka with our customizable tour packages.</p>
                <?php endif; ?>
                <div class="dashboard-hero-btns">
                    <?php if ($is_logged_in): ?>
                        <a href="/CeylonGo/public/tourist/customize-trip" class="btn-hero-secondary" aria-label="Customise Your Trip"> Customise Your Trip</a>
                    <?php else: ?>
                        <a href="#" class="btn-hero-secondary" id="customise-trip-btn" aria-label="Customise Your Trip"> Customise Your Trip</a>
                    <?php endif; ?>
                    <a href="/CeylonGo/public/tourist/packages" class="btn-hero-secondary"> Browse Popular Packages</a>
                </div>
            </div>
            <div class="dashboard-hero-get-started">
                <?php if (!$is_logged_in): ?>
                    <a href="/CeylonGo/public/register" class="btn-hero-primary">Get Started</a>
                <?php endif; ?>
            </div>
            <div class="dashboard-hero-scroll">↓</div>
            <div class="dashboard-hero-fab">
                <a href="tel:" class="fab-btn fab-phone" title="Call">📞</a>
                <a href="#" class="fab-btn fab-instagram" title="Instagram">📷</a>
                <a href="#" class="fab-btn fab-whatsapp" title="WhatsApp">💬</a>
                <a href="#" class="fab-btn fab-chat" title="Chat">💭</a>
            </div>
        </section>

        <section class="dashboard-how-it-works">
            <h2 class="how-it-works-title">How It <span class="how-it-works-accent">Works</span></h2>
            <p class="how-it-works-subtitle">Simple, fast, and completely personalized. Get your dream vacation planned in just 3 easy steps.</p>
            <div class="how-it-works-steps">
                <div class="how-step">
                    <div class="how-step-icon-wrap how-step-icon-1">
                        <i class="fa-regular fa-comment-dots"></i>
                        <span class="how-step-num">1</span>
                    </div>
                    <h3 class="how-step-title">Tell Us What You Want</h3>
                    <p class="how-step-desc">Fill out your travel preferences – destination, dates, budget, and special requirements.</p>
                </div>
                <div class="how-step-connector"></div>
                <div class="how-step">
                    <div class="how-step-icon-wrap how-step-icon-2">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span class="how-step-num">2</span>
                    </div>
                    <h3 class="how-step-title">We Craft the Perfect Plan</h3>
                    <p class="how-step-desc">Our travel experts create a personalized itinerary tailored just for you in 24 hours.</p>
                </div>
                <div class="how-step-connector"></div>
                <div class="how-step">
                    <div class="how-step-icon-wrap how-step-icon-3">
                        <i class="fa-solid fa-plane"></i>
                        <span class="how-step-num">3</span>
                    </div>
                    <h3 class="how-step-title">You Travel Stress-Free</h3>
                    <p class="how-step-desc">Everything's handled – from flight tickets to hotel bookings to 24/7 travel support.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Login popup modal (only shown for non-logged-in users) -->
    <?php if (!$is_logged_in): ?>
    <div class="login-modal-overlay" id="login-modal" aria-hidden="true">
        <div class="login-modal-box" role="dialog" aria-labelledby="login-modal-title" aria-modal="true">
            <button type="button" class="login-modal-close" id="login-modal-close" aria-label="Close">&times;</button>
            <h2 class="login-modal-title" id="login-modal-title">Login to Customise Your Trip</h2>
            <?php $loginError = $loginError ?? ''; ?>
            <?php if (!empty($loginError)): ?>
                <div class="login-modal-error"><?php echo htmlspecialchars($loginError); ?></div>
            <?php endif; ?>
            <form class="login-modal-form" method="POST" action="/CeylonGo/public/login">
                <input type="hidden" name="redirect" value="/CeylonGo/public/tourist/dashboard">
                <div class="form-group">
                    <label for="login-modal-email">Email Address</label>
                    <input type="email" id="login-modal-email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="login-modal-password">Password</label>
                    <input type="password" id="login-modal-password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="login-modal-btn">Login</button>
            </form>
            <p class="login-modal-register">Don't have an account? <a href="/CeylonGo/public/register">Register here</a></p>
        </div>
    </div>

    <script>
    (function () {
        var modal = document.getElementById('login-modal');
        var openBtn = document.getElementById('customise-trip-btn');
        var closeBtn = document.getElementById('login-modal-close');
        function openModal() {
            modal.classList.add('login-modal-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            modal.classList.remove('login-modal-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
        if (openBtn) openBtn.addEventListener('click', function (e) { e.preventDefault(); openModal(); });
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (modal) modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal.classList.contains('login-modal-open')) closeModal(); });
    })();
    </script>
    <?php endif; ?>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
