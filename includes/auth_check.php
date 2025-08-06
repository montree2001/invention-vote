<?php
// includes/auth_check.php
require_once __DIR__ . '/../config/config.php';

/**
 * ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
 * ถ้าไม่ได้เข้าสู่ระบบจะ redirect ไปหน้า login
 * 
 * @param array $allowed_types - ประเภทผู้ใช้ที่อนุญาตให้เข้าถึงหน้านี้
 */
function require_login($allowed_types = []) {
    // ตรวจสอบว่า session มีข้อมูลผู้ใช้หรือไม่
    if (!is_logged_in()) {
        // บันทึก URL ปัจจุบันเพื่อ redirect กลับมาหลังจาก login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
    
    // ตรวจสอบการหมดอายุของ session
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_destroy();
        redirect('login.php?timeout=1');
    }
    
    // อัปเดตเวลาความเคลื่อนไหวล่าสุด
    $_SESSION['last_activity'] = time();
    
    // ตรวจสอบสิทธิ์การเข้าถึง
    if (!empty($allowed_types)) {
        $user_type = $_SESSION['user_type'] ?? '';
        
        // ถ้าเป็น SUPER_ADMIN สามารถเข้าถึงได้ทุกหน้า
        if ($user_type !== 'SUPER_ADMIN' && !in_array($user_type, $allowed_types)) {
            redirect('unauthorized.php');
        }
    }
    
    // ตรวจสอบว่าบัญชียังใช้งานได้หรือไม่
    if (isset($_SESSION['user_id'])) {
        require_once __DIR__ . '/../classes/User.php';
        $userClass = new User();
        $user = $userClass->getUserById($_SESSION['user_id']);
        
        if (!$user || !$user['is_active']) {
            session_destroy();
            redirect('login.php?disabled=1');
        }
    }
}

/**
 * ตรวจสอบสิทธิ์การเข้าถึงเฉพาะ (ไม่ redirect)
 * 
 * @param array $allowed_types - ประเภทผู้ใช้ที่อนุญาต
 * @return boolean
 */
function check_permission($allowed_types = []) {
    if (!is_logged_in()) {
        return false;
    }
    
    if (empty($allowed_types)) {
        return true;
    }
    
    $user_type = $_SESSION['user_type'] ?? '';
    
    // SUPER_ADMIN มีสิทธิ์ทุกอย่าง
    if ($user_type === 'SUPER_ADMIN') {
        return true;
    }
    
    return in_array($user_type, $allowed_types);
}

/**
 * ตรวจสอบสิทธิ์การเข้าถึงรายการแข่งขันเฉพาะ
 * 
 * @param int $competition_id - ID ของรายการแข่งขัน
 * @return boolean
 */
function check_competition_access($competition_id) {
    if (!is_logged_in()) {
        return false;
    }
    
    $user_type = $_SESSION['user_type'];
    $user_id = $_SESSION['user_id'];
    
    // SUPER_ADMIN เข้าถึงได้ทุกรายการ
    if ($user_type === 'SUPER_ADMIN') {
        return true;
    }
    
    // ตรวจสอบว่าเป็น Admin หรือ Chairman ของรายการแข่งขันนี้หรือไม่
    require_once __DIR__ . '/../classes/Database.php';
    $db = Database::getInstance();
    
    $access = $db->selectOne(
        "SELECT id FROM competition_admins 
         WHERE competition_id = ? AND user_id = ?",
        [$competition_id, $user_id]
    );
    
    if ($access) {
        return true;
    }
    
    // สำหรับ Judge ตรวจสอบว่าได้รับมอบหมายให้ลงคะแนนในรายการนี้หรือไม่
    if ($user_type === 'JUDGE') {
        $assignment = $db->selectOne(
            "SELECT id FROM judge_assignments 
             WHERE competition_id = ? AND judge_id = ? AND is_active = 1",
            [$competition_id, $user_id]
        );
        return (bool) $assignment;
    }
    
    return false;
}

/**
 * ป้องกัน CSRF Attack
 * 
 * @param string $token - CSRF token จากฟอร์ม
 * @return boolean
 */
function verify_csrf($token = null) {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    }
    
    return verify_csrf_token($token);
}

/**
 * ล็อคเอาท์ผู้ใช้ชั่วคราว
 * 
 * @param string $reason - เหตุผลในการล็อค
 */
function lockout_user($reason = 'Security violation') {
    if (is_logged_in()) {
        require_once __DIR__ . '/../classes/User.php';
        
        // บันทึกลง audit log
        $logData = [
            'user_id' => $_SESSION['user_id'],
            'action' => 'LOCKOUT',
            'old_values' => json_encode(['reason' => $reason], JSON_UNESCAPED_UNICODE),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];
        
        $db = Database::getInstance();
        $db->insert('audit_logs', $logData);
    }
    
    session_destroy();
    redirect('login.php?lockout=1');
}

/**
 * ตรวจสอบ IP Address ที่น่าสงสัย
 * 
 * @return boolean
 */
function check_suspicious_ip() {
    $current_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    if (empty($current_ip)) {
        return false;
    }
    
    // ตรวจสอบจำนวน failed attempts จาก IP นี้
    $db = Database::getInstance();
    $recent_attempts = $db->selectOne(
        "SELECT COUNT(*) as count FROM audit_logs 
         WHERE action = 'FAILED_LOGIN' 
         AND ip_address = ? 
         AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
        [$current_ip]
    );
    
    return $recent_attempts['count'] > 10; // มากกว่า 10 ครั้งใน 1 ชั่วโมง
}

/**
 * บันทึก Activity Log
 * 
 * @param string $action - การกระทำ
 * @param string $table - ตารางที่เกี่ยวข้อง
 * @param int $record_id - ID ของข้อมูล
 * @param array $old_values - ข้อมูลเดิม
 * @param array $new_values - ข้อมูลใหม่
 */
function log_activity($action, $table = null, $record_id = null, $old_values = null, $new_values = null) {
    if (!is_logged_in()) {
        return;
    }
    
    $logData = [
        'user_id' => $_SESSION['user_id'],
        'action' => $action,
        'table_name' => $table,
        'record_id' => $record_id,
        'old_values' => $old_values ? json_encode($old_values, JSON_UNESCAPED_UNICODE) : null,
        'new_values' => $new_values ? json_encode($new_values, JSON_UNESCAPED_UNICODE) : null,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ];
    
    try {
        $db = Database::getInstance();
        $db->insert('audit_logs', $logData);
    } catch (Exception $e) {
        error_log("Failed to log activity: " . $e->getMessage());
    }
}

// ตรวจสอบ IP ที่น่าสงสัยทุกครั้งที่โหลดหน้า
if (check_suspicious_ip()) {
    // อาจจะแสดงหน้า Captcha หรือมาตรการความปลอดภัยเพิ่มเติม
    error_log("Suspicious IP detected: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
}
?>