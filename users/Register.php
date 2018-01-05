<?php
require_once __DIR__ . '/../lib.inc.php';
require_once __DIR__ . '/../php-mailer/GMail.lib.php';
require_once __DIR__ . '/../smsgw/smsgw.inc.php';
WebLib::initHTML5page("Register");
WebLib::IncludeCSS();
WebLib::IncludeCSS('css/forms.css');
WebLib::JQueryInclude();
WebLib::IncludeJS('js/jQuery-MD5/sha512.min.js');
?>
<script>
  $(function () {
    $('#ChgPwd')
      .button()
      .click(function () {
        if (($('#NewPassWD').val() === $('#CnfPassWD').val())) {
          if (validatePassword($('#CnfPassWD').val())) {
            $('#NewPassWD').val(sha512(sha512($('#NewPassWD').val()) + $('#LoginToken').val()));
            $('#CnfPassWD').val(sha512($('#CnfPassWD').val()));
            $('#ChgPwd-frm').submit();
            $(this).dialog("close");
          }
          else {
            alert('Strong Password is required!');
          }
        } else {
          alert('New passwords don\'t match');
        }
      });

    $('input[type="button"]').button();
    $('#Msg').text('');
    $('#NewPassWD').keyup(function () {
      $('#PwdScore').html('' + (validatePassword($(this).val()) ? 'Strong' : 'Weak'));
    });
    $('#CnfPassWD').keyup(function () {
      if (($('#NewPassWD').val() === $('#CnfPassWD').val())) {
        $('#PwdMatch').html('Matched');
      } else {
        $('#PwdMatch').html('Not Matched');
      }
    });
  });

  function validatePassword(newPassword) {
    var minNumberofChars = 8;
    var maxNumberofChars = 20;
    var regularExpression = /^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*]{8,20}$/;
    if (newPassword.length < minNumberofChars || newPassword.length > maxNumberofChars) {
      return false;
    }
    return regularExpression.test(newPassword);
  }
</script>
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
        <h3 class="formWrapper-h3">User Registration</h3>
      <?php
      $Data              = new MySQLiDBHelper();
      $UnregisteredUsers = $Data->where('Registered', 0)
        ->where('Activated', 0)
        ->query('Select `UserMapID`,`UserName`'
          . ' FROM `' . MySQL_Pre . 'Users`');

      if (WebLib::GetVal($_POST, 'UserID') !== null) {

        $email     = WebLib::GetVal($_POST, 'UserID', true);
        $MobileNo  = WebLib::GetVal($_POST, 'MobileNo', true);
        $Pass      = WebLib::GetVal($_POST, 'CnfPassWD', true);
        $UserMapID = WebLib::GetVal($_POST, 'UserMapID', true);

        if (WebLib::StaticCaptcha()) {

          $RegData['UserID']     = $email;
          $RegData['UserPass']   = $Pass; //hash('sha512', $Pass);
          $RegData['MobileNo']   = $MobileNo;
          $RegData['Registered'] = 1;
          if (hash('sha512', $Pass . $_SESSION['Token']) === WebLib::GetVal($_POST, 'NewPassWD')) {

            $Registered = $Data->where('Registered', 0)
              ->where('Activated', 0)
              ->where('UserMapID', $UserMapID)
              ->update(MySQL_Pre . "Users", $RegData);

            if ($Registered === true) {

              $UserName = $Data->where('UserMapID', $UserMapID)
                ->query("Select `UserName` FROM `" . MySQL_Pre . "Users`");

              $Subject = "User Account Details - Paschim Medinipur";
              $Body    = "<b>UserID: </b><span> {$email}</span><br/>"
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
              echo "<h3>Unable to process request. User account is already activated.</h3>";
            }
          } else {
            echo "<h3>Request modified during transmission.</h3>";
          }
        } else {
          echo "<h3>You solution of the code in the image is wrong.</h3>";
        }
      } elseif (count($UnregisteredUsers) > 0) {
        ?>
          <form name="feed_frm" id="ChgPwd-frm" method="post"
                action="Register.php" autocomplete="off">
              <label for="UserMapID"><strong>User Name:</strong><br/></label>
              <select id="UserMapID" name="UserMapID">
                <?php
                WebLib::showSelect("UserMapID", "UserName",
                  "Select `UserMapID`,`UserName` "
                  . " FROM `" . MySQL_Pre . "Users` "
                  . " Where NOT `Registered` AND NOT `Activated`;",
                  WebLib::GetVal($_POST, 'UserMapID', true));
                ?>
              </select>
              <div style="clear:both;"></div>
              <label for="UserID"><strong>E-Mail Address:</strong><br/></label>
              <input placeholder="Valid e-Mail Address" type="email" id="UserID"
                     name="UserID" class="form-TxtInput"
                     value="<?php echo WebLib::GetVal($_POST, 'UserID'); ?>"
                     autocomplete="off" required/>
              <label for="MobileNo"><strong>Mobile No:</strong><br/></label>
              <input placeholder="Mobile Number" maxlength="10" type="text"
                     id="MobileNo" name="MobileNo" class="form-TxtInput"
                     value="<?php echo WebLib::GetVal($_POST, 'MobileNo'); ?>"
                     autocomplete="off" required/>
              <label for="NewPassWD"><strong>Password: </strong><span
                          id="PwdScore"></span></label>
              <input type="password" placeholder="New Password" name="NewPassWD"
                     class="form-TxtInput"
                     id="NewPassWD" required/>
              <label for="CnfPassWD"><strong>Confirm Password: </strong><span
                          id="PwdMatch"></span></label>
              <input type="password" placeholder="Confirm Password"
                     name="CnfPassWD" class="form-TxtInput"
                     id="CnfPassWD" required/>

            <?php WebLib::StaticCaptcha(true); ?>
              <div style="clear:both;"></div>
              <hr/>
              <div class="formControl">
                  <input style="width:80px;" type="button" id="ChgPwd"
                         value="Register"/>
              </div>
          </form>
        <?php
      } else {
        echo "<h3>All Users are registered.</h3>";
      }
      $_SESSION['Token'] = md5($_SERVER['REMOTE_ADDR'] . microtime());
      ?>
        <input type="hidden" id="LoginToken" name="LoginToken" value="<?php
        echo WebLib::GetVal($_SESSION, 'Token');
        ?>"/>
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
