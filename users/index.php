<?php
require_once __DIR__ . '/../lib.inc.php';
WebLib::AuthSession();
WebLib::Html5Header("User Activity");
WebLib::IncludeCSS();
WebLib::CreateDB();
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
  WebLib::ShowMenuBar('USER');
  ?>
  <div class="content">
    <?php
    if (WebLib::GetVal($_SESSION, 'ReloadMenus')) {
      unset($_SESSION['RestrictedMenus']);
      unset($_SESSION['ReloadMenus']);
    }
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
