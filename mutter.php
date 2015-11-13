<?php
require_once('./include/line_define.php');
require_once('./include/common.php');
session_start();

$proc = escape($_POST['proc']);
if ($proc !== 'mutter' && $proc !== 'mutter2') {
  exit('NG');
}
// セッション情報の取得
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
$message = escape($_POST['message']);
$pState = escape($_POST['public']);
$now = date(YmdHis);
if ($message == '' || $pState == '') {
  exit('NG');
}
try {
  $con->beginTransaction();
  $sql = 'INSERT INTO mutterMstTbl (userId, message, publicState, regTime)
          VALUES (?, ?, ?, ?)';
  $stmt = $con->prepare($sql);
  $flg = $stmt->execute(array($userId, $message, $pState, $now));
  $stmt->closeCursor();
  if ($flg) {
    $con->commit();
  } else {
    $con->rollback();
    exit('投稿に失敗しました');
  }
} catch (Exception $e) {
  $con->rollback();
  exit('投稿に失敗しました');
}
// ひとりごと一覧の生成 userId, userName, userImage, message, time

$html = '';
if ($proc === 'mutter') {
//   $i = 0;
  $mutterHtml = getMutter($con, $userId);
  if ($mutterHtml) {
    foreach ($mutterHtml as $var) {
      $html .= '
      <tr class="tr_mutterList">
      <td class="td_mutterListImage">
      <a href="./profile.php?userId='.$var['userId'].'"><img src="'.$var['userImage'].'" class="image_s" alt="'.$var['userName'].'画像"></a>
      </td>
      <td class="td_mutterListMessage">
      <div>
      '.$var['time'].'&nbsp;<a href="./profile.php?userId='.$var['userId'].'">'.$var['userName'].'</a></div>
      <div>
      '.$var['message'].'</div>
      </td>
      </tr>';
//       if (count($mutterHtml) -1 == $i) {
//         $html .= '<input type="hidden" id="total" name="total" value='.count($mutterHtml).'>';
//       }

//       $i++;
    }
    $html .= '<input type="hidden" id="total" name="total" value='.count($mutterHtml).'>';
    exit($html);
  }
} else {
//   $i = 0;
  $mutterHtml = getMutter($con, $userId, true);
  if ($mutterHtml) {
    foreach ($mutterHtml as $var) {
      $html .= '
      <tr class="tr_mutterList2">
      <td class="td_mutterListImage2">
      <a href="./profile.php?userId='.$var['userId'].'"><img src="'.$var['userImage'].'" class="image_s" alt="'.$var['userName'].'画像"></a>
      </td>
      <td class="td_mutterListMessage2">
      <div>
      '.$var['time'].'&nbsp;<a href="./profile.php?userId='.$var['userId'].'">'.$var['userName'].'</a></div>
      <div>
      '.$var['message'].'</div>
      </td>
      </tr>';
//       if (count($mutterHtml) == $i) {
//         $html .= '<input type="hidden" id="total" name="total" value='.count($mutterHtml).'>';
//       }

//       $i++;
    }
    $html .= '<input type="hidden" id="total" name="total" value='.count($mutterHtml).'>';
    exit($html);
  }
}
?>