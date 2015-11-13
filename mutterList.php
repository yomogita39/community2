<?php
require_once('./include/line_define.php');
require_once('./include/common.php');
require_once("Smarty/Smarty.class.php");
$smarty = new Smarty();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
session_start();

// セッション情報の取得
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$userImage = $_SESSION['user']['imagepath'];
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// メッセージの受け取り
require_once('./include/inFlg.php');

// ひとりごと一覧の生成 userId, userName, userImage, message, time
$html = getMutter ($con, $userId, true);

$smarty->assign('userName', $userName);
$smarty->assign('userImage', $userImage);
$smarty->assign('html', $html);
$smarty->display('mutterList.tpl');
?>