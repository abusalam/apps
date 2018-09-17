<?php
$Data               = new MySQLiDBHelper();
$_SESSION['action'] = 0;
$Query              = '';
if (WebLib::GetVal($_POST, 'FormToken') !== null) {
  if (WebLib::GetVal($_POST, 'FormToken') !== WebLib::GetVal($_SESSION, 'FormToken')) {
    $_SESSION['action'] = 1;
  } else {
    if (isset($_POST['MenuID']) && isset($_POST['UserMapID'])) {
      // Authenticated Inputs
      switch (WebLib::GetVal($_POST, 'CmdMenuAction')) {
        case 'Restrict Menu':
          foreach ($_POST['UserMapID'] as $UserMapID) {
            foreach ($_POST['MenuID'] as $MenuID) {
              if ($UserMapID != 1) {
                $DataACL['UserMapID'] = $UserMapID;
                $DataACL['MenuID']    = $MenuID;
                $DataACL['AllowOnly'] = 0;
                $Data->insert(MySQL_Pre . 'MenuACL', $DataACL);
              }
            }
          }
          $_SESSION['Msg'] = 'Restricted Successfully!';
          break;
        case 'Allow Only Menu ACL':
          foreach ($_POST['UserMapID'] as $UserMapID) {
            foreach ($_POST['MenuID'] as $MenuID) {
              if ($UserMapID != 1) {
                $DataACL['AllowOnly'] = 1;
                $Data->where('UserMapID', $UserMapID);
                $Data->where('MenuID', $MenuID);
                $Data->update(MySQL_Pre . 'MenuACL', $DataACL);
              }
            }
          }
          $_SESSION['Msg'] = 'Restricted Successfully!';
          break;
        case 'Activate Menu Restriction':
          foreach ($_POST['UserMapID'] as $UserMapID) {
            foreach ($_POST['MenuID'] as $MenuID) {
              if ($UserMapID != 1) {
                $DataACL['AllowOnly'] = 0;
                $DataACL['Activated'] = 1;
                $Data->where('UserMapID', $UserMapID);
                $Data->where('MenuID', $MenuID);
                $Data->update(MySQL_Pre . 'MenuACL', $DataACL);
              }
            }
          }
          $_SESSION['Msg'] = 'Menu Restriction Activated Successfully!';
          break;
        case 'Deactivate Menu Restriction':
          foreach ($_POST['UserMapID'] as $UserMapID) {
            foreach ($_POST['MenuID'] as $MenuID) {
              if ($UserMapID != 1) {
                $DataACL['AllowOnly'] = 1;
                $DataACL['Activated'] = 0;
                $Data->where('UserMapID', $UserMapID);
                $Data->where('MenuID', $MenuID);
                $Data->update(MySQL_Pre . 'MenuACL', $DataACL);
              }
            }
          }
          $_SESSION['Msg'] = 'Menu Restriction Deactivated Successfully!';
          break;
      }
      if ($Query !== '') {
        $Inserted = $Data->do_ins_query($Query);
        if ($Inserted > 0) {
          $_SESSION['Msg'] = 'Action Completed Successfully!';
        } else {
          $_SESSION['Msg'] = 'Unable to '
            . WebLib::GetVal($_POST, 'CmdMenuAction') . '!';
        }
      }
    }
  }
}
$_SESSION['FormToken'] = md5($_SERVER['REMOTE_ADDR'] . session_id() . microtime());
unset($Data);
?>
