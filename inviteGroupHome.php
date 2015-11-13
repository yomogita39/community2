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
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// ポスト受け取り
require_once('./include/inFlg.php');
if (array_key_exists('groupId', $_POST)) {
    $gId = escape($_POST['groupId']);
    $num = groupUpdate($gId, $userId, $con);
    if ($num == 1) {
      header('location:'.SITE.'groupHome.php?groupId='.$gId);
      exit();
    }
}

$groupId = escape($_GET['groupId']);
$html = memberHtml ($con, $groupId, true);
if (!$html) {
  header('location:'.LOGOUT);
  db_close($con);
  exit();
}
db_close($con);
//テンプレートの表示
$smarty->assign('html', $html);
$smarty->assign('groupId', $groupId);
$smarty->assign('groupImage', $html[1]['groupImage']);
$smarty->assign('groupName', $html[1]['groupName']);
$smarty->assign('userImage', $fileName);
$smarty->assign('userName', $userName);
$smarty->display("inviteGroupHome.tpl");
if ($num) {
    alert($num);
}
exit();

/**
 * 受け取った引数をインサートし、alertに使用するnumberを返す
 * @param unknown_type $groupId
 * @param unknown_type $userId
 * @param unknown_type $pdo
 * @return number
 */
function groupUpdate($groupId, $userId, $pdo) {
    $now = date(YmdHis);
    try {
        $pdo->beginTransaction();
        $sql = 'update groupMemberTbl set acceptFlg=1, acceptTime=?
           where groupId=? and userId=?';
        $stmt = $pdo->prepare($sql);
        $flg = $stmt->execute(array($now, $groupId, $userId));
        $stmt->closeCursor();
        if ($flg) {
            $pdo->commit();
            return 1;
        } else {
            return 2;
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        return 2;
    }
    exit();
}
/**
 * $numに対応したアラートを返す
 * @param int $num
 */
function alert ($num) {
    switch ($num) {
        case 1:
            echo "<script type=\"text/javascript\" >";
            echo "alert(\"招待を受諾しました\");";
            echo "</script>";
            break;
        case 2:
            echo "<script type=\"text/javascript\" >";
            echo "alert(\"招待の受諾に失敗しました。もう一度やり直してください\");";
            echo "</script>";
            break;
    }
}
?>