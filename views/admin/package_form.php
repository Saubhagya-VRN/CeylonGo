<?php
    // views/admin/package_form.php  —  used for both Add (mode=create) and Edit (mode=edit)
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /CeylonGo/public/login");
        exit();
    }

    $mode    = $mode    ?? 'create';
    $p       = $package ?? null;
    $error   = $error   ?? null;
    $isEdit  = ($mode === 'edit');
    $formAction = $isEdit ? '/CeylonGo/public/admin/packages/update' : '/CeylonGo/public/admin/packages/create';
    $pageTitle  = $isEdit ? 'Edit Package' : 'Add New Package';

    function pv($p, $key, $default = '') {
        if ($p === null) return $default;
        return isset($p[$key]) ? $p[$key] : $default;
    }
    function arrToLines($arr) {
        if (!is_array($arr)) return '';
        return implode("\n", $arr);
    }

    $highlights    = is_array(pv($p,'highlights'))    ? pv($p,'highlights')    : [];
    $itinerary     = is_array(pv($p,'itinerary'))     ? pv($p,'itinerary')     : [];
    $accommodation = is_array(pv($p,'accommodation')) ? pv($p,'accommodation') : [];

    if (empty($highlights))    $highlights    = [['icon'=>'','title'=>'','desc'=>'']];
    if (empty($itinerary))     $itinerary     = [['day'=>1,'title'=>'','activities'=>[]]];
    if (empty($accommodation)) $accommodation = [['nights'=>'','location'=>'','hotel'=>'']];

    $validCategories = ['cultural','honeymoon','solo','adventure','heritage','safari','family','beach'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/admin/packages.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/transport/responsive.css">
        <title><?= $pageTitle ?>Manage Packages - Add Package</title>
    </head>

    <body>
        <header class="navbar">
            <div class="branding">
                <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
                <div class="logo-text">Ceylon Go</div>
            </div>
            <nav class="nav-links">
                <a href="/CeylonGo/public/admin/dashboard">Home</a>
                <div class="profile-dropdown">
                    <img src="/CeylonGo/public/images/profile.jpg" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
                    <div class="profile-dropdown-menu" id="profileDropdown">
                        <a href="/CeylonGo/public/admin/profile"><i class="fa-regular fa-user"></i> My Profile</a>
                        <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            </nav>
        </header>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <div class="page-wrapper">
            <div class="sidebar">
                <ul>
                    <li><a href="/CeylonGo/public/admin/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
                    <li><a href="/CeylonGo/public/admin/users"><i class="fa-solid fa-users"></i> Users</a></li>
                    <li><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
                    <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
                    <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
                    <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                    <li class="active"><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-box-open"></i> Packages</a></li>
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="user-management">
                    <h2 class="page-title"><?= $pageTitle ?></h2>
                    <?php if ($error): ?>
                    <div class="pkg-alert pkg-alert--error"><?= $error ?></div>
                    <?php endif; ?>
                    <div class="form-section">
                        <form method="POST" action="<?= $formAction ?>">
                            <?php if ($isEdit): ?>
                                <input type="hidden" name="id" value="<?= (int)pv($p,'id') ?>">
                            <?php endif; ?>

                            <h4 class="section-heading">Basic Information</h4>

                            <div class="form-group">
                                <label>Title *</label>
                                <input type="text" name="title" value="<?= htmlspecialchars(pv($p,'title')) ?>" required placeholder="e.g. Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla">
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label>Primary Location *</label>
                                    <input type="text" name="location" value="<?= htmlspecialchars(pv($p,'location')) ?>" required placeholder="e.g. Kandy">
                                </div>
                                <div class="form-group">
                                    <label>All Locations (comma separated)</label>
                                    <input type="text" name="locations" value="<?= htmlspecialchars(pv($p,'locations')) ?>" placeholder="e.g. Kandy, Sigiriya, Dambulla">
                                </div>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label>Duration</label>
                                    <input type="text" name="duration" value="<?= htmlspecialchars(pv($p,'duration')) ?>" placeholder="e.g. 5 Days 4 Nights">
                                </div>
                                <div class="form-group">
                                    <label>Duration (short)</label>
                                    <input type="text" name="duration_short" value="<?= htmlspecialchars(pv($p,'duration_short')) ?>" placeholder="e.g. 5 Days / 4 Nights">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Image URL / Path</label>
                                <input type="text" name="image" value="<?= htmlspecialchars(pv($p,'image')) ?>" placeholder="e.g. /CeylonGo/public/images/kandy.jpeg">
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label>Category *</label>
                                    <select name="category" required>
                                        <option value="">— Select —</option>
                                        <?php foreach ($validCategories as $cat): ?>
                                        <option value="<?= $cat ?>" <?= (pv($p,'category') === $cat) ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Trending</label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="trending" value="1" <?= !empty(pv($p,'trending')) ? 'checked' : '' ?>> Mark as Trending
                                    </label>
                                </div>
                            </div>

                            <h4 class="section-heading">Pricing</h4>

                            <div class="form-row-3">
                                <div class="form-group">
                                    <label>Price (LKR) *</label>
                                    <input type="number" name="price" value="<?= htmlspecialchars(pv($p,'price',0)) ?>" min="1" required placeholder="e.g. 125000">
                                </div>
                                <div class="form-group">
                                    <label>Child Price Ratio</label>
                                    <input type="number" name="price_child_ratio" value="<?= htmlspecialchars(pv($p,'price_child_ratio','0.50')) ?>" step="0.01" min="0" max="1" placeholder="0.50">
                                    <small class="field-hint">Child price = price × ratio</small>
                                </div>
                                <div class="form-group">
                                    <label>Infant Price Ratio</label>
                                    <input type="number" name="price_infant_ratio" value="<?= htmlspecialchars(pv($p,'price_infant_ratio','0.00')) ?>" step="0.01" min="0" max="1" placeholder="0.00">
                                    <small class="field-hint">0 = free, 1 = full price</small>
                                </div>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label>Rating</label>
                                    <input type="number" name="rating" value="<?= htmlspecialchars(pv($p,'rating','')) ?>" step="0.1" min="0" max="5" placeholder="e.g. 4.5">
                                </div>
                                <div class="form-group">
                                    <label>Reviews Count</label>
                                    <input type="number" name="reviews" value="<?= htmlspecialchars(pv($p,'reviews',0)) ?>" min="0" placeholder="e.g. 203">
                                </div>
                            </div>

                            <h4 class="section-heading">Trip Overview</h4>
                            <div class="form-group">
                                <label>Overview Paragraphs</label>
                                <textarea name="overview" placeholder="One paragraph / sentence per line"><?= htmlspecialchars(arrToLines(pv($p,'overview',[]))) ?></textarea>
                                <small class="field-hint">Each line becomes one paragraph on the detail page.</small>
                            </div>

                            <h4 class="section-heading">Package Highlights</h4>
                            <small class="field-hint" style="display:block;margin-bottom:10px;">Each highlight needs an icon type, a title, and a short description.</small>
                            <div id="highlightGroup">
                                <?php foreach ($highlights as $i => $h): ?>
                                <div class="rep-row">
                                    <div class="rep-row-inner">
                                        <div class="form-group">
                                            <label>Icon</label>
                                            <select name="h_icon[]">
                                                <?php foreach (['hotel','transfer','sightseeing','meals','activities','support'] as $icon): ?>
                                                <option value="<?= $icon ?>" <?= (($h['icon'] ?? '') === $icon) ? 'selected' : '' ?>><?= ucfirst($icon) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="h_title[]" value="<?= htmlspecialchars($h['title'] ?? '') ?>" placeholder="e.g. Accommodation">
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <input type="text" name="h_desc[]" value="<?= htmlspecialchars($h['desc'] ?? '') ?>" placeholder="e.g. 4 nights stay">
                                        </div>
                                    </div>
                                    <button type="button" class="icon-btn danger" onclick="removeRow(this)" title="Remove">🗑️</button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="footer-buttons">
                                <button type="button" class="footer-btn" onclick="addHighlight()">+ Add Highlight</button>
                            </div>

                            <h4 class="section-heading">Itinerary</h4>
                            <small class="field-hint" style="display:block;margin-bottom:10px;">One activity per line in the Activities box.</small>
                            <div id="itinGroup">
                                <?php foreach ($itinerary as $i => $day): ?>
                                <div class="rep-row">
                                    <div class="rep-row-inner">
                                        <div class="form-group" style="flex:0 0 80px;">
                                            <label>Day #</label>
                                            <input type="number" name="it_day[]" value="<?= (int)($day['day'] ?? $i+1) ?>" min="1">
                                        </div>
                                        <div class="form-group">
                                            <label>Day Title</label>
                                            <input type="text" name="it_title[]" value="<?= htmlspecialchars($day['title'] ?? '') ?>" placeholder="e.g. Arrival – Colombo to Kandy">
                                        </div>
                                        <div class="form-group" style="flex:2">
                                            <label>Activities</label>
                                            <textarea name="it_activities[]" placeholder="One activity per line"><?= htmlspecialchars(arrToLines($day['activities'] ?? [])) ?></textarea>
                                        </div>
                                    </div>
                                    <button type="button" class="icon-btn danger" onclick="removeRow(this)" title="Remove">🗑️</button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="footer-buttons">
                                <button type="button" class="footer-btn" onclick="addItinDay()">+ Add Day</button>
                            </div>

                            <h4 class="section-heading">Accommodation</h4>
                            <div id="accGroup">
                                <?php foreach ($accommodation as $i => $acc): ?>
                                <div class="rep-row">
                                    <div class="rep-row-inner">
                                        <div class="form-group" style="flex:0 0 90px;">
                                            <label>Nights</label>
                                            <input type="number" name="acc_nights[]" value="<?= htmlspecialchars($acc['nights'] ?? '') ?>" min="1" placeholder="e.g. 3">
                                        </div>
                                        <div class="form-group">
                                            <label>Location</label>
                                            <input type="text" name="acc_location[]" value="<?= htmlspecialchars($acc['location'] ?? '') ?>" placeholder="e.g. Kandy">
                                        </div>
                                        <div class="form-group" style="flex:2">
                                            <label>Hotel Name</label>
                                            <input type="text" name="acc_hotel[]" value="<?= htmlspecialchars($acc['hotel'] ?? '') ?>" placeholder="e.g. Earl's Regent Hotel">
                                        </div>
                                    </div>
                                    <button type="button" class="icon-btn danger" onclick="removeRow(this)" title="Remove">🗑️</button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="footer-buttons">
                                <button type="button" class="footer-btn" onclick="addAccRow()">+ Add Accommodation</button>
                            </div>

                            <h4 class="section-heading">What's Included & Excluded</h4>
                            <div class="form-row-2">
                                <div class="form-group">
                                    <label>Included (one item per line)</label>
                                    <textarea name="included" rows="6" placeholder="e.g. Airport transfers&#10;4 nights accommodation&#10;Entrance fees"><?= htmlspecialchars(arrToLines(pv($p,'included',[]))) ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Excluded (one item per line)</label>
                                    <textarea name="excluded" rows="6" placeholder="e.g. International flights&#10;Personal expenses&#10;Travel insurance"><?= htmlspecialchars(arrToLines(pv($p,'excluded',[]))) ?></textarea>
                                </div>
                            </div>

                            <div class="footer-buttons">
                                <button type="submit" class="footer-btn black"><?= $isEdit ? 'Update Package' : 'Save Package' ?></button>
                                <a href="/CeylonGo/public/admin/packages" class="footer-btn">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <footer>
            <ul>
                <li><a href="/CeylonGo/public/admin/bookings">View All Bookings</a></li>
                <li><a href="/CeylonGo/public/admin/reports">Generate Reports</a></li>
                <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
            </ul>
        </footer>

        <script>
            function toggleProfileDropdown() {
                const dropdown = document.getElementById('profileDropdown');
                dropdown.classList.toggle('show');
            }
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('profileDropdown');
                const profilePic = document.querySelector('.profile-pic');
                if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
                    dropdown.classList.remove('show');
                }
            });

            function removeRow(btn) {
                var row = btn.closest('.rep-row');
                var group = row.parentElement;
                if (group.querySelectorAll('.rep-row').length > 1) {
                    row.remove();
                }
            }

            function addHighlight() {
                var group = document.getElementById('highlightGroup');
                var iconOptions = ['hotel','transfer','sightseeing','meals','activities','support']
                    .map(function(o){ return '<option value="'+o+'">'+o.charAt(0).toUpperCase()+o.slice(1)+'</option>'; })
                    .join('');
                group.insertAdjacentHTML('beforeend',
                    '<div class="rep-row">'
                    + '<div class="rep-row-inner">'
                    + '<div class="form-group"><label>Icon</label><select name="h_icon[]">'+iconOptions+'</select></div>'
                    + '<div class="form-group"><label>Title</label><input type="text" name="h_title[]" placeholder="e.g. Accommodation"></div>'
                    + '<div class="form-group"><label>Description</label><input type="text" name="h_desc[]" placeholder="e.g. 4 nights stay"></div>'
                    + '</div>'
                    + '<button type="button" class="icon-btn danger" onclick="removeRow(this)">🗑️</button>'
                    + '</div>');
            }

            function addItinDay() {
                var group = document.getElementById('itinGroup');
                var nextDay = group.querySelectorAll('.rep-row').length + 1;
                group.insertAdjacentHTML('beforeend',
                    '<div class="rep-row">'
                    + '<div class="rep-row-inner">'
                    + '<div class="form-group" style="flex:0 0 80px;"><label>Day #</label><input type="number" name="it_day[]" value="'+nextDay+'" min="1"></div>'
                    + '<div class="form-group"><label>Day Title</label><input type="text" name="it_title[]" placeholder="e.g. Departure"></div>'
                    + '<div class="form-group" style="flex:2"><label>Activities</label><textarea name="it_activities[]" placeholder="One activity per line"></textarea></div>'
                    + '</div>'
                    + '<button type="button" class="icon-btn danger" onclick="removeRow(this)">🗑️</button>'
                    + '</div>');
            }

            function addAccRow() {
                var group = document.getElementById('accGroup');
                group.insertAdjacentHTML('beforeend',
                    '<div class="rep-row">'
                    + '<div class="rep-row-inner">'
                    + '<div class="form-group" style="flex:0 0 90px;"><label>Nights</label><input type="number" name="acc_nights[]" min="1" placeholder="e.g. 2"></div>'
                    + '<div class="form-group"><label>Location</label><input type="text" name="acc_location[]" placeholder="e.g. Kandy"></div>'
                    + '<div class="form-group" style="flex:2"><label>Hotel Name</label><input type="text" name="acc_hotel[]" placeholder="e.g. Earl\'s Regent Hotel"></div>'
                    + '</div>'
                    + '<button type="button" class="icon-btn danger" onclick="removeRow(this)">🗑️</button>'
                    + '</div>');
            }
        </script>
    </body>
</html>