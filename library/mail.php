<?php

/**
 * 会員登録時にウェルカムメールを送信する関数
 */

declare(strict_types=1);

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

$from = 'test@gmail.com';
$subject = '会員登録ありがとうございます！';

function sendmail($to, $name)
{
    $body = <<< BODY
{$name}さん！　こんにちは！
この度はTODOアプリにご登録いただきありがとうございます。
BODY;

    $sender = encodeHeader('TODOアプリ管理者');
    
    // メールヘッダ行の生成
    $header = <<< HEADER
    From: {$sender} <{gmial.smtp.test@gmail.com}>
    Reply-To: {gmial.smtp.test@gmail.com}
    HEADER;

    $isMailSent = mb_send_mail($to, '会員登録ありがとうございます！', $body, $header);

    echo $isMailSent ? 'メールを送信しました。' : 'メールは送信できませんでした。';
    echo PHP_EOL;
}
