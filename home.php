<?php
session_start();
include "db.php"; 

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['uid']; 
$userName = $_SESSION['user'];

// Fetch Latest Bill
$latest_bill_q = $conn->query("SELECT * FROM bills WHERE user_id='$uid' ORDER BY id DESC LIMIT 1");
$bill = $latest_bill_q->fetch_assoc();

$display_amount = ($bill) ? "₹ " . $bill['amount'] : "₹ 0.00";
$display_status = ($bill) ? $bill['status'] : "No Bill";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Customer Dashboard | EBMS</title>
    <style>
        html { scroll-behavior: smooth; }
        
        /* --- NEON LIGHT EFFECT --- */
        .neon-text {
            color: #fff;
            text-shadow: 0 0 7px #fff, 0 0 10px #fff, 0 0 21px #fff, 0 0 42px #0fa, 0 0 82px #0fa, 0 0 92px #0fa;
            animation: pulsate 1.5s infinite alternate;
        }

        @keyframes pulsate {
            100% { text-shadow: 0 0 4px #fff, 0 0 11px #fff, 0 0 19px #fff, 0 0 40px #0fa, 0 0 80px #0fa, 0 0 90px #0fa; }
            0% { text-shadow: 0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 10px #0fa, 0 0 45px #0fa, 0 0 55px #0fa; }
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), 
                        url('image/img5.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 20px;
            text-align: center;
            border-bottom: 5px solid #2563eb;
        }

        /* Stats Row */
        .stats-strip {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: -40px;
            padding: 0 20px;
        }

        .stat-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            text-align: center;
            min-width: 180px;
            border-bottom: 3px solid #2563eb;
        }

        .stat-box h4 { font-size: 12px; text-transform: uppercase; color: #64748b; margin-bottom: 5px; }
        .stat-box p { font-size: 20px; font-weight: bold; color: #1e293b; }

        /* Floating Help Button */
        .float-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #2563eb;
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            font-weight: bold;
            z-index: 1000;
            transition: 0.3s;
        }
        .float-btn:hover { background: #1d4ed8; transform: scale(1.1); }

        /* Review Section */
        .reviews {
            display: flex;
            justify-content: space-around;
            padding: 40px;
            background: #f8fafc;
            flex-wrap: wrap;
            gap: 20px;
        }
        .review-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-top: 3px solid #10b981;
        }

        /* Timeline Tracker */
        .tracker-container {
            padding: 40px 20px;
            background: white;
            text-align: center;
        }
        .tracker {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 30px;
        }
        .step { text-align: center; position: relative; }
        .circle { width: 40px; height: 40px; background: #2563eb; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; }
        .active-step { background: #10b981; }

    </style>
</head>
<body>

<a href="contact.php" class="float-btn">💬 Quick Help</a>

<div class="navbar">
    <h2>⚡ EBMS</h2>
    <div>
        <a href="home.php">Dashboard</a>
        <a href="viewbill.php">My Bills</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="hero">
    <h1 class="neon-text">Hello, <?=$userName?>!</h1>
    <p style="margin-top:15px; font-weight: 500;">Your reliable Energy Management Portal is ready.</p>
</div>

<div class="stats-strip">
    <div class="stat-box">
        <h4>Connection ID</h4>
        <p>EB-<?=$uid?></p>
    </div>
    <div class="stat-box">
        <h4>Live Status</h4>
        <p style="color: #10b981;">● Active</p>
    </div>
    <div class="stat-box">
        <h4>Last Month</h4>
        <p><?=($bill['units'] ?? 0)?> Units</p>
    </div>
</div>


<div class="tracker-container">
    <h2>Current Service Cycle</h2>
    <div class="tracker">
        <div class="step"><div class="circle active-step">✓</div><p>Meter Read</p></div>
        <div class="step"><div class="circle active-step">✓</div><p>Bill Generated</p></div>
        <div class="step"><div class="circle">3</div><p>Awaiting Payment</p></div>
        <div class="step"><div class="circle" style="background:#e2e8f0;">4</div><p>Verification</p></div>
    </div>
</div>

<div class="features">
    <div class="feature-card">
        <div style="height: 160px; background: url('image/img4.jpeg') center/cover;"></div>
        <div style="padding: 20px;">
            <h3>Latest Invoice</h3>
            <h2 style="color: #1e293b; margin: 10px 0;"><?=$display_amount?></h2>
            <span class="badge <?=strtolower($display_status)?>"><?=$display_status?></span>
            <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
            <a href="paybill.php" class="btn" style="width: 100%;">Proceed to Pay</a>
        </div>
    </div>

    <div class="feature-card">
        <div style="height: 160px; background: url('image/img3.jpeg') center/cover;"></div>
        <div style="padding: 20px;">
            <h3>Monthly Usage</h3>
            <h2 style="color: #1e293b; margin: 10px 0;"><?=($bill['units'] ?? 0)?> <small>Units</small></h2>
            <p style="color: #64748b;">Billing Month: <?=($bill['month'] ?? 'N/A')?></p>
            <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
            <a href="viewbill.php" class="btn secondary" style="width: 100%;">View History</a>
        </div>
    </div>

    <div class="feature-card">
        <div style="height: 160px; background: url('image/img2.jpeg') center/cover;"></div>
        <div style="padding: 20px;">
            <h3>Complaint Cell</h3>
            <p style="color: #64748b; margin-bottom: 20px;">Technical support active 24/7 for you.</p>
            <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
            <a href="contact.php" class="btn secondary" style="width: 100%;">Raise a Ticket</a>
        </div>
    </div>
</div>


<div class="chart-section">
    <h3 style="color: #1e293b; text-align: center;">Consumption Insights (Last 6 Months)</h3>
    <div class="bar-container">
        <div class="bar" style="height: 60%;" data-label="Aug"></div>
        <div class="bar" style="height: 45%;" data-label="Sep"></div>
        <div class="bar" style="height: 75%;" data-label="Oct"></div>
        <div class="bar" style="height: 90%;" data-label="Nov"></div>
        <div class="bar" style="height: 55%;" data-label="Dec"></div>
        <div class="bar" style="height: 80%; background: #2563eb;" data-label="Jan"></div>
    </div>
</div>

<div style="padding: 40px 20px; max-width: 900px; margin: auto;">
    <h2 style="text-align:center; margin-bottom: 30px;">Frequently Asked Questions</h2>
    <details>
        <summary>How is my bill calculated?</summary>
        <p>Bills are calculated based on the total units consumed multiplied by the current tariff rate, plus taxes.</p>
    </details>
    <details>
        <summary>What should I do if my meter is faulty?</summary>
        <p>Use the "Complaint Cell" above to raise a ticket. Our engineer will visit in 24 hours.</p>
    </details>
    <details>
        <summary>When is the last date to pay without a fine?</summary>
        <p>You have 15 days from the date of bill generation to pay without an LPS fine.</p>
    </details>
</div>

<div class="reviews">
    <div class="review-card">
        <p>"Very easy to use portal. I can pay my EB bills in just two clicks!"</p>
        <h4 style="margin-top:10px;">- Hari</h4>
    </div>
    <div class="review-card">
        <p>"The consumption chart helps me save more energy every month. Brilliant!"</p>
        <h4 style="margin-top:10px;">- Moni</h4>
    </div>
    <div class="review-card">
        <p>"Fast response from the complaint cell. Highly recommended service."</p>
        <h4 style="margin-top:10px;">- Vijay</h4>
    </div>
</div>

<div style="background: #1e293b; color: white; padding: 60px 20px; text-align: center;">
    <h2>Reach Our Experts</h2>
    <p style="margin: 15px 0; color: #cbd5e1;">Available 24/7 for technical and billing assistance.</p>
    <div style="display: flex; justify-content: center; gap: 30px; margin-top: 30px; flex-wrap: wrap;">
        <div><h4 style="color: #38bdf8;">📞 Helpline</h4><p>6385809382</p></div>
        <div><h4 style="color: #38bdf8;">📧 Support</h4><p>help@ebms.com</p></div>
        <div><h4 style="color: #38bdf8;">📍 Office</h4><p>Gobichettipalayam</p></div>
    </div>
    <br><br>
    <a href="contact.php" class="btn" style="background: #38bdf8; color: #1e293b; font-weight: bold;">Submit a Query</a>
</div>

<div class="footer">
    <p>&copy; 2026 EB Management System | Designed by Elamathi| Secure Portal</p>
</div>

</body>
</html>