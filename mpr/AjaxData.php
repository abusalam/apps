<?php
require_once __DIR__ . '/../lib.inc.php';
WebLib::AuthSession();
switch (WebLib::GetVal($_POST, 'CallAPI')) {

  case 'Users_GetUserData':
    $DB = new MySQLiDBHelper();
    $DB->where('CtrlMapID', $_SESSION['UserMapID']);
    WebLib::ShowTable($DB->query('Select `UserName` AS `Scheme Users`'
      . 'from ' . MySQL_Pre . 'MPR_ViewMappedUsers'));
    unset($DB);
    break;

  case 'Schemes_GetSchemeTable':
    $DB = new MySQLiDBHelper();
    $DB->where('UserMapID', $_SESSION['UserMapID']);
    $DB->where('SchemeID', WebLib::GetVal($_POST, 'Scheme'));
    WebLib::ShowTable($DB->query('Select Year, SchemeName, Amount, OrderNo, '
      . 'Date as `Order Date(YYYY-MM-DD)` '
      . 'from ' . MySQL_Pre . 'MPR_ViewUserSchemeAllotments'));
    unset($DB);
    break;

  case 'Reports_GetSchemeFunds':
    $DB = new MySQLiDBHelper();
    $DB->where('SchemeID', WebLib::GetVal($_POST, 'Scheme'));
    WebLib::ShowTable($DB->query('Select `UserName` as `Executing Agency`,'
      . ' `EstimatedCost` as `Estimated Cost`, `Funds` as `Released`, `Expenses` as `Expenditure`, `Balance` '
      . 'from ' . MySQL_Pre . 'MPR_ViewUserFunds'));

    $DB->where('SchemeID', WebLib::GetVal($_POST, 'Scheme'));
    WebLib::ShowTable($DB->query('Select `Year`,`SchemeName` as `Scheme`,'
      . '`Funds` as `Released`,`Expense` as `Expenditure`,`Balance` '
      . 'from ' . MySQL_Pre . 'MPR_ViewSchemeWiseFunds'));
    unset($DB);
    break;

  case 'Reports_GetWorkFunds':
    $DB = new MySQLiDBHelper();
    $DB->where('SchemeID', WebLib::GetVal($_POST, 'Scheme'));
    $DB->where('UserMapID', WebLib::GetVal($_POST, 'User'));
    WebLib::ShowTable($DB->query('Select CONCAT(`WorkID`,\' - \',`Work`) as '
      . '`Work Description`, `EstimatedCost` as `Estimated Cost`,'
      . '`Funds` as `Released`, `Expenses` as `Expenditure`,`Balance`,`Progress`,'
      . '`Remarks` AS `Status`,`TenderDate`,`WorkOrderDate`,`WorkRemarks` AS `Remarks`'
      . 'from ' . MySQL_Pre . 'MPR_ViewUserWorks'));
    unset($DB);
    break;

  case 'Works_GetWorks':
    ?>
      <div class="formWrapper-Autofit">
          <h3 class="formWrapper-h3">Sanctioned Works</h3>
          <table rules="all" frame="box" width="100%" cellpadding="5"
                 cellspacing="2">
              <tr>
                  <td>Sl No.</td>
                  <th>Description of Work</th>
                  <th>Estimated Cost(Rs.)</th>
                  <th>Fund Released(Rs.)</th>
                  <th>Fund Utilised(Rs.)</th>
                  <th>Balance(Rs.)</th>
                  <th>Physical Progress(%)</th>
                  <th>Tender Date</th>
                  <th>Work Order Date</th>
                  <th>Remarks</th>
              </tr>
            <?php
            $DB = new MySQLiDBHelper();
            $DB->where('CtrlMapID', $_SESSION['UserMapID']);
            $MprMapID = WebLib::GetVal($_POST, 'MprMapID');
            if ($MprMapID > 0) {
              $DB->where('MprMapID', $MprMapID);
            }
            $DB->where('SchemeID', WebLib::GetVal($_POST, 'Scheme'));
            $UserWorks = $DB->get(MySQL_Pre . 'MPR_ViewUserWorks');
            $i         = 1;
            foreach ($UserWorks as $Work) {
              ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $Work['WorkID'] . ' - ' . htmlentities($Work['Work']) ?></td>
                    <td><?php echo htmlentities($Work['EstimatedCost']) ?></td>
                    <td><?php echo htmlentities($Work['Funds']) ?></td>
                    <td><?php echo htmlentities($Work['Expenses']) ?></td>
                    <td><?php echo htmlentities($Work['Balance']) ?></td>
                    <td><?php echo htmlentities($Work['Progress']) ?></td>
                    <td><?php echo htmlentities($Work['TenderDate']) ?></td>
                    <td><?php echo htmlentities($Work['WorkOrderDate']) ?></td>
                    <td><?php echo htmlentities($Work['WorkRemarks']) ?></td>
                </tr>
              <?php
              $i++;
            } ?>
          </table>
      </div>
    <?php
    break;

  case 'Works_GetSanctions':
    ?>
      <div class="formWrapper-Autofit">
          <h3 class="formWrapper-h3">Sanctioned Funds</h3>
        <?php
        $DB = new MySQLiDBHelper();
        $DB->where('WorkID', WebLib::GetVal($_POST, 'WorkID'));
        $UserWorks = $DB->query('Select `SanctionOrderNo` AS `Order No.`, '
          . '`SanctionDate` AS `Sanction/Approval Date`, `SanctionAmount` AS `Amount`, '
          . '`SanctionRemarks` AS `Remarks` FROM ' . MySQL_Pre . 'MPR_Sanctions');
        WebLib::ShowTable($UserWorks);
        ?>
      </div>
    <?php
    break;

  case 'Works_GetWorksForSanction':
    $DB = new MySQLiDBHelper();
    $DB = new MySQLiDBHelper();
    $DB->where('SchemeID', WebLib::GetVal($_POST, 'Scheme'));
    $DB->where('MprMapID', WebLib::GetVal($_POST, 'MprMapID'));
    $Works = $DB->get(MySQL_Pre . 'MPR_Works');
    echo '<option></option>';
    foreach ($Works as $Work) {
      echo '<option value="' . $Work['WorkID'] . '">' . $Work['WorkID'] . '-' . htmlentities($Work['WorkDescription']) . '</option>';
    }
    //print_r($_POST);
    unset($DB);
    break;

  case 'Progress_GetWorksList':
    $DB = new MySQLiDBHelper();
    $DB->where('UserMapID', $_SESSION['UserMapID']);
    $DB->where('SchemeID', WebLib::GetVal($_POST, 'Scheme'));
    $Works = $DB->get(MySQL_Pre . 'MPR_ViewUserWorks');
    echo '<option></option>';
    foreach ($Works as $Work) {
      $Sel = '';
      if ($Work['WorkID'] == WebLib::GetVal($_POST, 'Work')) {
        $Sel = 'Selected';
      }
      echo '<option value="' . $Work['WorkID'] . '" ' . $Sel . '>'
        . htmlentities($Work['SchemeName']) . ' - ' . htmlentities($Work['Work']) . '</option>';
    }
    break;

  case 'Progress_GetWorkStatusJSON':
    $DB = new MySQLiDBHelper();
    $DB->where('UserMapID', $_SESSION['UserMapID']);
    $DB->where('WorkID', WebLib::GetVal($_POST, 'Work'));
    $Works = $DB->query('SELECT `Balance`, `Progress`, `TenderDate`, `WorkOrderDate` FROM ' . MySQL_Pre . 'MPR_ViewUserWorks');
    foreach ($Works as $Work) {
      echo json_encode($Work);
    }
    break;

  case 'Progress_GetProgressDetails':
    ?>
      <div class="formWrapper-Autofit">
          <h3 class="formWrapper-h3">Sanctioned Funds</h3>
        <?php
        $DB = new MySQLiDBHelper();
        $DB->where('WorkID', WebLib::GetVal($_POST, 'Work'));
        $UserWorks = $DB->query('Select `SanctionOrderNo` AS `Order No.`, '
          . '`SanctionDate` AS `Sanction/Approval Date`, `SanctionAmount` AS `Amount`, '
          . '`SanctionRemarks` AS `Remarks` FROM ' . MySQL_Pre . 'MPR_Sanctions');
        WebLib::ShowTable($UserWorks);
        ?>
      </div>
      <div class="formWrapper-Autofit">
          <h3 class="formWrapper-h3">Progress Details</h3>
          <table rules="all" frame="box" width="100%" cellpadding="5"
                 cellspacing="2">
              <tr>
                  <th>Report Date</th>
                  <th>Balance</th>
                  <th>Expenditure Amount</th>
                  <th>Physical Progress(%)</th>
                  <th>Remarks</th>
              </tr>
            <?php
            $DB = new MySQLiDBHelper();
            $DB->where('WorkID', WebLib::GetVal($_POST, 'Work'));
            $PrgReports = $DB->get(MySQL_Pre . 'MPR_Progress');
            foreach ($PrgReports as $PrgReport) {
              ?>
                <tr>
                    <td><?php echo htmlentities($PrgReport['ReportDate']); ?></td>
                    <td><?php echo htmlentities($PrgReport['Balance']); ?></td>
                    <td><?php echo htmlentities($PrgReport['ExpenditureAmount']); ?></td>
                    <td><?php echo htmlentities($PrgReport['Progress']); ?>%
                    </td>
                    <td><?php echo htmlentities($PrgReport['Remarks']); ?></td>
                </tr>
              <?php
            } ?>
          </table>
      </div>
    <?php
    break;

  case 'Progress_GetWorkDetails':
    ?>
      <div class="formWrapper-Autofit">
          <h3 class="formWrapper-h3">Sanctioned Works</h3>
          <table rules="all" frame="box" width="100%" cellpadding="5"
                 cellspacing="2">
              <tr>
                  <td>Sl No.</td>
                  <th>Description of Work</th>
                  <th>Estimated Cost(Rs.)</th>
                  <th>Fund Released(Rs.)</th>
                  <th>Fund Utilised(Rs.)</th>
                  <th>Balance(Rs.)</th>
                  <th>Physical Progress(%)</th>
                  <th>Tender Date</th>
                  <th>Work Order Date</th>
                  <th>Remarks</th>
              </tr>
            <?php
            $DB = new MySQLiDBHelper();
            $DB->where('UserMapID', $_SESSION['UserMapID']);
            $DB->where('SchemeID', WebLib::GetVal($_POST, 'Scheme'));
            $UserWorks = $DB->get(MySQL_Pre . 'MPR_ViewUserWorks');
            $i         = 1;
            foreach ($UserWorks as $Work) {
              ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo htmlentities($Work['Work']); ?></td>
                    <td><?php echo htmlentities($Work['EstimatedCost']); ?></td>
                    <td><?php echo htmlentities($Work['Funds']); ?></td>
                    <td><?php echo htmlentities($Work['Expenses']); ?></td>
                    <td><?php echo htmlentities($Work['Balance']); ?></td>
                    <td><?php echo htmlentities($Work['Progress']); ?></td>
                    <td><?php echo htmlentities($Work['TenderDate']); ?></td>
                    <td><?php echo htmlentities($Work['WorkOrderDate']); ?></td>
                    <td><?php echo htmlentities($Work['WorkRemarks']); ?></td>
                </tr>
              <?php
              $i++;
            } ?>
          </table>
      </div>
    <?php
    break;

}
//print_r($_POST);
?>
