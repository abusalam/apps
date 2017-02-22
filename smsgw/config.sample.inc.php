<?php

/**
 * Defines the SMS Gateway Credentials
 */

if (file_exists(__DIR__ . '/../config.inc.php')) {
    require_once __DIR__ . '/../config.inc.php';
} else {
    require_once __DIR__ . '/../config.sample.inc.php';
}

define('SMSGW_URL', 'http://localhost/sendmsg.php');

define('SMSGW_USER', 'user');

define('SMSGW_PASS', 'password');

define('SMSGW_SENDER', 'PASMED');

define('SMSGW_IP', '164.100.14.9');

define('AdminMobile', '9876543210');
?>
