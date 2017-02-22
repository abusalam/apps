<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Monthly Performance Report');
WebLib::IncludeCSS();
WebLib::IncludeCSS('css/forms.css');
WebLib::IncludeCSS('css/Style.css');
//WebLib::CreateDB();

$wid = $_SESSION['wid'];
if (isset($_POST['BtnEdtWrk']) == 1) {
  require_once __DIR__ . '/../lib.inc.php';
  $DB = new MySQLiDBHelper();
  $insertdata['AllotmentAmount'] = $_POST['txtAmount'];
  $insertdata['WorkDescription'] = $_POST['txtWork'];
  $insertdata['EstimatedCost'] = $_POST['txtCost'];
  $DB->where('WorkID', $wid);
  $WorkID = $DB->update(MySQL_Pre . 'MPR_Works', $insertdata);
  echo "<script>alert('Data has been updated')</script>";
  echo "<script>window.location.href='Works.php'</script>";
}

$DB = new MySQLiDBHelper();
$DB->where('WorkID', $wid);
$work = $DB->get(MySQL_Pre . 'MPR_Works');
$schemeid = $work[0]['SchemeID'];
$DB->where('SchemeID', $schemeid);
$SchemeName = $DB->get(MySQL_Pre . 'MPR_Schemes');
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
  <div class="formWrapper-Autofit">
    <fieldset>
      <legend>
        <h3>Edit Work Details</h3></legend>
      <form action="" method="post">
        Scheme ID:<input type="text" class="form-TxtInput" name="txtSchemeName" disabled
                         value="<?php echo $SchemeName[0]['SchemeName'] ?>">
        Description of Work:<input type="text" class="form-TxtInput" name="txtWork"
                                   value="<?php echo $work[0]['WorkDescription'] ?>">
        Estimated Cost:<input type="text" class="form-TxtInput" name="txtCost"
                              value="<?php echo $work[0]['EstimatedCost'] ?>">
        Allotment Amount:<input type="text" class="form-TxtInput" name="txtAmount"
                                value="<?php echo $work[0]['AllotmentAmount'] ?>">
        <input type="Submit" value="Update" name="BtnEdtWrk">
      </form>
    </fieldset>
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

