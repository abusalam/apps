<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once(__DIR__ . '/../lib.inc.php');
WebLib::SetPATH(17);
WebLib::InitHTML5page('SMS Gateway Report');
?>
</head>
<body>
  <?php
  $Data = new MySQLiDB();
  $Query = 'SELECT `MessageID`, `MobileNo`, `TxtSMS` as `SMS`, '
          . ' DATE_FORMAT(`SubmitTime`,"%r") as `Out Time`,`Response` FROM `' . MySQL_Pre . 'GatewaySMS`;';

  function callback($buffer) {
    // replace all the apples with oranges
    return (str_replace('width="100%" ', "", $buffer));
  }

  ob_start("callback");
  $Data->ShowTable($Query);
  ob_end_flush();
  $Data->do_close();
  ?>
</body>
</html>