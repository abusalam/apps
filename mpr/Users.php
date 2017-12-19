<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
if (WebLib::GetVal($_POST, 'UserID') === 'NewUser') {
  header('Location: ../users/Users.php');
}
WebLib::Html5Header('Monthly Performance Report');
WebLib::IncludeCSS();
WebLib::IncludeCSS('mpr/css/forms.css');
WebLib::JQueryInclude();
WebLib::IncludeCSS('css/chosen.css');
WebLib::IncludeJS('js/chosen.jquery.min.js');
WebLib::IncludeJS('mpr/js/Users.js');
require_once('UsersData.php');
?>
</head>
<body>
<div class="TopPanel">
  <div class="LeftPanelSide"></div>
  <div class="RightPanelSide"></div>
  <h1><?php echo $_SESSION['UserName']; ?></h1>
</div>
<div class="Header"></div>
<?php
WebLib::ShowMenuBar('MPR');
?>
<div class="content">
  <div class="formWrapper-Autofit">
    <h3 class="formWrapper-h3">Users of Schemes</h3>
    <?php WebLib::ShowMsg(); ?>
    <form id="frmUsers" action="" method="POST">
      <div class="FieldGroup">
        <label for="UserID"><strong>User Name:</strong></label><br/>
        <select id="UserID" name="UserID" class="chzn">
          <option></option>
          <?php
          WebLib::showSelect('UserMapID','UserName','Select `UserName`, `UserMapID` '
            . 'FROM ' . MySQL_Pre . 'Users Where `CtrlMapID`>='.$_SESSION['UserMapID'],WebLib::GetVal($_POST,'UserID'));
        ?>
          <option value="NewUser">Create New...</option>
        </select>
      </div>
      <div style="clear: both;"></div>
      <hr/>
      <div class="formControl">
        <input type="Submit" value="Add User" name="CmdAction">
        <input type="Submit" value="Remove User" name="CmdAction">
      </div>
        <input type="hidden" name="FormToken"
               value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>" />
    </form>
    <div id="DataTable"></div>
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