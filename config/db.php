<?php
$host = 'localhost';
$user = 'hari';
$pass = 'newpassword123'; // If password exists, write it here
$db = 'elective_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
