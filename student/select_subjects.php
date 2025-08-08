<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['student_logged_in'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Get student's department
$res = $conn->query("SELECT department_id FROM students WHERE id = $student_id");
$student_department_id = $res->fetch_assoc()['department_id'];

// Get electives: those open to all or for student's department
$electives = $conn->query("SELECT * FROM electives WHERE department_id IS NULL OR department_id = $student_department_id");

// Get student's current selections
$student_selections = [];
$res = $conn->query("SELECT subject_id FROM selections WHERE student_id = $student_id");
while ($row = $res->fetch_assoc()) {
    $student_selections[] = $row['subject_id'];
}

// Handle selection form
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['subject_id'])) {
    $subject_id = (int) $_POST['subject_id'];

    // Check if already selected subject from same elective
    $check = $conn->query("SELECT s.id FROM selections sel
        JOIN subjects s ON sel.subject_id = s.id
        WHERE sel.student_id = $student_id AND s.elective_id = (
            SELECT elective_id FROM subjects WHERE id = $subject_id
        )");

    if ($check->num_rows > 0) {
        $message = "<p class='error'>⚠️ You have already selected a subject from this elective.</p>";
    } else {
        // Check seats
        $check_seat = $conn->query("SELECT seats_left FROM subjects WHERE id = $subject_id");
        $row = $check_seat->fetch_assoc();
        if ($row['seats_left'] <= 0) {
            $message = "<p class='error'>❌ No seats left in this subject.</p>";
        } else {
            // Insert selection
            $conn->query("INSERT INTO selections (student_id, subject_id) VALUES ($student_id, $subject_id)");
            $conn->query("UPDATE subjects SET seats_left = seats_left - 1 WHERE id = $subject_id");
            $message = "<p class='success'>✅ Subject selected successfully!</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Subjects</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 850px;
            margin: auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.06);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        h3 {
            margin-top: 20px;
            color: #34495e;
        }
        .form-section {
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        button {
            padding: 10px 18px;
            background-color: #2e8b57;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #247144;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 6px solid #28a745;
            border-radius: 5px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 6px solid #dc3545;
            border-radius: 5px;
        }
        a.logout {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            color: #2e8b57;
            font-weight: bold;
        }
        a.logout:hover {
            color: #226b44;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🎯 Available Electives & Subjects</h2>

    <?php if (isset($message)) echo $message; ?>

    <?php while ($elective = $electives->fetch_assoc()): ?>
        <div class="form-section">
            <h3>📘 <?= htmlspecialchars($elective['name']) ?></h3>
            <form method="post">
                <?php
                $eid = $elective['id'];
                $subjects = $conn->query("SELECT * FROM subjects WHERE elective_id = $eid");
                while ($subject = $subjects->fetch_assoc()):
                    $sid = $subject['id'];
                    $seats = $subject['seats_left'];
                    $disabled = in_array($sid, $student_selections) || $seats <= 0;
                ?>
                    <label>
                        <input type="radio" name="subject_id" value="<?= $sid ?>" <?= $disabled ? 'disabled' : '' ?>>
                        <?= htmlspecialchars($subject['name']) ?> (Seats left: <?= $seats ?>)
                    </label><br>
                <?php endwhile; ?>
                <button type="submit">Select</button>
            </form>
        </div>
    <?php endwhile; ?>

    <a class="logout" href="logout.php">🚪 Logout</a>
</div>

</body>
</html>
