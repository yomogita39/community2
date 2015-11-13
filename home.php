<?PHP
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
$con = db_connect(, DB_UID, DB_PWD, DB_NAME);

switch ($_POST['fieldState']) {
  case 'fieldState':getFieldState($con, $userId);
  default:break;
}

// メッセージの受け取り
require_once('./include/inFlg.php');

// ユーザー画像の取得
$sql = "SELECT
          userImage
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

// ホームに表示するグループ一覧のHTML配列の生成
$html = groupHtml($con, $userId, true, true);
// 同じく友達一覧の生成 friendImage, friendId, friendName
$friendHtml = friendHtml($con, $userId, 'user', true, true);
// 日記一覧の生成 diaryId, userId, userName, title, time, cCount
$diaryHtml = getDiaryList($con, $userId);
// 自分の日記に対するコメントの一覧を生成する
$diaryNewCommentHtml = getDiaryNewCommentList($con, $userId);
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
$smarty->assign('diaryNewCommentHtml', $diaryNewCommentHtml);
$smarty->assign('mutterHtml', $mutterHtml);
$smarty->assign('userImage', $_SESSION['user']['imagepath']);
$smarty->assign('userName', $userName);
$smarty->assign('info', $info);
// 旧暦の表示
$qreki = getQreki(date(Y), date(m), date(d));
$today['week'] = getWeek(date(Ymd));
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
// キャッシュ無効
$smarty->assign('round', ceil(microtime(true)*1000).'='.ceil(microtime(true)*1000));

$smarty->display("home.tpl");

exit();

/**
 * 自分の日記に寄せられた最新のコメントを取得する
 */
function getDiaryNewCommentList($con, $userId) {
  $sql = 'SELECT
           diaryCommentTbl.regTime, diaryCommentTbl.message, userMstTbl.userName, userMstTbl.userId, diaryMstTbl.title, diaryMstTbl.diaryId,
            (SELECT COUNT(diaryCommentTbl.diaryId)
            FROM diaryCommentTbl
            WHERE diaryMstTbl.diaryId = diaryCommentTbl.diaryId
            ) AS cCount
          FROM
           diaryMstTbl
          LEFT JOIN
           diaryCommentTbl
          ON
           diaryMstTbl.diaryId = diaryCommentTbl.diaryId
          INNER JOIN
           userMstTbl
          ON
           diaryCommentTbl.userId = userMstTbl.userId
          WHERE
           diaryMstTbl.userId = ?
          ORDER BY diaryCommentTbl.regTime DESC LIMIT 0, 5';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId));

  $res = array();
  $i = 0;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $res[] = $data;
    $date = substr($data['regTime'], 5, 2).'月';
    $date .= substr($data['regTime'], 8, 2).'日';
    $res[$i]['regTime'] = $date;
    $i++;
  }
  return $res;
}

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
    $info[$i]['message'] = nl2br($data['message']);
    // changeDateは自作関数、年月日を付与する
    $info[$i]['time'] = changeDate($data['time']);
    $i++;
  }
  return $info;
}

/**
 * 本日の予定を取得する
 * @param pdoObject $con
 * @param ユーザ識別番号 $userId
 */
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
/**
 * 24節気を取得する
 * @param pdoObject $con
 */
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
      }
    }
    $dbdate = dateCheck($data['month'], $data['day']);
    // 今年度の24節気表示に使用するための値を生成、保持する
    $thisYearSeason[$i]['name'] = $data['name'];
    $thisYearSeason[$i]['date'] = $dbdate['month'].'月'.$dbdate['day'].'日';
    $thisYearSeason[$i]['time'] = $data['time'];
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

/**
 *
 * @param pdoObject $con
 * @param ユーザ固有の識別番号 $userId
 */
function getFieldState($con, $userId) {
  // 圃場番号の取得
  $sql = "SELECT
           divisionNo
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
  // 圃場状態の画像を取得するコマンドの作成
  if ($data['divisionNo'] != NULL) {
    $cmd = 'ls -t ./sideimage/side'.$data['divisionNo'].' | head -1';
    exec($cmd, $output);
    $fieldState = './sideimage/side'.$data['divisionNo'].'/'.$output[0];
    $companyId = intval(substr($data['divisionNo'], 0, 3));
    $farmId = intval(substr($data['divisionNo'], 4, 3));
    $divNo = intval(substr($data['divisionNo'], -3, 3));

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
    if ($divNo === 0) {
      $a = '<a href="http://http://www2161ui.sakura.ne.jp/community/friendTimeLine.php?friendId=1">管理人</a>';
      $message = '契約区画を表示します。<br/>圃場が必要な方は'.$a.'に<br/>メッセージをください。';
    }
    // キャッシュさせるとIEで読み込まなくなるため、毎回異なる引数で画像を呼び出す
    $loadImage = $fieldState.'?'.(microtime(true)*1000).'='.(microtime(true)*1000);
    $res = '
                        <a href="'.$loadImage.'" rel="prettyPhoto" title="" class="z40">
                            <img src="'.$loadImage.'" class="image_farm" alt="圃場画像">
                        </a>
                        <table class="table_farmState">
                            <tr>
                                <td colspan="2">'.$data['regTime'].'</td>
                            </tr>
                            <tr>
                                <td class="td_farmStateLabel">気温(℃)</td>
                                <td class="td_farmStateValue">'.$data['tmp'].'</td>
                            </tr>
                            <tr>
                                <td class="td_farmStateLabel">気圧(hPa)</td>
                                <td class="td_farmStateValue">'.$data['prss'].'</td>
                            </tr>
                            <tr>
                                <td class="td_farmStateLabel">湿度(%)</td>
                                <td class="td_farmStateValue">'.$data['rhum'].'</td>
                            </tr>
                            <tr>
                                <td class="td_farmStateLabel">日照(lux)</td>
                                <td class="td_farmStateValue">'.(round($data['sun'])).'</td>
                            </tr>
                        </table>
                      '.$message;
    echo $res;
    exit();
  } else {
    exit();
  }
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