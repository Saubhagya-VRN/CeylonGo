<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include session initialization for profile picture
require_once 'session_init.php';

// Include database and model
require_once dirname(__DIR__, 2) . "/config/config.php";
require_once dirname(__DIR__, 2) . "/core/Database.php";
require_once dirname(__DIR__, 2) . "/models/GuideBankDetails.php";

// Check if user is logged in
if(isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'guide'){
    $user_id = trim($_SESSION['user_id']);
} else {
    header('Location: /CeylonGo/public/login');
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

// Handle form submission for bank details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_bank_details') {
    try {
        $db = Database::getConnection();
        $bankModel = new GuideBankDetails($db);
        
        $bankModel->id = $user_id;
        $bankModel->bank_name = trim($_POST['bank_name']);
        $bankModel->acc_no = trim($_POST['acc_no']);
        $bankModel->acc_holder_name = trim($_POST['acc_holder_name']);
        $bankModel->branch_name = trim($_POST['branch_name']);
        
        if ($bankModel->saveBankDetails()) {
            $_SESSION['payment_message'] = "Bank details saved successfully!";
        } else {
            $_SESSION['payment_error'] = "Failed to save bank details.";
        }
    } catch (Exception $e) {
        $_SESSION['payment_error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: /CeylonGo/public/guide/payment");
    exit();
}

// Fetch bank details from database
try {
    $db = Database::getConnection();
    $bankModel = new GuideBankDetails($db);
    $bankData = $bankModel->getBankDetailsById($user_id);
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
        'booking_id' => '#G12345',
        'customer_name' => 'John Silva',
        'tour_date' => '2026-01-20',
        'payment_date' => '2026-01-21',
        'amount' => 15000,
        'status' => 'paid',
        'tour_type' => 'Historical Tour'
    ],
    [
        'booking_id' => '#G77889',
        'customer_name' => 'Sarah Fernando',
        'tour_date' => '2026-01-25',
        'payment_date' => '2026-01-26',
        'amount' => 20000,
        'status' => 'paid',
        'tour_type' => 'Cultural Tour'
    ],
    [
        'booking_id' => '#G45678',
        'customer_name' => 'David Perera',
        'tour_date' => '2026-02-01',
        'payment_date' => null,
        'amount' => 12500,
        'status' => 'pending',
        'tour_type' => 'Beach Tour'
    ],
    [
        'booking_id' => '#G99123',
        'customer_name' => 'Nimal Rajapaksa',
        'tour_date' => '2026-02-05',
        'payment_date' => '2026-02-06',
        'amount' => 18000,
        'status' => 'paid',
        'tour_type' => 'Wildlife Safari'
    ],
    [
        'booking_id' => '#G55667',
        'customer_name' => 'Amara Wijesinghe',
        'tour_date' => '2026-02-10',
        'payment_date' => null,
        'amount' => 14500,
        'status' => 'pending',
        'tour_type' => 'Adventure Tour'
    ]
];

// Calculate summary statistics
$totalEarnings = array_sum(array_column($payments, 'amount'));
$paidPayments = array_filter($payments, fn($p) => $p['status'] === 'paid');
$pendingPayments = array_filter($payments, fn($p) => $p['status'] === 'pending');
$completedAmount = array_sum(array_column($paidPayments, 'amount'));
$pendingAmount = array_sum(array_column($pendingPayments, 'amount'));
$averageTour = count($payments) > 0 ? $totalEarnings / count($payments) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go - My Payments</title>
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/forms.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">

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
            border-left: 4px solid #2d6a4f;
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
            background: rgba(45, 106, 79, 0.1);
            color: #2d6a4f;
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
            color: #2d6a4f;
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
            background: #2d6a4f;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s ease;
        }

        .export-btn:hover {
            background: #1e4d39;
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
            border: 1px solid #2d6a4f;
            background: transparent;
            color: #2d6a4f;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            background: #2d6a4f;
            color: #fff;
        }

        /* Table Container Fix */
        .table-container {
            overflow-x: auto !important;
            overflow-y: visible !important;
            margin-bottom: 30px;
            -webkit-overflow-scrolling: touch;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .table-container table {
            width: 100%;
            min-width: 950px !important;
            table-layout: auto !important;
            border-collapse: collapse;
        }

        /* Ensure table cells don't wrap */
        .table-container th,
        .table-container td {
            white-space: nowrap;
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .table-container th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .table-container tr:hover {
            background: #f8f9fa;
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
                min-width: 750px;
            }
        }

        /* Add Account Button */
        .add-account-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #2d6a4f 0%, #1e4d39 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(45, 106, 79, 0.3);
        }

        .add-account-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 106, 79, 0.4);
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
            border-color: #2d6a4f;
            box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.1);
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

        /* Welcome section */
        .welcome {
            margin-bottom: 25px;
        }

        .welcome h2 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 28px;
        }

        .welcome p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
    </style>
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
            <a href="/CeylonGo/public/guide/dashboard">Home</a>
            <div class="profile-dropdown">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
                <div class="profile-dropdown-menu" id="profileDropdown">
                    <a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a>
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
                <li><a href="/CeylonGo/public/guide/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
                <li><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
                <li><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
                <li><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
                <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
                <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
                <li class="active"><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
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
                    <h3>Average Per Tour</h3>
                    <p class="amount">Rs. <?= number_format($averageTour) ?></p>
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
                            <th>Tour Type</th>
                            <th>Tour Date</th>
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
                            <td><?= $payment['tour_type'] ?></td>
                            <td><?= date('M d, Y', strtotime($payment['tour_date'])) ?></td>
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
