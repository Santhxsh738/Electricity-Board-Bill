<?php
session_start();
include "db.php"; // Database connection path check pannikonga

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['uid'];
$userName = $_SESSION['user'];
$success_msg = "";

// --- INGA THAAN ANDHA CODE-A PODANUM ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO messages (user_id, subject, message) VALUES ('$uid', '$subject', '$message')";
    
    if ($conn->query($sql)) {
        $success_msg = "Your ticket has been raised successfully! Our team will contact you soon.";
    } else {
        $error_msg = "Error: " . $conn->error;
    }
}
// --- CODE ENDS HERE ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Contact Support</title>
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

<div class="page-box">
    <h2>Raise a Ticket</h2>

    <?php if($success_msg){ ?>
        <div style="background: #dcfce7; color: #10b981; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center;">
            <?=$success_msg?>
        </div>
    <?php } ?>

    <form action="contact.php" method="POST">
        <label>Subject</label>
        <select name="subject" required>
            <option value="">Select an Issue</option>
            <option value="Billing Issue">Billing Error</option>
            <option value="Meter Issue">Meter Not Working</option>
            <option value="Other">Other Queries</option>
        </select>

        <label>Message</label>
        <textarea name="message" rows="5" placeholder="Describe your issue..." style="width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc;"></textarea>
        
        <button type="submit">Submit Ticket</button>
    </form>
</div>

</body>
</html>