<?php
require_once("Smarty/Smarty.class.php");
require_once('./include/common.php');
require_once('./include/line_define.php');
$smarty = new Smarty();
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
session_start();
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);

$userId = $_SESSION['user']['userid'];
// メッセージの受け取り
require_once('./include/inFlg.php');

// 日記一覧の生成 diaryId, userId, title, userName, time, cCount
$html = getDiaryList($con, $userId, false);

//
$smarty->assign('html', $html);
$smarty->display("diaryList.tpl");