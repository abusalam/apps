<?php
require_once(__DIR__ . '/../lib.inc.php');
WebLib::AuthSession();
WebLib::Html5Header("Profile");
WebLib::IncludeCSS();
WebLib::IncludeJS('js/jQuery-MD5/sha512.min.js');
WebLib::JQueryInclude();
WebLib::IncludeCSS("Jcrop/css/jquery.Jcrop.min.css");
WebLib::IncludeJS("Jcrop/js/jquery.Jcrop.min.js");
WebLib::IncludeCSS('mpr/css/forms.css');
?>
<script>
  $(function () {
    $('#ChgPwd')
      .button()
      .click(function () {
        if (($('#NewPassWD').val() === $('#CnfPassWD').val())) {
          if (validatePassword($('#CnfPassWD').val())) {
            $('#OldPassWD').val(sha512(sha512($('#OldPassWD').val()) + $('#AjaxToken').val()));
            $('#NewPassWD').val(sha512(sha512($('#NewPassWD').val()) + $('#CnfPassWD').val()));
            $('#CnfPassWD').val(sha512($('#CnfPassWD').val()));
            $('#ChgPwd-frm').submit();
            $(this).dialog("close");
          }
          else {
            alert('Password Complexity as per policy is required!');
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
<div class="Header">
</div>
<?php
WebLib::ShowMenuBar('USER');
?>
<div class="content">
    <div class="formWrapper-Autofit">
        <h3 class="formWrapper-h3">Change Password</h3>
        <span class="Message" id="Msg" style="float: right;">
              <b>Loading please wait...</b>
          </span>
      <?php

      if (WebLib::GetVal($_POST, 'CnfPassWD') !== null) {
        $Data = new MySQLiDBHelper();
        //TODO :: Review Update Password
        $Data->where('UserMapID', $_SESSION['UserMapID']);
        $Users    = $Data->get(MySQL_Pre . 'Users', 1);
        $Password = WebLib::GetVal($Users[0], 'UserPass');
        if (hash('sha512', $Password . $_SESSION['Token']) === WebLib::GetVal($_POST, 'OldPassWD')) {
          $Data->where('Registered', 1);
          $Data->where('Activated', 1);
          $Data->where('UserMapID', $_SESSION['UserMapID']);

          $PassData['UserPass'] = WebLib::GetVal($_POST, 'CnfPassWD', true);
          $Updated              = $Data->update(MySQL_Pre . 'Users', $PassData);
          if ($Updated > 0) {
            $_SESSION['Msg'] = 'Password Changed Successfully!';
          } else {
            $_SESSION['Msg'] = 'Unable to change password!';
          }
        } else {
          $_SESSION['Msg'] = 'Wrong password!';
        }
        unset($Data);
      }
      $_SESSION['Token'] = md5($_SERVER['REMOTE_ADDR'] . session_id() . $_SESSION['ET']);
      WebLib::ShowMsg();
      ?>
        <pre id="Error">   <?php //print_r($_POST); ?></pre>
        <form id="ChgPwd-frm" action="Profile.php" method="post"
              autocomplete="off">
            <div id="chgpwd-dlg" title="Change Password">
                <div class="FieldGroup">
                    <label for="txtAmount"><strong>Current
                            Password:</strong><br/>
                        <input type="password" placeholder="Current Password"
                               name="OldPassWD"
                               id="OldPassWD" required
                               class="form-TxtInput"/>
                    </label>
                </div>
                <div style="clear: both;"></div>
                <div class="FieldGroup">
                    <label for="txtBalance"><strong>New Password: </strong><span
                                id="PwdScore"></span><br/>
                        <input type="password" placeholder="New Password"
                               name="NewPassWD"
                               id="NewPassWD" required
                               class="form-TxtInput"/>
                    </label>
                </div>
                <div style="clear: both;"></div>
                <div class="FieldGroup">
                    <label for="txtBalance"><strong>Confirm New
                            Password: </strong><span id="PwdMatch"></span><br/>
                        <input type="password"
                               placeholder="Confirm New Password"
                               name="CnfPassWD" id="CnfPassWD" required
                               class="form-TxtInput"/>

                    </label>
                </div>
            </div>
            <div style="clear: both;"></div>
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
            <div style="clear: both;"></div>
            <hr/>
            <div class="formControl">
                <input type="button" id="ChgPwd" value="Change Password"/>
            </div>
        </form>
        <input type="hidden" id="AjaxToken" name="FormToken"
               value="<?php echo WebLib::GetVal($_SESSION, 'Token'); ?>"/>
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

