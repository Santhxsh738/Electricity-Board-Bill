<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

/* DELETE CUSTOMER */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Security: converted to integer
    $conn->query("DELETE FROM users WHERE id=$id AND role='customer'");
    // Also delete their bills if needed, or keep them for records.
    header("Location: managecustomer.php?msg=User Deleted");
    exit();
}

// Fetch total count for stats
$count_q = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='customer'");
$total_users = $count_q->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers | EBMS Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f1f5f9; }
        
        .user-stats-bar {
            background: #fff;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid #2563eb;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 12px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            width: 100%;
            outline: none;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .search-container input:focus { border-color: #2563eb; width: 100%; }

        .user-initial {
            width: 40px;
            height: 40px;
            background: #e2e8f0;
            color: #2563eb;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }

        .role-badge {
            background: #e0f2fe;
            color: #0369a1;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .btn-delete {
            background: #fee2e2;
            color: #ef4444;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-delete:hover { background: #ef4444; color: #fff; }

        tr:hover { background: #f8fafc !important; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>⚡ EBMS Admin</h2>
    <div>
        <a href="admin_home.php">Dashboard</a>
        <a href="managecustomer.php">Manage Users</a>
        <a href="generate_bill.php">Generate Bill</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="page-box" style="width:95%; max-width: 1200px;">
    
    <div class="user-stats-bar">
        <div>
            <h4 style="color: #64748b; margin: 0;">TOTAL CUSTOMERS</h4>
            <h2 style="color: #1e293b; margin: 5px 0;"><?= $total_users ?> Users Registered</h2>
        </div>
        <div>
            <span style="color: #10b981; font-weight: bold;">● System Online</span>
        </div>
    </div>

    <h2 style="margin-bottom: 20px;">👥 Customer Directory</h2>

    <div class="search-container">
        <input type="text" id="userSearch" onkeyup="searchUsers()" placeholder="Search by name or email address...">
    </div>

    <table id="userTable">
        <thead>
            <tr style="background: #f8fafc;">
                <th>ID</th>
                <th>Customer Details</th>
                <th>Email</th>
                <th>Account Role</th>
                <th>Control</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM users WHERE role='customer' ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            $initial = substr($row['name'], 0, 1);
        ?>
        <tr>
            <td>#<?= $row['id'] ?></td>
            <td style="text-align: left; padding-left: 30px;">
                <div style="display: flex; align-items: center;">
                    <div class="user-initial"><?= strtoupper($initial) ?></div>
                    <span style="font-weight: 600; color: #1e293b;"><?= $row['name'] ?></span>
                </div>
            </td>
            <td style="color: #64748b;"><?= $row['email'] ?></td>
            <td><span class="role-badge"><?= $row['role'] ?></span></td>
            <td>
                <a href="managecustomer.php?delete=<?= $row['id'] ?>" 
                   onclick="return confirm('Are you sure? This will permanently remove the customer account.')" 
                   class="btn-delete">Remove User</a>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php if($result->num_rows == 0){ ?>
        <div style="text-align: center; padding: 50px;">
            <p style="color: #94a3b8;">No customers registered in the system yet.</p>
        </div>
    <?php } ?>
</div>

<script>
function searchUsers() {
    let input = document.getElementById("userSearch");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("userTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let nameCol = tr[i].getElementsByTagName("td")[1];
        let emailCol = tr[i].getElementsByTagName("td")[2];
        if (nameCol || emailCol) {
            let txtValue = (nameCol.textContent || nameCol.innerText) + (emailCol.textContent || emailCol.innerText);
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
    <p>&copy; 2026 EBMS | User Access Management Control</p>
</div>

</body>
</html>