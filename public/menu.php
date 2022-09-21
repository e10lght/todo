<?php

declare(strict_types=1);

session_start();
require_once(dirname(__FILE__) . "/../library/library.php");
require_once(dirname(__FILE__) . "/../config/connect.php");
require_once(dirname(__FILE__) . "/../models/db/fetchPicture.php");

// GETパラメータ
$page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT);
if (!empty($page)) {
    $ptr = "/^([1-9][0-9]{3})\-(0[1-9]{1}|1[0-2]{1})\-(0[1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1})$/";
    list($year, $month, $day) = explode("-", $page);
    $year = (int)$year;
    $month = (int)$month;
    $day = (int)$day;
    if (!preg_match($ptr, $page) || !checkdate($month, $day, $year)) {
        echo "不正な日付が入力されました";
        return;
    }
} else {
    $page = date("Y-m-d");
}



if (isset($_SESSION["id"]) && isset($_SESSION["name"])) {
    $name = $_SESSION["name"];
    $id = $_SESSION["id"];
} else {
    header("Location: login.php");
    exit();
}
try {
    $pdo->query("set names utf8mb4");

    $today = $page . "%";
    $stmt = $pdo->prepare('select m.id, m.user_id, m.memo, m.created, m.archive, u.name, u.pic from user u, memos m
    where u.id=m.user_id and user_id=? and date like ? order by id desc');
    $stmt->bindValue(1, (int)$id, PDO::PARAM_INT);
    $stmt->bindValue(2, $today, PDO::PARAM_STR);
    $res = $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<span class='error'>エラーがありました。</span><br>";
    echo $e->getMessage();
    exit();
}

// 写真だけ取得
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
                    <button><span class="fa fa-fw fa-search st-NewHeader_searchIcon"></span></button>
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

        <div class="action_btn">
            <a href="edit.php">新しいタスクを追加する</a>
            <a href="#" class="alert" data-date="<?php echo hsc($page); ?>">完了済みを全削除する</a>
            <button id="save_button">保存<i class="fas fa-cloud-download-alt fa-lg"></i></button>
        </div>

        <!-- 日付でページネーションする -->
        <p class="date">
            <a href="?page=<?php echo date("Y-m-d", strtotime("{$page} -1 day")); ?>">
                ◀<?php echo date("Y-m-d", strtotime("{$page} -1 day")); ?>
            </a> |
            <a href="?page=<?php echo date("Y-m-d", strtotime("{$page} +1 day")); ?>">
                <?php echo date("Y-m-d", strtotime("{$page} +1 day")); ?>▶
            </a>
        </p>

        <div id="content">
            <div class="msg" id="today_memo">
                <h3>
                    <?php if (!empty($page)) : ?>
                        <?php echo date("Y年n月j日", strtotime("{$page}")); ?>
                    <?php else : ?>
                        <?php echo date("Y年n月j日"); ?>
                    <?php endif; ?>
                    <br>タスク一覧
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
    <script src="assets/js/main.js"></script>
</body>

</html>