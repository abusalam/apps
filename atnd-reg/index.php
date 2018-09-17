<?php
require_once(__DIR__ . '/../lib.inc.php');

WebLib::AuthSession();
WebLib::Html5Header('Attendance Management System');
WebLib::IncludeCSS();
if (NeedsDB) {
  WebLib::CreateDB('ATND');
}
?>
</head>
<body>
<div class="TopPanel">
    <div class="LeftPanelSide"></div>
    <div class="RightPanelSide"></div>
    <h1><?php echo AppTitle; ?></h1>
</div>
<div class="Header"></div>
<?php
WebLib::ShowMenuBar('ATND');
?>
<div class="content">
    <span class="Message" id="Msg" style="float: right;"></span>
  <?php
  /*    $Data = new MySQLiDB();
      $Query = 'Select `W`.`SessionID`,`W`.`UserID`,`U`.`UserName`,`W`.`Action`,`W`.`AccessTime` FROM '
              . '(Select `UserID`,Max(`LogID`) as `LogID` FROM `' . MySQL_Pre . 'Logs`'
              . ' Where `UserID`>0 AND (`AccessTime`+0)>(CURRENT_TIMESTAMP -(' . LifeTime . ' * 60)) '
              . ' Group By `UserID`,`SessionID` HAVING MAX(`LogID`)) as `L`'
              . ' JOIN `' . MySQL_Pre . 'Logs` as `W` '
              . ' ON (`W`.`LogID`=`L`.`LogID` AND `Action` NOT LIKE \'LogOut:%\')'
              . ' JOIN `' . MySQL_Pre . 'Users` as `U` '
              . ' ON (`W`.`UserID`=`U`.`UserMapID`)';
      echo "<b>Currently Active Users: </b>" . $Data->do_sel_query($Query);
      if (WebLib::GetVal($_SESSION, 'CheckAuth') === 'Valid') {
        $Data->ShowTable($Query);
      }
      $Data->do_close();*/
  WebLib::ShowMsg();
  ?>
    <input type="hidden" id="AjaxToken"
           value="<?php echo WebLib::GetVal($_SESSION, 'Token'); ?>"/>
    <pre id="Error"></pre>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
</body>
</html>

