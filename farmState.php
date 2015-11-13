<?PHP
require_once('./include/line_define.php');
require_once('./include/common.php');

try{
  $con = new PDO("mysql:dbname=".DB_NAME.";host=".DB_URL,DB_UID,DB_PWD);
} catch(PDOException $e) {
  die('err:'. $e->getMessage());
}
$con->query('SET NAMES utf8');

$sql = 'SELECT
         farmId, latitude AS lat, longitude AS lon, companyId AS cId
        FROM
         farmMstTbl';

$stmt = $con->prepare($sql);
$stmt->execute();
$i = 0;

// 実行結果を取得し、DBにインサート
while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $lat[$i] = $data['lat'];
  $lon[$i] = $data['lon'];
  $fId[$i] = $data['farmId'];
  $cId[$i] = $data['cId'];
  $url = 'https://api.p2g.auone.jp/getSoratena?apiKey=FxFcfqmvc4qMQwLMp1ANTrw9QgPxeK6A&lat='.$data['lat'].'&lon='.$data['lon'];

  $json = file_get_contents($url, true);

  $obj = json_decode($json);

  $airtmp[$i] = $obj->{'AIRTMP'};
  $arpress[$i] = $obj->{'ARPRSS'};
  $rhum[$i] = $obj->{'RHUM'};
  $sun[$i] = $obj->{'SUN'};
  $s[$i] = pow(10, (pow($sun[$i]/0.6359, 1/3)))+99;

  $i++;
}

$sql = 'INSERT INTO
         farmStateTbl (farmId, airtmp, arprss, rhum, sun, regTime, companyId)
        VALUES
         (?,?,?,?,?,?,?)
';
$now = date(YmdHis);
$i = 0;
while (count($fId) > $i) {
  try {
    $con->beginTransaction();
    $stmt = $con->prepare($sql);
    $flg = $stmt->execute(array($fId[$i], $airtmp[$i], $arpress[$i], $rhum[$i], $s[$i], $now, $cId[$i]));
    $stmt->closeCursor();
    if ($flg) {
      $con->commit();
    } else {
      $con->rollBack();
    }
  } catch (Exception $e) {
    $con -> rollBack();
  }
  $i++;
}
// userTempTblに入っている期限切れのuserを削除する
$sql = 'DELETE FROM userTempTbl WHERE limitTime < NOW()';
try {
  $con->beginTransaction();
  $stmt = $con->prepare($sql);
  $flg = $stmt->execute();
  $stmt->closeCursor();
  if ($flg) {
    $con->commit();
  } else {
    $con->rollBack();
  }
} catch (Exception $e) {
  $con -> rollBack();
}
db_close($con);
exit();
?>

