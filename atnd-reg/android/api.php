<?php

date_default_timezone_set('Asia/Kolkata');
require_once(__DIR__ . '/AndroidAPI.php');

$RT = time();
$json     = file_get_contents('php://input');
$jsonData = @json_decode($json);
if ($jsonData !== null) {
  $mAPI = new AndroidAPI($jsonData, true);
  $mAPI();
}
exit();