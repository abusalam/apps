<?php

define("UseSMTP", false);
define("GMail_UserID", "username@gmail.com");
define("GMail_Pass", "password");
define("UserName", "Paschim Medinipur");
require_once __DIR__ . '/class.phpmailer.php';
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Asia/Kolkata');
?>
