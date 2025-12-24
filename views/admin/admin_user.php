<?php 
    // Session is already started in public/index.php
    require_once(__DIR__ . '/../../config/config.php');
    require_once(__DIR__ . '/../../core/Database.php');

    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /CeylonGo/public/login");
        exit();
    }

    // Get database connection
    $conn = Database::getMysqliConnection();

    // ===== STATUS FLAG HANDLER (MUST BE FIRST) =====
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_id'])) {

        $user_id = intval($_POST['status_id']);
        $status  = intval($_POST['status']); // 0 or 1

        $stmt1 = $conn->prepare(
            "UPDATE tourist_users SET is_active = ? WHERE id = ?"
        );
        $stmt1->bind_param("ii", $status, $user_id);
        $ok1 = $stmt1->execute();
        $stmt1->close();

        $stmt2 = $conn->prepare(
            "UPDATE users SET is_active = ? WHERE ref_id = ? AND role = 'tourist'"
        );
        $stmt2->bind_param("ii", $status, $user_id);
        $ok2 = $stmt2->execute();
        $stmt2->close();

        header('Content-Type: application/json');
        echo json_encode(["success" => ($ok1 && $ok2)]);
        exit(); // 🔴 THIS IS CRITICAL
    }

    // Add / Edit user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
        $first_name = trim($_POST['first_name']);
        $last_name  = trim($_POST['last_name']);
        $email      = trim($_POST['email']);
        $contact    = trim($_POST['contact']);
        $password   = isset($_POST['password']) ? trim($_POST['password']) : null;

        if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($contact)) {
            $user_id = intval($_POST['user_id']);
            $stmt = $conn->prepare("UPDATE tourist_users SET first_name = ?, last_name = ?, contact_number = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $first_name, $last_name, $contact, $email, $user_id);

            if ($stmt->execute()) {
                echo "<script>alert('✅ User saved successfully!'); window.location.href='/CeylonGo/public/admin/users';</script>";
                exit();
            } else {
                echo "<script>alert('❌ Database error. Try again.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('⚠️ Please fill all fields.');</script>";
        }
    }

    // Retrieve data
    $result = $conn->query(
        "SELECT id, first_name, last_name, contact_number, email, is_active 
        FROM tourist_users ORDER BY id ASC"
    );
    $users = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    // Don't close connection - it's a singleton
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Management</title>
        <link rel="stylesheet" href="../../public/css/admin/admin_user.css">
    </head>

    <body>
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
                <h2>Ceylon Go</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="/CeylonGo/public/admin/dashboard">Home</a></li>
                <li><a href="/CeylonGo/public/admin/users" class="active">Users</a></li>
                <li><a href="/CeylonGo/public/admin/bookings">Bookings</a></li>
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
            <div class="user-management">
                <h2 class="page-title">User Management</h2>
                <br><br>

                <div class="toolbar">
                    <div class="search-section">
                        <input type="text" placeholder="Search by name or email" id="searchInput" class="search-input">
                        <button class="search-btn" onclick="applySearch()">🔍</button>
                    </div>
                </div>

                <div class="users-section">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Contact Number</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                        <?php if(count($users)>0): ?>
                            <?php foreach($users as $user): ?>
                                <tr data-id="<?= $user['id'] ?>">
                                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                                    <td><?= htmlspecialchars($user['contact_number']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?= $user['is_active']
                                            ? "<span style='color:green;font-weight:bold'>Active</span>"
                                            : "<span style='color:red;font-weight:bold'>Inactive</span>" ?>
                                    </td>
                                    <td class="actions">
                                        <button class="icon-btn edit-btn">✏️</button>

                                        <?php if ($user['is_active']): ?>
                                            <button class="icon-btn danger deactivate-btn">🚩</button>
                                        <?php else: ?>
                                            <button class="icon-btn activate-btn">✅</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center;">No users found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="footer-buttons">
                    <button class="footer-btn" id="exportBtn">Export Users</button>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div class="modal" id="userModal">
            <div class="modal-content">
                <h3 id="modalTitle">Edit User</h3>
                <form method="POST" action="/CeylonGo/public/admin/users" id="userForm">
                    <input type="hidden" name="user_id" id="user_id">

                    <input type="text" name="first_name" id="first_name" placeholder="Enter first name" required><br>
                    <input type="text" name="last_name" id="last_name" placeholder="Enter last name" required><br>

                    <!-- Phone number validation: exactly 10 digits -->
                    <input type="text" name="contact" id="contact" placeholder="Enter contact number" required
                        pattern="\d{10}" title="Phone number must be exactly 10 digits"><br>

                    <!-- Email validation: format name@gmail.com -->
                    <input type="email" name="email" id="email" placeholder="Enter email address" required
                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                        title="Please enter a valid email, e.g., name@gmail.com"><br>

                    <button type="submit" name="edit_user" id="submitBtn" class="submit-btn">Save Changes</button>
                    <button type="button" class="cancel-btn" id="closeModalBtn">Cancel</button>
                </form>
            </div>
        </div>

        <script>
            const modal = document.getElementById("userModal");
            const modalTitle = document.getElementById("modalTitle");
            const submitBtn = document.getElementById("submitBtn");

            document.getElementById("closeModalBtn").addEventListener("click", () => modal.style.display="none");
            window.onclick = (e) => { if(e.target === modal) modal.style.display="none"; };

            // Edit user
            document.querySelectorAll(".edit-btn").forEach(btn => {
                btn.addEventListener("click", function(){
                    const row = this.closest("tr");

                    const id = row.dataset.id;
                    const first_name = row.cells[0].innerText;
                    const last_name = row.cells[1].innerText;
                    const contact = row.cells[2].innerText;
                    const email = row.cells[3].innerText;

                    modal.style.display="flex";
                    modalTitle.innerText = "Edit User";
                    submitBtn.name = "edit_user";
                    submitBtn.innerText = "Save Changes";

                    document.getElementById("user_id").value = id;
                    document.getElementById("first_name").value = first_name;
                    document.getElementById("last_name").value = last_name;
                    document.getElementById("contact").value = contact;
                    document.getElementById("email").value = email;
                });
            });

            // Activate / Deactivate user
            document.getElementById("userTableBody").addEventListener("click", function(e) {

                const button = e.target.closest("button");
                if (!button) return;

                if (!button.classList.contains("deactivate-btn") &&
                    !button.classList.contains("activate-btn")) return;

                const row = button.closest("tr");
                const userId = row.dataset.id;
                const status = button.classList.contains("deactivate-btn") ? 0 : 1;

                if (!confirm("Are you sure?")) return;

                fetch("/CeylonGo/public/admin/user/status", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `user_id=${userId}&status=${status}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert("Failed to update user status");
                    }
                })
                .catch(() => alert("Server error"));
            });

            // Search
            function applySearch(){
                const searchTerm = document.getElementById("searchInput").value.toLowerCase();
                document.querySelectorAll("#userTableBody tr").forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? "" : "none";
                });
            }

            //enter key search
            document.getElementById("searchInput").addEventListener("keydown", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault(); // prevent accidental form submit
                    applySearch();
                }
            });

            //auto search
            document.getElementById("searchInput").addEventListener("input", applySearch);

            // Export Users
            document.getElementById("exportBtn").addEventListener("click", () => {
                const rows = document.querySelectorAll("#userTableBody tr");
                if(rows.length === 0) { alert("No users to export!"); return; }

                let txtContent = "First Name\tLast Name\tContact Number\tEmail\n";
                rows.forEach(row => {
                    if(row.style.display !== "none"){
                        const fn = row.cells[0].innerText;
                        const ln = row.cells[1].innerText;
                        const cn = row.cells[2].innerText;
                        const em = row.cells[3].innerText;
                        txtContent += `${fn}\t${ln}\t${cn}\t${em}\n`;
                    }
                });

                const blob = new Blob([txtContent], { type: "text/plain" });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = "tourist_users.txt";
                link.click();
            });
        </script>
    </body>
</html>