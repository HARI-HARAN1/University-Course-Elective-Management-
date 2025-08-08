<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$query = "
SELECT 
    s.name AS student_name,
    s.email,
    subj.name AS subject_name,
    e.name AS elective_name
FROM selections ss
JOIN students s ON ss.student_id = s.id
JOIN subjects subj ON ss.subject_id = subj.id
JOIN electives e ON subj.elective_id = e.id
ORDER BY e.name, subj.name, s.name
";

$result = $conn->query($query);
?>

<h2>Student Subject Selections</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Student Name</th>
        <th>Email</th>
        <th>Elective</th>
        <th>Subject Chosen</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['elective_name']) ?></td>
            <td><?= htmlspecialchars($row['subject_name']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="dashboard.php">← Back to Dashboard</a>
