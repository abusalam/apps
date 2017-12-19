<?php
if (WebLib::GetVal($_POST, 'FormToken') !== null) {
  if (WebLib::GetVal($_POST, 'FormToken') == WebLib::GetVal($_SESSION, 'FormToken')) {
    switch (WebLib::GetVal($_POST, 'CmdAction')) {
      case 'Add User':
        $DB                     = new MySQLiDBHelper();
        $tableData['CtrlMapID'] = $_SESSION['UserMapID'];;
        $tableData['UserMapID'] = WebLib::GetVal($_POST, 'UserID');
        $MprMapID               = $DB->insert(MySQL_Pre . 'MPR_UserMaps', $tableData);
        if ($MprMapID > 0) {
          $_SESSION['Msg'] = "User Added Successfully!";
        } else {
          $_SESSION['Msg'] = "Unable to Add User!";
        }
        unset($DB);
        break;

      case 'Remove User':
        $DB = new MySQLiDBHelper();
        $DB->where('UserMapID', WebLib::GetVal($_POST, 'UserID'));
        $DB->where('CtrlMapID', $_SESSION['UserMapID']);
        $Deleted = $DB->delete(MySQL_Pre . 'MPR_UserMaps');
        if ($Deleted > 0) {
          $_SESSION['Msg'] = "User Removed Successfully!";
        } else {
          $_SESSION['Msg'] = "Unable to Remove User!";
        }
        unset($DB);
        break;
    }
  } else {
    $_SESSION['Msg'] .= "Request may have been modified!";
  }
}
$_SESSION['FormToken'] = md5($_SERVER['REMOTE_ADDR'] . session_id() . microtime());
?>
