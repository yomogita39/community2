<?php
require_once('./include/line_define.php');
require_once('./include/common.php');
session_start();

// 入力されたメールアドレスを受信
$address = escape($_POST['mail']);
// 受信した値がブランクでない場合
if ($address) {
  // db接続
  try{
    $con = new PDO("mysql:dbname=".DB_NAME.";host=".DB_URL,DB_UID,DB_PWD);
  } catch(PDOException $e){
    die('err:'. $e->getMessage());
  }
  // 文字コード設定
  $con->query('SET NAMES utf8');

  // 受信したメールアドレスは登録されているか
  $sql = '
         SELECT
          mailAddress AS mail
         FROM
          userMstTbl
         WHERE
          mailAddress = ?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($address));
  $getFlg = false;
  while($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $getFlg = true;
    $address = $data['mail'];
  }
  // 登録されていない場合処理を終了
  if (!$getFlg) {
    exit('0');
  }
  // 仮パスワード発行処理の開始
  try {
    $con->beginTransaction();
    // 乱数に使用する文字
    $strinit = 'abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ012345679';
    // str_split() は文字列を配列に変換する関数です ( PHP5 )
    $arr_str = str_split($strinit);

    for ($i = 0, $str = null; $i < 10; $i++) {
      // array_rand() は配列から一つ以上の要素のキーを取得します
      // 一つだけの時はキーを返し、それ以外はキーの配列を返します
      $rand_key = array_rand($arr_str, 1);
      $str .= $arr_str[$rand_key];
    }
    // 仮パスワード
    $tempPass = $str;
    // ユーザのパスワードを仮パスワードに設定
    $sql = 'UPDATE userMstTbl SET pwd = ?
            WHERE mailAddress = ?';
    $stmt = $con->prepare($sql);
    $flg = $stmt->execute(array(hash('SHA256', $tempPass), $address));
    // 成功した場合メール送信処理の開始
    if ($flg) {
      require_once("./regmail/qdsmtp.php");
      require_once("./regmail/qdmail.php");
      mb_language("ja");
      // qdmailオブジェクトの生成
      $mail = new Qdmail();

      // メールの設定
      $param = array(
          'host' => 'smtp.lolipop.jp',
          'port' => 587,
          'protocol' => 'SMTP_AUTH',
          'user' => 'sns-admin@t-sal.net',
          'pass' => 'suzumiya',
          'from' => 't-salSNS@t-sal.net',	// 適当なメールアドレスでOK
      );

      // メール本文の作成
      $text = '
T-SAL農園 SNS からのお知らせです。
仮パスワードを発行いたしました。

仮パスワード：'.$tempPass.'

下記ログインフォームより上記パスを使用しログイン後、「設定」よりパワード変更を行ってください。

<http://www.t-sal.net/community/login.php>

※本メールに覚えがない場合は本メールを破棄してください。
';
      $mail->smtp(true);
      $mail->smtpServer($param);

      $mail->to($address);
      $mail->subject('本登録確認メール');
      $mail->from('t-salSNS@t-sal.net','t-salSNS事務局');
      $mail->text($text);
      $mailFlg = $mail->send();
      //   } else {
      //     echo 'db登録失敗';
      //   }

      // メール送信成功時コミットと成功を返す
      if ($mailFlg) {
        $con->commit();
        db_close($con);
        exit('OK');
      } else {
        $con->rollBack();
        exit('2');
      }

    } else {
      $con->rollBack();
      exit('1');
    }
  } catch (Exception $e) {
    $con->rollBack();
    exit('1');
  }
}
