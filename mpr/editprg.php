<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Monthly Performance Report');
WebLib::IncludeCSS();
//WebLib::CreateDB();
WebLib::IncludeCSS('css/forms.css');
WebLib::IncludeCSS('css/Style.css');
$pid = $_SESSION['pid'];

if (isset($_POST['BtnEdtPrg']) == 1) {
  require_once __DIR__ . '/../lib.inc.php';
  $DB                              = new MySQLiDBHelper();
  $insertdata['ExpenditureAmount'] = $_POST['txtAmount'];
  $insertdata['Balance']           = $_POST['txtBalance'];
  $insertdata['Date']              = $_POST['txtDate'];
  $insertdata['Remarks']           = $_POST['txtRemark'];
  $DB->where('ProgressID', $pid);
  $PrgID = $DB->update(MySQL_Pre . 'MPR_Progress', $insertdata);
}

$DB = new MySQLiDBHelper();
$DB->where('ProgressID', $pid);
$prg    = $DB->get(MySQL_Pre . 'MPR_Progress');
$workid = $prg[0]['WorkID'];
$DB->where('WorkID', $workid);
$work = $DB->get(MySQL_Pre . 'MPR_Works');
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
                <h3>Edit Progress Details</h3></legend>
            <form action="" method="post">
                Work Details:<input type="text" class="form-TxtInput"
                                    name="txtwork" disabled
                                    value="<?php echo $work[0]['WorkDescription'] ?>">
                Expenditure Amount:<input type="text" class="form-TxtInput"
                                          name="txtAmount"
                                          value="<?php echo $prg[0]['ExpenditureAmount'] ?>">
                Balance:<input type="text" name="txtBalance"
                               class="form-TxtInput"
                               value="<?php echo $prg[0]['Balance'] ?>">
                Date:<input type="date" name="txtDate" class="form-TxtInput"
                            value="<?php echo $prg[0]['Date'] ?>">
                Remarks:<input type="text" name="txtRemark"
                               class="form-TxtInput"
                               value="<?php echo $prg[0]['Remarks'] ?>">
                <input type="Submit" value="Update" name="BtnEdtPrg">
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

