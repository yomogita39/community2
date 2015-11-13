<?php
// アップロードされたファイルを
require_once('./include/line_define.php');
require_once('./include/common.php');
session_start();
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);


$firstid = escape($_POST['firstid']);
$lastid = escape($_POST['lastid']);
$gid = escape($_POST['gid']);
$userid = $_SESSION['user']['userid'];
// 呼び出し元を判定する
$proc = escape($_POST['proc']);
// すでにアップロードされたファイルがあれば削除する
if(isset($_POST["postPhotoName"])){
  $tempPhotoName = $_POST["postPhotoName"];
  if (is_array($tempPhotoName)) {
    foreach ($tempPhotoName as $var) {
      $postPhotoName = escape($var);
      if (file_exists($postPhotoName) === TRUE) {
        unlink($postPhotoName);
      }
    }
  } else {
    $postPhotoName = escape($_POST["postPhotoName"]);
    if (file_exists($postPhotoName) === TRUE) {
      unlink($postPhotoName);
    }
    if (escape($_POST['del']) == 'del') {
      exit();
    }
  }
}
// ファイルが送信されてきた場合
if ( is_uploaded_file( $_FILES['file']['tmp_name'] ) === TRUE ) {
  // 拡張子を確認する 対応しているのはjpg, png, gif
  $pathJudge = checkImage($_FILES['file']);

  if ($pathJudge) {
    $now = date(YmdHis);
    // 保存するファイル名を生成
    if (isset($_POST['diaryCount'])) {
      $count = 'd'.escape($_POST['diaryCount']);
    }
    $imageName = $now.$userid.$count.'.'.$pathJudge;
    // pathを設定
    $imagePath = './'.$proc.'_image/'.$imageName;
    // アップロードを実行
    $flg = move_uploaded_file($_FILES['file']['tmp_name'], $imagePath);
//     $_SESSION['message']['imagePath'] = $imagePath;
//     $_SESSION['message']['image'] = $imageName;
  } else {
    die("投稿できる画像はjpg, png, gifです。");
  }
}
db_close($con);
if ($flg) {
  if ($proc != 'diary') {
    echo '&nbsp<input type="hidden" value="'.$imagePath.'" name="postPhotoName" id="postPhotoName">';
  } else {
    echo '<input type="hidden" value="'.$imagePath.'" name="postPhotoName[]" id="postPhotoName">';
  }

  exit();
}

?>