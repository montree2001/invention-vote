<?php
// config/config.php
session_start();

// Database Settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'invention_vote_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Settings
define('SITE_NAME', 'ระบบประมวลผลสิ่งประดิษฐ์คนรุ่นใหม่');

// User Types
$user_types = [
    'SUPER_ADMIN' => 'ผู้ดูแลระบบส่วนกลาง',
    'ADMIN' => 'ผู้ดูแลระบบ',
    'CHAIRMAN' => 'ประธานกรรมการ',
    'JUDGE' => 'กรรมการ'
];

// Database Connection
$pdo = null;
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Database connection failed');
}

// Simple Functions
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function clean_input($data) {
    return htmlspecialchars(trim($data));
}
?>