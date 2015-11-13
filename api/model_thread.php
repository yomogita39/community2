<?php

class model_thread
{
  /**
  * グループもしくはフレンドIDがユーザIDと合致するか調べる
  */
  public function checkOtherId($con, $uid, $otherid, $flg = 'group', $timeLine=false){
    $ret = FALSE;
    if ($flg === 'group') {
      if ($timeLine) {
        $accept = ' AND acceptFlg=1 ';
      } else {
        $accept = '';
      }
      $sql = "
      SELECT
      groupMemberTbl.userId AS uid,
      groupMstTbl.groupName AS name
      FROM
      groupMemberTbl
      INNER JOIN
      groupMstTbl
      ON
      groupMemberTbl.groupId = groupMstTbl.groupId
      WHERE
      groupMemberTbl.groupId = :oid
      AND groupMemberTbl.userId = :uid
      {$accept}
      --  AND delflg = 0
      ";
    } else {
      $sql = "
      SELECT
      userMstTbl.userId AS uid,
      userMstTbl.userName AS name,
      friendMstTbl.acceptFlg AS flg
      FROM
      friendMstTbl
      INNER JOIN
      userMstTbl
      ON
      (friendMstTbl.userId = userMstTbl.userId
       OR friendMstTbl.friendId = userMstTbl.userId)
      WHERE
      ((friendMstTbl.friendId = :oid
      AND friendMstTbl.userId = :uid)
      OR (friendMstTbl.friendId = :uid
      AND friendMstTbl.userId = :oid))
        AND NOT userMstTbl.userId = :uid
      ";
    }

    $stmt = $con->prepare($sql);
    $stmt->bindValue(':oid', $otherid, PDO::PARAM_STR);
    //$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
    $stmt->bindValue(':uid', $uid, PDO::PARAM_STR);
    $stmt->execute();
    //echo "sql=". $stmt->debugDumpParams();
    while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
      if ($flg == 'group') {
        if($data['uid'] == $uid){
          $_SESSION['user']['groupName'] = $data['name'];
          $ret = TRUE;
        }
      } else {
        if($data['uid'] == $otherid){
          if ($data['flg'] == 1) {
            $ret = TRUE;
            $_SESSION['user']['friendName'] = $data['name'];
          } else {
            $stmt->closeCursor();
            $sql = "
            SELECT
            userMstTbl.userId AS uid
            FROM
            friendMstTbl
            INNER JOIN
            userMstTbl
            ON
            (friendMstTbl.userId = userMstTbl.userId
            OR friendMstTbl.friendId = userMstTbl.userId)
            WHERE
            (friendMstTbl.friendId = :uid
            AND friendMstTbl.userId = :oid)
              AND NOT userMstTbl.userId = :uid
            ";

            $stmt = $con->prepare($sql);
            $stmt->bindValue(':oid', $otherid, PDO::PARAM_STR);
            //$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
            $stmt->bindValue(':uid', $uid, PDO::PARAM_STR);
            $stmt->execute();
            $ret = 'unFriend';
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $ret = 'unFriend2';
            }
          }
        }
      }
    }
    return $ret;
  }

  /**
  * コメントを追加する
  */
  public function addComment($con, $uid, $oid, $msg, $path, $flg = 'group'){
    $ret = FALSE;
    if ($flg === 'group') {
      $sql = "SELECT
      MAX(regNo)
      FROM
      messageTbl
      WHERE
      groupId = ?";
      $insert = 'messageTbl (groupId, regNo, regUserId, message, regImagePath, regTime)';
      $values = '( :oid, :no, :uid, :msg, :path, :time)';
    } else {
      $sql = "SELECT
      MAX(regNo)
      FROM
      friendMessageTbl
      WHERE
      ((userId = ? AND friendId = ?) OR (userId = ? AND friendId = ?))";
      $insert = 'friendMessageTbl (friendId, regNo, userId, message, regImagePath, regTime, readFlg)';
      $values = '( :oid, :no, :uid, :msg, :path, :time, 0)';
    }

    $stmt = $con->prepare($sql);
    if ($flg === 'group') {
      $stmt->execute(array($oid));
    } else {
      $stmt->execute(array($oid, $uid, $uid, $oid));
    }

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    if (!$data['MAX(regNo)']) {
        $max = 1;
    } else {
        $max = $data['MAX(regNo)'] + 1;
    }
    $now = date(YmdHis);
    $sql = "
      INSERT INTO
      {$insert}
      VALUES
        {$values}";
    try {
      $con->beginTransaction();
      $stmt = $con->prepare($sql);
      $stmt->bindValue(':oid', $oid, PDO::PARAM_INT);
      $stmt->bindValue(':no', $max, PDO::PARAM_INT);
      $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
      $stmt->bindValue(':msg', $msg, PDO::PARAM_STR);
      $stmt->bindValue(':path', $path, PDO::PARAM_STR);
      $stmt->bindValue(':time', $now, PDO::PARAM_INT);
      $stmt->execute();
      $con->commit();
      $ret = TRUE;
    } catch(Exception $e) {
      $con->rollBack();
    }
    //echo "sql=". $stmt->debugDumpParams();

    return $ret;
  }

  /**
  * コメントを削除する
  */
  public function delComment($con, $uid, $gid, $cid){
    switch($uid){
      case 'aaa': $uid=1; break;
      case 'bbb': $uid=2; break;
      case 'ccc': $uid=3; break;
    }
    $ret = 0;
    $sql = "
      UPDATE
        TR_COMMENT
      SET
        delflg = 1
      WHERE
        threadid = :gid
        AND userid = :uid
        AND commentid = :cid
        AND delflg = 0
    ";
    try{
      $con->beginTransaction();
      $stmt = $con->prepare($sql);
      $stmt->bindValue(':gid', $gid, PDO::PARAM_INT);
      $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
      $stmt->bindValue(':cid', $cid, PDO::PARAM_INT);
      $stmt->execute();
      $con->commit();
      $ret = $stmt->rowCount();
    } catch(Exception $e) {
      $con->rollBack();
    }
    //echo "sql=". $stmt->debugDumpParams();

    return $ret;
  }

  /**
  * コメント一覧を取得する
  */
  public function getThreadComment($con, $id, $lastid=0, $flg = 'group', $uid = ''){
    if ($flg === 'group') {
      $now = date(YmdHis);
      try {
        $con->beginTransaction();
        $sql = 'UPDATE groupMemberTbl SET messageCheckTime = ?
        WHERE groupId = ? AND userId = ?';
        $stmt = $con->prepare($sql);
        $upFlg = $stmt->execute(array($now, $id, $uid));
        if ($upFlg) {
          $con->commit();
        } else {
          $con->rollBack();
        }
      } catch (Exception $e) {
        $con->rollBack();
      }
      $stmt->closeCursor();
      $sql = "
      SELECT
      messageTbl.regNo AS cid
      , messageTbl.message AS message
      , messageTbl.regTime AS ctime
      , messageTbl.regImagePath AS path
      , userMstTbl.userName AS name
      , userMstTbl.userId AS uid
      , userMstTbl.userImage AS image
      FROM
      messageTbl
      INNER JOIN
      userMstTbl
      ON
      messageTbl.regUserId = userMstTbl.userId
      WHERE
      messageTbl.groupId = :id
      AND messageTbl.regNo > :lastid
      AND userMstTbl.withdrawalFlg = 0
      ORDER BY
      messageTbl.regNo ASC
      -- LIMIT
      --  0, 100
      ";
    } else {
      try {
        $con->beginTransaction();
        $sql = 'UPDATE friendMessageTbl SET readFlg = 1
                WHERE userId = ? AND friendId = ?';
        $stmt = $con->prepare($sql);
        $upFlg = $stmt->execute(array($id, $uid));
        if ($upFlg) {
          $con->commit();
        } else {
          $con->rollBack();
        }
      } catch (Exception $e) {
        $con->rollBack();
      }
      $stmt->closeCursor();
      $sql = "
      SELECT
      friendMessageTbl.regNo AS cid
      , friendMessageTbl.message AS message
      , friendMessageTbl.regTime AS ctime
      , friendMessageTbl.regImagePath AS path
      , userMstTbl.userName AS name
      , userMstTbl.userId AS uid
      , userMstTbl.userImage AS image
      FROM
      friendMessageTbl
      INNER JOIN
      userMstTbl
      ON
      (friendMessageTbl.userId = userMstTbl.userId
      OR friendMessageTbl.friendId = userMstTbl.userId)
      WHERE
      ( friendMessageTbl.userId =:id AND friendMessageTbl.friendId =:uid
       OR friendMessageTbl.friendId =:id AND friendMessageTbl.userId =:uid)
      AND (userMstTbl.userId = friendMessageTbl.userId)
      AND friendMessageTbl.regNo > :lastid
      AND userMstTbl.withdrawalFlg = 0
      ORDER BY
      friendMessageTbl.regNo ASC
      -- LIMIT
      --  0, 100
      ";
    }

    $stmt = $con->prepare($sql);
    if ($flg === 'friend') {
      $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
    }
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':lastid', $lastid, PDO::PARAM_INT);
    $stmt->execute();
//     echo "sql=". $stmt->debugDumpParams();
    $ret = array();

    while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
      $ret[] = $data;
    }
    return $ret;
  }


  /**
  * コメント一覧(履歴)を取得する
  */
  public function getThreadCommentHistory($con, $gid, $firstid){
    $sql = "
      SELECT
        comment.commentid AS cid
        , comment.comment AS message
        , comment.createtime AS ctime
        , user.username AS name
        , user.userid AS uid
      FROM
        TR_COMMENT AS comment
      INNER JOIN
        MST_USER AS user
      ON
        comment.userid = user.userid
      WHERE
        comment.threadid = :gid
        AND comment.commentid < :firstid
        AND user.delflg = 0
        AND comment.delflg = 0
      ORDER BY
        comment.commentid ASC
      -- LIMIT
      --  0, 100
    ";
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':gid', $gid, PDO::PARAM_INT);
    $stmt->bindValue(':firstid', $firstid, PDO::PARAM_INT);
    $stmt->execute();
    //echo "sql=". $stmt->debugDumpParams();
    $ret = array();

    while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
      $ret[] = $data;
    }
    return $ret;
  }


  /**
  * 画面表示用HTMLを生成する
  */
  public function createHtml($dat, $uid, $flg = 'group'){
  session_start();
  $ret = array();
  $lastid = -1;
  $firstid = -1;
  $gravity = 0;
  $i = 1;
  foreach($dat as $v) {
    if($v['uid']==$uid) {
      // 左右判定に使用0=L, 1=R
      $gravity = 0;
    } else {
      $gravity = 1;
    }
    $ret[$i]["gravity"] = $gravity;
    $ret[$i]["userName"] = $v['name'];
    $ret[$i]["time"] = $v['ctime'];
    $ret[$i]["message"] = $v['message'];
    $ret[$i]["path"] = $v['path'];
      $ret[$i]["userImage"] = $v['image'];

    if($v['cid']>$lastid){
      $lastid = $v['cid'];
    }
    if($firstid==-1){
      $firstid=$v['cid'];
    }
    $i++;
//     echo $firstid;
//     echo $lastid;
  }
  $html = '';
  $i = 1;
  if ($ret) {
      foreach ($ret as $var) {
          if ($var['gravity'] == 0) {
            if ($i == count($ret)) {
              $html .= '<div class="left" id="'.$firstid.'">';
            } else {
              $html .= '<div class="left">';
            }
            $html .= '
            <table class="table_timeLineMyself">
              <tr>
                <td>
                  <div class="balloon_myself">
                    <div>'.$var['time'].'</div>
                        <br>
                          <div>'.nl2br($var['message']).'<span class="balloon_myself_triangle">&nbsp;</span></div>
                          ';
            if ($var['path']) {
              $html .= '<div>
                          <a href="'.$var['path'].'" rel="prettyPhoto" title="" class="z40">
                            <img src ="'.$var['path'].'" class="image_l" alt=""/>
                          </a>
                        </div>';
            }
                      $html .='
                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                <div>&nbsp;</div>';
          } else {
            if ($i == count($ret)) {
              $html .= '<div id="'.$firstid.'">';
            } else {
              $html .= '<div>';
            }
            $html .= '
            <table class="table_timeLineMember">
              <tr>
                <td class="top">
                  <div class="balloon_member">
                    <div>'.$var['time'].'</div>
                    <br>
                      <div>'.nl2br($var['message']).'<span class="balloon_member_triangle">&nbsp;</span></div>
                      ';
            if ($var['path']) {
              $html .= '<div>
                          <a href="'.$var['path'].'" rel="prettyPhoto" title="" class="z40">
                            <img src ="'.$var['path'].'" class="image_l" alt=""/>
                          </a>
                        </div>';
            }
                      $html .='
                      </div>
                    </td>
                  <td class="td_timeLineMember_image"><img src="'.$var['userImage'].'" class="image_timeLineMember" alt="メンバー画像"><p>
                  '.$var['userName'].'</p></td>
              </tr>
             </table>
            </div>';
        }
        $i++;
      }
    }
    $ret['lastid'] = $lastid;
    $ret['firstid'] = $firstid;
    $ret['html'] = $html;
    return $ret;
  }
}
?>
