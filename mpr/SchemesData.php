<?php
if (isset($_POST['BtnCreScheme']) == 1) {
  $DB = new MySQLiDBHelper();
  $tableData['SchemeName'] = $_POST['txtSchemeName'];
  $tableData['UserMapID'] = $_SESSION['UserMapID'];
  $SchemeID = $DB->insert(MySQL_Pre . 'MPR_Schemes', $tableData);
  if ($SchemeID > 0) {
    $_SESSION['Msg'] = "Scheme Created Successfully!";
  }
  else {
    $_SESSION['Msg'] = "Unable to Create Scheme!";
  }
}

if (isset($_POST['BtnScheme']) == 1) {
  $DB = new MySQLiDBHelper();
  $tableData['SchemeID'] = $_POST['Scheme'];
  $tableData['Amount'] = $_POST['txtAmount'];
  $tableData['OrderNo'] = $_POST['txtOrderNo'];
  $tableData['Date'] = WebLib::ToDBDate($_POST['txtDate']);
  $tableData['Year'] = $_POST['txtYear'];
  $SchemeID = $DB->insert(MySQL_Pre . 'MPR_SchemeAllotments', $tableData);
  if ($SchemeID > 0) {
    $_SESSION['Msg'] = "Allotment Saved Successfully!";
  }
  else {
    $_SESSION['Msg'] .= "Unable to Save Allotment!";
  }
}
?>
