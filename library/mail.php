<?php

/**
 * 会員登録時にウェルカムメールを送信する関数
 */

declare(strict_types=1);

/* vnasrgbkhkcwgeso */

mb_language('Japanese');
mb_internal_encoding('UTF-8');

/**
 * mb_send_mailの第4引数にセットする日本語のエンコード
 */
function encodeHeader($value)
{
    return mb_encode_mimeheader(
        mb_convert_encoding($value, 'ISO-2022-JP', 'UTF-8'),
        'ISO-2022-JP',
        'B'
    );
}

/**
 * 送信元のメールアドレス。ご自身のものに書き換えてください。
 */
$from = 'test@gmail.com';

/**
 * 送信先のメールアドレス。送信して問題の無いアドレスに書き換えてください。
 */
// $to[] = 'kira_neymar@icloud.com';
// $to[] = 'itokira0819@gmail.com';

/**
 * メールの表題
 */
$subject = '会員登録ありがとうございます！';


function sendmail($to, $name)
{
    /**
     * メールの本文
     */
    $body = <<< BODY
{$name}さん！　こんにちは！
この度はTODOアプリにご登録いただきありがとうございます。
BODY;

    /**
     * メールの送信元(日本語表記)
     */
    $sender = encodeHeader('TODOアプリ管理者');
    // メールのヘッダ行を生成する。
    $header = <<< HEADER
    From: {$sender} <{gmial.smtp.test@gmail.com}>
    Reply-To: {gmial.smtp.test@gmail.com}
    HEADER;

    // メール送信する。
    $isMailSent = mb_send_mail($to, '会員登録ありがとうございます！', $body, $header);

    echo $isMailSent ? 'メールを送信しました。' : 'メールは送信できませんでした。';
    echo PHP_EOL;
}
// main($from, $to[0], $subject, "山田太郎");
// main($from, $to[1], $subject, "田中アサリ");
// main($from, $to, $subject, "田中アサリ");
