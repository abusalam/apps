<?php
if (version_compare(phpversion(), '5.3.0', 'ge')) {
  $MySQLi = extension_loaded('mysqli');
  $MySQL = extension_loaded('mysql');
  if (( $MySQLi === true) && ( $MySQL === true)) {
    include_once __DIR__ . '/lib.inc.php';
    WebLib::CreateDB();
  } else {
    die('Required PHP Extensions: mysql and mysqli  <br/>'
            . ' But you have: ' . implode(', ', get_loaded_extensions()));
  }
} else {
  die('Required PHP Version: 5.3 or later. <br/>'
          . ' You have: ' . phpversion());
}

WebLib::SetPATH();
WebLib::InitHTML5page('Home');
WebLib::IncludeCSS();
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
  WebLib::ShowMenuBar('APPS');
?>
<div class="content">
    <div class="formWrapper">
        <h3 class="formWrapper-h3">PHP <?php echo phpversion(); ?></h3>
      <?php
        $link = mysqli_connect(HOST_Name, MySQL_User, MySQL_Pass);
        if (!$link) {
          printf('Could not connect: %s<br/>' . mysqli_error($link));
        } else {
          printf("MySQL Server: %s<br/>", mysqli_get_server_info($link));
        }
        printf("MySQL Client: %s<br/>", mysqli_get_client_info());
        if (function_exists('mcrypt_encrypt')) {
          echo "The mcrypt extension is available.<br/>";
        } else {
          echo "The mcrypt extension is missing!<br/>";
        }
      ?>
        <span class="Message">
            <strong>Default User ID: admin Password: test@123</strong>
        </span>
    </div>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
</body>
</html>
