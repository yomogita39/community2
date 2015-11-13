<?php

class model_usertop
{
  
  /**
  * スレッド一覧を取得する
  */
  public function getThreadList($con, $uid){
   
    $sql = "
      SELECT
        thread.threadid AS tid
        , thread.threadname AS tname
      FROM
        TR_COMMENT AS comment
      INNER JOIN
        TR_THREAD AS thread 
      ON
        comment.threadid = thread.threadid
      WHERE
        comment.userid = :id
        AND thread.delflg =0
      GROUP BY
        tid, tname
      ORDER BY
        thread.createtime
    ";

    $sql = "
      SELECT
        gmst.groupId AS tid
        , gmst.groupName AS tname
      FROM
        groupMemberTbl AS gtbl
      INNER JOIN
        groupMstTbl AS gmst
      ON
        gtbl.groupId = gmst.groupId
      WHERE
        gtbl.userId = :id
      GROUP BY
        tid, tname
      -- ORDER BY
      --  thread.createtime
    ";

    $stmt = $con->prepare($sql);
    //$stmt->bindValue(':id', $uid, PDO::PARAM_INT);
    $stmt->bindValue(':id', $uid, PDO::PARAM_STR);
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
  public function createHtml($dat){
    $html = '';
    foreach($dat as $v){
      $html .= "<div>id={$v['tid']}, name=". escape($v['tname']) ."";
      $html .= "<a href='". COMMENT_PAGE ."?id=". $v['tid'] ."'>表示</a>";
      //$html .= "<input type='button' value='表示' tid='". $v['tid'] ."'>";
      $html .= "</div>\n";
    }
    return $html;
  }
}
