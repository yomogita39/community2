<?PHP
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
$otherId = escape($_GET['userId']);
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// ポスト受け取り
require_once('./include/inFlg.php');
if ($otherId) {
  $html = groupHtml ($con, $otherId, true);
  $sql = "SELECT
  userImage, userName
  FROM
  userMstTbl
  WHERE
  userId = ?";
  $stmt = $con->prepare($sql);
  $ret = $stmt->execute(array($otherId));
  $dataFlg = false;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dataFlg = true;
    $userName = $data['userName'];
    $image = $data['userImage'];
    $stmt->closeCursor();
//     // フラグが0の場合はデフォルト画像をセット
//     if ($imageFlg) {
//       $filePath = './user_image/';
//       $userImagePath = $filePath.$otherId;
//       // ユーザー画像のPATHの確認
//       if (file_exists($userImagePath.'.jpg')) {
//         $fileName = $userImagePath.'.jpg';
//       } elseif (file_exists($userImagePath.'.png')) {
//         $fileName = $userImagePath.'.png';
//       } elseif (file_exists($userImagePath.'.gif')) {
//         $fileName = $userImagePath.'.gif';
//       } elseif (file_exists($userImagePath.'.JPG')) {
//         $fileName = $userImagePath.'.JPG';
//       } elseif (file_exists($userImagePath.'.PNG')) {
//         $fileName = $userImagePath.'.PNG';
//       } elseif (file_exists($userImagePath.'.GIF')) {
//         $fileName = $userImagePath.'.GIF';
//       }
//     } else {
//       $fileName = "./user_image/defaultUserImage.png";
//     }
  }
  if (!$dataFlg) {
    header('location:'.LOGOUT);
  }
  db_close($con);
  $smarty->assign('html', $html);
  $smarty->assign('userImage', $image);
  $smarty->assign('userName', $userName);
  $smarty->display("memberGroupAdmin.tpl");
  exit();

} else {
  //テンプレートの表示
  $html = groupHtml ($con, $userId, true);
  db_close($con);
  $smarty->assign('html', $html);
  $smarty->display("groupAdmin.tpl");
  exit();
}
?>