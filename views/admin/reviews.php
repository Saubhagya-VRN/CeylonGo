<?php
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /CeylonGo/public/login");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- Font Awesome (REQUIRED) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Optional admin-only overrides -->
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/reviews.css">
        
        <!-- Shared Transport Layout -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">

        <!-- Responsive styles (always last) -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

        <title>Reviews Management</title>
    </head>

    <body>
        <!-- Navbar -->
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

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="page-wrapper">

            <!-- Sidebar -->
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
                    
                    <h4>Overall Ratings</h4><br>
                    <p class="sub-text">Service Performance Metrics</p>

                    <div class="footer-buttons">
                        <button class="footer-btn">
                            Average Rating:
                            <b><?= number_format($metrics['average'], 1) ?></b>
                        </button>

                        <button class="footer-btn">
                            Total Reviews:
                            <b><?= $metrics['total'] ?></b>
                            <?php if ($metrics['pending'] > 0): ?>
                                <span style="color:orange;font-size:12px;">
                                    (<?= $metrics['pending'] ?> pending)
                                </span>
                            <?php endif; ?>
                        </button>

                        <button class="footer-btn">
                            Positive Feedback:
                            <b><?= $metrics['positive_percentage'] ?>%</b>
                        </button>
                    </div>
                    <br>

                    <form method="GET" action="/CeylonGo/public/admin/reviews">
                        <div class="toolbar">
                            <div class="filter-buttons">
                                <button type="submit" name="rating" value="all"
                                    class="filter-btn <?= ($selectedRating=='all')?'active':'' ?>">
                                    All
                                </button>

                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <button type="submit" name="rating" value="<?= $i ?>"
                                        class="filter-btn <?= ($selectedRating==$i)?'active':'' ?>">
                                        <?= $i ?> ⭐
                                    </button>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </form>

                    <div class="users-section">
                        <table class="user-table">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                                
                                <!-- LEFT: Show entries -->
                                <div class="filter-buttons" style="align-items:center;">
                                    <span style="font-size:14px;">Show</span>

                                    <select id="rowsPerPage" class="filter-btn small-btn">
                                        <option value="10" selected>10</option>
                                        <option value="15">15</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>

                                    <span style="font-size:14px;">entries</span>
                                </div>

                                <!-- RIGHT: Pagination -->
                                <div id="paginationControls" class="filter-buttons"></div>

                            </div>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>User Name</th>
                                    <th>Comment</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                    <th>Admin Reply</th>
                                </tr>
                            </thead>
                            <tbody id="reviewTableBody">
                                <?php if(count($reviews) > 0): ?>
                                    <?php foreach($reviews as $review): ?>
                                        <tr data-id="<?= $review['id'] ?>"
                                            data-created-at="<?= htmlspecialchars(substr($review['created_at'] ?? '', 0, 10)) ?>">
                                            <td><?= htmlspecialchars($review['user_id']) ?></td>
                                            <td><?= htmlspecialchars($review['tourist_name']) ?></td>
                                            <td><?= htmlspecialchars($review['review_text']) ?></td>

                                            <!-- ⭐ Rating -->
                                            <td>
                                                <?php
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        echo $i <= $review['rating'] ? "⭐" : "☆";
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($review['status'] === 'approved'): ?>
                                                    <span style="color:green;font-weight:bold">Approved</span>
                                                <?php else: ?>
                                                    <span style="color:orange;font-weight:bold">Pending</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="actions">
                                                <?php if ($review['status'] === 'pending'): ?>
                                                    <button class="icon-btn approve-btn" title="Approve">✅</button>
                                                <?php endif; ?>
                                                <button class="icon-btn reply-btn" title="Comment">💬</button>
                                                <button class="icon-btn danger delete-btn" title="Delete">🗑️</button>
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
                                        <td colspan="6" style="text-align:center;">No reviews found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer-buttons" style="flex-direction:column;align-items:flex-start;gap:10px;">
                        <div class="export-timeline-toolbar" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            <label for="exportTimelinePreset">Report period:</label>
                            <select id="exportTimelinePreset" class="search-input" style="max-width:220px;padding:6px 8px;">
                                <option value="all">All time</option>
                                <option value="7d">Last 7 days</option>
                                <option value="30d">Last 30 days</option>
                                <option value="90d">Last 90 days</option>
                                <option value="ytd">Year to date</option>
                                <option value="custom">Custom range</option>
                            </select>
                            <span id="exportCustomRangeWrap" class="export-custom-date-range" style="display:none;align-items:center;gap:8px;flex-wrap:wrap;">
                                <span class="export-range-label">From</span>
                                <div class="date-filter"><input type="date" id="exportDateFrom" class="date-input"></div>
                                <span class="export-range-label">To</span>
                                <div class="date-filter"><input type="date" id="exportDateTo" class="date-input"></div>
                            </span>
                        </div>
                        <button class="footer-btn black" id="exportBtn">Export Reviews</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer -->
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

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('profileDropdown');
                const profilePic = document.querySelector('.profile-pic');
                
                if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
                dropdown.classList.remove('show');
                }
            });

            document.getElementById("reviewTableBody").addEventListener("click", function(e) {

                const button = e.target.closest("button");
                if (!button) return;

                const row = button.closest("tr");
                const reviewId = row.dataset.id;

                // ── Delete review ─────────────────────────────────
                if (button.classList.contains("delete-btn")) {
                    if (!confirm("Permanently delete this review?")) return;

                    fetch("/CeylonGo/public/admin/review/delete", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `review_id=${reviewId}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            row.remove();
                        } else {
                            alert("Failed to delete review");
                        }
                    });
                }

                // ── Reply to review ───────────────────────────────
                if (button.classList.contains("reply-btn")) {
                    const existingReply = row.dataset.reply || "";
                    const reply = prompt("Enter admin reply:", existingReply);
                    if (reply === null || reply.trim() === "") return;

                    fetch("/CeylonGo/public/admin/review/reply", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `review_id=${reviewId}&reply=${encodeURIComponent(reply)}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            row.dataset.reply = reply;
                            // Update the admin reply cell visually
                            const replyCell = row.cells[row.cells.length - 1];
                            replyCell.innerHTML = reply;
                            alert("Reply saved ✅");
                        } else {
                            alert("Failed to save reply ❌");
                        }
                    });
                }

                // ── Approve review ────────────────────────────────
                if (button.classList.contains("approve-btn")) {
                    if (!confirm("Approve this review?")) return;

                    fetch("/CeylonGo/public/admin/review/approve", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `review_id=${reviewId}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert("Failed to approve review. Check server logs.");
                        }
                    })
                    .catch(() => alert("Server error while approving."));
                }
            });

            (function() {
                const presetEl = document.getElementById("exportTimelinePreset");
                const wrap = document.getElementById("exportCustomRangeWrap");
                function pad(n) { return String(n).padStart(2, "0"); }
                function ymd(d) { return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate()); }
                function toggleCustom() {
                    if (!presetEl || !wrap) return;
                    wrap.style.display = presetEl.value === "custom" ? "inline-flex" : "none";
                }
                if (presetEl) { presetEl.addEventListener("change", toggleCustom); toggleCustom(); }

                function resolveExportRange() {
                    const v = presetEl ? presetEl.value : "all";
                    if (v === "custom") {
                        const f = document.getElementById("exportDateFrom").value;
                        const t = document.getElementById("exportDateTo").value;
                        if (!f || !t) { alert("Please select both From and To dates for a custom range."); return null; }
                        if (f > t) { alert("From date must be before or equal to To date."); return null; }
                        return { start: f, end: t };
                    }
                    if (v === "all") return { start: null, end: null };
                    const today = new Date();
                    const end = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                    let start = new Date(end);
                    if (v === "7d") start.setDate(start.getDate() - 6);
                    else if (v === "30d") start.setDate(start.getDate() - 29);
                    else if (v === "90d") start.setDate(start.getDate() - 89);
                    else if (v === "ytd") start = new Date(today.getFullYear(), 0, 1);
                    else return { start: null, end: null };
                    return { start: ymd(start), end: ymd(end) };
                }
                function inRange(dateStr, range) {
                    if (!range || (!range.start && !range.end)) return true;
                    const d = (dateStr && String(dateStr).trim().slice(0, 10)) || "";
                    if (!d) return false;
                    if (range.start && d < range.start) return false;
                    if (range.end && d > range.end) return false;
                    return true;
                }

                document.getElementById("exportBtn").addEventListener("click", () => {
                    const range = resolveExportRange();
                    if (range === null) return;

                    const rows = document.querySelectorAll("#reviewTableBody tr");
                    if (rows.length === 0) return alert("No reviews to export!");

                    let txt = "User ID\tUser Name\tComment\tRating\tStatus\tReview date\n";
                    let count = 0;
                    rows.forEach(row => {
                        if (row.style.display !== "none" && inRange(row.dataset.createdAt, range)) {
                            const cells = [...row.cells];
                            const userId = cells[0].innerText.trim();
                            const userName = cells[1].innerText.trim();
                            const comment = cells[2].innerText.trim();
                            const rating = cells[3].innerText.trim();
                            const status = cells[4].innerText.trim();
                            const revDate = row.dataset.createdAt || "—";
                            txt += [userId, userName, comment, rating, status, revDate].join("\t") + "\n";
                            count++;
                        }
                    });
                    if (count === 0) { alert("No reviews in the selected period."); return; }

                    const blob = new Blob([txt], { type: "text/plain" });
                    const link = document.createElement("a");
                    const stamp = new Date().toISOString().slice(0, 10);
                    const tag = range.start && range.end ? `${range.start}_to_${range.end}` : "all_time";
                    link.download = `reviews_${tag}_${stamp}.txt`;
                    link.href = URL.createObjectURL(blob);
                    link.click();
                });
            })();

            // ── PAGINATION FOR USERS ───────────────────

            // Get all rows initially rendered by PHP
            const allUserRows = Array.from(document.querySelectorAll("#reviewTableBody tr"))
                .filter(row => row.children.length > 1); // ignore "no data" row

            const rowsPerPageSelect = document.getElementById("rowsPerPage");
            const paginationControls = document.getElementById("paginationControls");

            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value);

            // Render table based on page
            function renderTable() {
                const tbody = document.getElementById("reviewTableBody");
                tbody.innerHTML = "";

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                const pageRows = allUserRows.slice(start, end);

                pageRows.forEach(row => tbody.appendChild(row));

                renderPagination();
            }

            // Pagination buttons
            function renderPagination() {
                const totalPages = Math.ceil(allUserRows.length / rowsPerPage);

                paginationControls.innerHTML = `
                    <button class="filter-btn small-btn" ${currentPage === 1 ? "disabled" : ""} onclick="prevPage()">Prev</button>

                    <span class="page-info">
                        Page ${currentPage} of ${totalPages}
                    </span>

                    <button class="filter-btn small-btn" ${currentPage === totalPages ? "disabled" : ""} onclick="nextPage()">Next</button>
                `;
            }

            // Navigation
            function nextPage() {
                const totalPages = Math.ceil(allUserRows.length / rowsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            }

            function prevPage() {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            }

            // Change rows per page
            rowsPerPageSelect.addEventListener("change", function() {
                rowsPerPage = parseInt(this.value);
                currentPage = 1;
                renderTable();
            });

            // Initialize
            renderTable();
        </script>
    </body>
</html>