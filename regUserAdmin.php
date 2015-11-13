<?php
// 仮登録ユーザ情報の管理者専用設定画面
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');

session_start();
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
$smarty = new Smarty();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
// 管理者権限
$auth = $_SESSION['user']['auth'];
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$proc = escape($_POST['proc']);
// ポスト受け取り
require_once('./include/inFlg.php');

// セッションに保存された権限は正しいか
$adminFlg = administrator($con, $userId, $auth);
if (!$adminFlg) {
  header('location:'.LOGOUT);
}

if ($proc == 'reco') {
  // 変更されるユーザID
  $cUser = escape($_POST['userId']);
  // 区画番号の取得
  $div[1] = escape($_POST['divisionNo1']);
  $div[2] = escape($_POST['divisionNo2']);
  $div[3] = escape($_POST['divisionNo3']);
  $div[4] = escape($_POST['divisionNo4']);
  $divFlg = false;
  if ((preg_match("/^[0-9]+$/", $div[1]) && preg_match("/^[0-9]+$/", $div[2]) && preg_match("/^[0-9]+$/", $div[3]) && preg_match("/^[0-9]+$/", $div[4]))
      || ($div[1] == '' && $div[2] == '' && $div[3] == '' && $div[4] == '')) {
    if ($div[1] == '' && $div[2] == '' && $div[3] == '' && $div[4] == '') {
      $div[1] = 1;
      $div[2] = 1;
      $div[3] = 1;
      $div[4] = 0;
    }
    $divFlg = true;
  } else {
    $dat['flg'] = 'NG';
    $dat['message'] = '区画番号は半角数字のみでご入力ください';
    echo json_encode($dat);
    exit();
  }
  if ($divFlg) {
    try {
      $con->beginTransaction();
      $i = 1;
      $j = 3;
      $dbDiv = '';
      // 区画番号の連結
      while (count($div) >= $i) {
        while (strlen($div[$i]) < $j) {
          $div[$i] = '0'.$div[$i];
        }
        $dbDiv .= $div[$i];
        if (count($div) != $i) {
          $dbDiv .= '-';
        }
        $i++;
      }
      if ($dbDiv == '') {
        $dbDiv = NULL;
      }
      $sql = 'UPDATE userMstTbl SET divisionNo = ?, approveTime = NOW()
      WHERE userID = ?';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($dbDiv, $cUser));
      $stmt->closeCursor();

      $address = escape($_POST['mail']);
      // 仮パスワードの発行
      $tempPass = array();
      $tempPass = randPass($address);
      $sql = 'insert into userTempTbl (userId, tempId, pwd, limitTime)
      values (?, ?, ?, ?)';
      $stmt = $con->prepare($sql);
      // sql文の実行
      $flg2 = $stmt->execute(array($cUser, $tempPass['address'], $tempPass['pass'], date(YmdHis, strtotime ("+7 day"))));
      // $stmtのクローズ
      $stmt->closeCursor();
      if ($flg && $flg2) {
        // アップデート完了後、メールを送信
        require_once("./regmail/qdsmtp.php");
        require_once("./regmail/qdmail.php");
        mb_language("ja");
        $mail = new Qdmail();
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
仮登録を受け付けしました。
下記URLにアクセスし、登録を完了してください。

<http://www.t-sal.net/community/regCompletion.php?id='.$cUser.'&address='.$tempPass['address'].'&pass='.$tempPass['pass'].'&mail='.$address.'>

なお、上記リンク先の有効期限は1週間以内となっております。
有効期限を過ぎてしまった場合登録を完了できませんので、
お手数ですが最初からやり直してくださいますようお願いします。

※本メールに覚えがない場合は本メールを破棄してください。
';
        $mail->smtp(true);
        $mail->smtpServer($param);
        $mail->to($address);
        $mail->subject('管理者承認完了');
        $mail->from('t-salSNS@t-sal.net','t-salSNS事務局');
        $mail->text($text);
        $mailFlg = $mail->send();
        if ($mailFlg) {
          $dat['flg'] = 'OK';
          $dat['message'] = '承認しました。ユーザへ承認完了メールが送られます。';
          $con->commit();
        } else {
          $dat['flg'] = 'NG';
          $dat['message'] = 'メールの送信に失敗しました。';
          $con->rollback();
        }
      } else {
        $con->rollback();
        $dat['flg'] = 'NG';
        $dat['message'] = '承認に失敗しました。';
      }
    } catch (Exception $e) {
      $con->rollback();
      $dat['flg'] = 'NG';
      $dat['message'] = '予期せぬエラーにより承認に失敗しました。';
    }
  } else {
    $dat['flg'] = 'NG';
    $dat['message'] = '入力内容に不備があります。ご確認ください。';
  }
  echo json_encode($dat);
  exit();
}
// 未承認ユーザ一覧
$sql = 'SELECT userMstTbl.userId, realName, userImage, realName, mailAddress
        FROM userMstTbl
        LEFT JOIN userTempTbl
        ON userMstTbl.userId = userTempTbl.userId
        WHERE userTempTbl.tempId IS NULL AND regCompFlg = 0';
$stmt = $con->prepare($sql);
$stmt->execute();
$i = 1;
while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $html[$i]['id'] = $data['userId'];
//   $html[$i]['name'] = $data['userName'];
  $html[$i]['image'] = $data['userImage'];
  $html[$i]['name'] = $data['realName'];
  $html[$i]['mail'] = $data['mailAddress'];
  $i++;
}
// 一週間以内の承認ユーザ一覧
$sql = 'SELECT userMstTbl.userId, realName, userImage, mailAddress,
         regCompFlg, approveTime, divisionNo
        FROM userMstTbl
        WHERE approveTime>=ADDDATE(CURDATE(),Interval -7 DAY)';
$stmt = $con->prepare($sql);
$stmt->execute();
$i = 1;
while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $appHtml[$i]['id'] = $data['userId'];
  //   $html[$i]['name'] = $data['userName'];
  $appHtml[$i]['image'] = $data['userImage'];
  $appHtml[$i]['name'] = $data['realName'];
  $appHtml[$i]['app'] = $data['approveTime'];
  $appHtml[$i]['flg'] = $data['regCompFlg'];
  $appHtml[$i]['mail'] = $data['mailAddress'];
  $appHtml[$i]['no'] = $data['divisionNo'];
  $i++;
}
$smarty->assign('total', count($html));
$smarty->assign('appTotal', count($appHtml));
$smarty->assign('html', $html);
$smarty->assign('appHtml', $appHtml);
$smarty->display('regUserAdmin.tpl');

/**
 *
 * @param String $addres
 * @return String $tempPass
 */
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