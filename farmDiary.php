<?php
/**
 * firmDiary画面を生成する
*/
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
// セッション情報の取得
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$userImage = $_SESSION['user']['imagepath'];
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// メッセージの受け取り
require_once('./include/inFlg.php');

$date = escape($_GET['date']);
$month = escape($_GET['month']);
$year = escape($_GET['year']);

if (!$date) {
  $date = date(Y).'-'.date(m).'-'.date(d);
}
$sql = 'SELECT
         workSchedule AS workS, work, diaryDate AS date,
         date_format(diaryDate,"%w") AS week
        FROM
         farmDiaryMstTbl
        WHERE
         regUserId = ?
        AND
         diaryDate = ?';
$stmt = $con->prepare($sql);
$stmt->execute(array($userId, changeDate($date, false)));
while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $workS = $data['workS'];
  $work = $data['work'];
}
// 曜日を返す
$week = getWeek($date);
$calendar = farmCalendar($userId, $con, FARMDIARY, $date, $year, $month);
$smarty->assign('calendar', $calendar);
$smarty->assign('userImage', $userImage);
$smarty->assign('userName', $userName);
$smarty->assign('workS', $workS);
$smarty->assign('work', $work);
$smarty->assign('week', $week);
$smarty->assign('cDate', changeDate($date));
$smarty->assign('date', $date);
$smarty->display('farmDiary.tpl');
?>