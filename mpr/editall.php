<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Monthly Performance Report');
WebLib::IncludeCSS();
//WebLib::CreateDB();

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
WebLib::ShowMenuBar('MPR');
?>
<div class="content">
    <h2>Edit Allotment Details</h2>

    <form action="" method="post">
        Scheme Name:<input type="text" name="txtAmount">
        Block Name:<input type="text" name="txtAmount">
        Allotment Amount:<input type="text" name="txtAmount">
        Date of Allotment:<input type="date" name="txtDate">
        <input type="Submit" value="Update" name="BtnEdtAll">
    </form>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
</body>
</html>

