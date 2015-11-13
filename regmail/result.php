<?php
  require_once("./qdsmtp.php");
  require_once("./qdmail.php");
  require_once ("../include/line_define.php");
  session_start();
  mb_language("ja");
  $mail = new Qdmail();
  try {
    // PDOオブジェクトの生成
    $pdo = new PDO("mysql:dbname=LA04088615-line;host=mysql593.phy.lolipop.jp", DB_UID, DB_PWD);
  } catch(PDOException $e) {
    die('err:'.$e->getMessage());
  }
  $pdo->query('SET NAMES utf8');

  $addres;
  $userId = $_SESSION['userId'];
  $pwd = hash('sha256', $_SESSION['pass']);
  $userName = $_SESSION['userName'];
  $realName = $_SESSION['realName'];
  $birthday = $_SESSION['birthday'];
  $gender = $_SESSION['gender'];
  $place = $_SESSION['place'];
//   // 仮パスワードの発行
//   $tempPass = array();
//   $tempPass = randPass($userId);

  try {
    $pdo->beginTransaction();

    $sql = 'insert into userMstTbl (mailAddress, pwd, userName, userImage, realName, gender, birthday, place, regCompFlg, withdrawalFlg)
    values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    // sql文の実行
    $flg2 = $stmt->execute(array($userId, $pwd, $userName, './user_image/defaultUserImage.png', $realName, $gender, $birthday, $place, 0, 0));
    // $stmtのクローズ
    $stmt->closeCursor();
    $lastInsertId = $pdo->lastInsertId();

    if ( $flg2) {
      $pdo->commit();
      $mailSendFlg = true;
    } else {
      $pdo->rollBack();
    }
  } catch (PDOException $e) {
    $pdo->rollBack();
    die('err:'.$e->getMessage());
  }
//   if ($mailSendFlg) {
    $param = array(
        'host' => 'smtp.lolipop.jp',
        'port' => 587,
        'protocol' => 'SMTP_AUTH',
        'user' => 'sns-admin@t-sal.net',
        'pass' => 'suzumiya',
        'from' => 't-salSNS@t-sal.net',	// 適当なメールアドレスでOK
    );

    // メール本文の作成
//     $text = '
// T-SAL農園 SNS からのお知らせです。
// 仮登録を受け付けしました。
// 下記URLにアクセスし、登録を完了してください。

// <http://www.t-sal.net/community/regCompletion.php?id='.$lastInsertId.'&address='.$tempPass['address'].'&pass='.$tempPass['pass'].'&mail='.$userId.'>

// なお、上記リンク先の有効期限は24時間以内となっております。
// 有効期限を過ぎてしまった場合登録を完了できませんので、
// お手数ですが最初からやり直してくださいますようお願いします。

// ※本メールに覚えがない場合は本メールを破棄してください。
// ';

    $sql = "SELECT mailAddress AS mail FROM userMstTbl WHERE userId = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    // 管理者に送信するメールの生成
    $text = '
T-SAL農園 SNS からのお知らせです。
'.$_SESSION['realName'].'様の仮登録を受け付けしました。
ユーザ管理画面にアクセスし、登録を完了してください。

<http://www.t-sal.net/community/login.php>
';
    $mail->smtp(true);
    $mail->smtpServer($param);

//     $mail->to($userId);
    $mail->to($data['mail']);
    $mail->subject('仮登録受付メール');
    $mail->from('t-salSNS@t-sal.net','t-salSNS事務局');
    $mail->text($text);
    $mailFlg = $mail->send();
//   } else {
//     echo 'db登録失敗';
//   }

    // 登録ユーザに送信するメールの生成
    $text = '
T-SAL農園 SNS からのお知らせです。
'.$_SESSION['realName'].'様の仮登録を受け付けしました。
T-SAL農園 SNS 管理者の承認待ちとなります。承認後、本登録完了ページへのURLを
記述したメールを送信いたします。2、3営業日ほどお待ちください。

※なお、本メールに覚えがない方は本メールを破棄してください。
';
    $mail->smtp(true);
    $mail->smtpServer($param);

    //     $mail->to($userId);
    $mail->to($userId);
    $mail->subject('仮登録受付メール');
    $mail->from('t-salSNS@t-sal.net','t-salSNS事務局');
    $mail->text($text);
    $mailFlg2 = $mail->send();

    // メール送信フラグ
  if ($mailFlg && $mailFlg2) {
    $_SESSION['regId']['sendMail'] = $text;
    header('location:'.REGISTRATIONCHECK);
  } else {
    $_SESSION['regId']['sendMail'] = 1;
    $_SESSION['regId']['message'] = 'メールの送信に失敗しました。お手数ですが再度登録してください。';
  }
  exit();
// /**
//  *
//  * @param String $addres
//  * @return String $tempPass
//  */
// function randPass($addres) {
//   // 乱数に使用する文字
//   $strinit = 'abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ012345679';
//   // str_split() は文字列を配列に変換する関数です ( PHP5 )
//   $arr_str = str_split($strinit);

//   for ($i = 0, $str = null; $i < 10; $i++) {
//     // array_rand() は配列から一つ以上の要素のキーを取得します
//     // 一つだけの時はキーを返し、それ以外はキーの配列を返します
//     $rand_key = array_rand($arr_str, 1);
//     $str .= $arr_str[$rand_key];
//   }
//   $tempPass['id'] = $addres;
//   $tempPass['address'] = hash('sha256', date(YmdHis).$addres);
//   $tempPass['pass'] = hash('sha256', $str);
//   return $tempPass;
// }
?>