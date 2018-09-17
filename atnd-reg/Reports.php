<?php
require_once(__DIR__ . '/../lib.inc.php');
require_once __DIR__ . '/PDF.php';
WebLib::AuthSession();

if (WebLib::GetVal($_POST, 'FormName') === 'Download') {
  $pdf       = new ATRG_PDF();
  $Query     = 'SELECT 1,DATE_FORMAT(`InDateTime`,"%d-%m-%Y %W") as `Attendance Date`, '
    . ' DATE_FORMAT(`InDateTime`,"%r") as `In Time`, '
    . ' DATE_FORMAT(`OutDateTime`,"%r") as `Out Time` '
    . ' FROM `' . MySQL_Pre . 'ATND_Register`'
    . ' WHERE DATE_FORMAT(`InDateTime`,"%m-%Y")=\''
    . WebLib::GetVal($_POST, 'MonYr', true) . '\''
    . ' AND `UserMapID`=' . $_SESSION['UserMapID']
    . ' ORDER BY `AtndID`';
  $ColWidths = array(
    array('Sl No.', 'Attendance Date', 'In Time', 'Out Time'),
    array(11, 59, 59, 59)
  );
  $Month     = substr(WebLib::GetVal($_POST, 'MonYr'), 0, 2);
  $Year      = substr(WebLib::GetVal($_POST, 'MonYr'), -4);
  $AtndMonth = date("F", mktime(0, 0, 0, $Month, 7, $Year)) . " - " . $Year;
  $Name      = WebLib::GetVal($_SESSION, 'UserName');
  //WebLib::GetVal($_POST, 'MonYr');
  $pdf->cols           = $ColWidths;
  $_SESSION['PDFName'] = 'Generated From: ' . $_SERVER['REMOTE_ADDR'];
  $pdf->Setauthor($Name);
  $pdf->SetTitle($AtndMonth);
  $pdf->AddPage();
  $pdf->Details($Query, 1);
  $pdf->Output('AttendanceRegister-'
    . WebLib::GetVal($_POST, 'MonYr', true) . ".pdf", "D");
  exit();
}

WebLib::Html5Header('Attendance Report');
WebLib::IncludeCSS();
WebLib::IncludeCSS();
WebLib::JQueryInclude();
WebLib::IncludeCSS('css/chosen.css');
WebLib::IncludeJS('js/chosen.jquery.min.js');
WebLib::IncludeJS('atnd-reg/Report.js');

$Data = new MySQLiDB();
?>
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
    <h2>Attendance Report</h2>
    <hr/>
    <form name="frmAtndRpt" method="post" action="Reports.php">
        <div class="FieldGroup">
            <label for="OfficeSL">
                <label for="textfield"><strong>Month:</strong></label>
            </label>
            <select id="MonYr" name="MonYr"
                    data-placeholder="Select Month"/>
          <?php
          $Query = 'Select DATE_FORMAT(`InDateTime`,"%m-%Y") as `MonYr`,'
            . ' DATE_FORMAT(`InDateTime`,"%b-%Y") as `MonthYear` '
            . ' FROM `' . MySQL_Pre . 'ATND_Register`'
            . ' Where `UserMapID`=' . $_SESSION['UserMapID']
            . ' GROUP BY DATE_FORMAT(`InDateTime`,"%m-%Y")';
          $Data->show_sel('MonYr', 'MonthYear', $Query,
            WebLib::GetVal($_POST, 'MonYr'));
          ?>
            </select>
        </div>
        <input type="submit" name="FormName" value="Show"/>
        <input type="submit" name="FormName" value="Download"/>
        <hr/>
        <br/>
    </form>
  <?php
  $Query = 'SELECT DATE_FORMAT(`InDateTime`,"%d-%m-%Y %W") as `Attendance Date`, '
    . ' DATE_FORMAT(`InDateTime`,"%r") as `In Time`, '
    . ' DATE_FORMAT(`OutDateTime`,"%r") as `Out Time`'
    . ' FROM `' . MySQL_Pre . 'ATND_Register`'
    . ' WHERE DATE_FORMAT(`InDateTime`,"%m-%Y")=\''
    . WebLib::GetVal($_POST, 'MonYr', true) . '\''
    . ' AND `UserMapID`=' . $_SESSION['UserMapID']
    . ' ORDER BY `AtndID`;';
  $Data->ShowTable($Query);
  $Data->do_close();
  ?>
    <br/>
</div>
<div class="pageinfo"><?php WebLib::PageInfo(); ?></div>
<div class="footer"><?php WebLib::FooterInfo(); ?></div>
</body>
</html>
