<?php
require_once __DIR__ . '/../lib.inc.php';
require_once __DIR__ . '/../php-mailer/GMail.lib.php';
require_once __DIR__ . '/../smsgw/smsgw.inc.php';
WebLib::InitHTML5page("Reset Password");
WebLib::IncludeCSS();
WebLib::IncludeCSS('css/forms.css');
WebLib::JQueryInclude();
WebLib::IncludeJS('js/jQuery-MD5/sha512.min.js');
$TokenValidity = 1800; // Allowed Elapsed Time in Seconds Since Token Generation
?>
<script>
  $(function () {
    $('#ChgPwd')
      .button()
      .click(function () {
        if (($('#NewPassWD').val() === $('#CnfPassWD').val())) {
          if (validatePassword($('#CnfPassWD').val())) {
            $('#NewPassWD').val(sha512(sha512($('#CnfPassWD').val()) + $('#AjaxToken').val()));
            $('#CnfPassWD').val(sha512($('#CnfPassWD').val()));
            $('#ChgPwd-frm').submit();
          } else {
            alert('Strong Password is required!');
          }
        } else {
          alert('New passwords don\'t match');
        }
      });

    $('input[type="button"]').button();
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
        <h3 class="formWrapper-h3">Reset Password</h3>
      <?php
      $IsValidToken = false;
      $Token = WebLib::GetVal($_REQUEST, 'Token');
      if ($Token !== null) {
        $TokenURL = '?Token=' . $Token;
        if (WebLib::GetVal($_POST, 'NewPassWD')) {
          if (WebLib::StaticCaptcha()) {
            $Data     = new MySQLiDBHelper();
            $Password = WebLib::GetVal($_POST, 'CnfPassWD');

            $Data->where('WebSiteURL', $Token);
            $Users   = $Data->get(MySQL_Pre . 'Users', 1);
            $OldPass = WebLib::GetVal($Users[0], 'UserPass');

            if (hash('sha512', $Password . $_SESSION['Token']) === WebLib::GetVal($_POST, 'NewPassWD')) {
              if ($OldPass == $Password) {
                $_SESSION['Msg'] = 'Previous password is not allowed!';
              } else {
                $Data->where('WebSiteURL', $Token);
                $Data->where('Registered', 1);
                $Data->where('Activated', 1);

                $PassData['UserPass']   = $Password;
                $PassData['WebSiteURL'] = null;
                $Updated                = $Data->update(MySQL_Pre . 'Users', $PassData);

                if ($Updated > 0) {
                  $_SESSION['Msg'] = 'Password Changed Successfully!';
                } else {
                  $_SESSION['Msg'] = 'Invalid Token or User to change password!';
                }
              }
            } else {
              $_SESSION['Msg'] = 'Typed Passwords do not match!';
            }
            unset($Data);
          } else {
            echo "<h3>Code in the image is not matched with that you typed.</h3>";
          }
        } else {
          $Data = new MySQLiDBHelper();
          $Data->where('WebSiteURL', $Token);
          $TokenData = $Data->get(MySQL_Pre . 'Users');
          if (count($TokenData) > 0) {
            $ResetUser   = $TokenData[0];
            $TimeElapsed = time() - strtotime($ResetUser['LastLoginTime']);
            if ($TimeElapsed > $TokenValidity) {
              $_SESSION['Msg'] = 'Password reset Link Expired!';
            } else {
              $IsValidToken = true;
            }
          } else {
            $_SESSION['Msg'] = 'Password reset Link is not valid!';
          }
        }
      } else {
        $TokenURL = null;
      }
      $Data = new MySQLiDBHelper();

      if (WebLib::GetVal($_POST, 'UserName') !== null) {

        $email    = WebLib::GetVal($_POST, 'UserEmail', true);
        $MobileNo = WebLib::GetVal($_POST, 'MobileNo', true);
        $Pass     = WebLib::GeneratePassword(10, 2, 2, 2);
        $UserName = WebLib::GetVal($_POST, 'UserName', true);

        if (WebLib::StaticCaptcha()) {

          $RegData['WebSiteURL'] = md5(WebLib::GetVal($_SESSION, 'Token') . microtime()); //TODO: Reset Password by link

          $Registered = $Data->where('Registered', 1)
            ->where('Activated', 1)
            ->where('UserName', $UserName)
            ->where('UserID', $email)
            ->where('MobileNo', $MobileNo)
            ->update(MySQL_Pre . "Users", $RegData);

          $TokenValidUpto = date('d/m/Y h:i A', time() + 1800);

          if ($Registered === true) {

            $User = $Data->where('UserID', $email)
              ->query("Select `UserName` FROM `" . MySQL_Pre . "Users`");

            $ResetLink = $_SESSION['BaseURL'] . '?PasswordResetToken=' . $RegData['WebSiteURL'];

            $Subject = "Reset Password - Paschim Medinipur";
            $Body    = "<b>UserID: </b><span> {$email}</span><br/>"
              . "<b>Link: </b><span>{$ResetLink}</span> valid upto {$TokenValidUpto}<br/>"
              . '<strong>Please click or open the above link to reset your password.</strong>';

            $TxtBody = 'UserID: ' . $email . "\r\n" . 'Link: ' . $ResetLink
              . "\r\n" . 'Please open the above link to reset your password.';
            $SentSMS = '';

            SMSGW::SendSMS($TxtBody, $MobileNo);
            $SentSMS = ' and registered mobile no. ' . substr_replace($MobileNo, 'xxxxxx', 2, 6);

            $MailSent = json_decode(GMailSMTP($email, $UserName,
              $Subject, $Body, $TxtBody));

            if ($MailSent->Sent) {
              $_SESSION['Msg'] = "<h3>Password reset link has been sent to your email-id{$SentSMS}.</h3>";
              //.'<br/>Link: <a href="' . $ResetLink.'">'.$ResetLink.'</a>';
              //. "<p>$TxtBody</p>" TODO: Display Password for Security Audit
              //. "<b>Please Note: </b>Password is sent to: {$email}" . $SentSMS;
            } else {
              $_SESSION['Msg'] = "<h3>Password reset successful but Unable to Send the link by Email.</h3>";
              //.'<br/>Link: <a href="' . $ResetLink.'">'.$ResetLink.'</a>';
              //. "<p>$TxtBody</p>"; TODO: Display Password for Security Audit
              echo '<pre>' . $MailSent->Status . '</pre>';
            }

          } else {
            echo "<h3>Unable to process request. Invalid email-id or the account is locked.</h3>";
          }
        } else {
          echo "<h3>Code in the image is not matched with that you typed.</h3>";
        }
      }
      $_SESSION['FormToken'] = md5($_SERVER['REMOTE_ADDR'] . session_id() . microtime());
      WebLib::ShowMsg();
      ?>
        <form id="ChgPwd-frm" name="ChgPwd-frm" method="post"
              action="Reset.php<?php echo $TokenURL; ?>" autocomplete="off">

          <?php if ($IsValidToken) { ?>
              <label for="NewPassWD"><strong>New Password: </strong><span
                          id="PwdScore"></span><br/></label>
              <input type="password" placeholder="New Password"
                     name="NewPassWD"
                     id="NewPassWD" required
                     class="form-TxtInput"/>

              <label for="txtBalance"><strong>Confirm New
                      Password: </strong><span
                          id="PwdMatch"></span><br/></label>
              <input type="password" placeholder="Confirm New Password"
                     name="CnfPassWD" id="CnfPassWD" required
                     class="form-TxtInput"/>
            <?php WebLib::StaticCaptcha(true); ?>
              <h4>Password Policy:</h4>
              <ul>
                  <li>Password must at least be 8 characters.</li>
                  <li>Password must not be the same as the previous 1
                      passwords.
                  </li>
                  <li>Password must contain Uppercase, lower case, number and
                      Special Characters.
                  </li>
              </ul>
              <hr/>
              <div class="formControl">
                  <input type="button" name="CmdReset" id="ChgPwd"
                         value="Reset Password"/>
              </div>
          <?php } else { ?>

              <label for="UserID"><strong>User Name:</strong><br/></label>
              <input placeholder="Enter your User Name" type="text" id="UserID"
                     name="UserName" class="form-TxtInput"
                     value="<?php echo WebLib::GetVal($_POST, 'UserName'); ?>"
                     autocomplete="off" required/>

              <label for="UserEmail"><strong>E-Mail
                      Address:</strong><br/></label>
              <input placeholder="Registered e-Mail Address" type="email"
                     id="UserEmail" name="UserEmail" class="form-TxtInput"
                     value="<?php echo WebLib::GetVal($_POST, 'UserEmail'); ?>"
                     autocomplete="off" required/>

              <label for="MobileNo"><strong>Mobile No:</strong><br/></label>
              <input placeholder="Mobile Number" maxlength="10" type="text"
                     id="MobileNo" name="MobileNo" class="form-TxtInput"
                     value="<?php echo WebLib::GetVal($_POST, 'MobileNo'); ?>"
                     autocomplete="off" required/>
            <?php WebLib::StaticCaptcha(true); ?>
              <hr/>
              <div class="formControl">
                  <input type="submit" name="CmdReset" value="Reset Password"/>
              </div>
          <?php } ?>
            <input type="hidden" name="FormToken" id="AjaxToken"
                   value="<?php echo WebLib::GetVal($_SESSION, 'Token'); ?>"/>
        </form>
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
