<?php
if (WebLib::GetVal($_POST, 'FormToken') !== null) {
  if (WebLib::GetVal($_POST, 'FormToken') == WebLib::GetVal($_SESSION, 'FormToken')) {
    if (isset($_POST['BtnCreScheme']) == 1) {
      $DB                      = new MySQLiDBHelper();
      $tableData['SchemeName'] = WebLib::GetVal($_POST, 'txtSchemeName');
      $tableData['UserMapID']  = $_SESSION['UserMapID'];
      $SchemeID                = $DB->insert(MySQL_Pre . 'MPR_Schemes', $tableData);
      if ($SchemeID > 0) {
        $_SESSION['Msg'] = "Scheme Created Successfully!";
      } else {
        $_SESSION['Msg'] = "Unable to Create Scheme!";
      }
    }

    if (isset($_POST['BtnScheme']) == 1) {
      $DB = new MySQLiDBHelper();
      $DB->where('UserMapID', $_SESSION['UserMapID']);
      $DB->where('SchemeID', WebLib::GetVal($_POST, 'Scheme'));
      $Schemes = $DB->get(MySQL_Pre . 'MPR_Schemes');
      if (count($Schemes) > 0) {
        $tableData['SchemeID'] = $_POST['Scheme'];
        $tableData['Amount']   = $_POST['txtAmount'];
        $tableData['OrderNo']  = $_POST['txtOrderNo'];
        $tableData['Date']     = WebLib::ToDBDate($_POST['txtDate']);
        $tableData['Year']     = $_POST['txtYear'];
        $SchemeID              = $DB->insert(MySQL_Pre . 'MPR_SchemeAllotments', $tableData);
        if ($SchemeID > 0) {
          $_SESSION['Msg'] = "Allotment Saved Successfully!";
        } else {
          $_SESSION['Msg'] .= "Unable to Save Allotment!";
        }
      } else {
        $_SESSION['Msg'] .= "Invalid Scheme: Unable to Save Allotment!";
      }
    }
  } else {
    $_SESSION['Msg'] .= "Request may have been modified!";
  }
}
$_SESSION['FormToken'] = md5($_SERVER['REMOTE_ADDR'] . session_id() . microtime());
?>
