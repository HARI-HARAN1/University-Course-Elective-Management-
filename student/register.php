<?php
require '../config/db.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department_id = $_POST['department_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $token = md5(uniqid($email, true));

    $check = $conn->query("SELECT * FROM students WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $message = "⚠️ Email already registered!";
    } else {
        $sql = "INSERT INTO students (name, email, password, token, department_id) 
                VALUES ('$name', '$email', '$password', '$token', $department_id)";

        if ($conn->query($sql)) {
            $verify_link = "http://localhost:8000/student/verify_email.php?token=$token";

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@gmail.com';          // ✅ Your Gmail
                $mail->Password = 'your_app_password';             // ✅ Your App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('your_email@gmail.com', 'College Admin');
                $mail->addAddress($email, $name);
                $mail->Subject = 'Verify Your Email';
                $mail->Body    = "Hello $name,\n\nClick below to verify your email:\n$verify_link";

                $mail->send();
                $message = "✅ Registration successful! Please check your email to verify.";
            } catch (Exception $e) {
                $message = "❌ Email not sent. Use this link to verify: <a href='$verify_link'>$verify_link</a>";
            }
        } else {
            $message = "❌ Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f5f7fa;
        }
        form {
            max-width: 450px;
            margin: auto;
            padding: 25px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
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
        h2 {
            text-align: center;
        }
        .message {
            text-align: center;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>🎓 Student Registration</h2>

<?php if ($message): ?>
    <p class="message"><?= $message ?></p>
<?php endif; ?>

<form method="POST">
    <label>Full Name:</label>
    <input type="text" name="name" required placeholder="Enter your full name">

    <label>Email:</label>
    <input type="email" name="email" required placeholder="Enter your email">

    <label>Password:</label>
    <input type="password" name="password" required placeholder="Enter your password">

    <label>Department:</label>
    <select name="department_id" required>
        <option value="">-- Select Department --</option>
        <?php
        $departments = $conn->query("SELECT * FROM departments");
        while ($dept = $departments->fetch_assoc()):
        ?>
            <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
        <?php endwhile; ?>
    </select>

    <button type="submit">Register</button>
</form>

</body>
</html>
