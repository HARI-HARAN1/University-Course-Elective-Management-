<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch all electives to show in dropdown
$electives = $conn->query("SELECT * FROM electives");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $elective_id = $_POST['elective_id'];
    $subject_name = $_POST['subject_name'];
    $total_seats = (int) $_POST['total_seats'];

    $stmt = $conn->prepare("INSERT INTO subjects (elective_id, name, total_seats, seats_left) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $elective_id, $subject_name, $total_seats, $total_seats);
    $stmt->execute();

    echo "✅ Subject added successfully!";
}
?>

<h2>Add Subject to Elective</h2>
<form method="post">
    <label>Select Elective:</label><br>
    <select name="elective_id" required>
        <option value="">-- Select Elective --</option>
        <?php while ($row = $electives->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <input type="text" name="subject_name" required placeholder="Subject Name"><br><br>
    <input type="number" name="total_seats" required min="1" placeholder="Total Seats"><br><br>

    <button type="submit">Add Subject</button>
</form>

<br>
<a href="dashboard.php">← Back to Dashboard</a>
