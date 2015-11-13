<?php
require_once('./include/line_define.php');
require_once('./include/common.php');
session_start();
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
// DB接続
try {
  // PDOオブジェクトの生成
  $con = new PDO("mysql:dbname=LA04088615-line;host=mysql593.phy.lolipop.jp", DB_UID, DB_PWD);
} catch(PDOException $e) {
  die('err:'.$e->getMessage());
}
// データベース内の書式設定をUTF8に
$con->query('SET NAMES utf8');

if (escape($_POST['proc']) == 'unreadCount') {
  $isHtml = friendHtml($con, $userId, 'user', true, true);
  $i = 1;
  $html = '';
  while (count($isHtml) >= $i) {
    if (($i + 2) % 3 == 0) {
    $html .= '<tr>';
    }
    $html .= '<td class="td_friendList">
    <a href="./friendTimeLine.php?friendId='.$isHtml[$i]['friendId'].'" class="position_homeUnreadCount">
    <img src="'.$isHtml[$i]['friendImage'].'" alt="'.$isHtml[$i]['friendName'].'画像" class="image_s">';

    if (1 <= $isHtml[$i]['friendUnread'] && $isHtml[$i]['friendUnread'] <= 9) {
      $html .= '<span class="homeUnreadCount1">'.$isHtml[$i]['friendUnread'].'</span>';
    } elseif (10 <= $isHtml[$i]['friendUnread'] && $isHtml[$i]['friendUnread'] <= 99) {
      $html .= '<span class="homeUnreadCount2">'.$isHtml[$i]['friendUnread'].'</span>';
    } elseif (100 <= $isHtml[$i]['friendUnread'] && $isHtml[$i]['friendUnread'] <= 999) {
      $html .= '<span class="homeUnreadCount3">'.$isHtml[$i]['friendUnread'].'</span>';
    }
    $html .= '
    </a>
                <div><a href="./friendTimeLine.php?friendId='.$isHtml[$i]['friendId'].'">'.$isHtml[$i]['friendName'].'('.$isHtml[$i]['friendCnt'].')</a></div>
                    </td>';
    if (count($isHtml) == $i) {
      if (count($isHtml) % 3 == 1) {
        $html .= '
        <td class="td_friendList">&nbsp;</td>
      <td class="td_friendList">&nbsp;</td>';
      } elseif (count($isHtml) % 3 == 2) {
        $html .= '
        <td class="td_friendList">&nbsp;</td>';
      }
    }
    if ($i == 3 || count($isHtml) == $i) {
      $html .= '
      </tr>';
    }
    $i++;
  }
  echo $html;
  exit();
}

if (escape($_POST['proc']) == 'groupUnread') {
  $isHtml = groupHtml($con, $userId, true, true);
  $i = 1;
  $html = '';
  while (count($isHtml) >= $i) {
    if (($i + 2) % 3 == 0) {
      $html .= '<tr>';
    }
    $html .= '<td class="td_friendList">
    <a href="./timeLine.php?groupId='.$isHtml[$i]['groupId'].'" class="position_homeUnreadCount">
    <img src="'.$isHtml[$i]['imagePath'].'" alt="'.$isHtml[$i]['groupName'].'画像" class="image_s">';

    if (1 <= $isHtml[$i]['groupUnread'] && $isHtml[$i]['groupUnread'] <= 9) {
      $html .= '<span class="homeUnreadCount1">'.$isHtml[$i]['groupUnread'].'</span>';
    } elseif (10 <= $isHtml[$i]['groupUnread'] && $isHtml[$i]['groupUnread'] <= 99) {
      $html .= '<span class="homeUnreadCount2">'.$isHtml[$i]['groupUnread'].'</span>';
    } elseif (100 <= $isHtml[$i]['groupUnread'] && $isHtml[$i]['groupUnread'] <= 999) {
      $html .= '<span class="homeUnreadCount3">'.$isHtml[$i]['groupUnread'].'</span>';
    }
    $html .= '
    </a>
    <div><a href="./timeLine.php?groupId='.$isHtml[$i]['groupId'].'">'.$isHtml[$i]['groupName'].'('.$isHtml[$i]['count'].')</a></div>
    </td>';
    if (count($isHtml) == $i) {
      if (count($isHtml) % 3 == 1) {
        $html .= '
        <td class="td_friendList">&nbsp;</td>
        <td class="td_friendList">&nbsp;</td>';
      } elseif (count($isHtml) % 3 == 2) {
        $html .= '
        <td class="td_friendList">&nbsp;</td>';
      }
    }
    if ($i == 3 || count($isHtml) == $i) {
      $html .= '
      </tr>';
    }
    $i++;
  }
  echo $html;
  exit();
}

$html = getInvite($con, $userId);
if (escape($_POST['proc']) == 'groupPost') {
//   $html = groupHtml ($con, $userId, false);
  if ($html['newGroupCount']) {
    echo $html['newGroupCount'];
    exit();
  } else {
    if ($html['groupHtml']) {
      echo 'OK';
      exit();
    } else {
      echo 0;
      exit();
    }
  }
}

if (escape($_POST['proc']) == 'friendPost') {
//   $html = friendHtml ($con, $userId, 'friend', false);
  if ($html['newFriendCount']) {
    echo $html['newFriendCount'];
    exit();
  } else {
  if ($html['friendHtml']) {
      echo 'OK';
      exit();
    } else {
      echo 0;
      exit();
    }
  }
}

if (escape($_POST['proc']) == 'mailPost') {
//   $html = mailHtml ($con, $userId);
  if ($html['unreadCount']) {
    echo $html['unreadCount'];
    exit();
  } else {
    echo 0;
    exit();
  }
}


// header('location:'.LOGOUT);
// exit();
?>