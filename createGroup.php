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
session_start();
$userId = $_SESSION['user']['userid'];
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
// メッセージの受け取り
require_once('./include/inFlg.php');
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $smarty->display("createGroup.tpl");
    exit();
} else {


    // 入力された値の取得
    $groupName = htmlspecialchars($_POST['groupName'], ENT_QUOTES);
    $files = $_FILES['groupImage'];
    $filesName = $files['name'];
    $postSize = $_SERVER["CONTENT_LENGTH"];
    // アップロード制限2MB
    $maxsize = ((1024 * 1024) * 2);
    // groupNameに受け取った値の文字数
    if (strlen($groupName) != 0) {
        $groupNameFlg = true;
    } else {
        $groupNameFlg = false;
    }
    if ($postSize > $maxsize) {
      $sizeFlg = false;
    } else {
      $sizeFlg = true;
    }
    // イメージパスに入力された文字列がある場合
    if (strlen($filesName) > 3) {
        // イメージファイルの名前の後ろから3文字を取得(拡張子)
        $checkExtension = substr($filesName, -3);
        if ($checkExtension == 'gif' ||
            $checkExtension == 'jpg' ||
            $checkExtension == 'png' ||
            $checkExtension == 'GIF' ||
            $checkExtension == 'JPG' ||
            $checkExtension == 'PNG') {
            $pathJudge = true;
//             $defaultImageFlg = false;
            $filePath = './group_image/group'.date(YmdHis).$userId.'.'.$checkExtension;
        } else {
            $pathJudge = false;
        }
    } else {
        if (strlen($filesName) == 0) {
            $pathJudge = true;
//             $defaultImageFlg = true;
            $filePath = './group_image/defaultGroupImage.png';
        } else {
            $pathJudge = false;
        }
    }
    if ($groupNameFlg && $pathJudge && $sizeFlg) {
        move_uploaded_file($files['tmp_name'], $filePath);
        $alertNumber = groupInsert($con, $userId, $filePath, $groupName);
    } else {
        if (!$groupNameFlg) {
            $alertNumber = 1;
        } else if (!$groupNameFlg){
            $alertNumber = 2;
        } else {
          $alertNumber = 4;
        }
    }
    // テンプレートの表示
    $smarty->display("createGroup.tpl");
    alert($alertNumber);
    db_close($con);
    exit();
}
/**
 * dbへのインサート結果を返す
 * @param pdo $con
 * @param String $userId
 * @param String $filePath
 * @param String $groupName
 * @return int alertNumber
 */
function groupInsert($pdo, $userId, $filePath, $groupName) {
    try {
        // トランザクション開始
        $pdo->beginTransaction();
        $sql = 'insert into groupMstTbl (groupName, regGroupImagePath)
               values (?, ?)';
        $stmt = $pdo->prepare($sql);
        // sql文の実行
        $flg = $stmt->execute(array($groupName, $filePath));
        // $stmtのクローズ
        $stmt->closeCursor();
        $now = date(YmdHis);
        if ($flg) {
            // 成功の場合コミット
//             $pdo->commit();
            // insertしたオートインクリメントされるgroupIdの取得
            $sql = 'SELECT
                      last_insert_id()
                    FROM
                      groupMstTbl
                    ';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_NUM);
            $groupId = $data[0];
            $stmt->closeCursor();
            $sql = 'insert into groupMemberTbl (groupId, userId, applyTime, applyUser, acceptFlg, acceptTime)
                  values (?, ?, ?, ?, ?, ?)';
            $stmt = $pdo->prepare($sql);
            $flg = $stmt->execute(array($groupId, $userId, $now, $userId, 1, $now));
            $stmt->closeCursor();
            if ($flg) {
                $pdo->commit();
                return 0;
            } else {
                $pdo->rollBack();
                return 3;
            }
        } else {
            // insert失敗時
            $pdo->rollBack();
            return 3;
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        return 3;
    }
    exit();
}
/**
 * numに対応したアラートを出す
 * @param int $num
 */
function alert($num) {
    switch ($num) {
        case 0:
            echo "<script type=\"text/javascript\" >";
            echo "alert(\"登録完了しました\");";
            echo "</script>";
            break;
        case 1:
            echo "<script type=\"text/javascript\" >";
            echo "alert(\"グループ名を入力してください\");";
            echo "</script>";
            break;
        case 2:
            echo "<script type=\"text/javascript\" >";
            echo "alert(\"使用できる画像はjpg, png, gifです\");";
            echo "</script>";
            break;
        case 3:
            echo "<script type=\"text/javascript\" >";
            echo "alert(\"登録に失敗しました。再度登録してください\");";
            echo "</script>";
            break;
        case 4:
            echo "<script type=\"text/javascript\" >";
            echo "alert(\"ファイルサイズが大きすぎます。\");";
            echo "</script>";
            break;
    }
}
?>