<?PHP
/**
 * グループのホーム画面
 */
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');
require_once('./api/model_thread.php');
$smarty = new Smarty();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";

session_start();
$userId = $_SESSION['user']['userid'];
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// ポスト受け取り
require_once('./include/inFlg.php');
$gid = htmlspecialchars(escape($_GET['groupId']), ENT_QUOTES);
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
$sql = 'SELECT
          groupMstTbl.regGroupImagePath, groupMstTbl.groupName,
          userMstTbl.userId, userMstTbl.userName, userMstTbl.userImage
        FROM
          groupMstTbl
        INNER JOIN
          groupMemberTbl
        ON
          groupMstTbl.groupId = groupMemberTbl.groupId
        INNER JOIN
          userMstTbl
        ON
          groupMemberTbl.userId = userMstTbl.userId
        WHERE
          groupMemberTbl.groupId = ? AND acceptFlg = 1';
$stmt = $con->prepare($sql);
$stmt->execute(array($gid));

$i = 1;
// smartyに渡す配列の生成
$html = array();
$filePath = './user_image/';
while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $groupMemberId = $data['userId'];
    $html[$i]['userImage'] = $data['userImage'];
    $_SESSION['group'][$groupMemberId] = $html[$i]['userImage'];
    // グループ名の表示に使用
    $groupName = $data['groupName'];
    $_SESSION['user']['groupName'] = $groupName;
    // グループ画像のPATH
    $groupImage = $data['regGroupImagePath'];
    $html[$i]['userName'] = $data['userName'];
    $html[$i]['userId'] = $groupMemberId;
    $i++;
}

// グループ作成者かどうかをチェック
$sql = 'SELECT
         groupId
        FROM
         groupMemberTbl
        WHERE
         userId=:id AND applyUser=:id AND groupId=:gid';
$stmt = $con->prepare($sql);
$stmt->bindValue(':id', $userId, PDO::PARAM_STR);
$stmt->bindValue(':gid', $gid, PDO::PARAM_STR);
$stmt->execute();
$leaderFlg = 0;
while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $leaderFlg = 1;
}

// メンバー総数の表示に使用
$maxMember = $i-1;
// テンプレートの表示
$smarty->assign("groupId", $_SESSION['user']['gid']);
$smarty->assign("flg", $leaderFlg);
$smarty->assign("groupImage", $groupImage);
$smarty->assign("groupName", $groupName);
$smarty->assign("maxMember", $maxMember);
$smarty->assign("html", $html);
$smarty->display("groupHome.tpl");
?>