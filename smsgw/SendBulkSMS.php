<?php
require_once('../library.php');
initHTML5page();
if ((GetVal($_POST, "ChkUseForm") === "No") && (GetVal($_POST, "CmdShow") === "Send")) {
  $_SESSION['SessionAppID'] = 'UseDB';
} elseif ((GetVal($_POST, "ChkUseForm") === "Yes") && (GetVal($_POST, "CmdShow") === "Send")) {
  $_SESSION['SessionAppID'] = 'UseForm';
}
$_SESSION['AppID'] = GetVal($_SESSION, "SessionAppID");
?>
</head>
<body>
  <h2>Send SMS</h2>
  <?php
  $Data = new DB();
  if (GetVal($_POST, 'TextSMS') !== NULL) {
    $_SESSION['TextSMS'] = GetVal($_POST, 'TextSMS');
  } elseif (GetVal($_SESSION, "TextSMS") === "") {
    $_SESSION['TextSMS'] = "Blank SMS";
  }

  if ((GetVal($_SESSION, "SessionAppID") === 'UseForm') && (GetVal($_POST, "AppMobile") != "") && (GetVal($_POST, "ChkUseForm") === "Yes")) {
    $Data->SendSMS($_SESSION['TextSMS'], GetVal($_POST, "AppMobile")); //GetVal($_POST, "TextSMS")
    echo "Sent To:" . GetVal($_POST, "AppMobile") . "<br/>";
  } elseif (GetVal($_SESSION, "SessionAppID") === 'UseDB') {
    $SMSData = new DB();
    $Qry = "Select `ContactNo` from `" . MySQL_Pre . "ToMobile`";
    $Data->do_sel_query($Qry);
    $NotSent = true;
    while ($MobileRow = $Data->get_row()) {
      $NotSent = false;
      $SMSData->SendSMS($_SESSION['TextSMS'], $MobileRow['ContactNo']);
      echo "Sent To(DB):" . $MobileRow['ContactNo'] . "<br/>";
    }
    if ($NotSent) {
      echo "No Contact Nos. in DB";
    }
  }
  ?>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <label for="AppMobile">Send SMS to this Mobile No:</label>
    <input id="AppMobile" type="text" name="AppMobile" value="<?php echo GetVal($_POST, "AppMobile"); ?>"/>
    <input id="ChkUseDBYes" type="radio" value="Yes" name="ChkUseForm" checked="checked" />
    <label for="ChkUseDBYes">Yes</label>
    <input id="ChkUseDBNo" type="radio" value="No" name="ChkUseForm" checked="checked" />
    <label for="ChkUseDBNo">No</label><br/>
    <textarea rows="20" cols="120" name="TextSMS"><?php echo GetVal($_SESSION, "TextSMS"); ?></textarea>
    <div style="clear:both;"></div>
    <input type="submit" value="Send" name="CmdShow" />
  </form>
  <?php
  echo "<br />Total SMS sent: " . $Data->do_max_query("Select count(*) from `" . MySQL_Pre . "SentSMS` "
          . " Where `AppID`='" . GetVal($_SESSION, "SessionAppID") . "'");
  ?>
</body>
</html>
