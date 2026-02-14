<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['uid'];

// Fetch stats for the top cards
$paid_q = $conn->query("SELECT SUM(amount) as total FROM bills WHERE user_id='$uid' AND status='Paid'");
$unpaid_q = $conn->query("SELECT SUM(amount) as total FROM bills WHERE user_id='$uid' AND status='Unpaid'");
$paid_amount = $paid_q->fetch_assoc()['total'] ?? 0;
$unpaid_amount = $unpaid_q->fetch_assoc()['total'] ?? 0;

// Fetch all bills
$q = $conn->query("SELECT * FROM bills WHERE user_id='$uid' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Bills | EBMS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Modern Table & Card Styling */
        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            flex: 1;
            padding: 20px;
            border-radius: 10px;
            color: white;
            text-align: center;
        }
        .search-box {
            margin-bottom: 20px;
            width: 100%;
            display: flex;
            gap: 10px;
        }
        tr:hover {
            background-color: #f8fafc !important;
            transition: 0.3s;
        }
        .status-pill {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }
        .paid-pill { background: #dcfce7; color: #10b981; }
        .unpaid-pill { background: #fee2e2; color: #ef4444; }
        
        /* Action Buttons */
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .btn-download { background: #64748b; color: white; margin-left: 5px; }
    </style>
</head>
<body>

<div class="navbar">
  <h2>⚡ EBMS</h2>
  <div>
    <a href="home.php">Home</a>
    <a href="viewbill.php">View Bills</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>
</div>

<div class="page-box" style="width:95%; max-width: 1200px;">
    <h2>🧾 Billing History</h2>
    <p style="color: #64748b; margin-bottom: 25px;">Track all your electricity consumption and payments here.</p>

    <div class="stats-row">
        <div class="stat-card" style="background: #ef4444;">
            <small>Total Outstanding</small>
            <h2>₹ <?=$unpaid_amount?></h2>
        </div>
        <div class="stat-card" style="background: #10b981;">
            <small>Total Paid to Date</small>
            <h2>₹ <?=$paid_amount?></h2>
        </div>
        <div class="stat-card" style="background: #2563eb;">
            <small>Total Record(s)</small>
            <h2><?=$q->num_rows?> Bills</h2>
        </div>
    </div>

    <div class="search-box">
        <input type="text" id="myInput" onkeyup="searchTable()" placeholder="Search by month (e.g. January)..." style="margin: 0; flex: 1;">
    </div>

    <?php if($q->num_rows > 0){ ?>
    <table id="billTable">
        <thead>
            <tr>
                <th>Consumer No</th>
                <th>Month</th>
                <th>Units</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($bill = $q->fetch_assoc()){ ?>
        <tr>
            <td style="font-family: monospace; font-weight: bold;"><?=$bill['consumer_no']?></td>
            <td><?=$bill['month']?></td>
            <td><?=$bill['units']?> kWh</td>
            <td style="font-weight: bold;">₹ <?=$bill['amount']?></td>
            <td>
                <?php if($bill['status']=="Paid"){ ?>
                    <span class="status-pill paid-pill">Paid</span>
                <?php } else { ?>
                    <span class="status-pill unpaid-pill">Unpaid</span>
                <?php } ?>
            </td>
            <td>
                <?php if($bill['status']=="Unpaid"){ ?>
                    <a href="paybill.php?id=<?=$bill['id']?>" class="btn btn-sm" style="background: #2563eb; color:white;">Pay Now</a>
                <?php } else { ?>
                    <span style="color: #10b981; font-weight: bold;">✔ Done</span>
                    <a href="#" class="btn-sm btn-download" title="Download Receipt">📄 PDF</a>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php } else { ?>
        <div style="text-align:center; padding: 50px;">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" style="opacity: 0.2;">
            <p style="color:#64748b; margin-top: 20px;">No billing records found in your account.</p>
        </div>
    <?php } ?>
</div>

<script>
function searchTable() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("billTable");
  tr = table.getElementsByTagName("tr");
  for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1]; // Search by Month (Column 2)
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>

</body>
</html>