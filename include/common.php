<?php
/**
* DB接続とセッション開始
*/
function db_connect($DB_URL, $DB_UID, $DB_PWD, $DB_NAME){
  start_page_session();
  try{
    $pdo = new PDO("mysql:dbname=$DB_NAME;host=$DB_URL",$DB_UID,$DB_PWD);
  } catch(PDOException $e){
    die('err:'. $e->getMessage());
  }
  $pdo->query('SET NAMES utf8');
  return $pdo;
}

/**
* PDO破棄
*/
function db_close($pdo){
  $pdo = null;
}

/**
* セッション開始
* ログインしていない場合は、ログインページにリダイレクト
*/
function start_page_session(){
  if(isset($_SESSION['user']['login'])){
    if($_SESSION['user']['login']===1
  && isset($_SESSION['user']['userid'])){
      //echo 'OK';
    } else {
      header('location:'. LOGIN_PAGE);
    }
  } else {
    header('location:'. LOGIN_PAGE);
  }
}

/**
* セッション初期化
* ログイン画面で使用
*/
function session_clear(){
  $_SESSION = array();
  if(isset($_COOKIE[session_name()])){
    setcookie(session_name(), '', time()-42000, '/');
  }
  session_destroy();
}

/**
* 文字列のエスケープ
*/
function escape($str){
  return str_replace('\\\\', '&yen;', trim(htmlspecialchars($str, ENT_QUOTES)));
}

/**
* 配列のスライス
*/
function ar_slice($arr, $limit, $isTop=1){
  // $isTop=0: 先頭から、　=1: 終わりから
  if(count($arr)>$limit){
    if($isTop==1){
      $arr = array_slice($arr, 0, $limit);
    } else {
      $arr = array_slice($arr, count($arr)-$limit, $limit);
    }
  }
  return $arr;
}

/**
 * debug
 */
function console($str) {
  $flg = true;
  if ($flg) {
    echo '<script type="text/javascript">';
    echo "window.console.log({$str})";
    echo '</script>';
  }
}

/**
 * URLリンクを生成する
 */
function autoLinker($str) {
  // エスケープ処理
  $pat_sub = preg_quote('-._~%:/?#[]@!$&\'()*+,;=', '/');
  // 正規表現パターン
  $pat  = '/((http|https):\/\/[0-9a-z' . $pat_sub . ']+)/i';
  // \\1が正規表現にマッチした文字列に置き換わる
  $rep  = '<a target="_blank" href="\\1">\\1</a>';

  $str = preg_replace ($pat, $rep, $str);
  return $str;
}

/**
 * dbから取得した時間を年月日に置き換える
 */
function changeDate($date, $boolean=true, $flg="") {
  if ($boolean) {
    $flg = str_split($flg);
    $cYear = substr($date, 0, 4).'年';
    $cYear .= substr($date, 5, 2).'月';
    $cYear .= substr($date, 8, 2).'日';
    if ($flg[0] === 't') {
      $cYear .= substr($date, 10);
    } else if ($flg[0] === 'T'){
      $cYear .= substr($date, 10, 3).'時';
      $cYear .= substr($date, 14, 2).'分';
      if ($flg[1] === 's') {
        $cYear .= substr($date, 17, 2).'秒';
      }
    }
  } else {
    $flg = str_split($flg);
    $cYear = substr($date, 0, 4).'-';
    $cYear .= substr($date, 5, 2).'-';
    $cYear .= substr($date, 8, 2);
    if ($flg[0] === 't') {
      $cYear .= substr($date, 10);
    } else if ($flg[0] === 'T'){
      $cYear .= substr($date, 10, 3).':';
      $cYear .= substr($date, 14, 2).':';
      if ($flg[1] === 's') {
        $cYear .= substr($date, 17, 2);
      }
    }
  }

  return $cYear;
}

/**
 * farmDiaryIdを取得する
 */
function checkMstId($con, $userId) {
  $sql = 'SELECT
  farmDiaryId
  FROM
  farmDiaryMstTbl
  WHERE
  regUserId = ?';
  $stmt = $con-> prepare($sql);
  $stmt->execute(array($userId));
  $flg = false;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $lastId = $data['farmDiaryId'];
    $flg = true;
    break;
  }
  if (!$flg) {
    try {
      $con->beginTransaction();
      $sql = 'INSERT INTO farmDiaryMstTbl(regUserId, diaryDate)
      VALUES (?, ?)';
      $stmt = $con->prepare($sql);
      $dbFlg =  $stmt->execute(array($userId, date(YmdHis)));
      $stmt->closeCursor();
      if ($dbFlg) {
        $lastId = $con->lastInsertId();
        $con->commit();
      } else {
        $con->rollback();
        exit();
      }
    } catch (Exception $e) {
      $con->rollback();
    }
  }
  return $lastId;
}
/**
 * 数字を曜日に変換して返す
 * @param unknown_type $date
 * @return string
 */
function getWeek($date) {
  switch (intval(date('w', strtotime($date)))) {
    case 0:$week = '日';break;
    case 1:$week = '月';break;
    case 2:$week = '火';break;
    case 3:$week = '水';break;
    case 4:$week = '木';break;
    case 5:$week = '金';break;
    case 6:$week = '土';break;
  }
  return $week;
}

/**
 * ユーザの権限を確認する
 */
function administrator($con, $userId, $auth) {
  // セッションに保存された権限は正しいか
  $sql = 'SELECT
  userId AS id, userAuthority AS auth
  FROM
  userMstTbl
  WHERE
  userId = ?
  AND userAuthority = ?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId, $auth));
  $successFlg = false;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($auth == $data['auth'] && $userId == $data['id']) {
      $successFlg = true;
    } else {
      header('location:'.LOGOUT);
    }
  }
  if ($successFlg) {
    if ($auth < 0 || 2 < $auth) {
      header('location:'.LOGOUT);
    }
  }
  return $successFlg;
}

/************************************
 * 農業日誌、経営日誌で使用する関数 *
 ***********************************/
/**
 * アクセス権限のあるユーザーのリストを作る
 */
function permissionDiaryList($con, $userId) {
  $sql = 'SELECT userMstTbl.userId, userName
  FROM userMstTbl
  INNER JOIN farmDiaryMstTbl
  ON farmDiaryMstTbl.regUserId = userMstTbl.userId
  INNER JOIN farmAccessMstTbl
  ON farmAccessMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  WHERE farmAccessMstTbl.permissionUserId = ?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId));
  $i = 1;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $per[$i]['userId'] = $data['userId'];
    $per[$i]['userName'] = $data['userName'];
    $i++;
  }
  return $per;
}

/**
 * 受け取ったリストからセレクトボックスのオプションを生成する
 */
function permissionDiaryHtml($con, $userId, $per, $perUserId) {
  $i = 1;
  $count = count($per);
  if ($count == 0) {
    $html = '<option value="">許可がありません</option>';
  } else {
    $html = '<option value="">許可のあるユーザ一覧</option>';
  }
  while ($count >= $i) {
    if ($per[$i]['userId'] == $perUserId) {
      $html .= '<option value="'.$per[$i]['userId'].'" selected="selected">'.$per[$i]['userName'].'</option>';
    } else {
      $html .= '<option value="'.$per[$i]['userId'].'">'.$per[$i]['userName'].'</option>';
    }
    $i++;
  }
  return $html;
}

/**
 * アクセス権限追加ダイアログに必要なオプション、メンバー一覧を取得する
 * @param db $con
 * @param int $userId
 */
function accessList($con, $userId) {
  $list = array();
  // アクセス権限を保有するメンバー一覧
  $sql = 'SELECT farmAccessMstTbl.permissionUserId AS userId, userName, userImage
  FROM farmAccessMstTbl
  INNER JOIN userMstTbl
  ON userMstTbl.userId = farmAccessMstTbl.permissionUserId
  LEFT JOIN farmDiaryMstTbl
  ON farmAccessMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  WHERE farmDiaryMstTbl.regUserId = ?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId));
  $i = 1;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $list['member'][$i]['userId'] = $data['userId'];
    $list['member'][$i]['userName'] = $data['userName'];
    $list['member'][$i]['userImage'] = $data['userImage'];
    $list['member'][$i]['mstId'] = checkAccessId($con, $data['userId'], $userId);
    $i++;
  }
  // 友達の一覧
  $friendList = friendHtml($con, $userId, 'user', true);
  $i = 1;
  $j = 1;
  while (count($friendList) >= $i) {
    $checkId = checkAccessId($con, $friendList[$i]['friendId'], $userId);
    if ($checkId == '') {
      $list['friend'][$j]['friendId'] = $friendList[$i]['friendId'];
      $list['friend'][$j]['friendName'] = $friendList[$i]['friendName'];
      $list['friend'][$j]['friendImage'] = $friendList[$i]['friendImage'];
      $j++;
    }
    $i++;
  }
  return $list;
}

/**
 * 受け取ったリストからアクセス権限追加ダイアログに使用するHTML文を生成する
 */
function accessHtml($con, $userId, $list) {
  $i = 1;
  $html = '<tr><td colspan="2">アクセス権限を持つ友達一覧</td><tr>';
  if (!count($list['member'])) {
    $html .= '<tr><td colspan="2">なし</td></tr>';
  }

  while (count($list['member']) >= $i) {
    $html .= '
    <tr>
    <td colspan="2" class="member">
    <input type="checkbox" name="accessCheck[]" value="'.$list['member'][$i]['mstId'].'" class="">
    &nbsp;'.$list['member'][$i]['userName'].'</td>
    </tr>
    ';
    $i++;
  }
  $optionHtml = '
  <option value="" >友達から選択できます</option>
  ';
  $i = 1;
  while (count($list['friend']) >= $i) {
    $optionHtml .= '
    <option value="'.$list['friend'][$i]['friendId'].'">'.$list['friend'][$i]['friendName'].'</option>
    ';
    $i++;
  }
  $ret['member'] = $html;
  $ret['option'] = $optionHtml;
  return $ret;
}

/**
 * アクセス管理テーブルに登録されているか
 * @param db $con
 * @param int $userId
 * @param int $otherId
 */
function checkAccessId($con, $userId, $otherId) {
  $sql = 'SELECT accessMstId
  FROM farmAccessMstTbl
  INNER JOIN farmDiaryMstTbl
  ON farmAccessMstTbl.farmDiaryId = farmDiaryMstTbl.farmDiaryId
  WHERE farmDiaryMstTbl.regUserId=? AND farmAccessMstTbl.permissionUserId=?';
  $stmt = $con->prepare($sql);
  $stmt->execute(array($otherId, $userId));
  $mstId = '';
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $mstId = $data['accessMstId'];
  }
  return $mstId;
}

function infoList($con) {
  $sql = 'SELECT
  infoId, userName, message, regTime
  FROM
  infoMstTbl
  INNER JOIN
  userMstTbl
  ON
  infoMstTbl.regUserId = userMstTbl.userId
  ORDER BY
  regTime DESC';
  $stmt = $con->prepare($sql);
  $stmt->execute();
  $flg = false;
  $i = 1;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $flg = true;
    $html[$i]['infoId'] = $data['infoId'];
    $html[$i]['userName'] = $data['userName'];
    $html[$i]['message'] = nl2br($data['message']);
    $html[$i]['regTime'] = $data['regTime'];
    $i++;
  }
  if ($flg) {
    return $html;
  } else {
    return $html;
  }
}

/**
 * pathのリネーム
 */
function renamePath ($postPhotoName, $proc, $userId) {
  if ($postPhotoName && $proc != 'upd') {
    if ($proc == 'profile' || $proc == 'edit') {
      $thumbPath = './user_image/thumb-';
      $oldPath = "./user_image/";
      $ext = substr($postPhotoName, -3);
      $now = date(YmdHis);
      $newPath = './user_image/'.$now.$userId.'.'.$ext;
    } else if ($proc == 'group') {
      $thumbPath = './group_image/thumb-';
      $oldPath = "./group_image/";
      $ext = substr($postPhotoName, -3);
      $now = date(YmdHis);
      $newPath = './group_image/group'.$now.$userId.'.'.$ext;
    } else {
      $thumbPath = '../message_image/thumb-';
      $newPath = './message_image/db-';
      $oldPath = "../message_image/";
    }
    $resizePath = $thumbPath.$postPhotoName;
    if ($proc == 'profile' || $proc == 'edit') {
      $path = $newPath;
    } else if ($proc == 'group') {
      $path = $newPath;
    } else {
      $path = $newPath.$postPhotoName;
    }

    if ($proc == 'profile' || $proc == 'edit') {
      rename($resizePath, $path);
    } else if ($proc == 'group') {
      rename($resizePath, $path);
    } else {
      if (file_exists('.'.$path) === TRUE) {
        unlink('.'.$path);
      }
      rename($resizePath, '.'.$path);
    }
    if (file_exists($oldPath.$postPhotoName) === TRUE) {
      unlink($oldPath.$postPhotoName);
    }
    if (file_exists($resizePath) === TRUE) {
      unlink($resizePath);
    }
  }
  return $path;
}
/**
 * グループ一覧の作成
 */
function groupHtml ($con, $userId, $flg, $setLimit=false, $html = array()) {
  if ($flg) {
    $accept = '1';
  } else {
    $accept = '0';
  }
  if ($setLimit) {
    $limit=' LIMIT 0, 6';
    $groupCount = ',
  (SELECT COUNT(groupMemberTbl.userId)
  FROM groupMemberTbl
  WHERE userId = :id AND acceptFlg = 1
) AS groupCount';
  } else {
    $limit='';
    $groupCount = '';
  }
  $sql = 'SELECT myGroup.groupId, myGroup.groupName, myGroup.regGroupImagePath,
 myGroup.messageCheckTime,
 ( SELECT COUNT( groupMemberTbl.groupId )
 FROM groupMemberTbl WHERE groupMemberTbl.groupId = myGroup.groupId
 AND acceptFlg =1 ) AS count '.$groupCount.' FROM messageTbl RIGHT OUTER JOIN
 ( SELECT groupMemberTbl.groupId, groupMstTbl.groupName,
 groupMstTbl.regGroupImagePath, groupMemberTbl.acceptTime,
 groupMemberTbl.messageCheckTime FROM groupMemberTbl
 INNER JOIN groupMstTbl ON groupMemberTbl.groupId = groupMstTbl.groupId
 WHERE groupMemberTbl.userId = :id AND groupMemberTbl.acceptFlg = '.$accept.' )
 AS myGroup ON messageTbl.groupId = myGroup.groupId
 GROUP BY myGroup.groupId ORDER BY MAX( messageTbl.regtime ) DESC ,
 myGroup.acceptTime DESC'.$limit;
//   echo $sql;
  $stmt = $con->prepare($sql);
  $stmt->bindValue(':id', $userId, PDO::PARAM_STR);
  $ret = $stmt->execute();
  // $html = "";

  $acceptHtml = array();
  $i = 1;
  $j = 1;
  // 取得した値の分だけhtml文を作成する
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //     $groupImage = $data['regGroupImagePath'];
    //     $groupName = $data['groupName'];
      if ($setLimit) {
        $html[$i]['groupCount'] = $data['groupCount'];
      }
      $html[$i]['count'] = $data['count'];
      $html[$i]['imagePath'] = $data['regGroupImagePath'];
      $html[$i]['groupName'] = $data['groupName'];
      $html[$i]['groupId'] = $data['groupId'];
      $html[$i]['checkTime'] = $data['messageCheckTime'];
      $i++;
  }
  $i = 1;
  $sql = 'SELECT COUNT(groupId) AS gUnread
  FROM
  messageTbl
  WHERE groupId = ? AND
   ? < messageTbl.regTime';
  while (count($html) >= $i) {
    $stmt = $con->prepare($sql);
    $stmt->execute(array($html[$i]['groupId'], $html[$i]['checkTime']));
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $html[$i]['groupUnread'] = $data['gUnread'];
    $i++;
  }
  return $html;
//   	echo $acceptHtml[1]['groupId'];
}

function friendHtml ($con, $userId, $friendFlg, $flg, $setLimit=false, $html = array()) {
  if ($setLimit) {
    $limit=' LIMIT 0, 6';
  }
  if ($flg && $friendFlg == 'user') {
    $sql = 'SELECT userMstTbl.userId, userMstTbl.userName, userMstTbl.userImage FROM userMstTbl
        RIGHT OUTER JOIN (
        SELECT CASE WHEN friendMessageTbl.friendId =:id
        THEN friendMessageTbl.regTime
        ELSE ""
        END AS messageTime, id
        FROM friendMessageTbl
        RIGHT OUTER JOIN (
        SELECT CASE WHEN userId =:id
        THEN friendId
        WHEN friendId =:id
        THEN userId
        END AS id, acceptTime
        FROM friendMstTbl
        WHERE (
        userId =:id
        OR friendId =:id
        )
        AND acceptFlg =1
        ) AS friend ON friendMessageTbl.userId = friend.id
        GROUP BY id
        ORDER BY MAX( messageTime ) DESC , friend.acceptTime DESC
        ) AS friendOrder ON friendOrder.id = userMstTbl.userId'.$limit;
  } else if($flg){
    $accept = ' AND acceptFlg = 1
    ORDER BY friendMstTbl.acceptTime DESC';
  } else {
    $accept = ' AND acceptFlg = 0
  ORDER BY friendMstTbl.applyTime DESC';
  }
  if ($friendFlg == 'user' || $friendFlg == 'userFriend') {
    if (!$flg || $friendFlg == 'userFriend') {
      $on = '( userMstTbl.userId = friendMstTbl.userId OR
      userMstTbl.userId = friendMstTbl.friendId )';
      $where = '( friendMstTbl.userId =:id OR friendMstTbl.friendId =:id )
      AND NOT userMstTbl.userId = :id ';
      $sql = 'SELECT DISTINCT
      userMstTbl.userId, userMstTbl.userName, userMstTbl.userImage
      FROM userMstTbl
      INNER JOIN
      friendMstTbl
      ON
      '.$on.'
      INNER JOIN
      friendMessageTbl
      ON
      (friendMstTbl.userId = friendMessageTbl.userId)
      OR (friendMstTbl.friendId = friendMessageTbl.userId)
      WHERE
      '.$where.$accept.$limit;
      //     echo $sql;
    }
  } else {
    $sql = 'SELECT userMstTbl.userId, userMstTbl.userName, userMstTbl.userImage
     FROM userMstTbl
     WHERE
      EXISTS (
      SELECT *
      FROM friendMstTbl
      WHERE userMstTbl.userId = (
      CASE WHEN friendId =:id
      THEN userId
      END ) AND acceptFlg = 0
     )';
  }
// 	if ($setLimit) {
// 		$limit=' LIMIT 0, 6';
// 		$groupCount = ',
// 		(SELECT COUNT(groupMemberTbl.userId)
// 		FROM groupMemberTbl
// 		WHERE userId = :id AND acceptFlg = 1
// 		) AS groupCount';
// 	} else {
// 		$limit='';
// 		$groupCount = '';
// 	}

  $stmt = $con->prepare($sql);
  $stmt->bindValue(':id', $userId, PDO::PARAM_STR);
  $ret = $stmt->execute();
  // $html = "";
  $acceptHtml = array();
  $i = 1;
  $j = 1;

  // 取得した値の分だけhtml文を作成する
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //     $groupImage = $data['regGroupImagePath'];
    //     $groupName = $data['groupName'];
//     if ($setLimit) {
//       $html[$i]['groupCount'] = $data['groupCount'];
//     }
//     $html[$i]['count'] = $data['count'];
    $html[$i]['friendImage'] = $data['userImage'];
    $html[$i]['friendName'] = $data['userName'];
    $html[$i]['friendId'] = $data['userId'];

//     $stmt = $con->prepare($sql);
//     $stmt->bindValue(':id', $data['userId'], PDO::PARAM_STR);
//     $ret = $stmt->execute();
    $i++;
  }
  $i = 1;
  $sql = 'SELECT COUNT( userId ) AS friendCnt
  FROM friendMstTbl
  WHERE (
  userId = :id
  OR friendId = :id
  )
  AND acceptFlg =1';
  $count = count($html);
  while ($count >= $i) {
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':id', $html[$i]['friendId'], PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $html[$i]['friendCnt'] = $data['friendCnt'];
    $i++;
  }

  $i = 1;
  $sql = 'SELECT COUNT(userId) AS fUnread
    FROM
     friendMessageTbl
    WHERE
     userId = :fid
    AND friendId = :uid
    AND readFlg = 0';
  while ($count >= $i) {
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':fid', $html[$i]['friendId'], PDO::PARAM_STR);
    $stmt->bindValue(':uid', $userId, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $html[$i]['friendUnread'] = $data['fUnread'];
    $i++;
  }

  if ($html) {
    $sql ='SELECT
    COUNT( userMstTbl.userId ) AS myFriendCnt
    FROM userMstTbl INNER JOIN friendMstTbl ON
    ( userMstTbl.userId = friendMstTbl.userId
    OR userMstTbl.userId = friendMstTbl.friendId )
    WHERE ( friendMstTbl.userId =:id OR friendMstTbl.friendId =:id )
    AND NOT userMstTbl.userId = :id AND acceptFlg = 1';

    $stmt = $con->prepare($sql);
    $stmt->bindValue(':id', $userId, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $html[1]['myFriendCnt'] = $data['myFriendCnt'];
  }

  return $html;
  //   	echo $acceptHtml[1]['groupId'];
}

function mailHtml ($con, $userId, $html = array()) {
  $sql = 'SELECT DISTINCT
           userMstTbl.userId, userMstTbl.userName, userMstTbl.userImage,
           (SELECT COUNT(friendMessageTbl.userId) FROM userMstTbl, friendMessageTbl
          WHERE
           userMstTbl.userId = friendMessageTbl.userId
          AND readFlg = 0 AND friendMessageTbl.friendId = :uid) AS noRead
          FROM
           userMstTbl
          INNER JOIN
           friendMessageTbl
          ON
           userMstTbl.userId = friendMessageTbl.userId
          WHERE
           friendMessageTbl.friendId = :uid
          AND
           readFlg = 0
          ORDER BY friendMessageTbl.regTime DESC';
  $stmt = $con->prepare($sql);
  $stmt->bindValue(':uid', $userId, PDO::PARAM_STR);
  $stmt->execute();
  $i = 1;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $html[$i]['mailImage'] = $data['userImage'];
    $html[$i]['mailName'] = $data['userName'];
    $html[$i]['mailId'] = $data['userId'];
    $html[$i]['mailUnread'] = $data['noRead'];
    $i++;
  }
  $i = 1;
  $sql = 'SELECT COUNT( userId ) AS friendCnt
  FROM friendMstTbl
  WHERE (
  userId = :id
  OR friendId = :id
  )
  AND acceptFlg =1';
  while (count($html) >= $i) {
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':id', $html[$i]['mailId'], PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $html[$i]['friendCnt'] = $data['friendCnt'];
    $i++;
  }
  return $html;

}

function memberHtml ($con, $gid, $flg) {
  $sql = 'SELECT
  groupMstTbl.regGroupImagePath, groupMstTbl.groupName,
  userMstTbl.userId, userMstTbl.userName, userMstTbl.userImage
  FROM
  groupMstTbl
  INNER JOIN
  groupMemberTbl
  ON
  groupMstTbl.groupId = groupMemberTbl.groupId
  INNER JOIN
  userMstTbl
  ON
  groupMemberTbl.userId = userMstTbl.userId
  WHERE
  groupMemberTbl.groupId = ? AND acceptFlg = ?';
  $stmt = $con->prepare($sql);
  if ($flg) {
    $stmt->execute(array($gid, 1));
  } else {
    $stmt->execute(array($gid, 0));
  }


  $i = 1;
  // smartyに渡す配列の生成
  $html = array();
  $filePath = './user_image/';
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // ユーザー画像がデフォルトか否か
    $groupMemberId = $data['userId'];
    $html[$i]['userImage'] = $data['userImage'];
    $_SESSION['group'][$groupMemberId] = $html[$i]['userImage'];
    // グループ名の表示に使用
    $groupName = $data['groupName'];
    $_SESSION['user']['groupName'] = $groupName;
    // グループ画像のPATH
    $groupImage = $data['regGroupImagePath'];
    $html[$i]['groupImage'] = $data['regGroupImagePath'];
    $html[$i]['groupName'] = $data['groupName'];
    $html[$i]['userName'] = $data['userName'];
    $html[$i]['userId'] = $groupMemberId;
    $i++;
  }
  return $html;
}

function getInvite($con, $userId) {
//    $html = groupHtml ($con, $userId, false);
$sql = 'SELECT COUNT(groupMemberTbl.groupId) AS newGroupCount FROM groupMemberTbl
          WHERE
          checkFlg = 0 AND groupMemberTbl.userId = :id AND acceptFlg = 0';
$stmt = $con->prepare($sql);
$stmt->bindValue(':id', $userId, PDO::PARAM_STR);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
//   $html = mailHtml ($con, $userId);
if ($data) {
  $ret['newGroupCount'] = $data['newGroupCount'];
  $ret['groupHtml'] = groupHtml ($con, $userId, false);
}

$sql = 'SELECT COUNT(friendMstTbl.userId) AS newFriendCount FROM friendMstTbl
WHERE
checkFlg = 0 AND friendMstTbl.friendId = :id AND acceptFlg = 0';
$stmt = $con->prepare($sql);
$stmt->bindValue(':id', $userId, PDO::PARAM_STR);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
//   $html = mailHtml ($con, $userId);
if ($data) {
  $ret['newFriendCount'] = $data['newFriendCount'];
  $ret['friendHtml'] = friendHtml ($con, $userId, 'friend', false, false);
}
//   if ($html) {
//     $ret[1]['groupId'] = true;
//   }
//   $html = friendHtml ($con, $userId, 'friend', false, false);
//   if ($html) {
//     $ret[1]['friendId'] = true;
//   }
  $sql = 'SELECT COUNT(friendMessageTbl.userId) AS unreadCount FROM friendMessageTbl
          WHERE
          readFlg = 0 AND friendMessageTbl.friendId = :id';
  $stmt = $con->prepare($sql);
  $stmt->bindValue(':id', $userId, PDO::PARAM_STR);
  $stmt->execute();
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
//   $html = mailHtml ($con, $userId);
  if ($data) {
    $ret['unreadCount'] = $data['unreadCount'];
  }
  return $ret;
  exit();
}

function getDiaryList($con, $userId, $flg=true, $friendId="", $dayFlg=false) {
  require_once('./api/model_thread.php');
  $wUserId = '';
  if ($friendId) {
    $wUserId = ' AND diaryMstTbl.userId='.$friendId;
  }
  $sql = "SELECT
          diaryId, diaryMstTbl.userId, title, publicState, createTime, userMstTbl.userName,
           (SELECT COUNT(diaryCommentTbl.diaryId)
            FROM diaryCommentTbl
            WHERE diaryMstTbl.diaryId = diaryCommentTbl.diaryId
            ) AS cCount
        FROM
          diaryMstTbl
        INNER JOIN
          userMstTbl
        ON
          diaryMstTbl.userId = userMstTbl.userId
        WHERE
          publicState <= 2 AND createTime>=ADDDATE(CURDATE(),Interval -30 DAY){$wUserId} ORDER BY createTime DESC";
  $stmt = $con->prepare($sql);
  $stmt->execute();
  $i = 1;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($userId == $data['userId']) {
      $html[$i]['diaryId'] = $data['diaryId'];
      $html[$i]['userId'] = $data['userId'];
      $html[$i]['title'] = $data['title'];
      if ($flg) {
        $cYear = substr($data['createTime'], 5, 2).'月';
        $cYear .= substr($data['createTime'], 8, 2).'日';
      } else {
        $cYear = substr($data['createTime'], 0, 4).'年';
        $cYear .= substr($data['createTime'], 5, 2).'月';
        $cYear .= substr($data['createTime'], 8, 2).'日';
        $cYear .= substr($data['createTime'], 10);
      }

      $html[$i]['time'] = $cYear;
      $html[$i]['userName'] = $data['userName'];
      $html[$i]['cCount'] = $data['cCount'];
    } else {
      if (intval($data['publicState']) == 1) {
        if( model_thread::checkOtherId($con, $userId, $data['userId'], 'friend') === TRUE ){
          $html[$i]['diaryId'] = $data['diaryId'];
          $html[$i]['userId'] = $data['userId'];
          $html[$i]['title'] = $data['title'];
          if ($flg) {
            if ($dayFlg) {
              $cYear = substr($data['createTime'], 0, 4).'年';
              $cYear .= substr($data['createTime'], 5, 2).'月';
              $cYear .= substr($data['createTime'], 8, 2).'日';
              $cYear .= substr($data['createTime'], 10);
            } else {
                $cYear = substr($data['createTime'], 5, 2).'月';
                $cYear .= substr($data['createTime'], 8, 2).'日';
            }
          } else {
            $cYear = substr($data['createTime'], 0, 4).'年';
            $cYear .= substr($data['createTime'], 5, 2).'月';
            $cYear .= substr($data['createTime'], 8, 2).'日';
            $cYear .= substr($data['createTime'], 10);
          }
          $html[$i]['time'] = $cYear;
          $html[$i]['userName'] = $data['userName'];
          $html[$i]['cCount'] = $data['cCount'];
        }
      } else if (intval($data['publicState']) == 0){
        $html[$i]['diaryId'] = $data['diaryId'];
        $html[$i]['userId'] = $data['userId'];
        $html[$i]['title'] = $data['title'];
        if ($flg) {
          if ($dayFlg) {
            $cYear = substr($data['createTime'], 0, 4).'年';
            $cYear .= substr($data['createTime'], 5, 2).'月';
            $cYear .= substr($data['createTime'], 8, 2).'日';
            $cYear .= substr($data['createTime'], 10);
          } else {
            $cYear = substr($data['createTime'], 5, 2).'月';
            $cYear .= substr($data['createTime'], 8, 2).'日';
          }
        } else {
          $cYear = substr($data['createTime'], 0, 4).'年';
          $cYear .= substr($data['createTime'], 5, 2).'月';
          $cYear .= substr($data['createTime'], 8, 2).'日';
          $cYear .= substr($data['createTime'], 10);
        }
        $html[$i]['time'] = $cYear;
        $html[$i]['userName'] = $data['userName'];
        $html[$i]['cCount'] = $data['cCount'];
      }
    }
    $i++;
  }
  if ($flg) {
    $html = ar_slice($html, 5, 1);
  }

  return $html;
}
function getMutter ($con, $userId, $flg=false, $friendId="") {
  require_once('./api/model_thread.php');
  $wUserId = '';
  if ($friendId) {
    $wUserId = ' AND mutterMstTbl.userId='.$friendId;
  }
  $sql = "SELECT
  userMstTbl.userId, userMstTbl.userName, userMstTbl.userImage,
  publicState, regTime, message
  FROM
  mutterMstTbl
  INNER JOIN
  userMstTbl
  ON
  mutterMstTbl.userId = userMstTbl.userId
  WHERE
  publicState <= 2 AND regTime>=ADDDATE(CURDATE(),Interval -7 DAY) {$wUserId} ORDER BY regTime DESC";
  $stmt = $con->prepare($sql);
  $stmt->execute();
  $i = 0;
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($userId == $data['userId']) {
      $html[$i]['userId'] = $data['userId'];
      $html[$i]['userName'] = $data['userName'];
      $html[$i]['userImage'] = $data['userImage'];
      $cYear = substr($data['regTime'], 0, 4).'年';
      $cYear .= substr($data['regTime'], 5, 2).'月';
      $cYear .= substr($data['regTime'], 8, 2).'日';
      $cYear .= substr($data['regTime'], 10);
      $html[$i]['time'] = $cYear;
      $html[$i]['message'] = nl2br(autoLinker($data['message']));
      $i++;
    } else {
      if (intval($data['publicState']) == 1) {
        if( model_thread::checkOtherId($con, $userId, $data['userId'], 'friend') === TRUE ){
          $html[$i]['userId'] = $data['userId'];
          $html[$i]['userName'] = $data['userName'];
          $html[$i]['userImage'] = $data['userImage'];
          $cYear = substr($data['regTime'], 0, 4).'年';
          $cYear .= substr($data['regTime'], 5, 2).'月';
          $cYear .= substr($data['regTime'], 8, 2).'日';
          $cYear .= substr($data['regTime'], 10);
          $html[$i]['time'] = $cYear;
          $html[$i]['message'] = nl2br(autoLinker($data['message']));
          $i++;
        }
      } else if (intval($data['publicState']) == 0){
        $html[$i]['userId'] = $data['userId'];
        $html[$i]['userName'] = $data['userName'];
        $html[$i]['userImage'] = $data['userImage'];
        $cYear = substr($data['regTime'], 0, 4).'年';
        $cYear .= substr($data['regTime'], 5, 2).'月';
        $cYear .= substr($data['regTime'], 8, 2).'日';
        $cYear .= substr($data['regTime'], 10);
        $html[$i]['time'] = $cYear;
        $html[$i]['message'] = nl2br(autoLinker($data['message']));
        $i++;
      }
    }
  }
  if (!$flg) {
    $html = ar_slice($html, 3, 1);
  }

  return $html;
}
/**
 * 旧暦を返す
 */
function getQreki($year, $month, $day) {
  require_once ('./api/qreki.php');
  $rokuyo = array('大安','赤口','先勝','友引','先負','仏滅');
  list($kyureki['year'],$kyureki['uruu'],$kyureki['month'],$kyureki['day'])=calc_kyureki($year,$month,$day);
  $kyureki['rokuyou'] = $rokuyo[get_rokuyou($year,$month,$day)];
  return $kyureki;
}

/**
 * 日記用のカレンダーを生成する
 */
function calendar($url, $userId, $con, $year = "", $month = "", $diaryId = "", $flg='user') {
  if(empty($year) && empty($month)) {
    $year = date("Y");
    $month = date("n");
  } else {
    $year = intval($year);
    $month = intval($month);
  }
  if ($diaryId != "") {
    $diaryId = '&diaryId='.$diaryId;
  }
  if ($flg !== 'user') {
    $friendId = '&friendId='.$userId;
    $friendSql = ' AND publicState < 2 ';
    $url2 = './friendDiaryList.php';
  } else {
    $friendId ='';
    $friendSql = '';
    $url2 = './myDiaryList.php';
  }
  switch ($month) {
    case 1: $pMonth = 12;$pYear = $year-1;
            $nMonth = $month+1; $nYear = $year; break;
    case 12: $nMonth = 1;$nYear = $year+1;
             $pMonth = $month-1; $pYear = $year; break;
    default: $pMonth = $month-1; $pYear = $year;
             $nMonth = $month+1; $nYear = $year; break;
  }
  if (9 >= $month) {
    $zero = 0;
  }
  $daySql = ' AND createTime BETWEEN '.$year.$zero.$month.'01'.' AND '.$year.$zero.$month.'31'.'235959';
  $sql = 'SELECT
  createTime, publicState
  FROM
  diaryMstTbl
  WHERE
  userId =?'.$friendSql.$daySql;
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId));
  $i = 1;
  $dayArray = array();
  $checkArray = '';
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pCheck = false;
    $day = intval(substr($data['createTime'], 8, 2));
    if ($checkArray != $day) {
      if ($flg !== 'user') {
        // 日記の公開範囲チェック
        switch ($data['publicState']) {
          case 0: $pCheck = true;
            break;
          case 1:
          if( model_thread::checkOtherId($con, $_SESSION['user']['userid'], $userId, 'friend') === TRUE ) {
            $pCheck = true;
          } break;
        }
      } else {
        $pCheck = true;
      }

      if ($pCheck) {
        $dayArray[$day] = true;
        $checkArray = $day;
      }
    }
  }
  //月末の取得
  $l_day = date("j", mktime(0, 0, 0, $month + 1, 0, $year));
  //初期出力
  $tmp = <<<EOM
<table class="calendarTb">
  <thead class="calendarThead">
    <tr>
      <td colspan="7">
        <a href="{$url}?year={$pYear}&month={$pMonth}{$diaryId}{$friendId}">&laquo;</a>
        {$year}年{$month}月
        <a href="{$url}?year={$nYear}&month={$nMonth}{$diaryId}{$friendId}">&raquo;</a>
      </td>
    </tr>
  </thead>
    <tr>
        <th class="sunTh">日</th>
        <th class="everydayTh">月</th>
        <th class="everydayTh">火</th>
        <th class="everydayTh">水</th>
        <th class="everydayTh">木</th>
        <th class="everydayTh">金</th>
        <th class="satTh">土</th>
    </tr>\n
EOM;
  $lc = 0;
  //月末分繰り返す
  for ($i = 1; $i < $l_day + 1;$i++) {
    $startA = '';
    $endA = '';
    if ($dayArray[$i]) {
      $startA = '<a href="'.$url2.'?year='.$year.'&month='.$month.'&day='.$i.$friendId.'">';
      $endA = '</a>';
    }
    //曜日の取得
    $week = date("w", mktime(0, 0, 0, $month, $i, $year));
    //曜日が日曜日の場合
    if ($week == 0) {
      $tmp .= "\t<tr>\n";
      $lc++;
    }
    //1日の場合
    if ($i == 1) {
      if($week != 0) {
        $tmp .= "\t<tr>\n";
        $lc++;
      }
      $tmp .= str_repeat("\t\t<td class=\"everydayTd\"></td>\n",$week);
    }
    if ($i == date("j") && $year == date("Y") && $month == date("n")) {
      //現在の日付の場合
      if ($week == 0) {
        $tmp .= "\t\t<td class=\"today sunTd\">{$startA}{$i}{$endA}</td>\n";
      } elseif ($week == 6){
        $tmp .= "\t\t<td class=\"today satTd\">{$startA}{$i}{$endA}</td>\n";
      } else {
        $tmp .= "\t\t<td class=\"today everydayTd\">{$startA}{$i}{$endA}</td>\n";
      }
    } else {
      //現在の日付ではない場合
      if ($week == 0) {
        $tmp .= "\t\t<td class=\"sunTd\">{$startA}{$i}{$endA}</td>\n";
      } elseif ($week == 6){
        $tmp .= "\t\t<td class=\"satTd\">{$startA}{$i}{$endA}</td>\n";
      } else {
        $tmp .= "\t\t<td class=\"everydayTd\">{$startA}{$i}{$endA}</td>\n";
      }
    }
    //月末の場合
    if ($i == $l_day) {
      $tmp .= str_repeat("\t\t<td class=\"everydayTd\"></td>\n", 6 - $week);
    }
    //土曜日の場合
    if($week == 6) {
      $tmp .= "\t</tr>\n";
    }
  }
  if($lc < 6) {
    $tmp .= "\t<tr>\n";
    $tmp .= repeat(7);
    $tmp .= "\t</tr>\n";
  }
  if($lc == 4) {
    $tmp .= "\t<tr>\n";
    $tmp .= repeat(7);
    $tmp .= "\t</tr>\n";
  }
  $tmp .= "</table>\n";
  return $tmp;
}

/**
 * 農業日誌用のカレンダーを生成する
 */
function farmCalendar($userId, $con, $url, $date, $year = "", $month = "", $preUserId) {
  if(empty($year) && empty($month)) {
    $year = date("Y");
    $month = date("n");
  } else {
    $year = intval($year);
    $month = intval($month);
  }
  switch ($month) {
    case 1: $pMonth = 12;$pYear = $year-1;
    $nMonth = $month+1; $nYear = $year; break;
    case 12: $nMonth = 1;$nYear = $year+1;
    $pMonth = $month-1; $pYear = $year; break;
    default: $pMonth = $month-1; $pYear = $year;
    $nMonth = $month+1; $nYear = $year; break;
  }
  if (9 >= $month) {
    $zero = 0;
  }
  $daySql = ' AND CAST(diaryDate AS DATETIME) BETWEEN '.$year.$zero.$month.'01000000'.' AND '.$year.$zero.$month.'31'.'235959';
  $sql = 'SELECT
  diaryDate AS date
  FROM
  farmDiaryMstTbl
  WHERE
  regUserId =?'.$daySql;
  $stmt = $con->prepare($sql);
  if ($preUserId != '') {
    $stmt->execute(array($preUserId));
  } else {
    $stmt->execute(array($userId));
  }
  $i = 1;
  $dayArray = array();
  $checkArray = '';
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $day = intval(substr($data['date'], 8, 2));
    // 重複チェック
    if ($checkArray != $day) {
      // 日にちをkeyに書き込みflgをtrueに
      $dayArray[$day] = true;
      // チェック用の変数に値の格納
      $checkArray = $day;
    }
  }
  //月末の取得
  $l_day = date("j", mktime(0, 0, 0, $month + 1, 0, $year));
  //初期出力
  $tmp = <<<EOM
<table class="calendarTb">
  <thead class="calendarThead">
    <tr>
      <td colspan="7">
        <a href="{$url}?year={$pYear}&month={$pMonth}&date={$date}&perDiary={$preUserId}">&laquo;</a>
        {$year}年{$month}月
        <a href="{$url}?year={$nYear}&month={$nMonth}&date={$date}&perDiary={$preUserId}">&raquo;</a>
      </td>
    </tr>
  </thead>
    <tr>
        <th class="sunTh">日</th>
        <th class="everydayTh">月</th>
        <th class="everydayTh">火</th>
        <th class="everydayTh">水</th>
        <th class="everydayTh">木</th>
        <th class="everydayTh">金</th>
        <th class="satTh">土</th>
    </tr>\n
EOM;
        $lc = 0;
        //月末分繰り返す
        for ($i = 1; $i < $l_day + 1;$i++) {
          $startA = '';
          $endA = '';
          if ($i <= 9) {
            $day = '0'.$i;
          } else {
            $day = $i;
          }
          //曜日の取得
          $week = date("w", mktime(0, 0, 0, $month, $i, $year));
          //曜日が日曜日の場合
          if ($week == 0) {
            $tmp .= "\t<tr class=\"trcalendar\">\n";
            $lc++;
            $wc = 'class="link_farmDiaryCalendar_sun"';
          } else if($week == 6) {
            $wc = 'class="link_farmDiaryCalendar_sat"';
          } else {
            $wc = 'class="link_farmDiaryCalendar_day"';
          }
//           if ($dayArray[$i]) {
//             $class = 'color_schedule';
//           } else {
//             $class = '';
//           }
          $startA = '<a '.$wc.' href="'.$url.'?year='.$year.'&month='.$month.'&date='.$year.'-'.$zero.$month.'-'.$day.'&perDiary='.$preUserId.'">';
          $endA = '</a>';
          //1日の場合
          if ($i == 1) {
            if($week != 0) {
              $tmp .= "\t<tr class=\"trcalendar\">\n";
              $lc++;
            }
            $tmp .= str_repeat("\t\t<td class=\"everydayTd\"></td>\n",$week);
          }
          if ($i == date("j") && $year == date("Y") && $month == date("n")) {
            //現在の日付の場合
            if ($week == 0) {
              $tmp .= "\t\t<td class=\"today sunTd {$class}\">{$startA}{$i}{$endA}</td>\n";
            } elseif ($week == 6){
              $tmp .= "\t\t<td class=\"today satTd {$class}\">{$startA}{$i}{$endA}</td>\n";
            } else {
              $tmp .= "\t\t<td class=\"today everydayTd {$class}\">{$startA}{$i}{$endA}</td>\n";
            }
          } else {
            //現在の日付ではない場合
            if ($week == 0) {
              $tmp .= "\t\t<td class=\"sunTd {$class}\">{$startA}{$i}{$endA}</td>\n";
            } elseif ($week == 6){
              $tmp .= "\t\t<td class=\"satTd {$class}\">{$startA}{$i}{$endA}</td>\n";
            } else {
              $tmp .= "\t\t<td class=\"everydayTd {$class}\">{$startA}{$i}{$endA}</td>\n";
            }
          }
          //月末の場合
          if ($i == $l_day) {
            $tmp .= str_repeat("\t\t<td class=\"everydayTd\"></td>\n", 6 - $week);
          }
          //土曜日の場合
          if($week == 6) {
            $tmp .= "\t</tr>\n";
          }
        }
        if($lc < 6) {
          $tmp .= "\t<tr>\n";
          $tmp .= repeat(7);
          $tmp .= "\t</tr>\n";
        }
        if($lc == 4) {
          $tmp .= "\t<tr>\n";
          $tmp .= repeat(7);
          $tmp .= "\t</tr>\n";
        }
        $tmp .= "</table>\n";
        return $tmp;
}

/**
 * カレンダー生成用の関数
 */
function repeat($n) {
  return str_repeat("\t\t<td> </td>\n", $n);

}
/**
 * メールアドレスの正規表現
 */
function isValidEmailFormat($email, $supportPeculiarFormat = true){
  $wsp              = '[\x20\x09]'; // 半角空白と水平タブ
  $vchar            = '[\x21-\x7e]'; // ASCIIコードの ! から ~ まで
  $quoted_pair      = "\\\\(?:{$vchar}|{$wsp})"; // \ を前につけた quoted-pair 形式なら \ と " が使用できる
  $qtext            = '[\x21\x23-\x5b\x5d-\x7e]'; // $vchar から \ と " を抜いたもの。\x22 は " , \x5c は \
  $qcontent         = "(?:{$qtext}|{$quoted_pair})"; // quoted-string 形式の条件分岐
  $quoted_string    = "\"{$qcontent}+\""; // " で 囲まれた quoted-string 形式。
  $atext            = '[a-zA-Z0-9!#$%&\'*+\-\/\=?^_`{|}~]'; // 通常、メールアドレスに使用出来る文字
  $dot_atom         = "{$atext}+(?:[.]{$atext}+)*"; // ドットが連続しない RFC 準拠形式をループ展開で構築
  $local_part       = "(?:{$dot_atom}|{$quoted_string})"; // local-part は dot-atom 形式 または quoted-string 形式のどちらか
  // ドメイン部分の判定強化
  $alnum            = '[a-zA-Z0-9]'; // domain は先頭英数字
  $sub_domain       = "{$alnum}+(?:-{$alnum}+)*"; // hyphenated alnum をループ展開で構築
  $domain           = "(?:{$sub_domain})+(?:[.](?:{$sub_domain})+)+"; // ハイフンとドットが連続しないように $sub_domain をループ展開
  $addr_spec        = "{$local_part}[@]{$domain}"; // 合成
  // 昔の携帯電話メールアドレス用
  $dot_atom_loose   = "{$atext}+(?:[.]|{$atext})*"; // 連続したドットと @ の直前のドットを許容する
  $local_part_loose = $dot_atom_loose; // 昔の携帯電話メールアドレスで quoted-string 形式なんてあるわけない。たぶん。
  $addr_spec_loose  = "{$local_part_loose}[@]{$domain}"; // 合成
  // 昔の携帯電話メールアドレスの形式をサポートするかで使う正規表現を変える
  if($supportPeculiarFormat){
    $regexp = $addr_spec_loose;
  }else{
    $regexp = $addr_spec;
  }
  // \A は常に文字列の先頭にマッチする。\z は常に文字列の末尾にマッチする。
  if(preg_match("/\A{$regexp}\z/", $email)){
    return true;
  }else{
    return false;
  }
}
/**
 * 拡張子のチェックをする
 */
function checkImage($file, $cron=false) {
  if (!$cron) {
    $filesName = basename($file['name']);
  } else {
    $filesName = $file;
  }

  $checkExtension = end(explode('.', $filesName));

  if ($checkExtension === 'gif' ||
      $checkExtension === 'jpg' ||
      $checkExtension === 'png' ||
      $checkExtension === 'GIF' ||
      $checkExtension === 'JPG' ||
      $checkExtension === 'PNG' ||
      $checkExtension === 'jpeg' ||
      $checkExtension === 'JPEG') {
    return $checkExtension;
  } else {
    return false;
  }
}

?>
