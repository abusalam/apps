<?php
date_default_timezone_set('Asia/Kolkata');
require_once(__DIR__ . '/MessageAPI.php');

$RT = time();
WebLib::CreateDB();
$json     = file_get_contents('php://input');
$jsonData = @json_decode($json);
if ($jsonData !== null) {
  $mAPI = new MessageAPI($jsonData, true);
  $mAPI();
}
exit();