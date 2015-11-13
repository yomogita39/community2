<?PHP
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');

session_start();
session_regenerate_id(TRUE);
$smarty = new Smarty();

//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir  = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
//print_r($_POST);
if (escape($_POST['proc']) != 'login') {
  session_clear();
  //inputView();
  $smarty->display("login.tpl");
} else {
  $userId = escape($_POST['userId']);
  $pass = escape($_POST['password']);
  if(isset($userId) && strlen($userId)>0
      && isset($pass) && strlen($pass)>0 ){
    //echo '<hr>';
    $ret = auth($userId, $pass);
    if (!$ret['loopFlg']) {
      $reg['flg'] = 'NG';
      $reg['message'] = 'メールアドレスとパスワードが一致しません。';
    } else if ($ret['passFlg']) {
      $reg['flg'] = 'NG';
      $reg['message'] = 'メールアドレスとパスワードが一致しません。';
    } else if ($ret['compFlg']) {
      $reg['flg'] = 'NG';
      $reg['message'] = '登録が完了していません。管理者からの承認後に送信されるメール記載のURLにアクセスし、登録を完了してください';
    }
  } else {
    $reg['flg'] = 'NG';
    $reg['message'] = '入力内容に不備があります。お確かめください。';
  }
  echo json_encode($reg);
  exit();
}


exit;

function auth($uid, $pwd)
{
//  echo hash('SHA256', 'aaa');
//  echo hash('SHA256', 'bbb');
//  echo hash('SHA256', 'ccc');
  //phpinfo();

  //$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
  try {
    // PDOオブジェクトの生成
    $con = new PDO("mysql:dbname=agridb;host=localhost", 'root', '');
  } catch(PDOException $e) {
    die('err:'.$e->getMessage());
  }
  // データベース内の書式設定をUTF8に
  $con->query('SET NAMES utf8');
  $sql = "
    SELECT
      userId AS userid
      , pwd AS password
      , phoneAddress AS phone
      , userName AS username
      , userAuthority AS level
      , regCompFlg as flg
      , lastLoginTime AS last
    FROM
      userMstTbl
    WHERE
      mailAddress = :userId
  ";

  $stmt = $con->prepare($sql);
  $stmt->bindValue(':userId', $uid, PDO::PARAM_INT);
  $stmt->execute();
  $ret['loopFlg'] = false;
  $ret['passFlg'] = false;
  $ret['compFlg'] = false;
  while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    $ret['loopFlg'] = true;
    if($data['password']===hash('SHA256', $pwd)){
      if (intval($data['flg']) === 1) {
        try {
          // トランザクション開始
          $con->beginTransaction();
          // sql文の発行
          $sql = 'UPDATE
          userMstTbl
          SET
          lastLoginTime=NOW() WHERE  userId=?';
          $stmt = $con->prepare($sql);
          $upFlg = $stmt->execute(array($data['userid']));
          $stmt->closeCursor();
          if ($upFlg) {
            $con->commit();
            $_SESSION['user']['login']=1;
            $_SESSION['user']['userid']=$data['userid'];
            $_SESSION['user']['phoneAddress']=$data['phone'];
            $_SESSION['user']['username']=$data['username'];
            $_SESSION['user']['mailAddress']=$uid;
            $_SESSION['user']['auth'] = $data['level'];
            $_SESSION['user']['last']=$data['last'];
            $ret['flg'] = 'OK';
            echo json_encode($ret);
            exit();
//             header('location:'. HOME_PAGE);
//             exit();
          } else {
            $con->rollBack();
            $ret['flg'] = 'NG';
            $ret['message'] = 'ログインに失敗しました。';
            echo json_encode($ret);
            exit();
          }
        } catch (Exeption $e) {
          $con->rollBack();
          $ret['flg'] = 'NG';
          $ret['message'] = 'ログインに失敗しました。';
          echo json_encode($ret);
          exit();
        }
      } else {
        $ret['compFlg'] = true;
      }
    } else {
      $ret['passFlg'] = true;
    }
  }

  db_close($con);
  return $ret;
}