<?php

require_once('../lib.inc.php');

if ($_SERVER['REMOTE_ADDR'] === SMSGW_IP) {

  $Data               = new MySQLiDBHelper();
  $smsData            = $Data->escape(json_encode($_GET));
  $QryData['IP']      = $_SERVER['REMOTE_ADDR'];
  $QryData['MsgData'] = $smsData;
  $Data->insert('SMS_Data', $QryData);
  unset($Data);

  $KeyWords = explode(" ", $_GET['Message'], 2);
  switch (strtolower($KeyWords[0])) {
    case strtolower("GetID"):
      SMSGW::SendSMS("Request for ID Registered Successfully.", $_GET['Sender']);
      break;
    case strtolower("DOB"):
      SMSGW::SendSMS("Request for Date of Birth Change Registered Successfully.", $_GET['Sender']);
      break;
    default:
      SMSGW::SendSMS("Invalid Keyword: {$KeyWords[0]}", $_GET['Sender']);
      break;
  }
} else {

  header("HTTP/1.1 404 Not Found");
}
?>
