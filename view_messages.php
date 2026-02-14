<?php
session_start();
include "db.php";

// Security Check: Only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch messages with user names using a JOIN
$sql = "SELECT messages.*, users.name as sender_name, users.email as sender_email 
        FROM messages 
        JOIN users ON messages.user_id = users.id 
        ORDER BY messages.created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Tickets | Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f1f5f9; }
        
        .ticket-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .ticket-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-left: 5px solid #2563eb;
            transition: 0.3s;
        }

        .ticket-card:hover { transform: scale(1.01); box-shadow: 0 10px 15px rgba(0,0,0,0.1); }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .sender-info h4 { color: #1e293b; margin: 0; }
        .sender-info small { color: #64748b; }

        .issue-tag {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Dynamic Colors for Subjects */
        .tag-billing { background: #fee2e2; color: #ef4444; }
        .tag-meter { background: #fef3c7; color: #d97706; }
        .tag-payment { background: #dcfce7; color: #10b981; }
        .tag-other { background: #e0f2fe; color: #0369a1; }

        .message-body {
            color: #475569;
            line-height: 1.6;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }

        .timestamp {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 15px;
            text-align: right;
        }

        .empty-state {
            text-align: center;
            padding: 100px;
            color: #94a3b8;
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
        <a href="view_messages.php">Messages</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="ticket-container">
    <h2 style="margin-bottom: 30px; color: #1e293b;">📬 Customer Support Tickets</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): 
            // Logic to pick a color class based on subject
            $subject = $row['subject'];
            $tagClass = "tag-other";
            if(strpos($subject, 'Billing') !== false) $tagClass = "tag-billing";
            if(strpos($subject, 'Meter') !== false) $tagClass = "tag-meter";
            if(strpos($subject, 'Payment') !== false) $tagClass = "tag-payment";
        ?>
            <div class="ticket-card">
                <div class="ticket-header">
                    <div class="sender-info">
                        <h4><?= $row['sender_name'] ?></h4>
                        <small><?= $row['sender_email'] ?> | User ID: #<?= $row['user_id'] ?></small>
                    </div>
                    <span class="issue-tag <?= $tagClass ?>"><?= $row['subject'] ?></span>
                </div>
                
                <div class="message-body">
                    <strong>Message:</strong><br>
                    <?= nl2br($row['message']) ?>
                </div>

                <div class="timestamp">
                    Sent on: <?= date('d M Y, h:i A', strtotime($row['created_at'])) ?>
                </div>
                
                <div style="margin-top: 10px;">
                    <a href="mailto:<?= $row['sender_email'] ?>" class="btn-sm" style="text-decoration:none; background:#2563eb; color:white; padding:5px 10px; border-radius:4px; font-size:12px;">Reply via Email</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <h1 style="font-size: 60px;">📭</h1>
            <h3>No messages found.</h3>
            <p>All customers are happy!</p>
        </div>
    <?php endif; ?>
</div>

<div class="footer">
    <p>&copy; 2026 EBMS Support Management</p>
</div>

</body>
</html>