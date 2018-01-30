<?php

if (file_exists(__DIR__ . '/config.inc.php')) {
  require_once __DIR__ . '/config.inc.php';
} else {
  require_once __DIR__ . '/config.sample.inc.php';
}

require_once __DIR__ . '/../php-mailer/GMail.lib.php';

class SMSGW {

  public static function SendSMS($SMSData, $MobileNo, $MsgType = 'PM', $ScheTime = null, $DlrType = 5) {

    $PostData['username']  = SMSGW_USER;
    $PostData['pin']       = SMSGW_PASS;
    $PostData['signature'] = SMSGW_SENDER;
    $PostData['mnumber']   = $MobileNo;
    $PostData['message']   = $SMSData;

    /**
     * Scheduled time to deliver this message in the format of yyyy/MM/dd/HH/mm;
     * default is null
     */
    $PostData['scheTime'] = $ScheTime;

    /**
     *
     * PM – Plain text message;
     * UC – Unicode Message;
     * BM – Binary text message(ringtone, logo, picture, wap link);
     * FL –Flash message;
     * SP – messages to special port; $PostData['port'] = 443;
     * default is PM
     */
    $PostData['msgType'] = $MsgType;

    /**
     * 0 – No need for dlr;
     * 1 – end delivery notification success or failure;
     * 2 – end delivery notification failure only;
     * 4 – SMS Platform failures / reject status only;
     * 5 - SMS Platform failures / reject status + end delivery notification success or failure;
     * 6 - SMS Platform failures / reject status + end delivery notification failure;
     * default is 0
     */
    $PostData['Dlrtype'] = $DlrType;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_USERPWD, SMSGW_USER . ':' . SMSGW_PASS);

    curl_setopt($ch, CURLOPT_URL, SMSGW_URL);

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($PostData));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (UseSMSGW === true) {
      $curl_output = curl_exec($ch);
    } else {
      $curl_output = 'SMS Gateway disabled in configuration.';
      //TODO: Send Email when SMS Gateway Disabled in Configuration
      $Subject='From: ' . $_SERVER['REMOTE_ADDR'] . ' On: ' . date('d/m/Y l H:i:s A', time());
      GMailSMTP('abu.alam@nic.in', 'SMS Gateway', $Subject, $SMSData);
    }

    if ($curl_output === false) {
      $Status = curl_errno($ch);
    } else {
      $Status = $curl_output;
    }

    curl_close($ch);

    return self::UsageLog($MobileNo, $SMSData, $Status, 'APPS');
  }

  private static function UsageLog($MobileNo, $MsgText, $Status, $AppID) {

    $UsageData['MobileNo'] = $MobileNo;
    $UsageData['MsgText']  = $MsgText;
    $UsageData['AppID']    = $AppID;
    $UsageData['Status']   = $Status;
    $UsageData['Script']   = __FILE__ . '[' . __LINE__ . ']';

    $MySQLiDB = new MySQLiDBHelper();
    $MySQLiDB->insert(MySQL_Pre . 'SMS_Usage', $UsageData);

    return $Status;
  }
}