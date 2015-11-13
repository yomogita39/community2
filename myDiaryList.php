<?php
require_once("Smarty/Smarty.class.php");
require_once('./include/common.php');
require_once('./include/line_define.php');
$smarty = new Smarty();
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
session_start();
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$userImage = $_SESSION['user']['imagepath'];
$year = escape($_GET['year']);
$month = escape($_GET['month']);
$day = escape($_GET['day']);

// ポスト受け取り
require_once('./include/inFlg.php');

if (!$year || !$month || $month > 12 || $day > 31) {
  $year = date(Y);
  $month = date(n);
}
if (9 >= $month) {
  $zero = 0;
}
if ($day) {
  if (9 >= $day) {
    $zero2 = 0;
  }
  $daySql = ' AND createTime BETWEEN '.$year.$zero.$month.$zero2.$day.' AND '.$year.$zero.$month.$zero2.$day.'235959';
} else {
  $daySql = ' AND createTime BETWEEN '.$year.$zero.$month.'01'.' AND '.$year.$zero.$month.'31'.'235959';
}
$sql = 'SELECT
          diaryId, title, photoPath1, createTime,
           (SELECT COUNT(diaryCommentTbl.diaryId)
            FROM diaryCommentTbl
            WHERE diaryMstTbl.diaryId = diaryCommentTbl.diaryId
            ) AS cCount
        FROM
          diaryMstTbl
        WHERE
          userId =?'.$daySql.' ORDER BY diaryId DESC';
$stmt = $con->prepare($sql);
$stmt->execute(array($userId));
$i = 1;
while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $html[$i]['diaryId'] = $data['diaryId'];
  $html[$i]['title'] = $data['title'];
  $cYear = substr($data['createTime'], 0, 4).'年';
  $cYear .= substr($data['createTime'], 5, 2).'月';
  $cYear .= substr($data['createTime'], 8, 2).'日';
  $cYear .= substr($data['createTime'], 10);
  $html[$i]['time'] = $cYear;
  $html[$i]['year'] = $year;
  $html[$i]['month'] = $month;
  $html[$i]['count'] = $data['cCount'];
  if ($data['photoPath1']) {
    $html[$i]['pathFlg'] = true;
  } else {
    $html[$i]['pathFlg'] = false;
  }
  $i++;
}
$url = MYDIARYLIST;
$calendar = calendar(MYDIARYLIST, $userId, $con, $year, $month);

$smarty->assign('userName', $userName);
$smarty->assign('userImage', $userImage);
$smarty->assign('html', $html);
$smarty->assign('calender', $calendar);
$smarty->display("myDiaryList.tpl");
?>