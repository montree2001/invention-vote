<?php
// logout.php
require_once 'config/config.php';
require_once 'includes/auth_check.php';

// ตรวจสอบว่าเข้าสู่ระบบอยู่หรือไม่
if (is_logged_in()) {
    // บันทึก logout activity
    log_activity('LOGOUT');
    
    // ลบ cookies
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
    
    // ทำลาย session
    session_unset();
    session_destroy();
    
    // เริ่ม session ใหม่เพื่อใช้ในการแสดง message
    session_start();
}

// Redirect ไปหน้า login พร้อม message
redirect('login.php?logout=1');
?>