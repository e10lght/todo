<?php
    // $host = "mysql57.notitle.sakura.ne.jp";
    // $dbName = "notitle_1";
    // $user = "notitle";
    // $password = "Kira0819";
    // $dsn = "mysql:host={$host};dbname={$dbName};charser=utf8";

    $host = "localhost:8889";
    $dbName = "todo";
    $user = "root";
    $password = "root";
    $dsn = "mysql:host={$host};dbname={$dbName};charser=utf8mb4";

    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
