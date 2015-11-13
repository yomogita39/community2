<?php
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');
session_start();
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
  try {
    // PDOオブジェクトの生成
    $pdo = new PDO("mysql:dbname=LA04088615-line;host=mysql593.phy.lolipop.jp", DB_UID, DB_PWD);
  } catch(PDOException $e) {
    die('err:'.$e->getMessage());
  }
  $pdo->query('SET NAMES utf8');

  $sql = "
  SELECT
    userId, tempId, pwd, limitTime
  FROM
  userTempTbl
  WHERE
  userId = ?
  ";
  $stmt = $pdo->prepare($sql);
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
      $pdo->beginTransaction();
      // sql文の発行
      $sql = 'update userMstTbl set regCompFlg=1
             where userId=?';
      $stmt = $pdo->prepare($sql);
      // sql文の実行
      $flg = $stmt->execute(array($userId));
      // $stmtのクローズ
      $stmt->closeCursor();

      // 初期グループへの登録
      $now = date(YmdHis);
      $sql = 'insert into groupMemberTbl (groupId, userId, applyTime, applyUser, acceptFlg, acceptTime)
                     values (?, ?, ?, ?, ?, ?)';
      $stmt = $pdo->prepare($sql);
      $flg2 = $stmt->execute(array(1, $userId, $now, 1, 1, $now));
      // $stmtのクローズ
      $stmt->closeCursor();

      if ($flg && $flg2) {
        // バグ報告グループへの登録
        $sql = 'insert into groupMemberTbl (groupId, userId, applyTime, applyUser, acceptFlg, acceptTime)
        values (?, ?, ?, ?, ?, ?)';
        $stmt = $pdo->prepare($sql);
        $inFlg = $stmt->execute(array(2, $userId, $now, 1, 1, $now));
        // $stmtのクローズ
        $stmt->closeCursor();

        // 管理人との友達登録
        $sql = 'insert into friendMstTbl (userId, friendId, applyTime, acceptFlg, acceptTime, checkFlg)
        values (?, ?, ?, ?, ?, ?)';
        $stmt = $pdo->prepare($sql);
        $inFlg2 = $stmt->execute(array(1, $userId, $now, 1, $now, 1));
        // $stmtのクローズ
        $stmt->closeCursor();

        $sql = 'SELECT userName
                FROM userMstTbl
                WHERE userId = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($userId));
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        // 管理人からのウェルカムメッセージ
        $message = $data['userName'].'さん
はじめまして、農業ＳＮＳ管理者です。

ようこそ農業ＳＮＳへ！
不明な点がありましたら、お気軽に
お問合せ下さい。';
        $sql = 'insert into friendMessageTbl (userId, friendId, regNo, message, regTime, readFlg)
        values (?, ?, ?, ?, ?, ?)';
        $stmt = $pdo->prepare($sql);
        $inFlg3 = $stmt->execute(array(1, $userId, 1, $message, $now, 0));
        // $stmtのクローズ
        $stmt->closeCursor();

        $sql = 'delete from userTempTbl where userId=?';
        $stmt = $pdo->prepare($sql);
        $flg3 = $stmt->execute(array($userId));
        $stmt->closeCursor();
        if ($inFlg && $flg3 && $inFlg2 && $inFlg3) {
          $pdo->commit();
          $smarty->assign('flg', 0);
        } else {
          $pdo->rollBack();
          $smarty->assign('flg', 1);
          $smarty->assign('userMail', $mail);
        }
      } else {
        $pdo->rollBack();
        $smarty->assign('flg', 1);
        $smarty->assign('userMail', $mail);
      }
    } catch (Exception $e) {
      // 何らかのエラーの際はエラーログをはいてロールバック
      $pdo->rollBack();
      $message = sprintf('登録に失敗しました。もう一度登録してください', $e->getMessage());
      $smarty->assign('flg', 1);
      $smarty->assign('userMail', $mail);
    }
  } //else {
//     header('location:'.LOGIN_PAGE);
//   }
  // テンプレートの表示
  if (!$dataFlg) {
    $smarty->assign('flg', 2);
  }
  $smarty->display("regUserInfoResult.tpl");
  db_close($pdo);
} else {
  header('location:'.LOGIN_PAGE);
}

?>