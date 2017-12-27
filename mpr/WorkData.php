<?php
if (WebLib::GetVal($_POST, 'FormToken') != NULL) {
  if (WebLib::GetVal($_POST, 'FormToken') == WebLib::GetVal($_SESSION, 'FormToken')) {
    switch (WebLib::GetVal($_POST, 'CmdAction')) {

      case 'Create Work':
        $DB                           = new MySQLiDBHelper();
        $tableData['SchemeID']        = $_POST['Scheme'];
        $tableData['MprMapID']        = $_POST['MprMapID'];
        $tableData['WorkDescription'] = $_POST['txtWork'];
        $tableData['EstimatedCost']   = $_POST['txtCost'];
        $tableData['WorkRemarks']     = $_POST['txtWorkRemarks'];
        $SchemeID                     = $DB->insert(MySQL_Pre . 'MPR_Works', $tableData);
        if($SchemeID){
          $_SESSION['Msg'] = "Work Created!";
        } else {
          $_SESSION['Msg'] = "Unable to Create Work!";
        }
        unset($tableData);
        unset($DB);
        break;

      case 'Release Fund':
        $DB                           = new MySQLiDBHelper();
        $tableData['WorkID']          = $_POST['WorkID'];
        $tableData['SanctionOrderNo'] = $_POST['txtOrderNo'];
        $tableData['SanctionDate']    = WebLib::ToDBDate($_POST['txtDate']);
        $tableData['SanctionAmount']  = $_POST['txtAmount'];
        $tableData['SanctionRemarks'] = $_POST['txtSanctionRemarks'];
        $SchemeID                     = $DB->insert(MySQL_Pre . 'MPR_Sanctions', $tableData);
        if($SchemeID){
          $_SESSION['Msg'] = "Fund Released!";
        } else {
          $_SESSION['Msg'] = "Unable to Release Fund!";
        }
        unset($tableData);
        unset($DB);
        break;
    }
  } else {
    $_SESSION['Msg'] = "Request may have been modified!";
  }
}
$_SESSION['FormToken'] = md5($_SERVER['REMOTE_ADDR'] . session_id() . microtime());
?>
<span class="Message" id="Msg" style="float: right;"></span>
<pre id="Error">
   <?php //print_r($_POST); ?>
</pre>