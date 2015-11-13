<?php
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');
require_once('./include/location.php');
session_start();
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
$smarty = new Smarty();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";

// 値の取得
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$postPhotoName = escape($_POST['postPhotoName']);
$proc = escape($_POST['proc']);

// ポスト受け取り
require_once('./include/inFlg.php');
if ($proc == 'edit') {
  $userName = escape($_POST['userName']);
  $gender = escape($_POST['gender']);
  $place = escape($_POST['place']);
  $intro = nl2br(escape($_POST['intro']));
  $realName = escape($_POST['realName']);
  $year = escape($_POST['year']);
  $month = escape($_POST['month']);
  $day = escape($_POST['day']);
  $birtyday = $year.'-'.$month.'-'.$day;
  if ($postPhotoName) {
    $path = renamePath ($postPhotoName, $proc, $userId);
  } else {
    $path ='';
  }

  // 重複の確認
  $sql = "
  SELECT
  userName AS name
  FROM
  userMstTbl
  WHERE
  userName = ? && userId != ?
  ";
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userName, $userId));
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  // 取得できた場合重複しているためNG
  if ($data['name']) {
    exit('NG2');
  }

  try {
    // トランザクション開始
    $con->beginTransaction();
    // sql文の発行
    $sql = 'update userMstTbl set userImage=?, userName=?, gender=?,
      birthday=?, place=?, introduction=?, realName=?
      where userId=?
    ';
    $stmt = $con->prepare($sql);

    if ($path) {
      if (file_exists($_SESSION['user']['imagepath']) === TRUE && $_SESSION['user']['imagepath'] != './user_image/defaultUserImage.png') {
        unlink($_SESSION['user']['imagepath']);
      }
    } else {
      if ($_SESSION['user']['imagepath'] == './user_image/defaultUserImage.png') {
        $path = './user_image/defaultUserImage.png';
      } else {
        $path = $_SESSION['user']['imagepath'];
      }
    }
    $flg = $stmt->execute(array($path, $userName, $gender, $birtyday, $place, $intro, $realName, $userId));
    // $stmtのクローズ
    $stmt->closeCursor();

    if ($flg) {
      $con->commit();
      $_SESSION['user']['imagepath'] = $path;
      $_SESSION['user']['username'] = $userName;
      $_SESSION['user']['gender'] = $gender;
      $_SESSION['user']['birthday'] = $birtyday;
      $_SESSION['user']['place'] = $place;
      $_SESSION['user']['realName'] = $realName;
      $_SESSION['user']['introduction'] = $intro;
      exit('OK');
    } else {
      $con->rollBack();
      exit('NG');

    }
  } catch (Exception $e) {
    $con->rollBack();
    exit('NG');
  }
}
$sql = "SELECT
gender, birthday, place, introduction, userImage,
userName, lastLoginTime, realName
FROM
userMstTbl
WHERE
userId = ?";
$stmt = $con->prepare($sql);
$ret = $stmt->execute(array($userId));
// データ取得フラグ
$dataFlg = false;
$data = $stmt->fetch(PDO::FETCH_ASSOC);
$userImage = $_SESSION['user']['imagepath'];

// $gender = $_SESSION['user']['gender'];
// $birtyday = $_SESSION['user']['birthday'];
// $place = $_SESSION['user']['place'];
// $intro = $_SESSION['user']['introduction'];
// $realName = $_SESSION['user']['realName'];
$gender = $data['gender'];
$birtyday = $data['birthday'];
$place = $data['place'];
$intro = $data['introduction'];
$realName = $data['realName'];

// $birthdayの値を年、月、日に分ける
if ($birtyday) {
  $year = substr($birtyday, 0, 4);
  $month = substr($birtyday, 5, 2);
  $day = substr($birtyday, 8, 8);
}
// // profileEdit.jsからリクエストがあった場合の処理
// if ($proc == 'getSession') {
//   $data = array(
//       'place'=> $place,
//       'month'=> $month,
//       'day'=> $day,
//       'gender'=> $gender
//       );
// //   $data['place'] = $place;
// //   $data['month'] = $month;
// //   $data['day'] = $day;
// //   $data['gender'] = $gender;
// //   json_encode($data);
//   echo json_encode($data);//$data;
//   exit();
// }


$birtydayYear = forYear($year);
$birtydayMonth = forMonth($month);
$birtydayDays = forDays($day);
// echo '$birtydayDays'.$day.$birtydayDays;
$sex = genderCheck($gender);
$location = placeCheck($place);
$_SESSION['proc'] = 'profile';
$select = 'selected="selected"';
$smarty->assign($sex, $select);
$smarty->assign($location, $select);
$smarty->assign('year', $birtydayYear);
$smarty->assign($birtydayMonth, $select);
$smarty->assign($birtydayDays, $select);
$smarty->assign('gender', $gender);
$smarty->assign('realName', $realName);
$smarty->assign('intro', str_replace(array('<br />'), '', $intro));
$smarty->assign('userImage', $userImage);;
$smarty->assign('userName', $userName);
$smarty->display('editProfile.tpl');

?>