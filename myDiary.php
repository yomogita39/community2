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
$diaryId = escape($_GET['diaryId']);
$proc = escape($_POST['send']);
$comment = escape($_POST['message']);
// ポスト受け取り
require_once('./include/inFlg.php');
$_SESSION['diary']['year'] = null;
$_SESSION['diary']['message'] = null;
$_SESSION['diary']['title'] = null;
$_SESSION['diary']['state'] = null;
$_SESSION['diary']['photo1'] = null;
$_SESSION['diary']['photo2'] = null;
$_SESSION['diary']['photo3'] = null;
if ($proc == 'comment' && $comment != '' && $_SESSION['comment'] != $comment) {
  $_SESSION['comment'] = $comment;
  $now = date(YmdHis);
  try {
    // トランザクション開始
    $con->beginTransaction();

    $sql = 'SELECT MAX(regNo) AS no FROM diaryCommentTbl
            WHERE diaryId=?';
    $stmt = $con->prepare($sql);
    $stmt->execute(array($diaryId));
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data['no']) {
      $no = $data['no'] + 1;
    } else {
      $no = 1;
    }
    $sql = 'INSERT INTO diaryCommentTbl (diaryId, regNo, userId, message, regTime)
            VALUES(?, ?, ?, ?, ?)';
    $stmt = $con->prepare($sql);
    $flg = $stmt->execute(array($diaryId, $no, $userId, nl2br($comment), $now));
    if ($flg) {
      $con->commit();
    } else {
      $con->rollBack();
      $system = '投稿失敗しました。';
    }
    $stmt->closeCursor();
  } catch (Exception $e) {
    $system = '投稿失敗しました。';
    $con->rollBack();
  }
}

// 日記内容の取得
$sql = 'SELECT
          title, message, photoPath1, photoPath2, photoPath3,
           createTime, publicState
        FROM
          diaryMstTbl
        WHERE
          diaryId = ?';
$stmt = $con->prepare($sql);
$stmt->execute(array($diaryId));
$photo = array();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
$i = 0;
if ($data['photoPath1']) {
  $photo[$i] = $data['photoPath1'];
  $i++;
  $_SESSION['diary']['photo1'] = $data['photoPath1'];
}
if ($data['photoPath2']) {
  $photo[$i] = $data['photoPath2'];
  $i++;
  $_SESSION['diary']['photo2'] = $data['photoPath2'];
}
if ($data['photoPath3']) {
  $photo[$i] = $data['photoPath3'];
  $i++;
  $_SESSION['diary']['photo3'] = $data['photoPath3'];
}
$cYear = substr($data['createTime'], 0, 4).'年';
$cYear .= substr($data['createTime'], 5, 2).'月';
$cYear .= substr($data['createTime'], 8, 2).'日';
$cYear .= substr($data['createTime'], 10);
$_SESSION['diary']['year'] = $cYear;
switch ($data['publicState']) {
  case 0: $pState = '全員に公開'; break;
  case 1: $pState = '友達まで公開'; break;
  case 2: $pState = '公開しない'; break;
}
$_SESSION['diary']['state'] = $data['publicState'];
$message = $data['message'];
$_SESSION['diary']['message'] = $message;
$title = $data['title'];
$_SESSION['diary']['title'] = $title;
$stmt->closeCursor();
$sql = 'SELECT
         diaryCommentTbl.message, diaryCommentTbl.regTime,
         userMstTbl.userName, userMstTbl.userId
        FROM
         diaryCommentTbl
        INNER JOIN
         userMstTbl
        ON
         diaryCommentTbl.userId = userMstTbl.userId
        WHERE
         diaryId = ?';
$stmt = $con->prepare($sql);
$stmt->execute(array($diaryId));
$i = 1;
while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $html[$i]['message'] = $data['message'];
  $cYear = substr($data['regTime'], 0, 4).'年';
  $cYear .= substr($data['regTime'], 5, 2).'月';
  $cYear .= substr($data['regTime'], 8, 2).'日';
  $cYear .= substr($data['regTime'], 10);
  $html[$i]['regTime'] = $cYear;
  $html[$i]['userName'] = $data['userName'];
  $html[$i]['userId'] = $data['userId'];
  $i++;
}

$stmt->closeCursor();

$year = escape($_GET['year']);
$month = escape($_GET['month']);
$day = escape($_GET['day']);
$calendar = calendar(MYDIARY, $userId, $con, $year, $month, $diaryId);

$smarty->assign('diaryId', $diaryId);
$smarty->assign('userName', $userName);
$smarty->assign('userImage', $userImage);
$smarty->assign('html', $html);
$smarty->assign('year', $year);
$smarty->assign('month', $month);
$smarty->assign('day', $day);
$smarty->assign('photo', $photo);
$smarty->assign('cYear', $_SESSION['diary']['year']);
$smarty->assign('pState', $pState);
$smarty->assign('message', $message);
$smarty->assign('title', $title);
$smarty->assign('calender', $calendar);
$smarty->assign('system', $system);
$smarty->display("myDiary.tpl");
?>