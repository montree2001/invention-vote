<?php
// logout.php - ออกจากระบบ
require_once 'config/settings.php';
require_once 'includes/auth.php';

// ออกจากระบบ
$auth->logout();

// Redirect ไปหน้า login
header('Location: login.php');
exit();
?>