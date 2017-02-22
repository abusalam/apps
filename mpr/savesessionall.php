<?php
session_start();
$_SESSION['aid'] = $_REQUEST['aid'];
echo "<script>window.location.href='editall.php'</script>";
?>