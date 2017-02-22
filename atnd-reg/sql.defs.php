<?php

function CreateSchemas() {
    $ObjDB = new MySQLiDBHelper();
    $ObjDB->ddlQuery(SQLDefs('ATND_Register'));
    $ObjDB->ddlQuery(SQLDefs('ATND_View'));
    unset($ObjDB);
}

function SQLDefs($ObjectName) {
  $SqlDB = '';
  switch ($ObjectName) {
    case 'ATND_Register':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . 'ATND_Register` ('
              . '`AtndID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,'
              . '`UserMapID` int(11) NOT NULL,'
              . '`InDateTime` timestamp NULL,'
              . '`OutDateTime` timestamp NULL,'
              . ' PRIMARY KEY (`AtndID`)'
              . ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      break;
    case 'ATND_View':
      $SqlDB = 'CREATE VIEW `' . MySQL_Pre . 'ATND_View` AS '
              . ' Select `U`.`UserName` AS `UserName`,`R`.`InDateTime` AS `InDateTime`,'
              . ' `R`.`OutDateTime` AS `OutDateTime` '
              . ' From (`' . MySQL_Pre . 'Users` `U` join `' . MySQL_Pre . 'ATND_Register` `R` '
              . ' ON((`R`.`UserMapID` = `U`.`UserMapID`))) '
              . ' Where (`R`.`UserMapID` > 1) Order By `R`.`AtndID`;';
      break;
  }
  return $SqlDB;
}

?>
