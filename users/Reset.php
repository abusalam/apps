<?php
require_once __DIR__ . '/../lib.inc.php';
require_once __DIR__ . '/../php-mailer/GMail.lib.php';
require_once __DIR__ . '/../smsgw/smsgw.inc.php';
WebLib::initHTML5page("Reset Password");
WebLib::IncludeCSS();
WebLib::IncludeCSS('css/forms.css');
WebLib::JQueryInclude();
?>
</head>
<body>
<div class="TopPanel">
  <div class="LeftPanelSide"></div>
  <div class="RightPanelSide"></div>
  <h1><?php echo AppTitle; ?></h1>
</div>
<div class="Header"></div>
<?php
WebLib::ShowMenuBar('WebSite');
?>
<div class="content">
  <div class="formWrapper-Autofit">
    <h3 class="formWrapper-h3">Reset Password</h3>
    <?php
    $Data = new MySQLiDBHelper();
    $UnregisteredUsers = $Data->where('Registered', 0)
      ->where('Activated', 0)
      ->query('Select `UserMapID`,`UserName`'
        . ' FROM `' . MySQL_Pre . 'Users`');

    if (WebLib::GetVal($_POST, 'UserID') !== NULL) {

      $email = WebLib::GetVal($_POST, 'UserID', TRUE);
      $MobileNo = WebLib::GetVal($_POST, 'MobileNo', TRUE);
      $Pass = WebLib::GeneratePassword(10, 2, 2, 2);
      $UserMapID = WebLib::GetVal($_POST, 'UserMapID', TRUE);

      if (WebLib::StaticCaptcha()) {

        $RegData['UserID'] = $email;
        $RegData['UserPass'] = hash('sha512', $Pass);
        $RegData['MobileNo'] = $MobileNo;
        $RegData['Registered'] = 1;

        $Registered = $Data->where('Registered', 0)
          ->where('Activated', 0)
          ->where('UserMapID', $UserMapID)
          ->update(MySQL_Pre . "Users", $RegData);

        if ($Registered === TRUE) {

          $UserName = $Data->where('UserMapID', $UserMapID)
            ->query("Select `UserName` FROM `" . MySQL_Pre . "Users`");

          $Subject = "User Account Details - Paschim Medinipur";
          $Body = "<b>UserID: </b><span> {$email}</span><br/>"
            . "<b>Password: </b><span> {$Pass}</span>";

          $TxtBody = 'UserID: ' . $email . "\r\n" . 'Password: ' . $Pass;
          $SentSMS = '';

          SMSGW::SendSMS($TxtBody, $MobileNo);
          $SentSMS = ' and ' . $MobileNo;

          $MailSent = json_decode(GMailSMTP($email, $UserName[0]['UserName'],
            $Subject, $Body, $TxtBody));

          WebLib::ShowMsg();
          if ($MailSent->Sent) {
            $_SESSION['Msg'] = "<h3>Registration successful.</h3>";
              //. "<p>$TxtBody</p>" TODO: Display Password for Security Audit
              //. "<b>Please Note: </b>Password is sent to: {$email}" . $SentSMS;
          } else {
            $_SESSION['Msg'] = "<h3>Registration successful but Unable to Send Email.</h3>";
              //. "<p>$TxtBody</p>"; TODO: Display Password for Security Audit
          }
          WebLib::ShowMsg();
        } else {
          echo "<h3>Unable to send request.</h3>";
        }
      } else {
        echo "<h3>You solution of the Math in the image is wrong.</h3>";
      }
    }
      ?>
      <form name="feed_frm" method="post" action="Register.php">

          <label for="UserMapID"><strong>User ID:</strong><br/></label>
            <input placeholder="Enter your User ID" type="text" id="UserMapID" name="UserMapID" class="form-TxtInput"
                   value="<?php echo WebLib::GetVal($_POST, 'UserMapID'); ?>" required/>

          <label for="UserID"><strong>E-Mail Address:</strong><br/></label>
          <input placeholder="Valid e-Mail Address" type="email" id="UserID" name="UserID" class="form-TxtInput"
                 value="<?php echo WebLib::GetVal($_POST, 'UserID'); ?>" required/>

          <label for="MobileNo"><strong>Mobile No:</strong><br/></label>
          <input placeholder="Mobile Number" maxlength="10" type="text" id="MobileNo" name="MobileNo" class="form-TxtInput"
                 value="<?php echo WebLib::GetVal($_POST, 'MobileNo'); ?>" required/>

          <input type="hidden" name="LoginToken" value="<?php
          echo WebLib::GetVal($_SESSION, 'Token');
          ?>"/>

        <?php WebLib::StaticCaptcha(true); ?>
        <div style="clear:both;"></div>
        <hr/>
        <div class="formControl">
          <input style="width:80px;" type="submit" value="Reset"/>
        </div>
      </form>
    <div style="clear:both;"></div>
  </div>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
</body>
</html>
