<?php
require_once(__DIR__ . '/../lib.inc.php');
require_once __DIR__ . '/../smsgw/smsgw.inc.php';
$saveData['MobileNo'] = WebLib::GetVal($_POST, 'MobileNo', true);
$saveData['TxtSMS']   = WebLib::GetVal($_POST, 'TxtSMS', true) . "\n"
  . '--' . "\n" . date('l d/m/Y H:i:s', time()) . "\n" . 'NIC SMS Gateway';

if (($_SERVER['REMOTE_ADDR'] === '208.91.198.76') || ($_SERVER['REMOTE_ADDR'] === '10.26.19.4')) {
  require_once(__DIR__ . '/../class.MySQLiDBHelper.php');
  $Data                 = new MySQLiDBHelper(HOST_Name, MySQL_User, MySQL_Pass, MySQL_DB);
  $saveData['PostData'] = json_encode($_POST);
  $saveData['Response'] = SMSGW::SendSMS($saveData['TxtSMS'], $saveData['MobileNo']);
  $Resp['GatewayResp']  = $saveData;
  $Resp['Sent']         = $Data->insert(MySQL_Pre . 'GatewaySMS', $saveData);
  $Resp['Msg']          = ($Resp['Sent'] > 0 ? 'SMS Submitted for Delivery!' : 'Unable to Submit for Delivery!');
  echo json_encode($Resp);
  unset($Resp);
  unset($saveData);
  unset($Data);
  exit();
} else {
  $Resp['Msg'] = 'Access Denied! for ' . $_SERVER['REMOTE_ADDR'];
  echo json_encode($Resp);
  unset($Resp);
  exit();
}
?>

