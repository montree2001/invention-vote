<?php
// config/config.php
session_start();

// Error Reporting (ปิดใน production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'invention_vote_system');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site Settings
define('SITE_NAME', 'ระบบประมวลผลสิ่งประดิษฐ์คนรุ่นใหม่');
define('SITE_VERSION', '2.0');
define('SITE_URL', 'http://localhost/invention-vote/');

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes

// File Upload Settings
define('MAX_FILE_SIZE', 10485760); // 10MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'mp4']);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// User Types
define('USER_TYPES', [
    'SUPER_ADMIN' => 'ผู้ดูแลระบบส่วนกลาง',
    'ADMIN' => 'ผู้ดูแลระบบ',
    'CHAIRMAN' => 'ประธานกรรมการ',
    'JUDGE' => 'กรรมการ'
]);

// Competition Status
define('COMPETITION_STATUS', [
    'PREPARING' => 'กำลังเตรียม',
    'REGISTRATION' => 'เปิดรับสมัคร',
    'VOTING' => 'กำลังลงคะแนน',
    'COMPLETED' => 'เสร็จสิ้น'
]);

// Invention Status
define('INVENTION_STATUS', [
    'DRAFT' => 'ร่าง',
    'SUBMITTED' => 'ส่งแล้ว',
    'APPROVED' => 'อนุมัติ',
    'REJECTED' => 'ไม่อนุมัติ'
]);

// Database Connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Include required files
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['username']) && 
           isset($_SESSION['user_type']);
}

/**
 * Get current user data
 */
function get_current_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'full_name' => $_SESSION['full_name'] ?? 'ผู้ใช้',
        'user_type' => $_SESSION['user_type'],
        'institution_name' => $_SESSION['institution_name'] ?? null
    ];
}

/**
 * Redirect function
 */
function redirect($url) {
    // ถ้า URL ไม่มี protocol ให้เติม
    if (!parse_url($url, PHP_URL_SCHEME)) {
        $url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/' . ltrim($url, '/');
    }
    
    header("Location: $url");
    exit;
}

/**
 * Clean and sanitize input
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Clean input for database
 */
function clean_input($data) {
    return sanitize_input($data);
}

/**
 * Generate CSRF Token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format Thai date
 */
function format_thai_date($date, $show_time = true) {
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    
    $thai_months = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
        5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
        9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = (int)date('n', $timestamp);
    $year = date('Y', $timestamp) + 543;
    
    $formatted = "{$day} {$thai_months[$month]} {$year}";
    
    if ($show_time) {
        $time = date('H:i', $timestamp);
        $formatted .= " {$time} น.";
    }
    
    return $formatted;
}

/**
 * Check user permission
 */
function has_permission($required_types) {
    if (!is_logged_in()) {
        return false;
    }
    
    $user_type = $_SESSION['user_type'];
    
    // SUPER_ADMIN has all permissions
    if ($user_type === 'SUPER_ADMIN') {
        return true;
    }
    
    return in_array($user_type, (array)$required_types);
}

/**
 * Require login with optional permission check
 */
function require_login($required_types = []) {
    if (!is_logged_in()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
    
    if (!empty($required_types) && !has_permission($required_types)) {
        redirect('unauthorized.php');
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
}

/**
 * Get pagination info
 */
function get_pagination($total_records, $records_per_page = 20, $current_page = 1) {
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($current_page - 1) * $records_per_page;
    
    return [
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'records_per_page' => $records_per_page,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

/**
 * Display alert message
 */
function display_alert($message, $type = 'info', $dismissible = true) {
    $icons = [
        'success' => 'bi-check-circle-fill',
        'danger' => 'bi-exclamation-triangle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'info' => 'bi-info-circle-fill'
    ];
    
    $icon = $icons[$type] ?? $icons['info'];
    $dismissible_class = $dismissible ? 'alert-dismissible' : '';
    $close_btn = $dismissible ? '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' : '';
    
    return sprintf(
        '<div class="alert alert-%s %s d-flex align-items-center" role="alert">
            <i class="bi %s me-2"></i>
            <div>%s</div>
            %s
        </div>',
        $type, $dismissible_class, $icon, $message, $close_btn
    );
}

/**
 * Get user type badge
 */
function get_user_type_badge($user_type) {
    $badges = [
        'SUPER_ADMIN' => '<span class="badge bg-danger">ผู้ดูแลระบบส่วนกลาง</span>',
        'ADMIN' => '<span class="badge bg-primary">ผู้ดูแลระบบ</span>',
        'CHAIRMAN' => '<span class="badge bg-warning text-dark">ประธานกรรมการ</span>',
        'JUDGE' => '<span class="badge bg-info">กรรมการ</span>'
    ];
    
    return $badges[$user_type] ?? '<span class="badge bg-secondary">' . $user_type . '</span>';
}

/**
 * Get status badge
 */
function get_status_badge($status, $type = 'general') {
    $badges = [
        'general' => [
            '1' => '<span class="badge bg-success">ใช้งาน</span>',
            '0' => '<span class="badge bg-secondary">ไม่ใช้งาน</span>',
            'ACTIVE' => '<span class="badge bg-success">ใช้งาน</span>',
            'INACTIVE' => '<span class="badge bg-secondary">ไม่ใช้งาน</span>'
        ],
        'competition' => [
            'PREPARING' => '<span class="badge bg-warning text-dark">กำลังเตรียม</span>',
            'REGISTRATION' => '<span class="badge bg-info">เปิดรับสมัคร</span>',
            'VOTING' => '<span class="badge bg-primary">กำลังลงคะแนน</span>',
            'COMPLETED' => '<span class="badge bg-success">เสร็จสิ้น</span>'
        ],
        'invention' => [
            'DRAFT' => '<span class="badge bg-secondary">ร่าง</span>',
            'SUBMITTED' => '<span class="badge bg-info">ส่งแล้ว</span>',
            'APPROVED' => '<span class="badge bg-success">อนุมัติ</span>',
            'REJECTED' => '<span class="badge bg-danger">ไม่อนุมัติ</span>'
        ]
    ];
    
    return $badges[$type][$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}
?>