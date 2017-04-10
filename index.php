<?php
if (version_compare(phpversion(), '5.3.0', 'ge')) {
  $MySQLi = extension_loaded('mysqli');
  $MySQL  = extension_loaded('mysql');
  if (($MySQLi === true) && ($MySQL === true)) {
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
WebLib::IncludeCSS('css/forms.css');
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
        <h3 class="formWrapper-h3">Staging Platform Information</h3>
        <span class="Message">
            <ul>
                <li>PHP <?php echo phpversion(); ?></li>
              <?php
              $link = mysqli_connect(HOST_Name, MySQL_User, MySQL_Pass);
              if (!$link) {
                printf('<li>Could not connect: %s</li>' . mysqli_error($link));
              } else {
                printf('<li>MySQL Server: %s</li>', mysqli_get_server_info($link));
              }
              printf('<li>MySQL Client: %s</li>', mysqli_get_client_info());
              if (function_exists('mcrypt_encrypt')) {
                echo '<li>The mcrypt extension is available.</li>';
              } else {
                echo '<li>The mcrypt extension is missing!</li>';
              }
              ?>
                <li>Default User ID: admin Password: test@123</li>
            </ul>
            <hr/>
            Note: The above information to be removed after Security Audit.
            <hr/>
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
