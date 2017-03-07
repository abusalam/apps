<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');
require_once(__DIR__ . '/AndroidAPI.php');

$RT = time();
WebLib::CreateDB();
$json     = file_get_contents('php://input');
$jsonData = @json_decode($json);
if ($jsonData !== null) {
  $mAPI = new AndroidAPI($jsonData);
  $mAPI();
}
exit();