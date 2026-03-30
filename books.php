<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* تأكد من السلة */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* إضافة للسلة */
if (isset($_GET['add'])) {
    $book_id = (int)$_GET['add'];
    $_SESSION['cart'][] = $book_id;
    header("Location: books.php");
    exit();
}

/* لايك */
if (isset($_GET['like'])) {
    $book_id = (int)$_GET['like'];

    $check = $conn->query("SELECT id FROM likes WHERE book_id=$book_id AND user_id=$user_id");
    if ($check && $check->num_rows == 0) {
        $conn->query("INSERT INTO likes (book_id, user_id) VALUES ($book_id,$user_id)");
    }

    header("Location: books.php");
    exit();
}

/* حذف كتاب */
if (isset($_GET['delete'])) {
    $book_id = (int)$_GET['delete'];

    $get_book = $conn->query("SELECT * FROM books WHERE id=$book_id AND user_id=$user_id");
    if ($get_book && $get_book->num_rows > 0) {
        $book = $get_book->fetch_assoc();
        $image_path = "images/" . $book['image'];

        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $conn->query("DELETE FROM comments WHERE book_id=$book_id");
        $conn->query("DELETE FROM likes WHERE book_id=$book_id");
        $conn->query("DELETE FROM books WHERE id=$book_id AND user_id=$user_id");
    }

    header("Location: books.php");
    exit();
}

/* إضافة تعليق */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    $book_id = (int)$_POST['book_id'];
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
        $comment = $conn->real_escape_string($comment);
        $conn->query("INSERT INTO comments (book_id,user_id,comment) VALUES ($book_id,$user_id,'$comment')");
    }

    header("Location: books.php");
    exit();
}

$result = $conn->query("SELECT * FROM books ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>الكتب</title>
<link rel="stylesheet" href="style.css">
<style>
body{
    margin:0;
    font-family:Arial, sans-serif;
    background:#f6f1ec;
}

header{
    background:#5a3e2b;
    padding:15px 0;
}

.container{
    width:min(92%,1200px);
    margin:auto;
}

.nav{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    flex-wrap:wrap;
}

.logo-box{
    display:flex;
    align-items:center;
    gap:10px;
}

.site-logo{
    width:38px;
    height:38px;
    object-fit:contain;
}

.nav h1{
    color:white;
    margin:0;
}

.nav nav{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
}

.nav a{
    color:white;
    text-decoration:none;
}

.page-top{
    text-align:center;
    margin:30px 0 20px;
}

.add-book-btn{
    display:inline-block;
    background:#8b5e3c;
    color:#fff;
    text-decoration:none;
    padding:12px 20px;
    border-radius:10px;
    margin-top:12px;
}

.add-book-btn:hover{
    background:#6b4a35;
}

.books{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
    gap:22px;
    width:min(92%,1200px);
    margin:0 auto 40px;
}

.card{
    background:#fff;
    border-radius:16px;
    padding:15px;
    text-align:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.card img{
    width:150px;
    height:210px;
    object-fit:cover;
    border-radius:10px;
    margin:auto;
    display:block;
}

.card h3{
    margin:14px 0 8px;
    color:#3e2c23;
}

.author{
    color:#666;
    margin-bottom:8px;
    font-size:14px;
}

.price{
    color:#8b5e3c;
    font-weight:bold;
    margin-bottom:10px;
    font-size:18px;
}

.desc{
    color:#444;
    font-size:14px;
    line-height:1.7;
    margin-bottom:12px;
    min-height:45px;
}

.actions{
    display:flex;
    justify-content:center;
    gap:8px;
    flex-wrap:wrap;
    margin-bottom:10px;
}

.btn{
    background:#8b5e3c;
    color:white;
    padding:8px 12px;
    border-radius:8px;
    text-decoration:none;
    border:none;
    cursor:pointer;
}

.btn:hover{
    background:#6b4a35;
}

.delete{
    background:#c0392b;
}

.delete:hover{
    background:#a93226;
}

.like-box{
    margin:8px 0 14px;
    font-size:15px;
}

textarea{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
    resize:vertical;
    min-height:80px;
    margin-bottom:8px;
    font-family:inherit;
}

.comment{
    background:#f0ece7;
    padding:8px;
    border-radius:8px;
    margin-top:6px;
    text-align:right;
    font-size:14px;
}

.empty{
    text-align:center;
    color:#666;
    margin-top:20px;
}

@media (max-width:768px){
    .nav{
        flex-direction:column;
        text-align:center;
    }

    .books{
        grid-template-columns:1fr;
    }

    .card{
        max-width:320px;
        margin:auto;
    }

    .card img{
        width:140px;
        height:190px;
    }
}
</style>
</head>
<body>

<header>
    <div class="container nav">
        <div class="logo-box">
            <h1>متجر الكتب</h1>
        </div>

        <nav>
            <a href="index.php">الرئيسية</a>
            <a href="books.php">الكتب</a>
            <a href="logout.php">تسجيل خروج</a>
        </nav>
    </div>
</header>

<div class="page-top">
    <h2>الكتب المتوفرة</h2>
    <a href="add_book.php" class="add-book-btn">+ إضافة كتاب جديد</a>
</div>

<div class="books">
<?php if ($result && $result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="card">
            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">

            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p class="author">الكاتب: <?php echo htmlspecialchars($row['author']); ?></p>
            <p class="price"><?php echo $row['price']; ?> ر.س</p>
            <p class="desc"><?php echo htmlspecialchars($row['description']); ?></p>

            <div class="actions">
                <a class="btn" href="?add=<?php echo $row['id']; ?>">سلة</a>
                <a class="btn" href="?like=<?php echo $row['id']; ?>">♥</a>

                <?php if($row['user_id'] == $user_id): ?>
                    <a class="btn delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('متأكد من حذف الكتاب؟')">حذف</a>
                <?php endif; ?>
            </div>

            <?php
            $likes = $conn->query("SELECT COUNT(*) as c FROM likes WHERE book_id=".$row['id']);
            $likes_count = 0;
            if ($likes) {
                $likes_count = $likes->fetch_assoc()['c'];
            }
            ?>
            <div class="like-box">❤️ <?php echo $likes_count; ?></div>

            <form method="POST">
                <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
                <textarea name="comment" placeholder="اكتب تعليقك"></textarea>
                <button class="btn" type="submit">إرسال</button>
            </form>

            <?php
            $comments = $conn->query("SELECT * FROM comments WHERE book_id=".$row['id']." ORDER BY id DESC");
            if ($comments && $comments->num_rows > 0):
                while($c = $comments->fetch_assoc()):
            ?>
                <div class="comment"><?php echo htmlspecialchars($c['comment']); ?></div>
            <?php
                endwhile;
            endif;
            ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="empty">
        <p>لا توجد كتب بعد</p>
        <a href="add_book.php" class="add-book-btn">+ أضف أول كتاب</a>
    </div>
<?php endif; ?>
</div>

</body>
</html>