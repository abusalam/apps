<?php
session_start();
$_SESSION['wid'] = $_REQUEST['wid'];
echo "<script>window.location.href='editwork.php'</script>";
?>