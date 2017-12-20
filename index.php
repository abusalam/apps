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
$ResetToken = WebLib::GetVal($_REQUEST,'PasswordResetToken');
if($ResetToken!==null){
    header('Location: ' . $_SESSION['BaseURL'] . 'users/Reset.php?Token='.$ResetToken);
    exit();
}
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
                <li>Default User ID: admin Password: test@123</li>
                <li>Download Android App <a href="android/app-release.apk">Project-AIO.apk</a></li>
                <?php if(isset($_SESSION['MobileNo']) && strlen($_SESSION['MobileNo'])>0): ?>
                <li>Get Android App <a href="android">Activation Key</a>
                    for Mobile No. <?php echo $_SESSION['MobileNo']; ?>
                </li>
                <?php endif ?>
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
