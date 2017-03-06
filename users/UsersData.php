<?php

/*
 * @ todo Fetch District AC and Parts Combo Data on seperate request via ajax
 * @todo Keep All District, AC, Parts available for parent users
 */

$DB = new MySQLiDBHelper();
$Inserted = 0;
$RunQuery = true;
$_SESSION['action'] = 0;
$Query = '';
if (WebLib::GetVal($_POST, 'FormToken') !== null) {
  if (WebLib::GetVal($_POST, 'FormToken') !==
    WebLib::GetVal($_SESSION, 'FormToken')
  ) {
    $_SESSION['action'] = 1;
  } else {
    // Authenticated Inputs
    switch (WebLib::GetVal($_POST, 'CmdSubmit')) {
      case 'Create':
        if (strlen(WebLib::GetVal($_POST, 'UserName')) > 2) {
          $Query = 'Insert Into `' . MySQL_Pre . 'Users` '
            . '(`UserName`,`CtrlMapID`,`Registered`,`Activated`)'
            . ' Values(\'' . WebLib::GetVal($_POST, 'UserName', true)
            . '\',' . WebLib::GetVal($_SESSION, 'UserMapID', true) . ',0,0)';
        } else {
          $Query           = '';
          $_SESSION['Msg'] = 'UserName must be at least 3 characters or more.';
        }
        break;

      case 'Impersonate':
        if (WebLib::GetVal($_POST, 'UserMapID') !== null) {
          if (WebLib::GetVal($_SESSION, 'ImpFromUserMapID') === null) {
            $_SESSION['ImpFromUserMapID'] = $_SESSION['UserMapID'];
            $_SESSION['ImpFromUserName']  = $_SESSION['UserName'];
          }
          $_SESSION['UserMapID'] = WebLib::GetVal($_POST, 'UserMapID');

          $DB->where("UserMapID", $_SESSION['UserMapID']);
          $User = $DB->query('Select UserName ' . ' From `' . MySQL_Pre . 'Users`');
          $_SESSION['UserName']    = 'Impersonated-' . $User[0]['UserName'];
          $_SESSION['Msg']         = $_SESSION['UserName'];
          $_SESSION['ReloadMenus'] = true;
        } else {
          $_SESSION['Msg'] = 'Select the User to Impersonate!';
        }
        break;

      case 'Stop Impersonating':
        if (WebLib::GetVal($_SESSION, 'ImpFromUserMapID') !== null) {
          $_SESSION['UserMapID'] = $_SESSION['ImpFromUserMapID'];
          $_SESSION['UserName']  = $_SESSION['ImpFromUserName'];
          unset($_SESSION['ImpFromUserMapID']);
          unset($_SESSION['ImpFromUserName']);
          $_SESSION['ReloadMenus'] = true;
        }
        break;

      case 'Activate':
        $DB->where('Activated', 0);
        $DB->where('CtrlMapID', WebLib::GetVal($_SESSION, 'UserMapID', true));
        $DB->where('UserMapID', WebLib::GetVal($_POST, 'UserMapID'));
        $Inserted = $DB->update(MySQL_Pre . 'Users', array('Activated' => 1));

        $QueryUser = 'Select `UserName`,`UserID` '
          . ' FROM `' . MySQL_Pre . 'Users`';
        $DB->where('UserMapID', WebLib::GetVal($_POST, 'UserMapID'));
        $Rows = $DB->query($QueryUser);
        $User = $Rows[0];
        unset($Rows);

        $Subject = 'User Account Activated';
        $Body    = '<span>Your UserID: <b>' . $User['UserID']
          . '</b> is now Activated</span>';

        $RunQuery = false;
        break;

      case 'De-Activate':
        $DB->where('Activated', 1);
        $DB->where('CtrlMapID', WebLib::GetVal($_SESSION, 'UserMapID', true));
        $DB->where('UserMapID', WebLib::GetVal($_POST, 'UserMapID'));
        $Inserted = $DB->update(MySQL_Pre . 'Users', array('Activated' => 0));
        
        $DB->where("UserMapID", $_SESSION['UserMapID']);
        $User = $DB->query('Select `UserName`,`UserID`' . ' From `' . MySQL_Pre . 'Users`');

        $Subject = 'User Account De-Activated - Paschim Medinipur District Portal';
        $Body    = '<span>Your UserID: <b>' . $User[0]['UserID']
          . '</b> is now De-Activated</span>';

        $RunQuery = false;
        break;

      case 'Reset Password':
        $Pass = WebLib::GeneratePassword(10, 2, 2, 2);
        $DB->where('Registered', 1);
        $DB->where('Activated', 1);
        $DB->where('CtrlMapID', WebLib::GetVal($_SESSION, 'UserMapID', true));
        $DB->where('UserMapID', WebLib::GetVal($_POST, 'UserMapID'));

        $Inserted = $DB->update(MySQL_Pre . 'Users', array('UserPass' => md5($Pass)));

        $QueryUser = 'Select `UserName`,`UserID`,`MobileNo`'
          . ' FROM `' . MySQL_Pre . 'Users`';
        $DB->where('UserMapID', WebLib::GetVal($_POST, 'UserMapID'));
        $Rows = $DB->query($QueryUser);
        $User = $Rows[0];
        unset($Rows);

        $TxtBody = 'UserID: ' . $User['UserID'] . "\r\n" . 'Password: ' . $Pass;
        $SentSMS = '';
        if ($Inserted > 0) {
          if (UseSMSGW === true) {
            SMSGW::SendSMS($TxtBody, $User['MobileNo']);
            $_SESSION['Msg'] = 'Password Sent To: ' . $User['MobileNo'] . '<br/>';
          } else {
            $_SESSION['Msg'] = 'Password has been Reset: ' . $TxtBody . '<br/>';
          }
        }

        $Subject  = 'User Account Password Reset - Paschim Medinipur District Portal';
        $Body     = '<span>Password Your UserID: <b>' . $User['UserID']
          . '</b> is: </span>' . $Pass . '<br/>'
          . '<b>Please Login to change the Current Password</b>';
        $RunQuery = false;
        break;
    }
    if ($Query !== '') {
      if ($RunQuery) {
        $Inserted = $DB->ddlQuery($Query);
      }
      if ($Inserted > 0) {
        if (WebLib::GetVal($_POST, 'CmdSubmit') === 'Create') {
          $_SESSION['Msg'] = 'User Created Successfully!';
        } else {
          if (WebLib::GetVal($User, 'UserID')) {
            $GmailResp = GMailSMTP($User['UserID'], $User['UserName'], $Subject, $Body);
            $Mail      = json_decode($GmailResp);
            if ($Mail->Sent) {
              if (WebLib::GetVal($_SESSION, 'Msg') === '') {
                $_SESSION['Msg'] = 'User '
                  . WebLib::GetVal($_POST, 'CmdSubmit') . 'd Successfully!';
              }
            } else {
              $_SESSION['Msg'] = 'Action completed Successfully!'
                . ' But Unable to Send eMail!';
            }
          }
        }
      } else {
        $_SESSION['Msg'] = 'Unable to ' . WebLib::GetVal($_POST, 'CmdSubmit') . '!' . $Inserted;
      }
    }
  }
}
$_SESSION['FormToken'] = md5($_SERVER['REMOTE_ADDR'] . session_id() . microtime());
unset($Mail);
unset($GmailResp);
unset($DB);
?>
