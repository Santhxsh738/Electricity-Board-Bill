<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";
if(isset($_POST['generate'])){
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $consumer = $conn->real_escape_string($_POST['consumer']);
    $month_name = $conn->real_escape_string($_POST['month_name']);
    $year = date("Y"); // Automatically takes current year
    $full_month = $month_name . " " . $year; // Combines like "January 2026"
    
    $units = intval($_POST['units']);
    $amount = $units * 5; 
    
    $sql = "INSERT INTO bills (user_id, consumer_no, month, units, amount, status)
            VALUES ('$user_id', '$consumer', '$full_month', '$units', '$amount', 'Unpaid')";
            
    if($conn->query($sql)){
        $msg = "Success: Bill generated for $full_month";
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Bill | EBMS Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f1f5f9; }
        .billing-container { max-width: 700px; margin: 40px auto; }
        .bill-form-box {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-top: 5px solid #2563eb;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #1e293b; }
        
        select, input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }
        select:focus, input:focus { border-color: #2563eb; }

        .calc-preview {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px dashed #2563eb;
            text-align: center;
        }

        .success-alert {
            background: #dcfce7;
            color: #15803d;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>⚡ EBMS Admin</h2>
    <div>
        <a href="admin_home.php">Dashboard</a>
        <a href="managecustomer.php">Users</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="billing-container">
    <div class="bill-form-box">
        <h2>📝 Generate Bill</h2>
        <p style="color: #64748b; margin-bottom: 30px;">Select a month and enter units for the customer.</p>

        <?php if($msg != ""){ ?>
            <div class="success-alert"><?= $msg ?></div>
        <?php } ?>

        <form method="post">
            <div class="form-group">
                <label>Select Customer</label>
                <select name="user_id" required>
                    <option value="">-- Choose Customer --</option>
                    <?php
                    $users = $conn->query("SELECT id, name FROM users WHERE role='customer'");
                    while($u = $users->fetch_assoc()){
                        echo "<option value='{$u['id']}'>{$u['name']} (ID: {$u['id']})</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Consumer Number</label>
                <input type="text" name="consumer" placeholder="EB-XXXX-XXXX" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Select Month</label>
                    <select name="month_name" required>
                        <?php
                        $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                        foreach ($months as $m) {
                            $selected = (date('F') == $m) ? "selected" : ""; // Current month auto-selected
                            echo "<option value='$m' $selected>$m</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Units Consumed</label>
                    <input type="number" name="units" id="units" placeholder="0" required oninput="calculateAmount()">
                </div>
            </div>

            <div class="calc-preview">
                <p style="color: #64748b; margin-bottom: 5px;">Live Bill Preview</p>
                <h1 id="amt_display" style="color: #1e293b;">₹ 0</h1>
                <small>Fixed Rate: ₹5 / unit</small>
            </div>

            <button name="generate" class="btn" style="width: 100%; padding: 15px; font-weight: bold;">Generate Bill Now</button>
        </form>
    </div>
</div>



<script>
function calculateAmount() {
    let units = document.getElementById('units').value;
    let total = units * 5;
    document.getElementById('amt_display').innerText = "₹ " + (total ? total : 0);
}
</script>

<div class="footer">
    <p>&copy; 2026 EBMS | Administration Control</p>
</div>

</body>
</html>