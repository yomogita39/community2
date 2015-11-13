<?php
require_once('../include/line_define.php');
require_once('../include/common.php');
require_once('./model_thread.php');

session_start();

$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);

$firstid = escape($_POST['firstid']);
$lastid = escape($_POST['lastid']);
$msg = escape($_POST['msg']);
if (array_key_exists('gid', $_POST)) {
  $oid = escape($_POST['gid']);
  $oFlg = 'group';
  if (intval($_SESSION['user']['gid'])!=intVal($oid)) {
    // セッション不正
    $dat['res'] = 'NG';
    $dat['inf'] = '001';
    echo json_encode($dat);
    exit();
  }
} elseif (array_key_exists('friendId', $_POST)){
  $oid = escape($_POST['friendId']);
  $oFlg = 'friend';
  if (intval($_SESSION['user']['friendId'])!=intVal($oid)) {
    // セッション不正
    $dat['res'] = 'NG';
    $dat['inf'] = '001';
    echo json_encode($dat);
    exit();
  }
}

$userid = $_SESSION['user']['userid'];
$proc = escape($_POST['proc']);
$path = escape($_POST['postPhotoName']);
// $path = renamePath ($postPhotoName, $proc, $userid);
if ($msg == "メッセージを入力してください") {
  $msg = '';
}
//print_r($_SESSION);exit();

//echo "$gid , $_SESSION['user']['gid']";
if( $oid=='' || $userid=='' ){
  // セッション不正
  $dat['res'] = 'NG';
  $dat['inf'] = '001';
  echo json_encode($dat);
  exit();
}

if( $proc=='' ){
  // パラメータ不正
  $dat['res'] = 'NG';
  $dat['inf'] = '002';
  echo json_encode($dat);
  exit();
}

if( $proc=='add' || $proc=='upd' ){
  // コメント追加または更新
  if(($proc=='add' && $msg!='') || ($proc=='add' && $path!=null)){
    //コメント登録
    if( model_thread::addComment($con, $userid, $oid, $msg, $path, $oFlg) !== TRUE ){
      // 登録エラーの場合
      exit();
    }
  }

  //追加コメントの取得
  $ret = model_thread::getThreadComment($con, $oid, $lastid, $oFlg, $userid);
  //全件取得するので、直近のlimit件のみ表示する
  $limit=5;
  $ret = ar_slice($ret, $limit, 1);

  $ret = model_thread::createHtml($ret, $userid, $oFlg);
  $ret['res'] = $proc;
  echo json_encode($ret);

} else if( $proc=='his' ){
  // 履歴取得
  if($firstid==''){
    // パラメータ不正
    $dat['res'] = 'NG';
    $dat['inf'] = '003';
    echo json_encode($dat);
    exit();
  }
  //履歴コメントの取得
  $ret = model_thread::getThreadCommentHistory($con, $gid, $firstid);

  //全件取得するので、直近のlimit件のみ表示する
  $limit=5;
  $ret = ar_slice($ret, $limit, 0);

  $ret = model_thread::createHtml($ret, $userid);
  $ret['res'] = $proc;
  echo json_encode($ret);

} else if( $proc=='del' ){
  // コメント削除
  $cid = escape($_POST['cid']);
  $dat['res'] = $proc;
  $dat['cid'] = $cid;
  $dat['count'] = model_thread::delComment($con, $userid, $gid, $cid);
  echo json_encode($dat);

} else {
  exit();
}

db_close($con);
exit();

?>