<?php
require_once("Smarty/Smarty.class.php");
require_once('./include/common.php');
require_once('./include/line_define.php');
require_once('./api/model_thread.php');
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
$otherId = escape($_GET['friendId']);
$proc = escape($_POST['send']);
$comment = escape($_POST['message']);

// アクセスが自分自身の場合myDiary.phpにリダイレクト
if ($userId == $otherId) {
  header('location:'.MYDIARY.'?diaryId='.$diaryId);
  exit();
}

// ポスト受け取り
require_once('./include/inFlg.php');

// コメントが投稿された場合
if ($proc == 'comment' && $comment != '' && $_SESSION['comment'] != $comment) {
  $_SESSION['comment'] = $comment;
  $now = date(YmdHis);
  try {
    // トランザクション開始
    $con->beginTransaction();

    // インサートするregNoの判定
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
    // INSERTの実行
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
}
if ($data['photoPath2']) {
  $photo[$i] = $data['photoPath2'];
  $i++;
}
if ($data['photoPath3']) {
  $photo[$i] = $data['photoPath3'];
  $i++;
}
// データベースに格納された日付データを表示ように変換
$cYear = substr($data['createTime'], 0, 4).'年';
$cYear .= substr($data['createTime'], 5, 2).'月';
$cYear .= substr($data['createTime'], 8, 2).'日';
$cYear .= substr($data['createTime'], 10);
// 日記へのアクセスは公開範囲と合致するか
switch ($data['publicState']) {
  case 0: $pState = '全員に公開'; break;
  case 1: $pState = '友達まで公開';
    if( model_thread::checkOtherId($con, $userId, $otherId, 'friend') !== TRUE ) {
      // 不正IDはリダイレクト
      header('location:'. LOGIN_PAGE);
      exit();
    } break;
  case 2: $pState = '公開しない';
    if ($userId != $otherId) {
      header('location:'. LOGIN_PAGE);
      exit();
    }
    break;
}
$message = $data['message'];
$title = $data['title'];
$stmt->closeCursor();

// コメントの取得
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
  $cYear2 = substr($data['regTime'], 0, 4).'年';
  $cYear2 .= substr($data['regTime'], 5, 2).'月';
  $cYear2 .= substr($data['regTime'], 8, 2).'日';
  $cYear2 .= substr($data['regTime'], 10);
  $html[$i]['regTime'] = $cYear2;
  $html[$i]['userName'] = $data['userName'];
  $html[$i]['userId'] = $data['userId'];
  $i++;
}

$stmt->closeCursor();

// 自分のIDと一致しない場合、friendDiary
if ($userId != $otherId) {
  $userId = $otherId;
  $sql = 'SELECT userName, userImage
          FROM userMstTbl
          WHERE userId = ?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId));
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  $userName = $data['userName'];
  $userImage = $data['userImage'];
}

$year = escape($_GET['year']);
$month = escape($_GET['month']);
$calendar = calendar(FDIARY, $userId, $con, $year, $month, $diaryId, 'friend');

$smarty->assign('diaryId', $diaryId);
$smarty->assign('friendId', $userId);
$smarty->assign('userName', $userName);
$smarty->assign('userImage', $userImage);
$smarty->assign('html', $html);
$smarty->assign('year', $year);
$smarty->assign('month', $month);
$smarty->assign('photo', $photo);
$smarty->assign('cYear', $cYear);
$smarty->assign('pState', $pState);
$smarty->assign('message', $message);
$smarty->assign('title', $title);
$smarty->assign('calender', $calendar);
$smarty->assign('system', $system);
$smarty->display("friendDiary.tpl");
?>