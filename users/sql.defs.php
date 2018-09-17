<?php

function CreateSchemas() {
  $ObjDB = new MySQLiDBHelper();
  $ObjDB->ddlQuery(SQLDefs('MenuData'));
  unset($ObjDB);
}

function SQLDefs($ObjectName) {
  $SqlDB = '';
  switch ($ObjectName) {
    case 'MenuData':
      $SqlDB = 'INSERT INTO `' . MySQL_Pre . 'MenuItems` '
        . '(`AppID`,`MenuOrder`,`AuthMenu`,`Caption`,`URL`,`Activated`) VALUES'
        . '(\'USER\', 1, 0, \'Home\', \'index.php\', 1),'
        . '(\'USER\', 2, 1, \'My Profile\', \'users/Profile.php\', 1),'
        . '(\'USER\', 3, 1, \'Manage Users\', \'users/Users.php\', 1),'
        . '(\'USER\', 4, 1, \'Manage Menus\', \'users/MenuACL.php\', 1),'
        . '(\'USER\', 5, 1, \'User Activity\', \'users/AuditLogs.php\', 1),'
        . '(\'USER\', 6, 1, \'Log Out!\', \'login.php?LogOut=1\', 1);';
      break;
  }
  return $SqlDB;
}

?>
