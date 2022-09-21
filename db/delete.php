<?php
session_start();

require_once(dirname(__FILE__) . "/../../library/library.php");
require_once(dirname(__FILE__) . "/../../config/connect.php");


if (isset($_SESSION["id"]) && isset($_SESSION["name"])) {
    $name = $_SESSION["name"];
    $id = $_SESSION["id"];
    $date = filter_input(INPUT_GET, "date", FILTER_SANITIZE_SPECIAL_CHARS);
} else {
    header("Location:" . "../public/menu.php", true, 301);
    exit();
}

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('delete from memos where user_id=? and archive=0 and date=?');
    $stmt->bindValue(1, (int)$id, PDO::PARAM_INT);
    $stmt->bindValue(2, $date, PDO::PARAM_STR);
    $result = $stmt->execute();
} catch (Exception $e) {
    echo "<span class='error'>エラーがありました。</span><br>";
    echo $e->getMessage();
    exit();
}

header("Location:" . "../menu.php?page=".$date, true, 301);
exit();
