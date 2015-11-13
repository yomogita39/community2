<?php
/**
 * home画面を生成する
 */
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');
require_once('./api/getHoliday.php');
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
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// メッセージの受け取り
require_once('./include/inFlg.php');

// ユーザー画像の取得
$sql = "SELECT
          userImage, divisionNo
        FROM
          userMstTbl
        WHERE
          userId = :id";
$stmt = $con->prepare($sql);
// :idをuserIdに置き換え
$stmt->bindValue(':id', $userId, PDO::PARAM_STR);
$ret = $stmt->execute();
// 実行結果の受け取り
$data = $stmt->fetch(PDO::FETCH_ASSOC);
// $imageFlg = $data['userImageFlg'];
$stmt->closeCursor();
// セッション情報として保存
$_SESSION['user']['imagepath'] = $data['userImage'];
// 圃場状態の画像を取得するコマンドの作成
if ($data['divisionNo'] != NULL) {
  $cmd = 'ls -t ./sideimage/side'.$data['divisionNo'].' | head -1';
  exec($cmd, $output);
  $fieldState = './sideimage/side'.$data['divisionNo'].'/'.$output[0];
  $companyId = intval(substr($data['divisionNo'], 0, 3));
  $farmId = intval(substr($data['divisionNo'], 4, 3));
  $sql = 'SELECT
           airtmp AS tmp, arprss AS prss, rhum, sun, regTime
          FROM
           farmStateTbl
          WHERE
           farmId = ? AND companyId = ? AND regTime IN (SELECT MAX(regTime) FROM farmStateTbl)
           ';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($farmId, $companyId));
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  $farmState['tmp'] = $data['tmp'];
  $farmState['prss'] = $data['prss'];
  $farmState['rhum'] = $data['rhum'];
  $farmState['sun'] = $data['sun'];
  $farmState['regTime'] = $data['regTime'];
} else {
  $fieldState = '';
  $sql = 'SELECT
  airtmp AS tmp, arprss AS prss, rhum, sun, regTime
  FROM
  farmStateTbl
  WHERE
  farmId = ? AND companyId = ? AND regTime IN (SELECT MAX(regTime) FROM farmStateTbl)
  ';
  $stmt = $con->prepare($sql);
  $stmt->execute(array(1, 1));
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  $farmState['tmp'] = $data['tmp'];
  $farmState['prss'] = $data['prss'];
  $farmState['rhum'] = $data['rhum'];
  $farmState['sun'] = $data['sun'];
  $farmState['regTime'] = $data['regTime'];
}


// ホームに表示するグループ一覧のHTML配列の生成
$html = groupHtml($con, $userId, true, true);
// 同じく友達一覧の生成 friendImage, friendId, friendName
$friendHtml = friendHtml($con, $userId, 'user', true, true);
// 日記一覧の生成 diaryId, userId, userName, title, time, cCount
$diaryHtml = getDiaryList($con, $userId);
// ひとりごと一覧の生成 userId, userName, userImage, message, time
$mutterHtml = getMutter($con, $userId);
// インフォメーションの生成
$info = getInfo($con);
// 24節気の生成
$season = getSeason($con);
$todaySeason = $season['today'];
$thisYearSeason = $season['thisYear'];
$seasonCount = $season['count'];
// DBのクローズ
db_close($con);
//テンプレートの表示
$smarty->assign('html', $html);
$smarty->assign('friendHtml', $friendHtml);
$smarty->assign('diaryHtml', $diaryHtml);
$smarty->assign('mutterHtml', $mutterHtml);
$smarty->assign('userImage', $_SESSION['user']['imagepath']);
$smarty->assign('fieldState', $fieldState);
$smarty->assign('userName', $userName);
$smarty->assign('info', $info);
// 気象情報の表示
$smarty->assign('tmp', $farmState['tmp']);
$smarty->assign('prss', $farmState['prss']);
$smarty->assign('rhum', $farmState['rhum']);
$smarty->assign('sun', round($farmState['sun']));
$smarty->assign('regTime', $farmState['regTime']);
$qreki = getQreki(date(Y), date(m), date(d));
$today['week'] = getWeek(date(Ymd));
// 旧暦の表示
$smarty->assign('qreki', $qreki);
// 現在の日付
$smarty->assign('week', $today['week']);
// 前回ログイン時間
$smarty->assign('lastLoginTime', $_SESSION['user']['last']);
// 24節気
$smarty->assign('season', $todaySeason);
$smarty->assign('seasonCount', $seasonCount);
$smarty->assign('seasonHtml', $thisYearSeason);
// 祝日表示
$getHoliday = new getHoliday();
$holidays = $getHoliday->getHolidays(date(Y));
$holiday = explode(' ', $holidays[date(Y).'-'.date(m).'-'.date(d)]);
if ($holiday[0]) {
  $smarty->assign('holiday', $holiday[0]);
}
// 予定表示
$smarty->assign('plan', getPlan($con, $userId));

$smarty->display("copyhome.tpl");

exit();

/**
 * infomationを最新同日の全て取得する
 * @param unknown_type $con
 * @return unknown
 */
function getInfo($con) {
  $sql = 'SELECT
           message, DATE(regTime) AS time
          FROM
           infoMstTbl
          WHERE
           DATE(regTime) = (SELECT MAX(DATE(regTime)) FROM infoMstTbl)
          ORDER BY regTime DESC';
  $stmt = $con->prepare($sql);
  $stmt->execute();
  $i = 1;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $info[$i]['message'] = $data['message'];
    // changeTimeは自作関数、年月日を付与する
    $info[$i]['time'] = changeDate($data['time']);
    $i++;
  }
  return $info;
}

function getPlan($con, $userId) {
  $sql = 'SELECT workPlan AS plan FROM farmWorkerMstTbl
          INNER JOIN farmDiaryMstTbl
          ON farmWorkerMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
          WHERE farmDiaryMstTbl.regUserId = ? AND farmWorkerMstTbl.regDate = ?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, date('Y-m-d')));
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  return $data['plan'];
}

function getSeason($con) {
  // 今日の日付を取得する
  $year = date(Y);
  $month = date(m);
  $day = date(d);

  $date = dateCheck($month, $day);
  // 24節気の取得
  $sql = 'SELECT
            name, year, month, day, time
          FROM
            season24MstTbl
          WHERE
            year = ?
          ';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($year));
  $i = 1;
  $seasonFlg = false;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // 今日の日付に合致した節気があった場合表示ように保持
    if (!$seasonFlg) {
      if ($data['year'] == intval($year) && $data['month'] == $date['month']
          && $data['day'] == $date['day']) {
        $seasonFlg = true;
        $todaySeason['name'] = $data['name'];
        $todaySeason['time'] = $data['time'];
//         echo '<span style="display:none;">success</span>';
      }
    }
    $dbdate = dateCheck($data['month'], $data['day']);
    // 今年度の24節気表示に使用するための値を生成、保持する
    $thisYearSeason[$i]['name'] = $data['name'];
    $thisYearSeason[$i]['date'] = $dbdate['month'].'月'.$dbdate['day'].'日';
    $thisYearSeason[$i]['time'] = $data['time'];
//     echo '<span style="display:none;">'.$thisYearSeason[$i]['name'].'</span>';
    $i++;
  }
  $ret['thisYear'] = $thisYearSeason;
  if ($seasonFlg) {
    $ret['today'] = $todaySeason;
  } else {
    $sql = 'SELECT * FROM season24MstTbl WHERE concat(year, month, day) > ? LIMIT 0, 1';
    $stmt = $con->prepare($sql);
    $stmt->execute(array($year.$month.$day));
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $count['name'] = $data['name'];
    $count['count'] = (strtotime($data['year'].$data['month'].$data['day']) - strtotime($year.$month.$day)) / ( 60 * 60 * 24);
    $ret['count'] = $count;
  }
  return $ret;

}

// 月日がそれぞれ一ケタだった時レイアウト崩れ防止のため先頭に0をつける
function dateCheck ($month, $day) {
  if (mb_strlen($month,"UTF-8") == 1) {
    $date['month'] = '0'.$month;
  } else {
    $date['month'] = $month;
  }
  if (mb_strlen($day,"UTF-8") == 1) {
    $date['day'] = '0'.$day;
  } else {
    $date['day'] = $day;
  }
  return $date;
}
?>
// require_once('./include/line_define.php');
// require_once('./include/common.php');
// $con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);

// try {
//   $con->beginTransaction();
//   $sql = 'SELECT workCompRealDate as realDate, workCompDate FROM farmRoutineWorkTbl
//   WHERE workCompRealDate = ?';
//   $stmt = $con->prepare($sql);
//   $stmt->execute(array('0000-00-00 00:00:00'));
//   $res = array();
//   while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
//     $res[] = $data;
//   }
//   $sql = 'UPDATE farmRoutineWorkTbl SET workCompRealDate=?
//   WHERE workCompDate=? AND workCompRealDate=?';
//   foreach ($res as $var) {
//     $stmt = $con->prepare($sql);
//     $flg = $stmt->execute(array($var['workCompDate'], $var['workCompDate'], $var['realDate']));
//     $stmt->closeCursor();
//     if (!$flg) {
//       $con->rollback();
//       echo 'NG';
//       break;
//     }
//   }
//   if ($flg) {
//     $con->commit();
//     echo 'OK';
//   }


// } catch (Exception $e) {
//   echo 'NG2';
//   $con->rollback();
// }
?>