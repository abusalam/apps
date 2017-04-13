<?php
require_once __DIR__ . '/../lib.inc.php';
require_once __DIR__ . '/../smsgw/smsgw.inc.php';
if (file_exists(__DIR__ . '/config.inc.php')) {
  require_once __DIR__ . '/config.inc.php';
} else {
  require_once __DIR__ . '/config.sample.inc.php';
}
WebLib::AuthSession();
WebLib::Html5Header('Attendance Register');
WebLib::IncludeCSS();
WebLib::JQueryInclude();

function PrintArr($Arr) {
  echo '<pre>';
  print_r($Arr);
  echo '</pre>';
}
?>
<script type="text/javascript" >
  $(function() {
    $('input[type="submit"]').button();
    $('input[type="button"]').button();
  });
</script>
</head>
<body>
  <div class="TopPanel">
    <div class="LeftPanelSide"></div>
    <div class="RightPanelSide"></div>
    <h1><?php echo AppTitle; ?></h1>
  </div>
  <div class="Header">
  </div>
  <?php
  WebLib::ShowMenuBar('ATND');
  ?>
  <div class="content">
    <h2>Attendance Register</h2>
    <form name="frmEditUser" id="frmAssignParts" method="post" action="Attendance.php">
      <?php
      $Data = new MySQLiDBHelper();

      if ((WebLib::GetVal($_POST, 'CmdAtnd') !== null) && (WebLib::GetVal($_SESSION, 'AtndDone') !== '1')) {
        $MobileNo=@explode('', $Data->query('Select MobileNo FROM `' . MySQL_Pre . 'Users`'
                            .' WHERE `UserMapID`=' . $_SESSION['UserMapID']));

        if (strstr($_SERVER['REMOTE_ADDR'], AtndAllowedIP) !== false) {
          if ($_SESSION['InOut'] === 'In') {
            $AtndData['UserMapID']=WebLib::GetVal($_SESSION,'UserMapID',true);
            $AtndData['InDateTime']=date('Y-m-d H:i:s', $_SESSION['ATND_TIME']);
            $AtndDone=$Data->insert(MySQL_Pre . 'ATND_Register',$AtndData);
          } else {
            $Data->where('AtndID',WebLib::GetVal($_SESSION,'AtndID',true));
            $AtndData['OutDateTime']=date('Y-m-d H:i:s', $_SESSION['ATND_TIME']);
            $AtndDone=$Data->update(MySQL_Pre . 'ATND_Register',$AtndData);
          }
          unset($Data);
          if ($AtndDone > 0) {
            $_SESSION['Msg'] = 'Attendance Registered!';
            $_SESSION['AtndDone'] = '1';
            if (UseSMSGW === true) {
              $TxtSMS = $_SESSION['InOut'] . ': ' . $_SESSION['UserName'] . "\n"
                      . ' Mobile No: ' . $MobileNo . "\n"
                      . ' From: ' . $_SERVER['REMOTE_ADDR'] . "\n"
                      . ' On: ' . date('d/m/Y l H:i:s A', $_SESSION['ATND_TIME']);
              SMSGW::SendSMS($TxtSMS, AdminMobile);
            }
          } else {
            $_SESSION['Msg'] = 'Unable to Register Attendance!';
          }
        } else {
          if (UseSMSGW === true) {
            $TxtSMS = 'UnAuthorised Access!' . "\n" . $_SESSION['InOut'] . ': '
              . $_SESSION['UserName'] . "\n"
              . ' Mobile No: ' . $MobileNo . "\n"
              . ' From: ' . $_SERVER['REMOTE_ADDR'] . "\n"
              . ' On: ' . date('d/m/Y l H:i:s A', $_SESSION['ATND_TIME']);
            SMSGW::SendSMS($TxtSMS, AdminMobile);
          }
          $_SESSION['Msg'] = 'Attendance Not Permitted From IP:' . WebLib::GetVal($_SERVER, 'REMOTE_ADDR');
        }
      }
      WebLib::ShowMsg();

      $Data = new MySQLiDBHelper();
      $Query = 'SELECT Max(`AtndID`) as `AtndID` FROM `' . MySQL_Pre . 'ATND_Register`'
              . ' WHERE `UserMapID`=' . $_SESSION['UserMapID'] . ' AND `InDateTime`>CURDATE();';
      $AtndIDs=$Data->query($Query);
      unset($Data);

      $_SESSION['AtndID'] = (is_null($AtndIDs[0]['AtndID'])?0:$AtndIDs[0]['AtndID']);

      $Query = 'SELECT DATE_FORMAT(`InDateTime`,"%d-%m-%Y") as `Attendance Date`, '
              . ' DATE_FORMAT(`InDateTime`,"%r") as `In Time`, '
              . ' DATE_FORMAT(`OutDateTime`,"%r") as `Out Time` FROM `' . MySQL_Pre . 'ATND_Register`'
              . ' WHERE `UserMapID`=' . $_SESSION['UserMapID'] . ' ORDER BY `AtndID`;';

      $_SESSION['ATND_TIME'] = time();
      $_SESSION['InOut'] = ($_SESSION['AtndID'] === 0 ? 'In' : 'Out');
      if (WebLib::GetVal($_SESSION, 'AtndDone') !== '1') {
        ?>
        <span class="Notice"><b style="font-size:large;">Attendance for <?php echo date('d-m-Y') . ':'; ?></b>
          <input class="button" name="CmdAtnd" id="CmdAtnd" type="submit"
                 value="<?php echo $_SESSION['InOut'] . ' : ' . date('H:i:s a', $_SESSION['ATND_TIME']); ?>"/>
        </span>
        <?php
      }
      ?>
    </form>
    <?php
    //PrintArr($_SESSION);
    $Data = new MySQLiDB();
    $Data->ShowTable($Query);
    $Data->do_close();
    ?>
    <div style="clear:both;"></div>
  </div>
  <div class="pageinfo">
    <?php WebLib::PageInfo(); ?>
  </div>
  <div class="footer">
    <?php WebLib::FooterInfo(); ?>
  </div>
</body>
</html>
