<?php
$inFlg = getInvite($con, $userId);
if ($inFlg['newGroupCount']) {
    $smarty->assign('class', 'headMenuPostImage');
    $smarty->assign('newGroupCount', $inFlg['newGroupCount']);
} else {
  if ($inFlg['groupHtml']) {
    $smarty->assign('class', 'headMenuPostImage');
  } else {
    $smarty->assign('class', 'headMenuPostImageGray');
    $smarty->assign('disabled', 'disabled');
    //   $smarty->assign('newGroupCount', 0);
  }


}
if ($inFlg['newFriendCount']) {
  $smarty->assign('friendClass', 'headMenuFriendImage');
  $smarty->assign('newFriendCount', $inFlg['newFriendCount']);
} else {
  if ($inFlg['friendHtml']) {
    $smarty->assign('friendClass', 'headMenuFriendImage');
  } else {
    $smarty->assign('friendClass', 'headMenuFriendImageGray');
    $smarty->assign('friendDisabled', 'disabled');
  }

//   $smarty->assign('newFriendCount', 0);
}
if ($inFlg['unreadCount']) {
  $smarty->assign('mailClass', 'headMenuMailImage');
  $smarty->assign('unreadCount', $inFlg['unreadCount']);
} else {
  $smarty->assign('mailClass', 'headMenuMailImageGray');
  $smarty->assign('mailDisabled', 'disabled');
//   $smarty->assign('unreadCount', 0);
}
$smarty->assign('authority', $_SESSION['user']['auth']);

?>