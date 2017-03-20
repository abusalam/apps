<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Projects');
WebLib::IncludeCSS();
WebLib::JQueryInclude();
WebLib::IncludeCSS('css/chosen.css');
WebLib::IncludeJS('mpr/js/forms.js');
WebLib::IncludeCSS('mpr/css/forms.css');
WebLib::IncludeJS('js/chosen.jquery.min.js');
?>
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
  WebLib::ShowMenuBar('MPR');
  ?>
  <div class="content">
    <div class="formWrapper">
      <form method="post" action="<?php
      echo WebLib::GetVal($_SERVER, 'PHP_SELF');
      ?>">
        <h3>Project</h3>
        <label for="ProjectName"><strong>Name of Project</strong>
        </label>
        <input type="text" name="ProjectName" id="ProjectName"
               placeholder="Name of Projects"/>
        <div class="FieldGroup">
          <label for="SchemeName"><strong>Name of Scheme</strong></label><br/>
          <select id="SchemeID" name="SchemeID" data-placeholder="Select Scheme"
                  class="chzn-select">
                    <?php
                    $Query = 'Select `SchemeID`, `SchemeName` '
                        . ' FROM `' . MySQL_Pre . 'MPR_Schemes` '
                        . ' Order By `SchemeID`';
                    $Data->show_sel('SchemeID', 'SchemeName', $Query,
                                    WebLib::GetVal($_POST, 'SchemeID'));
                    ?>
          </select>
        </div>
        <div class="FieldGroup">
          <label for="ProjectCost"><strong>Project Cost</strong>
          </label>
          <input type="text" name="ProjectCost" id="ProjectCost"
                 placeholder="Project Cost"/>
        </div>
        <div class="FieldGroup">
          <label for="StartDate"><strong>Project Start Date</strong>
          </label>
          <input type="text" id="StartDate" name="StartDate"
                 class="DatePicker" placeholder="YYYY-MM-DD" />
        </div>
        <div class="FieldGroup">
          <label for="AlotmentAmount"><strong>Project Allotment Amount</strong>
          </label>
          <input type="text" name="AlotmentAmount" id="AlotmentAmount"
                 placeholder="Project Allotment Amount"/>
        </div>
        <div class="FieldGroup">
          <label for="AlotmentDate"><strong>Project Allotment Date</strong>
          </label>
          <input type="text" id="AlotmentDate" name="AlotmentDate"
                 class="DatePicker" placeholder="YYYY-MM-DD" />
        </div>
        <div class="FieldGroup">
          <label for="TenderDate"><strong>Project Tender Date</strong>
          </label>
          <input type="text" id="TenderDate" name="TenderDate"
                 class="DatePicker" placeholder="YYYY-MM-DD" />
        </div>
        <div class="FieldGroup">
          <label for="WorkOrderDate"><strong>Project Work Order Date</strong>
          </label>
          <input type="text" id="WorkOrderDate" name="WorkOrderDate"
                 class="DatePicker" placeholder="YYYY-MM-DD" />
        </div>
        <div class="formControl">
          <input type="submit" name="CmdSubmit" value="Create Project">
        </div></div>
    <input type="hidden" name="FormToken"
           value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>" />
  </form>
</div>
<?php
unset($Data);
unset($Data1);
WebLib::ShowMsg();
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
