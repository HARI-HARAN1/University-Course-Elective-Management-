<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $elective_name = $_POST['elective_name'];
    $stmt = $conn->prepare("INSERT INTO electives (name) VALUES (?)");
    $stmt->bind_param("s", $elective_name);
    $stmt->execute();
    echo "✅ Elective added successfully!";
}
?>

<h2>Add Elective</h2>
<form method="post">
    <input type="text" name="elective_name" required placeholder="Elective Name"><br><br>
    <button type="submit">Add Elective</button>
</form>

<br>
<a href="dashboard.php">← Back to Dashboard</a>
