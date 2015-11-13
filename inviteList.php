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
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// ポスト受け取り
require_once('./include/inFlg.php');
$html = groupHtml($con, $userId, false);

try {
  $con->beginTransaction();
  $sql = 'UPDATE groupMemberTbl SET checkFlg = 1 WHERE userId = ?';
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

//テンプレートの表示
$smarty->assign('html', $html);
$smarty->display("inviteList.tpl");
db_close($con);
exit();


?>