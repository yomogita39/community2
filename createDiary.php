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
$userName = $_SESSION['user']['username'];
$userImage = $_SESSION['user']['imagepath'];

$create = escape($_POST['create']);
$trueCreate = 'trueCreate';
// メッセージの受け取り
require_once('./include/inFlg.php');
if ($create != '') {
  $title = escape($_POST['title']);
  $body = escape($_POST['message']);
  $pState = escape($_POST['publicState']);
  $byteSize = 2;
  $messageByte = '最大'.$byteSize.'Mバイトまでです。';
  $messageExt = '対応ファイルはjpg, png, gifです。';
  $style = 'style="display:none"';
  $i = 0;
  // uploadエラーがないか確認する
  $checkFlg = true;
  while (count($_FILES['file']['tmp_name']) > $i) {
    if ( is_uploaded_file( $_FILES['file']['tmp_name'][$i] ) === TRUE ) {
      $file = $_FILES['file'];
      if ($file['size'][$i] > ($byteSize*1024*1024)) {
        $cautionMessage[$i] = $messageByte;
        $checkFlg = false;
      } else {
        $fileExt[$i] = checkImage($file['name'][$i], true);
        if (!$fileExt[$i]) {
          $cautionMessage[$i] = $messageExt;
          $checkFlg = false;
        } else {
          $fileUp[$i] = true;
        }
      }
    }
    $i++;
  }

  if ($title == '' || $body == '' || !$checkFlg) {
    if ($create != $trueCreate) {
      $smarty->assign('cTitle', $title);
      $smarty->assign('cBody', $body);
      $i = 0;
      while (count($cautionMessage) > $i) {
        $smarty->assign('caution'.($i+1).'Message', $cautionMessage[$i]);
        $i++;
      }
    } else {
      exit();
    }

  } else {
    $now = date(YmdHis);
    $imagePath = './diary_image/'.$now.$userId;
    $i = 0;
    $uploadFlg = true;
    while (count($fileUp) > $i) {
      if ($fileUp[$i]) {
        if ($uploadFlg) {
          $filePath[$i] = $imagePath.'d.'.($i+1).$fileExt[$i];
          $uploadFlg = move_uploaded_file($file['tmp_name'][$i], $filePath[$i]);
        }
      }
      $i++;
    }

    if (!$uploadFlg) {
      $smarty->assign('caution1', 'ファイルの送信に失敗しました。');
      $smarty->display("createDiary.tpl");
      exit();
    } else {
      if ($create == $trueCreate) {
        $i = 0;
        // 最大画像数
        $max = 3;
        while ($max > $i) {
          $fileName = escape($_POST['postPhotoName'][$i]);
          if ($fileName != '') {
            $filePath[$i] = $fileName;
          } else {
            $filePath[$i] = NULL;
          }
          $i++;
        }
      }
      try {
        // トランザクション開始
        $con->beginTransaction();
        // sql文の発行
        $sql = 'INSERT INTO
        diaryMstTbl (userId, title, message, publicState,
        photoPath1, photoPath2, photoPath3, createTime)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?)
        ';
        $stmt = $con->prepare($sql);
        $dbFlg = $stmt->execute(array($userId, $title, nl2br($body), $pState,
             $filePath[0], $filePath[1], $filePath[2], $now));
        $stmt->closeCursor();

        $error = '失敗しました';
        if ($dbFlg) {
          $con->commit();
          $res['flg'] = 'OK';
        } else {
          $con->rollBack();
          $res['flg'] = 'NG';
          $res['message'] = $error;
        }
      } catch (Exception $e) {
        $res['flg'] = 'NG';
        $res['message'] = $error;
        $con->rollBack();
      }
      if ($create == $trueCreate) {
        echo json_encode($res);
        exit();
      } else {
        header('location:'.MYDIARYLIST);
      }
    }

  }
}
$smarty->assign('userName', $userName);
$smarty->assign('userImage', $userImage);
$smarty->display('createDiary.tpl');


?>