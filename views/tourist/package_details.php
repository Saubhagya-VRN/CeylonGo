<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$p = $package ?? null;
if (!$p) {
    header('Location: /CeylonGo/public/tourist/packages');
    exit;
}
$package_reviews = $package_reviews ?? [];
$pr_count = count($package_reviews);
$pr_avg = $pr_count > 0 ? round(array_sum(array_column($package_reviews, 'rating')) / $pr_count, 1) : 0;
$pr_satisfaction = $pr_count > 0 ? min(100, (int) round(($pr_avg / 5) * 100)) : 0;
$overview = $p['overview'] ?? [];
$highlights = $p['highlights'] ?? [];
$itinerary = $p['itinerary'] ?? [];
$included = $p['included'] ?? [];
$excluded = $p['excluded'] ?? [];
$accommodation = $p['accommodation'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($p['title']); ?> - Ceylon Go</title>
    <?php
    $asset_base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    if ($asset_base === '' || $asset_base === '/') $asset_base = '/CeylonGo/public';
    ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/common.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/navbar.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/footer.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/package_details.css">
</head>
<body class="pkg-detail-page">
    <?php include __DIR__ . '/header.php'; ?>

    <main class="pkg-detail-main">
        <div class="pkg-detail-content">

            <div class="pkg-detail-header">
                <h1 class="pkg-detail-title"><?php echo htmlspecialchars($p['title']); ?></h1>
                <div class="pkg-detail-badges">
                    <span class="pkg-badge pkg-badge--primary">
                        <svg class="pkg-badge-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?php echo htmlspecialchars($p['duration_short'] ?? $p['duration']); ?>
                    </span>
                    <span class="pkg-badge pkg-badge--blue"><?php echo htmlspecialchars($p['category']); ?></span>
                    <span class="pkg-badge pkg-badge--green">
                        <svg class="pkg-badge-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?php echo htmlspecialchars($p['location']); ?>
                    </span>
                    <?php if (!empty($p['trending'])): ?>
                    <span class="pkg-badge pkg-badge--trending">Trending</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pkg-detail-hero">
                <?php if (!empty($p['image'])): ?>
                <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="pkg-detail-image">
                <?php endif; ?>
                <?php if (isset($p['price'])): ?>
                <div class="pkg-detail-price-wrap">
                    <span class="pkg-detail-price">LKR <?php echo number_format((int)$p['price']); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <section class="pkg-section pkg-overview">
                <h2 class="pkg-section-title"><span class="pkg-section-bar"></span>Trip Overview</h2>
                <div class="pkg-overview-inner">
                    <?php foreach ($overview as $para): ?>
                    <p class="pkg-overview-para"><?php echo htmlspecialchars($para); ?></p>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="pkg-section pkg-highlights">
                <h2 class="pkg-section-title">Package Highlights</h2>
                <div class="pkg-highlights-grid">
                    <?php foreach ($highlights as $h): ?>
                    <div class="pkg-highlight-card">
                        <div class="pkg-highlight-icon pkg-highlight-icon--<?php echo htmlspecialchars($h['icon'] ?? 'hotel'); ?>">
                            <?php if (($h['icon'] ?? '') === 'hotel'): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            <?php elseif (($h['icon'] ?? '') === 'transfer'): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9-.6 1.1-.9 2.4-.9 3.6v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                            <?php elseif (($h['icon'] ?? '') === 'sightseeing'): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <?php elseif (($h['icon'] ?? '') === 'meals'): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                            <?php elseif (($h['icon'] ?? '') === 'activities'): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/></svg>
                            <?php else: ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <?php endif; ?>
                        </div>
                        <h3 class="pkg-highlight-title"><?php echo htmlspecialchars($h['title'] ?? ''); ?></h3>
                        <p class="pkg-highlight-desc"><?php echo htmlspecialchars($h['desc'] ?? ''); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="pkg-tags">
                    <span class="pkg-tag">Best Price Guaranteed</span>
                    <span class="pkg-tag">Expert Travel Guide</span>
                    <span class="pkg-tag">Confirmation within 24 hrs</span>
                </div>
            </section>

            <section class="pkg-section pkg-itinerary">
                <h2 class="pkg-section-title">Detailed Itinerary</h2>
                <div class="pkg-itinerary-summary">
                    <span><?php echo count($itinerary); ?> Days Adventure</span>
                </div>
                <div class="pkg-itinerary-days">
                    <?php foreach ($itinerary as $idx => $day): ?>
                    <div class="pkg-day-card <?php echo $idx === 0 ? 'pkg-day-card--open' : ''; ?>" data-day="<?php echo (int)$day['day']; ?>">
                        <div class="pkg-day-header">
                            <div class="pkg-day-num"><?php echo (int)$day['day']; ?></div>
                            <div class="pkg-day-title-wrap">
                                <h3 class="pkg-day-title">Day <?php echo (int)$day['day']; ?>: <?php echo htmlspecialchars($day['title'] ?? ''); ?></h3>
                                <p class="pkg-day-sub">Day <?php echo (int)$day['day']; ?> Itinerary</p>
                            </div>
                            <span class="pkg-day-toggle" aria-label="Toggle">▼</span>
                        </div>
                        <div class="pkg-day-body">
                            <div class="pkg-day-activities">
                                <?php foreach ($day['activities'] ?? [] as $act): ?>
                                <div class="pkg-activity">
                                    <span class="pkg-activity-dot"></span>
                                    <p><?php echo htmlspecialchars($act); ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="pkg-notes">
                    <strong>Important Notes</strong>
                    <ul>
                        <li>Itinerary is subject to change based on weather and local circumstances.</li>
                        <li>All timings are approximate and may vary.</li>
                        <li>Entry fees to monuments and attractions are included unless mentioned otherwise.</li>
                    </ul>
                </div>
            </section>

            <section class="pkg-section pkg-accommodation">
                <h2 class="pkg-section-title">Accommodation Details</h2>
                <div class="pkg-accommodation-inner">
                    <?php if (!empty($accommodation)): ?>
                    <ul class="pkg-accommodation-list">
                        <?php foreach ($accommodation as $stay): ?>
                        <li>
                            <strong><?php echo (int)$stay['nights']; ?> <?php echo (int)$stay['nights'] === 1 ? 'night' : 'nights'; ?></strong>
                            — <?php echo htmlspecialchars($stay['hotel']); ?>
                            <span class="pkg-accommodation-location">(<?php echo htmlspecialchars($stay['location']); ?>)</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p>Accommodation details for this package are available on request. Our travel experts will share options when you enquire.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="pkg-section pkg-included">
                <h2 class="pkg-section-title">What's Included & Excluded</h2>
                <p class="pkg-section-sub">Transparent pricing with detailed breakdown of what's covered.</p>
                <div class="pkg-included-grid">
                    <div class="pkg-included-col pkg-included-col--yes">
                        <h3>What's Included</h3>
                        <ul>
                            <?php foreach ($included as $item): ?>
                            <li><span class="pkg-check">✓</span> <?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="pkg-included-note">All above services are included in the package price.</p>
                    </div>
                    <div class="pkg-included-col pkg-included-col--no">
                        <h3>What's Not Included</h3>
                        <ul>
                            <?php foreach ($excluded as $item): ?>
                            <li><span class="pkg-cross">✕</span> <?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="pkg-included-note">These services are not included.</p>
                    </div>
                </div>
            </section>

            <section class="pkg-section pkg-reviews">
                <h2 class="pkg-section-title">Customer Reviews</h2>
                <?php if ($pr_count === 0): ?>
                <div class="pkg-reviews-empty">
                    <p><strong>No published reviews yet</strong></p>
                    <p>Be the first to share your experience! New submissions are moderated before they appear here.</p>
                    <a href="/CeylonGo/public/tourist/add-review?package=<?php echo (int)$p['id']; ?>" class="pkg-btn pkg-btn--primary">Write a review</a>
                </div>
                <?php else: ?>
                <ul class="pkg-reviews-list">
                    <?php foreach ($package_reviews as $rev): ?>
                    <li class="pkg-review-card">
                        <div class="pkg-review-card__head">
                            <strong class="pkg-review-card__name"><?php echo htmlspecialchars($rev['name'] ?? 'Traveler'); ?></strong>
                            <span class="pkg-review-card__stars" aria-label="<?php echo (int)($rev['rating'] ?? 0); ?> out of 5 stars"><?php
                                $rr = (int)($rev['rating'] ?? 0);
                                for ($si = 1; $si <= 5; $si++) {
                                    echo $si <= $rr ? '★' : '☆';
                                }
                            ?></span>
                        </div>
                        <p class="pkg-review-card__text"><?php echo nl2br(htmlspecialchars($rev['review_text'] ?? '')); ?></p>
                        <time class="pkg-review-card__date" datetime="<?php echo htmlspecialchars($rev['created_at'] ?? ''); ?>"><?php echo !empty($rev['created_at']) ? htmlspecialchars(date('M j, Y', strtotime($rev['created_at']))) : ''; ?></time>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <div class="pkg-reviews-stats">
                    <div><span class="pkg-stat-num"><?php echo $pr_count > 0 ? (int) $pr_count : '0'; ?>+</span><span>Reviews</span></div>
                    <div><span class="pkg-stat-num"><?php echo $pr_count > 0 ? (int) $pr_satisfaction . '%' : '0%'; ?></span><span>Satisfaction</span></div>
                    <div><span class="pkg-stat-num">24/7</span><span>Support</span></div>
                </div>
            </section>

            <p class="pkg-contact-note">For more information, contact us: <a href="tel:+94112345678">+94 11 234 5678</a></p>
        </div>
    </main>

    <div class="pkg-detail-actions">
        <a href="/CeylonGo/public/tourist/booking-form?package=<?php echo (int)$p['id']; ?>" class="pkg-btn pkg-btn--primary">Book Now</a>
        <a href="/CeylonGo/public/tourist/packages" class="pkg-btn pkg-btn--outline">Back to Packages</a>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script>
    (function() {
        var cards = document.querySelectorAll('.pkg-day-card');
        cards.forEach(function(card) {
            var header = card.querySelector('.pkg-day-header');
            var body = card.querySelector('.pkg-day-body');
            var toggle = card.querySelector('.pkg-day-toggle');
            if (!header || !body) return;
            header.addEventListener('click', function() {
                var isOpen = card.classList.contains('pkg-day-card--open');
                cards.forEach(function(c) {
                    c.classList.remove('pkg-day-card--open');
                    var t = c.querySelector('.pkg-day-toggle');
                    if (t) t.textContent = '▼';
                });
                if (!isOpen) {
                    card.classList.add('pkg-day-card--open');
                    if (toggle) toggle.textContent = '▲';
                }
            });
            if (toggle) toggle.textContent = card.classList.contains('pkg-day-card--open') ? '▲' : '▼';
        });
    })();
    </script>
</body>
</html>
