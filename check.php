<?php

/**
 * 会員登録内容の確認ページ
 * 「入力内容を確認する」ボタン押下後、DBへの登録を行う
 * また、同時に登録されたメールアドレス宛にウェルカムメールを送信する
 */

declare(strict_types=1);

session_start();

require_once(dirname(__FILE__) . "/../library/library.php");
require_once(dirname(__FILE__) . "/../library/mail.php");
require_once(dirname(__FILE__) . "/../config/connect.php");

if (isset($_SESSION['form'])) {
	$form = $_SESSION['form'];
} else {
	header('Location: index.php');
	exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	try {
		$stmt = $pdo->prepare('insert into user(name, email, password, pic) values(?,?,?,?)');
		$stmt->bindValue(1, $form["name"], PDO::PARAM_STR);
		$stmt->bindValue(2, $form["email"], PDO::PARAM_STR);
		$stmt->bindValue(3, $form["password"], PDO::PARAM_STR);
		$stmt->bindValue(4, $form["image"], PDO::PARAM_STR);
		$stmt->execute();

		sendmail($form["email"], $form["name"]);
		unset($_SESSION['form']);
		header("Location: thanks.php");
	} catch (Exception $e) {
		echo "<span class='error'>エラーがありました。</span><br>";
		echo $e->getMessage();
		exit();
	}
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="css/style.css" />
</head>

<body>
	<div id="wrap">
		<div id="head">
			<h1>会員登録</h1>
		</div>

		<div id="content">
			<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
			<form action="" method="post">
				<dl>
					<dt>ニックネーム</dt>
					<dd><?php echo hsc($form['name']); ?></dd>
					<dt>メールアドレス</dt>
					<dd><?php echo hsc($form['email']); ?></dd>
					<dt>パスワード</dt>
					<dd>
						<?php echo hsc($form['password']); ?>
					</dd>
					<dt>写真など</dt>
					<dd>
						<?php if (!empty($form["image"])) : ?>
							<img src="pic/<?php echo hsc($form['image']); ?>" />
						<?php endif; ?>
					</dd>
				</dl>
				<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
			</form>
		</div>

	</div>
</body>

</html>