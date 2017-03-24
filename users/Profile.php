<?php
/**
 * @todo User Password Change incomplete [Working currently]
 */
require_once(__DIR__ . '/../lib.inc.php');
WebLib::AuthSession();
WebLib::Html5Header("Profile");
WebLib::IncludeCSS();
WebLib::IncludeJS("js/md5.js");
WebLib::JQueryInclude();
WebLib::IncludeCSS("Jcrop/css/jquery.Jcrop.min.css");
WebLib::IncludeJS("Jcrop/js/jquery.Jcrop.min.js");
?>
<script>
  $(function() {
    $('#ChgPwd')
            .button()
            .click(function() {
      if (($('#NewPassWD').val() === $('#CnfPassWD').val())) {
        if (scorePassword($('#CnfPassWD').val()) >= 80) {
          $('#OldPassWD').val(MD5(MD5($('#OldPassWD').val()) + $('#AjaxToken').val()));
          $('#NewPassWD').val(MD5(MD5($('#NewPassWD').val()) + $('#CnfPassWD').val()));
          $('#CnfPassWD').val(MD5($('#CnfPassWD').val()));
          $('#ChgPwd-frm').submit();
          $(this).dialog("close");
        }
        else {
          alert('Password Complexity atleast 80 is required!');
        }
      } else {
        alert('New passwords don\'t match');
      }
    });

    $('input[type="button"]').button();
    $('#Msg').text('');
    $('#NewPassWD').keyup(function() {
      $('#PwdScore').html('(' + scorePassword($(this).val()) + '/100)');
    });
    $('#CnfPassWD').keyup(function() {
      if (($('#NewPassWD').val() === $('#CnfPassWD').val())) {
        $('#PwdMatch').html('Matched');
      } else {
        $('#PwdMatch').html('Not Matched');
      }
    });
  });
  function scorePassword(pass) {
    var score = 0;
    if (!pass)
      return score;

    // award every unique letter until 5 repetitions
    var letters = new Object();
    for (var i = 0; i < pass.length; i++) {
      letters[pass[i]] = (letters[pass[i]] || 0) + 1;
      score += 5.0 / letters[pass[i]];
    }

    // bonus points for mixing it up
    var variations = {
      digits: /\d/.test(pass),
      lower: /[a-z]/.test(pass),
      upper: /[A-Z]/.test(pass),
      nonWords: /\W/.test(pass),
    }

    variationCount = 0;
    for (var check in variations) {
      variationCount += (variations[check] == true) ? 1 : 0;
    }
    score += (variationCount - 1) * 10;

    return parseInt(score);
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
    <span class="Message" id="Msg">
      <b>Loading please wait...</b>
    </span>
  <?php

  if ((WebLib::GetVal($_POST, 'CnfPassWD') !== null) && ($_SESSION['Token'] === WebLib::GetVal($_POST, 'FormToken'))) {
    $Data = new MySQLiDBHelper();
    //TODO :: Review Update Password
    $Data->where('UserMapID', $_SESSION['UserMapID']);
    $Users=$Data->get(MySQL_Pre . 'Users',1);
    $Password=WebLib::GetVal($Users,'UserPass');
    if(md5($Password.$_SESSION['Token'])===WebLib::GetVal($_POST, 'OldPassWD')){
      $Data->where('Registered', 1);
      $Data->where('Activated', 1);
      $Data->where('UserMapID', $_SESSION['UserMapID']);

      $PassData['Pass'] = WebLib::GetVal($_POST, 'CnfPassWD', true);
      $Updated = $Data->update(MySQL_Pre . 'Users', $PassData);
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
    <form id="ChgPwd-frm" action="Profile.php" method="post" autocomplete="off">
        <h2>Change Password</h2>
        <div id="chgpwd-dlg" title="Change Password">
            <input type="password" placeholder="Old Password" name="OldPassWD"
                   id="OldPassWD"/><br/>
            <input type="password" placeholder="New Password" name="NewPassWD"
                   id="NewPassWD"/><span id="PwdScore"></span><br/>
            <input type="password" placeholder="Confirm Password"
                   name="CnfPassWD" id="CnfPassWD"/><span id="PwdMatch"></span>
        </div>
        <input type="hidden" id="AjaxToken" name="FormToken"
               value="<?php echo WebLib::GetVal($_SESSION, 'Token'); ?>"/>
        <input type="button" id="ChgPwd" value="Change Password"/>
    </form>
  </div>
  <div class="pageinfo">
    <?php WebLib::PageInfo(); ?>
  </div>
  <div class="footer">
    <?php WebLib::FooterInfo(); ?>
  </div>
</body>
</html>

