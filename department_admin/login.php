<?php
session_start();
require '../config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM department_admins WHERE email = '$email'");
    if ($res && $res->num_rows > 0) {
        $admin = $res->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['dept_admin_logged_in'] = true;
            $_SESSION['dept_admin_id'] = $admin['id'];
            $_SESSION['dept_id'] = $admin['department_id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "❌ Incorrect password!";
        }
    } else {
        $message = "❌ Email not registered!";
    }
}
?>
<!-- Add styled HTML similar to your student login -->
<!DOCTYPE html>
<html>
<head>
    <title>Department Admin Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 420px;
            margin: auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #2e8b57;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #247144;
        }
        .error {
            color: #d8000c;
            background-color: #ffbaba;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🔐 Department Admin Login</h2>

    <?php if (!empty($message)): ?>
        <div class="error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required placeholder="Enter your email">

        <label for="password">Password:</label>
        <input type="password" name="password" required placeholder="Enter your password">

        <button type="submit">Login</button>
    </form>

    <div class="footer">
        Need help? Contact Super Admin
    </div>
</div>

</body>
</html>
