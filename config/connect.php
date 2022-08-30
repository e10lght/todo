<?php
    $host = "localhost:";
    $dbName = "todo";
    $user = "root";
    $password = "";
    $dsn = "mysql:host={$host};dbname={$dbName};charser=utf8mb4";

    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
