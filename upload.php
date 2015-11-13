<?php


require_once('./include/line_define.php');
require_once('./include/common.php');
require_once('./api/model_thread.php');
require_once('./api/class.image.php');
session_start();
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);


$postPhotoName = escape($_POST["postPhotoName"]);
$firstid = escape($_POST['firstid']);
$lastid = escape($_POST['lastid']);
$gid = escape($_POST['gid']);
$userid = $_SESSION['user']['userid'];
$proc = escape($_POST['proc']);
if($postPhotoName){
  if ($proc == 'profile') {
//     $_SESSION['profile']['image'] = null;
//     $_SESSION['profile']['imagePath'] = null;
    if (file_exists("./user_image/".$postPhotoName) === TRUE) {
      unlink("./user_image/".$postPhotoName);
    }
    if (file_exists("./user_image/thumb-".$postPhotoName) === TRUE) {
      unlink("./user_image/thumb-".$postPhotoName);
    }
    if (escape($_POST['del']) == 'del') {
      exit();
    }
  } else if ($proc == 'group') {
//     $_SESSION['profile']['image'] = null;
//     $_SESSION['profile']['imagePath'] = null;
    if (file_exists("./group_image/".$postPhotoName) === TRUE) {
      unlink("./group_image/".$postPhotoName);
    }
    if (file_exists("./group_image/thumb-".$postPhotoName) === TRUE) {
      unlink("./group_image/thumb-".$postPhotoName);
    }
    if (escape($_POST['del']) == 'del') {
      exit();
    }
  } else {
//     $_SESSION['message']['image'] = null;
//     $_SESSION['message']['imagePath'] = null;
    if (file_exists("./message_image/".$postPhotoName) === TRUE) {
      unlink("./message_image/".$postPhotoName);
    }
    if (file_exists("./message_image/thumb-".$postPhotoName) === TRUE) {
      unlink("./message_image/thumb-".$postPhotoName);
    }
  }
}
if ( is_uploaded_file( $_FILES['file']['tmp_name'] ) === TRUE ) {
  $pathJudge = checkImage($_FILES['file']);

  if ($pathJudge) {
    $now = date(YmdHis);
    $imageName = $now.$userid.'.'.$pathJudge;
    if ($proc == 'profile') {
      $imagePath = './user_image/'.$imageName;
    } else if ($proc == 'group') {
      $imagePath = './group_image/'.$imageName;
    } else {
      $imagePath = './message_image/'.$imageName;
    }
    $flg = move_uploaded_file($_FILES['file']['tmp_name'], $imagePath);
//     $_SESSION['message']['imagePath'] = $imagePath;
//     $_SESSION['message']['image'] = $imageName;
  } else {
    die("投稿できる画像はjpg, png, gifです。");
  }
} else {
//   $_SESSION['message']['image'] = null;
//   $_SESSION['message']['imagePath'] = null;
//   die("ファイルが選択されていません");
}
$result = false;
if ($flg) {
  list($width, $height, $type, $attr) = getimagesize($imagePath);
  if ($proc == 'profile' || $proc == 'group') {
    $length = 150;
  } else {
    $length = 250;
  }
  $thumb = new Image($imagePath);
  $thumb->name('thumb-'.basename($imageName));

  if($width>$height){
    if($width > $length) $thumb->width($length);
  }else{
    if($height > $length) $thumb->height($length);
  }
  $thumb->save();
  $result = true;
} else {
//   unlink($_SESSION['message']['imagePath']);
//   unlink('./message_image/thumb-'.$_SESSION['message']['image']);
//   $_SESSION['message']['image'] = null;
//   $_SESSION['message']['imagePath'] = null;

  die('サムネイルの作成に失敗しました。'.$imagePath);
}
db_close($con);
if($result == true){
//   $html = '<input type="hidden" value="'.$imageName.'" name="postPhotoName" id="postPhotoName">';
//   echo '<input';
//   $html['html: '] =  ' type="hidden" value="'.$imageName.'" name="postPhotoName" id="postPhotoName">';
// exit();
  header('Content-Type: text/html; charset=utf-8');
// $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
  echo '画像チェックOK';
  echo '<input type="hidden" value="'.$imageName.'" name="postPhotoName" id="postPhotoName">';
  exit();
}
?>