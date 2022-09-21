<?php

/**
 * 写真を取得する
 */

declare(strict_types=1);

require_once(dirname(__FILE__) . "/../../config/connect.php");

function getPic($pdo, int $id)
{
    $stmt = $pdo->prepare('select id, name, pic from user where id=?');
    $stmt->bindValue(1, (int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $pic_rslt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (isset($pic_rslt["pic"])) {
        $pic = $pic_rslt["pic"];
        return $pic;
    }
    return false;
}
