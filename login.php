<?php
/**
 * On successful Authentication the following Session Variables are set
 *
 *  $_SESSION['CheckAuth'] = "Valid";
 *  $_SESSION['UserName'] = $Row['UserName'];
 *  $_SESSION['UserMapID'] = $Row['UserMapID'];
 */
$FailedTry = 2; //No of Allowed failed login without captcha

require_once __DIR__ . '/lib.inc.php';
require_once __DIR__ . '/smsgw/smsgw.inc.php';
session_start();

if (WebLib::GetVal($_SESSION, 'BaseDIR') === null) {
  header('Location: index.php');
  exit();
}
$_SESSION['ET'] = microtime(true);
$Data = new MySQLiDBHelper();
$ID = WebLib::GetVal($_SESSION, 'ID');
$_SESSION['ID'] = session_id();
if (WebLib::GetVal($_SESSION, 'LifeTime') === null) {
  $_SESSION['LifeTime'] = time();
}
$action = WebLib::CheckAuth();


if ($action == "LogOut") {
  $QueryData['SessionID'] = WebLib::GetVal($_SESSION, 'ID');
  $QueryData['IP']        = $_SERVER['REMOTE_ADDR'];
  $QueryData['Referrer']  = $Data->escape($_SERVER["HTTP_REFERER"]);
  $QueryData['UserAgent'] = $_SERVER['HTTP_USER_AGENT'];
  $QueryData['UserID']    = WebLib::GetVal($_SESSION, 'UserMapID');
  $QueryData['URL']       = $Data->escape($_SERVER['PHP_SELF']);
  $QueryData['Action']    = $action . ': (' . $_SERVER['SCRIPT_NAME'] . ')';
  $QueryData['Method']    = $Data->escape($_SERVER['REQUEST_METHOD']);
  $QueryData['URI']       = $Data->escape($_SERVER['REQUEST_URI']);
  $Data->insert(MySQL_Pre . 'Logs', $QueryData);
  unset($QueryData);
  unset($Data);
  session_unset();
  session_destroy();
  session_start();
  $_SESSION          = array();
  $_SESSION['ET']    = microtime(true);
  $_SESSION['Debug'] = WebLib::GetVal($_SESSION, 'Debug') . $action . "TOKEN-!Valid";
  header("Location: index.php");
  exit();
} elseif ($action != "Valid") {
  WebLib::InitSess();
}

if (WebLib::GetVal($_SESSION, 'TryCount') >= $FailedTry) {
  $ValidCaptcha = WebLib::StaticCaptcha();
} else {
  $ValidCaptcha = true;
}

if ((WebLib::GetVal($_POST, 'UserID') !== null) && (WebLib::GetVal($_POST,
      'UserPass') !== null) && $ValidCaptcha
) {
  $QueryLogin = "Select UserMapID,UserName,UserID,MobileNo from `" . MySQL_Pre . "Users` "
    . " Where `UserID`=? AND MD5(CONCAT(`UserPass`,MD5(?)))=? AND Activated";
  $filter[]   = WebLib::GetVal($_POST, 'UserID', true);
  $filter[]   = WebLib::GetVal($_SESSION, 'Token', true);
  $filter[]   = WebLib::GetVal($_POST, 'UserPass', true);

  $rows = $Data->rawQuery($QueryLogin, $filter);

  if (count($rows) > 0) {
    session_regenerate_id();
    $Row                     = $rows[0];
    $_SESSION['CheckAuth']   = "Valid";
    $_SESSION['UserName']    = $Row['UserName'];
    $_SESSION['UserMapID']   = $Row['UserMapID'];
    $_SESSION['UserID']      = $Row['UserID'];
    $_SESSION['MobileNo']    = $Row['MobileNo'];
    $_SESSION['ID']          = session_id();
    $_SESSION['FingerPrint'] = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . "KeyLeft");
    $_SESSION['REFERER1']    = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $action                  = "JustLoggedIn";

    $Data->ddlQuery("Update " . MySQL_Pre . "Users Set LoginCount=LoginCount+1"
      . " Where `UserID`='" . WebLib::GetVal($_POST, 'UserID', true) . "'"
      . " AND MD5(concat(`UserPass`,MD5('" . WebLib::GetVal($_POST,
        'LoginToken', true) . "')))='" . WebLib::GetVal($_POST,
        'UserPass',
        true) . "'");

    $QueryData['SessionID'] = WebLib::GetVal($_SESSION, 'ID');
    $QueryData['IP']        = $_SERVER['REMOTE_ADDR'];
    $QueryData['Referrer']  = $Data->escape($_SERVER["HTTP_REFERER"]);
    $QueryData['UserAgent'] = $_SERVER['HTTP_USER_AGENT'];
    $QueryData['UserID']    = WebLib::GetVal($_SESSION, 'UserMapID');
    $QueryData['URL']       = $Data->escape($_SERVER['PHP_SELF']);
    $QueryData['Action']    = 'Login: Success';
    $QueryData['Method']    = $Data->escape($_SERVER['REQUEST_METHOD']);
    $QueryData['URI']       = $Data->escape($_SERVER['REQUEST_URI']);

    $Data->insert(MySQL_Pre . 'Logs', $QueryData);
    unset($QueryData);

  } else {
    $action = "NoAccess";

    $QueryData['SessionID'] = WebLib::GetVal($_SESSION, 'ID');
    $QueryData['IP']        = $_SERVER['REMOTE_ADDR'];
    $QueryData['Referrer']  = $Data->escape($_SERVER["HTTP_REFERER"]);
    $QueryData['UserAgent'] = $_SERVER['HTTP_USER_AGENT'];
    $QueryData['UserID']    = WebLib::GetVal($_SESSION, 'UserMapID');
    $QueryData['URL']       = $Data->escape($_SERVER['PHP_SELF']);
    $QueryData['Action']    = 'Login: Failed[' . WebLib::GetVal($_POST, 'UserID', true) . ']';
    $QueryData['Method']    = $Data->escape($_SERVER['REQUEST_METHOD']);
    $QueryData['URI']       = $Data->escape($_SERVER['REQUEST_URI']);

    $Data->insert(MySQL_Pre . 'Logs', $QueryData);
    unset($QueryData);
  }
}
$_SESSION['Token'] = md5($_SERVER['REMOTE_ADDR'] . $ID . time());
WebLib::Html5Header("Login");
WebLib::IncludeCSS();
WebLib::IncludeCSS('css/forms.css');
WebLib::JQueryInclude();
WebLib::IncludeJS('js/forms.js');
WebLib::IncludeJS('js/jQuery-MD5/jquery.md5.js');
?>
</head>
<body>
<div class="TopPanel">
  <div class="LeftPanelSide"></div>
  <div class="RightPanelSide"></div>
  <h1><?php echo AppTitle; ?></h1>
</div>
<div class="Header">
</div>
<?php
WebLib::ShowMenuBar('APPS');
?>
<div class="content">
  <div class="formWrapper-Autofit">
    <?php
    switch ($action) {
      case "LogOut":
        echo "<h2>Thank You! You Have Successfully Logged Out!</h2>";
        break;
      case "JustLoggedIn":
        echo "<h2>Welcome {$_SESSION['UserName']}!</h2>";
        if (UseSMSGW === true) {
          $TxtSMS = $action . '-' . $_SESSION['UserMapID'] . ': ' . $_SESSION['UserName'] . "\n"
            . ' E-Mail: ' . $_SESSION['UserID'] . "\n"
            . ' Mobile: ' . $_SESSION['MobileNo'] . "\n"
            . ' From: ' . $_SERVER['REMOTE_ADDR'] . "\n"
            . ' On: ' . date('d/m/Y l H:i:s A', time());
          SMSGW::SendSMS($TxtSMS, AdminMobile);
        }
        break;
      case "Valid":
        echo "<h2>Already logged In as {$_SESSION['UserName']}!</h2>";
        break;
      case "NoAccess":
        $_SESSION['Msg']      = "Sorry! Access Denied!";
        $_SESSION['TryCount'] = WebLib::GetVal($_SESSION, 'TryCount') + 1;
        //echo "Try Count:" . WebLib::GetVal($_SESSION, 'TryCount');
        break;
    }
    if (($action != "JustLoggedIn") && ($action != "Valid")) {
      echo '<h3 class="formWrapper-h3">Login</h3>';
      WebLib::ShowMsg();
      ?>
      <form name="frmLogin" method="post"
            action="<?php $_SERVER['PHP_SELF'] ?>">
        <label for="UserID">
          <strong>User ID</strong> (Registered E-Mail)
          <input type="text" id="UserID" class="form-TxtInput"
                 name="UserID" value="" autocomplete="off"/>
        </label>
        <label for="UserPass">
          <strong>Password</strong>
          <input type="password" id="UserPass" class="form-TxtInput"
                 name="UserPass" value="" autocomplete="off"/>
        </label>
        <?php
        if (WebLib::GetVal($_SESSION, 'TryCount') >= $FailedTry) {
          WebLib::StaticCaptcha(true);
        }
        ?>
        <hr/>
        <div class="formControl">
          <input type="hidden" id="LoginToken" name="LoginToken"
                 value="<?php
                 echo WebLib::GetVal($_SESSION, 'Token');
                 ?>"/>
          <input type="submit" class="formButton" value="Login"/>
        </div>
      </form>

      <?php
      //echo WebLib::GetVal($_SESSION,'Debug');
    }
    ?>
  </div>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
<?php
//print_r($_SESSION);
?>
</body>
</html>