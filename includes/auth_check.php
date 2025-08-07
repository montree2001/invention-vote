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
        session_start();
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
        try {
            $db = Database::getInstance();
            $user = $db->selectOne(
                "SELECT is_active FROM users WHERE id = ?",
                [$_SESSION['user_id']]
            );
            
            if (!$user || !$user['is_active']) {
                session_destroy();
                session_start();
                redirect('login.php?disabled=1');
            }
        } catch (Exception $e) {
            error_log("User status check error: " . $e->getMessage());
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
    
    try {
        $db = Database::getInstance();
        
        // ตรวจสอบว่าเป็น Admin หรือ Chairman ของรายการแข่งขันนี้หรือไม่
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
    } catch (Exception $e) {
        error_log("Competition access check error: " . $e->getMessage());
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
        // บันทึกลง audit log
        try {
            $logData = [
                'user_id' => $_SESSION['user_id'],
                'action' => 'LOCKOUT',
                'old_values' => json_encode(['reason' => $reason], JSON_UNESCAPED_UNICODE),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db = Database::getInstance();
            $db->insert('audit_logs', $logData);
        } catch (Exception $e) {
            error_log("Lockout logging error: " . $e->getMessage());
        }
    }
    
    session_destroy();
    session_start();
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
    
    try {
        // ตรวจสอบจำนวน failed attempts จาก IP นี้
        $db = Database::getInstance();
        $recent_attempts = $db->selectOne(
            "SELECT COUNT(*) as count FROM audit_logs 
             WHERE action = 'FAILED_LOGIN' 
             AND ip_address = ? 
             AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [$current_ip]
        );
        
        return ($recent_attempts['count'] ?? 0) > 10; // มากกว่า 10 ครั้งใน 1 ชั่วโมง
    } catch (Exception $e) {
        error_log("Suspicious IP check error: " . $e->getMessage());
        return false;
    }
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
        return false;
    }
    
    $logData = [
        'user_id' => $_SESSION['user_id'],
        'action' => $action,
        'table_name' => $table,
        'record_id' => $record_id,
        'old_values' => $old_values ? json_encode($old_values, JSON_UNESCAPED_UNICODE) : null,
        'new_values' => $new_values ? json_encode($new_values, JSON_UNESCAPED_UNICODE) : null,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    try {
        $db = Database::getInstance();
        return $db->insert('audit_logs', $logData);
    } catch (Exception $e) {
        error_log("Failed to log activity: " . $e->getMessage());
        return false;
    }
}

/**
 * ตรวจสอบว่าผู้ใช้สามารถลงคะแนนสิ่งประดิษฐ์นี้ได้หรือไม่
 * 
 * @param int $invention_id - ID ของสิ่งประดิษฐ์
 * @param int $judge_id - ID ของกรรมการ (ถ้าไม่ระบุจะใช้ user ปัจจุบัน)
 * @return array - ['allowed' => boolean, 'reason' => string]
 */
function check_voting_permission($invention_id, $judge_id = null) {
    if (!is_logged_in()) {
        return ['allowed' => false, 'reason' => 'ไม่ได้เข้าสู่ระบบ'];
    }
    
    $judge_id = $judge_id ?: $_SESSION['user_id'];
    $user_type = $_SESSION['user_type'];
    
    // เฉพาะ JUDGE เท่านั้นที่ลงคะแนนได้
    if ($user_type !== 'JUDGE') {
        return ['allowed' => false, 'reason' => 'เฉพาะกรรมการเท่านั้นที่สามารถลงคะแนนได้'];
    }
    
    try {
        $db = Database::getInstance();
        
        // ดึงข้อมูลสิ่งประดิษฐ์และผู้ใช้
        $invention = $db->selectOne(
            "SELECT i.*, u.institution_name as judge_institution 
             FROM inventions i, users u 
             WHERE i.id = ? AND u.id = ?",
            [$invention_id, $judge_id]
        );
        
        if (!$invention) {
            return ['allowed' => false, 'reason' => 'ไม่พบข้อมูลสิ่งประดิษฐ์'];
        }
        
        // ตรวจสอบว่าเป็นสถานศึกษาเดียวกันหรือไม่
        if ($invention['institution_name'] === $invention['judge_institution']) {
            return ['allowed' => false, 'reason' => 'ไม่สามารถลงคะแนนสิ่งประดิษฐ์จากสถานศึกษาเดียวกันได้'];
        }
        
        // ตรวจสอบข้อจำกัดเพิ่มเติม
        $restriction = $db->selectOne(
            "SELECT * FROM voting_restrictions 
             WHERE invention_id = ? AND judge_id = ?",
            [$invention_id, $judge_id]
        );
        
        if ($restriction) {
            $reason_map = [
                'SAME_INSTITUTION' => 'สถานศึกษาเดียวกัน',
                'MANUAL_BLOCK' => 'ถูกจำกัดโดยผู้ดูแลระบบ'
            ];
            $reason = $reason_map[$restriction['restriction_type']] ?? 'มีข้อจำกัดในการลงคะแนน';
            
            return ['allowed' => false, 'reason' => $reason];
        }
        
        // ตรวจสอบว่าผลได้รับการรับรองแล้วหรือไม่
        $approved = $db->selectOne(
            "SELECT ra.id FROM result_approvals ra
             JOIN inventions i ON ra.competition_id = i.competition_id AND ra.category_id = i.category_id
             WHERE i.id = ? AND ra.is_active = 1",
            [$invention_id]
        );
        
        if ($approved) {
            return ['allowed' => false, 'reason' => 'ผลการแข่งขันได้รับการรับรองแล้ว ไม่สามารถแก้ไขคะแนนได้'];
        }
        
        return ['allowed' => true, 'reason' => ''];
        
    } catch (Exception $e) {
        error_log("Voting permission check error: " . $e->getMessage());
        return ['allowed' => false, 'reason' => 'เกิดข้อผิดพลาดในระบบ'];
    }
}

/**
 * ตรวจสอบสถานะการลงคะแนนของกรรมการ
 * 
 * @param int $competition_id - ID ของการแข่งขัน
 * @param int $category_id - ID ของประเภท
 * @param int $judge_id - ID ของกรรมการ (ถ้าไม่ระบุจะใช้ user ปัจจุบัน)
 * @return array - ข้อมูลสถานะการลงคะแนน
 */
function get_voting_progress($competition_id, $category_id, $judge_id = null) {
    $judge_id = $judge_id ?: $_SESSION['user_id'];
    
    try {
        $db = Database::getInstance();
        
        // นับจำนวนสิ่งประดิษฐ์ทั้งหมดในประเภทนี้
        $total_inventions = $db->selectOne(
            "SELECT COUNT(*) as count FROM inventions 
             WHERE competition_id = ? AND category_id = ? AND status = 'APPROVED'",
            [$competition_id, $category_id]
        );
        
        // นับจำนวนที่ลงคะแนนแล้ว
        $voted_inventions = $db->selectOne(
            "SELECT COUNT(DISTINCT vs.invention_id) as count 
             FROM voting_scores vs
             JOIN inventions i ON vs.invention_id = i.id
             WHERE vs.competition_id = ? AND i.category_id = ? AND vs.judge_id = ?",
            [$competition_id, $category_id, $judge_id]
        );
        
        $total = $total_inventions['count'] ?? 0;
        $voted = $voted_inventions['count'] ?? 0;
        $percentage = $total > 0 ? round(($voted / $total) * 100, 2) : 0;
        
        return [
            'total_inventions' => $total,
            'voted_inventions' => $voted,
            'remaining_inventions' => $total - $voted,
            'percentage' => $percentage,
            'is_completed' => $voted >= $total
        ];
        
    } catch (Exception $e) {
        error_log("Voting progress error: " . $e->getMessage());
        return [
            'total_inventions' => 0,
            'voted_inventions' => 0,
            'remaining_inventions' => 0,
            'percentage' => 0,
            'is_completed' => false
        ];
    }
}

// ตรวจสอบ IP ที่น่าสงสัยทุกครั้งที่โหลดหน้า
if (check_suspicious_ip()) {
    // บันทึก log แต่ไม่ block เพื่อไม่ให้กระทบกับผู้ใช้ปกติ
    error_log("Suspicious IP detected: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    // อาจจะแสดง warning หรือเพิ่มมาตรการความปลอดภัยเพิ่มเติมในอนาคต
}
?>