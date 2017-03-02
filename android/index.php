<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once('AuthOTP.php');

session_start();

WebLib::CreateDB();

$AuthOTP = new AuthOTP(AuthOTP::TOKEN_DATA_TEMP);

//$AuthOTP->setUser('1', 'TOTP');

//echo $AuthOTP->getData('8972096989');
?>
<h2>Users</h2>
<table border="1">
  <tr>
    <th>Mobile No</th>
    <th>Has Token?</th>
    <th>Key</th>
    <th>Secret Key</th>
  </tr>
  <?php
  // now we get our list of users - this part of the page just has a list of users
  // and the ability to create new ones. This isnt really in the scope of the
  // GA4PHP, but for this example, we need to be able to create users, so heres where
  // you do it.
  $MobileNo =$_SESSION['MobileNo'];// $_GET['mdn'];
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
    $UserData=unserialize(base64_decode($AuthOTP->getData($MobileNo)));

    $url        = urlencode($AuthOTP->createURL($MobileNo));
    $keyurl     = "<img src=\"http://chart.apis.google.com/chart?cht=qr&chl=$url&chs=200x200\">";
    $CheckCodes = "<br/>1. " . $AuthOTP->oath_hotp($hexkey, 1)
      . "<br/>2. " . $AuthOTP->oath_hotp($hexkey, 2)
      . "<br/>3. " . $AuthOTP->oath_hotp($hexkey, 3)
      . "<br/>Counter:" . $UserData['tokencounter'];
  } else {
    $b32key   = "";
    $hexkey   = "";
    $type     = "";
    $hastoken = "no";
    $keyurl   = "";
  }


  // now we generate the qrcode for the user

  echo "<tr><td>" . $MobileNo . "</td><td>$hastoken $type</td><td>$keyurl</td><td>$b32key $CheckCodes</td></tr>";

  ?>
</table>
