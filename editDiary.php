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
$diaryId = escape($_POST['diaryId']);
$proc = escape($_POST['proc']);
require_once('./include/inFlg.php');

$photo1 = $_SESSION['diary']['photo1'];
$photo2 = $_SESSION['diary']['photo2'];
$photo3 = $_SESSION['diary']['photo3'];
$date = $_SESSION['diary']['year'];
$year = substr($date, 0, 4);
$month = substr($date, 5, 2);
$calendar = calendar(EDITDIARY, $userId, $con, $year, $month, $diaryId);

$smarty->assign('userName', $userName);
$smarty->assign('userImage', $userImage);
$smarty->assign('calender', $calendar);
$smarty->assign('photo1', $photo1);
$smarty->assign('photo2', $photo2);
$smarty->assign('photo3', $photo3);
if ($proc == 'edit') {

  $message = $_SESSION['diary']['message'];
  $title = $_SESSION['diary']['title'];
  $state = $_SESSION['diary']['state'];

  $smarty->assign('diaryId', $diaryId);
  $smarty->assign('date', $date);
  $smarty->assign('message', str_replace(array('<br />'), '', $message));
  $smarty->assign('title', $title);
  $smarty->assign('state', $state);

  $smarty->display("editDiary.tpl");
  exit();
}
$message = escape($_POST['message']);
$title = escape($_POST['title']);
$state = escape($_POST['publicState']);
if ($proc == 'regEdit' && $message && $title && $state >= 0 && 2 >= $state) {
//   $photo1 = escape($_POST['photo1']);
//   $photo2 = escape($_POST['photo2']);
//   $photo3 = escape($_POST['photo3']);

  $delete1 = escape($_POST['delete1']);
  $delete2 = escape($_POST['delete2']);
  $delete3 = escape($_POST['delete3']);
  $byteSize = 6000000;
  $messageByte = '最大'.$byteSize.'バイトまでです。';
  $messageExt = '対応ファイルはjpg, png, gifです。';
  $photo1Flg = true;
  if ( is_uploaded_file( $_FILES['photo1']['tmp_name'] ) === TRUE ) {
    $photo1Flg = false;
    $photo1 = $_FILES['photo1'];
    if ($photo1['size'] > $byteSize) {
      $caution1Message = $messageByte;
    } else {
      $photo1Flg = checkImage($photo1);
      if (!$photo1Flg) {
        $caution1Message = $messageExt;
      } else {
        $photo1Ext = $photo1Flg;
        $photo1Up = true;
      }
    }
  }
  $photo2Flg = true;
  if ( is_uploaded_file( $_FILES['photo2']['tmp_name'] ) === TRUE ) {
    $photo2Flg = false;
    $photo2 = $_FILES['photo2'];
    if ($photo2['size'] > $byteSize) {
      $caution2Message = $messageByte;
    } else {
      $photo2Flg = checkImage($photo2);
      if (!$photo2Flg) {
        $caution2Message = $messageExt;
      } else {
        $photo2Ext = $photo2Flg;
        $photo2Up = true;
      }
    }
  }
  $photo3Flg = true;
  if ( is_uploaded_file( $_FILES['photo3']['tmp_name'] ) === TRUE ) {
    $photo3Flg = false;
    $photo3 = $_FILES['photo3'];
    if ($photo3['size'] > $byteSize) {
      $caution3Message = $messageByte;
    } else {
      $photo3Flg = checkImage($photo3);
      if (!$photo3Flg) {
        $caution3Message = $messageExt;
      } else {
        $photo3Ext = $photo3Flg;
        $photo3Up = true;
      }
    }
  }
  if (!$photo1Flg || !$photo2Flg || !$photo3Flg) {
    $smarty->assign('message'. $message);
    $smarty->assign('title', $title);
    $smarty->assign('state', $state);
    $smarty->assign('caution1Message', $caution1Message);
    $smarty->assign('caution2Message', $caution2Message);
    $smarty->assign('caution3Message', $caution3Message);
    $smarty->display("editDiary.tpl");
    exit();
  } else {
    $now = date(YmdHis);
    $imagePath = './diary_image/'.$now.$userId;
    if ($photo1Up) {
      $photo1Path = $imagePath.'d1.'.$photo1Ext;
      $flg1 = move_uploaded_file($photo1['tmp_name'], $photo1Path);
    }
    if ($photo2Up) {
      $photo2Path = $imagePath.'d2.'.$photo2Ext;
      $flg2 = move_uploaded_file($photo2['tmp_name'], $photo2Path);
    }
    if ($photo3Up) {
      $photo3Path = $imagePath.'d3.'.$photo3Ext;
      $flg3 = move_uploaded_file($photo3['tmp_name'], $photo3Path);
    }
    if ($flg1 || $delete1) {
      $photoSql1 = ', photoPath1="'.$photo1Path.'"';
      $delFlg1 = true;
    }
    if ($flg2 || $delete2) {
      $photoSql2 = ', photoPath2="'.$photo2Path.'"';
      $delFlg2 = true;
    }
    if ($flg3 || $delete3) {
      $photoSql3 = ', photoPath3="'.$photo3Path.'"';
      $delFlg3 = true;
    }
    try {
      // トランザクション開始
      $con->beginTransaction();
      // sql文の発行
      $sql = 'UPDATE
      diaryMstTbl
      SET
      title=?, message=?, publicState=? '.$photoSql1.$photoSql2.
      $photoSql3.' WHERE diaryId=? AND userId=?';
      $stmt = $con->prepare($sql);
      $upFlg = $stmt->execute(array($title, nl2br($message), $state,
          $diaryId, $userId));
      $stmt->closeCursor();
      if ($upFlg) {
        $con->commit();
        if ($delFlg1) {
          if (file_exists($_SESSION['diary']['photo1']) === TRUE) {
            unlink($_SESSION['diary']['photo1']);
          }
        }
        if ($delFlg2) {
          if (file_exists($_SESSION['diary']['photo2']) === TRUE) {
            unlink($_SESSION['diary']['photo2']);
          }
        }
        if ($delFlg3) {
          if (file_exists($_SESSION['diary']['photo3']) === TRUE) {
            unlink($_SESSION['diary']['photo3']);
          }
        }
        // 使用した情報の破棄
        $_SESSION['diary']['year'] = null;
        $_SESSION['diary']['message'] = null;
        $_SESSION['diary']['title'] = null;
        $_SESSION['diary']['state'] = null;
        $_SESSION['diary']['photo1'] = null;
        $_SESSION['diary']['photo2'] = null;
        $_SESSION['diary']['photo3'] = null;
        header('location:'.MYDIARY.'?diaryId='.$diaryId);
        exit();
      } else {
        $con->rollBack();
      }

    } catch (Exception $e) {
      $con->rollBack();
    }
  }
  $smarty->assign('message'. $message);
  $smarty->assign('title', $title);
  $smarty->assign('state', $state);
  $smarty->assign('caution1Message', '編集に失敗しました。もう一度やり直してください。');
  $smarty->display("editDiary.tpl");
  exit();
} else {
  $smarty->assign('message'. $message);
  $smarty->assign('title', $title);
  $smarty->assign('state', $state);
  $smarty->assign('cautionMessage', '入力内容に誤りがあります。');
  $smarty->display("editDiary.tpl");
  exit();
}
?>