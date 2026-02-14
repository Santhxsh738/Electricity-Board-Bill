<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Total Unpaid Amount calculation
$total_unpaid_q = $conn->query("SELECT SUM(amount) as total FROM bills WHERE status='Unpaid'");
$total_data = $total_unpaid_q->fetch_assoc();
$total_amt = $total_data['total'] ?? 0;

// Fetch unpaid bills with User Names (using JOIN)
$sql = "SELECT bills.*, users.name as customer_name 
        FROM bills 
        JOIN users ON bills.user_id = users.id 
        WHERE bills.status = 'Unpaid' 
        ORDER BY bills.id DESC";
$q = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unpaid List | EBMS Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f8fafc; }
        
        /* Stats Card for Unpaid total */
        .unpaid-banner {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 8px solid #ef4444;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .search-container input {
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            width: 100%;
            outline: none;
        }

        .status-badge {
            background: #fee2e2;
            color: #ef4444;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        tr:hover { background: #fff1f2 !important; transition: 0.2s; }
        
        .warn-icon { font-size: 20px; margin-right: 10px; }
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
    
    <div class="unpaid-banner">
        <div>
            <h4 style="color: #64748b; margin-bottom: 5px;">TOTAL PENDING COLLECTION</h4>
            <h1 style="color: #ef4444;">₹ <?= number_format($total_amt, 2) ?></h1>
        </div>
        <div style="text-align: right;">
            <p style="font-weight: bold; color: #1e293b;">Pending Records</p>
            <h2 style="color: #1e293b;"><?= $q->num_rows ?> Bills</h2>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>❌ Unpaid Revenue List</h2>
    </div>

    <div class="search-container">
        <input type="text" id="adminSearch" onkeyup="filterTable()" placeholder="Search by Customer Name or Consumer No...">
    </div>

    <table id="unpaidTable">
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
            <td><span class="status-badge">Pending</span></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php if($q->num_rows == 0){ ?>
        <div style="text-align:center; padding: 40px;">
            <h3 style="color: #10b981;">🎉 All bills are paid! No pending records.</h3>
        </div>
    <?php } ?>
</div>

<script>
function filterTable() {
    let input = document.getElementById("adminSearch");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("unpaidTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let nameCol = tr[i].getElementsByTagName("td")[1];
        let idCol = tr[i].getElementsByTagName("td")[2];
        if (nameCol || idCol) {
            let txtValue = (nameCol.textContent || nameCol.innerText) + (idCol.textContent || idCol.innerText);
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>

<div class="footer">
    <p>&copy; 2026 EBMS | Admin Revenue Monitoring</p>
</div>

</body>
</html>