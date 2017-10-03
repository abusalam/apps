<?php

function CreateSchemas() {
  $ObjDB = new MySQLiDBHelper();
  $ObjDB->ddlQuery(SQLDefs('APP_Users'));
  $ObjDB->ddlQuery(SQLDefs('APP_Register'));
  $ObjDB->ddlQuery(SQLDefs('APP_Logs'));
  unset($ObjDB);
}

function SQLDefs($ObjectName) {
  $SqlDB = '';
  switch ($ObjectName) {

    case 'APP_Users':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`MobileNo` varchar(10) NOT NULL,'
        . '`UserMapID` varchar(10) NOT NULL,'
        . '`UserData` text DEFAULT NULL,'
        . '`TempData` text DEFAULT NULL,'
        . '`UsageCount` int DEFAULT 0,'
        . '`Status` enum(\'Registered\',\'Activated\',\'Inactive\') DEFAULT NULL,'
        . '`LastAccessTime` timestamp NOT NULL '
        . ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,'
        . ' PRIMARY KEY (`MobileNo`)'
        . ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      break;

    case 'APP_Register':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`RequestID` int NOT NULL AUTO_INCREMENT,'
        . '`MobileNo` varchar(10) DEFAULT NULL,'
        . '`RequestTime` timestamp DEFAULT CURRENT_TIMESTAMP,'
        . ' PRIMARY KEY (`RequestID`)'
        . ') ENGINE=InnoDB  DEFAULT CHARSET = utf8;';
      break;

    case 'APP_Logs':
      $SqlDB ='CREATE TABLE `' . MySQL_Pre . $ObjectName . '` ('
      .'`LogID` bigint(20) NOT NULL AUTO_INCREMENT,'
      .'`IMEI` varchar(20) NOT NULL,'
      .'`LocalIP` varchar(16) NOT NULL,'
      .'`IP` varchar(16) NOT NULL,'
      .'`MobileNo` varchar(15) NOT NULL,'
      .'`AccessTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,'
      .'PRIMARY KEY (`LogID`)'
      .') ENGINE=InnoDB DEFAULT CHARSET=utf8';
      break;
  }

  return $SqlDB;
}