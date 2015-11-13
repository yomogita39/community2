<?php
$postPhotoName = escape($_POST["postPhotoName"]);
$proc = escape($_POST['proc']);
if ($postPhotoName){
  if ($proc == 'profile') {
    $_SESSION['profile']['image'] = null;
    $_SESSION['profile']['imagePath'] = null;
    if (file_exists("./user_image/".$postPhotoName) === TRUE) {
      unlink("./user_image/".$postPhotoName);
    }
    if (file_exists("./user_image/thumb-".$postPhotoName) === TRUE) {
      unlink("./user_image/thumb-".$postPhotoName);
    }
  } else {
    $_SESSION['message']['image'] = null;
    $_SESSION['message']['imagePath'] = null;
    if (file_exists("./message_image/".$postPhotoName) === TRUE) {
      unlink("./message_image/".$postPhotoName);
    }
    if (file_exists("./message_image/thumb-".$postPhotoName) === TRUE) {
      unlink("./message_image/thumb-".$postPhotoName);
    }
  }
}