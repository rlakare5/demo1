<?php
session_start();
unset($_SESSION['super_admin_id']);
unset($_SESSION['super_admin_name']);
header('Location: login.php');
exit;
?>
