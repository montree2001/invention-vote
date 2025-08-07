<?php
// includes/auth.php - ระบบการตรวจสอบสิทธิ์

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

class Auth {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // ฟังก์ชันการเข้าสู่ระบบ
    public function login($username, $password) {
        try {
            $query = "SELECT id, username, password, first_name, last_name, email, user_type, institution_name, is_active 
                      FROM users 
                      WHERE username = :username AND is_active = 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch();
                
                if (password_verify($password, $user['password'])) {
                    // อัพเดทข้อมูลการเข้าสู่ระบบล่าสุด
                    $this->updateLastLogin($user['id']);
                    
                    // สร้าง Session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['user_type'] = $user['user_type'];
                    $_SESSION['institution_name'] = $user['institution_name'];
                    $_SESSION['login_time'] = time();
                    
                    // บันทึก Audit Log
                    logAuditAction($user['id'], 'LOGIN');
                    
                    return [
                        'success' => true,
                        'message' => 'เข้าสู่ระบบสำเร็จ',
                        'user_type' => $user['user_type']
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'รหัสผ่านไม่ถูกต้อง'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'ไม่พบผู้ใช้งานหรือบัญชีถูกระงับ'
                ];
            }
        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
            ];
        }
    }
    
    // ฟังก์ชันออกจากระบบ
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            logAuditAction($_SESSION['user_id'], 'LOGOUT');
        }
        
        // ทำลาย Session
        session_unset();
        session_destroy();
        
        // เริ่ม Session ใหม่สำหรับ message
        session_start();
        setAlert('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }
    
    // ฟังก์ชันตรวจสอบการเข้าสู่ระบบ
    public function isLoggedIn() {
        if (isset($_SESSION['user_id']) && isset($_SESSION['login_time'])) {
            // ตรวจสอบ Session Timeout
            if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
                $this->logout();
                return false;
            }
            return true;
        }
        return false;
    }
    
    // ฟังก์ชันตรวจสอบสิทธิ์
    public function hasPermission($allowedRoles = []) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        if (empty($allowedRoles)) {
            return true;
        }
        
        return in_array($_SESSION['user_type'], $allowedRoles);
    }
    
    // ฟังก์ชันบังคับการเข้าสู่ระบบ
    public function requireLogin($allowedRoles = []) {
        if (!$this->isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login.php');
            exit();
        }
        
        if (!$this->hasPermission($allowedRoles)) {
            header('Location: ' . BASE_URL . 'unauthorized.php');
            exit();
        }
    }
    
    // ฟังก์ชันการเปลี่ยนรหัสผ่าน
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // ตรวจสอบรหัสผ่านปัจจุบัน
            $query = "SELECT password FROM users WHERE id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch();
                
                if (password_verify($currentPassword, $user['password'])) {
                    // ตรวจสอบความแข็งแรงของรหัสผ่านใหม่
                    $validation = validatePassword($newPassword);
                    if ($validation !== true) {
                        return [
                            'success' => false,
                            'message' => $validation
                        ];
                    }
                    
                    // อัพเดทรหัสผ่าน
                    $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
                    $updateQuery = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :user_id";
                    $updateStmt = $this->db->prepare($updateQuery);
                    $updateStmt->bindParam(':password', $hashedPassword);
                    $updateStmt->bindParam(':user_id', $userId);
                    
                    if ($updateStmt->execute()) {
                        logAuditAction($userId, 'PASSWORD_CHANGE', 'users', $userId);
                        return [
                            'success' => true,
                            'message' => 'เปลี่ยนรหัสผ่านสำเร็จ'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'ไม่สามารถเปลี่ยนรหัสผ่านได้'
                        ];
                    }
                } else {
                    return [
                        'success' => false,
                        'message' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'ไม่พบผู้ใช้งาน'
                ];
            }
        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
            ];
        }
    }
    
    // ฟังก์ชันอัพเดทเวลาการเข้าสู่ระบบล่าสุด
    private function updateLastLogin($userId) {
        try {
            $query = "UPDATE users SET last_login = NOW(), last_ip = :ip_address WHERE id = :user_id";
            $stmt = $this->db->prepare($query);
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            $stmt->bindParam(':ip_address', $ipAddress);
            $stmt->bindParam(':user_id', $userId);
            
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Update last login error: " . $e->getMessage());
            return false;
        }
    }
    
    // ฟังก์ชันดึงข้อมูลผู้ใช้
    public function getUserData($userId) {
        try {
            $query = "SELECT id, username, first_name, last_name, email, phone, user_type, 
                             institution_name, province, is_active, last_login, created_at 
                      FROM users 
                      WHERE id = :user_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                return $stmt->fetch();
            }
            
            return false;
        } catch(Exception $e) {
            error_log("Get user data error: " . $e->getMessage());
            return false;
        }
    }
    
    // ฟังก์ชันตรวจสอบ Username ซ้ำ
    public function isUsernameExists($username, $excludeUserId = null) {
        try {
            $query = "SELECT id FROM users WHERE username = :username";
            $params = [':username' => $username];
            
            if ($excludeUserId) {
                $query .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeUserId;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->rowCount() > 0;
        } catch(Exception $e) {
            error_log("Check username exists error: " . $e->getMessage());
            return true; // Return true เพื่อป้องกันการสร้าง username ซ้ำในกรณีเกิดข้อผิดพลาด
        }
    }
    
    // ฟังก์ชันดึงสิทธิ์การเข้าถึงตาม User Type
    public function getAccessPermissions($userType) {
        $permissions = [
            USER_TYPE_SUPER_ADMIN => [
                'users' => ['view', 'create', 'edit', 'delete'],
                'competitions' => ['view', 'create', 'edit', 'delete'],
                'levels' => ['view', 'create', 'edit', 'delete'],
                'categories' => ['view', 'create', 'edit', 'delete'],
                'scoring_criteria' => ['view', 'create', 'edit', 'delete'],
                'reports' => ['view', 'export'],
                'settings' => ['view', 'edit']
            ],
            USER_TYPE_ADMIN => [
                'inventions' => ['view', 'edit', 'approve'],
                'judges' => ['view', 'assign', 'manage'],
                'chairman' => ['view', 'assign'],
                'scoring' => ['manage', 'control'],
                'reports' => ['view', 'export'],
                'monitoring' => ['view']
            ],
            USER_TYPE_CHAIRMAN => [
                'inventions' => ['view'],
                'scoring' => ['approve', 'view'],
                'reports' => ['view', 'export']
            ],
            USER_TYPE_JUDGE => [
                'inventions' => ['view'],
                'scoring' => ['vote', 'view_own']
            ]
        ];
        
        return $permissions[$userType] ?? [];
    }
}

// สร้าง Instance ของ Auth Class
$auth = new Auth();
?>