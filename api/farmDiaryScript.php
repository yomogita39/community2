<?php
require_once('../include/line_define.php');
require_once('../include/common.php');

session_start();
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);

$userId = $_SESSION['user']['userid'];
$proc = escape($_POST['proc']);

switch ($proc) {
  case 'work':insertWork($con, $userId);break;
  case 'edit':updateInfo($con, $userId, $infoId);break;
  case 'del':delInfo($con, $userId, $infoId);break;
}

exit();

function insertWork($con, $userId) {
  $work = escape($_POST['work']);
  $schedule = escape($_POST['schedule']);
  $date = escape($_POST['date']);
  if (!$work && !$schedule || !$date) {
    exit();
  }
  $sql = 'SELECT
           farmDiaryId AS fId
          FROM
           farmDiaryMstTbl
          WHERE
           diaryDate = ?
          AND
           regUserId = ?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($date, $userId));
  $flg = false;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $fId = $data['fId'];
    $flg = true;
  }
  try {
    $con->beginTransaction();
    if ($flg) {
    $sql = 'UPDATE farmDiaryMstTbl
            SET workSchedule=?, work=?
            WHERE regUserId = ?
            AND farmDiaryId = ?';
    $stmt = $con->prepare($sql);
    $dbFlg = $stmt->execute(array(nl2br($schedule), nl2br($work), $userId, $fId));
    } else {
      $sql = 'INSERT INTO farmDiaryMstTbl
               (regUserId, workSchedule, work, diaryDate)
              VALUES
               (?, ?, ?, ?)';
      $stmt = $con->prepare($sql);
      $dbFlg = $stmt->execute(array($userId, nl2br($schedule), nl2br($work), $date));
    }
    if ($dbFlg) {
      $con->commit();
      $ret['flg'] = 'OK';
    } else {
      $con->rollback();
      $ret['flg'] = 'NG';
    }
  } catch (Exception $e) {
    $con->rollback();
    $ret['flg'] = 'NG';
  }
  echo json_encode($ret);
  exit();
}