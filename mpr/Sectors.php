<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Monthly Performance Report');
WebLib::IncludeCSS();
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
    <form method="post" enctype="multipart/form-data"
          action="<?php
          echo WebLib::GetVal($_SERVER, 'PHP_SELF');
          ?>">
            <?php
            if (WebLib::GetVal($_POST, 'CmdUpload') === 'Upload') {
              print_r($_FILES);
            }
            ?>
      <input name="ExcelFile" type="file"/>
      <input type="submit" name="CmdUpload" value="Upload"/>
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

