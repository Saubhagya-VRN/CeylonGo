<?php 
    session_start();
    include("../../config/database.php");

    if (!isset($_SESSION['admin_username'])) {
        header("Location: admin_login.php");
        exit();
    }

    // Delete user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
        $user_id = intval($_POST['delete_id']);
        $stmt = $conn->prepare("DELETE FROM tourist_users WHERE id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "DB error"]);
        }
        $stmt->close();
        $conn->close();
        exit();
    }

    // Add / Edit user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_user']) || isset($_POST['edit_user']))) {
        $first_name = trim($_POST['first_name']);
        $last_name  = trim($_POST['last_name']);
        $email      = trim($_POST['email']);
        $contact    = trim($_POST['contact']);
        $password   = isset($_POST['password']) ? trim($_POST['password']) : null;

        if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($contact)) {
            if (isset($_POST['add_user'])) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO tourist_users (first_name, last_name, contact_number, email, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $first_name, $last_name, $contact, $email, $hashed_password);
            } else if (isset($_POST['edit_user'])) {
                $user_id = intval($_POST['user_id']);
                $stmt = $conn->prepare("UPDATE tourist_users SET first_name = ?, last_name = ?, contact_number = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $first_name, $last_name, $contact, $email, $user_id);
            }

            if ($stmt->execute()) {
                echo "<script>alert('✅ User saved successfully!'); window.location.href='admin_user.php';</script>";
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
    $result = $conn->query("SELECT id, first_name, last_name, contact_number, email FROM tourist_users ORDER BY id ASC");
    $users = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    $conn->close();
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
                <li><a href="admin_dashboard.php">Home</a></li>
                <li><a href="admin_user.php" class="active">Users</a></li>
                <li><a href="admin_bookings.php">Bookings</a></li>
                <li><a href="admin_service.php">Service Providers</a></li>
                <li><a href="admin_payments.php">Payments</a></li>
                <li><a href="admin_reports.php">Reports</a></li>
                <li><a href="admin_reviews.php">Reviews</a></li>
                <li><a href="admin_inquiries.php">Inquiries</a></li>
                <li><a href="admin_settings.php">System Settings</a></li>
                <li><a href="admin_promotions.php">Promotions</a></li>
                <li><a href="../../controllers/admin/logout.php">Logout</a></li>
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
                                    <td class="actions">
                                        <button class="icon-btn edit-btn">✏️</button>
                                        <button class="icon-btn danger delete-btn">❌</button>
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
                    <button class="footer-btn black" id="openModalBtn">+ Add New User</button>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div class="modal" id="userModal">
            <div class="modal-content">
                <h3 id="modalTitle">Add New User</h3>
                <form method="POST" action="admin_user.php" id="userForm">
                    <input type="hidden" name="user_id" id="user_id">
                    <input type="text" name="first_name" id="first_name" placeholder="Enter first name" required><br>
                    <input type="text" name="last_name" id="last_name" placeholder="Enter last name" required><br>
                    <input type="text" name="contact" id="contact" placeholder="Enter contact number" required><br>
                    <input type="email" name="email" id="email" placeholder="Enter email address" required><br>
                    <input type="password" name="password" id="password" placeholder="Enter password"><br>
                    <button type="submit" name="add_user" id="submitBtn" class="submit-btn">Add User</button>
                    <button type="button" class="cancel-btn" id="closeModalBtn">Cancel</button>
                </form>
            </div>
        </div>

        <script>
            const modal = document.getElementById("userModal");
            const modalTitle = document.getElementById("modalTitle");
            const submitBtn = document.getElementById("submitBtn");

            document.getElementById("openModalBtn").addEventListener("click", () => {
                modal.style.display = "flex";
                modalTitle.innerText = "Add New User";
                submitBtn.name = "add_user";
                document.getElementById("userForm").reset();
                document.getElementById("password").style.display = "block";
            });

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
                    document.getElementById("password").style.display = "none";

                    document.getElementById("user_id").value = id;
                    document.getElementById("first_name").value = first_name;
                    document.getElementById("last_name").value = last_name;
                    document.getElementById("contact").value = contact;
                    document.getElementById("email").value = email;
                });
            });

            // Delete user
            document.getElementById("userTableBody").addEventListener("click", function(e){
                if(e.target && e.target.classList.contains("delete-btn")){
                    const row = e.target.closest("tr");
                    const userId = row.dataset.id;
                    if(!confirm("Are you sure you want to delete this user?")) return;

                    fetch("admin_user.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "delete_id=" + encodeURIComponent(userId)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success){
                            alert("User deleted successfully!");
                            row.remove(); 
                        } else {
                            alert("Error: " + (data.message || "Could not delete user"));
                        }
                    })
                    .catch(err => console.error(err));
                }
            });

            // Search
            function applySearch(){
                const searchTerm = document.getElementById("searchInput").value.toLowerCase();
                document.querySelectorAll("#userTableBody tr").forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? "" : "none";
                });
            }

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
