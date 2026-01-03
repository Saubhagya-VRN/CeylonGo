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
        <title>Booking Management</title>
        <link rel="stylesheet" href="../../public/css/admin/bookings.css">
    </head>
    <body>
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
                <h2>Ceylon Go</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="/CeylonGo/public/admin/dashboard">Home</a></li>
                <li><a href="/CeylonGo/public/admin/users">Users</a></li>
                <li><a href="/CeylonGo/public/admin/bookings" class="active">Bookings</a></li>
                <li><a href="/CeylonGo/public/admin/service">Service Providers</a></li>
                <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
                <li><a href="/CeylonGo/public/admin/reports">Reports</a></li>
                <li><a href="/CeylonGo/public/admin/reviews">Reviews</a></li>
                <li><a href="/CeylonGo/public/admin/inquiries">Inquiries</a></li>
                <li><a href="/CeylonGo/public/admin/settings">System Settings</a></li>
                <li><a href="/CeylonGo/public/admin/promotions">Promotions</a></li>
                <li><a href="/CeylonGo/public/logout">Logout</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <div class="booking-management">
                <h2 class="page-title">Booking Management</h2>
                <br><br>

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
                <h3>Booking Details</h3>
                <div id="bookingDetailsContent">Loading...</div>
            </div>
        </div>

        <script>
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
