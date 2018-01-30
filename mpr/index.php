<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Monthly Performance Report');
WebLib::IncludeCSS();
WebLib::CreateDB();
?>
</head>
<body>
<div class="TopPanel">
    <div class="LeftPanelSide"></div>
    <div class="RightPanelSide"></div>
    <h1><?php echo htmlentities($_SESSION['UserName']); ?></h1>
</div>
<div class="Header"></div>
<?php
WebLib::ShowMenuBar('MPR');
?>
<div class="content">
  <?php
  //$userid=$_SESSION['UserMapID'];
  //$DB = new MySQLiDBHelper();
  //$DB->where('UserMapID',$userid);
  //$user=$DB->get(MySQL_Pre.'MPR_UserAccess');
  //$_SESSION['UserLevel']=$user['0']['UserLevel'];
  //$_SESSION['BlockCode']=$user['0']['BlockCode'];
  ?>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
</body>
</html>

