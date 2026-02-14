<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['uid'];

// Fetch User Details
$q = $conn->query("SELECT * FROM users WHERE id='$uid'");
$user = $q->fetch_assoc();

// Fetch Billing Stats for the Card
$bill_res = $conn->query("SELECT count(*) as total, SUM(amount) as paid FROM bills WHERE user_id='$uid' AND status='Paid'");
$bill_stats = $bill_res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile | EBMS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f8fafc; }

        /* Full Page Profile Wrapper */
        .profile-hero {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding-bottom: 100px;
        }

        .profile-container {
            max-width: 800px;
            margin: -150px auto 50px;
            padding: 0 20px;
        }

        /* The Main Profile Card */
        .id-card {
            background: #fff;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .card-header {
            background: #2563eb;
            height: 120px;
            position: relative;
        }

        .user-circle {
            width: 140px;
            height: 140px;
            background: #fff;
            border-radius: 50%;
            position: absolute;
            bottom: -70px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .avatar-main {
            width: 125px;
            height: 125px;
            background: #1e293b;
            border-radius: 50%;
            color: #38bdf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            font-weight: bold;
        }

        .card-body {
            padding: 90px 40px 40px;
            text-align: center;
        }

        .user-name { font-size: 32px; color: #1e293b; margin-bottom: 5px; }
        .user-email { color: #64748b; font-size: 16px; margin-bottom: 25px; }

        /* Data Grid */
        .info-strip {
            display: flex;
            justify-content: space-around;
            background: #f1f5f9;
            padding: 25px;
            border-radius: 15px;
            margin-top: 30px;
        }

        .info-item h4 { color: #94a3b8; font-size: 12px; text-transform: uppercase; margin-bottom: 5px; }
        .info-item p { color: #1e293b; font-size: 18px; font-weight: bold; }

        /* Account Status Tag */
        .status-tag {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 6px 18px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px;
        }

        /* Detail Rows */
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
        }
        .detail-row span { color: #64748b; font-weight: 500; }
        .detail-row b { color: #1e293b; }

    </style>
</head>
<body>

<div class="navbar">
    <h2>⚡ EBMS</h2>
    <div>
        <a href="home.php">Home</a>
        <a href="viewbill.php">Bills</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="profile-hero">
    <div>
        <h1 style="color: white;">My Account</h1>
        <p style="color: #94a3b8;">Registered User Profile Details</p>
    </div>
</div>

<div class="profile-container">
    <div class="id-card">
        <div class="card-header">
            <div class="user-circle">
                <div class="avatar-main"><?= substr($user['name'], 0, 1) ?></div>
            </div>
        </div>

        <div class="card-body">
            <h1 class="user-name"><?= $user['name'] ?></h1>
            <p class="user-email"><?= $user['email'] ?></p>
            <div class="status-tag">ACTIVE ACCOUNT</div>

            <div class="info-strip">
                <div class="info-item">
                    <h4>Bills Paid</h4>
                    <p><?= $bill_stats['total'] ?></p>
                </div>
                <div class="info-item">
                    <h4>Total Spend</h4>
                    <p>₹ <?= number_format($bill_stats['paid'] ?? 0) ?></p>
                </div>
                <div class="info-item">
                    <h4>User Role</h4>
                    <p><?= strtoupper($user['role']) ?></p>
                </div>
            </div>

            <div style="margin-top: 40px;">
                <div class="detail-row">
                    <span>Consumer Type</span>
                    <b>Domestic / Residential</b>
                </div>
                <div class="detail-row">
                    <span>Member Since</span>
                    <b>February 2026</b>
                </div>
                <div class="detail-row">
                    <span>Last Login</span>
                    <b>Today</b>
                </div>
            </div>

            <br>
            <a href="home.php" class="btn secondary" style="width: 100%; border-radius: 10px;">Return to Dashboard</a>
        </div>
    </div>
</div>

<div class="footer">
    <p>&copy; 2026 EB Management System | Official Customer Record</p>
</div>

</body>
</html>