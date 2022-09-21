<?php

declare(strict_types=1);

session_start();

require_once(dirname(__FILE__) . "/../library/library.php");
require_once(dirname(__FILE__) . "/../config/connect.php");

header("X-FRAME-OPTIONS: DENY");

/**
 * フォーム入力された値がブランクかどうか判定する関数を定義
 *　emptyではなく===""なのは、フォーム入力された値が「0」の場合にfalseになるため
 * @param string $form_data
 * @return bool
 */
function checkBlank(string $form_data = null): bool
{
    if ($form_data === "") {
        return true;
    } else {
        return false;
    }
}

/**
 * パスワードのバリデーションチェックを行う関数を定義
 */


/**
 * メールアドレスのバリデーションを行う関数を定義
 * @param string $email
 * @return bool
 */
function checkEmail(string $email): bool
{
    $array = explode('@', $email);
    $domain = array_pop($array);

    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false
        && (checkdnsrr($domain) || checkdnsrr($domain, 'A')
            || checkdnsrr($domain, 'AAAA'));
}

// 遷移先(check.php)から書き直しで戻った場合に一度入力された値をセットしておく処理
if (isset($_GET['action']) && $_GET['action'] === "rewrite" && isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
    unset($form["image"]);
} else {
    $form = [
        "name" => "",
        "email" => "",
        "password" => ""
    ];
}
$error = [];

/**
 * フォームの内容をindex.php内でチェック
 * 問題なければcheck.phpに遷移する
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form["name"] = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
    $form["email"] = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $form["password"] = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    $image = $_FILES["image"];

    /**
     * ブランクの場合にエラー判定用の文字列を代入する
     */
    foreach ($form as $index => $data) {
        if (checkBlank($data)) {
            $error[$index] = "blank";
        }
    }

    if (!checkEmail($form["email"])) {
        $error["email"] = "incorrect";
    }

    try {
        /**
         * 入力されたメールアドレスがデータベースに存在するかどうかチェック
         */
        $stmt = $pdo->prepare('select count(*) from user where email = ?');
        $stmt->bindValue(1, $form["email"], PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_NUM);

        /**
         * すでに登録があるメールアドレスがある場合の処理
         */
        if ($result[0] > 0) {
            $error['email'] = "duplicate";
        }
    } catch (Exception $e) {
        echo "<span class='error'>エラーがありました。</span><br>";
        echo $e->getMessage();
        exit();
    }

    /**
     * パスワードのバリデーション処理
     * 入力なし、4文字未満の場合エラー用の文字列を代入
     */
    if (!preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/', $form["password"])) {
        $error["password"] = "length";
    } else {
        $form["password"] = password_hash($form["password"], PASSWORD_DEFAULT);
    }

    /**
     * 登録された画像のMIMEタイプがpng/jpeg以外ならエラー用の文字列を代入
     */
    if ($image["name"] !== "" && $image["error"] === 0) {
        $type = mime_content_type($image["tmp_name"]);
        if ($type !== "image/png" && $type !== "image/jpeg") {
            $error["image"] = "type";
        }
    }

    /**
     * 画像のアップロード処理
     */
    if (empty($error) && $image['name'] !== '') {
        echo "ここ";
        $filename = date('YmdHis') . '_' . $image['name'];
        if (!move_uploaded_file($image['tmp_name'], 'pic/' . $filename)) {
            die("ファイルのアップロードに失敗しました");
        } else {
            $form["image"] = $filename;
            $_SESSION['form']['image'] = $filename;
        }
    }

    /**
     * 入力されたフォーム値にエラーがなければcheck.phpに遷移する
     */
    if (empty($error)) {
        $_SESSION['form'] = $form;
        header('Location: check.php');
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <link rel="stylesheet" href="assets/css/front.css" />
    <link rel="icon" href="assets/image/favicon.ico">
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>会員登録</h1>
        </div>

        <div id="content">
            <p>次のフォームに必要事項をご記入ください。</p>
            <form action="" method="post" enctype="multipart/form-data">
                <dl>
                    <dt>ニックネーム<span class="required">必須</span></dt>
                    <dd>
                        <input type="text" name="name" size="35" maxlength="255" value="<?php echo hsc($form["name"]); ?>" />
                        <?php if (isset($error["name"]) && $error["name"] === "blank") : ?>
                            <p class="error">* ニックネームを入力してください</p>
                        <?php endif; ?>
                    </dd>
                    <dt>メールアドレス<span class="required">必須</span></dt>
                    <dd>
                        <input type="text" name="email" size="35" maxlength="255" value="<?php echo hsc($form["email"]); ?>" />
                        <?php if (isset($error["email"]) && $error["email"] === "blank") : ?>
                            <p class="error">* メールアドレスを入力してください</p>
                        <?php endif; ?>
                        <?php if (isset($error["email"]) && $error["email"] === "incorrect") : ?>
                            <p class="error">* 不正なメールアドレスです</p>
                        <?php endif; ?>
                        <?php if (isset($error["email"]) && $error["email"] === "duplicate") : ?>
                            <p class="error">* 指定されたメールアドレスはすでに登録されています</p>
                        <?php endif; ?>
                    <dt>パスワード<span class="required">必須</span></dt>
                    <dd>
                        <input type="password" name="password" size="10" maxlength="20" value="" />
                        <?php if (isset($error["password"]) && $error["password"] === "blank") : ?>
                            <p class="error">* パスワードを入力してください</p>
                        <?php endif; ?>
                        <?php if (isset($error["password"]) && $error["password"] === "length") : ?>
                            <p class="error">* パスワードは小文字大文字混合の英数字8文字以上で入力してください</p>
                        <?php endif; ?>
                    </dd>
                    <dt>写真など</dt>
                    <dd>
                        <input type="file" name="image" size="35" value="" />
                        <?php if (isset($error["image"]) && $error["image"] === "type") : ?>
                            <p class="error">* 写真などは「.png」または「.jpg」の画像を指定してください</p>
                        <?php endif; ?>
                        <?php if (isset($_GET['action']) && $_GET['action'] === "rewrite" && isset($_SESSION['form'])) : ?>
                            <p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
                        <?php endif; ?>
                    </dd>
                </dl>
                <div><input type="submit" value="入力内容を確認する" /></div>
            </form>
        </div>
        <a href="login.php">ログインはこちら</a>
</body>

</html>