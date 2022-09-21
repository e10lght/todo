<?php
session_start();
require_once(dirname(__FILE__) . "/../library/library.php");
require_once(dirname(__FILE__) . "/../config/connect.php");
require_once(dirname(__FILE__) . "/../models/db/fetchPicture.php");

// CSRF対策用トークン発行
$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(24));

if (isset($_SESSION["id"]) && isset($_SESSION["name"])) {
    $name = $_SESSION["name"];
    $id = $_SESSION["id"];
} else {
    header("Location: login.php");
    exit();
}

// 写真だけ取得
$pic = getPic($pdo, $id);

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="icon" href="assets/image/favicon.ico">
</head>

<body>
    <div class="container">
        <header>
            <div class="search_box">
                <form action="serach.php" method="post">
                    <span class="fa fa-fw fa-search st-NewHeader_searchIcon"></span>
                    <input type="search" name="search" id="search" placeholder="キーワードを入力">
                </form>
            </div>
            <a href="menu.php"><h1>TODO管理アプリ</h1></a>
            <nav>
                <div class="header_menu">
                    <?php if ($pic) : ?>
                        <img src="pic/<?php echo hsc($pic); ?>" width="48" height="48" alt="" id="img" />
                    <?php else : ?>
                        <img src="assets/image/sample.jpg" width="48" height="48" alt="" id="img" />
                    <?php endif; ?>
                    <p><?php echo hsc($name); ?></p>
                </div>
                <!-- くりっくされたら表示するメニュー -->
                <div class="menu_nav">
                    <ul>
                        <li> <a href="menu.php">メニュー</a>
                        </li>
                        <li> <a href="logout.php">ログアウト</a>
                        </li>
                        <li>改修記録</li>
                    </ul>
                </div>
            </nav>
        </header>
        <a href="menu.php">◀タスク一覧に戻る</a>
    </div>

    <form action="edit-action.php" method="post">
        <input type="hidden" name="token" value="<?php echo hsc($_SESSION['token']) ?>">
        <textarea name="memo1" cols="50" rows="5"></textarea>
        <input type="date" name="date1" class="date">
        <textarea name="memo2" cols="50" rows="5"></textarea>
        <input type="date" name="date2" class="date">
        <textarea name="memo3" cols="50" rows="5"></textarea>
        <input type="date" name="date3" class="date">
        <textarea name="memo4" cols="50" rows="5"></textarea>
        <input type="date" name="date4" class="date">
        <textarea name="memo5" cols="50" rows="5"></textarea>
        <input type="date" name="date5" class="date">
        <textarea name="memo6" cols="50" rows="5"></textarea>
        <input type="date" name="date6" class="date">
        <textarea name="memo7" cols="50" rows="5"></textarea>
        <input type="date" name="date7" class="date">
        <textarea name="memo8" cols="50" rows="5"></textarea>
        <input type="date" name="date8" class="date">
        <textarea name="memo9" cols="50" rows="5"></textarea>
        <input type="date" name="date9" class="date">
        <textarea name="memo10" cols="50" rows="5"></textarea>
        <input type="date" name="date10" class="date">
        <div>
            <p><input type="submit" value="投稿する" /></p>
        </div>
    </form>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/date.js"></script>
</body>

</html>