<?php
require_once('./include/common.php');
require_once('./include/line_define.php');
session_start();
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);

$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$postPhotoName = escape($_POST['postPhotoName']);
$proc = escape($_POST['proc']);
$groupId = escape($_POST['groupId']);
if ($proc == 'group') {
  $groupName = escape($_POST['groupName']);
  if ($postPhotoName != '') {
    $path = renamePath ($postPhotoName, $proc, $groupId);
  } else {
    $path ='';
  }


  try {
    // トランザクション開始
    $con->beginTransaction();

    if ($path != '') {
      $sql = 'update groupMstTbl set groupName=?, regGroupImagePath=?
      where groupId=?
      ';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($groupName, $path, $groupId));
    } else {
      $sql = 'update groupMstTbl set groupName=?
      where groupId=?
      ';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($groupName, $groupId));
    }
    // $stmtのクローズ
    $stmt->closeCursor();

    if ($flg) {
      $con->commit();
      exit('OK');
    } else {
      $con->rollBack();
      exit('NG');
    }
  } catch (Exception $e) {
    $con->rollBack();
    exit('NG');
  }
}
?>