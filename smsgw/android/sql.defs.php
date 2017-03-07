<?php

function CreateSchemas() {
  $ObjDB = new MySQLiDBHelper();
  $ObjDB->ddlQuery(SQLDefs('SMS_Groups'));
  $ObjDB->ddlQuery(SQLDefs('SMS_Messages'));
  $ObjDB->ddlQuery(SQLDefs('SMS_Contacts'));
  $ObjDB->ddlQuery(SQLDefs('SMS_GroupDetails'));
  $ObjDB->ddlQuery(SQLDefs('SMS_GroupMembers'));
  $ObjDB->ddlQuery(SQLDefs('SMS_ViewContacts'));
  $ObjDB->ddlQuery(SQLDefs('SMS_Status'));
  unset($ObjDB);
}

function SQLDefs($ObjectName) {
  $SqlDB = '';
  switch ($ObjectName) {

    case 'SMS_Groups':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`GroupID` int NOT NULL AUTO_INCREMENT,'
        . '`GroupName` varchar(20) DEFAULT NULL,'
        . ' PRIMARY KEY (`GroupID`),'
        . ' UNIQUE KEY `GroupName` (`GroupName`)'
        . ') ENGINE=InnoDB  DEFAULT CHARSET = utf8;';
      break;

    case 'SMS_GroupDetails':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`ContactID` int(11) NOT NULL,'
        . '`GroupID` int(11) NOT NULL,'
        . 'PRIMARY KEY (`ContactID`,`GroupID`),'
        . 'FOREIGN KEY (`GroupID`) REFERENCES `' . MySQL_Pre . 'SMS_Groups` (`GroupID`) ON UPDATE CASCADE,'
        . 'FOREIGN KEY (`ContactID`) REFERENCES `' . MySQL_Pre . 'SMS_Contacts` (`ContactID`) ON UPDATE CASCADE'
        . ') ENGINE=InnoDB  DEFAULT CHARSET = utf8;';
      break;

    case 'SMS_Messages':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`MessageID` int NOT NULL AUTO_INCREMENT,'
        . '`UserID` varchar(10) DEFAULT NULL,'
        . '`GroupID` int DEFAULT NULL,'
        . '`MsgText` varchar(500) DEFAULT NULL,'
        . '`SentTime` timestamp NULL DEFAULT NULL,'
        . ' PRIMARY KEY (`MessageID`)'
        . ') ENGINE=InnoDB  DEFAULT CHARSET=utf8;';
      break;

    case 'SMS_Contacts':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`ContactID` int NOT NULL AUTO_INCREMENT,'
        . '`ContactName` varchar(50) DEFAULT NULL,'
        . '`Designation` varchar(50) NOT NULL,'
        . '`MobileNo` varchar(10) DEFAULT NULL,'
        . ' PRIMARY KEY (`ContactID`),'
        . ' UNIQUE KEY `MobileNo` (`MobileNo`)'
        . ') ENGINE=InnoDB  DEFAULT CHARSET = utf8;';
      break;

    case 'SMS_ViewContacts':
      $SqlDB = 'CREATE VIEW `' . MySQL_Pre . $ObjectName . '` AS SELECT '
        . '`C`.`ContactID` AS `ContactID`,`G`.`GroupID` AS `GroupID`,`C`.`MobileNo` AS `MobileNo` '
        . 'from (`' . MySQL_Pre . 'SMS_GroupDetails` `G` join `' . MySQL_Pre . 'SMS_Contacts` `C` '
        . 'on(`C`.`ContactID` = `G`.`ContactID`));';
      break;

    case 'SMS_GroupMembers':
      $SqlDB = 'CREATE VIEW `' . MySQL_Pre . $ObjectName . '` AS SELECT '
        . '`C`.`ContactID` AS `ContactID`,`C`.`ContactName` AS `ContactName`,`C`.`Designation` AS `Designation`,'
        . '`G`.`GroupID` AS `GroupID`,`C`.`MobileNo` AS `MobileNo` from (`' . MySQL_Pre . 'SMS_GroupDetails` `G` join '
        . '`' . MySQL_Pre . 'SMS_Contacts` `C` on((`C`.`ContactID` = `G`.`ContactID`))) ;';
      break;

    case 'SMS_Status':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`StatusID` int NOT NULL AUTO_INCREMENT,'
        . '`MessageID` int DEFAULT NULL,'
        . '`Report` text DEFAULT NULL,'
        . '`MobileNo` varchar(10) DEFAULT NULL,'
        . '`Status` text DEFAULT NULL,'
        . ' PRIMARY KEY (`StatusID`)'
        . ') ENGINE=InnoDB  DEFAULT CHARSET = utf8;';
      break;
  }

  return $SqlDB;
}

?>
