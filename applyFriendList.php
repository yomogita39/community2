<?php
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
$userId = $_SESSION['user']['userid'];
$friendId = $_POST['friendId'];
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);

if ($friendId) {
  $num = friendUpdate($userId, $friendId, $con);
}

if (escape($_POST['proc']) == 'profile') {
  $num = friendUpdate($userId, $friendId, $con);
  if ($num == 1) {
    exit('OK');
  } else {
    exit();
  }
}
// メッセージの受け取り
require_once('./include/inFlg.php');

$html = friendHtml($con, $userId, 'friend', false);


if (!$html) {
  header('location:'.HOME_PAGE);
}
//テンプレートの表示
//$html内配列 friendImage, friendName, friendId
$smarty->assign('html', $html);
$smarty->display("applyFriendList.tpl");
try {
  $con->beginTransaction();
  $sql = 'UPDATE friendMstTbl SET checkFlg = 1 WHERE friendId = ?';
  $stmt = $con->prepare($sql);
  // sql文の実行
  $flg = $stmt->execute(array($userId));
  // $stmtのクローズ
  $stmt->closeCursor();
  if ($flg) {
    $con->commit();
  } else {
    $con->rollback();
  }
} catch (Exception $e) {
  $con->rollback();
}
db_close($con);
if ($num) {
  alert($num);
}
exit();

function friendUpdate($userId, $friendId, $con) {
  $now = date(YmdHis);
  try {
    $con->beginTransaction();
    $sql = 'update friendMstTbl set acceptFlg=1, acceptTime=?
    where (friendId=? and userId=?) OR (userId=? and friendId=?)';
    $stmt = $con->prepare($sql);
    $flg = $stmt->execute(array($now, $userId, $friendId, $userId, $friendId));
    $stmt->closeCursor();
    if ($flg) {
      $con->commit();
      return 1;
    } else {
      return 2;
    }
  } catch (PDOException $e) {
    $con->rollBack();
    return 2;
  }
  exit();
}
function alert ($num) {
  switch ($num) {
    case 1:
      echo "<script type=\"text/javascript\" >";
      echo "alert(\"承認しました\");";
      echo "</script>";
      break;
    case 2:
      echo "<script type=\"text/javascript\" >";
      echo "alert(\"承認に失敗しました。もう一度やり直してください\");";
      echo "</script>";
      break;
  }
}
?>