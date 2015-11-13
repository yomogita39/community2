<?PHP
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
$gid = $_SESSION['user']['gid'];
$userImage = $_SESSION['user']['imagepath'];
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);

$gid = escape($_GET['groupId']);
// 送られてきたIDは正しいか
if( model_thread::checkOtherId($con, $userId, $gid, 'group', true) !== TRUE ){
  unset($_SESSION['user']['gid']);
  unset($_SESSION['user']['groupName']);
  // 不正IDはリダイレクト
  header('location:'. LOGIN_PAGE);
  exit();
} else {
  $_SESSION['user']['gid'] = $gid;
}
// ポスト受け取り
require_once('./include/inFlg.php');
$ret = model_thread::getThreadComment($con, $gid, 0, 'group', $userId);

//全件取得するので、直近のlimit件のみ表示する
$limit=50;
$ret = ar_slice($ret, $limit, 0);

//$ret = model_thread::createHtml($ret, $userId);
$ret = model_thread::createHtml($ret, $userId);
//listView($ret['html'], $ret['lastid'], $ret['firstid'], $gid);

// テンプレートの表示
$smarty->assign('userImage', $userImage);
$smarty->assign('userName', $userName);
$smarty->assign('html', $ret);
$smarty->assign('firstid', $ret['firstid']);
$smarty->assign('lastid', $ret['lastid']);
$smarty->assign('gid', $gid);
$smarty->assign('groupId', $gid);
$smarty->assign('groupName', $_SESSION['user']['groupName']);
$smarty->display("timeLine.tpl");

db_close($con);
exit();


// function createHtml($dat, $uid) {
//   session_start();
//   $ret = array();
// //   $lastid = -1;
// //   $firstid = -1;
//   $gravity = 0;
//   $i = 1;
//   foreach($dat as $v) {
//     if($v['uid']==$uid) {
//       // 左右判定に使用0=L, 1=R
//       $gravity = 0;
//     } else {
//       $gravity = 1;
//     }
//     $ret[$i]["gravity"] = $gravity;
//     $ret[$i]["userName"] = $v['name'];
//     $ret[$i]["time"] = $v['ctime'];
//     $ret[$i]["message"] = escape($v['message']);
//     $ret[$i]["userImage"] = $_SESSION['group'][$v['uid']];
// //    if($v['cid']>$lastid){
// //       $lastid = $v['cid'];
// //     }
// //     if($firstid==-1){
// //       $firstid=$v['cid'];
// //     }
//     $i++;
// //     echo $firstid;
// //     echo $lastid;
//   }
// //   $ret['lastid'] = $lastid;
// //   $ret['firstid'] = $firstid;
//   return $ret;
// }

?>