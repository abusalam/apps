<?php
require_once('AuthOTP.php');

session_start();

WebLib::CreateDB();

$AuthOTP = new AuthOTP(AuthOTP::TOKEN_DATA_TEMP);

//$AuthOTP->setUser('1', 'TOTP');

//echo $AuthOTP->getData('8972096989');
?>

<div>
  <?php
  $MobileNo   = $_SESSION['MobileNo'];// $_GET['mdn'];
  $CheckCodes = '';
  if ($AuthOTP->hasToken($MobileNo)) {
    $hastoken = "Yes";
    $type     = $AuthOTP->getTokenType($MobileNo);
    if ($type == "HOTP") {
      $type = "- Counter Based";
    } else {
      $type = "- Time Based";
    }
    $hexkey = $AuthOTP->getKey($MobileNo);
    $b32key = $AuthOTP->helperhex2b32($hexkey);
    //$AuthOTP->resyncCode($MobileNo,'381723','990920');
    $UserData = unserialize(base64_decode($AuthOTP->getData($MobileNo)));

    $url        = urlencode($AuthOTP->createURL($MobileNo));
    $keyurl     = "<img src=\"http://chart.apis.google.com/chart?cht=qr&chl=$url&chs=200x200\">";
    $CheckCodes = "<br/>1. " . $AuthOTP->oath_hotp($hexkey, 1)
      . "<br/>2. " . $AuthOTP->oath_hotp($hexkey, 2)
      . "<br/>3. " . $AuthOTP->oath_hotp($hexkey, 3)
      . "<br/>Counter:" . $UserData['tokencounter'];
    // now we generate the qrcode for the user

    echo '<h2>Scan the QR Code or Enter the Key</h2>'
      . '<b>User:</b> ' . $_SESSION['UserName']
      . '<br/><b>MobileNo:</b> ' . $MobileNo . '<br/>'
      . $keyurl . '<br/><b>Activation Key:</b> ' . $b32key;
  } else {
    echo '<h1>Please open the Android App and Register your Mobile No. ' . $_SESSION['MobileNo'] . '</h1>';
  }
  ?>
</div>
