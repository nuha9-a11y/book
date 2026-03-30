<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];

        $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            $new_image_name = time() . "_" . rand(1000,9999) . "." . $ext;
            $target_path = "images/" . $new_image_name;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $title = $conn->real_escape_string($title);
                $author = $conn->real_escape_string($author);
                $description = $conn->real_escape_string($description);
                $price = (float)$price;

                $sql = "INSERT INTO books (user_id, title, author, price, description, image)
                        VALUES ('$user_id', '$title', '$author', '$price', '$description', '$new_image_name')";

                if ($conn->query($sql) === TRUE) {
                    header("Location: books.php");
                    exit();
                } else {
                    $message = "خطأ في قاعدة البيانات";
                }
            } else {
                $message = "فشل رفع الصورة";
            }
        } else {
            $message = "صيغة الصورة غير مدعومة";
        }
    } else {
        $message = "اختاري صورة للكتاب";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إضافة كتاب</title>
<style>
body{
    margin:0;
    font-family:Arial, sans-serif;
    background:#f6f1ec;
}
.wrapper{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
}
.box{
    width:100%;
    max-width:420px;
    background:white;
    padding:25px;
    border-radius:16px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}
h2{
    text-align:center;
    margin-bottom:20px;
    color:#5a3e2b;
}
input, textarea, button{
    width:100%;
    padding:12px;
    margin-bottom:12px;
    border-radius:10px;
    border:1px solid #ccc;
    box-sizing:border-box;
    font-family:inherit;
}
textarea{
    min-height:100px;
    resize:vertical;
}
button{
    background:#8b5e3c;
    color:white;
    border:none;
    cursor:pointer;
}
button:hover{
    background:#6b4a35;
}
.message{
    text-align:center;
    color:#b33b3b;
    margin-bottom:10px;
}
.back{
    text-align:center;
    margin-top:10px;
}
.back a{
    color:#5a3e2b;
    text-decoration:none;
}
</style>
</head>
<body>

<div class="wrapper">
    <div class="box">
        <h2>إضافة كتاب جديد</h2>

        <?php if($message != ""): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="اسم الكتاب" required>
            <input type="text" name="author" placeholder="اسم الكاتب" required>
            <input type="number" step="0.01" name="price" placeholder="السعر" required>
            <textarea name="description" placeholder="وصف الكتاب" required></textarea>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
            <button type="submit">إضافة الكتاب</button>
        </form>

        <div class="back">
            <a href="books.php">الرجوع للكتب</a>
        </div>
    </div>
</div>

</body>
</html>