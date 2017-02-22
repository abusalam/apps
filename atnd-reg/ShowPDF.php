<?php

require_once __DIR__ . '/../lib.inc.php';
require_once __DIR__ . '/PDF.php';
require_once __DIR__ . '/../class.MySQLiDBHelper.php';
session_start();
$Data                = new MySQLiDBHelper(HOST_Name, MySQL_User, MySQL_Pass,
                                          MySQL_DB);
$Data->where('PartID', $_POST['PartID']);
$_SESSION['Part']    = $Data->get(MySQL_Pre . 'ATND_Register', 1);
$_SESSION['Part']    = $_SESSION['Part'][0];
$_SESSION['PDFName'] = $_SESSION['Part']['ACNo']
    . '-' . $_SESSION['Part']['PartNo'] . '-'
    . $_SESSION['Part']['PartName'];
unset($Data);

if (intval($_SESSION['Part']['PartID']) > 0) {
  $pdf                   = new SRER_PDF();
  $_SESSION['TableName'] = MySQL_Pre . 'ATND_Register';
  $_SESSION['Fields']    = '`InDateTime`,`OutDateTime`';
  $ColWidths             = array(
      array('1', '2'),
      array(17, 25)
  );
  $pdf->cols             = $ColWidths;
  ShowPDF($pdf, "Form 6");
}

function ShowPDF(&$pdf,
                 $AttendanceReport,
                 $Finish = 0) {
  $ColHead = & $pdf->cols[0];
  $Data    = new MySQLiDB();
  $i       = 0;
  $Query   = "Select {$_SESSION['Fields']} from {$_SESSION['TableName']} Where PartID={$_SESSION['Part']['PartID']}";
  $Data->do_sel_query($Query);

  while ($i < $Data->ColCount) {
    $ColHead[$i] = $Data->GetCaption($Data->GetFieldName($i));
    $i++;
  }
  unset($ColHead);
  unset($Data);
  $pdf->SetTitle($AttendanceReport);
  $pdf->AddPage();
  $pdf->Details($Query, 0);
  if ($Finish) {
    $pdf->Output($_SESSION['PDFName'] . ".pdf", "D");
    unset($pdf);
    exit();
  }
}

?>