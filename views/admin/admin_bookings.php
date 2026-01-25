<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Make sure $bookings, $stats, $selectedStatus, $searchId, $date are passed from AdminController
?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Font Awesome (REQUIRED) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Shared Transport Layout -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">

        <!-- Optional admin-only overrides -->
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_overrides.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/bookings.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_common.css">

        <!-- Responsive styles (always last) -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

        <title>Booking Management</title>
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
                <li class="active"><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
                <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
                <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
                <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                <li><a href="/CeylonGo/public/admin/promotions"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
                <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
                <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="booking-management">
                    <h2 class="page-title">Booking Management</h2>
                    <br>

                    <!-- Search & Filter Form -->
                    <form method="GET" action="/CeylonGo/public/admin/bookings">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" name="search" placeholder="Search by booking ID" class="search-input" value="<?= htmlspecialchars($searchId ?? '') ?>">
                                <button type="submit" class="search-btn">🔍</button>
                            </div>
                            <div class="filter-buttons">
                                <?php
                                    $statuses = ['all','pending','completed','cancelled'];
                                    foreach($statuses as $s):
                                        $active = ($selectedStatus ?? 'all') === $s ? 'active' : '';
                                        echo "<button type='submit' name='status' value='{$s}' class='filter-btn {$active}'>" . ucfirst($s) . "</button>";
                                    endforeach;
                                ?>
                            </div>
                            <div class="date-filter">
                                <input type="date" name="date" class="date-input" value="<?= htmlspecialchars($date ?? '') ?>" onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>

                    <!-- Booking Stats -->
                    <div class="stats-section">
                        <h4>Booking Statistics</h4><br>
                        <div class="stats-grid">
                            <?php
                                $keys = ['total','pending','completed','cancelled'];
                                foreach($keys as $k):
                                    $val = $stats[$k] ?? 0;
                                    echo "<div class='stat-box'><strong>" . ucfirst($k) . "</strong><br><span>{$val}</span></div>";
                                endforeach;
                            ?>
                        </div>
                    </div>
                    <br>

                    <!-- Bookings Table -->
                    <div class="bookings-section">
                        <table class="booking-table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody">
                                <?php foreach($bookings as $b): 
                                    switch (strtolower($b['status'])) {
                                        case 'pending':
                                            $statusClass = 'pending'; // orange
                                            break;
                                        case 'completed':
                                            $statusClass = 'completed'; // green
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'cancelled'; // red
                                            break;
                                        default:
                                            $statusClass = '';
                                    }
                                ?>
                                <tr>
                                    <td><?= $b['booking_id'] ?></td>
                                    <td><?= htmlspecialchars($b['user_name']) ?></td>
                                    <td><span class="status <?= $statusClass ?>"><?= ucfirst($b['status']) ?></span></td>
                                    <td><?= date('Y-m-d', strtotime($b['created_at'])) ?></td>
                                    <td>
                                        <button class="icon-btn view-btn" data-booking-id="<?= $b['booking_id'] ?>">👁️</button>
                                        <button class="icon-btn danger">❌</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer-buttons">
                        <button class="footer-btn black" id="exportBtn">Export Users</button>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div id="bookingModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <u><h3>Booking Details</h3></u>
                    <div id="bookingDetailsContent">Loading...</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <ul>
                <li><a href="/CeylonGo/public/admin/bookings">View All Bookings</a></li>
                <li><a href="/CeylonGo/public/admin/settings">Update Settings</a></li>
                <li><a href="/CeylonGo/public/admin/reports">Generate Report</a></li>
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

            const modal = document.getElementById("bookingModal");
            const modalContent = document.getElementById("bookingDetailsContent");
            const spanClose = modal.querySelector(".close");

            // Open modal
            document.querySelectorAll(".view-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    const bookingId = btn.dataset.bookingId;
                    if(!bookingId) return alert("Invalid booking ID");

                    modalContent.innerHTML = "Loading...";
                    modal.style.display = "block";

                    fetch('/CeylonGo/public/admin/booking-details?booking_id=' + bookingId)
                    .then(res => res.json())
                    .then(data => {
                        if(!data.success){
                            modalContent.innerHTML = `<p style="color:red">${data.message}</p>`;
                            return;
                        }

                        const b = data.booking;
                        let html = `<p><strong>Booking ID:</strong> ${b.booking_id}</p>`;
                        html += `<p><strong>User:</strong> ${b.user_name}</p>`;
                        html += `<p><strong>Status:</strong> ${b.status}</p>`;
                        html += `<p><strong>Date:</strong> ${b.created_at}</p>`;
                        html += `<h4>Destinations</h4><table>
                                    <tr>
                                        <th>Destination</th>
                                        <th>People</th>
                                        <th>Days</th>
                                        <th>Hotel</th>
                                        <th>Transport</th>
                                    </tr>`;
                        data.destinations.forEach(d => {
                            html += `<tr>
                                        <td>${d.destination}</td>
                                        <td>${d.people_count}</td>
                                        <td>${d.days}</td>
                                        <td>${d.hotel}</td>
                                        <td>${d.transport}</td>
                                    </tr>`;
                        });
                        html += "</table>";
                        modalContent.innerHTML = html;
                    })
                    .catch(err => {
                        console.error(err);
                        modalContent.innerHTML = "<p style='color:red'>Error loading booking details.</p>";
                    });
                });
            });

            // Close modal
            spanClose.onclick = () => modal.style.display = "none";
            window.onclick = e => { if(e.target == modal) modal.style.display = "none"; };

            // Export table
            document.getElementById("exportBtn").addEventListener("click", () => {
                const rows = document.querySelectorAll("#bookingsTableBody tr");
                if(rows.length === 0) return alert("No users to export!");
                let txt = "Booking ID\tUser\tStatus\tDate\n";
                rows.forEach(r => {
                    if(r.style.display !== "none"){
                        txt += [...r.cells].slice(0,4).map(c => c.innerText).join("\t") + "\n";
                    }
                });
                const blob = new Blob([txt], {type:"text/plain"});
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = "bookings.txt";
                link.click();
            });
        </script>
    </body>
</html>
