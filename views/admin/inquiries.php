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
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/inquiries.css">
        
        <!-- Shared Transport Layout -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">

        <!-- Responsive styles (always last) -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

        <title>Inquiry Management</title>
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
                    <li class="active"><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                    <li><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="inquiry-management">

                    <h2 class="page-title">Inquiry Management</h2>

                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Total Inquiries</strong><br>
                                <span><?= $stats['total'] ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Pending</strong><br>
                                <span><?= $stats['pending'] ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Replied</strong><br>
                                <span><?= $stats['replied'] ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>From Guests</strong><br>
                                <span><?= $stats['guest'] ?></span>
                            </div>
                        </div>
                    </div>
                    <br>

                    <form method="GET" action="/CeylonGo/public/admin/inquiries">
                        <div class="toolbar">
                            <div class="search-section">
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Search by customer"
                                    class="search-input"
                                    value="<?= htmlspecialchars($search ?? '') ?>"
                                >
                                <button type="submit" class="search-btn">🔍</button>
                            </div>
                            <div class="filter-buttons">
                                <button type="submit" name="status" value="all"
                                    class="filter-btn <?= ($selectedStatus === 'all') ? 'active' : '' ?>">
                                    All
                                </button>
                                <button type="submit" name="status" value="pending"
                                    class="filter-btn <?= ($selectedStatus === 'pending') ? 'active' : '' ?>">
                                    Pending
                                </button>
                                <button type="submit" name="status" value="replied"
                                    class="filter-btn <?= ($selectedStatus === 'replied') ? 'active' : '' ?>">
                                    Replied
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="inquiries-section">
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
                        <table class="inquiry-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                    <th>Admin Reply</th>
                                </tr>
                            </thead>
                            <tbody id="inquiryTableBody">
                                <?php if (count($inquiries) > 0): ?>
                                    <?php foreach ($inquiries as $inquiry): ?>
                                        <tr data-id="<?= $inquiry['id'] ?>"
                                            data-created-at="<?= !empty($inquiry['created_at']) ? htmlspecialchars(date('Y-m-d', strtotime($inquiry['created_at']))) : '' ?>">
                                            <td>
                                                <?php if (!empty($inquiry['tourist_name'])): ?>
                                                    <?= htmlspecialchars($inquiry['tourist_name']) ?>
                                                <?php elseif (!empty($inquiry['guest_name'])): ?>
                                                    <?= htmlspecialchars($inquiry['guest_name']) ?>
                                                    <br><small style="color:#888;"><?= htmlspecialchars($inquiry['guest_email']) ?></small>
                                                <?php else: ?>
                                                    <span style="color:#aaa;font-style:italic;">Unknown</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($inquiry['subject']) ?></td>
                                            <td><?= htmlspecialchars($inquiry['message']) ?></td>
                                            <td>
                                                <?php if ($inquiry['status'] === 'replied'): ?>
                                                    <span style="color:green;font-weight:bold;">Replied</span>
                                                <?php else: ?>
                                                    <span style="color:orange;font-weight:bold;">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('Y-m-d', strtotime($inquiry['created_at'])) ?></td>
                                            <td class="actions">
                                                <?php if ($inquiry['status'] === 'pending'): ?>
                                                    <button class="icon-btn reply-btn" title="Reply">💬</button>
                                                <?php endif; ?>
                                                <button class="icon-btn danger delete-btn" title="Delete">🗑️</button>
                                            </td>
                                            <td>
                                                <?php if (!empty($inquiry['admin_reply'])): ?>
                                                    <?= htmlspecialchars($inquiry['admin_reply']) ?>
                                                <?php else: ?>
                                                    <span style="color:#aaa;font-style:italic;">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center;">No inquiries found.</td>
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
                        <button class="footer-btn black" id="exportBtn">Export Inquiries</button>
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

            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('profileDropdown');
                const profilePic = document.querySelector('.profile-pic');
                if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
                    dropdown.classList.remove('show');
                }
            });

            document.getElementById("inquiryTableBody").addEventListener("click", function(e) {
                const button = e.target.closest("button");
                if (!button) return;

                const row = button.closest("tr");
                const inquiryId = row.dataset.id;

                // ── Delete inquiry ─────────────────────────────────
                if (button.classList.contains("delete-btn")) {
                    if (!confirm("Permanently delete this inquiry?")) return;

                    fetch("/CeylonGo/public/admin/inquiry/delete", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `inquiry_id=${inquiryId}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            row.remove();
                        } else {
                            alert("Failed to delete inquiry.");
                        }
                    });
                }

                // ── Reply to inquiry ──────────────────────────────
                if (button.classList.contains("reply-btn")) {
                    const existingReply = row.dataset.reply || "";
                    const reply = prompt("Enter admin reply:", existingReply);
                    if (reply === null || reply.trim() === "") return;

                    fetch("/CeylonGo/public/admin/inquiry/reply", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `inquiry_id=${inquiryId}&reply=${encodeURIComponent(reply)}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            row.dataset.reply = reply;
                            // Update reply cell and status cell
                            const replyCell = row.cells[row.cells.length - 1];
                            replyCell.innerHTML = reply;
                            const statusCell = row.cells[3];
                            statusCell.innerHTML = '<span style="color:green;font-weight:bold;">Replied</span>';
                            // Remove reply button since it's now replied
                            button.remove();
                            alert("Reply saved ✅");
                        } else {
                            alert("Failed to save reply ❌");
                        }
                    });
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

                    const rows = document.querySelectorAll("#inquiryTableBody tr");
                    if (rows.length === 0) return alert("No inquiries to export!");

                    let txt = "User\tSubject\tMessage\tStatus\tDate\n";
                    let count = 0;
                    rows.forEach(row => {
                        if (row.style.display !== "none" && inRange(row.dataset.createdAt, range)) {
                            const cells = [...row.cells];
                            const user    = cells[0].innerText.trim();
                            const subject = cells[1].innerText.trim();
                            const message = cells[2].innerText.trim();
                            const status  = cells[3].innerText.trim();
                            const date    = cells[4].innerText.trim();
                            txt += [user, subject, message, status, date].join("\t") + "\n";
                            count++;
                        }
                    });
                    if (count === 0) { alert("No inquiries in the selected period."); return; }

                    const blob = new Blob([txt], { type: "text/plain" });
                    const link = document.createElement("a");
                    const stamp = new Date().toISOString().slice(0, 10);
                    const tag = range.start && range.end ? `${range.start}_to_${range.end}` : "all_time";
                    link.download = `inquiries_${tag}_${stamp}.txt`;
                    link.href = URL.createObjectURL(blob);
                    link.click();
                });
            })();

            // ── PAGINATION FOR USERS ───────────────────

            // Get all rows initially rendered by PHP
            const allUserRows = Array.from(document.querySelectorAll("#inquiryTableBody tr"))
                .filter(row => row.children.length > 1); // ignore "no data" row

            const rowsPerPageSelect = document.getElementById("rowsPerPage");
            const paginationControls = document.getElementById("paginationControls");

            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value);

            // Render table based on page
            function renderTable() {
                const tbody = document.getElementById("inquiryTableBody");
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