<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if user is logged in (same rule as header.php / trip(): role OR type)
$_dash_role = strtolower((string) (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : ''));
$_dash_type = strtolower((string) (isset($_SESSION['user_type']) ? $_SESSION['user_type'] : ''));
$is_logged_in = isset($_SESSION['user_id']) && ($_dash_role === 'tourist' || $_dash_type === 'tourist');
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
$public_reviews = isset($public_reviews) && is_array($public_reviews) ? $public_reviews : array();
$tourist_data = isset($tourist_data) ? $tourist_data : null;
$inq_prefill_first = '';
$inq_prefill_last = '';
$inq_prefill_email = '';
if ($is_logged_in && is_array($tourist_data)) {
    $inq_prefill_first = isset($tourist_data['first_name']) ? (string) $tourist_data['first_name'] : '';
    $inq_prefill_last = isset($tourist_data['last_name']) ? (string) $tourist_data['last_name'] : '';
    $inq_prefill_email = isset($tourist_data['email']) ? (string) $tourist_data['email'] : '';
}
if ($is_logged_in && $inq_prefill_email === '' && isset($_SESSION['user_email'])) {
    $inq_prefill_email = (string) $_SESSION['user_email'];
}
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
    <script>
    /* Login modal sets body overflow:hidden; clear on every load/back-forward (bfcache). */
    (function () {
        function unlockScroll() {
            try {
                document.body.style.overflow = '';
                document.documentElement.style.overflow = '';
            } catch (e) {}
        }
        unlockScroll();
        window.addEventListener('pageshow', function (ev) {
            if (ev.persisted) unlockScroll();
        });
        <?php if (!empty($is_logged_in)): ?>
        try {
            var u = new URL(window.location.href);
            if (u.searchParams.has('openLogin')) {
                u.searchParams.delete('openLogin');
                window.history.replaceState({}, '', u.pathname + (u.searchParams.toString() ? '?' + u.searchParams.toString() : '') + u.hash);
            }
        } catch (e) {}
        <?php endif; ?>
    })();
    </script>
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
                <a href="tel:+94112345678" class="fab-btn fab-call" title="Call +94 11 234 5678" aria-label="Call +94 11 234 5678">📞</a>
                <a href="mailto:ceylongo@gmail.com" class="fab-btn fab-email" title="Email ceylongo@gmail.com" aria-label="Email ceylongo@gmail.com"><i class="fa-regular fa-envelope"></i></a>
                <a href="#inquiry" class="fab-btn fab-inquire" title="Inquire" aria-label="Go to inquiry form">💭</a>
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
                    <h3 class="how-step-title">Tell Us Your Travel Plans</h3>
                    <p class="how-step-desc">Share your destination, dates, budget, and any special requirements.</p>
                </div>
                <div class="how-step-connector"></div>
                <div class="how-step">
                    <div class="how-step-icon-wrap how-step-icon-2">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span class="how-step-num">2</span>
                    </div>
                    <h3 class="how-step-title">We Find the Best Options</h3>
                    <p class="how-step-desc">CeylonGo matches you with the most suitable transport, guides, and stays.</p>
                </div>
                <div class="how-step-connector"></div>
                <div class="how-step">
                    <div class="how-step-icon-wrap how-step-icon-3">
                        <i class="fa-solid fa-plane"></i>
                        <span class="how-step-num">3</span>
                    </div>
                    <h3 class="how-step-title">Travel with Ease</h3>
                    <p class="how-step-desc">Confirm your bookings and enjoy a smooth, stress-free journey.</p>
                </div>
            </div>
        </section>

        <section class="dashboard-trending" aria-label="Trending destinations">
            <h2 class="how-it-works-title">Trending <span class="how-it-works-accent">destinations</span></h2>
            <p class="how-it-works-subtitle">Most popular choices for travelers from Sri Lanka</p>

            <div class="dash-dest-grid">
                <a class="dash-dest-card dash-dest-card--colombo" href="/CeylonGo/public/tourist/packages?search=Colombo" style="background-image:url('/CeylonGo/public/images/colombo.jpg');">
                    <span class="dash-dest-card__shade" aria-hidden="true"></span>
                    <span class="dash-dest-card__title">Colombo</span>
                </a>
                <a class="dash-dest-card dash-dest-card--kandy" href="/CeylonGo/public/tourist/packages?search=Kandy" style="background-image:url('/CeylonGo/public/images/kandy.jpg');">
                    <span class="dash-dest-card__shade" aria-hidden="true"></span>
                    <span class="dash-dest-card__title">Kandy</span>
                </a>
                <a class="dash-dest-card dash-dest-card--nuwara" href="/CeylonGo/public/tourist/packages?search=Nuwara" style="background-image:url('/CeylonGo/public/images/Nuwara-Eliya-.jpg');">
                    <span class="dash-dest-card__shade" aria-hidden="true"></span>
                    <span class="dash-dest-card__title">Nuwara Eliya</span>
                </a>
                <a class="dash-dest-card dash-dest-card--negombo" href="/CeylonGo/public/tourist/packages?search=Negombo" style="background-image:url('/CeylonGo/public/images/negombo.jpg');">
                    <span class="dash-dest-card__shade" aria-hidden="true"></span>
                    <span class="dash-dest-card__title">Negombo</span>
                </a>
                <a class="dash-dest-card dash-dest-card--galle" href="/CeylonGo/public/tourist/packages?search=Galle" style="background-image:url('/CeylonGo/public/images/galle.webp');">
                    <span class="dash-dest-card__shade" aria-hidden="true"></span>
                    <span class="dash-dest-card__title">Galle</span>
                </a>
            </div>
        </section>

        <?php if (!empty($public_reviews)): ?>
        <section class="dashboard-travelers-say" aria-label="What our travelers say">
            <h2 class="how-it-works-title">What Our <span class="how-it-works-accent">Travelers Say</span></h2>
            <p class="how-it-works-subtitle">Real stories from travelers who experienced our personalized Sri Lanka trips.</p>

            <div class="dash-reviews-carousel">
                <button type="button" class="dash-reviews-arrow dash-reviews-arrow--prev" id="dashReviewsPrev" aria-label="Previous reviews">
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </button>
                <div class="dash-reviews-scroll-wrap">
                    <div class="dash-reviews-scroll" id="dashReviewsScroll" tabindex="0">
                        <?php foreach ($public_reviews as $rev):
                            $rt = isset($rev['review_text']) ? (string) $rev['review_text'] : '';
                            $rating = (int) (isset($rev['rating']) ? $rev['rating'] : 0);
                            if ($rating < 1) {
                                $rating = 1;
                            }
                            if ($rating > 5) {
                                $rating = 5;
                            }
                            $nm = isset($rev['name']) ? trim((string) $rev['name']) : 'Traveler';
                            $initial = $nm !== '' ? strtoupper(substr($nm, 0, 1)) : 'T';
                            $rawTs = isset($rev['created_at']) ? strtotime((string) $rev['created_at']) : false;
                            $dateLabel = $rawTs ? date('F Y', $rawTs) : '';
                            $excerptLen = 180;
                            $needsToggle = function_exists('mb_strlen') ? (mb_strlen($rt) > $excerptLen) : (strlen($rt) > $excerptLen);
                            $excerpt = $needsToggle
                                ? ((function_exists('mb_substr') ? mb_substr($rt, 0, $excerptLen) : substr($rt, 0, $excerptLen)) . '…')
                                : $rt;
                        ?>
                        <article class="dash-review-card">
                            <div class="dash-review-stars" aria-label="<?php echo $rating; ?> out of 5 stars">
                                <?php for ($si = 1; $si <= 5; $si++): ?>
                                <span class="dash-review-star<?php echo $si <= $rating ? ' dash-review-star--on' : ''; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <blockquote class="dash-review-quote">
                                <?php if ($needsToggle): ?>
                                <div class="dash-review-snippet dash-review-snippet--collapsed">
                                    <span class="dash-review-open">"</span><span class="dash-review-excerpt"><?php echo htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-review-close">"</span>
                                </div>
                                <div class="dash-review-snippet dash-review-snippet--expanded" hidden>
                                    <span class="dash-review-open">"</span><span class="dash-review-full"><?php echo htmlspecialchars($rt, ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-review-close">"</span>
                                </div>
                                <button type="button" class="dash-review-toggle" data-expanded="false">
                                    <span class="dash-review-toggle-more">View more <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></span>
                                    <span class="dash-review-toggle-less" hidden>View less <i class="fa-solid fa-chevron-up" aria-hidden="true"></i></span>
                                </button>
                                <?php else: ?>
                                <div class="dash-review-snippet">
                                    <span class="dash-review-open">"</span><span class="dash-review-single"><?php echo htmlspecialchars($rt, ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-review-close">"</span>
                                </div>
                                <?php endif; ?>
                            </blockquote>
                            <div class="dash-review-footer">
                                <div class="dash-review-avatar" aria-hidden="true"><?php echo htmlspecialchars($initial, ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="dash-review-meta">
                                    <div class="dash-review-name"><?php echo htmlspecialchars($nm, ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="dash-review-tagline">Experience with Ceylon Go<?php echo $dateLabel !== '' ? ' · ' . htmlspecialchars($dateLabel, ENT_QUOTES, 'UTF-8') : ''; ?></div>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="button" class="dash-reviews-arrow dash-reviews-arrow--next" id="dashReviewsNext" aria-label="Next reviews">
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </button>
            </div>
        </section>
        <?php endif; ?>

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
                <form class="dash-inquiry-form" id="dash-inquiry-form" method="post" action="/CeylonGo/public/tourist/inquiries">
                  <div class="dash-inquiry-row dash-inquiry-row--split">
                    <div class="dash-inquiry-field">
                      <label for="inq_first_name">1. Full name</label>
                      <input type="text" id="inq_first_name" name="first_name" placeholder="First Name" autocomplete="given-name" value="<?php echo $is_logged_in ? htmlspecialchars($inq_prefill_first, ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                    <div class="dash-inquiry-field">
                      <label class="dash-inquiry-label-spacer" aria-hidden="true">&nbsp;</label>
                      <input type="text" id="inq_last_name" name="last_name" placeholder="Last Name" autocomplete="family-name" value="<?php echo $is_logged_in ? htmlspecialchars($inq_prefill_last, ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                  </div>

                  <div class="dash-inquiry-row">
                    <div class="dash-inquiry-field dash-inquiry-field--icon">
                      <label for="inq_email">2. Email</label>
                      <div class="dash-inquiry-input-icon-wrap">
                        <span class="dash-inquiry-icon"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" id="inq_email" name="email" placeholder="Email" autocomplete="email" value="<?php echo $is_logged_in ? htmlspecialchars($inq_prefill_email, ENT_QUOTES, 'UTF-8') : ''; ?>">
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
                  <div class="dash-inquiry-list-head">
                    <h3 class="dash-inquiry-list-title">My inquiries</h3>
                    <div class="dash-inquiry-list-cta">
                      <a href="/CeylonGo/public/tourist/my-inquiries" class="dash-inquiry-btn dash-inquiry-btn--outline dash-inquiry-btn--compact">View all my inquiries</a>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <p class="dash-inquiry-guest-hint">Log in to submit your inquiry. We'll get back to you soon.</p>
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
                <input type="hidden" name="redirect" id="login-modal-redirect" value="/CeylonGo/public/tourist/customize-trip">
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
        var inqForm = document.getElementById('dash-inquiry-form');
        var closeBtn = document.getElementById('login-modal-close');
        var redirectInput = document.getElementById('login-modal-redirect');
        var titleEl = document.getElementById('login-modal-title');
        var customizeRedirect = '/CeylonGo/public/tourist/customize-trip';
        var inquiryRedirect = '/CeylonGo/public/tourist/dashboard#inquiry';

        function setLoginRedirect(url) {
            if (redirectInput) redirectInput.value = url;
        }
        function setModalTitleForRedirect(url) {
            if (!titleEl) return;
            titleEl.textContent = (url && url.indexOf('#inquiry') !== -1)
                ? 'Login to submit an inquiry'
                : 'Login to Customise Your Trip';
        }
        function openModal(redirectUrl) {
            if (!modal) return;
            var url = redirectUrl || customizeRedirect;
            setLoginRedirect(url);
            setModalTitleForRedirect(url);
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
        if (openBtn) openBtn.addEventListener('click', function (e) { e.preventDefault(); openModal(customizeRedirect); });
        if (inqForm) inqForm.addEventListener('submit', function (e) {
            e.preventDefault();
            try {
                var draft = {
                    subject: (document.getElementById('inq_type') || {}).value || '',
                    message: (document.getElementById('inq_message') || {}).value || ''
                };
                sessionStorage.setItem('CeylonGo_inquiry_draft', JSON.stringify(draft));
            } catch (err) {}
            openModal(inquiryRedirect);
        });
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (modal) modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal && modal.classList.contains('login-modal-open')) closeModal(); });

        try {
            var params = new URLSearchParams(window.location.search || '');
            if (params.get('openLogin') === '1') openModal(customizeRedirect);
            else if (params.get('openLogin') === 'inquiry') {
                openModal(inquiryRedirect);
                var sec = document.getElementById('inquiry');
                if (sec) setTimeout(function () { sec.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 100);
                if (history.replaceState) {
                    history.replaceState({}, '', window.location.pathname + '#inquiry');
                }
            }
        } catch (e) {}
    })();
    </script>
    <?php endif; ?>

    <?php if ($is_logged_in): ?>
    <script>
    (function () {
        var KEY = 'CeylonGo_inquiry_draft';
        try {
            var raw = sessionStorage.getItem(KEY);
            if (!raw) return;
            var d = JSON.parse(raw);
            sessionStorage.removeItem(KEY);
            var sub = document.getElementById('inq_type');
            var msg = document.getElementById('inq_message');
            if (sub && d && typeof d.subject === 'string') sub.value = d.subject;
            if (msg && d && typeof d.message === 'string') msg.value = d.message;
        } catch (e) {
            try { sessionStorage.removeItem(KEY); } catch (x) {}
        }
    })();
    </script>
    <?php endif; ?>

    <?php if (!empty($public_reviews)): ?>
    <script>
    (function () {
        var scrollEl = document.getElementById('dashReviewsScroll');
        var prev = document.getElementById('dashReviewsPrev');
        var next = document.getElementById('dashReviewsNext');
        if (scrollEl && prev && next) {
            function step() {
                var w = scrollEl.clientWidth;
                return Math.max(260, Math.floor(w * 0.88));
            }
            prev.addEventListener('click', function () {
                scrollEl.scrollBy({ left: -step(), behavior: 'smooth' });
            });
            next.addEventListener('click', function () {
                scrollEl.scrollBy({ left: step(), behavior: 'smooth' });
            });
        }
        document.querySelectorAll('.dash-review-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var q = btn.closest('.dash-review-quote');
                if (!q) return;
                var shortBlock = q.querySelector('.dash-review-snippet--collapsed');
                var longBlock = q.querySelector('.dash-review-snippet--expanded');
                var more = btn.querySelector('.dash-review-toggle-more');
                var less = btn.querySelector('.dash-review-toggle-less');
                var expanded = btn.getAttribute('data-expanded') === 'true';
                if (expanded) {
                    if (shortBlock) shortBlock.removeAttribute('hidden');
                    if (longBlock) longBlock.setAttribute('hidden', '');
                    if (more) more.removeAttribute('hidden');
                    if (less) less.setAttribute('hidden', '');
                    btn.setAttribute('data-expanded', 'false');
                } else {
                    if (shortBlock) shortBlock.setAttribute('hidden', '');
                    if (longBlock) longBlock.removeAttribute('hidden');
                    if (more) more.setAttribute('hidden', '');
                    if (less) less.removeAttribute('hidden');
                    btn.setAttribute('data-expanded', 'true');
                }
            });
        });
    })();
    </script>
    <?php endif; ?>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
