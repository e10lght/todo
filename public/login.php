<?php
session_start();
require_once(dirname(__FILE__) . "/../library/library.php");
require_once(dirname(__FILE__) . "/../config/connect.php");

$error = [];
$email = "";
$pass = "";
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $pass = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
}

if ($email === "" || $pass === "") {
    $error["login"] = "blank";
}
// ログインチェック
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('select id, name, password from user where email = ? limit 1');
    $stmt->bindValue(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($pass, $result["password"])) {
        session_regenerate_id();
        $_SESSION["id"] = $result["id"];
        $_SESSION["name"] = $result["name"];
        header("Location: menu.php");
        exit();
    } else {
        $error["login"] = "failed";
    }
} catch (Exception $e) {
    echo "<span class='error'>エラーがありました。</span><br>";
    echo $e->getMessage();
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ログイン</h1>
        </div>
        <div id="content">
            <form action="" method="post">
                <input type="text" name="email" placeholder="メールアドレス" size="35" maxlength="255" value="<?php echo hsc($email); ?>" />
                <?php if (isset($error["login"]) && $error["login"] === "blank") : ?>
                    <p class="error">メールアドレスとパスワードをご記入ください</p>
                <?php endif; ?>
                <?php if (isset($error["login"]) && $error["login"] === "failed") : ?>
                    <p class="error">ログインに失敗しました。正しくご記入ください。</p>
                <?php endif; ?>
                <input type="password" name="password" placeholder="パスワード" size="35" maxlength="255" value="<?php echo hsc($pass); ?>" />
                <div>
                    <input type="submit" value="ログインする" />
                </div>
            </form>
        </div>
        <a href="index.php">新規登録</a>
    </div>
</body>

</html>