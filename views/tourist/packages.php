<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$packages = $packages ?? [];
$package_count = count($packages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Packages - Ceylon Go</title>
    <link rel="stylesheet" href="/CeylonGo/public/css/common.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/footer.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/packages.css">
</head>
<body class="packages-page">
    <?php include __DIR__ . '/header.php'; ?>

    <section class="packages-hero">
        <h1>Explore Our Travel Packages</h1>
        <p>Discover unforgettable experiences tailored just for you!</p>
    </section>

    <div class="packages-layout">
        <aside class="packages-sidebar">
            <h2 class="sidebar-title">
                <svg class="sidebar-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filters
            </h2>

            <div class="sidebar-block">
                <label class="sidebar-label">
                    <svg class="sidebar-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    Sort By
                </label>
                <select class="sidebar-select" id="sortBy">
                    <option value="featured">Featured</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="duration">Duration</option>
                    <option value="rating">Rating</option>
                </select>
            </div>

            <div class="sidebar-block">
                <label class="sidebar-label">
                    <svg class="sidebar-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Search Destination
                </label>
                <div class="sidebar-search-wrap sidebar-search-wrap--suggest">
                    <svg class="sidebar-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" class="sidebar-search" placeholder="Search destinations..." id="searchDestination" autocomplete="off" name="destination_query" data-lpignore="true" data-form-type="other">
                    <div class="destination-suggestions" id="destinationSuggestions" role="listbox" aria-hidden="true"></div>
                </div>
            </div>

            <div class="sidebar-block">
                <label class="sidebar-label">Price Range</label>
                <input type="range" class="range-slider" id="priceMin" min="0" max="300000" value="0" step="10000">
                <input type="range" class="range-slider" id="priceMax" min="0" max="300000" value="300000" step="10000">
                <div class="range-labels"><span id="priceMinLabel">Price on request</span> – <span id="priceMaxLabel">Rs 300,000</span></div>
            </div>

            <div class="sidebar-block">
                <label class="sidebar-label">Categories</label>
                <div class="sidebar-categories">
                    <label class="sidebar-check"><input type="checkbox" name="cat" value="cultural"> Cultural</label>
                    <label class="sidebar-check"><input type="checkbox" name="cat" value="honeymoon"> Honeymoon</label>
                    <label class="sidebar-check"><input type="checkbox" name="cat" value="family"> Family</label>
                    <label class="sidebar-check"><input type="checkbox" name="cat" value="solo"> Solo</label>
                    <label class="sidebar-check"><input type="checkbox" name="cat" value="safari"> Safari</label>
                    <label class="sidebar-check"><input type="checkbox" name="cat" value="beach"> Beach</label>
                    <label class="sidebar-check"><input type="checkbox" name="cat" value="heritage"> Heritage</label>
                    <label class="sidebar-check"><input type="checkbox" name="cat" value="adventure"> Adventure</label>
                </div>
            </div>
        </aside>

        <main class="packages-main">
            <div class="packages-header">
                <div>
                    <h1 class="packages-title">Travel Packages</h1>
                    <p class="packages-count"><span id="packagesCount"><?php echo $package_count; ?></span> packages found</p>
                </div>
                <div class="view-toggle" role="group" aria-label="View mode">
                    <button type="button" class="view-btn view-list active" id="viewList" aria-pressed="true" title="List view">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    </button>
                    <button type="button" class="view-btn view-grid" id="viewGrid" aria-pressed="false" title="Grid view">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </button>
                </div>
            </div>

            <div class="packages-list" id="packagesList">
                <?php foreach ($packages as $idx => $p):
                    $nights = 0;
                    if (!empty($p['duration']) && preg_match('/(\d+)\s*[Nn]ight/', $p['duration'], $m)) {
                        $nights = (int)$m[1];
                    }
                    $locStr = isset($p['locations']) ? $p['locations'] : $p['location'];
                ?>
                <article class="package-card" data-id="<?php echo (int)$p['id']; ?>" data-index="<?php echo $idx; ?>" data-location="<?php echo htmlspecialchars(strtolower($p['location'])); ?>" data-locations="<?php echo htmlspecialchars(strtolower($locStr)); ?>" data-category="<?php echo htmlspecialchars($p['category']); ?>" data-price="<?php echo (int)(isset($p['price']) ? $p['price'] : 0); ?>" data-rating="<?php echo htmlspecialchars((string)(isset($p['rating']) ? $p['rating'] : 0)); ?>" data-nights="<?php echo $nights; ?>">
                    <div class="package-card-image-wrap">
                        <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" class="package-card-image">
                        <?php if (!empty($p['trending'])): ?>
                        <span class="package-badge package-badge--trending">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 23c-4-2-7-6-7-11 0-5 4-9 7-9s7 4 7 9c0 5-3 9-7 11zm0-18c-2 0-4 2-4 5 0 3 2 6 4 8 2-2 4-5 4-8 0-3-2-5-4-5z"/></svg>
                            Trending
                        </span>
                        <?php endif; ?>
                        <div class="package-duration-overlay">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?php echo htmlspecialchars($p['duration']); ?>
                        </div>
                    </div>
                    <div class="package-card-body">
                        <div class="package-card-actions">
                            <span class="package-price">LKR <?php echo number_format(isset($p['price']) ? (int)$p['price'] : 0); ?></span>
                        </div>
                        <h3 class="package-card-title"><?php echo htmlspecialchars($p['title']); ?></h3>
                        <p class="package-card-location">
                            <?php
                            $locStr = $p['locations'] ?? $p['location'];
                            $locs = array_filter(array_map('trim', preg_split('/\s*,\s*/', $locStr)));
                            $pinSvg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
                            foreach ($locs as $loc):
                                if ($loc === '') continue;
                            ?><span class="package-location-item"><?php echo $pinSvg; ?><?php echo htmlspecialchars($loc); ?></span><?php endforeach; ?>
                        </p>
                        <ul class="package-features">
                            <?php if (!empty($p['meals'])): ?><li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg> Meals</li><?php endif; ?>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> Standard</li>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9-.6 1.1-.9 2.4-.9 3.6v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg> Cab Facility</li>
                        </ul>
                        <div class="package-rating">
                            <svg class="star" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <strong><?php echo htmlspecialchars($p['rating']); ?></strong>
                            <span class="package-reviews">(<?php echo (int)$p['reviews']; ?> reviews)</span>
                        </div>
                        <div class="package-card-cta">
                            <a href="/CeylonGo/public/contact" class="package-link">Contact for best rates</a>
                            <a href="/CeylonGo/public/tourist/booking-form?package=<?php echo (int)$p['id']; ?>" class="btn btn-outline-pkg">Book Now</a>
                            <a href="/CeylonGo/public/tourist/package-details/<?php echo (int)$p['id']; ?>" class="btn btn-primary-pkg">View Details</a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script>
    (function() {
        var list = document.getElementById('packagesList');
        var countEl = document.getElementById('packagesCount');
        var sortBy = document.getElementById('sortBy');
        var searchDest = document.getElementById('searchDestination');
        var priceMin = document.getElementById('priceMin');
        var priceMax = document.getElementById('priceMax');
        var priceMinL = document.getElementById('priceMinLabel');
        var priceMaxL = document.getElementById('priceMaxLabel');
        var catCheckboxes = document.querySelectorAll('input[name="cat"]');

        function formatPrice(v) {
            if (!v || v === 0) return 'Price on request';
            return 'Rs ' + Number(v).toLocaleString();
        }

        function updatePriceLabels() {
            if (priceMinL) priceMinL.textContent = formatPrice(parseInt(priceMin.value, 10));
            if (priceMaxL) priceMaxL.textContent = formatPrice(parseInt(priceMax.value, 10));
        }
        if (priceMin && priceMax) {
            priceMin.addEventListener('input', function() {
                var a = parseInt(priceMin.value, 10), b = parseInt(priceMax.value, 10);
                if (a > b) priceMax.value = a;
                updatePriceLabels();
                applyFilters();
            });
            priceMax.addEventListener('input', function() {
                var b = parseInt(priceMax.value, 10), a = parseInt(priceMin.value, 10);
                if (b < a) priceMin.value = b;
                updatePriceLabels();
                applyFilters();
            });
        }
        updatePriceLabels();

        function applyFilters() {
            if (!list || !list.children.length) return;
            var search = (searchDest && searchDest.value) ? searchDest.value.trim().toLowerCase() : '';
            var pMin = priceMin ? parseInt(priceMin.value, 10) : 0;
            var pMax = priceMax ? parseInt(priceMax.value, 10) : 999999999;
            var cats = [];
            if (catCheckboxes.length) {
                for (var i = 0; i < catCheckboxes.length; i++) {
                    if (catCheckboxes[i].checked) cats.push(catCheckboxes[i].value);
                }
            }
            var sort = sortBy ? sortBy.value : 'featured';

            var cards = Array.prototype.slice.call(list.querySelectorAll('.package-card'));
            var visible = cards.filter(function(card) {
                var locs = (card.getAttribute('data-locations') || '') + ' ' + (card.getAttribute('data-location') || '');
                var title = (card.querySelector('.package-card-title') && card.querySelector('.package-card-title').textContent) || '';
                if (search && locs.indexOf(search) === -1 && title.toLowerCase().indexOf(search) === -1) return false;
                var price = parseInt(card.getAttribute('data-price'), 10) || 0;
                if (price < pMin || (pMax > 0 && price > pMax)) return false;
                if (cats.length) {
                    var cat = card.getAttribute('data-category') || '';
                    if (cats.indexOf(cat) === -1) return false;
                }
                return true;
            });

            visible.sort(function(a, b) {
                if (sort === 'featured') {
                    return (parseInt(a.getAttribute('data-index'), 10) || 0) - (parseInt(b.getAttribute('data-index'), 10) || 0);
                }
                if (sort === 'price-low') {
                    return (parseInt(a.getAttribute('data-price'), 10) || 0) - (parseInt(b.getAttribute('data-price'), 10) || 0);
                }
                if (sort === 'price-high') {
                    return (parseInt(b.getAttribute('data-price'), 10) || 0) - (parseInt(a.getAttribute('data-price'), 10) || 0);
                }
                if (sort === 'duration') {
                    return (parseInt(a.getAttribute('data-nights'), 10) || 0) - (parseInt(b.getAttribute('data-nights'), 10) || 0);
                }
                if (sort === 'rating') {
                    return (parseFloat(b.getAttribute('data-rating')) || 0) - (parseFloat(a.getAttribute('data-rating')) || 0);
                }
                return 0;
            });

            cards.forEach(function(card) {
                var show = visible.indexOf(card) !== -1;
                card.style.display = show ? '' : 'none';
            });
            visible.forEach(function(card) {
                list.appendChild(card);
            });
            if (countEl) countEl.textContent = visible.length;
        }

        var suggestionList = document.getElementById('destinationSuggestions');
        var destinationNames = [];
        if (list) {
            var cards = list.querySelectorAll('.package-card');
            var set = {};
            for (var c = 0; c < cards.length; c++) {
                var locs = (cards[c].getAttribute('data-locations') || '').split(',');
                for (var L = 0; L < locs.length; L++) {
                    var name = locs[L].trim();
                    if (name && !set[name]) {
                        set[name] = true;
                        destinationNames.push(name);
                    }
                }
            }
            destinationNames.sort();
        }
        function showSuggestions(query) {
            if (!suggestionList || !searchDest) return;
            query = (query || '').trim().toLowerCase();
            suggestionList.innerHTML = '';
            suggestionList.setAttribute('aria-hidden', 'true');
            if (query.length < 1) {
                suggestionList.classList.remove('destination-suggestions--open');
                return;
            }
            var matches = [];
            for (var i = 0; i < destinationNames.length; i++) {
                if (destinationNames[i].toLowerCase().indexOf(query) !== -1) matches.push(destinationNames[i]);
            }
            if (matches.length === 0) {
                suggestionList.classList.remove('destination-suggestions--open');
                return;
            }
            function toTitleCase(s) {
                return (s || '').replace(/\w\S*/g, function(w) { return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase(); });
            }
            for (var j = 0; j < Math.min(matches.length, 10); j++) {
                var item = document.createElement('div');
                item.className = 'destination-suggestions-item';
                item.setAttribute('role', 'option');
                item.textContent = toTitleCase(matches[j]);
                (function(place) {
                    item.addEventListener('click', function() {
                        searchDest.value = toTitleCase(place);
                        suggestionList.classList.remove('destination-suggestions--open');
                        suggestionList.innerHTML = '';
                        suggestionList.setAttribute('aria-hidden', 'true');
                        applyFilters();
                    });
                })(matches[j]);
                suggestionList.appendChild(item);
            }
            suggestionList.classList.add('destination-suggestions--open');
            suggestionList.setAttribute('aria-hidden', 'false');
        }
        if (searchDest) {
            searchDest.addEventListener('input', function() {
                showSuggestions(searchDest.value);
                applyFilters();
            });
            searchDest.addEventListener('focus', function() {
                showSuggestions(searchDest.value);
            });
            searchDest.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    suggestionList.classList.remove('destination-suggestions--open');
                    suggestionList.innerHTML = '';
                }
            });
        }
        if (suggestionList) {
            document.addEventListener('click', function(e) {
                if (searchDest && suggestionList && !searchDest.contains(e.target) && !suggestionList.contains(e.target)) {
                    suggestionList.classList.remove('destination-suggestions--open');
                    suggestionList.innerHTML = '';
                }
            });
        }

        if (sortBy) sortBy.addEventListener('change', applyFilters);
        if (catCheckboxes.length) {
            for (var j = 0; j < catCheckboxes.length; j++) {
                catCheckboxes[j].addEventListener('change', applyFilters);
            }
        }

        var viewList = document.getElementById('viewList');
        var viewGrid = document.getElementById('viewGrid');
        if (viewList && viewGrid && list) {
            viewList.addEventListener('click', function() {
                viewList.classList.add('active');
                viewList.setAttribute('aria-pressed', 'true');
                viewGrid.classList.remove('active');
                viewGrid.setAttribute('aria-pressed', 'false');
                list.classList.remove('packages-list--grid');
            });
            viewGrid.addEventListener('click', function() {
                viewGrid.classList.add('active');
                viewGrid.setAttribute('aria-pressed', 'true');
                viewList.classList.remove('active');
                viewList.setAttribute('aria-pressed', 'false');
                list.classList.add('packages-list--grid');
            });
        }
    })();
    </script>
</body>
</html>
