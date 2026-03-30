<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: books.php");
            exit();
        } else {
            $message = "كلمة المرور غير صحيحة";
        }
    } else {
        $message = "البريد الإلكتروني غير موجود";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <div class="auth-box">
        <h2>تسجيل الدخول</h2>

        <?php if($message != ""): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <button type="submit">دخول</button>
        </form>

        <p class="back-link">
            ليس لديك حساب؟ <a href="register.php">إنشاء حساب</a>
        </p>
    </div>
</div>

</body>
</html>