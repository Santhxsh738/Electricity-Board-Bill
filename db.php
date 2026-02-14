<?php
$conn = new mysqli("localhost","root","","eb");
if ($conn->connect_error) {
    die("DB Error");
}
?>
