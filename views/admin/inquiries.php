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
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="inquiry-management">

                    <h2 class="page-title">Inquiry Management</h2>

                    <div class="stats-section">
                        <h4>Inquiry Statistics</h4><br>
                        <p class="sub-text">Overview of all submitted inquiries</p><br>
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
                                    placeholder="Search by user"
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
                        <table class="inquiry-table">
                            <thead>
                                <tr>
                                    <th>User</th>
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
                                        <tr data-id="<?= $inquiry['id'] ?>">
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
                    <br>

                    <div class="footer-buttons">
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

            // Export Inquiries
            document.getElementById("exportBtn").addEventListener("click", () => {
                const rows = document.querySelectorAll("#inquiryTableBody tr");
                if (rows.length === 0) return alert("No inquiries to export!");

                let txt = "User\tSubject\tMessage\tStatus\tDate\n";

                rows.forEach(row => {
                    if (row.style.display !== "none") {
                        const cells = [...row.cells];
                        const user    = cells[0].innerText.trim();
                        const subject = cells[1].innerText.trim();
                        const message = cells[2].innerText.trim();
                        const status  = cells[3].innerText.trim();
                        const date    = cells[4].innerText.trim();

                        txt += [user, subject, message, status, date].join("\t") + "\n";
                    }
                });

                const blob = new Blob([txt], { type: "text/plain" });
                const link = document.createElement("a");
                const date = new Date().toISOString().slice(0, 10);
                link.download = `inquiries_${date}.txt`;
                link.href = URL.createObjectURL(blob);
                link.click();
            });
        </script>
    </body>
</html>