<?php

require_once(__DIR__ . '/../lib.inc.php');

$Data               = new MySQLiDBHelper();
$_SESSION['action'] = 0;
$Query              = '';
if (WebLib::GetVal($_POST, 'FormToken') !== null) {
  if (WebLib::GetVal($_POST, 'FormToken') !==
    WebLib::GetVal($_SESSION, 'FormToken')) {
    $_SESSION['action'] = 1;
  } else {
    // Authenticated Inputs
    switch (WebLib::GetVal($_POST, 'CmdSubmit')) {
      case 'Create Department':
        $DataMPR['DeptName'] = WebLib::GetVal($_POST, 'DeptName', true);
        if (strlen($DataMPR['DeptName']) > 2) {
          $DataMPR['UserMapID'] = $_SESSION['UserMapID'];
          $Query                = MySQL_Pre . 'MPR_Departments';
          $_SESSION['Msg']      = 'Department Created Successfully!';
        } else {
          $Query           = '';
          $_SESSION['Msg'] = 'Department Name must be at least 3 characters or more.';
        }
        break;

      case 'Create Sector':
        $DataMPR['SectorName'] = WebLib::GetVal($_POST, 'SectorName');
        if (strlen($DataMPR['SectorName']) > 2) {
          $DataMPR['UserMapID'] = $_SESSION['UserMapID'];
          $Query                = MySQL_Pre . 'MPR_Sectors';
          $_SESSION['Msg']      = 'Sector Created Successfully!';
        } else {
          $Query           = '';
          $_SESSION['Msg'] = 'Sector Name must be at least 3 characters or more.';
        }
        break;

      case 'Create Scheme':
        $DataMPR['SchemeName'] = WebLib::GetVal($_POST, 'SchemeName');
        $DataMPR['DeptID']     = WebLib::GetVal($_POST, 'DeptID');
        $DataMPR['SectorID']   = WebLib::GetVal($_POST, 'SectorID');
        if ((strlen($DataMPR['SchemeName']) > 2) && ($DataMPR['DeptID'] !== null) && ($DataMPR['SectorID'] !== null)) {
          $DataMPR['UserMapID'] = $_SESSION['UserMapID'];
          $Query                = MySQL_Pre . 'MPR_Schemes';
          $_SESSION['Msg']      = 'Scheme Created Successfully!';
        } else {
          $Query           = '';
          $_SESSION['Msg'] = 'Scheme Name must be at least 3 characters or more.';
        }
        break;

      case 'Create Project':
        $DataMPR['ProjectName']    = WebLib::GetVal($_POST, 'ProjectName');
        $DataMPR['ProjectCost']    = WebLib::GetVal($_POST, 'ProjectCost');
        $DataMPR['AlotmentAmount'] = WebLib::GetVal($_POST, 'AlotmentAmount');
        $DataMPR['StartDate']      = WebLib::ToDBDate(WebLib::GetVal($_POST,
          'StartDate'));
        $DataMPR['AlotmentDate']   = WebLib::ToDBDate(WebLib::GetVal($_POST,
          'AlotmentDate'));
        $DataMPR['TenderDate']     = WebLib::ToDBDate(WebLib::GetVal($_POST,
          'TenderDate'));
        $DataMPR['WorkOrderDate']  = WebLib::ToDBDate(WebLib::GetVal($_POST,
          'WorkOrderDate'));
        $DataMPR['SchemeID']       = WebLib::GetVal($_POST, 'SchemeID');

        if ((strlen($DataMPR['ProjectName']) > 2) && ($DataMPR['SchemeID'] !== null)) {
          $DataMPR['UserMapID'] = $_SESSION['UserMapID'];
          $Query                = MySQL_Pre . 'MPR_Projects';
          $_SESSION['Msg']      = 'Project Created Successfully!';
        } else {
          $Query           = '';
          $_SESSION['Msg'] = 'Project Name must be at least 3 characters or more.';
        }
        break;

      case 'Create Progress':
        $DataMPR['ProjectID']         = WebLib::GetVal($_POST, 'ProjectID');
        $DataMPR['ReportDate']        = WebLib::ToDBDate(WebLib::GetVal($_POST,
          'ReportDate'));
        $DataMPR['PhysicalProgress']  = WebLib::GetVal($_POST,
          'PhysicalProgress');
        $DataMPR['FinancialProgress'] = WebLib::GetVal($_POST,
          'FinancialProgress');
        $DataMPR['Remarks']           = WebLib::GetVal($_POST, 'Remarks');
        if ((strlen($DataMPR['Remarks']) > 2) && ($DataMPR['ProjectID'] !== null)) {
          $DataMPR['UserMapID'] = $_SESSION['UserMapID'];
          $Query                = MySQL_Pre . 'MPR_Progress';
          $_SESSION['Msg']      = 'Progress Created Successfully!';
        }
        break;
      case 'GetREPORTData':
        $_SESSION['POST'] = $_POST;
        $Query            = 'Select `ReportID`, `UserMapID`, `ReportDate`, '
          . '`ProjectID`, `PhysicalProgress`, `FinancialProgress`, `Remarks`'
          . ' From `' . MySQL_Pre . 'MPR_Progress`'
          . ' Where `ProjectID`=?';
        doQuery($DataResp, $Query, array(WebLib::GetVal($_POST, 'ProjectID')));
        break;
    }
    if ($Query !== '') {
      $Inserted = $Data->insert($Query, $DataMPR);
      if ($Inserted === false) {
        $_SESSION['Msg'] = 'Unable to ' . WebLib::GetVal($_POST, 'CmdSubmit') . '!';
      }
    }
  }
}
$_SESSION['FormToken'] = md5($_SERVER['REMOTE_ADDR'] . session_id() . microtime());
unset($DataMPR);
unset($Data);

/**
 * Perfroms Select Query to the database
 *
 * @param ref    $DataResp
 * @param string $Query
 * @param array  $Params
 * @example GetData(&$DataResp, "Select a,b,c from Table Where c=? Order By b
 *          LIMIT ?,?", array('1',30,10))
 */
function doQuery(&$DataResp,
                 $Query,
                 $Params = null) {
  $Data             = new MySQLiDBHelper();
  $Result           = $Data->rawQuery($Query, $Params);
  $DataResp['Data'] = $Result;
  $DataResp['Msg']  = 'Total Rows: ' . count($Result);
  unset($Result);
  unset($Data);
}

?>
