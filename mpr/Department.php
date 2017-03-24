<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Departments');
WebLib::IncludeCSS();
WebLib::JQueryInclude();
//WebLib::IncludeJS('mpr/js/forms.js');
WebLib::IncludeJS('mpr/js/mpr.js');
WebLib::IncludeCSS('mpr/css/forms.css');
WebLib::IncludeCSS('css/chosen.css');
WebLib::IncludeJS('js/chosen.jquery.min.js');
?>
</head>
<body>
  <div class="TopPanel">
    <div class="LeftPanelSide"></div>
    <div class="RightPanelSide"></div>
    <h1><?php echo AppTitle; ?></h1>
  </div>
  <div class="Header"></div>
  <?php
  WebLib::ShowMenuBar('MPR');
  ?>
  <div class="content">
    <div class="formWrapper">
      <form method="post" action="Department.php"><?php
              include __DIR__ . '/DataMPR.php';
              WebLib::ShowMsg();
              ?>
        <fieldset>
          <legend>Create New Department & Sector</legend>

          <div class="FieldGroup">
            <label for="DeptName"><span id="myfont">Name of Department</span>
              <input type="text" name ="DeptName" id="DeptName"
                     placeholder="Name of Department"/>
            </label>
            <div style="clear: both;"></div>
            <hr/>
            <div class="formControl">
              <input type="submit" name="CmdSubmit" value="Create Department">
              <input type="hidden" name="FormToken"
                     value="<?php echo WebLib::GetVal($_SESSION, 'FormToken')
              ?>
                     " />
            </div>
          </div>
          <div class="FieldGroup">
            <label for="SectorName"><span id="myfont">Name of Sector</span>
              <input type="text" id="SectorName" name="SectorName"
                     placeholder="Name of Sector"/>
            </label>
            <div style="clear: both;"></div>
            <hr/>
            <div class="formControl">
              <input type="submit" name="CmdSubmit" value="Create Sector">
              <input type="hidden" name="FormToken"
                     value="<?php
                     echo WebLib::GetVal($_SESSION, 'FormToken')
                     ?>" />
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>Create New Scheme</legend>

          <div class="FieldGroup">
            <label for="DeptID"><span id="myfont">Select Name of Department</span>
              <select id="DeptID" name="DeptID"
                      data-placeholder="Select Department"
                      >
              </select></label>
          </div>
          <div class="FieldGroup">
            <label for="SectorID"><span id="myfont">Select Name of Sectors</span>
              <select id="SectorID" name="SectorID"
                      data-placeholder="Select Sector"
                      >
              </select></label>
          </div>
          <div class="FieldGroup">
            <label for="SchemeName"><span id="myfont">Name of Schemes</span>
              <input type="text" name="SchemeName" id="SchemeName"
                     placeholder="Name of Schemes"/>
            </label>
          </div>
          <div style="clear: both;"></div>
          <hr/>
          <div class="formControl">
            <input type="submit" name="CmdSubmit" value="Create Scheme">
            <input type="reset" name="CmdReset" value="Reset">
          </div>

          <input type="hidden" name="FormToken"
                 value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>" />
        </fieldset>
        <fieldset>
          <legend>Create New Project</legend>
          <?php
          $Data  = new MySQLiDB();
          $Data1 = new MySQLiDBHelper();
          ?>
          <div class="FieldGroup">
            <label for="SchemeName"><span id="myfont">Select Scheme</span>
              <select id="SchemeID" name="SchemeID"
                      data-placeholder="Select Scheme">
              </select>
            </label>
          </div>
          <div class="FieldGroup">
            <label for="ProjectName"><span id="myfont">Name of Project</span>
              <input type="text" name="ProjectName" id="ProjectName"
                     placeholder="Name of Projects"/>
            </label>
          </div>
          <div class="FieldGroup">
            <label for="ProjectCost"><span id="myfont">Project Cost</span>
              <input type="text" name="ProjectCost" id="ProjectCost"
                     placeholder="Project Cost"/>
            </label>
          </div>
          <div class="FieldGroup">
            <label for="AlotmentAmount"><span id="myfont">
                Project Allotment Amount</span>
              <input type="text" name="AlotmentAmount" id="AlotmentAmount"
                     placeholder="Project Allotment Amount"/>
            </label>
          </div>
          <div class="FieldGroup">
            <label for="StartDate"><span id="myfont">Project Start Date</span>
              <input type="text" id="StartDate" name="StartDate"
                     placeholder="YYYY-MM-DD" size="12" />
            </label>
          </div>
          <div class="FieldGroup">
            <label for="AlotmentDate"><span id="myfont">Project Allotment Date
              </span>
              <input type="text" id="AlotmentDate" name="AlotmentDate"
                     placeholder="YYYY-MM-DD" size="12"  />
            </label>
          </div>
          <div class="FieldGroup">
            <label for="TenderDate"><span id="myfont">Project Tender Date</span>
              <input type="text" id="TenderDate" name="TenderDate"
                     placeholder="YYYY-MM-DD" size="12" />
            </label>
          </div>
          <div class="FieldGroup">
            <label for="WorkOrderDate"><span id="myfont">Project Work Order Date</span>
              <input type="text" id="WorkOrderDate" name="WorkOrderDate"
                     placeholder="YYYY-MM-DD"  size="12" />
            </label>
          </div>
          <div style="clear: both;"></div>
          <hr/>

          <div class="formControl">
            <input type="submit" name="CmdSubmit" value="Create Project">
            <input type="reset" name="CmdReset" value="Reset">
            <input type="hidden" name="FormToken"
                   value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>" />
          </div>
        </fieldset>
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

