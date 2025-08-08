<?php
session_start();
require '../config/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email is registered
    $result = $conn->query("SELECT * FROM students WHERE email = '$email'");

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            if ($user['is_verified']) {
                // ✅ Save session
                $_SESSION['student_logged_in'] = true;
                $_SESSION['student_id'] = $user['id'];
                $_SESSION['department_id'] = $user['department_id']; // ✅ Optional: store department too

                header("Location: dashboard.php");
                exit;
            } else {
                $message = "⚠️ Please verify your email before logging in.";
            }
        } else {
            $message = "❌ Incorrect password!";
        }
    } else {
        $message = "❌ Email not registered!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f0f2f5;
        }
        form {
            max-width: 400px;
            margin: auto;
            padding: 25px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background-color: #2e8b57;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #276d47;
        }
        .error {
            color: red;
            text-align: center;
        }
        .link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">🎓 Student Login</h2>

<?php if ($message): ?>
    <p class="error"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Email:</label>
    <input type="email" name="email" required placeholder="Enter your registered email">

    <label>Password:</label>
    <input type="password" name="password" required placeholder="Enter your password">

    <button type="submit">Login</button>

    <div class="link">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</form>

</body>
</html>
