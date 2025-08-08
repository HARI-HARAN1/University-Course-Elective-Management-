<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['student_logged_in'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch selections with subject and elective names
$sql = "
SELECT 
    e.name AS elective_name,
    s.name AS subject_name
FROM selections sel
JOIN subjects s ON sel.subject_id = s.id
JOIN electives e ON s.elective_id = e.id
WHERE sel.student_id = $student_id
ORDER BY e.name;
";

$result = $conn->query($sql);
$selections = [];

while ($row = $result->fetch_assoc()) {
    $selections[$row['elective_name']] = $row['subject_name'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .card {
            border: 1px solid #e1e4e8;
            border-left: 6px solid #2e8b57;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fafafa;
            transition: background 0.3s;
        }

        .card:hover {
            background-color: #f0f5f3;
        }

        h3 {
            margin: 0 0 10px;
            color: #34495e;
        }

        p {
            margin: 0;
            font-size: 15px;
            color: #555;
        }

        .btn-group {
            text-align: center;
            margin-top: 30px;
        }

        a.button {
            display: inline-block;
            text-decoration: none;
            background-color: #2e8b57;
            color: white;
            padding: 12px 20px;
            margin: 5px;
            border-radius: 6px;
            font-size: 15px;
            transition: background 0.3s;
        }

        a.button:hover {
            background-color: #226b44;
        }

        .no-selection {
            text-align: center;
            font-style: italic;
            color: #888;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🎓 Student Dashboard</h2>

    <?php if (count($selections) > 0): ?>
        <?php foreach ($selections as $elective => $subject): ?>
            <div class="card">
                <h3>📚 Elective: <?= htmlspecialchars($elective) ?></h3>
                <p>✅ Selected Subject: <strong><?= htmlspecialchars($subject) ?></strong></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-selection">You have not selected any subjects yet.</p>
    <?php endif; ?>

    <div class="btn-group">
        <a class="button" href="select_subjects.php">📝 Go to Subject Selection</a>
        <a class="button" href="logout.php">🚪 Logout</a>
    </div>
</div>

</body>
</html>
