<?php

declare(strict_types=1);

session_start();
require_once(dirname(__FILE__) . "/../library/library.php");
require_once(dirname(__FILE__) . "/../config/connect.php");
require_once(dirname(__FILE__) . "/../models/db/fetchPicture.php");

if (isset($_SESSION["id"]) && isset($_SESSION["name"])) {
    $name = $_SESSION["name"];
    $id = $_SESSION["id"];
} else {
    header("Location: login.php");
    exit();
}

$search = filter_input(INPUT_POST, "search", FILTER_SANITIZE_SPECIAL_CHARS);

$param = "%" . $search . "%";
$stmt = $pdo->prepare('select m.id, m.user_id, m.memo, m.created, m.archive, m.date, u.name, u.pic from user u, memos m
where u.id=m.user_id and user_id=? and memo like ? order by date desc');
$stmt->bindValue(1, $id, PDO::PARAM_INT);
$stmt->bindValue(2, $param, PDO::PARAM_STR);
$res = $stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 写真の取得
$pic = getPic($pdo, $id);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TODO管理アプリ</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="icon" href="assets/image/favicon.ico">
</head>

<body>
    <div class="container">
        <header>
            <div class="search_box">
                <form action="serach.php" method="post">
                    <span class="fa fa-fw fa-search st-NewHeader_searchIcon"></span>
                    <input type="search" name="search" id="search" placeholder="キーワードを入力" size="13">
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

        <div class="search">
            <a href="menu.php">◀MENUに戻る</a>
            <button>保存<i class="fas fa-cloud-download-alt fa-lg"></i></button>
        </div>
        <h2>検索結果</h2>

        <div id="content">
            <div class="msg" id="today_memo">
                <h3>
                    タスク一覧
                </h3>
                <form action="#" method="get" name="form">
                    <div class="l-content">
                        <?php foreach ($result as $index => $value) : ?>
                            <?php if ($value["archive"] === "1") : ?>
                                <div class="memo_<?php echo hsc($index + 1); ?> content" id="memo" title="<?php echo $value["id"]; ?>">
                                    <!-- labelは各要素ごとにカーソルを当てるために必要 -->
                                    <div class="task">
                                        <label for="check<?php echo hsc($index + 1); ?>">
                                            <p>
                                                <input id="check<?php echo hsc($index + 1); ?>" class="check" type="checkbox" name="memo_check">
                                            <pre><?php echo $value['memo']; ?></pre>
                                            <pre><?php echo $value['created']; ?></pre>
                                            </p>
                                        </label>
                                    </div>
                                    <a href="#" title="<?php echo $value["id"]; ?>" class="delete_item">削除</a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>

            <div class="msg" id="msg_fin">
                <h3>完了済み</h3>
                <div class="r-content">
                    <?php foreach ($result as $index => $value) : ?>
                        <?php if ($value["archive"] === "0") : ?>
                            <div class="memo_<?php echo hsc($index + 1); ?> content archive" id="memo" title="<?php echo $value["id"]; ?>">
                                <div class="task">
                                    <label for="check<?php echo hsc($index + 1); ?>">
                                        <p>
                                            <input id="check<?php echo hsc($index + 1); ?>" class="check archive" type="checkbox" name="memo_check" checked>
                                        <pre><?php echo $value['memo']; ?></pre>
                                        <pre><?php echo $value['created']; ?></pre>
                                        </p>
                                    </label>
                                </div>
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