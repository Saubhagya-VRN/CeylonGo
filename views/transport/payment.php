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

// Sample payment data - Replace with database queries
$payments = [
    [
        'booking_id' => '#12345',
        'customer_name' => 'John Silva',
        'booking_date' => '2025-03-15',
        'payment_date' => '2025-03-16',
        'amount' => 10000,
        'status' => 'paid',
        'method' => 'Online'
    ],
    [
        'booking_id' => '#77889',
        'customer_name' => 'Sarah Fernando',
        'booking_date' => '2025-03-18',
        'payment_date' => '2025-03-19',
        'amount' => 15000,
        'status' => 'paid',
        'method' => 'Cash'
    ],
    [
        'booking_id' => '#45678',
        'customer_name' => 'David Perera',
        'booking_date' => '2025-03-20',
        'payment_date' => null,
        'amount' => 8500,
        'status' => 'pending',
        'method' => 'Online'
    ],
    [
        'booking_id' => '#99123',
        'customer_name' => 'Nimal Rajapaksa',
        'booking_date' => '2025-03-22',
        'payment_date' => '2025-03-23',
        'amount' => 12000,
        'status' => 'paid',
        'method' => 'Card'
    ],
    [
        'booking_id' => '#55667',
        'customer_name' => 'Amara Wijesinghe',
        'booking_date' => '2025-03-25',
        'payment_date' => null,
        'amount' => 9500,
        'status' => 'pending',
        'method' => 'Cash'
    ]
];

// Calculate summary statistics
$totalEarnings = array_sum(array_column($payments, 'amount'));
$paidPayments = array_filter($payments, fn($p) => $p['status'] === 'paid');
$pendingPayments = array_filter($payments, fn($p) => $p['status'] === 'pending');
$completedAmount = array_sum(array_column($paidPayments, 'amount'));
$pendingAmount = array_sum(array_column($pendingPayments, 'amount'));
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
    
    <style>
        /* Payment Summary Cards */
        .payment-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid #0077b6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .summary-card.pending {
            border-left-color: #ff9800;
        }

        .summary-card.completed {
            border-left-color: #28a745;
        }

        .summary-card.average {
            border-left-color: #6c757d;
        }

        .summary-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .summary-card.total .summary-card-icon {
            background: rgba(0, 119, 182, 0.1);
            color: #0077b6;
        }

        .summary-card.pending .summary-card-icon {
            background: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }

        .summary-card.completed .summary-card-icon {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .summary-card.average .summary-card-icon {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        .summary-card h3 {
            font-size: 14px;
            color: #666;
            margin: 0 0 8px 0;
            font-weight: 500;
        }

        .summary-card .amount {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        /* Bank Account Section */
        .bank-account-section {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .bank-account-section h3 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .bank-account-section h3 i {
            color: #0077b6;
        }

        .bank-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .bank-detail-item {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .bank-detail-item label {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .bank-detail-item p {
            margin: 0;
            font-size: 15px;
            color: #333;
            font-weight: 500;
        }

        /* Payment Table Enhancements */
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header h3 {
            margin: 0;
            color: #333;
            font-size: 20px;
        }

        .table-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fff;
            color: #333;
            font-size: 14px;
            cursor: pointer;
        }

        .export-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            background: #0077b6;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s ease;
        }

        .export-btn:hover {
            background: #005a8d;
        }

        .export-btn i {
            font-size: 12px;
        }

        /* Status Badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.paid {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.failed {
            background: #f8d7da;
            color: #721c24;
        }

        /* Action Buttons */
        .action-btn {
            padding: 6px 12px;
            border: 1px solid #0077b6;
            background: transparent;
            color: #0077b6;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            background: #0077b6;
            color: #fff;
        }

        /* Table Container Fix */
        .table-container {
            overflow-x: auto !important;
            overflow-y: visible !important;
            margin-bottom: 30px;
            -webkit-overflow-scrolling: touch;
        }

        .table-container table {
            width: 100%;
            min-width: 900px !important;
            table-layout: auto !important;
        }

        /* Ensure table cells don't wrap */
        .table-container th,
        .table-container td {
            white-space: nowrap;
            padding: 12px 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                width: 100% !important;
                left: 0 !important;
                right: 0 !important;
            }

            .payment-summary {
                grid-template-columns: 1fr;
            }

            .bank-details {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .table-container table {
                min-width: 700px;
            }
        }

        /* Add Account Button */
        .add-account-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #0077b6 0%, #005a8d 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 119, 182, 0.3);
        }

        .add-account-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 119, 182, 0.4);
        }

        /* Bank Modal Styles */
        .bank-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .bank-modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bank-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .bank-modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 20px;
        }

        .bank-modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #666;
            transition: color 0.2s ease;
        }

        .bank-modal-close:hover {
            color: #333;
        }

        .bank-modal .form-group {
            margin-bottom: 20px;
        }

        .bank-modal .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .bank-modal .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .bank-modal .form-group input:focus {
            outline: none;
            border-color: #0077b6;
            box-shadow: 0 0 0 3px rgba(0, 119, 182, 0.1);
        }

        .bank-modal-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .bank-modal .cancel-btn {
            padding: 12px 25px;
            border: 2px solid #ddd;
            background: #fff;
            color: #666;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .bank-modal .cancel-btn:hover {
            background: #f8f9fa;
            border-color: #ccc;
        }

        .bank-modal .save-btn {
            padding: 12px 25px;
            border: none;
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: #fff;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .bank-modal .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <header class="navbar">
        <div class="branding">
            <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Logo">
            <div class="logo-text">Ceylon Go</div>
        </div>
        <nav class="nav-links">
            <a href="#">Home</a>
            <a href="/CeylonGo/views/transport/logout.php">Logout</a>
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic">
        </nav>
    </header>

    <div class="page-wrapper">

        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
                <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
                <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
                <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
                <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
                <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
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
                            <th>Actions</th>
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
                            <td>
                                <button class="action-btn" onclick="alert('View receipt for <?= $payment['booking_id'] ?>')">
                                    <i class="fa-solid fa-receipt"></i> Receipt
                                </button>
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
</body>
</html>
