<?php
session_start();
require_once(dirname(__FILE__) . "/../library/library.php");
require_once(dirname(__FILE__) . "/../config/connect.php");

if (isset($_SESSION["id"]) && isset($_SESSION["name"])) {
    $name = $_SESSION["name"];
    $id = $_SESSION["id"];
} else {
    header("Location: login.php");
    exit();
}
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->query("set names utf8mb4");

    $stmt = $pdo->prepare('select id, name, pic from user where id=?');
    $stmt->bindValue(1, (int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<span class='error'>エラーがありました。</span><br>";
    echo $e->getMessage();
    exit();
}

if (isset($result["pic"])) {
    $pic = $result["pic"];
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $memo_cnt = 0;
    for ($i = 1; $i < 10; $i++) {
        $memo_cnt = "memo" . $i;
        var_dump($memo_cnt);
        $memo = filter_input(INPUT_POST, $memo_cnt, FILTER_SANITIZE_SPECIAL_CHARS);

        if (!empty($memo)) {
            $stmt = $pdo->prepare('insert into memos(memo, user_id) values(?, ?)');
            var_dump($memo);
            $stmt->bindValue(1, $memo, PDO::PARAM_STR);
            $stmt->bindValue(2, (int)$id, PDO::PARAM_INT);
            $result = $stmt->execute();
        }
    }
    header("Location: menu.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <header id="head" style="display: flex;
    justify-content: space-around;">
        <div id="today"></div>
        <h1>編集画面（仮)</h1>
        <img src="pic/<?php echo hsc($pic); ?>" width="48" height="48" alt="" />
        <div>
            <a href="menu.php">メニュー</a>
            <a href="logout.php">ログアウト</a>
        </div>
        <p><?php echo hsc($name); ?></p>
    </header>

    <form action="" method="post">

        <textarea name="memo1" cols="50" rows="5"></textarea>
        <textarea name="memo2" cols="50" rows="5"></textarea>
        <textarea name="memo3" cols="50" rows="5"></textarea>
        <textarea name="memo4" cols="50" rows="5"></textarea>
        <textarea name="memo5" cols="50" rows="5"></textarea>
        <textarea name="memo6" cols="50" rows="5"></textarea>
        <textarea name="memo7" cols="50" rows="5"></textarea>
        <textarea name="memo8" cols="50" rows="5"></textarea>
        <textarea name="memo9" cols="50" rows="5"></textarea>
        <textarea name="memo10" cols="50" rows="5"></textarea>
        <div>
            <p><input type="submit" value="投稿する" /></p>
        </div>
    </form>
    <script src="js/main.js"></script>
</body>

</html>