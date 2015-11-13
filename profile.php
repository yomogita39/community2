<?php
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');
require_once('./api/model_thread.php');
$smarty = new Smarty();
session_start();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$userImage = $_SESSION['user']['imagepath'];
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// ポスト受け取り
require_once('./include/inFlg.php');
// 他ユーザーのIDもしくは自分自身のID
$otherId = escape($_GET['userId']);
$sql = "SELECT
         gender, birthday, place, introduction, userImage,
         userName, lastLoginTime, realName
        FROM
        userMstTbl
        WHERE
         userId = ?";
$stmt = $con->prepare($sql);
if ($otherId) {
  $ret = $stmt->execute(array($otherId));
} else {
  $ret = $stmt->execute(array($userId));
}
// データ取得フラグ
$dataFlg = false;
while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $dataFlg = true;
  // 各要素が未設定の場合はその旨表示
  // 性別
  if ($data['gender']) {
    $edit['gender'] = $data['gender'];
    $_SESSION['user']['gender'] = $data['gender'];
  } else {
    $edit['gender'] = '未設定';
    $_SESSION['user']['gender'] = '';
  }
  // 誕生日
  if ($data['birthday']) {
    $edit['birthday'] = $data['birthday'];
    $_SESSION['user']['birthday'] = $data['birthday'];
  } else {
    $edit['birthday'] = '未設定';
    $_SESSION['user']['birthday'] = '';
  }
  // 所在地
  if ($data['place']) {
    $edit['place'] = $data['place'];
    $_SESSION['user']['place'] = $data['place'];
  } else {
    $edit['place'] = '未設定';
    $_SESSION['user']['place'] = '';
  }
  // 自己紹介
  if ($data['introduction']) {
    $edit['introduction'] = $data['introduction'];
    $_SESSION['user']['introduction'] = $data['introduction'];
  } else {
    $edit['introduction'] = '未設定';
    $_SESSION['user']['introduction'] = '';
  }
  // 本名
  if ($data['realName']) {
    $edit['realName'] = $data['realName'];
    $_SESSION['user']['realName'] = $data['realName'];
  } else {
    $edit['realName'] = '未設定';
    $_SESSION['user']['realName'] = '';
  }
  // 他ユーザのプロフィールの場合の設定
  if ($otherId) {
    $userName = $data['userName'];
    $image = $data['userImage'];
    $stmt->closeCursor();
  }
  $last = $data['lastLoginTime'];
}
// 送信されてきたユーザーIDがデータベースに存在しない場合リダイレクト
if (!$dataFlg) {
  db_close($con);
  header('location:'.LOGOUT);
  exit();
}
db_close($con);
// プロフィール編集ボタンの表示非表示
if ($otherId) {
  if ($userId != $otherId) {
    $style = 'style="display:none"';
    $html = groupHtml($con, $otherId, true, true);
    // 日記リストの生成 diaryId, userId, title, userName, time, cCount
    $diaryHtml = getDiaryList($con, $userId, true, $otherId, true);
    // 友達一覧の生成 friendImage, friendId, friendName
    $friendHtml = friendHtml($con, $otherId, 'user', true, true);
    // プロフィールの人物は友達かどうか
    $checkFriend = model_thread::checkOtherId($con, $userId, $otherId, 'friend');
    if ($checkFriend === TRUE) {
      $friendFlg = 2; // 友達
    } else if ($checkFriend === 'unFriend') {
      $friendFlg = 1; // 申請中
    } else if ($checkFriend === 'unFriend2'){
      $friendFlg = 3; // 申請されている
    } else {
      $friendFlg = 0; // 友達じゃない
    }
    $smarty->assign('userName', $userName);
    $_SESSION['friend'.$otherId]['userName'] = $userName;
    $smarty->assign('userId', $otherId);
    $smarty->assign('friendId', $otherId);
    $_SESSION['friend'.$otherId]['userId'] = $otherId;
    $smarty->assign('style', $style);
    $smarty->assign('html', $html);
    $smarty->assign('friendHtml', $friendHtml);
    $smarty->assign('diaryHtml', $diaryHtml);
    $smarty->assign('friendFlg', $friendFlg);
    $smarty->assign('gender', $edit['gender']);
    $smarty->assign('birthday', $edit['birthday']);
    $smarty->assign('place', $edit['place']);
    $smarty->assign('introduction', $edit['introduction']);
    $smarty->assign('userImage', $image);
    $smarty->assign('last', $last);
    $_SESSION['friend'.$otherId]['userImage'] = $image;
    $smarty->display('memberProfile.tpl');
    exit();
  } else {
    $style = 'style="display:"';
    $smarty->assign('userName', $userName);
    $smarty->assign('style', $style);
    $smarty->assign('gender', $edit['gender']);
    $smarty->assign('birthday', $edit['birthday']);
    $smarty->assign('place', $edit['place']);
    $smarty->assign('introduction', $edit['introduction']);
    $smarty->assign('realName', $edit['realName']);
    $smarty->assign('userImage', $userImage);
    $smarty->assign('last', $last);
    $smarty->display('profile.tpl');
    exit();
  }
} else {
  $style = 'style="display:"';
  $smarty->assign('userName', $userName);
  $smarty->assign('style', $style);
  $smarty->assign('gender', $edit['gender']);
  $smarty->assign('birthday', $edit['birthday']);
  $smarty->assign('place', $edit['place']);
  $smarty->assign('introduction', $edit['introduction']);
  $smarty->assign('realName', $edit['realName']);
  $smarty->assign('userImage', $userImage);
  $smarty->assign('last', $last);
  $smarty->display('profile.tpl');
  exit();
}


?>