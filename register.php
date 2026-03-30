<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // تحقق إذا الايميل موجود
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");

    if ($check->num_rows > 0) {
        $message = "الإيميل مستخدم من قبل";
    } else {

        $sql = "INSERT INTO users (username, email, password)
                VALUES ('$username', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {

            $new_user_id = $conn->insert_id;

            // تسجيل دخول تلقائي
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['username'] = $username;

            // تحويل للكتب مباشرة
            header("Location: books.php");
            exit();
        } else {
            $message = "خطأ: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <div class="auth-box">
        <h2>إنشاء حساب</h2>

        <?php if($message != ""): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="اسم المستخدم" required>
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <button type="submit">إنشاء الحساب</button>
        </form>

        <p class="back-link">
            لديك حساب؟ <a href="login.php">تسجيل دخول</a>
        </p>
    </div>
</div>

</body>
</html>