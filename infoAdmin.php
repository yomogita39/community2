<?php
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

// ポスト受け取り
require_once('./include/inFlg.php');

// セッションに保存された権限は正しいか
$adminFlg = administrator($con, $userId, $auth);
if ($adminFlg) {
  $html = infoList($con);
  $smarty->assign('html', $html);
  $smarty->assign('total', count($html));
  $smarty->display('infoAdmin.tpl');

}

