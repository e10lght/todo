<?php

session_start();

require_once(dirname(__FILE__) ."/../../library/library.php");
require_once(dirname(__FILE__) ."/../../config/connect.php");

$json_todo = json_decode($_POST["todo_title"], true);
$json_archive = json_decode($_POST["archive_title"], true);

if(!$json_todo && !$json_archive) {
    header("Location: menu.php");
    exit();
}
$test = count($json_todo);
$arraylen = count($json_archive);
$index = $test + $arraylen;

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($json_todo)) {
        foreach ($json_todo as $value) {
            $stmt = $pdo->prepare('update memos set archive = 1 WHERE id=?');
            $stmt->bindValue(1, (int)$value, PDO::PARAM_INT);
            $result = $stmt->execute();
        }
    }
    if (!empty($json_archive)) {
        foreach ($json_archive as $value) {
            $stmt = $pdo->prepare('update memos set archive = 0 WHERE id=?');
            $stmt->bindValue(1, (int)$value, PDO::PARAM_INT);
            $result = $stmt->execute();
        }
    }
} catch (Exception $e) {
    echo "<span class='error'>エラーがありました。</span>";
    echo $e->getMessage();
    exit();
}
