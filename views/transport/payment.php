<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include session initialization for profile picture
require_once 'session_init.php';

// Include database and model
require_once "../config/config.php";
require_once "../core/Database.php";
require_once "../models/BankDetails.php";

// Check if user is logged in
if(isset($_SESSION['transporter_id'])){
    $user_id = trim($_SESSION['transporter_id']);
} else {
    header('Location: /CeylonGo/views/transport/login.php');
    exit();
}

// Check for session messages (from controller)
$message = null;
$error = null;

if (isset($_SESSION['payment_message'])) {
    $message = $_SESSION['payment_message'];
    unset($_SESSION['payment_message']);
}

if (isset($_SESSION['payment_error'])) {
    $error = $_SESSION['payment_error'];
    unset($_SESSION['payment_error']);
}


// Fetch bank details from database
try {
    $db = Database::getConnection();
    $bankModel = new BankDetails($db);
    $bankData = $bankModel->getBankDetailsByRefId($user_id);
} catch (Exception $e) {
    $bankData = null;
}

// Set bank account details from database or defaults
$bankAccount = [
    'bank_name' => isset($bankData['bank_name']) ? $bankData['bank_name'] : 'Not set',
    'account_number' => isset($bankData['acc_no']) && $bankData['acc_no'] ? '****' . substr($bankData['acc_no'], -4) : 'Not set',
    'account_number_full' => isset($bankData['acc_no']) ? $bankData['acc_no'] : '',
    'holder_name' => isset($bankData['acc_holder_name']) ? $bankData['acc_holder_name'] : 'Not set',
    'branch' => isset($bankData['branch_name']) ? $bankData['branch_name'] : 'Not set'
];

// Build payments array from database data passed by controller
// $payments is already set via view() extract — it comes from TransportRequest::getPaymentsByDriverId()
if (!isset($payments)) {
    $payments = [];
}

// Transform DB records into the format used by the view
$formattedPayments = [];
foreach ($payments as $p) {
    $formattedPayments[] = [
        'booking_id' => '#TR' . str_pad($p['id'], 3, '0', STR_PAD_LEFT),
        'customer_name' => $p['customer_name'],
        'booking_date' => $p['date'],
        'payment_date' => $p['created_at'] ?? null,
        'amount' => floatval($p['estimated_fare'] ?? 0),
        'status' => $p['status'] === 'completed' ? 'paid' : 'confirmed',
    ];
}
$payments = $formattedPayments;

// Calculate summary statistics
$totalEarnings = array_sum(array_column($payments, 'amount'));
$paidPayments = array_filter($payments, fn($p) => $p['status'] === 'paid');
$confirmedPayments = array_filter($payments, fn($p) => $p['status'] === 'confirmed');
$completedAmount = array_sum(array_column($paidPayments, 'amount'));
$pendingAmount = array_sum(array_column($confirmedPayments, 'amount'));
$averageBooking = count($payments) > 0 ? $totalEarnings / count($payments) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go - My Payments</title>
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/cards.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/forms.css">
    
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/tables.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/charts.css">

    <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/payment.css">
</head>
<body>

    <!-- Navbar -->
    <header class="navbar">
        <div class="branding">
            <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
            <div class="logo-text">Ceylon Go</div>
        </div>
        <nav class="nav-links">
            <a href="/CeylonGo/public/transporter/dashboard">Home</a>
            <div class="profile-dropdown">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
                <div class="profile-dropdown-menu" id="profileDropdown">
                    <a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a>
                    <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="page-wrapper">

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <ul>
        <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
            </ul>
        </div>

        <div class="main-content">

            <!-- Welcome Section -->
            <div class="welcome">
                <h2>My Payments</h2>
                <p>Track your earnings and payment history</p>
            </div>

            <!-- Success/Error Messages -->
            <?php if ($message): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-check-circle"></i>
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Payment Summary Cards -->
            <div class="payment-summary">
                <div class="summary-card total">
                    <div class="summary-card-icon">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h3>Total Earnings</h3>
                    <p class="amount">Rs. <?= number_format($totalEarnings) ?></p>
                </div>

                <div class="summary-card pending">
                    <div class="summary-card-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <h3>Pending Payments</h3>
                    <p class="amount">Rs. <?= number_format($pendingAmount) ?></p>
                </div>

                <div class="summary-card completed">
                    <div class="summary-card-icon">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                    <h3>Completed Payments</h3>
                    <p class="amount">Rs. <?= number_format($completedAmount) ?></p>
                </div>

                <div class="summary-card average">
                    <div class="summary-card-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3>Average Booking</h3>
                    <p class="amount">Rs. <?= number_format($averageBooking) ?></p>
                </div>
            </div>

            <!-- Bank Account Details -->
            <div class="bank-account-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;"><i class="fa-solid fa-building-columns"></i> Bank Account Details</h3>
                    <button class="add-account-btn" onclick="openBankModal()">
                        <i class="fa-solid fa-plus"></i> Add / Update Account
                    </button>
                </div>
                <div class="bank-details">
                    <div class="bank-detail-item">
                        <label>Bank Name</label>
                        <p id="display-bank-name"><?= $bankAccount['bank_name'] ?></p>
                    </div>
                    <div class="bank-detail-item">
                        <label>Account Number</label>
                        <p id="display-account-number"><?= $bankAccount['account_number'] ?></p>
                    </div>
                    <div class="bank-detail-item">
                        <label>Account Holder</label>
                        <p id="display-holder-name"><?= $bankAccount['holder_name'] ?></p>
                    </div>
                    <div class="bank-detail-item">
                        <label>Branch</label>
                        <p id="display-branch"><?= $bankAccount['branch'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Payment History Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3>Payment History</h3>
                    <div class="table-actions">
                        <select class="filter-select" id="statusFilter">
                            <option value="all">All Payments</option>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer Name</th>
                            <th>Booking Date</th>
                            <th>Payment Date</th>
                            <th>Amount</th>
                            <th>Status</th>

                        </tr>
                    </thead>

                    <tbody id="paymentTableBody">
                        <?php foreach($payments as $payment): ?>
                        <tr data-status="<?= $payment['status'] ?>">
                            <td><?= $payment['booking_id'] ?></td>
                            <td><?= $payment['customer_name'] ?></td>
                            <td><?= date('M d, Y', strtotime($payment['booking_date'])) ?></td>
                            <td><?= $payment['payment_date'] ? date('M d, Y', strtotime($payment['payment_date'])) : '-' ?></td>
                            <td>Rs. <?= number_format($payment['amount']) ?></td>
                            <td>
                                <span class="status-badge <?= $payment['status'] ?>">
                                    <?= ucfirst($payment['status']) ?>
                                </span>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bank Account Modal -->
    <div id="bankModal" class="bank-modal">
        <div class="bank-modal-content">
            <div class="bank-modal-header">
                <h3><i class="fa-solid fa-building-columns"></i> Update Bank Account</h3>
                <button class="bank-modal-close" onclick="closeBankModal()">&times;</button>
            </div>
            <form id="bankAccountForm" method="POST" action="">
                <input type="hidden" name="action" value="save_bank_details">
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" id="bank_name" name="bank_name" placeholder="Enter bank name" value="<?= isset($bankData['bank_name']) ? htmlspecialchars($bankData['bank_name']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Account Number</label>
                    <input type="text" id="acc_no" name="acc_no" placeholder="Enter account number" value="<?= isset($bankData['acc_no']) ? htmlspecialchars($bankData['acc_no']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Account Holder Name</label>
                    <input type="text" id="acc_holder_name" name="acc_holder_name" placeholder="Enter account holder name" value="<?= isset($bankData['acc_holder_name']) ? htmlspecialchars($bankData['acc_holder_name']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Branch Name</label>
                    <input type="text" id="branch_name" name="branch_name" placeholder="Enter branch name" value="<?= isset($bankData['branch_name']) ? htmlspecialchars($bankData['branch_name']) : '' ?>" required>
                </div>
                <div class="bank-modal-actions">
                    <button type="button" class="cancel-btn" onclick="closeBankModal()">Cancel</button>
                    <button type="submit" class="save-btn"><i class="fa-solid fa-save"></i> Save Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer Links -->
    <footer>
        <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact Us</a></li>
        </ul>
    </footer>

    <script>
        // Filter functionality
        document.getElementById('statusFilter').addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('#paymentTableBody tr');
            
            rows.forEach(row => {
                if (filterValue === 'all') {
                    row.style.display = '';
                } else {
                    if (row.dataset.status === filterValue) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });

        // Bank Modal Functions
        function openBankModal() {
            document.getElementById('bankModal').style.display = 'block';
        }

        function closeBankModal() {
            document.getElementById('bankModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('bankModal');
            if (event.target === modal) {
                closeBankModal();
            }
        }
    </script>

    <!-- Profile Dropdown Script -->
    <script>
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('show');
        }

        // Close profile dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const profilePic = document.querySelector('.profile-pic');
            
            if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
                dropdown.classList.remove('show');
            }
        });

        // Hamburger Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            function toggleSidebar() {
                hamburgerBtn.classList.toggle('active');
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
            }
            
            function closeSidebar() {
                hamburgerBtn.classList.remove('active');
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            if (hamburgerBtn) {
                hamburgerBtn.addEventListener('click', toggleSidebar);
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }
            
            const sidebarLinks = document.querySelectorAll('.sidebar ul li a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });
            
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            });
        });
    </script>
</body>
</html>
