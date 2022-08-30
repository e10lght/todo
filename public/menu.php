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

    $stmt = $pdo->prepare('select m.id, m.user_id, m.memo, m.created, m.archive, u.name, u.pic from user u, memos m
    where u.id=m.user_id and user_id=? order by id desc');
    $stmt->bindValue(1, (int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<span class='error'>エラーがありました。</span><br>";
    echo $e->getMessage();
    exit();
}

// 写真だけ取得
$stmt = $pdo->prepare('select id, name, pic from user where id=?');
$stmt->bindValue(1, (int)$id, PDO::PARAM_INT);
$stmt->execute();
$pic_rslt = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($pic_rslt["pic"])) {
    $pic = $pic_rslt["pic"];
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>本日のTODO管理アプリ</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="icon" href="assets/image/favicon.ico">
</head>

<body>
    <div class="container">
        <header>
            <h1>本日のTODO管理アプリ</h1>
            <nav>
                <div class="header_menu">
                    <?php if (isset($pic)) : ?>
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

        <div class="action_btn">
            <a href="edit.php">TODOを追加する</a>
            <a href="#" class="alert">完了済みを全削除する</a>
            <button>保存する<i class="fas fa-cloud-download-alt fa-2x"></i>
            </button>
        </div>

        <div id="content">
            <div class="msg" id="today_memo">
                <h2>本日のTODO</h2>
                <form action="#" method="get" name="form">
                    <div class="l-content">
                        <?php foreach ($result as $index => $value) : ?>
                            <?php if ($value["archive"] === "1") : ?>
                                <div class="memo_<?php echo hsc($index + 1); ?> content" id="memo" title="<?php echo $value["id"]; ?>">
                                    <!-- labelは各要素ごとにカーソルを当てるために必要 -->
                                    <label for="check<?php echo hsc($index + 1); ?>">
                                        <p>
                                            <input id="check<?php echo hsc($index + 1); ?>" class="check" type="checkbox" name="memo_check">
                                        <pre><?php echo $value['memo']; ?></pre>
                                        </p>
                                    </label>
                                    <a href="#" title="<?php echo $value["id"]; ?>" class="delete_item">削除</a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>

            <div class="msg" id="msg_fin">
                <h2>完了済み</h2>
                <div class="r-content">
                    <?php foreach ($result as $index => $value) : ?>
                        <?php if ($value["archive"] === "0") : ?>
                            <div class="memo_<?php echo hsc($index + 1); ?> content archive" id="memo" title="<?php echo $value["id"]; ?>">
                                <label for="check<?php echo hsc($index + 1); ?>">
                                    <p>
                                        <input id="check<?php echo hsc($index + 1); ?>" class="check archive" type="checkbox" name="memo_check" checked>
                                    <pre><?php echo $value['memo']; ?></pre>
                                    </p>
                                </label>
                                <a href="#" title="<?php echo $value["id"]; ?>" class="delete_item">削除</a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- <script src="js/main.js"></script> -->
    <script src="assets/js/main.js"></script>
</body>

</html>