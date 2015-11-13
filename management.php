<?php

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
$proc = escape($_POST['proc']);
$perUserId = escape($_REQUEST['perDiary']);
$date = escape($_GET['date']);
$month = escape($_GET['month']);
$year = escape($_GET['year']);

if (!$date) {
  $date = date(Y).'-'.date(m).'-'.date(d);
}

// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
if ($perUserId != '') {
  $accessCheck = checkAccessId($con, $userId, $perUserId);
  if ($accessCheck == '') {
    header('location:'.LOGOUT);
  } else {
    $sql = 'SELECT userName, userImage, userId
    FROM userMstTbl
    WHERE userId = ?';
    $stmt = $con->prepare($sql);
    $stmt->execute(array($perUserId));
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $userName = $data['userName'];
    $userImage = $data['userImage'];
    $otherId = $data['userId'];
  }
}

switch ($proc) {
  case 'harvestButton': if ($otherId){$userId = $otherId;} harvestAdd($con, $userId, $perUserId);exit();
  case 'costButton':  if ($otherId){$userId = $otherId;} costAdd($con, $userId, $perUserId);exit();
  case 'workButton':  workerAdd($con, $userId, $perUserId);exit();
  case 'crops':
  case 'eCrops[]': if ($otherId){$userId = $otherId;} if ($_POST['param']) {brandAutocompleat($con, $userId, escape($_POST['param']));}exit();
  case 'destination':
  case 'eDestination[]': if ($otherId){$userId = $otherId;} if ($_POST['param']) {desAutocompleat($con, $userId, escape($_POST['param']));}exit();
  case 'workerContents':
  case 'eWorkerContents[]': if ($otherId){$userId = $otherId;} if ($_POST['param']) {workConAutocompleat($con, $userId, escape($_POST['param']));}exit();
  case 'getHarvestCSV': $table = totalHarvestList($con, $userId, $date);totalHarvestCSV($table);exit();
  case 'getCostCSV': $table = totalCostList($con, $userId, $date);totalCostCSV($table);exit();
  case 'getWorkCSV': $table = totalWorkList($con, $userId, $date);totalWorkCSV($table);exit();
}

// メッセージの受け取り
require_once('./include/inFlg.php');

// 登録ワーク一覧の取得
if ($perUserId == '') {
  $har = harvestList($con, $userId, $date);
  $table = totalHarvestList($con, $userId, $date);
  $costTotal = totalCostList($con, $userId, $date);
  $cost = costList($con, $userId, $date);
  $work = workerList($con, $userId, $date);
  $workTotal = totalWorkList($con, $userId, $date);
  $list = accessList($con, $userId);
  $calendar = farmCalendar($userId, $con, 'management.php', $date, $year, $month, $perUserId);
} else {
  $style = 'display:none;';
  $har = harvestList($con, $perUserId, $date);
  $table = totalHarvestList($con, $perUserId, $date);
  $costTotal = totalCostList($con, $perUserId, $date);
  $cost = costList($con, $perUserId, $date);
  $work = workerList($con, $perUserId, $date);
  $workTotal = totalWorkList($con, $perUserId, $date);
  $calendar = farmCalendar($perUserId, $con, 'management.php', $date, $year, $month, $perUserId);
}
$per = permissionDiaryList($con, $userId);
$perHtml = permissionDiaryHtml($con, $userId, $per, $perUserId);
$harHtml = harvestHtml($con, $har, $userId, $perUserId);
$tableHtml = totalHarvestHtml($con, $table, $date);
$costHtml = costHtml($con, $cost, $userId, $perUserId);
$totalCostHtml = totalCostHtml($con, $costTotal, $date);
$workHtml = workerHtml($con, $work, $userId, $perUserId);
$totalWorkHtml = totalWorkHtml($con, $workTotal, $date);
$listHtml = accessHtml($con, $userId, $list);
// 曜日を返す
$week = getWeek($date);

$smarty->assign('html', $html);
$smarty->assign('style', $style);
$smarty->assign('commHtml', $commHtml);
$smarty->assign('harvestHtml', $harHtml);
$smarty->assign('harvestTotal', $tableHtml);
$smarty->assign('costHtml', $costHtml);
$smarty->assign('costTotal', $totalCostHtml);
$smarty->assign('workHtml', $workHtml);
$smarty->assign('workTotal', $totalWorkHtml);
$smarty->assign('accessHtml', $listHtml['member']);
$smarty->assign('friendOption', $listHtml['option']);
$smarty->assign('pDiaryOption', $perHtml);
$smarty->assign('calendar', $calendar);
$smarty->assign('userImage', $userImage);
$smarty->assign('userName', $userName);
$smarty->assign('week', $week);
$smarty->assign('cDate', changeDate($date));
$smarty->assign('date', $date);
$smarty->display('management.tpl');
exit();

function brandAutocompleat($con, $userId, $str) {
  // 品名セレクトボックス内のオプションタグを生成する
  $sql = 'SELECT brand
  FROM (SELECT * FROM farmHarvestTbl ORDER BY harvestDate DESC) AS a
  INNER JOIN farmDiaryMstTbl
  ON a.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  WHERE regUserId = ? AND (brand LIKE ?"%" OR brand LIKE ?"%")
  GROUP BY brand  ORDER BY harvestDate DESC LIMIT 0, 5
  ';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, mb_convert_kana($str, 'c'), mb_convert_kana($str, 'C')));
  $option = array();
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $option[] = $data['brand'];
  }
  echo json_encode($option);
  exit();
}

function desAutocompleat($con, $userId, $str) {
  // 品名セレクトボックス内のオプションタグを生成する
  $sql = 'SELECT destination
  FROM (SELECT * FROM farmHarvestTbl ORDER BY harvestDate DESC) AS a
  INNER JOIN farmDiaryMstTbl
  ON a.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  WHERE regUserId = ? AND (destination LIKE ?"%" OR destination LIKE ?"%")
  GROUP BY destination ORDER BY harvestDate DESC LIMIT 0, 5
  ';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, mb_convert_kana($str, 'c'), mb_convert_kana($str, 'C')));
  $option = array();
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $option[] = $data['destination'];
  }
  echo json_encode($option);
  exit();
}

function workConAutocompleat($con, $userId, $str) {
  // 品名セレクトボックス内のオプションタグを生成する
  $sql = 'SELECT contents
  FROM (SELECT * FROM hoursWorkedMstTbl ORDER BY workedMstId DESC) AS a
  INNER JOIN farmDiaryMstTbl
  ON a.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  WHERE regUserId = ? AND (contents LIKE ?"%" OR contents LIKE ?"%")
  GROUP BY contents ORDER BY workedMstId DESC LIMIT 0, 5
  ';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, mb_convert_kana($str, 'c'), mb_convert_kana($str, 'C')));
  $option = array();
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $option[] = $data['contents'];
  }
  echo json_encode($option);
  exit();
}

function harvestHtml($con, $har, $userId, $perUserId) {
  $count = count($har);
  if ($perUserId != '') {
      $html = '
      <tr>
      <th class="th_farmDiary"> 品種 </th>
      <th class="th_farmDiary"> 数量(/kg) </th>
      <th class="th_farmDiary"> 単価(/kg)</th>
      <th class="th_farmDiary"> 出荷先 </th>
      </tr>
      ';
  } else {
    $html = '
    <tr>
    <th class="th_farmDiary"> 品種 </th>
    <th class="th_farmDiary"> 数量(/kg) </th>
    <th class="th_farmDiary"> 単価(/kg)</th>
    <th class="th_farmDiary"> 出荷先 </th>
    </tr>
    ';
  }

  $i = 1;
  $loopFlg = false;
  while ($count >= $i) {
    $loopFlg = true;
    $html .= '
    <tr>
    <td class="td_farmDiaryValue center">
    <input type="text" name="eCrops[]" id="eCrops'.$i.'" value="'.$har[$i]['brand'].'" style="width: 200px;" class="auto">
    </td>
    <td class="td_farmDiaryValue center">
    <input type="text" name="eWeight[]" id="eWeight'.$i.'" value="'.$har[$i]['weight'].'" style="width: 50px;" class="auto">
    </td>
    <td class="td_farmDiaryValue center">
    <input type="text" name="ePrice[]" id="ePrice'.$i.'" value="'.number_format($har[$i]['price']).'" style="width: 50px;" class="auto">
    </td>
    <td class="td_farmDiaryValue center">
    <input type="text" name="eDestination[]" id="eDestination'.$i.'" value="'.$har[$i]['destination'].'" style="width: 200px;" class="auto">
    <input type="hidden" name="harvestMstId[]" value="'.$har[$i]['harvestId'].'">
    </td>
    </tr>
    ';
    $i++;
  }
  //   if ($loopFlg) {
  //     if ($perUserId == '') {
  $html .= '
  <tr>
  <td class="center border_input" >
  <input type="text" name="crops" style="width: 200px;" id="crops" maxlength="20" class="auto">
  </td>
  <td class="center border_input" >
  <input type="text" name="weight" style="width: 50px;" maxlength="5" class="auto">
  </td>
  <td class="center border_input" >
  <input type="text" name="price" style="width: 50px;" maxlength="5" class="auto">
  </td>
  <td class="center border_input" >
  <input type="text" name="destination" style="width: 200px;" id="destination" maxlength="20" class="auto">
  </td>
  </tr>
  ';
  //     }

  //   }

  return $html;

}

function harvestList($con, $userId, $date) {
  $sql = 'SELECT harvestMstId AS harvestId, brand, price, weight, destination, harvestDate
  FROM farmHarvestTbl
  INNER JOIN farmDiaryMstTbl
  ON farmHarvestTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  WHERE regUserId = ? AND harvestDate = ? ';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, $date));
  $i = 1;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $har[$i]['harvestId'] = $data['harvestId'];
    $har[$i]['brand'] = $data['brand'];
    $har[$i]['price'] = $data['price'];
    $har[$i]['weight'] = $data['weight'];
    $har[$i]['destination'] = $data['destination'];
    $har[$i]['harvestDate'] = $data['harvestDate'];
    $i++;
  }
  return $har;
}

function totalHarvestList($con, $userId, $date) {
  // 年月日を年月にして取得
  $ym = substr($date, 0, 7);
  $sql = 'SELECT brand, price, weight, destination, harvestDate
  FROM farmHarvestTbl
  INNER JOIN farmDiaryMstTbl
  ON farmHarvestTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  WHERE regUserId = ? AND harvestDate BETWEEN ? AND ?
  ORDER BY harvestDate ASC';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, $ym.'-01', $ym.'-31'));
  $i = 1;
  $total = 0;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $table[$i]['brand'] = $data['brand'];
    $table[$i]['price'] = $data['price'];
    $table[$i]['weight'] = $data['weight'];
    $table[$i]['pt'] = $data['price'] * $data['weight'];
    $table[$i]['destination'] = $data['destination'];
    $table[$i]['harvestDate'] = $data['harvestDate'];
    $total += $table[$i]['pt'];
    $i++;
  }
  $i -= 1;
  $table[$i]['total'] = $total;
  return $table;
}

function totalHarvestHtml($con, $table, $date) {
  $count = count($table);
  $year = substr($date, 0, 4);
  $month = substr($date, 5, 2);
  $i = 1;
  $html = '
  <tr>
  <th colspan="7" class="th_farmDiary">'.$year.'年'.$month.'月 集計</th>
  </tr>
  <tr>
  <td class="th_farmDiary center">No</td>
  <td class="th_farmDiary center">品名</td>
  <td class="th_farmDiary center">単価</td>
  <td class="th_farmDiary center">キロ数</td>
  <td class="th_farmDiary center">計</td>
  <td class="th_farmDiary center">出荷先</td>
  <td class="th_farmDiary center">日付</td>
  </tr>
  ';
  while ($count >= $i) {
    $html .= '
    <tr>
      <td class="td_farmDiaryValue center">'.$i.'</td>
      <td class="td_farmDiaryValue center">'.$table[$i]['brand'].'</td>
      <td class="td_farmDiaryValue right">'.number_format($table[$i]['price']).'</td>
      <td class="td_farmDiaryValue right">'.$table[$i]['weight'].'</td>
      <td class="td_farmDiaryValue right">'.number_format($table[$i]['pt']).'</td>
      <td class="td_farmDiaryValue center">'.$table[$i]['destination'].'</td>
      <td class="td_farmDiaryValue center">'.$table[$i]['harvestDate'].'</td>
    </tr>
    ';
    $i++;
  }
  $i -= 1;
  $html .= '
  <tr>
    <td colspan="4" class="th_farmDiary right">合計</td>
    <td class="td_farmDiaryValue right">'.number_format($table[$i]['total']).'</td>
  </tr>
  <tr>
    <td colspan="7" class="right">
      <form action="management.php?date='.$date.'" method="post">
        <input type="submit" value="CSV形式で保存" class="">
        <input type="hidden" name="proc" value="getHarvestCSV">
      </form>
    </td>
  </tr>';
  return $html;
}

function totalHarvestCSV($table) {
  $count = count($table);
  // CSVファイルの生成
  $csv = "No,品名,単価,キロ数,計,出荷先,日付\n";
  $i = 1;
  while ($count >= $i) {
    $csv .= $i.",".$table[$i]['brand'].",".number_format($table[$i]['price']).",".$table[$i]['weight'].",".$table[$i]['pt'].",".$table[$i]['destination'].",".$table[$i]['harvestDate']."\n";
    $i++;
  }
  $csv .= " , , ,合計,".number_format($table[$i - 1]['total']);
  // 出力ファイル名の生成
  $csvFile = "csv_".date(Ymd).".csv";
  // 文字コードをUTF-8に
  $csv = mb_convert_encoding($csv, "sjis-win" , 'utf-8');

  // MIMEタイプの設定
  header("Content-Type: application/octet-stream");
  // 保存ダイアログ表示時の初期ファイル名
  header("Content-Disposition: attachment; filename={$csvFile}");
  echo($csv);
  exit();
}

/**
 * 入力された収穫データをDBに格納する
 * @param db $con
 * @param int $userId
 */
function harvestAdd($con, $userId, $perUserId) {
  $date = escape($_POST['date']);
  // 新規で登録する値
  $diaryMstId = checkMstId($con, $userId);
  $crops = trim(mb_convert_kana(escape($_POST['crops']), 's'));
  $weight = mb_convert_kana(escape($_POST['weight']), "a", "UTF-8");
  $price = mb_convert_kana(escape($_POST['price']), "a", "UTF-8");
  $destination = trim(mb_convert_kana(escape($_POST['destination']), 's'));
  // 編集状態にある値の取得
  $count = count($_POST['eCrops']);
  try {
    $con->beginTransaction();
    $flg = true;
    $flg2 = true;
    if ($crops != '' && is_numeric($weight) && is_numeric($price) && $destination != '') {
      $sql = 'INSERT INTO farmHarvestTbl(farmDiaryId, brand, price, weight, destination, harvestDate)
      VALUES(?, ?, ?, ?, ?, ?)';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($diaryMstId, $crops, $price, $weight, $destination, $date));
      $stmt->closeCursor();
    }
    $i = 0;
    $sql = 'UPDATE farmHarvestTbl SET brand=?, price=?, weight=?,
    destination=?, harvestDate=?
    WHERE harvestMstId=?';
    while ($count > $i) {
      $eCrops[$i] = trim(mb_convert_kana(escape($_POST['eCrops'][$i]), 's'));
      $eWeight[$i] = mb_convert_kana(escape($_POST['eWeight'][$i]), "a", "UTF-8");
      $ePrice[$i] = mb_convert_kana(escape($_POST['ePrice'][$i]), "a", "UTF-8");
      $eDestination[$i] = trim(mb_convert_kana(escape($_POST['eDestination'][$i]), 's'));
      $harvestMstId[$i] = escape($_POST['harvestMstId'][$i]);
      if ($eCrops[$i] !='' && is_numeric($eWeight[$i]) && is_numeric($ePrice[$i]) &&
          $eDestination[$i]) {
        $stmt = $con->prepare($sql);
        $flg2 = $stmt->execute(array($eCrops[$i], $ePrice[$i], $eWeight[$i], $eDestination[$i], $date, $harvestMstId[$i]));
      }
      $i++;
    }
    if ($flg && $flg2) {
      $con->commit();
      $ret['flg'] = 'OK';
      $har = harvestList($con, $userId, $date);
      $ret['html'] = harvestHtml($con, $har, $userId, $perUserId);
      $table = totalHarvestList($con, $userId, $date);
      $ret['total'] = totalHarvestHtml($con, $table, $date);
      $ret['proc'] = 'harvest';
    } else {
      $con->rollback();
      $ret['flg'] = 'NG';
    }
  } catch (Exception $e) {
    $con->rollback();
    $ret['flg'] = 'NG2';
  }
  echo json_encode($ret);
  exit();
}

/**
 * 入力された経費データをDBに格納する
 * @param db $con
 * @param int $userId
 */
function costAdd($con, $userId, $perUserId) {
  $date = escape($_POST['date']);
  // 新規で登録する値
  $diaryMstId = checkMstId($con, $userId);
  $cost = mb_convert_kana(escape($_POST['cost']), 's');
  $contents = mb_convert_kana(escape($_POST['contents']), 's');
  $expense = mb_convert_kana(escape($_POST['expense']), "a", "UTF-8");
  $pay = mb_convert_kana(escape($_POST['pay']), 's');
  $note = mb_convert_kana(escape($_POST['note']), 's');
  // 編集状態にある総数の取得
  $count = count($_POST['eCost']);
  try {
    $con->beginTransaction();
    $flg = true;
    $flg2 = true;
    if ($cost != '' && $contents != '' && is_numeric($expense) && $pay != '') {
      $sql = 'INSERT INTO costMstTbl(farmDiaryId, cost, contents, expense, pay, note, regDate)
      VALUES(?, ?, ?, ?, ?, ?, ?)';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($diaryMstId, $cost, $contents, $expense, $pay, $note, $date));
      $stmt->closeCursor();
    } //else {
//       $ret['flg'] = 'NG';
//       $ret['message'] = '入力値に誤りがあります。';
//       echo json_encode($ret);
//       exit();
//     }
    $i = 0;
    $sql = 'UPDATE costMstTbl SET cost=?, contents=?, expense=?,
    pay=?, note=?
    WHERE costMstId=?';
    $stmt = $con->prepare($sql);
    while ($count > $i) {
      $eCost = mb_convert_kana(escape($_POST['eCost'][$i]), 's');
      $eContents = mb_convert_kana(escape($_POST['eContents'][$i]), 's');
      $eExpense = mb_convert_kana(str_replace(',','',escape($_POST['eExpense'][$i])), "a", "UTF-8");
      $ePay = mb_convert_kana(escape($_POST['ePay'][$i]), 's');
      $eNote = mb_convert_kana(escape($_POST['eNote'][$i]), 's');
      $costMstId = escape($_POST['costMstId'][$i]);
      if ($eCost !='' && is_numeric($eExpense) && $ePay != '' &&
          $eContents != '') {
        $flg2 = $stmt->execute(array($eCost, $eContents, $eExpense, $ePay, $eNote, $costMstId));
      }
      $i++;
    }
    if ($flg && $flg2) {
      $con->commit();
      $ret['flg'] = 'OK';
      $costList = costList($con, $userId, $date);
      $ret['html'] = costHtml($con, $costList, $userId, $perUserId);
      $costTotal = totalCostList($con, $userId, $date);
      $ret['total'] = totalCostHtml($con, $costTotal, $date);
      $ret['proc'] = 'cost';
    } else {
      $con->rollback();
      $ret['flg'] = 'NG';
      $ret['message'] = 'サーバーエラーが生じています。しばらくお待ちください。';
    }
  } catch (Exception $e) {
    $con->rollback();
    $ret['flg'] = 'NG2';
    $ret['message'] = 'サーバーエラーが生じています。しばらくお待ちください。';
  }
  echo json_encode($ret);
  exit();
}

function costList($con, $userId, $date) {
  $sql = 'SELECT costMstId, cost, contents, expense, pay, note, regDate
  FROM costMstTbl
  INNER JOIN farmDiaryMstTbl
  ON costMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  WHERE regUserId = ? AND regDate = ? ';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, $date));
  $costList = array();
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $costList[] = $data;
  }
  return $costList;
}

function costHtml($con, $costList, $userId, $perUserId) {
  $html = '
                  <tr>
                    <th class="th_farmDiary"> 経費 </th>
                    <th class="th_farmDiary"> 内容 </th>
                    <th class="th_farmDiary"> 金額 </th>
                    <th class="th_farmDiary"> 支払先 </th>
                    <th class="th_farmDiary"> 備考</th>
                  </tr>
  ';

  $loopFlg = false;
  foreach ($costList as $var) {
    $loopFlg = true;
    switch ($var['cost']) {
      case "農具備品":$str = 'nougu'; break;
      case "肥料費":$str = 'hiryou'; break;
      case "農薬費":$str = 'nouyaku'; break;
      case "燃料費":$str = 'nennryou'; break;
      case "光熱費":$str = 'kounetsu'; break;
      case "種苗費":$str = 'tanenae'; break;
      case "その他":$str = 'sonota'; break;
    }
    // 可変変数
    $$str = 'selected="selected"';
    $html .= '
                  <tr>
                    <td class="td_farmDiaryValue center">
                      <select name="eCost[]" class="select">
                        <option value="農具備品" '.$nougu.'>農具備品</option>
                        <option value="肥料費" '.$hiryou.'>肥料費</option>
                        <option value="農薬費" '.$nouyaku.'>農薬費</option>
                        <option value="燃料費" '.$nennryou.'>燃料費</option>
                        <option value="光熱費" '.$kounetsu.'>光熱費</option>
                        <option value="種苗費" '.$tanenae.'>種苗費</option>
                        <option value="その他" '.$sonota.'>その他</option>
                      </select>
                    </td>
                    <td class="td_farmDiaryValue center">
                      <input type="text" name="eContents[]" value="'.$var['contents'].'" style="width: 100px;" class="">
                    </td>
                    <td class="td_farmDiaryValue center">
                      <input type="text" name="eExpense[]" value="'.number_format($var['expense']).'" style="width: 50px;" class="">
                    </td>
                    <td class="td_farmDiaryValue center">
                      <input type="text" name="ePay[]" value="'.$var['pay'].'" style="width: 150px;" class="">
                    </td>
                    <td class="td_farmDiaryValue center">
                      <input type="text" name="eNote[]" value="'.$var['note'].'" style="width: 200px;" class="">
                      <input type="hidden" name="costMstId[]" value="'.$var['costMstId'].'">
                    </td>
                  </tr>
    ';
  }
  //   if ($loopFlg) {
  //     if ($perUserId == '') {
  $html .= '
                  <tr>
                    <td class="center border_input" >
                      <select name="cost" id="cost" class="select">
                        <option value="農具備品">農具備品</option>
                        <option value="肥料費">肥料費</option>
                        <option value="農薬費">農薬費</option>
                        <option value="燃料費">燃料費</option>
                        <option value="光熱費">光熱費</option>
                        <option value="種苗費">種苗費</option>
                        <option value="その他">その他</option>
                      </select>
                    </td>
                    <td class="center border_input" >
                      <input type="text" name="contents" id="contents" style="width: 100px;" maxlength="126">
                    </td>
                    <td class="center border_input" >
                      <input type="text" name="expense" id="expense" style="width: 50px;" maxlength="5">
                    </td>

                    <td class="center border_input" >
                      <input type="text" name="pay" id="pay" style="width: 150px;" maxlength="20">
                    </td>

                    <td class="center border_input" >
                      <input type="text" name="note" style="width: 200px;" id="note" maxlength="126">
                    </td>
                  </tr>
  ';
  //     }

  //   }

  return $html;

}

function totalCostList($con, $userId, $date) {
  // 年月日を年月にして取得
  $ym = substr($date, 0, 7);
  $sql = 'SELECT cost, contents, expense, pay, note, regDate
  FROM costMstTbl
  INNER JOIN farmDiaryMstTbl
  ON costMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  WHERE regUserId = ? AND regDate BETWEEN ? AND ?
  ORDER BY regDate ASC';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, $ym.'-01', $ym.'-31'));
  $table = array();
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $table[] = $data;
  }
  return $table;
}

function totalCostHtml($con, $table, $date) {
  $count = count($table);
  $year = substr($date, 0, 4);
  $month = substr($date, 5, 2);
  $i = 0;
  $total = 0;
  $html = '
  <tr>
    <th colspan="7" class="th_farmDiary">'.$year.'年'.$month.'月 集計</th>
  </tr>
  <tr>
    <td class="th_farmDiary center">No</td>
    <td class="th_farmDiary center">経費</td>
    <td class="th_farmDiary center">内容</td>
    <td class="th_farmDiary center">金額</td>
    <td class="th_farmDiary center">支払先</td>
    <td class="th_farmDiary center">備考</td>
    <td class="th_farmDiary center">日付</td>
  </tr>
  ';
  while ($count > $i) {
    $html .= '
    <tr>
      <td class="td_farmDiaryValue center">'.($i+1).'</td>
      <td class="td_farmDiaryValue center">'.$table[$i]['cost'].'</td>
      <td class="td_farmDiaryValue right">'.$table[$i]['contents'].'</td>
      <td class="td_farmDiaryValue right">'.number_format($table[$i]['expense']).'</td>
      <td class="td_farmDiaryValue right">'.$table[$i]['pay'].'</td>
      <td class="td_farmDiaryValue center">'.$table[$i]['note'].'</td>
      <td class="td_farmDiaryValue center">'.$table[$i]['regDate'].'</td>
    </tr>
    ';
    $total += $table[$i]['expense'];
    $i++;
  }
  $html .= '
  <tr>
    <td colspan="3" class="th_farmDiary right">合計</td>
    <td class="td_farmDiaryValue right">'.number_format($total).'</td>
  </tr>
  <tr>
    <td colspan="7" class="right">
      <form action="management.php?date='.$date.'" method="post">
        <input type="submit" value="CSV形式で保存" class="">
        <input type="hidden" name="proc" value="getCostCSV">
      </form>
    </td>
  </tr>';
  return $html;
}

function totalCostCSV($table) {
  $count = count($table);
  // CSVファイルの生成
  $csv = "No,経費,内容,金額,支払先,備考,日付\n";
  $i = 0;
  $total = 0;
  while ($count > $i) {
    $csv .= ($i+1).",".$table[$i]['cost'].",".$table[$i]['contents'].",".number_format($table[$i]['expense']).",".$table[$i]['pay'].",".$table[$i]['note'].",".$table[$i]['regDate']."\n";
    $total += $table[$i]['expense'];
    $i++;
  }
  $csv .= " , ,合計,".number_format($total);
  // 出力ファイル名の生成
  $csvFile = "csv_COST_".date(Ymd).".csv";
  // 文字コードをUTF-8に
  $csv = mb_convert_encoding($csv, "sjis-win" , 'utf-8');

  // MIMEタイプの設定
  header("Content-Type: application/octet-stream");
  // 保存ダイアログ表示時の初期ファイル名
  header("Content-Disposition: attachment; filename={$csvFile}");
  echo($csv);
  exit();
}


/**
 * 入力された作業時間データをDBに格納する
 * @param db $con
 * @param int $userId
 */
function workerAdd($con, $userId, $perUserId) {
  $date = escape($_POST['date']);
  // 新規で登録する値
  if ($perUserId != '') {
    $diaryMstId = checkMstId($con, $perUserId);
  } else {
    $diaryMstId = checkMstId($con, $userId);
  }

  $workerContents = trim(mb_convert_kana(escape($_POST['workerContents']), "s"));
  $workMemo = trim(mb_convert_kana(escape($_POST['memo']), "s"));
  $worker = trim(escape($_POST['worker']));
  $workTime = trim(mb_convert_kana(escape($_POST['workTime']), 'a'));
  // 編集状態にある総数の取得
  $count = count($_POST['eWorkMstId']);
  try {
    $con->beginTransaction();
    $flg = true;
    $flg2 = true;
    if ($workerContents != '' && is_numeric($workTime)) {
      $sql = 'INSERT INTO hoursWorkedMstTbl(farmDiaryId, contents, memo, userId, hoursWorked, workedDate)
       VALUES (?, ?, ?, ?, ?, ?)
    ';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($diaryMstId, $workerContents, $workMemo, $userId, $workTime, $date));

      $stmt->closeCursor();
    } //else {
//       $ret['flg'] = 'NG';
//       $ret['message'] = '入力値に誤りがあります。';
//       echo json_encode($ret);
//       exit();
//     }
    $i = 0;
    $sql = 'UPDATE hoursWorkedMstTbl SET contents=?, memo=?, hoursWorked=?
    WHERE workedMstId=?';
    while ($count > $i) {
      $eContents[$i] = trim(mb_convert_kana(escape($_POST['eWorkerContents'][$i]), 's'));
      $eMemo[$i] = trim(mb_convert_kana(escape($_POST['eWorkMemo'][$i]), 's'));
      $eWorkTime[$i] = trim(mb_convert_kana(escape($_POST['eWorkTime'][$i]), "a", "UTF-8"));
      $eWorkMstId[$i] = escape($_POST['eWorkMstId'][$i]);
      if ($eContents[$i] !='' && is_numeric($eWorkTime[$i])) {
        $stmt = $con->prepare($sql);
        $flg2 = $stmt->execute(array($eContents[$i], $eMemo[$i], $eWorkTime[$i], $eWorkMstId[$i]));
      }
      $i++;
    }
    if ($flg && $flg2) {
      $con->commit();
      if ($perUserId != '') {
        $userId = $perUserId;
      }
      $ret['flg'] = 'OK';
      $workerList = workerList($con, $userId, $date);
      $ret['html'] = workerHtml($con, $workerList, $userId, $perUserId);
      $workTotal = totalWorkList($con, $userId, $date);
      $ret['total'] = totalWorkHtml($con, $workTotal, $date);
      $ret['proc'] = 'work';
    } else {
      $con->rollback();
      $ret['flg'] = 'NG';
      $ret['message'] = 'サーバーエラーが生じています。しばらくお待ちください。';
    }
  } catch (Exception $e) {
    $con->rollback();
    $ret['flg'] = 'NG2';
    $ret['message'] = 'サーバーエラーが生じています。しばらくお待ちください。';
  }
  echo json_encode($ret);
  exit();
}

function workerList($con, $userId, $date) {
  $sql = 'SELECT workedMstId, contents, memo, userName, hoursWorked AS workTime
  FROM hoursWorkedMstTbl
  INNER JOIN farmDiaryMstTbl
  ON hoursWorkedMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  INNER JOIN userMstTbl
  ON hoursWorkedMstTbl.userId = userMstTbl.userId
  WHERE regUserId = ? AND workedDate = ? ';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, $date));
  $workList = array();
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $workList[] = $data;
  }
  return $workList;
}

function workerHtml($con, $workList, $userId, $perUserId) {
  $html = '
                  <tr>
                    <th colspan="4" class="th_farmDiary"> 作業入力 </th>
                  </tr>
                  <tr>
                    <th class="th_farmDiary"> 作業名 </th>
                    <th colspan="2" class="th_farmDiary"> メモ </th>
                    <th class="th_farmDiary"> 時間(/h) </th>
                  </tr>
  ';

  $loopFlg = false;
  foreach ($workList as $var) {
    $loopFlg = true;

    $html .= '
                  <tr>
                    <td class="td_farmDiaryValue center">
                      <input type="text" name="eWorkerContents[]" value="'.$var['contents'].'" style="width: 150px;" class="auto">
                    </td>
                    <td class="td_farmDiaryValue center">
                      <input type="text" name="eWorkMemo[]" value="'.$var['memo'].'" style="width: 300px;" class="">
                    </td>
                    <td style="width:100px;" class="td_farmDiaryValue center">
                      <span>'.$var['userName'].'</span>
                    </td>
                    <td class="td_farmDiaryValue center">
                      <input type="text" name="eWorkTime[]" value="'.$var['workTime'].'" style="width: 50px;" class="">
                      <input type="hidden" name="eWorkMstId[]" value="'.$var['workedMstId'].'">
                    </td>
                  </tr>
    ';
  }
  //   if ($loopFlg) {
  //     if ($perUserId == '') {
  $html .= '
                  <tr>
                    <td class="center border_input" >
                      <input type="text" name="workerContents" id="workerContents" style="width: 150px;" class="auto">
                    </td>
                    <td colspan="2" class="center border_input" >
                      <input type="text" name="memo" id="memo" style="width: 410px;" class="">
                    </td>
                    <td class="center border_input" >
                      <input type="text" name="workTime" id="workTime" style="width: 50px;" maxlength="5" class="">
                    </td>
                  <tr>
  ';
  //     }

  //   }

  return $html;
}

function totalWorkList($con, $userId, $date) {
  // 年月日を年月にして取得
  $ym = substr($date, 0, 7);
  $sql = 'SELECT contents, userName, hoursWorked AS workTime, workedDate
  FROM hoursWorkedMstTbl
  INNER JOIN farmDiaryMstTbl
  ON hoursWorkedMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  INNER JOIN userMstTbl
  ON hoursWorkedMstTbl.userId = userMstTbl.userId
  WHERE regUserId = ? AND workedDate BETWEEN ? AND ?
  ORDER BY workedDate ASC';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, $ym.'-01', $ym.'-31'));
  $table = array();
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $table[] = $data;
  }
  return $table;
}

function totalWorkHtml($con, $table, $date) {
  $count = count($table);
  $year = substr($date, 0, 4);
  $month = substr($date, 5, 2);
  $i = 0;
  $total = 0;
  $html = '
  <tr>
  <th colspan="5" class="th_farmDiary">'.$year.'年'.$month.'月 集計</th>
  </tr>
  <tr>
  <td class="th_farmDiary center">No</td>
  <td class="th_farmDiary center">作業内容</td>
  <td class="th_farmDiary center">作業者</td>
  <td class="th_farmDiary center">作業時間</td>
  <td class="th_farmDiary center">作業日</td>
  </tr>
  ';
  while ($count > $i) {
    $html .= '
    <tr>
    <td class="td_farmDiaryValue center">'.($i+1).'</td>
    <td class="td_farmDiaryValue center">'.$table[$i]['contents'].'</td>
    <td class="td_farmDiaryValue right">'.$table[$i]['userName'].'</td>
    <td class="td_farmDiaryValue right">'.$table[$i]['workTime'].'/h</td>
    <td class="td_farmDiaryValue right">'.$table[$i]['workedDate'].'</td>
    </tr>
    ';
    $total += $table[$i]['workTime'];
    $i++;
  }
  $html .= '
  <tr>
    <td colspan="3" class="th_farmDiary right">合計</td>
    <td class="td_farmDiaryValue right">'.$total.'/h</td>
  </tr>
  <tr>
    <td colspan="5" class="right">
      <form action="management.php?date='.$date.'" method="post">
        <input type="submit" value="CSV形式で保存" class="">
        <input type="hidden" name="proc" value="getWorkCSV">
      </form>
    </td>
  </tr>';
  return $html;
}

function totalWorkCSV($table) {
  $count = count($table);
  // CSVファイルの生成
  $csv = "No,作業内容,作業者,作業時間,作業日\n";
  $i = 0;
  $total = 0;
  while ($count > $i) {
    $csv .= ($i+1).",".$table[$i]['contents'].",".$table[$i]['userName'].",".$table[$i]['workTime'].",".$table[$i]['workedDate'].",\n";
    $total += $table[$i]['workTime'];
    $i++;
  }
  $csv .= " , ,合計,".$total;
  // 出力ファイル名の生成
  $csvFile = "csv_WORK_".date(Ymd).".csv";
  // 文字コードをUTF-8に
  $csv = mb_convert_encoding($csv, "sjis-win" , 'utf-8');

  // MIMEタイプの設定
  header("Content-Type: application/octet-stream");
  // 保存ダイアログ表示時の初期ファイル名
  header("Content-Disposition: attachment; filename={$csvFile}");
  echo($csv);
  exit();
}