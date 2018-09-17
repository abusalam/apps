<?php

date_default_timezone_set('Asia/Kolkata');
require_once(__DIR__ . '/AndroidAPI.php');

$RT = time();
WebLib::CreateDB();
$json = file_get_contents('php://input');

function ParseJson($json) {
  if ($json == '') {
    throw new RuntimeException('Invalid Input Data');
  }
  $jsonData = @json_decode($json);
  if (JSON_ERROR_NONE !== json_last_error()) {
    throw new RuntimeException('Unable to parse response body into JSON: ' . json_last_error());
  }
  return $jsonData;
}

try {
  $jsonData = ParseJson($json);
  if ($jsonData !== null) {
    $mAPI = new AndroidAPI($jsonData);
    $mAPI();
  }
} catch (Exception $e) {
  echo 'Error: ' . $e->getMessage();
}

exit();