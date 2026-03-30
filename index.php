<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>متجر الكتب</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="landing-page">

<header class="main-header">
    <div class="container nav">
        <div class="logo-box">
           
            <h1>متجر الكتب</h1>
        </div>

        <nav class="main-nav">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="books.php">الكتب</a>
                <a href="add_book.php">إضافة كتاب</a>
                <a href="logout.php">تسجيل خروج</a>
            <?php else: ?>
                <a href="login.php">تسجيل دخول</a>
                <a href="register.php">إنشاء حساب</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<section class="hero hero-landing">
    <div class="overlay"></div>
    <div class="container hero-content">
        <h2>عالم الكتب بين يديك</h2>
       

        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="books.php" class="btn">الدخول إلى الكتب</a>
        <?php else: ?>
            <div class="hero-buttons">
                <a href="login.php" class="btn">تسجيل دخول</a>
                <a href="register.php" class="btn btn-light">إنشاء حساب</a>
            </div>
        <?php endif; ?>
    </div>
</section>

</body>
</html>