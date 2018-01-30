<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Monthly Performance Report');
WebLib::IncludeCSS();
WebLib::IncludeCSS('mpr/css/forms.css');
WebLib::JQueryInclude();
WebLib::IncludeCSS('css/chosen.css');
WebLib::IncludeJS('js/chosen.jquery.min.js');
WebLib::IncludeJS('mpr/js/Schemes.js');
require_once('SchemesData.php');
?>
<script type="text/javascript">

</script>
</head>
<body>
<div class="TopPanel">
    <div class="LeftPanelSide"></div>
    <div class="RightPanelSide"></div>
    <h1><?php echo htmlentities($_SESSION['UserName']); ?></h1>
</div>
<div class="Header"></div>
<?php
WebLib::ShowMenuBar('MPR');
?>
<div class="content">
    <div class="formWrapper-Autofit">
        <h3 class="formWrapper-h3">Allotment Received for Schemes</h3>
      <?php WebLib::ShowMsg(); ?>
        <form id="frmSchemeAllotment" action="" method="POST">
            <div class="FieldGroup">
                <label for="Schemes"><strong>Scheme Name:</strong></label><br/>
                <select id="Schemes" name="Scheme" class="chzn">
                    <option></option>
                  <?php
                  $DB = new MySQLiDBHelper();
                  $DB->where('UserMapID', $_SESSION['UserMapID']);
                  $Schemes = $DB->get(MySQL_Pre . 'MPR_Schemes');
                  foreach ($Schemes as $SchemeID) {
                    echo '<option value="' . $SchemeID['SchemeID'] . '">' . $SchemeID['SchemeID'] . ' - ' . htmlentities($SchemeID['SchemeName']) . '</option>';
                  } ?>
                    <option value="NewScheme">Create New...</option>
                </select>
            </div>
            <div class="FieldGroup">
                <label for="txtAmount"><strong>Amount:</strong><br/>
                    <input id="txtAmount" type="text" name="txtAmount"
                           class="form-TxtInput" style="width: 90px;">
                </label>
            </div>
            <div class="FieldGroup">
                <label for="txtOrderNo"><strong>Order No.:</strong><br/>
                    <input id="txtOrderNo" type="text" name="txtOrderNo"
                           class="form-TxtInput">
                </label>
            </div>
            <div class="FieldGroup">
                <label for="txtDate"><strong>Date:</strong><br/>
                    <input id="txtDate" type="text" name="txtDate"
                           class="form-TxtInput datePicker">
                </label>
            </div>
            <div class="FieldGroup">
                <label for="txtYear"><strong>Year:</strong><br/>
                    <input id="txtYear" type="text" name="txtYear"
                           class="form-TxtInput" style="width: 60px;">
                </label>
            </div>
            <div style="clear: both;"></div>
            <hr/>
            <div class="formControl">
                <input type="Submit" value="Save Allotment" name="BtnScheme">
            </div>
            <input type="hidden" name="FormToken"
                   value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>"/>
        </form>
        <div id="DataTable"></div>
    </div>
    <div id="createNewScheme" title="Create New Scheme">
        <form id="frmCreateScheme" action="" method="POST">
            <label for="txtSchemeName"><strong>Scheme Name:</strong></label>
            <input id="txtSchemeName" type="text" name="txtSchemeName"
                   class="form-TxtInput">
            <input type="hidden" name="BtnCreScheme" value="1"/>
            <input type="hidden" name="FormToken"
                   value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>"/>
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