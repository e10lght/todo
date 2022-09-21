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

// CSRF対策
if ($_SESSION['token'] !== $_POST['token']) {
    echo filter_input(INPUT_POST, "token", FILTER_SANITIZE_SPECIAL_CHARS), PHP_EOL;
    echo '<h2 style="color:red;">CSRF検証に失敗したため、</h2>';
    echo '<h2 style="color:red;">リクエストは中断されました</h2>';
    echo "数秒後にMENU画面にリダイレクトします";
    header( "refresh:5;url=menu.php" );
    die();
}

try {
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
    $memo_tmp = 0;
    for ($i = 1; $i < 10; $i++) {
        $memo_tmp = "memo" . $i;
        $date_tmp = "date" . $i;
        /**
         * filter_inputのサニタイズはhtmlspecialcharsと併用すると多重エスケープになるので、
         * どちらかをエスケープしないように気を付ける。
         * ※基本的にはhscでエスケープするのが妥当らしい。
         *  */ 
        $memo = filter_input(INPUT_POST, $memo_tmp, FILTER_SANITIZE_SPECIAL_CHARS);
        $date = filter_input(INPUT_POST, $date_tmp, FILTER_SANITIZE_SPECIAL_CHARS);
        if (empty($date)) {
            $date = date("Y-m-d");
        }
        echo $date;
        if (!empty($memo)) {
            $stmt = $pdo->prepare('insert into memos(memo, user_id, date) values(?, ?, ?)');
            var_dump($memo);
            $stmt->bindValue(1, $memo, PDO::PARAM_STR);
            $stmt->bindValue(2, (int)$id, PDO::PARAM_INT);
            $stmt->bindValue(3, $date, PDO::PARAM_STR);
            $result = $stmt->execute();
        }
    }
    header("Location: menu.php");
    exit();
}
