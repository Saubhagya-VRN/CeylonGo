<?php
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /CeylonGo/public/login");
        exit();
    }

    $selectedRating = $selectedRating ?? 'all';
    $selectedPkgRating = $selectedPkgRating ?? 'all';
    $reviews = $reviews ?? [];
    $packageReviews = $packageReviews ?? [];
    $metrics = $metrics ?? ['total' => 0, 'pending' => 0, 'average' => 0];
    $packageMetrics = $packageMetrics ?? ['total' => 0, 'pending' => 0, 'average' => 0];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/reviews.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">
        <title>Reviews Management</title>
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
                    <li><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
                    <li class="active"><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="reviews-management">
                    <h2 class="page-title">Reviews Management</h2>

                    <h4 class="page-title" style="font-size:16px;">Customized Trip Reviews</h4>

                    <form method="GET" action="/CeylonGo/public/admin/reviews">
                        <input type="hidden" name="pkg_rating" value="<?= htmlspecialchars((string) $selectedPkgRating) ?>">
                        <div class="toolbar">
                            <div class="filter-buttons">
                                <button type="submit" name="rating" value="all"
                                    class="filter-btn <?= ($selectedRating === 'all') ? 'active' : '' ?>">
                                    All
                                </button>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <button type="submit" name="rating" value="<?= $i ?>"
                                        class="filter-btn <?= ($selectedRating == (string) $i) ? 'active' : '' ?>">
                                        <?= $i ?> ⭐
                                    </button>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </form>

                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Average Rating</strong><br>
                                <span><?= number_format((float) ($metrics['average'] ?? 0), 1) ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Total Reviews</strong><br>
                                <span><?= (int) ($metrics['total'] ?? 0) ?></span>
                                <?php if (($metrics['pending'] ?? 0) > 0): ?>
                                    <span style="color:orange;font-size:12px;">
                                        (<?= (int) $metrics['pending'] ?> pending)
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="users-section">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                            <div class="filter-buttons" style="align-items:center;">
                                <span style="font-size:14px;">Show</span>
                                <select id="customRowsPerPage" class="filter-btn small-btn">
                                    <option value="10" selected>10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <span style="font-size:14px;">entries</span>
                            </div>
                            <div id="customPaginationControls" class="filter-buttons"></div>
                        </div>
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>User Name</th>
                                    <th>Destination</th>
                                    <th>Comment</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                    <th>Admin Reply</th>
                                </tr>
                            </thead>
                            <tbody id="customReviewTableBody">
                                <?php if (count($reviews) > 0): ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <tr data-id="<?= (int) $review['id'] ?>"
                                            data-kind="custom"
                                            data-reply="<?= htmlspecialchars($review['admin_reply'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            data-created-at="<?= htmlspecialchars(substr($review['created_at'] ?? '', 0, 10)) ?>">
                                            <td><?= htmlspecialchars((string) $review['user_id']) ?></td>
                                            <td><?= htmlspecialchars($review['tourist_name'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($review['destination'] ?? '') ?: '—' ?></td>
                                            <td><?= htmlspecialchars($review['review_text'] ?? '') ?></td>
                                            <td>
                                                <?php
                                                    $ratingVal = (int) ($review['rating'] ?? 0);
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        echo $i <= $ratingVal ? '⭐' : '☆';
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $st = strtolower((string) ($review['status'] ?? ''));
                                                    $hasReply = trim((string) ($review['admin_reply'] ?? '')) !== '';
                                                    if ($st === 'pending' && !$hasReply) {
                                                        echo '<span style="color:orange;font-weight:bold">Pending</span>';
                                                    } else {
                                                        echo '<span style="color:#198754;font-weight:bold">Replied</span>';
                                                    }
                                                ?>
                                            </td>
                                            <td class="actions">
                                                <?php if (($review['status'] ?? '') === 'pending'): ?>
                                                    <button type="button" class="icon-btn approve-btn" title="Approve">✅</button>
                                                <?php endif; ?>
                                                <button type="button" class="icon-btn reply-btn" title="Comment">💬</button>
                                                <button type="button" class="icon-btn danger delete-btn" title="Delete">🗑️</button>
                                            </td>
                                            <td>
                                                <?php if (!empty($review['admin_reply'])): ?>
                                                    <?= htmlspecialchars($review['admin_reply']) ?>
                                                <?php else: ?>
                                                    <span style="color:#aaa;font-style:italic;">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center;">No customized reviews found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer-buttons" style="margin-top: 24px;">
                        <a href="/CeylonGo/public/admin/reviews/export?rating=<?= rawurlencode((string) $selectedRating) ?>"
                           class="report-link-btn">Download Customized Review Report
                        </a>
                    </div>

                    <br><br>
                    <h4 class="page-title" style="font-size:16px;">Package Reviews</h4>

                    <form method="GET" action="/CeylonGo/public/admin/reviews">
                        <input type="hidden" name="rating" value="<?= htmlspecialchars((string) $selectedRating) ?>">
                        <div class="toolbar">
                            <div class="filter-buttons">
                                <button type="submit" name="pkg_rating" value="all"
                                    class="filter-btn <?= ($selectedPkgRating === 'all') ? 'active' : '' ?>">
                                    All
                                </button>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <button type="submit" name="pkg_rating" value="<?= $i ?>"
                                        class="filter-btn <?= ($selectedPkgRating == (string) $i) ? 'active' : '' ?>">
                                        <?= $i ?> ⭐
                                    </button>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </form>

                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Average Rating</strong><br>
                                <span><?= number_format((float) ($packageMetrics['average'] ?? 0), 1) ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Total Reviews</strong><br>
                                <span><?= (int) ($packageMetrics['total'] ?? 0) ?></span>
                                <?php if (($packageMetrics['pending'] ?? 0) > 0): ?>
                                    <span style="color:orange;font-size:12px;">
                                        (<?= (int) $packageMetrics['pending'] ?> pending)
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="users-section">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                            <div class="filter-buttons" style="align-items:center;">
                                <span style="font-size:14px;">Show</span>
                                <select id="pkgReviewRowsPerPage" class="filter-btn small-btn">
                                    <option value="10" selected>10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <span style="font-size:14px;">entries</span>
                            </div>
                            <div id="pkgReviewPaginationControls" class="filter-buttons"></div>
                        </div>
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>User Name</th>
                                    <th>Package</th>
                                    <th>Comment</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                    <th>Admin Reply</th>
                                </tr>
                            </thead>
                            <tbody id="packageReviewTableBody">
                                <?php if (count($packageReviews) > 0): ?>
                                    <?php foreach ($packageReviews as $packageReview): ?>
                                        <tr data-id="<?= (int) $packageReview['id'] ?>"
                                            data-kind="package"
                                            data-reply="<?= htmlspecialchars($packageReview['admin_reply'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            data-created-at="<?= htmlspecialchars(substr($packageReview['created_at'] ?? '', 0, 10)) ?>">
                                            <td><?= htmlspecialchars((string) ($packageReview['user_id'] ?? '')) ?></td>
                                            <td><?= htmlspecialchars($packageReview['tourist_name'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($packageReview['destination'] ?? '') ?: '—' ?></td>
                                            <td><?= htmlspecialchars($packageReview['review_text'] ?? '') ?></td>
                                            <td>
                                                <?php
                                                    $pkgRatingVal = (int) ($packageReview['rating'] ?? 0);
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        echo $i <= $pkgRatingVal ? '⭐' : '☆';
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $pkgStatus = strtolower((string) ($packageReview['status'] ?? ''));
                                                    $pkgHasReply = trim((string) ($packageReview['admin_reply'] ?? '')) !== '';
                                                    if ($pkgStatus === 'pending' && !$pkgHasReply) {
                                                        echo '<span style="color:orange;font-weight:bold">Pending</span>';
                                                    } else {
                                                        echo '<span style="color:#198754;font-weight:bold">Replied</span>';
                                                    }
                                                ?>
                                            </td>
                                            <td class="actions">
                                                <?php if (($packageReview['status'] ?? '') === 'pending'): ?>
                                                    <button type="button" class="icon-btn pkg-approve-btn" title="Approve">✅</button>
                                                <?php endif; ?>
                                                <button type="button" class="icon-btn pkg-reply-btn" title="Comment">💬</button>
                                                <button type="button" class="icon-btn danger pkg-delete-btn" title="Delete">🗑️</button>
                                            </td>
                                            <td>
                                                <?php if (!empty($packageReview['admin_reply'])): ?>
                                                    <?= htmlspecialchars($packageReview['admin_reply']) ?>
                                                <?php else: ?>
                                                    <span style="color:#aaa;font-style:italic;">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center;">No package reviews found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer-buttons" style="margin-top: 24px;">
                        <a href="/CeylonGo/public/admin/reviews/export-package?pkg_rating=<?= rawurlencode((string) $selectedPkgRating) ?>"
                           class="report-link-btn">Download Package Review Report
                        </a>
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

            const ENDPOINTS = {
                custom: {
                    delete: '/CeylonGo/public/admin/review/delete',
                    reply: '/CeylonGo/public/admin/review/reply',
                    approve: '/CeylonGo/public/admin/review/approve'
                },
                package: {
                    delete: '/CeylonGo/public/admin/package-review/delete',
                    reply: '/CeylonGo/public/admin/package-review/reply',
                    approve: '/CeylonGo/public/admin/package-review/approve'
                }
            };

            function wireReviewActions(tbodyId, kind) {
                const tbody = document.getElementById(tbodyId);
                if (!tbody) return;

                tbody.addEventListener('click', function(e) {
                    const button = e.target.closest('button');
                    if (!button) return;

                    const row = button.closest('tr');
                    if (!row || !row.dataset.id) return;

                    const reviewId = row.dataset.id;
                    const ep = ENDPOINTS[kind];

                    if (button.classList.contains('delete-btn') || button.classList.contains('pkg-delete-btn')) {
                        if (!confirm('Permanently delete this review?')) return;

                        fetch(ep.delete, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'review_id=' + encodeURIComponent(reviewId)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                row.remove();
                            } else {
                                alert('Failed to delete review');
                            }
                        });
                        return;
                    }

                    if (button.classList.contains('reply-btn') || button.classList.contains('pkg-reply-btn')) {
                        const existingReply = row.dataset.reply || '';
                        const reply = prompt('Enter admin reply:', existingReply);
                        if (reply === null || reply.trim() === '') return;

                        fetch(ep.reply, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'review_id=' + encodeURIComponent(reviewId) + '&reply=' + encodeURIComponent(reply)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                row.dataset.reply = reply;
                                const replyCell = row.cells[row.cells.length - 1];
                                replyCell.textContent = reply;
                                alert('Reply saved ✅');
                            } else {
                                alert('Failed to save reply ❌');
                            }
                        });
                        return;
                    }

                    if (button.classList.contains('approve-btn') || button.classList.contains('pkg-approve-btn')) {
                        if (!confirm('Approve this review?')) return;

                        fetch(ep.approve, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'review_id=' + encodeURIComponent(reviewId)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Failed to approve review. Check server logs.');
                            }
                        })
                        .catch(() => alert('Server error while approving.'));
                    }
                });
            }

            wireReviewActions('customReviewTableBody', 'custom');
            wireReviewActions('packageReviewTableBody', 'package');

            function initReviewPagination(tbodyId, rowsSelId, pagId, prevFnName, nextFnName) {
                const tbody = document.getElementById(tbodyId);
                const rowsPerPageSelect = document.getElementById(rowsSelId);
                const paginationControls = document.getElementById(pagId);
                if (!tbody || !rowsPerPageSelect || !paginationControls) return;

                const allRows = Array.from(tbody.querySelectorAll('tr'));

                if (allRows.length === 1 && allRows[0].children.length === 1) {
                    paginationControls.innerHTML = '';
                    return;
                }

                let currentPage = 1;
                let rowsPerPage = parseInt(rowsPerPageSelect.value, 10);

                function renderPagination(totalPages) {
                    paginationControls.innerHTML = `
                        <button type="button" class="filter-btn small-btn" ${currentPage === 1 ? 'disabled' : ''}>Prev</button>
                        <span class="page-info">Page ${currentPage} of ${totalPages}</span>
                        <button type="button" class="filter-btn small-btn" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>
                    `;
                    const btns = paginationControls.querySelectorAll('button.filter-btn.small-btn');
                    if (btns[0] && currentPage > 1) {
                        btns[0].addEventListener('click', function() { window[prevFnName](); });
                    }
                    if (btns[1] && currentPage < totalPages) {
                        btns[1].addEventListener('click', function() { window[nextFnName](); });
                    }
                }

                function renderTable() {
                    const totalPages = Math.max(1, Math.ceil(allRows.length / rowsPerPage));
                    if (currentPage > totalPages) currentPage = totalPages;
                    if (currentPage < 1) currentPage = 1;

                    tbody.innerHTML = '';
                    const start = (currentPage - 1) * rowsPerPage;
                    allRows.slice(start, start + rowsPerPage).forEach(function(row) {
                        tbody.appendChild(row);
                    });
                    renderPagination(totalPages);
                }

                window[nextFnName] = function() {
                    const totalPages = Math.max(1, Math.ceil(allRows.length / rowsPerPage));
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderTable();
                    }
                };
                window[prevFnName] = function() {
                    if (currentPage > 1) {
                        currentPage--;
                        renderTable();
                    }
                };

                rowsPerPageSelect.addEventListener('change', function() {
                    rowsPerPage = parseInt(this.value, 10);
                    currentPage = 1;
                    renderTable();
                });

                renderTable();
            }

            initReviewPagination(
                'customReviewTableBody',
                'customRowsPerPage',
                'customPaginationControls',
                'customRevPrevPage',
                'customRevNextPage'
            );
            initReviewPagination(
                'packageReviewTableBody',
                'pkgReviewRowsPerPage',
                'pkgReviewPaginationControls',
                'pkgRevPrevPage',
                'pkgRevNextPage'
            );
        </script>
    </body>
</html>
