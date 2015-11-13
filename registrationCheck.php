<?php
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');
//ディレクトリ設定
$smarty = new Smarty();
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
session_start();

if ($_SESSION['regId']['sendMail']) {
  if ($_SESSION['regId']['sendMail'] == 1) {
    $smarty->assign('flg', 1);
    $smarty->display("regUserInfoTempResult.tpl");
    $_SESSION['regId']['sendMail'] = null;
    exit();
  }
  $smarty->assign('flg', 0);
  $smarty->display("regUserInfoTempResult.tpl");
  $_SESSION['regId']['sendMail'] = null;
//   $smarty->display("");
  exit();
}
// 前画面からの遷移の場合のみflgはtrue
if (!$_SESSION['reg']['flg']) {
    header('location:'.LOGIN_PAGE);
    exit();
} else {
    $_SESSION['reg']['flg'] = false;
    // registration.phpにて入力された値の取得
    $userId = $_SESSION['userId'];
    $pw = $_SESSION['pass'];
    $userName = $_SESSION['userName'];
    $realName = $_SESSION['realName'];
    $birthday = $_SESSION['birthday'];
    $gender = $_SESSION['gender'];
    $place = $_SESSION['place'];
    $_SESSION['regCheck']['flg'] = true;
    // スマ―ティー内の表示する値を動的に変更
    $smarty->assign('showId', $userId);
    $smarty->assign('showPw', $pw);
    $smarty->assign('showName', $userName);
    $smarty->assign('realName', $realName);
    $smarty->assign('birthday', $birthday);
    $smarty->assign('gender', $gender);
    $smarty->assign('place', $place);
    // スマ―ティー内のPOSTする値を動的に変更
    $smarty->assign('userId', $userId);
    $smarty->assign('userName', $userName);
    $smarty->assign('userPass', $pw);
    // スマーティーの表示
    $smarty->display("regUserInfoConfirm.tpl");
}
?>