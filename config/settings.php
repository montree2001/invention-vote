<?php
// config/settings.php - การตั้งค่าระบบทั่วไป

// ตั้งค่า Timezone
date_default_timezone_set('Asia/Bangkok');

// ตั้งค่า Session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // เปลี่ยนเป็น 1 สำหรับ HTTPS
session_start();

// การตั้งค่าการแสดงข้อผิดพลาด (ปิดใน Production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ค่าคงที่ของระบบ
define('SYSTEM_NAME', 'ระบบประมวลผลสิ่งประดิษฐ์คนรุ่นใหม่ (INVENTION-VOTE)');
define('SYSTEM_VERSION', '2.0');
define('BASE_URL', 'http://localhost/invention-vote-system/');
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 10485760); // 10MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'mp4']);

// การตั้งค่าการอัพโหลด
define('DOCUMENTS_PATH', UPLOAD_PATH . 'documents/');
define('IMAGES_PATH', UPLOAD_PATH . 'images/');
define('VIDEOS_PATH', UPLOAD_PATH . 'videos/');

// การตั้งค่า Pagination
define('RECORDS_PER_PAGE', 20);

// การตั้งค่า Security
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 ชั่วโมง

// User Types
define('USER_TYPE_SUPER_ADMIN', 'SUPER_ADMIN');
define('USER_TYPE_ADMIN', 'ADMIN');
define('USER_TYPE_CHAIRMAN', 'CHAIRMAN');
define('USER_TYPE_JUDGE', 'JUDGE');

// Competition Status
define('COMPETITION_STATUS_PREPARING', 'PREPARING');
define('COMPETITION_STATUS_REGISTRATION', 'REGISTRATION');
define('COMPETITION_STATUS_VOTING', 'VOTING');
define('COMPETITION_STATUS_COMPLETED', 'COMPLETED');

// Invention Status
define('INVENTION_STATUS_DRAFT', 'DRAFT');
define('INVENTION_STATUS_SUBMITTED', 'SUBMITTED');
define('INVENTION_STATUS_APPROVED', 'APPROVED');
define('INVENTION_STATUS_REJECTED', 'REJECTED');

// ฟังก์ชันสำหรับสร้าง CSRF Token
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// ฟังก์ชันสำหรับตรวจสอบ CSRF Token
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// ฟังก์ชันสำหรับฟอร์แมตวันที่
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

// ฟังก์ชันสำหรับ Sanitize Input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// ฟังก์ชันสำหรับแปลง User Type เป็นภาษาไทย
function getUserTypeText($userType) {
    switch($userType) {
        case USER_TYPE_SUPER_ADMIN:
            return 'ผู้ดูแลระบบส่วนกลาง';
        case USER_TYPE_ADMIN:
            return 'ผู้ดูแลระบบ';
        case USER_TYPE_CHAIRMAN:
            return 'ประธานกรรมการ';
        case USER_TYPE_JUDGE:
            return 'กรรมการ';
        default:
            return $userType;
    }
}

// ฟังก์ชันสำหรับแปลง Competition Status เป็นภาษาไทย
function getCompetitionStatusText($status) {
    switch($status) {
        case COMPETITION_STATUS_PREPARING:
            return 'กำลังเตรียมการ';
        case COMPETITION_STATUS_REGISTRATION:
            return 'เปิดรับสมัคร';
        case COMPETITION_STATUS_VOTING:
            return 'กำลังลงคะแนน';
        case COMPETITION_STATUS_COMPLETED:
            return 'เสร็จสิ้น';
        default:
            return $status;
    }
}

// ฟังก์ชันสำหรับสร้าง Alert Message
function setAlert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}
?>