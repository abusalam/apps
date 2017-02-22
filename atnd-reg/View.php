<?php
require_once(__DIR__ . '/../lib.inc.php');
WebLib::SetPATH(17);
WebLib::InitHTML5page('Attendance Register');
?>
</head>
<body>
  <?php
  $Data = new MySQLiDB();
  $Query = 'SELECT `UserName`,DATE_FORMAT(`InDateTime`,"%d-%m-%Y") as `Atnd Date`, '
          . ' DATE_FORMAT(`InDateTime`,"%r") as `In Time`, '
          . ' DATE_FORMAT(`OutDateTime`,"%r") as `Out Time` FROM `' . MySQL_Pre . 'ATND_View`;';

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
