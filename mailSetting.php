<?php
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');

$smarty = new Smarty();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";

$mail = escape($_GET['mail']);
$userId = escape($_GET['id']);
$tempId = escape($_GET['address']);
$tempPass = escape($_GET['pass']);
if ($userId && $tempId && $tempPass) {
  try{
    $con = new PDO("mysql:dbname=".DB_NAME.";host=".DB_URL,DB_UID,DB_PWD);
  } catch(PDOException $e){
    die('err:'. $e->getMessage());
  }
  $con->query('SET NAMES utf8');
  $sql = "
  SELECT
  userId, tempId, pwd, limitTime
  FROM
  userTempTbl
  WHERE
  userId = ?
  ";
  $stmt = $con->prepare($sql);
  $stmt->execute(array($userId));
  $regCompFlg = false;
  $dataFlg = false;
  while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    $dataFlg = true;
    if(time() < strtotime($data['limitTime'])){
      if($data['tempId']===$tempId){
        if($data['pwd']===$tempPass){
          $regCompFlg = true;
        }
      }
    }
  }
  $stmt->closeCursor();
  if ($regCompFlg) {
    try {
      // トランザクション開始
      $con->beginTransaction();
      // sql文の発行
      $sql = 'update userMstTbl set mailAddress=?
      where userId=?';
      $stmt = $con->prepare($sql);
      // sql文の実行
      $flg = $stmt->execute(array($mail, $userId));
      // $stmtのクローズ
      $stmt->closeCursor();

      if ($flg) {
        $sql = 'delete from userTempTbl where userId=?';
        $stmt = $con->prepare($sql);
        $flg2 = $stmt->execute(array($userId));
        $stmt->closeCursor();
        if ($flg2) {
          $con->commit();
          $smarty->assign('flg', 2);
        } else {
          $con->rollBack();
          $smarty->assign('flg', 1);
        }
      } else {
        $con->rollBack();
        $smarty->assign('flg', 1);
      }
    } catch (Exception $e) {
      $con->rollBack();
      $smarty->assign('flg', 1);
    }
  } else {
    $smarty->assign('flg', 3);
  }
  $smarty->display("regUserInfoResult.tpl");
  db_close($con);
} else {
  header('location:'.LOGIN_PAGE);
}