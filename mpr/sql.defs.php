<?php

function CreateSchemas() {
  $ObjDB = new MySQLiDBHelper();
  $DB    = new mysqli(HOST_Name, MySQL_User, MySQL_Pass, MySQL_DB);
  $ObjDB->ddlQuery(SQLDefs('MPR_UserMaps'));
  $ObjDB->ddlQuery(SQLDefs('MPR_Schemes'));
  $ObjDB->ddlQuery(SQLDefs('MPR_SchemeAllotments'));
  $ObjDB->ddlQuery(SQLDefs('MPR_Works'));
  $ObjDB->ddlQuery(SQLDefs('MPR_Sanctions'));
  $ObjDB->ddlQuery(SQLDefs('MPR_Progress'));
  $ObjDB->ddlQuery(SQLDefs('MenuData'));
  $ObjDB->ddlQuery(SQLDefs('MPR_ViewMappedUsers'));
  $ObjDB->ddlQuery(SQLDefs('MPR_ViewWorkAllotments'));
  $ObjDB->ddlQuery(SQLDefs('MPR_ViewWorkExpenses'));
  $ObjDB->ddlQuery(SQLDefs('MPR_ViewLatestProgressID'));
  $DB->query(SQLDefs('MPR_ViewLatestProgress'));
  //echo SQLDefs('MPR_ViewLatestProgress'); //TODO: Unable to Create MPR_ViewLatestProgress
  $DB->query(SQLDefs('MPR_ViewUserWorks'));
  //echo SQLDefs('MPR_ViewUserWorks'); //TODO: Unable to Create MPR_ViewUserWorks
  $ObjDB->ddlQuery(SQLDefs('MPR_ViewWorkerSchemes'));
  $ObjDB->ddlQuery(SQLDefs('MPR_ViewUserSchemeAllotments'));
  $ObjDB->ddlQuery(SQLDefs('MPR_ViewSchemeWiseExpenditure'));
  $ObjDB->ddlQuery(SQLDefs('MPR_ViewSchemeWiseAllotments'));
  $DB->query(SQLDefs('MPR_ViewSchemeWiseFunds'));
  //echo SQLDefs('MPR_ViewSchemeWiseFunds'); //TODO: Unable to Create MPR_ViewSchemeWiseFunds
  $DB->query(SQLDefs('MPR_ViewUserFunds'));
  //echo SQLDefs('MPR_ViewUserFunds'); //TODO: Unable to Create MPR_ViewUserFunds
  unset($ObjDB);
}

function SQLDefs($ObjectName) {
  $SqlDB = '';
  switch ($ObjectName) {

    case 'MPR_UserMaps':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`MprMapID` bigint NOT NULL AUTO_INCREMENT,'
        . '`UserMapID` int NOT NULL,'
        . '`CtrlMapID` int NOT NULL,'
        . '`UserLevel` VARCHAR(100) DEFAULT NULL,'
        . ' PRIMARY KEY (`MprMapID`),'
        . ' UNIQUE KEY (`UserMapID`,`CtrlMapID`),'
        . ' FOREIGN KEY (`UserMapID`)'
        . ' REFERENCES `' . MySQL_Pre . 'Users`(`UserMapID`)'
        . ' ON DELETE RESTRICT ON UPDATE CASCADE'
        . ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      break;

    case 'MPR_Schemes':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`SchemeID` bigint NOT NULL AUTO_INCREMENT,'
        . '`SchemeName` VARCHAR(100) DEFAULT NULL,'
        . '`UserMapID` int NOT NULL,'
        . ' PRIMARY KEY (`SchemeID`),'
        . ' FOREIGN KEY (`UserMapID`)'
        . ' REFERENCES `' . MySQL_Pre . 'MPR_UserMaps`(`UserMapID`)'
        . ' ON DELETE RESTRICT ON UPDATE CASCADE,'
        . ' UNIQUE KEY (`SchemeName`)'
        . ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      break;

    case 'MPR_SchemeAllotments':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`AllotmentID` bigint NOT NULL AUTO_INCREMENT,'
        . '`SchemeID` bigint NOT NULL,'
        . '`Amount` bigint DEFAULT NULL,'
        . '`OrderNo` text,'
        . '`Date` date,'
        . '`Year` text NOT NULL,'
        . ' PRIMARY KEY (`AllotmentID`),'
        . ' FOREIGN KEY (`SchemeID`)'
        . ' REFERENCES `' . MySQL_Pre . 'MPR_Schemes`(`SchemeID`)'
        . ' ON DELETE RESTRICT ON UPDATE CASCADE'
        . ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      break;

    case 'MPR_Works':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`WorkID` bigint NOT NULL AUTO_INCREMENT,'
        . '`SchemeID` bigint NOT NULL,'
        . '`MprMapID` bigint NOT NULL,'
        . '`WorkDescription` text NOT NULL,'
        . '`EstimatedCost` bigint NOT NULL,'
        . '`TenderDate` date,'
        . '`WorkOrderDate` date,'
        . '`WorkRemarks` text NOT NULL,'
        . ' PRIMARY KEY (`WorkID`),'
        . ' FOREIGN KEY (`SchemeID`)'
        . ' REFERENCES `' . MySQL_Pre . 'MPR_Schemes`(`SchemeID`)'
        . ' ON DELETE RESTRICT ON UPDATE CASCADE,'
        . ' FOREIGN KEY (`MprMapID`)'
        . ' REFERENCES `' . MySQL_Pre . 'MPR_UserMaps`(`MprMapID`)'
        . ' ON DELETE RESTRICT ON UPDATE CASCADE'
        . ') ENGINE=InnoDB  DEFAULT CHARSET=utf8;';
      break;

    case 'MPR_Sanctions':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`SanctionID` bigint NOT NULL AUTO_INCREMENT,'
        . '`WorkID` bigint NOT NULL,'
        . '`SanctionOrderNo` text NOT NULL,'
        . '`SanctionDate` date,'
        . '`SanctionAmount` bigint NOT NULL,'
        . '`SanctionRemarks` text NOT NULL,'
        . ' PRIMARY KEY (`SanctionID`),'
        . ' FOREIGN KEY (`WorkID`)'
        . ' REFERENCES `' . MySQL_Pre . 'MPR_Works`(`WorkID`)'
        . ' ON DELETE RESTRICT ON UPDATE CASCADE'
        . ') ENGINE=InnoDB  DEFAULT CHARSET=utf8;';
      break;

    case 'MPR_Progress':
      $SqlDB = 'CREATE TABLE IF NOT EXISTS `' . MySQL_Pre . $ObjectName . '` ('
        . '`ProgressID` bigint NOT NULL AUTO_INCREMENT,'
        . '`WorkID` bigint,'
        . '`Progress` tinyint DEFAULT 0,'
        . '`ExpenditureAmount` BIGINT DEFAULT 0,'
        . '`ReportDate` DATE,'
        . '`Balance` BIGINT DEFAULT 0,'
        . '`Remarks` VARCHAR(300) DEFAULT NULL,'
        . '`MobileNo` VARCHAR(10) DEFAULT NULL,'
        . '`UpdatedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,'
        . ' PRIMARY KEY (`ProgressID`),'
        . ' FOREIGN KEY (`WorkID`)'
        . ' REFERENCES `' . MySQL_Pre . 'MPR_Works`(`WorkID`)'
        . ' ON DELETE RESTRICT ON UPDATE CASCADE'
        . ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      break;

    case 'MenuData':
      $SqlDB = 'INSERT INTO `' . MySQL_Pre . 'MenuItems` '
        . '(`AppID`,`MenuOrder`,`AuthMenu`,`Caption`,`URL`,`Activated`) VALUES'
        . '(\'MPR\', 1, 0, \'Home\', \'index.php\', 1),'
        . '(\'MPR\', 2, 1, \'Users\', \'mpr/Users.php\', 1),'
        . '(\'MPR\', 4, 1, \'Schemes\', \'mpr/Schemes.php\', 1),'
        . '(\'MPR\', 5, 1, \'Works\', \'mpr/Works.php\', 1),'
        . '(\'MPR\', 6, 1, \'Progress\', \'mpr/Progress.php\', 1),'
        . '(\'MPR\', 8, 1, \'Reports\', \'mpr/Reports.php\', 1),'
        . '(\'MPR\', 9, 1, \'Log Out!\', \'login.php?LogOut=1\', 1);';
      break;

    case 'MPR_ViewUserSchemeAllotments':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `A`.`SchemeID` AS `SchemeID`,`S`.`SchemeName` AS `SchemeName`,'
        . '`A`.`AllotmentID` AS `AllotmentID`,`A`.`Amount` AS `Amount`,'
        . '`A`.`OrderNo` AS `OrderNo`,`A`.`Date` AS `Date`,'
        . '`A`.`Year` AS `Year`,`S`.`UserMapID` AS `UserMapID`'
        . ' from (`' . MySQL_Pre . 'MPR_SchemeAllotments` `A` join `' . MySQL_Pre . 'MPR_Schemes` `S`'
        . ' on(`S`.`SchemeID` = `A`.`SchemeID`));';
      break;

    case 'MPR_ViewMappedUsers':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `M`.`MprMapID` AS `MprMapID`,`U`.`UserMapID` AS `UserMapID`,'
        . '`U`.`UserName` AS `UserName`,`M`.`CtrlMapID` AS `CtrlMapID`'
        . ' from (`' . MySQL_Pre . 'MPR_UserMaps` `M` join `' . MySQL_Pre . 'Users` `U`'
        . ' on(`M`.`UserMapID` = `U`.`UserMapID`));';
      break;

    /*
     * COALESCE, an SQL command that selects the first non-null from a range of values
     */
    case 'MPR_ViewWorkAllotments':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `W`.`WorkID` AS `WorkID`,'
        . 'COALESCE(SUM(`S`.`SanctionAmount`),0) AS `Funds`'
        . ' FROM `' . MySQL_Pre . 'MPR_Works` `W`'
        . ' LEFT join `' . MySQL_Pre . 'MPR_Sanctions` `S` on(`S`.`WorkID`=`W`.`WorkID`)'
        . ' GROUP BY `W`.`WorkID`;';
      break;

    case 'MPR_ViewWorkExpenses':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `W`.`WorkID` AS `WorkID`,'
        . 'COALESCE(SUM(`P`.`ExpenditureAmount`),0) AS `Expenses`, MAX(`Progress`) AS `Progress`'
        . ' FROM `' . MySQL_Pre . 'MPR_Works` `W`'
        . ' LEFT join `' . MySQL_Pre . 'MPR_Progress` `P` on(`P`.`WorkID`=`W`.`WorkID`)'
        . ' GROUP BY `W`.`WorkID`;';
      break;

    case 'MPR_ViewLatestProgressID':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `W`.`WorkID` AS `WorkID`,'
        . 'MAX(`ProgressID`) AS `ProgressID`'
        . ' FROM `' . MySQL_Pre . 'MPR_Works` `W`'
        . ' LEFT join `' . MySQL_Pre . 'MPR_Progress` `P` on(`P`.`WorkID`=`W`.`WorkID`)'
        . ' GROUP BY `W`.`WorkID`;';
      break;

    case 'MPR_ViewLatestProgress':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `W`.`WorkID` AS `WorkID`,`P`.`ProgressID` AS `ProgressID`,'
        . '`Progress`,`Remarks`'
        . ' FROM `' . MySQL_Pre . 'MPR_ViewLatestProgressID` `W`'
        . ' LEFT join `' . MySQL_Pre . 'MPR_Progress` `P` on(`P`.`ProgressID`=`W`.`ProgressID`);';
      break;

    case 'MPR_ViewUserWorks':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `W`.`WorkID` AS `WorkID`,`W`.`WorkDescription` AS `Work`,'
        . '`W`.`SchemeID` AS `SchemeID`,`S`.`SchemeName` AS `SchemeName`,'
        . '`M`.`MprMapID` AS `MprMapID`,`M`.`CtrlMapID` AS `CtrlMapID`,`M`.`UserMapID` AS `UserMapID`,'
        . 'FORMAT(`EstimatedCost`,0,"en_IN") AS `EstimatedCost`,FORMAT(`A`.`Funds`,0,"en_IN") AS `Funds`,'
        . 'FORMAT(`E`.`Expenses`,0,"en_IN") AS `Expenses`,'
        . 'FORMAT(`A`.`Funds`-`E`.`Expenses`,0,"en_IN") AS `Balance`,`P`.`Progress`,'
        . 'DATE_FORMAT(`TenderDate`,"%d/%m/%Y") AS `TenderDate`,'
        . 'DATE_FORMAT(`WorkOrderDate`,"%d/%m/%Y") AS `WorkOrderDate`,`WorkRemarks`,`P`.`Remarks`'
        . ' FROM (((`' . MySQL_Pre . 'MPR_Works` `W` '
        . ' JOIN `' . MySQL_Pre . 'MPR_Schemes` `S` on(`S`.`SchemeID`=`W`.`SchemeID`))'
        . ' LEFT JOIN `' . MySQL_Pre . 'MPR_UserMaps` `M` on(`M`.`MprMapID` = `W`.`MprMapID`))'
        . ' LEFT JOIN `' . MySQL_Pre . 'MPR_ViewWorkAllotments` `A` on(`A`.`WorkID`=`W`.`WorkID`))'
        . ' LEFT JOIN `' . MySQL_Pre . 'MPR_ViewWorkExpenses` `E` on(`E`.`WorkID`=`W`.`WorkID`)'
        . ' LEFT JOIN `' . MySQL_Pre . 'MPR_ViewLatestProgress` `P` on(`P`.`WorkID`=`W`.`WorkID`);';
      break;

    case 'MPR_ViewWorkerSchemes':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `W`.`SchemeID` AS `SchemeID`,`S`.`SchemeName` AS `SchemeName`,'
        . '`M`.`UserMapID` AS `UserMapID`,`U`.`UserName` AS `UserName`,`U`.`MobileNo` AS `MobileNo`'
        . ' from (`' . MySQL_Pre . 'MPR_UserMaps` `M` join `' . MySQL_Pre . 'MPR_Works` `W`'
        . ' on(`M`.`MprMapID` = `W`.`MprMapID`)) join `' . MySQL_Pre . 'MPR_Schemes` `S`'
        . ' on(`S`.`SchemeID`=`W`.`SchemeID`) join `' . MySQL_Pre . 'Users` `U`'
        . ' on(`U`.`UserMapID`=`M`.`UserMapID`) '
        . 'Group By `S`.`SchemeID`,`S`.`SchemeName`,`M`.`UserMapID`,`U`.`UserName`,`U`.`MobileNo` '
        . 'Order By `U`.`UserName`;';
      break;

    case 'MPR_ViewSchemeWiseExpenditure':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `A`.`Year` AS `Year`,`S`.`SchemeID` AS `SchemeID`,'
        . '`S`.`SchemeName` AS `SchemeName`,COALESCE(SUM(`P`.`ExpenditureAmount`), 0) AS `Expense` '
        . 'from (((`' . MySQL_Pre . 'MPR_Schemes` `S`'
        . ' LEFT JOIN `' . MySQL_Pre . 'MPR_SchemeAllotments` `A` on(`A`.`SchemeID` = `S`.`SchemeID`))'
        . ' LEFT JOIN `' . MySQL_Pre . 'MPR_Works` `W` on(`W`.`SchemeID` = `S`.`SchemeID`))'
        . ' LEFT JOIN `' . MySQL_Pre . 'MPR_Progress` `P` on(`W`.`WorkID` = `P`.`WorkID`))'
        . ' GROUP BY `A`.`Year`,`S`.`SchemeID`,`S`.`SchemeName`;';
      break;

    case 'MPR_ViewSchemeWiseAllotments':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `A`.`Year` AS `Year`,`S`.`SchemeID` AS `SchemeID`,'
        . '`S`.`SchemeName` AS `SchemeName`,SUM(`A`.`Amount`) AS `Funds`'
        . ' from `' . MySQL_Pre . 'MPR_Schemes` `S`'
        . ' LEFT JOIN `' . MySQL_Pre . 'MPR_SchemeAllotments` `A` on(`A`.`SchemeID` = `S`.`SchemeID`)'
        . ' GROUP BY `A`.`Year`,`S`.`SchemeID`,`S`.`SchemeName`;';
      break;

    case 'MPR_ViewSchemeWiseFunds':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `A`.`Year` AS `Year`,`A`.`SchemeID` AS `SchemeID`,'
        . '`A`.`SchemeName` AS `SchemeName`,FORMAT(`Funds`,0,"en_IN") AS `Funds`,'
        . ' FORMAT(`Expense`,0,"en_IN") AS `Expense`, '
        . ' FORMAT(`A`.`Funds`-`E`.`Expense`,0,"en_IN") AS `Balance`'
        . ' from `' . MySQL_Pre . 'MPR_ViewSchemeWiseAllotments` `A`'
        . ' LEFT JOIN `' . MySQL_Pre . 'MPR_ViewSchemeWiseExpenditure` `E` '
        . ' on((`A`.`SchemeID` = `E`.`SchemeID`) AND (`A`.`Year`=`E`.`Year`))'
        . ' GROUP BY `A`.`Year`,`A`.`SchemeID`,`A`.`SchemeName`;';
      break;

    case 'MPR_ViewUserFunds':
      $SqlDB = 'CREATE OR REPLACE VIEW `' . MySQL_Pre . $ObjectName . '` AS '
        . 'select `S`.`SchemeID` AS `SchemeID`,`S`.`SchemeName` AS `SchemeName`,'
        . '`M`.`UserMapID` AS `UserMapID`,`U`.`UserName` AS `UserName`,`U`.`MobileNo` AS `MobileNo`,'
        . 'format(sum(`W`.`EstimatedCost`),0) AS `EstimatedCost`,'
        . 'format(sum(`A`.`Funds`),0) AS `Funds`,format(sum(`E`.`Expenses`),0) AS `Expenses`,'
        . 'format((sum(`A`.`Funds`) - sum(`E`.`Expenses`)),0) AS `Balance` '
        . 'from (((((`' . MySQL_Pre . 'MPR_Works` `W` join `' . MySQL_Pre . 'MPR_Schemes` `S`'
        . ' on((`S`.`SchemeID` = `W`.`SchemeID`))) left join `' . MySQL_Pre . 'MPR_UserMaps` `M`'
        . ' on((`M`.`MprMapID` = `W`.`MprMapID`))) left join `' . MySQL_Pre . 'MPR_ViewWorkAllotments` `A`'
        . ' on((`A`.`WorkID` = `W`.`WorkID`))) left join `' . MySQL_Pre . 'MPR_ViewWorkExpenses` `E`'
        . ' on((`E`.`WorkID` = `W`.`WorkID`))) left join `' . MySQL_Pre . 'Users` `U`'
        . ' on((`U`.`UserMapID` = `M`.`UserMapID`))) '
        . 'group by `S`.`SchemeID`,`S`.`SchemeName`,`M`.`UserMapID`,`U`.`UserName`;';
      break;

  }
  return $SqlDB;
}

?>


