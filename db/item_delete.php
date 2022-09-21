<?php

session_start();

require_once(dirname(__FILE__) . "/../../library/library.php");
require_once(dirname(__FILE__) . "/../../config/connect.php");

$get_id = filter_input(INPUT_GET, "getId", FILTER_SANITIZE_NUMBER_INT);
if (!$get_id) {
    echo true;
    header("Location: index.php");
    exit();
}

if (isset($_SESSION["id"]) && isset($_SESSION["name"])) {
    $name = $_SESSION["name"];
    $id = $_SESSION["id"];
    $date = filter_input(INPUT_GET, "page", FILTER_SANITIZE_SPECIAL_CHARS);
} else {
    header("Location: login.php");
    exit();
}

try {

    $stmt = $pdo->prepare('delete from memos where id=? and user_id=? limit 1');
    $stmt->bindValue(1, (int)$get_id, PDO::PARAM_INT);
    $stmt->bindValue(2, (int)$id, PDO::PARAM_INT);
    $result = $stmt->execute();
} catch (Exception $e) {
    echo "<span class='error'>エラーがありました。</span><br>";
    echo $e->getMessage();
    exit();
}

header("Location:" . "../menu.php?page=".$date, true, 301);
exit();
