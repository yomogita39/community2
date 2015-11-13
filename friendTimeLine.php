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
$friendId = $_GET['friendId'];
$userImage = $_SESSION['user']['imagepath'];

$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);

// 送られてきたIDは正しいか
if( model_thread::checkOtherId($con, $userId, $friendId, 'friend') !== TRUE ){
  unset($_SESSION['user']['friendId']);
  unset($_SESSION['user']['friendName']);
  // 不正IDはリダイレクト
  header('location:'. LOGIN_PAGE);
  exit();
} else {
  $_SESSION['user']['friendId'] = $friendId;
}
$ret = model_thread::getThreadComment($con, $friendId, 0, 'friend', $userId);

//全件取得するので、直近のlimit件のみ表示する
$limit=10;
// $ret = ar_slice($ret, $limit, 0);

//$ret = model_thread::createHtml($ret, $userId);
$ret = model_thread::createHtml($ret, $userId, 'friend');
//listView($ret['html'], $ret['lastid'], $ret['firstid'], $gid);
// ポスト受け取り
require_once('./include/inFlg.php');
// テンプレートの表示
$smarty->assign('userImage', $userImage);
$smarty->assign('userName', $userName);
$smarty->assign('html', $ret);
$smarty->assign('firstid', $ret['firstid']);
$smarty->assign('lastid', $ret['lastid']);
$smarty->assign('friendId', $friendId);
$smarty->assign('friendName', $_SESSION['user']['friendName']);
$smarty->display("friendTimeLine.tpl");

db_close($con);
exit();