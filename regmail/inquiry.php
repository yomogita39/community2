<?php
require_once("./qdsmtp.php");
require_once("./qdmail.php");
require_once ("../include/line_define.php");
session_start();
mb_language("ja");
$mail = new Qdmail();
$userMail = htmlspecialchars($_POST['userMail'], ENT_QUOTES);

//   if ($mailSendFlg) {
$param = array(
    'host' => 'smtp.lolipop.jp',
    'port' => 587,
    'protocol' => 'SMTP_AUTH',
    'user' => 'sns-admin@t-sal.net',
    'pass' => 'suzumiya',
    'from' => 'sample123@t-sal.net',	// 適当なメールアドレスでOK
);

// メール本文（html形式）の作成
$html = '
会員登録時障害が発生した模様
お問い合わせメールアドレス
'.$userMail;
$mail->smtp(true);
$mail->smtpServer($param);

$mail->to('sns-admin@t-sal.net');
$mail->subject('会員登録エラー問い合わせ');
$mail->from($userMail,$userMail);
$mail->html($html);
$mailFlg = $mail->send();
//   } else {
//     echo 'db登録失敗';
//   }

// メール送信フラグ
if ($mailFlg) {
  echo 1;
} else {
  echo 0;
}
exit();

?>