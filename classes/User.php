<?php
// classes/User.php
require_once 'Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function authenticate($username, $password, $ip_address = null, $user_agent = null) {
        try {
            // ตรวจสอบ login attempts
            if ($this->isLockedOut($username, $ip_address)) {
                return [
                    'success' => false,
                    'message' => 'บัญชีถูกล็อคเนื่องจากพยายามเข้าสู่ระบบผิดหลายครั้ง กรุณารอ 15 นาที'
                ];
            }

            // ค้นหาผู้ใช้
            $user = $this->db->selectOne(
                "SELECT * FROM users WHERE username = ? AND is_active = 1",
                [$username]
            );

            if (!$user || !password_verify($password, $user['password'])) {
                $this->recordFailedAttempt($username, $ip_address);
                return [
                    'success' => false,
                    'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'
                ];
            }

            // อัปเดตข้อมูลการเข้าสู่ระบบ
            $this->updateLastLogin($user['id'], $ip_address);

            // ล็อกการเข้าสู่ระบบ
            $this->logActivity($user['id'], 'LOGIN', null, null, null, null, $ip_address, $user_agent);

            // ล้างข้อมูล login attempts
            $this->clearFailedAttempts($username, $ip_address);

            return [
                'success' => true,
                'user' => $user,
                'message' => 'เข้าสู่ระบบสำเร็จ'
            ];

        } catch (Exception $e) {
            error_log("Authentication error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง'
            ];
        }
    }

    public function getUserById($id) {
        return $this->db->selectOne(
            "SELECT id, username, first_name, last_name, email, phone, user_type, 
                    institution_name, province, is_active, last_login, created_at 
             FROM users WHERE id = ?",
            [$id]
        );
    }

    public function createUser($data) {
        try {
            // ตรวจสอบ username ซ้ำ
            if ($this->usernameExists($data['username'])) {
                return [
                    'success' => false,
                    'message' => 'ชื่อผู้ใช้นี้มีอยู่แล้วในระบบ'
                ];
            }

            // เข้ารหัสรหัสผ่าน
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['created_at'] = date('Y-m-d H:i:s');

            $userId = $this->db->insert('users', $data);

            if ($userId) {
                $this->logActivity(
                    $_SESSION['user_id'] ?? null,
                    'CREATE_USER',
                    'users',
                    $userId,
                    null,
                    $data
                );

                return [
                    'success' => true,
                    'user_id' => $userId,
                    'message' => 'สร้างผู้ใช้งานสำเร็จ'
                ];
            }

            return [
                'success' => false,
                'message' => 'ไม่สามารถสร้างผู้ใช้งานได้'
            ];

        } catch (Exception $e) {
            error_log("Create user error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง'
            ];
        }
    }

    public function updateUser($id, $data) {
        try {
            $oldData = $this->getUserById($id);
            if (!$oldData) {
                return [
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลผู้ใช้งาน'
                ];
            }

            // ถ้ามีการเปลี่ยน username ต้องตรวจสอบไม่ให้ซ้ำ
            if (isset($data['username']) && $data['username'] !== $oldData['username']) {
                if ($this->usernameExists($data['username'])) {
                    return [
                        'success' => false,
                        'message' => 'ชื่อผู้ใช้นี้มีอยู่แล้วในระบบ'
                    ];
                }
            }

            // ถ้ามีการเปลี่ยนรหัสผ่าน
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']); // ไม่เปลี่ยนรหัสผ่าน
            }

            $data['updated_at'] = date('Y-m-d H:i:s');

            $updated = $this->db->update(
                'users',
                $data,
                ['id = ?'],
                [$id]
            );

            if ($updated) {
                $this->logActivity(
                    $_SESSION['user_id'] ?? null,
                    'UPDATE_USER',
                    'users',
                    $id,
                    $oldData,
                    $data
                );

                return [
                    'success' => true,
                    'message' => 'อัปเดตข้อมูลผู้ใช้งานสำเร็จ'
                ];
            }

            return [
                'success' => false,
                'message' => 'ไม่มีข้อมูลที่เปลี่ยนแปลง'
            ];

        } catch (Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง'
            ];
        }
    }

    public function getAllUsers($filters = []) {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['user_type'])) {
            $where[] = 'user_type = ?';
            $params[] = $filters['user_type'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(username LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR institution_name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (isset($filters['is_active'])) {
            $where[] = 'is_active = ?';
            $params[] = $filters['is_active'];
        }

        $sql = "SELECT id, username, first_name, last_name, email, phone, user_type, 
                       institution_name, province, is_active, last_login, created_at 
                FROM users 
                WHERE " . implode(' AND ', $where) . " 
                ORDER BY created_at DESC";

        return $this->db->select($sql, $params);
    }

    private function usernameExists($username) {
        return $this->db->exists('users', ['username = ?'], [$username]);
    }

    private function updateLastLogin($userId, $ipAddress) {
        $this->db->update(
            'users',
            [
                'last_login' => date('Y-m-d H:i:s'),
                'last_ip' => $ipAddress
            ],
            ['id = ?'],
            [$userId]
        );
    }

    private function isLockedOut($username, $ipAddress) {
        // นับจำนวน failed attempts ใน 15 นาทีที่ผ่านมา
        $since = date('Y-m-d H:i:s', time() - LOCKOUT_TIME);
        
        $count = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM audit_logs 
             WHERE action = 'FAILED_LOGIN' 
             AND (old_values LIKE ? OR ip_address = ?) 
             AND created_at > ?",
            ["%\"username\":\"$username\"%", $ipAddress, $since]
        );

        return $count['count'] >= MAX_LOGIN_ATTEMPTS;
    }

    private function recordFailedAttempt($username, $ipAddress) {
        $this->logActivity(
            null,
            'FAILED_LOGIN',
            null,
            null,
            ['username' => $username],
            null,
            $ipAddress
        );
    }

    private function clearFailedAttempts($username, $ipAddress) {
        // ลบ failed attempts logs ที่เกี่ยวข้อง
        $this->db->delete(
            'audit_logs',
            [
                'action = ?',
                '(old_values LIKE ? OR ip_address = ?)'
            ],
            ['FAILED_LOGIN', "%\"username\":\"$username\"%", $ipAddress]
        );
    }

    private function logActivity($userId, $action, $table = null, $recordId = null, $oldValues = null, $newValues = null, $ipAddress = null, $userAgent = null) {
        $logData = [
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $table,
            'record_id' => $recordId,
            'old_values' => $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
            'new_values' => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
            'ip_address' => $ipAddress ?: $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $userAgent ?: $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db->insert('audit_logs', $logData);
        } catch (Exception $e) {
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
}
?>