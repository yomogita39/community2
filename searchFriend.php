<?php
/**
 * 友達検索画面の実装
 */
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');
require_once('./include/location.php');
$smarty = new Smarty();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
session_start();
$userId = $_SESSION['user']['userid'];
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// ポスト受け取り
require_once('./include/inFlg.php');

if (array_key_exists('proc', $_POST)) {
  if (escape($_POST['proc']) == 'profile') {
    $friendId = escape($_POST['friendId']);
    if (dbInsert($con, $userId, $friendId) === 3) {
      echo 'OK';
//       header('location:'.PROFILE.'?userId='.$friendId);
      exit();
    } else {
      echo 'NG';
//       header('location:'.PROFILE.'?userId='.$friendId);
      exit();
    }
  }

  $inUserName = escape($_POST['userName']);
  $inGender = escape($_POST['gender']);
  $inBirthday = escape($_POST['birthday']);
  $inPlace = escape($_POST['place']);
  $inIntro = escape($_POST['intro']);

  if (!$inUserName && !$inGender && !$inBirthday && !$inPlace
      && !$inIntro) {
    $sql = 'SELECT
    userId, userImage, userName, introduction
    FROM
    userMstTbl
    WHERE
    NOT userId=?
    AND NOT EXISTS
    (SELECT friendId
    FROM friendMstTbl
    WHERE
    friendMstTbl.friendId=userMstTbl.userId AND
    userId=?)
    AND NOT EXISTS
    (SELECT userId
    FROM friendMstTbl
    WHERE
    friendMstTbl.userId=userMstTbl.userId AND
    friendId=?)
    ';
    $stmt = $con->prepare($sql);
    $stmt->execute(array($userId, $userId, $userId));
    $i = 0;
    $dataFlg = false;
    $html = array();
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $dataFlg = true;
      $html[$i]['userId'] = $data['userId'];
      $html[$i]['userName'] = $data['userName'];
      $html[$i]['introduction'] = $data['introduction'];
      $html[$i]['userImage'] = $data['userImage'];
      $i++;
    }
    $stmt->closeCursor();
    if (!$dataFlg) {
      $num = 1;
    } else {
      $num = 0;
      $smarty->assign('html', $html);
      $smarty->assign('flg', true);
    }
    $select = ' selected="selected"';
    $checkGender = genderCheck($inGender);
    $checkPlace = placeCheck($inPlace);
    $year = forYear($inBirthday);
    $smarty->assign('inUserName', $inUserName);
    $smarty->assign($checkGender, $select);
    $smarty->assign('year', $year);
    $smarty->assign($checkPlace, $select);
    $smarty->assign('inIntro', $inIntro);
    $smarty->display("searchFriend.tpl");
    db_close($con);
    exit();
  } else {
    // 招待グループに招待ユーザがいない場合のみ結果を表示する
    $where = "";
    $wPlace = "";
    $wBirthday = "";
    $wGender = "";
    $wUserName = "";
    if($inIntro){
      $str = array("　", " and ", " AND ");
      $inIntro = str_replace($str, " ", $inIntro);
      if(stristr($inIntro, " ")){//複数キーワードでの検索
        $ex = explode(" ", $inIntro);
        $count = count($ex);
        for($i=0; $i<$count; $i++){
          if($i!="0"){
            $where = $where." and";
          }
          $where = $where.' introduction LIKE "%'.$ex[$i].'%"';
        }
      }else{//単体キーワードでの検索
        $where =  'introduction LIKE "%'.$inIntro.'%"';
      }
    }
    if ($inUserName) {
      if ($where) {
        $wUserName = ' AND userName LIKE "%'.$inUserName.'%"';
      } else {
        $wUserName = ' userName LIKE "%'.$inUserName.'%"';
      }

    }
    if ($inPlace) {
      if ($inUserName || $where) {
        $wPlace = ' AND place = "'.$inPlace.'"';
      } else {
        $wPlace = ' place = "'.$inPlace.'"';
      }
    }
    if ($inBirthday) {
      if ($inUserName || $inPlace || $where) {
        $wBirthday = ' AND birthday = '.$inBirthday;
      } else {
        $wBirthday = ' birthday = '.$inBirthday;
      }
    }
    if ($inGender) {
      if ($inUserName || $inPlace || $inBirthday || $where) {
        $wGender = ' AND gender = "'.$inGender.'"';
      } else {
        $wGender = ' gender = "'.$inGender.'"';
      }

    }
    $sql = 'SELECT
    userId, userImage, userName, introduction
    FROM
    userMstTbl
    WHERE
    '.$where.$wUserName.$wPlace.$wBirthday.$wGender.' AND NOT EXISTS
    (SELECT friendId
    FROM friendMstTbl
    WHERE
    friendMstTbl.friendId=userMstTbl.userId AND
    userId=?)
    AND NOT EXISTS
    (SELECT userId
    FROM friendMstTbl
    WHERE
    friendMstTbl.userId=userMstTbl.userId AND
    friendId=?)AND NOT userId=?
    ';
    $stmt = $con->prepare($sql);
    $stmt->execute(array($userId, $userId, $userId));
    $i = 0;
    $dataFlg = false;
    $html = array();
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $dataFlg = true;
      $html[$i]['userId'] = $data['userId'];
      $html[$i]['userName'] = $data['userName'];
      $html[$i]['introduction'] = $data['introduction'];
      $html[$i]['userImage'] = $data['userImage'];
      $i++;
    }
    $stmt->closeCursor();
    if (!$dataFlg) {
      $smarty->assign('html', $html);
      $smarty->assign('flg', true);
      $num = 1;
    } else {
      $num = 0;
      $smarty->assign('html', $html);
      $smarty->assign('flg', true);


    }
    $select = ' selected="selected"';
    $checkGender = genderCheck($inGender);
    $checkPlace = placeCheck($inPlace);
    $year = forYear($inBirthday);
    $smarty->assign('inUserName', $inUserName);
    $smarty->assign($checkGender, $select);
    $smarty->assign('year', $year);
    $smarty->assign($checkPlace, $select);
    $smarty->assign('inIntro', $inIntro);
    $smarty->display("searchFriend.tpl");
    db_close($con);
    exit();
  }
} elseif (array_key_exists('userId', $_POST)) {
  $inviteUserId = escape($_POST['userId']);
  $num = dbInsert($con, $userId, $inviteUserId);
}
if ($num) {
  // テンプレートの表示
  $year = forYear(0);
  $smarty->assign('year', $year);
  $smarty->display("searchFriend.tpl");
  alert($num);
} else {
  $smarty->assign('flg', false);
  $year = forYear(0);
  $smarty->assign('year', $year);
  $smarty->display("searchFriend.tpl");
}
db_close($con);
exit();

/**
 * 指定したnumに対応したアラートを表示する
 * @param int $num
 */
function alert($num) {
  switch ($num) {
    case 1:
      echo "<script type=\"text/javascript\" >";
      echo "alert(\"ユーザーが見つかりませんでした\");";
      echo "</script>";
      break;
    case 2:
      echo "<script type=\"text/javascript\" >";
      echo "alert(\"申請に失敗しました\");";
      echo "</script>";
      break;
    case 3:
      echo "<script type=\"text/javascript\" >";
      echo "alert(\"申請しました\");";
      echo "</script>";
      break;
  }
  exit();
}

/**
 * データベースへのインサートを行い結果をnumで返す
 * @param db $con
 * @param String $userId
 * @param int $groupId
 * @param String $inviteUserId
 * @return $num
 */
function dbInsert($con, $userId, $friendId) {
  try {
    $now = date(YmdHis);
    $con->beginTransaction();
    $sql = 'insert into friendMstTbl (userId, friendId, applyTime, acceptFlg, checkFlg)
    values (?, ?, ?, ?, ?)';
    $stmt = $con->prepare($sql);
    // sql文の実行
    $flg = $stmt->execute(array($userId, $friendId, $now, 0, 0));
    // $stmtのクローズ
    $stmt->closeCursor();
    if ($flg) {
      $con->commit();
      return 3;
    } else {
      $con->rollBack();
      return 2;
    }

  } catch (PDOException $e) {
    $con->rollBack();
    return 2;
  }
}
?>