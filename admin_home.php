<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// --- FETCHING ADMIN STATS ---
// 1. Total Customers
$user_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='customer'")->fetch_assoc()['total'];

// 2. Total Revenue (Paid Bills)
$revenue = $conn->query("SELECT SUM(amount) as total FROM bills WHERE status='Paid'")->fetch_assoc()['total'];

// 3. Pending Bills Count
$pending_count = $conn->query("SELECT COUNT(*) as total FROM bills WHERE status='Unpaid'")->fetch_assoc()['total'];

// 4. Total Messages/Tickets
$msg_count = $conn->query("SELECT COUNT(*) as total FROM messages")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | EBMS Control</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f1f5f9; }
        
        /* Neon Welcome for Admin */
        .admin-neon {
            color: #fff;
            text-shadow: 0 0 5px #fff, 0 0 10px #2563eb, 0 0 20px #2563eb;
            font-size: 45px;
            margin-bottom: 10px;
        }

        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 80px 20px;
            text-align: center;
            border-bottom: 4px solid #3b82f6;
        }

        /* Stats Grid */
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            padding: 0 40px;
            margin-top: -40px;
        }

        .stat-card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            border-bottom: 4px solid #2563eb;
            transition: 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card h4 { color: #64748b; font-size: 14px; text-transform: uppercase; margin-bottom: 10px; }
        .stat-card h2 { color: #1e293b; font-size: 28px; }

        /* Admin Quick Links */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            padding: 40px;
        }

        .action-box {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 20px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .action-box:hover { border-color: #2563eb; background: #f8fafc; }
        .icon-circle {
            width: 60px;
            height: 60px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>⚡ EBMS Admin</h2>
    <div>
        <a href="admin_home.php">Dashboard</a>
        <a href="managecustomer.php">Users</a>
        <a href="generate_bill.php">Generate Bill</a>
        <a href="paid_list.php">Paid</a>
        <a href="unpaid_list.php">Unpaid</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="hero">
    <h1 class="admin-neon">System Overview</h1>
    <p style="color: #94a3b8;">Welcome back, Administrator. Monitoring the grid in real-time.</p>
</div>

<div class="admin-stats">
    <div class="stat-card">
        <h4>Total Revenue</h4>
        <h2>₹ <?= number_format($revenue ?? 0) ?></h2>
    </div>
    <div class="stat-card" style="border-color: #10b981;">
        <h4>Total Customers</h4>
        <h2><?= $user_count ?></h2>
    </div>
    <div class="stat-card" style="border-color: #ef4444;">
        <h4>Pending Bills</h4>
        <h2><?= $pending_count ?></h2>
    </div>
    <div class="stat-card" style="border-color: #f59e0b;">
        <h4>User Queries</h4>
        <h2><?= $msg_count ?></h2>
    </div>
</div>

<h2 style="text-align: center; margin-top: 60px; color: #1e293b;">Quick Management</h2>
<div class="quick-grid">
    <a href="generate_bill.php" class="action-box">
        <div class="icon-circle">📝</div>
        <div>
            <h3 style="margin-bottom: 5px;">Create New Bill</h3>
            <p style="font-size: 14px; color: #64748b;">Add monthly units for customers.</p>
        </div>
    </a>

    <a href="managecustomer.php" class="action-box">
        <div class="icon-circle">👥</div>
        <div>
            <h3 style="margin-bottom: 5px;">Manage Users</h3>
            <p style="font-size: 14px; color: #64748b;">Verify or remove user accounts.</p>
        </div>
    </a>

    <a href="view_messages.php" class="action-box">
        <div class="icon-circle">📩</div>
        <div>
            <h3 style="margin-bottom: 5px;">Customer Tickets</h3>
            <p style="font-size: 14px; color: #64748b;">Respond to user complaints.</p>
        </div>
    </a>
</div>

<div class="footer">
    <p>&copy; 2026 EBMS Control Panel | Secure Admin Access</p>
</div>

</body>
</html>