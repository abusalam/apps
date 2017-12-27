<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Monthly Performance Report');
WebLib::IncludeCSS();
WebLib::JQueryInclude();
WebLib::IncludeCSS('mpr/css/forms.css');
WebLib::IncludeCSS('css/chosen.css');
WebLib::IncludeJS('js/chosen.jquery.min.js');
WebLib::IncludeJS('mpr/js/Works.js');
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
  <?php require_once(__DIR__ . '/WorkData.php'); ?>
  <?php WebLib::ShowMsg(); ?>
  <div class="formWrapper-Autofit">
    <h3 class="formWrapper-h3">Works for Schemes</h3>

    <form id="frmWorks" action="" method="post">
      <div class="FieldGroup">
        <label for="Scheme"><strong>Scheme:</strong></label><br/>
        <select id="Scheme" name="Scheme">
          <option></option>
          <?php
          $DB = new MySQLiDBHelper();
          $DB->where('UserMapID', $_SESSION['UserMapID']);
          $Schemes = $DB->get(MySQL_Pre . 'MPR_Schemes');
          foreach ($Schemes as $Scheme) {
            echo '<option value="' . $Scheme['SchemeID'] . '">' . $Scheme['SchemeName'] . '</option>';
          } ?>
        </select>
      </div>
      <div class="FieldGroup">
        <label for="MprMapID"><strong>Executing Agency:</strong></label><br/>
        <select id="MprMapID" name="MprMapID">
          <option></option>
          <?php
          $DB = new MySQLiDBHelper();
          $DB->where('CtrlMapID', $_SESSION['UserMapID']);
          $Users = $DB->get(MySQL_Pre . 'MPR_ViewMappedUsers');
          foreach ($Users as $User) {
            echo '<option value="' . $User['MprMapID'] . '">' . $User['UserName'] . '</option>';
          } ?>
        </select>
      </div>
      <br/>

      <div class="FieldGroup">
        <label for="txtWork"><strong>Description of Work:</strong><br/>
          <input id="txtWork" type="text" name="txtWork" class="form-TxtInput" style="width: 410px;">
        </label>
      </div>
      <br/>

      <div class="FieldGroup">
        <label for="txtCost"><strong>Estimated Cost:</strong><br/>
          <input id="txtCost" type="text" name="txtCost" class="form-TxtInput" style="width: 80px;">
        </label>
      </div>
      <div class="FieldGroup">
        <label for="txtWorkRemarks"><strong>Items/Remarks:</strong><br/>
          <input id="txtWorkRemarks" type="text" name="txtWorkRemarks" class="form-TxtInput" style="width: 275px;">
        </label>
      </div>
      <div style="clear: both;"></div>
      <hr/>
      <div class="formControl">
        <input type="Submit" value="Create Work" name="CmdAction">
      </div>
        <input type="hidden" name="FormToken"
               value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>" />
    </form>
  </div>
  <div class="formWrapper-Autofit">
    <h3 class="formWrapper-h3">Sanction for Works</h3>

    <form action="" method="post">
      <div class="FieldGroup">
        <label for="WorkID"><strong>Work:</strong></label><br/>
        <select id="WorkID" name="WorkID">
          <option></option>
        </select>
      </div>
      <div style="clear: both;"></div>
      <div class="FieldGroup">
        <label for="txtOrderNo"><strong>Order No:</strong><br/>
          <input id="txtOrderNo" type="text" name="txtOrderNo" class="form-TxtInput" style="width: 120px;">
        </label>
      </div>
      <div class="FieldGroup">
        <label for="txtDate"><strong>Order Date:</strong><br/>
          <input id="txtDate" type="text" name="txtDate" class="form-TxtInput datePicker">
        </label>
      </div>
      <div class="FieldGroup">
        <label for="txtAmount"><strong>Funds To Release:</strong><br/>
          <input id="txtAmount" type="text" name="txtAmount" class="form-TxtInput" style="width: 100px;">
        </label>
      </div>
      <div style="clear: both;"></div>
      <div class="FieldGroup">
        <label for="txtSanctionRemarks"><strong>Remarks:</strong><br/>
          <input id="txtSanctionRemarks" type="text" name="txtSanctionRemarks" class="form-TxtInput" style="width: 410px;">
        </label>
      </div>
      <div style="clear: both;"></div>
      <hr/>
      <div class="formControl">
        <input type="Submit" value="Release Fund" name="CmdAction">
      </div>
        <input type="hidden" name="FormToken"
               value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>" />
    </form>
  </div>
  <div id="SanctionData"></div>
  <div style="clear: both;"></div>
  <div id="DataTable"></div>
  <div class="formWrapper-Clear"></div>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
</body>
</html>
