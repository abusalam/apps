<?php
session_start();
$_SESSION['pid'] = $_REQUEST['pid'];
echo "<script>window.location.href='editprg.php'</script>";
?>