<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Total Revenue calculation
$revenue_q = $conn->query("SELECT SUM(amount) as total FROM bills WHERE status='Paid'");
$rev_data = $revenue_q->fetch_assoc();
$total_revenue = $rev_data['total'] ?? 0;

// Fetch paid bills with User Names
$sql = "SELECT bills.*, users.name as customer_name 
        FROM bills 
        JOIN users ON bills.user_id = users.id 
        WHERE bills.status = 'Paid' 
        ORDER BY bills.id DESC";
$q = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Paid Records | EBMS Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f8fafc; }
        
        /* Stats Card for Revenue */
        .revenue-banner {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 8px solid #10b981;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            width: 100%;
            outline: none;
            transition: 0.3s;
        }
        .search-container input:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }

        .status-badge-paid {
            background: #dcfce7;
            color: #10b981;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        tr:hover { background: #f0fdf4 !important; transition: 0.2s; }

        /* Print Button - Bonus */
        .btn-print {
            background: #64748b;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            float: right;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>⚡ EBMS Admin</h2>
    <div>
        <a href="admin_home.php">Dashboard</a>
        <a href="paid_list.php">Paid List</a>
        <a href="unpaid_list.php">Unpaid List</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="page-box" style="width:95%; max-width: 1200px;">
    
    <div class="revenue-banner">
        <div>
            <h4 style="color: #64748b; margin-bottom: 5px;">TOTAL COLLECTION (PAID)</h4>
            <h1 style="color: #10b981;">₹ <?= number_format($total_revenue, 2) ?></h1>
        </div>
        <div style="text-align: right;">
            <p style="font-weight: bold; color: #1e293b;">Verified Transactions</p>
            <h2 style="color: #1e293b;"><?= $q->num_rows ?> Invoices</h2>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>✅ Success Payment Records</h2>
        <a href="javascript:window.print()" class="btn-print">🖨️ Print Report</a>
    </div>

    <div class="search-container">
        <input type="text" id="paidSearch" onkeyup="filterTable()" placeholder="Search by Customer Name, Consumer No or Month...">
    </div>

    <table id="paidTable">
        <thead>
            <tr style="background: #f1f5f9;">
                <th>Cust ID</th>
                <th>Customer Name</th>
                <th>Consumer No</th>
                <th>Month</th>
                <th>Units</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $q->fetch_assoc()){ ?>
        <tr>
            <td>#<?= $row['user_id'] ?></td>
            <td style="font-weight: bold;"><?= $row['customer_name'] ?></td>
            <td style="font-family: monospace;"><?= $row['consumer_no'] ?></td>
            <td><?= $row['month'] ?></td>
            <td><?= $row['units'] ?> kWh</td>
            <td style="font-weight: bold; color: #1e293b;">₹ <?= number_format($row['amount'], 2) ?></td>
            <td><span class="status-badge-paid">Verified</span></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php if($q->num_rows == 0){ ?>
        <div style="text-align:center; padding: 40px;">
            <p style="color: #64748b;">No successful payments recorded yet.</p>
        </div>
    <?php } ?>
</div>

<script>
function filterTable() {
    let input = document.getElementById("paidSearch");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("paidTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let content = tr[i].textContent || tr[i].innerText;
        if (content.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}
</script>

<div class="footer">
    <p>&copy; 2026 EBMS | Verified Financial Records</p>
</div>

</body>
</html>