<?PHP
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');
$smarty = new Smarty();
session_start();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
// セッション情報の取得
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$regUserId = $userId;//登録者
print "登録者 $userName <br />";
print "regUserId $regUserId <br />";
$regTime = date(YmdHis);
print "regTime $regTime <br />";
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// メッセージの受け取り
require_once('./include/inFlg.php');

//insert文・新規投稿ここから
$message = escape($_POST['insertMessage']);
print "message $message <br />";

//投稿時間 現在時間を求める
$regTime = date(YmdHis);

try {
    // トランザクションの開始
    $con->beginTransaction();
//テーブルにデータを挿入するSQL文を記述
//インフォメーションID'infoId'[これのみオートカラム、挿入不要]　登録者'regUserId'　メッセージ'message'　投稿時間'regTime'
        $sql = 'INSERT INTO
        infoMstTbl (regUserId, message, regTime)
        VALUES (?, ?, ?)';
        $stmt = $con->prepare($sql);//prepare　ステートメントオブジェクトを 返します。エラー時には FALSE を返します。
        $dbFlg = $stmt->execute(array($regUserId, $message, $regTime));
//ブラウザに<br />が表示されたので nl2br()関数を省いてみる。必要ないなら使用しない。
//        $dbFlg = $stmt->execute(array($regUserId, nl2br($message), $regTime));
        $stmt->closeCursor();//カーソルを閉じてステートメントを再実行できるようにする
print " dbFlg【0】でcommit、【1】でrollBack　結果＝ $dbFlg <br />";
	if ($message != ""){
		if ($dbFlg) {
		$con->commit();
		} else {
		$con->rollBack();
		}
	}else {
	$con->rollBack();
print "文字列が空<br />";	
	}

  // エラーがあった場合も処理を巻き戻す
	} catch (PDOException $e) {
    $con->rollBack();
    die('err:'.$e->getMessage());
	  echo 'catch 巻き戻し <br />';
  }
//insert文・新規投稿ここまで*/

print "while文の繰り返し回数【i】の値は $i <br />";





$data = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();
// DBのクローズ
db_close($con);
$smarty->assign('html', $html);


//infoUpdate内容変更ここから
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// メッセージの受け取り
require_once('./include/inFlg.php');

try {
//ラジオボタンで選択された行番号 = infoId
$radioUpdate = $_POST['radioUpdate'];
//行番号+text結合
$numberMessage = $radioUpdate;
$numberMessage .= 'updateMessage';
//infoIdと文字列updateMessageを掛け合わせると infoTest.tplのtextarea指定が出来る
//×$numUpdateMessage = escape($_POST['$numberMessage']);
$numUpdateMessage = escape($_POST[$numberMessage]);
print "numUpdateMessage $numUpdateMessage <br />";
print "radioUpdate $radioUpdate <br />";
print "numberMessage $numberMessage <br />";
    // トランザクションの開始
    $con->beginTransaction();
	//INFO内容変更 ラジオボタンの押されている行のメッセージに置き換える　'取り外し中'
	$sql = "UPDATE 
	infoMstTbl 
	SET
	 message = '$numUpdateMessage' 
	WHERE
	 infoId = '$radioUpdate'";

	$stmt = $con->prepare($sql);//prepare　ステートメントオブジェクトを 返します。エラー時には FALSE を返します。
	$stmt->execute();//SQL実行
	$dbFlg = $stmt->execute();
	$stmt->closeCursor();
	    if ($dbFlg) {
	      $con->commit();
print "コミット";
	    } else {
	      $con->rollBack();
print "巻き戻し";
	    }
  // エラーがあった場合も処理を巻き戻す
	} catch (PDOException $e) {
    $con->rollBack();
    die('err:'.$e->getMessage());
	  echo 'catch 巻き戻し <br />';
  }
// DBのクローズ
db_close($con);

//infoUpdate内容変更ここまで
//削除ここから
// DB接続
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// メッセージの受け取り
require_once('./include/inFlg.php');
try {
$delTest = $_POST['radioDelete'];
print "$delTest<br />";
    // トランザクションの開始
    $con->beginTransaction();
	//削除
	$sql = "DELETE 
	FROM 
	 infoMstTbl 
	WHERE 
	 infoId = '$delTest'";
	$stmt = $con->prepare($sql);//prepare　ステートメントオブジェクトを 返します。エラー時には FALSE を返します。
	$stmt->execute();//SQL実行
	$dbFlg = $stmt->execute();
	$stmt->closeCursor();
	    if ($dbFlg) {
	      $con->commit();
	    } else {
	      $con->rollBack();
	    }
  // エラーがあった場合も処理を巻き戻す
	} catch (PDOException $e) {
    $con->rollBack();
    die('err:'.$e->getMessage());
	  echo 'catch 巻き戻し <br />';
  }
// DBのクローズ
db_close($con);
//削除ここまで

//画面表示作業ここから
 //インフォメーションテーブルから表示するデータを持ってくる
$sql = "SELECT
 infoId, regUserId, message, regTime
FROM
 infoMstTbl";

$stmt = $con->prepare($sql);
$stmt->execute();

// 実行結果の受け取り
//fetch(PDO::FETCH_ASSOC)で、データの数だけ繰り返す
  $i = 1;
//while文でinfoMstTblの行数文だけ繰り返し処理
//infoTest.tplで【{foreach from=$html item=var name=infoTest}、{$var.infoId}～】を使用して値を受け渡す
  while($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$html[$i]['infoId'] = $data['infoId'];
	$html[$i]['regUserId'] = $data['regUserId'];
	$html[$i]['message'] = escape($data['message']);
	$html[$i]['regTime'] = $data['regTime'];
	$i++;
  }
//画面表示作業ここまで
/*
//infoId, regUserId, message, regTime
$smarty->assign('infoId', $infoId);
$smarty->assign('regUserId', $regUserId);
$smarty->assign('message', $message);
$smarty->assign('regTime', $regTime);
*/

 //テンプレートの表示
$smarty->assign('html', $html);
$smarty->display("infoTest.tpl");
exit();
?>