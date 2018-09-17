<?php

require_once(__DIR__ . '/../lib.inc.php');
if (!isset($_SESSION)) {
  session_start();
}

if (WebLib::GetVal($_POST, 'AjaxToken') === WebLib::GetVal($_SESSION, 'Token')) {
  $_SESSION['LifeTime']  = time();
  $_SESSION['RT']        = microtime(true);
  $_SESSION['CheckAuth'] = 'Valid';
  $DataResp['Data']      = array();
  $DataResp['Msg']       = '';
  switch (WebLib::GetVal($_POST, 'CallAPI')) {

    case 'GETDATA':
      $Data                  = new MySQLiDBHelper();
      $Query                 = 'SELECT `DeptID`,`DeptName`'
        . ' FROM `' . MySQL_Pre . 'MPR_Departments`'
        . ' Order by `DeptID`';
      $DataResp['DeptID']    = $Data->rawQuery($Query);
      $Query                 = 'SELECT `SectorID`,`SectorName`'
        . ' FROM `' . MySQL_Pre . 'MPR_Sectors`'
        . ' Order by `SectorID`';
      $DataResp['SectorID']  = $Data->rawQuery($Query);
      $Query                 = 'SELECT `SchemeID`,`SchemeName`'
        . ' FROM `' . MySQL_Pre . 'MPR_Schemes`'
        . ' Order by `SchemeID`';
      $DataResp['SchemeID']  = $Data->rawQuery($Query);
      $Query                 = 'SELECT `ProjectID`,`ProjectName`'
        . ' FROM `' . MySQL_Pre . 'MPR_Projects`'
        . ' Order by `ProjectID`';
      $DataResp['ProjectID'] = $Data->rawQuery($Query);
      break;

//      echo json_encode($DataResp);
//      unset($Data);
  }
  $_SESSION['Token']     = md5($_SERVER['REMOTE_ADDR'] . session_id() . $_SESSION['ET']);
  $_SESSION['LifeTime']  = time();
  $DataResp['AjaxToken'] = $_SESSION['Token'];
  $DataResp['RT']        = '<b>Response Time:</b> '
    . round(microtime(true) - WebLib::GetVal($_SESSION, 'RT'), 6) . ' Sec';
  //PHP 5.4+ is required for JSON_PRETTY_PRINT
  //@todo Remove PRETTY_PRINT for Production
  if (strnatcmp(phpversion(), '5.4') >= 0) {
    $AjaxResp = json_encode($DataResp, JSON_PRETTY_PRINT);
  } else {
    $AjaxResp = json_encode($DataResp); //WebLib::prettyPrint(json_encode($DataResp));
  }
  unset($DataResp);

  header('Content-Type: application/json');
  header('Content-Length: ' . strlen($AjaxResp));
  echo $AjaxResp;
  exit();
}
header("HTTP/1.1 404 Not Found");
exit();
?>