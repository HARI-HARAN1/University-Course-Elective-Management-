<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
?>

<h2>Welcome, Admin</h2>
<ul>
    <li><a href="add_elective.php">Add Electives</a></li>
    <li><a href="add_subject.php">Add Subjects</a></li>
    <li><a href="view_selections.php">View Student Selections</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
