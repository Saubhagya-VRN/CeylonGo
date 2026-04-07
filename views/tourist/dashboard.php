<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist';
$user_name = $is_logged_in ? (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Tourist') : '';

// Full package data for dashboard dropdowns (with image, location, duration, rating)
$package_cards = array(
    1 => array('id' => 1, 'title' => 'Cultural Triangle', 'image' => '/CeylonGo/public/images/kandy.jpeg', 'location' => 'Kandy', 'duration' => '5D/4N', 'rating' => 4.5),
    2 => array('id' => 2, 'title' => 'Southern Coast Honeymoon', 'image' => '/CeylonGo/public/images/beach.jpg', 'location' => 'Galle', 'duration' => '5D/4N', 'rating' => 4.5),
    3 => array('id' => 3, 'title' => 'Hill Country Escape', 'image' => '/CeylonGo/public/images/greenary.jpg', 'location' => 'Nuwara Eliya', 'duration' => '6D/5N', 'rating' => 4.8),
    4 => array('id' => 4, 'title' => 'Ancient Heritage Trail', 'image' => '/CeylonGo/public/images/perehara.jpeg', 'location' => 'Anuradhapura', 'duration' => '4D/3N', 'rating' => 4.6),
    5 => array('id' => 5, 'title' => 'Wildlife Safari', 'image' => '/CeylonGo/public/images/elephant.jpg', 'location' => 'Yala', 'duration' => '4D/3N', 'rating' => 4.7),
    6 => array('id' => 6, 'title' => 'Solo Explorer', 'image' => '/CeylonGo/public/images/train.jpg', 'location' => 'Ella', 'duration' => '6D/5N', 'rating' => 4.4),
    7 => array('id' => 7, 'title' => 'Family Fun', 'image' => '/CeylonGo/public/images/resort.jpg', 'location' => 'Bentota', 'duration' => '5D/4N', 'rating' => 4.5),
    8 => array('id' => 8, 'title' => 'Beach Getaway', 'image' => '/CeylonGo/public/images/sunset.jpg', 'location' => 'Hikkaduwa', 'duration' => '3D/2N', 'rating' => 4.3),
);
$packages_by_cat = array(
    'solo'      => array(6),
    'honeymoon' => array(2),
    'family'    => array(7),
    'cultural'  => array(1),
    'adventure' => array(3),
    'heritage'  => array(4),
    'safari'    => array(5),
    'beach'     => array(8),
);
$trending_bar_packages = isset($trending_bar_packages) ? $trending_bar_packages : array();
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

    <nav class="dashboard-package-bar" aria-label="Package options">
        <div class="package-bar-dropdown">
            <span class="package-bar-trigger">Popular <i class="fa-solid fa-chevron-down"></i></span>
            <div class="package-bar-panel package-bar-panel--cards">
                <?php foreach ($trending_bar_packages as $p): ?>
                <a href="/CeylonGo/public/tourist/package-details/<?php echo (int)$p['id']; ?>" class="package-bar-card">
                    <img src="<?php echo htmlspecialchars(isset($p['image']) ? $p['image'] : ''); ?>" alt="" class="package-bar-card-img">
                    <div class="package-bar-card-body">
                        <span class="package-bar-card-title"><?php echo htmlspecialchars(isset($p['title']) ? $p['title'] : ''); ?></span>
                        <span class="package-bar-card-meta"><?php echo htmlspecialchars(isset($p['location']) ? $p['location'] : ''); ?> · <span class="package-bar-card-dur"><?php echo htmlspecialchars(isset($p['duration_short']) ? $p['duration_short'] : (isset($p['duration']) ? $p['duration'] : '')); ?></span> <span class="package-bar-card-star">★ <?php echo htmlspecialchars((string)(isset($p['rating']) ? $p['rating'] : '')); ?></span></span>
                    </div>
                </a>
                <?php endforeach; ?>
                <a href="/CeylonGo/public/tourist/packages?trending=1" class="package-bar-explore">Explore trending packages</a>
            </div>
        </div>
        <div class="package-bar-dropdown">
            <span class="package-bar-trigger">Solo <i class="fa-solid fa-chevron-down"></i></span>
            <div class="package-bar-panel package-bar-panel--cards">
                <?php foreach ($packages_by_cat['solo'] ?? [] as $id): $p = $package_cards[$id] ?? null; if (!$p) continue; ?>
                <a href="/CeylonGo/public/tourist/package-details/<?php echo (int)$p['id']; ?>" class="package-bar-card">
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" class="package-bar-card-img">
                    <div class="package-bar-card-body">
                        <span class="package-bar-card-title"><?php echo htmlspecialchars($p['title']); ?></span>
                        <span class="package-bar-card-meta"><?php echo htmlspecialchars($p['location']); ?> · <span class="package-bar-card-dur"><?php echo htmlspecialchars($p['duration']); ?></span> <span class="package-bar-card-star">★ <?php echo htmlspecialchars($p['rating']); ?></span></span>
                    </div>
                </a>
                <?php endforeach; ?>
                <a href="/CeylonGo/public/tourist/packages?category=solo" class="package-bar-explore">Explore all Solo packages</a>
            </div>
        </div>
        <div class="package-bar-dropdown">
            <span class="package-bar-trigger">Honeymoon <i class="fa-solid fa-chevron-down"></i></span>
            <div class="package-bar-panel package-bar-panel--cards">
                <?php foreach ($packages_by_cat['honeymoon'] ?? [] as $id): $p = $package_cards[$id] ?? null; if (!$p) continue; ?>
                <a href="/CeylonGo/public/tourist/package-details/<?php echo (int)$p['id']; ?>" class="package-bar-card">
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" class="package-bar-card-img">
                    <div class="package-bar-card-body">
                        <span class="package-bar-card-title"><?php echo htmlspecialchars($p['title']); ?></span>
                        <span class="package-bar-card-meta"><?php echo htmlspecialchars($p['location']); ?> · <span class="package-bar-card-dur"><?php echo htmlspecialchars($p['duration']); ?></span> <span class="package-bar-card-star">★ <?php echo htmlspecialchars($p['rating']); ?></span></span>
                    </div>
                </a>
                <?php endforeach; ?>
                <a href="/CeylonGo/public/tourist/packages?category=honeymoon" class="package-bar-explore">Explore all Honeymoon packages</a>
            </div>
        </div>
        <div class="package-bar-dropdown">
            <span class="package-bar-trigger">Family <i class="fa-solid fa-chevron-down"></i></span>
            <div class="package-bar-panel package-bar-panel--cards">
                <?php foreach ($packages_by_cat['family'] ?? [] as $id): $p = $package_cards[$id] ?? null; if (!$p) continue; ?>
                <a href="/CeylonGo/public/tourist/package-details/<?php echo (int)$p['id']; ?>" class="package-bar-card">
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" class="package-bar-card-img">
                    <div class="package-bar-card-body">
                        <span class="package-bar-card-title"><?php echo htmlspecialchars($p['title']); ?></span>
                        <span class="package-bar-card-meta"><?php echo htmlspecialchars($p['location']); ?> · <span class="package-bar-card-dur"><?php echo htmlspecialchars($p['duration']); ?></span> <span class="package-bar-card-star">★ <?php echo htmlspecialchars($p['rating']); ?></span></span>
                    </div>
                </a>
                <?php endforeach; ?>
                <a href="/CeylonGo/public/tourist/packages?category=family" class="package-bar-explore">Explore all Family packages</a>
            </div>
        </div>
        <div class="package-bar-dropdown">
            <span class="package-bar-trigger">Group Departure <i class="fa-solid fa-chevron-down"></i></span>
            <div class="package-bar-panel">
                <a href="/CeylonGo/public/tourist/packages?category=group" class="package-bar-explore">Explore all Group Departure packages</a>
            </div>
        </div>
        <div class="package-bar-dropdown">
            <span class="package-bar-trigger">One Day <i class="fa-solid fa-chevron-down"></i></span>
            <div class="package-bar-panel">
                <a href="/CeylonGo/public/tourist/packages?category=one-day" class="package-bar-explore">Explore all One Day packages</a>
            </div>
        </div>
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
                    <p class="how-step-desc">Everything's handled – from itinerary planning to hotel and transport bookings to 24/7 travel support.</p>
                </div>
            </div>
        </section>

        <section class="dashboard-inquiry" id="inquiry">
            <h2 class="how-it-works-title">Contact <span class="how-it-works-accent">Inquiry Form</span></h2>
            <p class="how-it-works-subtitle">Ask a question about your trip, bookings, or payments. Our team will reply here.</p>

            <?php
              $inquiries = isset($inquiries) ? $inquiries : array();
              $inqErr = isset($_SESSION['inquiry_error']) ? $_SESSION['inquiry_error'] : '';
              $inqInfo = isset($_SESSION['inquiry_info']) ? $_SESSION['inquiry_info'] : '';
              unset($_SESSION['inquiry_error'], $_SESSION['inquiry_info']);
            ?>

            <?php if ($inqErr): ?>
              <div class="dash-flash dash-flash--error"><?php echo htmlspecialchars($inqErr); ?></div>
            <?php endif; ?>
            <?php if ($inqInfo): ?>
              <div class="dash-flash dash-flash--info"><?php echo htmlspecialchars($inqInfo); ?></div>
            <?php endif; ?>

            <div class="dash-inquiry-card">
                <form class="dash-inquiry-form" method="post" action="/CeylonGo/public/tourist/inquiries">
                  <div class="dash-inquiry-row dash-inquiry-row--split">
                    <div class="dash-inquiry-field">
                      <label for="inq_first_name">1. Full name</label>
                      <input type="text" id="inq_first_name" name="first_name" placeholder="First Name" autocomplete="given-name">
                    </div>
                    <div class="dash-inquiry-field">
                      <label class="dash-inquiry-label-spacer" aria-hidden="true">&nbsp;</label>
                      <input type="text" id="inq_last_name" name="last_name" placeholder="Last Name" autocomplete="family-name">
                    </div>
                  </div>

                  <div class="dash-inquiry-row">
                    <div class="dash-inquiry-field dash-inquiry-field--icon">
                      <label for="inq_email">2. Email</label>
                      <div class="dash-inquiry-input-icon-wrap">
                        <span class="dash-inquiry-icon"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" id="inq_email" name="email" placeholder="Email" autocomplete="email" <?php echo $is_logged_in ? '' : 'required'; ?>>
                      </div>
                    </div>
                  </div>

                  <div class="dash-inquiry-row">
                    <div class="dash-inquiry-field">
                      <label for="inq_type">3. Inquiry type</label>
                      <input type="text" id="inq_type" name="subject" placeholder="Type" required maxlength="150">
                    </div>
                  </div>

                  <div class="dash-inquiry-row">
                    <div class="dash-inquiry-field">
                      <label for="inq_message">4. Message</label>
                      <textarea id="inq_message" name="message" rows="4" placeholder="Message" required></textarea>
                    </div>
                  </div>

                  <div class="dash-inquiry-actions">
                    <button type="submit" class="dash-inquiry-btn">Submit</button>
                  </div>
                </form>
              </div>

              <?php if ($is_logged_in): ?>
                <div class="dash-inquiry-list">
                  <h3 class="dash-inquiry-list-title">Your recent inquiries</h3>
                  <?php if (empty($inquiries)): ?>
                    <p class="dash-inquiry-empty">No inquiries yet.</p>
                  <?php else: ?>
                    <?php foreach ($inquiries as $inq): ?>
                      <?php $st = isset($inq['status']) ? $inq['status'] : 'pending'; ?>
                      <article class="dash-inquiry-item">
                        <div class="dash-inquiry-item-head">
                          <strong><?php echo htmlspecialchars(isset($inq['subject']) ? $inq['subject'] : ''); ?></strong>
                          <span class="dash-inquiry-status <?php echo ($st === 'replied') ? 'dash-inquiry-status--replied' : 'dash-inquiry-status--pending'; ?>">
                            <?php echo htmlspecialchars(ucfirst($st)); ?>
                          </span>
                        </div>
                        <div class="dash-inquiry-item-body"><?php echo nl2br(htmlspecialchars(isset($inq['message']) ? $inq['message'] : '')); ?></div>
                        <?php if (!empty($inq['admin_reply'])): ?>
                          <div class="dash-inquiry-reply">
                            <strong>Admin reply:</strong>
                            <div><?php echo nl2br(htmlspecialchars($inq['admin_reply'])); ?></div>
                          </div>
                        <?php else: ?>
                          <div class="dash-inquiry-reply dash-inquiry-reply--muted">No reply yet.</div>
                        <?php endif; ?>
                      </article>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <p style="max-width:560px;margin:12px auto 0;color:#6b7280;">Tip: Log in to view your inquiry history and admin replies inside your dashboard.</p>
              <?php endif; ?>
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
                <input type="hidden" name="redirect" value="/CeylonGo/public/tourist/customize-trip">
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
            if (!modal) return;
            modal.classList.add('login-modal-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            if (!modal) return;
            modal.classList.remove('login-modal-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
        if (openBtn) openBtn.addEventListener('click', function (e) { e.preventDefault(); openModal(); });
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (modal) modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal.classList.contains('login-modal-open')) closeModal(); });

        // If coming from header "Customize Trip" as guest, open modal automatically
        try {
            var params = new URLSearchParams(window.location.search || '');
            if (params.get('openLogin') === '1') openModal();
        } catch (e) {}
    })();
    </script>
    <?php endif; ?>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
