<?php
require_once('../include/line_define.php');
require_once('../include/common.php');

session_start();
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);

// 管理者権限
$auth = $_SESSION['user']['auth'];
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$proc = escape($_POST['proc']);
$infoId = escape($_POST['infoId']);

switch ($proc) {
  case 'new':insertInfo($con, $userId);break;
  case 'edit':updateInfo($con, $userId, $infoId, $userName);break;
  case 'del':delInfo($con, $userId, $infoId);break;
}

exit();

function insertInfo($con, $userId) {
  $message = escape($_POST['newMessage']);
  $now = date(YmdHis);
  if ($message == '') {
    $ret['flg'] = 'NG';
    echo json_encode($ret);
    exit();
  }
  try {
    $con->beginTransaction();
    $sql = 'INSERT INTO infoMstTbl(regUserId, message, regTime)
            VALUES (?, ?, ?)';
    $stmt = $con->prepare($sql);
    $flg = $stmt->execute(array($userId, $message, $now));
    if ($flg) {
      $con->commit();
      $html = infoList($con);
      $ret['flg'] = 'OK';
      $ret['html'] = infoHtml($con, $html);
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

function updateInfo($con, $userId, $infoId, $userName) {
  $message = escape($_POST['editMessage']);
  if ($message == '') {
    $ret['flg'] = 'NG';
    echo json_encode($ret);
    exit();
  }
  try {
    $con->beginTransaction();
    $sql = 'UPDATE infoMstTbl
            SET message = ?, regUserId = ?
            WHERE infoId = ?';
    $stmt = $con->prepare($sql);
    $flg = $stmt->execute(array($message, $userId, $infoId));
    if ($flg) {
      $con->commit();
      $html = infoList($con);
      $ret['flg'] = 'OK';
      $ret['html'] = infoHtml($con, $html);
      $ret['message'] = nl2br($message);
      $ret['id'] = $infoId;
      $ret['name'] = $userName;
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

function delInfo($con, $userId, $infoId) {
  try {
    $con->beginTransaction();
    $sql = 'DELETE FROM infoMstTbl
            WHERE infoId = ?';
    $stmt = $con->prepare($sql);
    $flg = $stmt->execute(array($infoId));
    if ($flg) {
      $con->commit();
      $html = infoList($con);
      $ret['flg'] = 'OK';
      $ret['html'] = infoHtml($con, $html);
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

function infoHtml($con, $var) {
  $i = 1;
  $count = count($var);
  $html = '';
  while ($count >=$i) {
    $html .= '
                          <tr>
                              <td colspan="5">&nbsp;</td>
                          </tr>
                          <tr id="'.$var[$i]['infoId'].'">
                              <td class="td_infoAdminList_no">'.$i.'</td>
                              <td class="td_infoAdminList_userName">'.$var[$i]['userName'].'</td>
                              <td class="td_infoAdminList_message">'.$var[$i]['message'].'</td>
                              <td class="td_infoAdminList_date">'.$var[$i]['regTime'].'</td>
                              <td class="td_infoAdminList_action">
                                  <form>
                                      <input type="hidden" name="infoId" value="'.$var[$i]['infoId'].'">
                                      <div><input type="button" value="編集" class="button_s edit"></div>
                                      <div><input type="button" value="削除" class="button_s textColorRed del"></div>
                                  </form>
                              </td>
                          </tr>';
    if ($i == $count) {
      $html .= '<input type="hidden" id="total" name="total" value='.$count.'>';
    }
    $i++;
  }
  return $html;
}

