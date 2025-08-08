<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['dept_admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$dept_id = $_SESSION['dept_id'];

// Get electives of this department
$electives = $conn->query("SELECT * FROM electives WHERE department_id = $dept_id");

echo "<h2>Department Admin Dashboard</h2>";

while ($elective = $electives->fetch_assoc()) {
    echo "<h3>Elective: " . htmlspecialchars($elective['name']) . "</h3>";

    $eid = $elective['id'];
    $subjects = $conn->query("SELECT * FROM subjects WHERE elective_id = $eid");

    while ($sub = $subjects->fetch_assoc()) {
        echo "<p>📘 Subject: " . htmlspecialchars($sub['name']) . " | Seats Left: " . $sub['seats_left'] . "</p>";

        // Students who selected this subject
        $sid = $sub['id'];
        $students = $conn->query("SELECT s.name, s.email FROM selections sel 
                                  JOIN students s ON sel.student_id = s.id 
                                  WHERE sel.subject_id = $sid AND s.department_id = $dept_id");

        if ($students->num_rows > 0) {
            echo "<ul>";
            while ($stu = $students->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($stu['name']) . " (" . htmlspecialchars($stu['email']) . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No students selected this subject.</p>";
        }
    }

    echo "<hr>";
}
?>
