<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Reports');
WebLib::IncludeCSS();
WebLib::JQueryInclude();
WebLib::IncludeCSS('mpr/css/forms.css');
WebLib::IncludeCSS('css/chosen.css');
WebLib::IncludeJS('js/chosen.jquery.min.js');
WebLib::IncludeJS('mpr/js/Reports.js');
?>
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
    <div class="formWrapper">
        <form action="" method="post">
            <div class="FieldGroup">
                <label for="Scheme"><strong>Scheme:</strong></label><br/>
                <select id="Scheme" name="Scheme">
                    <option></option>
                  <?php
                  $DB = new MySQLiDBHelper();
                  //$DB->where('UserMapID', $_SESSION['UserMapID']);
                  $Schemes = $DB->get(MySQL_Pre . 'MPR_Schemes');
                  foreach ($Schemes as $SchemeID) {
                    echo '<option value="' . $SchemeID['SchemeID'] . '">'
                      . $SchemeID['SchemeID'] . ' - ' . htmlentities($SchemeID['SchemeName']) . '</option>';
                  } ?>
                </select>
            </div>
            <div class="FieldGroup">
                <label for="UserID"><strong>Executing
                        Agency:</strong></label><br/>
                <select id="UserID" name="UserID">
                    <option></option>
                  <?php
                  $DB = new MySQLiDBHelper();
                  //$DB->where('CtrlMapID', $_SESSION['UserMapID']);
                  $Users = $DB->query('Select distinct UserMapID,UserName FROM ' . MySQL_Pre . 'MPR_ViewMappedUsers');
                  foreach ($Users as $User) {
                    echo '<option value="' . $User['UserMapID'] . '">'
                      . $User['UserMapID'] . ' - ' . htmlentities($User['UserName']) . '</option>';
                  } ?>
                </select>
            </div>
            <div style="clear: both;"></div>
            <hr/>
        </form>
        <div id="DataTable">
          <?php
          $DB = new MySQLiDBHelper();

          WebLib::ShowTable($DB->query('Select `Year`,`SchemeName` as `Scheme`,'
            . '`Funds` as `Sanctioned Funds`,`Expense` as `Expenditure`,`Balance` '
            . 'from ' . MySQL_Pre . 'MPR_ViewSchemeWiseFunds'));
          unset($DB);
          ?>
        </div>
    </div>
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
