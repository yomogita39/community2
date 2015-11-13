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
$date = escape($_REQUEST['date']);
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
  case 'routineAdd':
  case 'behindAdd': if ($otherId){$userId = $otherId;} workAdd($con, $userId, $date, $proc, $perUserId); exit();
  case 'workPre': workPre($con, $userId, $perUserId); exit();
  case 'workDel': workDel($con, $userId, $perUserId); exit();
  case 'planAdd': if ($otherId){$userId = $otherId;} planAdd($con, $userId, $perUserId); exit();
  case 'farmMessageAdd': farmMessageAdd($con, $userId, $perUserId); exit();
  case 'harvestAdd': if ($otherId){$userId = $otherId;} harvestAdd($con, $userId, $perUserId);exit();
  case 'accessAdd': accessAdd($con, $userId);exit();
  case 'accessDel': accessDel($con, $userId);exit();
  case 'priority': savePriority($con, $userId, $perUserId); exit();
  case 'getCSV': $table = totalHarvestList($con, $userId, $date);totalHarvestCSV($table);exit();
}

// メッセージの受け取り
require_once('./include/inFlg.php');

// 登録ワーク一覧の取得
if ($perUserId == '') {
  $work = workList($con, $userId, $date);
  $comm = commMessageList($con, $userId, $date);
  $har = harvestList($con, $userId, $date);
  $table = totalHarvestList($con, $userId, $date);
  $list = accessList($con, $userId);
  $plan = getWorkPlan($con, $userId, $date);
  $calendar = farmCalendar($userId, $con, 'workEdit.php', $date, $year, $month, $perUserId);
} else {
  $style = 'display:none;';
  $work = workList($con, $perUserId, $date);
  $comm = commMessageList($con, $perUserId, $date);
  $har = harvestList($con, $perUserId, $date);
  $table = totalHarvestList($con, $perUserId, $date);
  $plan = getWorkPlan($con, $perUserId, $date);
  $calendar = farmCalendar($perUserId, $con, 'workEdit.php', $date, $year, $month, $perUserId);
}
$per = permissionDiaryList($con, $userId);
$html = workHtml($con, $work, $date, $style, $perUserId);
$commHtml = commMessageHtml($con, $comm);
$harHtml = harvestHtml($con, $har, $userId, $perUserId);
$tableHtml = totalHarvestHtml($con, $table, $date);
$listHtml = accessHtml($con, $userId, $list);
$perHtml = permissionDiaryHtml($con, $userId, $per, $perUserId);

// 曜日を返す
$week = getWeek($date);

$smarty->assign('html', $html);
$smarty->assign('style', $style);
$smarty->assign('commHtml', $commHtml);
$smarty->assign('harvestHtml', $harHtml);
$smarty->assign('tableHtml', $tableHtml);
$smarty->assign('accessHtml', $listHtml['member']);
$smarty->assign('friendOption', $listHtml['option']);
$smarty->assign('pDiaryOption', $perHtml);
$smarty->assign('plan', $plan['workPlan']);
$smarty->assign('performance', $plan['workPerformance']);
$smarty->assign('calendar', $calendar);
$smarty->assign('userImage', $userImage);
$smarty->assign('userName', $userName);
$smarty->assign('workS', $workS);
$smarty->assign('work', $work);
$smarty->assign('week', $week);
$smarty->assign('cDate', changeDate($date));
$smarty->assign('date', $date);
$smarty->display('editWorking.tpl');

function getWorkPlan($con, $userId, $date) {
  $sql = 'SELECT workPlan, workPerformance FROM farmWorkerMstTbl
  INNER JOIN farmDiaryMstTbl
  ON farmWorkerMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  INNER JOIN userMstTbl
  ON farmDiaryMstTbl.regUserId = userMstTbl.userId
  WHERE userMstTbl.userId=? AND regDate=?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, $date));
  $ret = array();
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $ret = $data;
  }
  return $ret;
}

function savePriority($con, $userId, $perUserId) {
  $i = 0;
  while (count($_POST['behindId']) > $i) {
    $behindId[$i] = escape($_POST['behindId'][$i]);
    $i++;
  }
  $i = 0;
  while (count($_POST['priority']) > $i) {
    $priority[$i] = escape($_POST['priority'][$i]);
    $i++;
  }
  $idC = count($behindId);
  $priorityC = count($priority);
  try {
    $con->beginTransaction();
    $flg = true;
    $flg2 = true;
    if ($idC > 0) {
      $sql = 'UPDATE farmBehindWorkTbl SET priority=?
              WHERE behindWorkId=?';
      $i = 0;
      while ($idC > $i) {
        if ($flg) {
          $stmt = $con->prepare($sql);
          $flg = $stmt->execute(array($priority[$i], $behindId[$i]));
          $stmt->closeCursor();
          $i++;
        } else {
          break;
        }
      }
    }
    if ($flg && $idC > 0) {
      $con->commit();
      $ret['flg'] = 'OK';
      $ret['perUserId'] = $perUserId;
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

function accessAdd($con, $userId) {
  $friendId = escape($_POST['friendSelect']);
  // 新規の登録か否か
  $diaryMstId = checkMstId($con, $userId);
  if (friendId != '') {
    try {
      $con->beginTransaction();
      $sql = 'INSERT INTO farmAccessMstTbl(farmDiaryId, permissionUserId)
              VALUES(?, ?)';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($diaryMstId, $friendId));
      if($flg) {
        $con->commit();
        $ret['flg'] ='OK';
        $list = accessList($con, $userId);
        $html = accessHtml($con, $userId, $list);
        $ret['html'] = $html['member'];
        $ret['option'] = $html['option'];
      } else {
        $con->rollback();
        $ret['flg'] ='NG';
      }
    } catch (Exception $e) {
      $con->rollback();
      $ret['flg'] ='NG2';
    }
  } else {
    $ret['flg'] ='NG3';
  }
  echo json_encode($ret);
  exit();
}

function accessDel($con, $userId) {
  // 削除するユーザIDを取得
  $i = 0;
  while(count($_POST['accessCheck']) > $i) {
    $delId[$i] = escape($_POST['accessCheck'][$i]);
    $i++;
  }
  if ($delId) {
    try {
      $con->beginTransaction();
      $sql = 'DELETE FROM farmAccessMstTbl
      WHERE accessMstId=?';
      $i = 0;
      $flg = false;
      while (count($delId) > $i) {
        $stmt = $con->prepare($sql);
        $flg = $stmt->execute(array($delId[$i]));
        $stmt->closeCursor();
        if(!$flg) {
          break;
        }
        $i++;
      }
      if ($flg) {
        $con->commit();
        $ret['flg'] ='OK';
        $list = accessList($con, $userId);
        $html = accessHtml($con, $userId, $list);
        $ret['html'] = $html['member'];
        $ret['option'] = $html['option'];
      } else {
        $con->rollback();
        $ret['flg'] ='NG';
      }
    } catch (Exception $e) {
      $con->rollback();
      $ret['flg'] ='NG2';
    }
  } else {
    $ret['flg'] ='NG3';
  }
  echo json_encode($ret);
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
  $crops = escape($_POST['crops']);
  $weight = mb_convert_kana(escape($_POST['weight']), "a", "UTF-8");
  $price = mb_convert_kana(escape($_POST['price']), "a", "UTF-8");
  $destination = escape($_POST['destination']);
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
      $eCrops[$i] = escape($_POST['eCrops'][$i]);
      $eWeight[$i] = mb_convert_kana(escape($_POST['eWeight'][$i]), "a", "UTF-8");
      $ePrice[$i] = mb_convert_kana(escape($_POST['ePrice'][$i]), "a", "UTF-8");
      $eDestination[$i] = escape($_POST['eDestination'][$i]);
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
      $ret['tableHtml'] = totalHarvestHtml($con, $table, $date);
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

function planAdd($con, $userId, $perUserId) {
  $date = escape($_POST['date']);
  // 新規で登録する値
  $diaryMstId = checkMstId($con, $userId);
  $plan = escape($_POST['plan']);
  $performance = escape($_POST['performance']);
  $sql = 'SELECT no FROM farmWorkerMstTbl
            INNER JOIN farmDiaryMstTbl
            ON farmWorkerMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
            INNER JOIN userMstTbl
            ON farmDiaryMstTbl.regUserId = userMstTbl.userId
            WHERE userMstTbl.userId=? AND regDate=?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, $date));
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  try {
    $con->beginTransaction();
    if ($data['no']) {
      $sql = 'UPDATE farmWorkerMstTbl SET workPlan=?, workPerformance=? WHERE no=?';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($plan, $performance, $data['no']));
    } else {
      $sql = 'INSERT INTO farmWorkerMstTbl(farmDiaryId, workPlan, workPerformance, regDate) VALUES(?, ?, ?, ?)';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($diaryMstId, $plan, $performance, $date));
    }
    if ($flg) {
      $con->commit();
      $ret['flg'] = 'OK';
      echo json_encode($ret);
      exit();
    } else {
      $con->rollback();
    }
  } catch (Exeption $e) {
    $con->rollback();
  }
  $ret['flg'] = 'NG';
  echo json_encode($ret);
  exit();
}

function workDel($con, $userId, $perUserId) {
  $work = checking();
  $routineW = $work['routineW'];
  $behindW = $work['behindW'];
  $routineC = count($routineW);
  $behindC = count($behindW);
  $date = escape($_POST['date']);
  try {
    $con->beginTransaction();
    $flg = true;
    $flg2 = true;
    if ($routineC > 0) {
      $sql = 'UPDATE farmRoutineMstTbl SET delDate = ? WHERE routineWorkId = ?';
      $i = 0;
      while ($routineC > $i) {
        if ($flg) {
          $stmt = $con->prepare($sql);
          $flg = $stmt->execute(array($date.' 12:00:00', $routineW[$i]));
          $stmt->closeCursor();
          $i++;
        } else {
          break;
        }
      }
    }
    if ($flg) {
      if ($behindC > 0) {
        $sql = 'UPDATE farmBehindWorkTbl SET delDate=?
        WHERE behindWorkId = ?';
        $i = 0;
        while ($behindC > $i) {
          if ($flg2) {
            $sql2 = 'UPDATE  farmBehindWorkTbl
                     INNER JOIN farmDiaryMstTbl
                     ON farmBehindWorkTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
                     SET priority = priority-1
                     WHERE (SELECT priority FROM (SELECT priority FROM farmBehindWorkTbl WHERE behindWorkId = ?) AS temp ) < priority
                       AND regUserId = ?';
            $stmt = $con->prepare($sql2);
            $flg2 = $stmt->execute(array($behindW[$i], $userId));
            $stmt->closeCursor();
            if ($flg2) {
              $stmt = $con->prepare($sql);
              $flg2 = $stmt->execute(array($date.' 12:00:00', $behindW[$i]));
              $stmt->closeCursor();
            } else {
              break;
            }
            $i++;
          } else {
            break;
          }
        }
      }
    }
    if (($flg && $flg2 && $routineC > 0) || ($flg && $flg2 && $behindC > 0)) {
      $con->commit();
      $ret['flg'] = 'OK';
    } else {
      $con->rollback();
      $ret['flg'] = 'NG';
    }
  } catch (Exception $e) {
    $con->rollback();
  }
  $ret['perUserId'] = $perUserId;
  echo json_encode($ret);
  exit();
}
function checking() {
  $i = 0;
  while (count($_POST['routineCheck']) > $i) {
    $work['routineW'][$i] = escape($_POST['routineCheck'][$i]);
    $i++;
  }
  $i = 0;
  while (count($_POST['behindCheck']) > $i) {
    $work['behindW'][$i] = escape($_POST['behindCheck'][$i]);
    $i++;
  }
  return $work;
}
function workPre($con, $userId, $perUserId) {
  $work = checking();
  $routineW = $work['routineW'];
  $behindW = $work['behindW'];
  $date = escape($_POST['date']);
  $routineC = count($routineW);
  $behindC = count($behindW);

  try {
    $con->beginTransaction();
    $flg = true;
    $flg2 = true;
    if ($routineC > 0) {
      $sql = 'INSERT INTO farmRoutineWorkTbl(routineWorkId, workerId, workCompFlg, workCompDate, workCompRealDate)
              VALUES (?, ?, ?, ?, ?)';
      $i = 0;
      while ($routineC > $i) {
        if ($flg) {
          $stmt = $con->prepare($sql);
          $flg = $stmt->execute(array($routineW[$i], $userId, 1, $date.' '.date(H).'-'.date(i).'-'.date(s), date(YmdHis)));
          $stmt->closeCursor();
          $i++;
        } else {
          break;
        }
      }
    }
    if ($flg) {
      if ($behindC > 0) {
        $sql = 'UPDATE farmBehindWorkTbl SET workerId = ?, workCompFlg = 1, workCompDate=?, priority=0
                WHERE behindWorkId = ?';
        $i = 0;
        while ($behindC > $i) {
          if ($flg2) {
            $stmt = $con->prepare($sql);
            $flg2 = $stmt->execute(array($userId, date(YmdHis), $behindW[$i]));
            $stmt->closeCursor();
            $i++;
          } else {
            break;
          }
        }
      }
    }
    if (($flg && $flg2 && $routineC > 0) || ($flg && $flg2 && $behindC > 0)) {
      $con->commit();
      $ret['flg'] = 'OK';
      $ret['perUserId'] = $perUserId;
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

function workAdd($con, $userId, $date, $proc, $perUserId) {
  $diaryMstId = checkMstId($con, $userId);
  // 入力された値の取得
  $addWork = escape($_POST['add']);
  if ($addWork == '') {
    exit();
  }
  $res = workInsert($con, $userId, $addWork, $diaryMstId, $proc);
  if ($res) {
    // 登録ワーク一覧の取得
    $work = workList($con, $userId, $date);
    $html = workHtml($con, $work, $date, $style, $perUserId);
    if ($html != '') {
      $ret['flg'] = 'OK';
      $ret['html'] = $html;
      echo json_encode($ret);
      exit();
    }
  } else {
    $ret['flg'] = 'NG';
    echo json_encode($ret);
    exit();
  }
}

function farmMessageAdd($con, $userId, $perUserId) {
  $message = escape($_POST['commText']);
  $date = escape($_POST['date']);
  $file = escape($_POST['postPhotoName']);
  if ($perUserId != '') {
    $id = $perUserId;
  } else {
    $id = $userId;
  }
  if (!$file) {
    $file = NULL;
  }
  $diaryMstId = checkMstId($con, $id);
  try {
    $con->beginTransaction();
    $sql = '
            INSERT INTO farmCommunicationTbl(farmDiaryId, userId, message, regImagePath, regDate, regTrueDate)
            VALUES(?, ?, ?, ?, ?, ?)
            ';
    $stmt = $con->prepare($sql);
    $flg = $stmt->execute(array($diaryMstId, $userId, nl2br($message), $file, $date, date(YmdHis)));

    if ($flg) {
      $con->commit();
      $ret['flg'] = 'OK';
      $comm = commMessageList($con, $id, $date);
      $ret['html'] = commMessageHtml($con, $comm);
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

function workInsert($con, $userId, $addWork, $lastId, $proc) {
  $flg = false;

  try {
    $con->beginTransaction();
    if ($proc == 'routineAdd') {
      $sql = 'INSERT INTO farmRoutineMstTbl(farmDiaryId, work, regDate)
      VALUES(?, ?, ?)';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($lastId, $addWork, date(YmdHis)));
    } else {
      $sql = 'UPDATE farmBehindWorkTbl
              INNER JOIN farmDiaryMstTbl
              ON farmBehindWorkTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
              SET priority = priority+1 WHERE workCompFlg=0
              AND delDate IS NULL AND regUserId=?';
      $stmt = $con->prepare($sql);
      $flg = $stmt->execute(array($userId));
      $stmt->closeCursor();
      if ($flg) {
        $sql = 'INSERT INTO farmBehindWorkTbl(farmDiaryId, work, regDate, priority)
                VALUES(?, ?, ?, ?)';
        $stmt = $con->prepare($sql);
        $flg = $stmt->execute(array($lastId, $addWork, date(YmdHis), 1));
      }
    }
    $stmt->closeCursor();
    if ($flg) {
      $con->commit();
    } else {
      $con->rollback();
    }
  } catch (Exception $e) {
    $con->rollback();
  }
  return $flg;
}

function workHtml($con, $work, $date, $style, $perUserId) {
  // 第三者からの追加ができるよう修正
  $style = '';
  $routine = $work['routine'];
  $behind = $work['behind'];
  $routineCount = count($routine);
  $behindCount = count($behind);
  $html = '';
  $i = 1;
  $loopFlg = false;
  $html .= '
  <tr>
  <th colspan="4" class="th_farmDiary"> ルーチンワーク </th>
  </tr>';
  while ($routineCount >= $i) {
    if ($i == 1) {
      $loopFlg = true;
    }
    $html .= '<tr>';
    if ($routine[$i]['flg'] == 1) {
      if ($routine[$i]['id']) {
        $html .= '<td colspan="1" class="" style="word-wrap: break-word; word-break: break-all;width: 440px; border-top: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;padding: 5px;"><span>&nbsp;&nbsp;&nbsp;</span><span><s>'.$routine[$i]['work'].'</s></span></td><td colspan="3" class="right" style="width: 70px; border-top: 1px solid #c0bfbf;border-right: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;padding: 5px;">'.$routine[$i]['name'].'&nbsp;'.$routine[$i]['realDate'].'</td>';
      } else {
        $html .= '<td class="td_farmDiaryValue">&nbsp;</td>';
      }
    } else {
      if ($routine[$i]['id']) {
        $html .= '<td colspan="4" class="td_farmDiaryValue"><input type="checkbox" name="routineCheck[]" value="'.$routine[$i]['id'].'" class="">&nbsp;'.$routine[$i]['work'].'</td>';
      } else {
        $html .= '<td class="td_farmDiaryValue">&nbsp;</td>';
      }
    }
    $html .= '</tr>';
//     if ($count == $i) {

//     }
    $i++;
  }
  $html .= '<tr>
  <td colspan="4" class="td_farmDiaryValue right">
  <a href="#" id="routine"  onclick="return false;" style="'.$style.'">追加</a>
  </td>
  </tr>';
  $html .= '
  <tr>
  <th colspan="4" class="th_farmDiary"> ビハインドワーク </th>
  </tr>';
  // 未作業の件数をカウントする
  $flgCount = 0;
  for ($i = 1; $behindCount >= $i; $i++) {
    switch ($behind[$i]['flg']) {
      case 0: $flgCount++; break;
    }
  }
  $i = 1;
  $j = 0;
  while ($behindCount >= $i) {
    $html .= '<tr>';
    if ($behind[$i]['flg'] == 1) {
      if ($behind[$i]['id']) {
        $html .= '<td colspan="1" class="" style="word-wrap: break-word; word-break: break-all;width: 440px;border-top: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;padding: 5px;"><span>&nbsp;&nbsp;&nbsp;</span><span><s>'.$behind[$i]['work'].'</s></span></td><td colspan="3" class="right" style="width: 70px; border-top: 1px solid #c0bfbf;border-right: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;">'.$behind[$i]['name'].'&nbsp;'.$behind[$i]['compDate'].'</td>';
      } else {
        $html .= '<td class="td_farmDiaryValue" >&nbsp;</td>';
      }
    } else {
      if ($behind[$i]['id']) {
        if ($date > date(Y).'-'.date(m).'-'.date(d)) {
          $html .= '<td colspan="2" style="word-wrap: break-word; word-break: break-all;width: 440px;border-top: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;padding: 5px;" class="move">&nbsp;&nbsp;&nbsp;'.$behind[$i]['work'].'</td>';
        } else {
          $html .= '<td colspan="2" style="word-wrap: break-word; word-break: break-all;width: 440px;border-top: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;padding: 5px;" class="move"><input type="checkbox" name="behindCheck[]" value="'.$behind[$i]['id'].'" class="">&nbsp;'.$behind[$i]['work'].'<input type="hidden" name="behindId[]" value="'.$behind[$i]['id'].'"></td>';
        }
        if ($flgCount != 1) {
          $j++;
          $up = '<a href="#" class="up" onClick="return false;"><img src="./templates/image/up.png" alt="優先順位を上げます"></a>';
          $down = '<a href="#" class="down" onClick="return false;"><img src="./templates/image/down.png" alt="優先順位を下げます"></a>';
          if ($j == 1) {
            $html .= '<td colspan="1" style="width: 20px; border-top: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;padding: 5px;">&nbsp<input type="hidden" name="priority[]" value="'.$behind[$i]['priority'].'"></td>';
            $html .= '<td colspan="1" style="width: 20px; border-top: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;border-right: 1px solid #c0bfbf;padding: 5px;">'.$down.'</td>';
          } else if ($j == $flgCount) {
            $html .= '<td colspan="1" style="width: 20px; border-top: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;padding: 5px;">'.$up.'<input type="hidden" name="priority[]" value="'.$behind[$i]['priority'].'"></td>';
            $html .= '<td colspan="1" style="width: 20px; border-top: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;border-right: 1px solid #c0bfbf;padding: 5px;">&nbsp</td>';
          } else {
            $html .= '<td colspan="1" style="width: 20px; border-top: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;padding: 5px;">'.$up.'<input type="hidden" name="priority[]" value="'.$behind[$i]['priority'].'"></td>';
            $html .= '<td colspan="1" style="width: 20px; border-top: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;border-right: 1px solid #c0bfbf;padding: 5px;">'.$down.'</td>';
          }
        } else {
          $html .= '<td class="" style="border-right: 1px solid #c0bfbf;">&nbsp;</td>';
        }

      } else {
        $html .= '<td class="td_farmDiaryValue">&nbsp;</td>';
      }
    }

    $html .= '</tr>';
    $i++;
  }
  $html .= '
  <tr>
  <td colspan="4" class="td_farmDiaryValue right">
  <a href="#" id="prioritySave" onclick="return false;" style="display:none;">優先順位を保存</a>&nbsp
  <a href="#" id="behind" onclick="return false;" style="'.$style.'">追加</a>
  </td>
  </tr>';
  $html .= '
  <tr>
  <td colspan="4" class="right">
    <span>チェックした作業を<span>
    <input type="button" id="workDel" value="削除" class="button_s textColorRed position_createButton_diary" style="'.$style.'">
    <input type="button" id="workButton" value="完了" class="button_s textColorRed position_createButton_diary">
    <input type="hidden" value="'.$date.'" name="date" id="date">
    <input type="hidden" value="'.$perUserId.'" name="perDiary" id="perDiary">
  </td>
</tr>';
//   if (!$loopFlg) {
//   	$html = '<tr><td class="td_farmDiaryValue"></td></tr>';
//   }
  return $html;
}

function workList($con, $userId, $date) {
  $sql = 'SELECT farmRoutineMstTbl.routineWorkId AS id, farmRoutineMstTbl.work,
           farmRoutineMstTbl.regDate AS date, delDate AS del
          FROM farmRoutineMstTbl
          INNER JOIN farmDiaryMstTbl
          ON farmRoutineMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
          WHERE farmDiaryMstTbl.regUserId = ? AND
           (CASE WHEN delDate IS NOT NULL THEN farmRoutineMstTbl.delDate
            ELSE  ?
            END
           ) > ? AND farmRoutineMstTbl.regDate < ?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, '9999-99-99 99:99:99', $date.' 23:59:59', $date.' 23:59:59'));
  $i = 1;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $work['routine'][$i]['id'] = $data['id'];
    $work['routine'][$i]['work'] = $data['work'];
    $work['routine'][$i]['date'] = $data['date'];
    $work['routine'][$i]['del'] = $data['del'];
    $i++;
  }
  $sql = 'SELECT workerId, userName, workCompFlg AS flg, workCompDate AS compDate, workCompRealDate as realDate
  FROM farmRoutineWorkTbl
  INNER JOIN userMstTbl
  ON workerId = userId
  WHERE
  routineWorkId = ? AND workCompDate BETWEEN ? AND ?';
  $i = 1;
  while (count($work['routine']) >= $i) {
    $stmt = $con->prepare($sql);
    $stmt->execute(array($work['routine'][$i]['id'], $date.' 00:00:00', $date.' 23:59:59'));
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $work['routine'][$i]['userId'] = $data['userId'];
      $work['routine'][$i]['name'] = $data['userName'];
      $work['routine'][$i]['flg'] = $data['flg'];
      $work['routine'][$i]['compDate'] = $data['compDate'];
      $work['routine'][$i]['realDate'] = $data['realDate'];
    }
    $i++;
  }
  $sql = 'SELECT farmBehindWorkTbl.behindWorkId AS id, farmBehindWorkTbl.work,
           farmBehindWorkTbl.regDate AS date, workCompFlg AS flg,
           farmBehindWorkTbl.workCompDate AS compDate, userName, priority
          FROM farmBehindWorkTbl
          INNER JOIN farmDiaryMstTbl
          ON farmBehindWorkTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
          LEFT JOIN userMstTbl
          ON userMstTbl.userId = workerId
          WHERE farmDiaryMstTbl.regUserId = ? AND
           (CASE WHEN delDate IS NOT NULL THEN farmBehindWorkTbl.delDate
            ELSE  ?
            END
           ) > ? AND
            farmBehindWorkTbl.regDate < ? AND
            (farmBehindWorkTbl.workCompDate > ? OR workCompFlg = 0)
            ORDER BY priority DESC';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, '9999-99-99 99:99:99', $date.' 23:59:59', $date.' 23:59:59', $date.' 00:00:00'));
  $i = 1;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $work['behind'][$i]['id'] = $data['id'];
    $work['behind'][$i]['work'] = $data['work'];
    $work['behind'][$i]['date'] = $data['date'];
    $work['behind'][$i]['flg'] = $data['flg'];
    $work['behind'][$i]['compDate'] = $data['compDate'];
    $work['behind'][$i]['name'] = $data['userName'];
    $work['behind'][$i]['priority'] = $data['priority'];
    $i++;
  }
  return $work;
}

function commMessageHtml($con, $comm) {
  $count = count($comm);
  $html = '<tr>
    <th colspan="4" class="th_farmDiary"> 通信欄 </th>
    </tr>';
  $i = 1;
  while ($count >= $i) {
    $html .= '
      <tr>
        <td class="td_farmDiaryValue" style="width: 18px;">'
        .$i.'
        </td>
        <td class="td_farmDiaryValue">
          '.$comm[$i]['name'].'
        </td>
        <td class="td_farmDiaryValue">
          '.$comm[$i]['message'].'<br>
          ';
    if ($comm[$i]['image']) {
      $html .= '
      <a href="'.$comm[$i]['image'].'" rel="prettyPhoto" title="" class="z40">
      <img src="'.$comm[$i]['image'].'" class="image_l" alt="">
      </a>
      ';
    }
    $html .= '
        </td>
        <td class="td_farmDiaryValue right" style="width: 100px;">
          '.$comm[$i]['date'].'
        </td>
      </tr>';
    $i++;
  }
  $html .= '<tr><td colspan="4"><div>&nbsp;</div></td></tr>';
  return $html;
}

function commMessageList($con, $userId, $date) {
  $sql = 'SELECT messageId AS messId, userName AS name, message, regImagePath AS image, regTrueDate AS date
         FROM farmCommunicationTbl
         INNER JOIN farmDiaryMstTbl
         ON farmCommunicationTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
         INNER JOIN userMstTbl
         ON farmCommunicationTbl.userId = userMstTbl.userId
         WHERE farmDiaryMstTbl.regUserId=?
         AND farmCommunicationTbl.regDate BETWEEN ? AND ?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, $date.' 00:00:00', $date.' 23:59:59'));
  $i = 1;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $comm[$i]['messId'] = $data['messId'];
    $comm[$i]['name'] = $data['name'];
    $comm[$i]['message'] = $data['message'];
    $comm[$i]['image'] = $data['image'];
    $comm[$i]['date'] = $data['date'];
    $i++;
  }
  return $comm;
}

function harvestHtml($con, $har, $userId, $perUserId) {
  $count = count($har);
  if ($perUserId != '') {
    if ($count > 0) {
      $html = '
      <tr>
        <th class="th_farmDiary"> 品種 </th>
        <th class="th_farmDiary"> 数量(/kg) </th>
        <th class="th_farmDiary"> 単価(/kg)</th>
        <th class="th_farmDiary"> 出荷先 </th>
      </tr>
      ';
    }
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
      <input type="text" name="eCrops[]" value="'.$har[$i]['brand'].'" style="width: 200px;">
    </td>
    <td class="td_farmDiaryValue center">
      <input type="text" name="eWeight[]" value="'.$har[$i]['weight'].'" style="width: 50px;">
    </td>
    <td class="td_farmDiaryValue center">
      <input type="text" name="ePrice[]" value="'.$har[$i]['price'].'" style="width: 50px;">
    </td>
    <td class="td_farmDiaryValue center">
      <input type="text" name="eDestination[]" value="'.$har[$i]['destination'].'" style="width: 200px;">
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
      <td class="center" style="border-top: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-right: 1px solid #c0bfbf;padding: 5px;">
      <input type="text" name="crops" style="width: 200px;" id="crops" maxlength="20">
      </td>
      <td class="center" style="border-top: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-right: 1px solid #c0bfbf;padding: 5px;">
      <input type="text" name="weight" style="width: 50px;" maxlength="5">
      </td>
      <td class="center" style="border-top: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-right: 1px solid #c0bfbf;padding: 5px;">
      <input type="text" name="price" style="width: 50px;" maxlength="5">
      </td>
      <td class="center" style="border-top: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-right: 1px solid #c0bfbf;padding: 5px;">
      <input type="text" name="destination" style="width: 200px;" id="destination" maxlength="20">
      </td>
      </tr>
      ';
      // 品名セレクトボックス内のオプションタグを生成する
      $sql = 'SELECT DISTINCT brand
      FROM farmHarvestTbl
      INNER JOIN farmDiaryMstTbl
      ON farmHarvestTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
      WHERE regUserId = ? ORDER BY brand ASC
      ';
      $stmt = $con->prepare($sql);
      $stmt->execute(array($userId));
      $option = "";
      while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $option .= '
        <option value="'.$data['brand'].'">'.$data['brand'].'</option>
        ';
      }
      // 出荷先セレクトボックス内のオプションタグを生成する
      $sql = 'SELECT DISTINCT destination
      FROM farmHarvestTbl
      INNER JOIN farmDiaryMstTbl
      ON farmHarvestTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
      WHERE regUserId = ? ORDER BY destination ASC
      ';
      $stmt = $con->prepare($sql);
      $stmt->execute(array($userId));
      $dOption = "";
      while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dOption .= '
        <option value="'.$data['destination'].'">'.$data['destination'].'</option>
        ';
      }
      $html .= '
      <tr>
      <td class="" style="border-right: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;padding: 5px;">
      <select style="width: 200px;" name="hinmoku_code" id="selectName">
      <option value="">過去に入力した品名を選べます</option>
      '.$option.'
      </select>
      </td>
      <td class="" style="border-right: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;padding: 5px;">&nbsp;</td>
      <td class="" style="border-right: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;padding: 5px;">&nbsp;</td>
      <td class="" style="border-right: 1px solid #c0bfbf;border-left: 1px solid #c0bfbf;border-bottom: 1px solid #c0bfbf;padding: 5px;">
      <select style="width: 200px;" name="destination_code" id="selectDestination">
      <option value="">過去に入力した出荷先を選べます</option>
      '.$dOption.'
      </select>
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
      <td class="td_farmDiaryValue right">'.$table[$i]['price'].'</td>
      <td class="td_farmDiaryValue right">'.$table[$i]['weight'].'</td>
      <td class="td_farmDiaryValue right">'.$table[$i]['pt'].'</td>
      <td class="td_farmDiaryValue center">'.$table[$i]['destination'].'</td>
      <td class="td_farmDiaryValue center">'.$table[$i]['harvestDate'].'</td>
    </tr>
    ';
    $i++;
  }
  $html .= '
  <tr>
  <td colspan="4" class="th_farmDiary right">合計</td>
  <td class="td_farmDiaryValue right">'.$table[$i - 1]['total'].'</td>
  </tr>
  <tr>
    <td>
      <a href="#" onclick="return false" id="close">閉じる</a>
    </td>
    <td>
      <form action="workEdit.php?date='.$date.'" method="post">
        <input type="submit" id="harvestButton" value="CSV形式で保存" class="">
        <input type="hidden" name="proc" value="getCSV">
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
    $csv .= $i.",".$table[$i]['brand'].",".$table[$i]['price'].",".$table[$i]['weight'].",".$table[$i]['pt'].",".$table[$i]['destination'].",".$table[$i]['harvestDate']."\n";
    $i++;
  }
  $csv .= " , , ,合計,".$table[$i - 1]['total'];
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
?>
