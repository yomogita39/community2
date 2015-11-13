<?php
require_once("Smarty/Smarty.class.php");
$smarty = new Smarty();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";

require_once('./include/line_define.php');
require_once('./include/common.php');
require_once('./api/model_thread.php');

session_start();
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$userImage = $_SESSION['user']['imagepath'];

$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// ポスト受け取り
require_once('./include/inFlg.php');

// 未読メールをもつユーザー一覧の生成 mailImage, mailName, mailId, mailUnread, friendCnt
$html = mailHtml ($con, $userId);

// テンプレートの表示
$smarty->assign('userImage', $userImage);
$smarty->assign('userName', $userName);
$smarty->assign('html', $html);
$smarty->display("unreadList.tpl");

db_close($con);
exit();