<?php
/**
 * @todo Integrate SMS Gateway
 * @ todo Working on User Management Module
 */
require_once __DIR__ . '/../lib.inc.php';
require_once __DIR__ . '/../php-mailer/GMail.lib.php';
require_once __DIR__ . '/../smsgw/smsgw.inc.php';
WebLib::AuthSession();
WebLib::Html5Header('Users');
WebLib::IncludeCSS();
WebLib::JQueryInclude();
WebLib::IncludeCSS('css/chosen.css');
WebLib::IncludeJS('js/chosen.jquery.min.js');
?>
<script type="text/javascript">
  $(function () {
    $('#CreateUser-dialog-form').dialog({
      autoOpen: false,
      modal: true
    });
    $('#CmdCreateSubmit').button();
    $('#CmdCreate').bind('click', function () {
      $('#CreateUser-dialog-form').dialog('open');
    });
    $('input[type="submit"]').button();
    $('input[type="button"]').button();
    $('select').chosen({width: '400px'});
  });
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
include __DIR__ . '/UsersData.php';
WebLib::ShowMenuBar('USER');
$Data = new MySQLiDB();
?>
<div class="content">
  <?php
  $Msg[0] = '<h2>Manage Users</h2>';
  $Msg[1] = '<h2>Un-Authorised</h2>';
  echo $Msg[$_SESSION['action']];
  WebLib::ShowMsg();
  if ($_SESSION['action'] == 0) {
    ?>
    <div class="FieldGroup" id="CreateUser-dialog-form" style="display:none">
      <form name="frmCreateUser" id="frmCreateUser" method="post"
            action="Users.php">
        <label for="UserName">Display Name: </label>
        <input type="text" name="DisplayName" id="DisplayName" required/>
        <br/>
        <label for="UserDesg">User Name: </label>
        <input type="text" name="UserName" id="UserName" required/>
        <br/>
        <input type="hidden" name="FormToken"
               value="<?php
               echo WebLib::GetVal($_SESSION, 'FormToken')
               ?>"/>
        <br/>
        <input type="submit" id="CmdCreateSubmit" name="CmdSubmit"
               value="Create"/>
      </form>
    </div>

    <form name="frmEditUser" id="frmCreateUser" method="post"
          action="Users.php">
      <div class="FieldGroup">
        <label for="UserName">Select User: </label><br/>
        <select name="UserMapID" data-placeholder="Select an User">
          <?php
          $Query = 'Select `UserMapID`,'
            . ' CONCAT(`UserName`,\' [\',IFNULL(`UserID`,'
            . '\'Un-Registered\'),\']\') as `UserName` '
            . ' FROM `' . MySQL_Pre . 'Users` '
            . ' Where `CtrlMapID`='
            . WebLib::GetVal($_SESSION, 'UserMapID', true)
            . ' Order By `UserName`';
          $Data->show_sel('UserMapID', 'UserName', $Query, WebLib::GetVal($_POST, 'UserMapID'));
          ?>
        </select>
        <hr/>
        <input type="hidden" name="FormToken" value="<?php
        echo WebLib::GetVal($_SESSION, 'FormToken')
        ?>"/>
        <input type="button" id="CmdCreate" value="Create New User"/>
        <input type="submit" name="CmdSubmit" value="Impersonate"/>
        <?php
        if (WebLib::GetVal($_SESSION, 'ImpFromUserMapID') !== null) {
          ?>
          <input type="submit" name="CmdSubmit" value="Stop Impersonating"/>
        <?php
        }
        ?>
        <input type="submit" name="CmdSubmit" value="Activate"/>
        <input type="submit" name="CmdSubmit" value="De-Activate"/>
        <hr/>
      </div>
    </form>
    <div style="clear:both;"></div>
    <h3>Users:</h3>
    <hr/>
  <?php
  }
  //if (WebLib::GetVal($_SESSION, 'Query') === NULL) {
  $_SESSION['Query'] = 'Select `UserID` as `E-Mail Address`,`UserName`,'
    . '`MobileNo`,`LoginCount`,`LastLoginTime`,`Registered`,`Activated`'
    . ' FROM `' . MySQL_Pre . 'Users` '
    . ' Where `CtrlMapID`=' . WebLib::GetVal($_SESSION, 'UserMapID', true);
  //}
  $Data->ShowTable($_SESSION['Query']);
  ?>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
</body>
</html>

