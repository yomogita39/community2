<?php
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');
require_once('./include/location.php');
$smarty = new Smarty();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $smarty->assign("year", forYear(''));
    $smarty->display("regUserInfo.tpl");
    exit();
} else {
//   if ($_POST['mailCheck']) {
//     $userId = htmlspecialchars($_POST['userId'], ENT_QUOTES);
//     if (isValidEmailFormat($userId) === TRUE) {
//       echo true;
//       exit();
//     } else {
//       echo false;
//       exit();
//     }
//   }
  if ($_POST['regcomp'] == 'comp') {
    // 入力された値の取得
    // メールアドレス
    $userId = escape($_POST['userId']);
    // パスワード
    $pass = escape($_POST['password']);
    // ニックネーム
    $userName = escape($_POST['userName']);
    // 氏名
    $realName = escape($_POST['realName']);
    // パスワード再確認
    $passCheck = escape($_POST['repassword']);
    // 年
    $year = escape($_POST['year']);
    // 月
    $month = escape($_POST['month']);
    // 日
    $day = escape($_POST['day']);
    // 性別
    $gender = escape($_POST['gender']);
    // 都道府県
    $place = escape($_POST['place']);
    // 必須入力は入力されているか
    if ($userId != '' && $pass != '' && $userName != '' && $realName != '' &&
         $passCheck != '' && $year != '' && $month != '' && $day != '' &&
         gender != '' && $place != '') {
      // パスワードの文字数をチェックする
      $passLength = strlen($pass);
      $repassLength = strlen($passCheck);
      if (preg_match("/^[a-zA-Z0-9]+$/", $pass) && preg_match("/^[a-zA-Z0-9]+$/", $passCheck)
          && $passLength >= 6 && $repassLength >= 6 && $pass == $passCheck) {
        $passFlg = true;
      } else {
        echo 2;
        exit();
      }
      try {
        // PDOオブジェクトの生成
        $pdo = new PDO("mysql:dbname=LA04088615-line;host=mysql593.phy.lolipop.jp", DB_UID, DB_PWD);
      } catch(PDOException $e) {
        die('err:'.$e->getMessage());
      }
      // データベース内の書式設定をUTF8に
       $pdo->query('SET NAMES utf8');
      // sql文の発行
      $sql = "
      SELECT
      mailAddress AS mail,userName AS name
      FROM
      userMstTbl
      WHERE
      mailAddress = :id OR userName = :name
      ";
      // データベース検索
      $stmt = $pdo->prepare($sql);
      // sql文の：idをPOSTしたuserIdと置き換え
      $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
      $stmt->bindValue(':name', $userName, PDO::PARAM_STR);
      // 検索結果の実行
      $stmt->execute();
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      db_close($pdo);
      $mailCheck = isValidEmailFormat($userId);
      if (!$data['mail'] && !$data['name'] && $passFlg && $mailCheck) {
        session_start();
        // 次のページへ持っていく登録情報を保存する
        $_SESSION['userId'] = $userId;
        $_SESSION['userName'] = $userName;
        $_SESSION['realName'] = $realName;
        $_SESSION['data'] = $data;
        $_SESSION['pass'] = $pass;
        $_SESSION['birthday'] = $year.'-'.$month.'-'.$day;
        $_SESSION['gender'] = $gender;
        $_SESSION['place'] = $place;
        $_SESSION['reg']['flg'] = true;
        $alert = 'success';
        echo $alert;
        exit();
        //           header('location:'. REGISTRATIONCHECK);
      }
    }
  }
  if ($data) {
    if ($data['mail']) {
      $alert = 'mail';
    } elseif ($data['name']) {
      $alert = 'name';
    } else {
      $alert = 'other';
    }
  }
  echo $alert;
  exit();
}
?>