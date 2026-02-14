<?php
session_start();
include "db.php";

if(isset($_POST['login'])){
    $email = $conn->real_escape_string($_POST['email']);
    $pass = $conn->real_escape_string($_POST['password']);

    $q = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$pass'");
    $r = $q->fetch_assoc();

    if($r){
        $_SESSION['user'] = $r['name'];
        $_SESSION['role'] = $r['role'];
        $_SESSION['uid']  = $r['id'];

        if($r['role']=='admin')
            header("Location: admin_home.php");
        else
            header("Location: home.php");
    } else {
        $msg = "Invalid Email or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login | EBMS</title>
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
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card h2 {
            font-size: 30px;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .login-card p {
            color: #64748b;
            margin-bottom: 30px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-login {
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .error-box {
            background: #fee2e2;
            color: #ef4444;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .register-link {
            margin-top: 25px;
            font-size: 14px;
            color: #64748b;
        }

        .register-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div style="font-size: 50px; margin-bottom: 10px;">⚡</div>
    <h2>Welcome! </h2>
    <p>Sign in to manage your EBB account</p>

    <?php if(isset($msg)){ ?>
        <div class="error-box">⚠️ <?=$msg?></div>
    <?php } ?>

    <form method="post">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="name@example.com" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button name="login" class="btn-login">Sign In</button>
    </form>

    <div class="register-link">
        Don't have an account? <a href="register.php">Create one now</a>
    </div>
</div>

</body>
</html>