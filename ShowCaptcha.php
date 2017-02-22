<?php

// captcha_display.php
require_once __DIR__ . '/lib.inc.php';
require_once __DIR__ . '/captcha/securimage.php';

$captchaId = $_GET['captchaId'];

if (empty($captchaId)) {
  die('no id');
}

// database settings must be configured in securimage.php or passed in $options

$options = array('captchaId' => $captchaId,
    'database_driver' => Securimage::SI_DRIVER_MYSQL,
    'database_host' => HOST_Name,
    'database_user' => MySQL_User,
    'database_pass' => MySQL_Pass,
    'database_name' => MySQL_DB,
    'database_table' => MySQL_Pre . "CaptchaCodes",
    'captcha_type' => Securimage::SI_CAPTCHA_MATHEMATIC,
    'no_session' => true);
$captcha = new Securimage($options);
$captcha->show();
exit;
?>