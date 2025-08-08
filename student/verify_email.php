<?php
require '../config/db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $check = $conn->query("SELECT * FROM students WHERE token = '$token'");
    if ($check->num_rows === 1) {
        $conn->query("UPDATE students SET verified = 1, token = NULL WHERE token = '$token'");
        echo "<h3>✅ Email verified successfully! You can now <a href='login.php'>Login</a>.</h3>";
    } else {
        echo "❌ Invalid or expired verification link.";
    }
} else {
    echo "❌ No token provided.";
}
