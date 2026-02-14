<?php
include "db.php";

$msg = "";
$status = "";

if (isset($_POST['register'])) {
    $name  = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $pass  = $conn->real_escape_string($_POST['password']);

    // Check email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $msg = "This email is already registered!";
        $status = "error";
    } else {
        $q = $conn->query("INSERT INTO users (name, email, password, role) 
                          VALUES ('$name', '$email', '$pass', 'customer')");
        if($q) {
            $msg = "Account created! You can now login.";
            $status = "success";
        } else {
            $msg = "Something went wrong. Try again.";
            $status = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join EBMS | Create Account</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), 
                        url('image/img1.jpeg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.98);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            text-align: center;
            transition: 0.3s;
        }

        .register-card h2 {
            font-size: 28px;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .register-card p {
            color: #64748b;
            margin-bottom: 25px;
            font-size: 15px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-register {
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-register:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }

        .alert {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .alert-error { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
        .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

        .login-link {
            margin-top: 20px;
            font-size: 14px;
            color: #64748b;
        }

        .login-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="register-card">
    <div style="font-size: 45px; margin-bottom: 10px;">⚡</div>
    <h2>Create Account</h2>
    <p>Join EBMS to manage your electricity bills online</p>

    <?php if($msg != ""){ ?>
        <div class="alert <?= ($status == 'success') ? 'alert-success' : 'alert-error' ?>">
            <?= ($status == 'success') ? '✅' : '⚠️' ?> <?= $msg ?>
        </div>
    <?php } ?>

    <form method="post">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="name@example.com" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Minimum 6 characters" required>
        </div>

        <button name="register" class="btn-register">Get Started</button>
    </form>

    <div class="login-link">
        Already have an account? <a href="login.php">Sign In</a>
    </div>
</div>

</body>
</html>