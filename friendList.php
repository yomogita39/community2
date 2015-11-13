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
$userName = $_SESSION['user']['username'];
$userImage = $_SESSION['user']['imagepath'];
$friendId = escape($_GET['friendId']);
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);

// メッセージの受け取り
require_once('./include/inFlg.php');

// 友達一覧の生成 friendImage, friendId, friendName, friendCnt
if ($friendId) {
  $html = friendHtml($con, $friendId, 'user', true);
  $smarty->assign('userImage', $_SESSION['friend'.$friendId]['userImage']);
  $smarty->assign('userName', $_SESSION['friend'.$friendId]['userName']);
} else {
  $html = friendHtml($con, $userId, 'user', true);
  $smarty->assign('userImage', $userImage);
  $smarty->assign('userName', $userName);
}

$smarty->assign('html', $html);
if ($friendId) {
  $smarty->display("memberFriendList.tpl");
} else {
  $smarty->display("friendList.tpl");
}

?>