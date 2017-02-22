<?php
require_once __DIR__ . '/../lib.inc.php';
WebLib::AuthSession();
WebLib::Html5Header('Monthly Performance Report');
WebLib::IncludeCSS();
//WebLib::CreateDB();
WebLib::IncludeCSS('css/forms.css');
WebLib::IncludeCSS('css/Style.css');

if (isset($_POST['BtnAll']) == 1) {
  require_once __DIR__ . '/../lib.inc.php';
  $DB = new MySQLiDBHelper();
  $insertdata['SchemeID'] = $_POST['Scheme'];
  $insertdata['BlockID'] = $_POST['Block'];
  $insertdata['AllotmentDate'] = $_POST['txtDate'];
  $insertdata['Amount'] = $_POST['txtAmount'];
  //$insertdata['UserMapID']="2";
  $SchemeID = $DB->insert(MySQL_Pre . 'MPR_Allotment', $insertdata);
}
$DB = new MySQLiDBHelper();
$Schemes = $DB->get(MySQL_Pre . 'MPR_Schemes');
$Block = $DB->get(MySQL_Pre . 'MPR_Users');
//$All = $DB->get(MySQL_Pre . 'MPR_Allotment');
$n = count($All);
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
  <fieldset>
    <legend>
      <h2>Allotment</h2></legend>
    <div class="formWrapper-Autofit">
      <fieldset>
        <legend>
          <h3>Create New Allotment</h3></legend>
        <form action="" method="post">
          Scheme Name:<select name="Scheme" class="form-TxtInput">
            <option>--Select Scheme--</option>
            <?php foreach ($Schemes as $SchemeID) {
              echo '<option value="' . $SchemeID['SchemeID'] . '">' . $SchemeID['SchemeName'] . '</option>';
            } ?>
          </select>
          Block Name:<select name="Block" class="form-TxtInput">
            <option>--Select Block--</option>
            <?php foreach ($Block as $BlockID) {
              echo '<option value="' . $BlockID['BlockID'] . '">' . $BlockID['BlockName'] . '</option>';
            } ?>
          </select>

          <div class="FieldGroup">
            <label for="txtAmount"><strong>Allotment Amount:</strong><br/>
              <input id="txtAmount" type="text" name="txtAmount" class="form-TxtInput">
            </label>
          </div>
          <div class="FieldGroup">
            <label for="txtDate"><strong>Date of Allotment:</strong><br/>
              <input id="txtDate" type="text" name="txtDate" class="form-TxtInput">
            </label>
          </div>
          <input type="Submit" value="Create" name="BtnAll">
        </form>
      </fieldset>
    </div>
    <div class="formWrapper-Autofit">
      <fieldset>
        <legend>
          <h3>Allotment List</h3></legend>
        <table rules="all" frame="box" width="100%" cellpadding="5" cellspacing="2">
          <tr>
            <th>Scheme Name</th>
            <th>Block Name</th>
            <th>Allotment Amount(Rs.)</th>
            <th>Allotment Date</th>
            <th>Action</th>
          </tr>
          <?php $i = 0;
          while ($i < $n) {
            $schemeid = $All[$i]['SchemeID'];
            $blkid = $All[$i]['BlockID'];
            $DB->where('BlockID', $blkid);
            $block = $DB->get(MySQL_Pre . 'MPR_Blocks');
            $DB->where('SchemeID', $schemeid);
            $SchemeName = $DB->get(MySQL_Pre . 'MPR_Schemes'); ?>
            <tr>
              <td><?php echo $SchemeName[0]['SchemeName'] ?></td>
              <td><?php echo $block[0]['BlockName'] ?></td>
              <td><?php echo $All[$i]['Amount'] ?></td>
              <td><?php echo $All[$i]['AllotmentDate'] ?></td>
              <td><a href="savesessionall.php?aid=<?php echo $All[$i]['AllotmentID'] ?>">edit</a></td>
            </tr>
            <?php $i++;
          } ?>
        </table>
      </fieldset>
    </div>
  </fieldset>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
</body>
</html>

