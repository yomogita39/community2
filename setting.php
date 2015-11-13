<?PHP
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');
$smarty = new Smarty();
session_start();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
// セッション情報の取得
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
switch (intval(escape($_POST['proc']))) {
  case 1: mailSet($con, $userId); exit();
  case 2: passSet($con, $userId); exit();
  case 3: mobileSet($con, $userId); exit();
  case 4: defaultMailPublicStateSet($con, $userId); exit();
}

// メッセージの受け取り
require_once('./include/inFlg.php');
$flg = intval(escape($_GET['setting']));
if ($flg) {
  $smarty->assign('flg', $flg);
  if ($flg == 3) {
    $smarty->assign('phoneAddress', $_SESSION['user']['phoneAddress']);
    $sql = '
      SELECT
        defaultPublicState
      FROM
        userMstTbl
      WHERE
        userId = ?
    ';
    $stmt = $con->prepare($sql);
    $stmt->execute(array($userId));
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    // switch文は型を比較しないため、TRUEを比較対象にし厳密演算子にて比較する
    switch (TRUE) {
      case ($data['defaultPublicState'] === 0): $publicState = 'public';break;
      case ($data['defaultPublicState'] === 1): $publicState = 'protected';break;
      default: $publicState = 'private';break;
    }
    $smarty->assign($publicState, 'selected="selected"');
  }
}



// DBのクローズ
db_close($con);
//テンプレートの表示
$smarty->display("settings.tpl");

exit();

/**
 * 携帯アドレス登録に使用する
 * @param unknown_type $con
 * @param unknown_type $userId
 */
function mobileSet($con, $userId) {
  // キャプチャ画像の認証コード
  $captcyaCode = escape($_POST['keyword']);
  // 使用するアドレス
  $address = escape($_POST['mail']);
  // 確認用のアドレス
  $reAddress = escape($_POST['remail']);

  // 入力されたメールアドレスはフォーマットとして正しいか
  if (isValidEmailFormat($address) !== true) {
    exit('addNG');
  }
  if ($address !== $reAddress) {
    exit('readdNG');
  }
  if ($captcyaCode != "" && isset($captcyaCode)) {
    // 入力されたキャプチャコードは正しいか
    if ($captcyaCode == $_SESSION["securimage_code_disp"]["default"]) {
      // キャプチャ認証に使用するライブラリ
      require_once ('./securimage/securimage.php');
      $securimage = new Securimage();
      // 入力された値を再度検証する
      if ($securimage->check($captcyaCode) == true) {
//         $tempPass = randPass($address);
        try {
          $con->beginTransaction();

          // 入力された携帯アドレスを登録sql文の発行
          $sql = 'UPDATE userMstTbl SET phoneAddress=? WHERE userId=?';
          $stmt = $con->prepare($sql);
          // sql文の実行
          $flg = $stmt->execute(array($address, $userId));
          // $stmtのクローズ
          $stmt->closeCursor();
          $mailSendFlg = false;
          // SQL実行に成功したらコミットしフラグをONに
          if ($flg) {
            $con->commit();
            $mailSendFlg = true;
            $_SESSION['user']['phoneAddress'] = $address;
          } else {
            $con->rollBack();
          }
        } catch (PDOException $e) {
          $con->rollBack();
          die('err:'.$e->getMessage());
        }
        if ($mailSendFlg) {
          // メール送信用ライブラリを読み込み
          require_once("./regmail/qdsmtp.php");
          require_once("./regmail/qdmail.php");
          // 言語設定を日本に
          mb_language("ja");
          $mail = new Qdmail();
          //   if ($mailSendFlg) {
          // メール生成のための設定
          $param = array(
              'host' => 'smtp.lolipop.jp',
              'port' => 587,
              'protocol' => 'SMTP_AUTH',
              'user' => 'sns-admin@t-sal.net',
              'pass' => 'suzumiya',
              'from' => 't-salSNS@t-sal.net',	// 適当なメールアドレスでOK
          );

          // メール本文（html形式）の作成
          $text = '
T-SAL農園 SNS からのお知らせです。
投稿用携帯メールアドレス登録を受け付けしました。
投稿を反映させる機能により宛先と書式が異なりますので
注意してください。

それぞれの投稿用の宛先と書式は下記の通りです。
○日記
・宛先
<sns-farmdiarysystem@t-sal.net>

・書式
件名：日記のタイトルになります。
本文：日記の本文になります。
添付ファイル：3件まで。

○自分の農業日誌通信欄
・宛先
<sns-farmdiarysystem@t-sal.net>

・書式
件名：スペースを空けずに投稿したい日付を8桁で入力。
      例 20130901
入力した日付に誤りがある場合や、日付を入力しなかった場合は
投稿日の日付として処理されます。
本文：通信欄の本文になります。
添付ファイル：1件まで。

それでは、沢山のご投稿お待ちしております。

※本メールに覚えがない場合は本メールを破棄してください。
';
          $mail->smtp(true);
          $mail->smtpServer($param);

          $mail->to($address);
          $mail->subject('携帯メールアドレス受付');
          $mail->from('t-salSNS@t-sal.net','t-salSNS事務局');
          $mail->text($text);
          $mailFlg = $mail->send();
          //   } else {
          //     echo 'db登録失敗';
          //   }

          // メール送信フラグ
          if ($mailFlg) {
            exit('mobileOK');
          } else {
            exit('mobileNG');
          }
        }
      }
    }
  }
  exit('mailNG');
}
/**
 * パスワードの再設定
 * @param db $con
 * @param int $userId
 */
function passSet($con, $userId) {
  $oldPass = escape($_POST['oldPassword']);
  $newPass = escape($_POST['newPassword']);
  $newRepass = escape($_POST['newRepassword']);
  // 入力された文字が英数字のみであることの確認など
  if (preg_match("/^[a-zA-Z0-9]+$/", $newPass) && preg_match("/^[a-zA-Z0-9]+$/", $newRepass)
      && preg_match("/^[a-zA-Z0-9]+$/", $oldPass) && $newPass === $newRepass
      && strlen($newPass) >= 6) {
    // ユーザの登録されているパスワードを取得
    $sql = "
    SELECT
      pwd AS password
    FROM
      userMstTbl
    WHERE
      userId = :userId
    AND
      regCompFlg = 1
  ";

  $stmt = $con->prepare($sql);
  $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
  $ret = $stmt->execute();

  // 入力されたパスワードと登録されているデータは一致するか
  while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    if($data['password']===hash('SHA256', $oldPass)){
      // 一致した場合新しいパスワードに変更
      try {
        $con->beginTransaction();
        $sql = 'UPDATE userMstTbl SET pwd = ?
        WHERE userId = ?';
        $stmt = $con->prepare($sql);
        // パスワードはhash関数を使い暗号化
        $flg = $stmt->execute(array(hash('SHA256', $newPass), $userId));
        // 成功の場合成功を返す
        if ($flg) {
          $con->commit();
          exit('passOK');
        } else {
          $con->rollback();
        }
      }catch (Exception $e) {
        $con->rollback();
      }
    } else {
      exit('passNG');
    }
  }
  }
  exit('passNG');
}


/**
 * mailSettingMethod
 * @param unknown_type $con
 * @param unknown_type $userId
 * @param unknown_type $smarty
 */
function mailSet($con, $userId) {
  $captcyaCode = escape($_POST['keyword']);
  $address = escape($_POST['mail']);
  $reAddress = escape($_POST['remail']);
  if (isValidEmailFormat($address) !== true) {
    exit('addNG');
  }
  if ($address !== $reAddress) {
    exit('readdNG');
  }
  if ($captcyaCode != "" && isset($captcyaCode)) {
    if ($captcyaCode == $_SESSION["securimage_code_disp"]["default"]) {
      require_once ('./securimage/securimage.php');
      $securimage = new Securimage();
      if ($securimage->check($captcyaCode) == true) {
        $tempPass = randPass($address);

        try {
          $con->beginTransaction();

          $sql = 'SELECT userId FROM userTempTbl WHERE userId = ?';
          $stmt = $con->prepare($sql);
          // sql文の実行
          $stmt->execute(array($userId));
          $wFlg = false;
          while($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $wFlg = true;
          }
          // $stmtのクローズ
          $stmt->closeCursor();
          if ($wFlg) {
            $sql = 'delete from userTempTbl where userId=?';
            $stmt = $con->prepare($sql);
            $flg2 = $stmt->execute(array($userId));
            $stmt->closeCursor();
            if ($flg2) {
              $con->commit();
            } else {
              $con->rollBack();
              exit('NG');
            }
          }
          // sql文の発行
          $sql = 'insert into userTempTbl (userId, tempId, pwd, limitTime)
          values (?, ?, ?, ?)';
          $stmt = $con->prepare($sql);
          // sql文の実行
          $flg = $stmt->execute(array($userId, $tempPass['address'], $tempPass['pass'], date(YmdHis, strtotime ("+1 day"))));
          // $stmtのクローズ
          $stmt->closeCursor();
          $mailSendFlg = false;
          if ($flg) {
            $con->commit();
            $mailSendFlg = true;
          } else {
            $con->rollBack();
          }
        } catch (PDOException $e) {
          $con->rollBack();
          die('err:'.$e->getMessage());
        }
        if ($mailSendFlg) {
          require_once("./regmail/qdsmtp.php");
          require_once("./regmail/qdmail.php");
          mb_language("ja");
          $mail = new Qdmail();
          //   if ($mailSendFlg) {
          $param = array(
              'host' => 'smtp.lolipop.jp',
              'port' => 587,
              'protocol' => 'SMTP_AUTH',
              'user' => 'sns-admin@t-sal.net',
              'pass' => 'suzumiya',
              'from' => 't-salSNS@t-sal.net',	// 適当なメールアドレスでOK
          );

          // メール本文（html形式）の作成
          $text = '
T-SAL農園 SNS からのお知らせです。
メールアドレス変更を受け付けしました。
下記URLにアクセスし、ログインできることを確認してください。

<http://www.t-sal.net/community/mailSetting.php?id='.$userId.'&address='.$tempPass['address'].'&pass='.$tempPass['pass'].'&mail='.$address.'>

なお、上記リンク先の有効期限は24時間以内となっております。
有効期限を過ぎてしまった場合登録を完了できませんので、
お手数ですが最初からやり直してくださいますようお願いします。

※本メールに覚えがない場合は本メールを破棄してください。
';
          $mail->smtp(true);
          $mail->smtpServer($param);

          $mail->to($address);
          $mail->subject('登録メールアドレス変更受付');
          $mail->from('t-salSNS@t-sal.net','t-salSNS事務局');
          $mail->text($text);
          $mailFlg = $mail->send();

          // メール送信フラグ
          if ($mailFlg) {
            exit('mailOK');
          } else {
            exit('mailNG');
          }
        }
      }
    }
  }
  exit('mailNG');
}

/**
 * デフォルトの公開範囲を設定
 * @param unknown_type $con
 * @param unknown_type $userId
 */
function defaultMailPublicStateSet($con, $userId) {
  try {
    $con->beginTransaction();
    $sql = 'UPDATE userMstTbl SET defaultPublicState = ? WHERE userId = ?';
    $stmt = $con->prepare($sql);
    $bind = array(
        escape($_POST['publicState']),
        $userId
        );
    $flg = $stmt->execute($bind);
    if ($flg) {
      $con->commit();
      echo '変更しました';
    } else {
      $con->rollback();
      echo '失敗しました';
    }
  } catch (Exeption $e) {
    console('補足した例外'.$e->getMessage());
    $con->rollback();
    echo '失敗しました';
  }
  exit();
}

function randPass($addres) {
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
  $tempPass['id'] = $addres;
  $tempPass['address'] = hash('sha256', date(YmdHis).$addres);
  $tempPass['pass'] = hash('sha256', $str);
  return $tempPass;
}
?>