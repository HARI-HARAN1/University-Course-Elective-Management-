<?php
session_start();
require '../config/db.php';

// Optional: Add session check for super admin

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dept_id = $_POST['department_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $exists = $conn->query("SELECT * FROM department_admins WHERE email = '$email'");
    if ($exists->num_rows > 0) {
        $message = "❌ Email already registered!";
    } else {
        $sql = "INSERT INTO department_admins (name, email, password, department_id)
                VALUES ('$name', '$email', '$password', $dept_id)";
        if ($conn->query($sql)) {
            $message = "✅ Department Admin created successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}
?>

<h2>Create Department Admin</h2>
<?php if (isset($message)) echo "<p>$message</p>"; ?>
<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>
    <label>Department:</label><br>
    <select name="department_id" required>
        <?php
        $depts = $conn->query("SELECT * FROM departments");
        while ($d = $depts->fetch_assoc()) {
            echo "<option value='{$d['id']}'>{$d['name']}</option>";
        }
        ?>
    </select><br><br>
    <button type="submit">Create Admin</button>
</form>
