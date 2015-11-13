<?php
require_once('../pear/PEAR/Net/POP3.php');
// require_once('../pear/PEAR/Mail/mimeDecode.php');
require_once('ReceiptMailDecoder.class.php');
require_once('../include/line_define.php');
require_once('../include/common.php');

try {
  // PDOオブジェクトの生成
  $con = new PDO(DB_ACCESS, DB_UID, DB_PWD);
} catch(PDOException $e) {
  die('err:'.$e->getMessage());
}
// データベース内の書式設定をUTF8に
$con->query('SET NAMES utf8');

// メール送信用ライブラリを読み込み
require_once("../regmail/qdsmtp.php");
require_once("../regmail/qdmail.php");
// 言語設定を日本に
mb_language("ja");
$mail = new Qdmail();

$param = array(
    'host' => 'smtp.lolipop.jp',
    'port' => 587,
    'protocol' => 'SMTP_AUTH',
    'user' => 'sns-admin@t-sal.net',
    'pass' => 'suzumiya',
    'from' => 'sns-diarysystem@t-sal.net',	// 適当なメールアドレスでOK
);

// インスタンス作成
$pop3 = new Net_POP3();

// 接続
$res = $pop3->connect ("pop3.lolipop.jp", 110);

// ログイン ( APOP )
$res = $pop3->login( "sns-diarysystem@t-sal.net", "suzumiya06", true);

// 更新件数取得
$numMsg = $pop3->numMsg();

for ($i=1; $i<=$numMsg; $i++) {

  //メールをパースする
  $decoder =& new ReceiptMailDecoder($pop3->getMsg($i));
  // To:アドレスのみを取得する
  $toAddr = $decoder->getToAddr();
  echo $toAddr;
  // To:ヘッダの値を取得する
  $toString = $decoder->getDecodedHeader( 'to' );
  echo $toString;
  // From:ヘッダの値を取得する
  $fromString = $decoder->getFromAddr();
  echo '<br>'.$fromString.'<br>';
  // Subject:ヘッダの値を取得する
  $subject = ltrim(mb_convert_encoding($decoder->getDecodedHeader( 'subject' ),"UTF-8","jis"));
  echo $subject;
  // 日付を取得する
  $date = date("Y-m-d H:i:s", strtotime($decoder->getDecodedHeader( 'date' )));
  echo $date;
  // text/planなメール本文を取得する
  $body = ltrim(mb_convert_encoding($decoder->body['text'],"UTF-8","jis"));
  echo $body;
//   // text/htmlなメール本文を取得する
//   $body = mb_convert_encoding($decoder->body['html'],"UTF-8","jis");
//   echo $body;
  // マルチパートのデータを取得する
  if ( $decoder->isMultipart() ) {
    echo 'multi';
    $tempFiles = array();
    $num_of_attaches = $decoder->getNumOfAttach();
    echo $num_of_attaches;
    for ( $j=0 ; $j < $num_of_attaches ; ++$j ) {
      echo 'for';
      /*
       * ファイルを一時ディレクトリ _TEMP_ATTACH_FILE_DIR_ に保存する
      * 一時ファイルには tempnam()を使用する
      * この部分は使用に合わせて変更して下せい
      */
      $fpath = tempnam( './mobile-image', "todoattach_" );
//       echo $fpath;
//       if ( $decoder->saveAttachFile( $j, $fpath ) ) {
        $tempFiles = $decoder->attachments[$j]['file_name'];
        echo $tempFiles;
        $checkExt = checkImage($tempFiles, true);
        echo '拡張子は'.$checkExt;
        if (!empty($checkExt)) {
          echo '通った';
          $fileName = uniqid("mobile_").$j.$tempFiles;
          //添付内容をファイルに保存
          $path[$j] = "./diary_image/".$fileName;
          // ファイルを作成する
          $fp = fopen('.'.$path[$j], "w");
          $length = strlen($decoder->attachments[$j]['binary']);
          fwrite($fp, $decoder->attachments[$j]['binary'], $length);
          fclose($fp);
          echo $tempFiles;
        } else {
          $path = NULL;
        }
//       }
    }
  } else {
    $path = NULL;
  }
  $sublength = strlen($subject);
  // 非公開時の設定番号
  $private = 2;
  // 送信元メールアドレス認証に使用
  $getFlg = false;
//   if ($sublength > 0) {
    if ($subject != '' && $body != '') {
      // 送信されてきたメールアドレスをもとに反映先を判断
      $sql = 'SELECT
                userId, defaultPublicState
              FROM
                userMstTbl
              WHERE
                phoneAddress = ?';
        $stmt = $con->prepare($sql);
        $stmt->execute(array($fromString));
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $userId = $data['userId'];
          if ($data['defaultPublicState'] !== NULL) {
            $publicState = $data['defaultPublicState'];
          } else {
            // デフォルト設定がされてない場合、非公開に
            $publicState = $private;
          }
          $getFlg = true;
        }
//       }
      if ($getFlg) {
        try {
          $con->beginTransaction();
          $sql = 'INSERT INTO diaryMstTbl
                    (userId, title, message, publicState, photoPath1, photoPath2, photoPath3, createTime)
                  VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?)';
          $stmt = $con->prepare($sql);
          $flg = $stmt->execute(array($userId, $subject, nl2br($body), $publicState, $path[0], $path[1], $path[2], $date));
          if ($flg) {
            $con->commit();
            $message = '
反映処理が完了しました。投稿された日付は
'.$date.'
です。
            ';
          } else {
            $con->rollBack();
            $message ='
サーバーエラーにより投稿に失敗しました。しばらくお待ちください。
            ';
          }
        } catch (Exception $e) {
          $con->rollBack();
          $message ='
サーバーエラーにより投稿に失敗しました。しばらくお待ちください。
          ';
        }
      } else {
        $message ='
登録されていないメールアドレスです。
設定画面より携帯メールアドレス登録を完了してください。
        ';
      }
    } else {
      $message ='
本文もしくは画像添付の上送信してください。
添付ファイルを送信した場合はシステム非対応の添付ファイルです。
対応ファイルはjpg, png, gifのみです。
      ';
    }
  $mail->smtp(true);
  $mail->smtpServer($param);

  $mail->to($fromString);
  $mail->subject('携帯投稿受信メッセージ');
  $mail->from('sns-diarysystem@t-sal.net','t-salSNS画像投稿システム');
  $mail->text($message);
  $mailFlg = $mail->send();

  // メールの削除
  $pop3->deleteMsg($i);

}

// 接続解除
$pop3->disconnect();
?>