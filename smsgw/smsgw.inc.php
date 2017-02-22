<?php

if (file_exists(__DIR__ . '/config.inc.php')) {
  require_once __DIR__ . '/config.inc.php';
} else {
  require_once __DIR__ . '/config.sample.inc.php';
}

class SMSGW {

  public static function SendSMS($SMSData, $MobileNo, $MsgType = 'PM', $ScheTime = null, $DlrType = 5) {

    $PostData['username'] = SMSGW_USER;

    $PostData['pin'] = SMSGW_PASS;
    $PostData['signature'] = SMSGW_SENDER;
    $PostData['mnumber'] = $MobileNo;
    $PostData['message'] = $SMSData;

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

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    curl_setopt($ch, CURLOPT_USERPWD, SMSGW_USER . ':' . SMSGW_PASS);

    curl_setopt($ch, CURLOPT_URL, SMSGW_URL);

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($PostData));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $curl_output = curl_exec($ch);

    curl_close($ch);

    return $curl_output;
  }

}

?>