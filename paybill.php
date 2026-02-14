<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['uid'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'], $_POST['csrf_token'])) {
        header("Location: viewbill.php");
        exit();
    }
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    $id = intval($_POST['id']);

    $stmt = $conn->prepare("SELECT user_id, status FROM bills WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        header("Location: viewbill.php");
        exit();
    }
    $bill = $res->fetch_assoc();
    if ($bill['user_id'] != $uid) {
        die('Unauthorized');
    }
    if ($bill['status'] === 'Paid') {
        header("Location: viewbill.php");
        exit();
    }

    $u = $conn->prepare("UPDATE bills SET status = 'Paid' WHERE id = ?");
    $u->bind_param("i", $id);
    $u->execute();

    echo "<script>alert('Payment Successful'); window.location='viewbill.php';</script>";
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: viewbill.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT id, consumer_no, month, amount, status FROM bills WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: viewbill.php");
    exit();
}
$bill = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Secure Payment | EBMS</title>
    <link rel="stylesheet" href="style.css">
    <meta charset="utf-8">
    <style>
        .payment-container { max-width: 600px; margin: 40px auto; }
        .payment-methods { display: flex; gap: 10px; margin: 20px 0; }
        .method-box {
            flex: 1;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
        }
        .method-box:hover, .method-box.active { border-color: #2563eb; background: #eff6ff; }
        .secure-badge {
            background: #f8fafc;
            padding: 10px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            font-size: 14px;
            color: #64748b;
        }
        .bill-summary {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .bill-summary h3 { margin-bottom: 15px; color: #1e293b; }
        .row { display: flex; justify-content: space-between; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>⚡ EBMS</h2>
    <div>
        <a href="home.php">Home</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="payment-container">
    <div class="page-box">
        <h2 style="text-align: left; margin-bottom: 20px;">💳 Secure Checkout</h2>
        
        <div class="bill-summary">
            <h3>Bill Summary</h3>
            <div class="row"><span>Consumer Number</span> <strong><?=htmlspecialchars($bill['consumer_no'])?></strong></div>
            <div class="row"><span>Billing Month</span> <strong><?=htmlspecialchars($bill['month'])?></strong></div>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
            <div class="row" style="font-size: 20px;">
                <span>Total Payable</span> 
                <strong style="color: #2563eb;">₹ <?=htmlspecialchars($bill['amount'])?></strong>
            </div>
        </div>

        <p><strong>Select Payment Method:</strong></p>
        <div class="payment-methods">
            <div class="method-box active">
                <div style="font-size: 24px;">💳</div>
                <small>Card</small>
            </div>
            <div class="method-box">
                <div style="font-size: 24px;">📱</div>
                <small>UPI</small>
            </div>
            <div class="method-box">
                <div style="font-size: 24px;">🏦</div>
                <small>Net Banking</small>
            </div>
        </div>

        <form method="post">
            <input type="hidden" name="id" value="<?=htmlspecialchars($bill['id'])?>">
            <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['csrf_token'])?>">
            
            <button type="submit" class="btn" style="width: 100%; padding: 15px; font-size: 18px;">Confirm & Pay ₹<?=htmlspecialchars($bill['amount'])?></button>
            <a href="viewbill.php" style="display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none;">← Go back</a>
        </form>

        <div class="secure-badge">
            <span style="color: #10b981;">🛡️</span>
            256-bit SSL Secure Payment Gateway
        </div>
    </div>
</div>

<div class="footer">
    <p>&copy; 2026 EBMS Secure Checkout | SSL Encrypted</p>
</div>

</body>
</html>