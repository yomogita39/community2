<?php
// ユーザ情報の管理者専用設定画面
require_once("Smarty/Smarty.class.php");
require_once('./include/line_define.php');
require_once('./include/common.php');

session_start();
$con = db_connect(DB_URL, DB_UID, DB_PWD, DB_NAME);
$smarty = new Smarty();
//ディレクトリ設定
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir   = "./configs";
$smarty->cache_dir    = "./cache";
// 管理者権限
$auth = $_SESSION['user']['auth'];
$userId = $_SESSION['user']['userid'];
$userName = $_SESSION['user']['username'];
$proc = escape($_POST['proc']);
// ポスト受け取り
require_once('./include/inFlg.php');
if ($proc == 'editUser') {
  // 強制変更パスワード
  $cPass = escape($_POST['pass']);
  // 管理者権限
  $cAuth = intval(escape($_POST['auth']));
  // 変更されるユーザID
  $cUser = escape($_POST['userId']);
  $div[1] = mb_convert_kana(escape($_POST['divisionNo1']), n);
  $div[2] = mb_convert_kana(escape($_POST['divisionNo2']), n);
  $div[3] = mb_convert_kana(escape($_POST['divisionNo3']), n);
  $div[4] = mb_convert_kana(escape($_POST['divisionNo4']), n);
  $divFlg = true;
  for ($i = 1; count($div) >= $i; $i++) {
    if (preg_match("/^[0-9]+$/", $div[$i])) {
      $checkStr .= $div[$i];
      if ($checkStr == '') {
        if (count($div) == $i) {
          $divFlg = true;
          $unset($div);
        } else {
          $divFlg = false;
        }
      }
    } else {
      $divFlg = false;
      break;
    }
  }
  if (!$divFlg) {
    $dat['flg'] = 'NG';
    $dat['message'] = '区画番号は半角数字のみでご入力ください';
    echo json_encode($dat);
    exit();
  }
  if ((preg_match("/^[a-zA-Z0-9]+$/", $cPass) && strlen($cPass) >= 6 && $cAuth < 2 && $cAuth >= 0)
       || ($cPass == '' && $cAuth < 2 && $cAuth >= 0) && $divFlg) {
    try {
      $con->beginTransaction();
      $i = 1;
      $j = 3;
      $dbDiv = '';
      while (count($div) >= $i) {
        while (strlen($div[$i]) < $j) {
          $div[$i] = '0'.$div[$i];
        }
        $dbDiv .= $div[$i];
        if (count($div) != $i) {
          $dbDiv .= '-';
        }
        $i++;
      }
      if ($dbDiv != '') {
        $divSql = ', divisionNo = "'.$dbDiv.'"';
      } else {
        $divSql = ', divisionNo = NULL';
      }
      if ($cPass == '') {
        $sql = 'UPDATE userMstTbl SET userAuthority = ?'.$divSql.'
        WHERE userID = ?';
        $stmt = $con->prepare($sql);
        $flg = $stmt->execute(array($cAuth, $cUser));
      } else {
        $sql = 'UPDATE userMstTbl SET pwd = ?, userAuthority = ?'.$divSql.'
        WHERE userID = ?';
        $stmt = $con->prepare($sql);
        $flg = $stmt->execute(array(hash('SHA256', $cPass), $cAuth, $cUser));
      }
      if ($flg) {
        $con->commit();
        $html = userSummary($con, $cUser);
        $dat['html'] = scriptSummaryHtml($con, $html);
        $dat['id'] = $cUser;
        $dat['flg'] = 'OK';
      } else {
        $con->rollback();
        $dat['flg'] = 'NG';
      }
    } catch (Exception $e) {
      $con->rollback();
      $dat['flg'] = 'NG';
    }
  } else {
    $dat['flg'] = 'NG';
    $dat['message'] = '入力内容に不備があります。ご確認ください。';
  }
  echo json_encode($dat);
  exit();
}

if ($proc == 'searchUser') {
  $keyWord = escape($_POST['search']);
  if ($keyWord != '') {
    $html = userSummary($con, '', $keyWord);
    $userHtml = userSummaryHtml($con, $html);
    $dat['flg'] = 'OK';
    $dat['html'] = $userHtml;
  } else {
    $dat['flg'] = 'NG';
  }
  echo json_encode($dat);
  exit();
}

if ($proc == 'clear') {
  $html = userSummary($con);
  $userHtml = userSummaryHtml($con, $html);
  $dat['flg'] = 'OK';
  $dat['html'] = $userHtml;
  echo json_encode($dat);
  exit();
}

// セッションに保存された権限は正しいか
$adminFlg = administrator($con, $userId, $auth);
$html = userSummary($con);
// $userHtml = userSummaryHtml($con, $html);
if (!$adminFlg) {
  header('location:'.LOGOUT);
}

$smarty->assign('total', count($html));
$smarty->assign('html', $html);
$smarty->display('userAdmin.tpl');
function scriptSummaryHtml($con, $html) {
  $i = 1;
  $count = count($html);
  $userHtml = '';

  while ($count >= $i) {
    switch (intval($html[$i]['auth'])) {
      case 0: $option = '<option value="0" selected="selected">なし</option>
      <option value="1" >あり</option>';
      break;
      case 1:$option = '<option value="0" >なし</option>
      <option value="1" selected="selected">あり</option>';
      break;
    }
    $no = explode('-', $html[$i]['no']);
    //     if ($i == 1) {
//     $userHtml .= '<table class="table_selectResultList">
//     <tbody	id="">';
    //     }

    $userHtml .= '
    <tr>
        <td rowspan="6" class="td_adminUserListImage">
            <img src="'.$html[$i]['image'].'" class="image_m" alt="'.$html[$i]['name'].'画像">
        </td>
        <td class="td_adminUserListLabel">名前</td>
        <td class="td_adminUserListMessage">'.$html[$i]['name'].'</td>
        <td rowspan="5" class="center">
            <input type="button" value="変更" class="button_s textColorRed">
        </td>
    </tr>
    <tr>
        <td class="td_adminUserListLabel">ニックネーム</td>
        <td class="td_adminUserListMessage">'.$html[$i]['nickname'].'</td>
    </tr>
    <tr>
        <td class="td_adminUserListLabel">メールアドレス</td>
        <td class="td_adminUserListMessage">'.$html[$i]['mail'].'</td>
    </tr>
    <tr>
        <td class="td_adminUserListLabel">パスワード</td>
        <td class="td_adminUserListMessage">
            <input type="text" name="pass" class="input_adminUserListPass" maxlength="20">
            <input type="text" name="dummy" style="display:none;">
        </td>
    </tr>
    <tr>
        <td class="td_adminUserListLabel">区画番号</td>
        <td class="td_adminUserListMessage">
            <input type="text" name="divisionNo1" class="input_divisionNo" maxlength="3" value="'.$html[$i]['no1'].'">
            <span>&ndash;</span>
            <input type="text" name="divisionNo2" class="input_divisionNo" maxlength="3" value="'.$html[$i]['no2'].'">
            <span>&ndash;</span>
            <input type="text" name="divisionNo3" class="input_divisionNo" maxlength="3" value="'.$html[$i]['no3'].'">
            <span>&ndash;</span>
            <input type="text" name="divisionNo4" class="input_divisionNo" maxlength="3" value="'.$html[$i]['no4'].'">
        </td>
    </tr>
    <tr>
        <td class="td_adminUserListLabel">管理者権限</td>
        <td class="td_adminUserListMessage">
            <select name="auth" class="select_userAuth">
                '.$option.'
            </select>
        </td>
    </tr>
    ';

    //     if ($count == $i) {
//     $userHtml .= '</tbody></table>';
    //     }
    $i++;
  }
  return $userHtml;
}

function userSummaryHtml($con, $html) {
  $i = 1;
  $count = count($html);
  $userHtml = '';

  while ($count >= $i) {
    switch (intval($html[$i]['auth'])) {
      case 0: $option = '<option value="0" selected="selected">なし</option>
      <option value="1" >あり</option>';
      break;
      case 1:$option = '<option value="0" >なし</option>
      <option value="1" selected="selected">あり</option>';
      break;
    }
//     if ($i == 1) {
      $userHtml .= '
      <tr><td>
      <form>
          <input type="hidden" name="userId" value="'.$html[$i]['id'].'">
          <table class="table_adminUserList">
              <tbody id="'.$html[$i]['id'].'">
                  <tr>
                      <td rowspan="6" class="td_adminUserListImage">
                          <img src="'.$html[$i]['image'].'" class="image_m" alt="'.$html[$i]['name'].'画像">
                      </td>
                      <td class="td_adminUserListLabel">名前</td>
                      <td class="td_adminUserListMessage">'.$html[$i]['name'].'</td>
                      <td rowspan="5" class="center">
                          <input type="button" value="変更" class="button_s textColorRed">
                      </td>
                  </tr>
                  <tr>
                      <td class="td_adminUserListLabel">ニックネーム</td>
                      <td class="td_adminUserListMessage">'.$html[$i]['nickname'].'</td>
                  </tr>
                  <tr>
                      <td class="td_adminUserListLabel">メールアドレス</td>
                      <td class="td_adminUserListMessage">'.$html[$i]['mail'].'</td>
                  </tr>
                  <tr>
                      <td class="td_adminUserListLabel">パスワード</td>
                      <td class="td_adminUserListMessage">
                          <input type="text" name="pass" class="input_adminUserListPass" maxlength="20">
                          <input type="text" name="dummy" style="display:none;">
                      </td>
                  </tr>
                  <tr>
                      <td class="td_adminUserListLabel">区画番号</td>
                      <td class="td_adminUserListMessage">
                          <input type="text" name="divisionNo1" class="input_divisionNo" maxlength="3" value="'.$html[$i]['no1'].'">
                          <span>&ndash;</span>
                          <input type="text" name="divisionNo2" class="input_divisionNo" maxlength="3" value="'.$html[$i]['no2'].'">
                          <span>&ndash;</span>
                          <input type="text" name="divisionNo3" class="input_divisionNo" maxlength="3" value="'.$html[$i]['no3'].'">
                          <span>&ndash;</span>
                          <input type="text" name="divisionNo4" class="input_divisionNo" maxlength="3" value="'.$html[$i]['no4'].'">
                      </td>
                  </tr>
                  <tr>
                      <td class="td_adminUserListLabel">管理者権限</td>
                      <td class="td_adminUserListMessage">
                          <select name="auth" class="select_userAuth">
                              '.$option.'
                          </select>
                      </td>
                  </tr>
              </tbody>
          </table>
      </form></td></tr>';
    if ($count == $i) {
      $userHtml .= '<input type="hidden" id="total" name="total" value="'.$count.'">';
    }
//     }
    $i++;
  }
  return $userHtml;
}

function userSummary($con, $userId='', $keyWord = '') {
  if ($userId != '') {
    $userSql = ' AND userId = "'.$userId.'"';
  } else {
    $userSql = '';
  }
  if ($keyWord != '') {
    $str = array("　", " and ", " AND ");
    $keyWord = str_replace($str, " ", $keyWord);
    if(stristr($keyWord, " ")){//複数キーワードでの検索
      $ex = explode(" ", $keyWord);
      $count = count($ex);
      for($i=0; $i<$count; $i++){
        $where .= ' AND (userName LIKE "%'.$ex[$i].'%"
              OR realName LIKE "%'.$ex[$i].'%"
              OR mailAddress LIKE "%'.$ex[$i].'%")';
      }
    }else{//単体キーワードでの検索
      $where = ' AND (userName LIKE "%'.$keyWord.'%"
              OR realName LIKE "%'.$keyWord.'%"
              OR mailAddress LIKE "%'.$keyWord.'%")';
    }
  } else {
    $where = '';
  }
  // ユーザ一覧の取得
  $sql = '
  SELECT
  userId AS id, userName AS nickname, realName AS name, userImage AS image,
  mailAddress AS mail, userAuthority AS auth, divisionNo AS no
  FROM
  userMstTbl
  WHERE
  NOT userId = 1
  '.$userSql.$where;
  $stmt = $con->prepare($sql);
  $stmt->execute();
  $i = 1;
  // 取得した一覧を配列に格納
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $html[$i]['id'] = $data['id'];
    $html[$i]['nickname'] = $data['nickname'];
    $html[$i]['name'] = $data['name'];
    $html[$i]['image'] = $data['image'];
    $html[$i]['mail'] = $data['mail'];
    $html[$i]['auth'] = $data['auth'];
    if ($data['no'] != NULL) {
      $no = explode('-', $data['no']);
      $html[$i]['no1'] = $no[0];
      $html[$i]['no2'] = $no[1];
      $html[$i]['no3'] = $no[2];
      $html[$i]['no4'] = $no[3];
    }
    $i++;
  }
  $stmt->closeCursor();
  return $html;
}
