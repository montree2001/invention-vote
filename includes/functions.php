<?php
// includes/functions.php - ฟังก์ชันทั่วไปที่ใช้ร่วมกัน

require_once 'config/database.php';
require_once 'config/settings.php';

// ฟังก์ชันสำหรับบันทึก Audit Log
function logAuditAction($userId, $action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) 
                  VALUES (:user_id, :action, :table_name, :record_id, :old_values, :new_values, :ip_address, :user_agent)";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':table_name', $tableName);
        $stmt->bindParam(':record_id', $recordId);
        $stmt->bindParam(':old_values', $oldValues);
        $stmt->bindParam(':new_values', $newValues);
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt->bindParam(':ip_address', $ipAddress);
        $stmt->bindParam(':user_agent', $userAgent);
        
        return $stmt->execute();
    } catch(Exception $e) {
        error_log("Audit log error: " . $e->getMessage());
        return false;
    }
}

// ฟังก์ชันสำหรับตรวจสอบสิทธิ์การเข้าถึง
function checkUserRole($allowedRoles = []) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
        header('Location: login.php');
        exit();
    }
    
    if (!empty($allowedRoles) && !in_array($_SESSION['user_type'], $allowedRoles)) {
        header('Location: unauthorized.php');
        exit();
    }
    
    return true;
}

// ฟังก์ชันสำหรับสร้างรหัสผ่านแบบสุ่ม
function generateRandomPassword($length = 8) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $password;
}

// ฟังก์ชันสำหรับตรวจสอบความแข็งแรงของรหัสผ่าน
function validatePassword($password) {
    if (strlen($password) < 6) {
        return "รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร";
    }
    return true;
}

// ฟังก์ชันสำหรับตรวจสอบอีเมล
function validateEmail($email) {
    if (empty($email)) {
        return "กรุณาระบุอีเมล";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "รูปแบบอีเมลไม่ถูกต้อง";
    }
    
    return true;
}

// ฟังก์ชันสำหรับอัพโหลดไฟล์
function uploadFile($file, $targetDir, $allowedTypes = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'ไม่มีไฟล์ที่อัพโหลดหรือเกิดข้อผิดพลาด'];
    }
    
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // ตรวจสอบขนาดไฟล์
    if ($fileSize > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'ขนาดไฟล์เกินที่กำหนด'];
    }
    
    // ตรวจสอบประเภทไฟล์
    if ($allowedTypes === null) {
        $allowedTypes = ALLOWED_FILE_TYPES;
    }
    
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'ประเภทไฟล์ไม่ได้รับอนุญาต'];
    }
    
    // สร้างชื่อไฟล์ใหม่เพื่อป้องกันการซ้ำกัน
    $newFileName = uniqid() . '.' . $fileExt;
    $targetPath = $targetDir . $newFileName;
    
    // สร้างโฟลเดอร์หากไม่มี
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // อัพโหลดไฟล์
    if (move_uploaded_file($fileTmp, $targetPath)) {
        return [
            'success' => true,
            'message' => 'อัพโหลดไฟล์สำเร็จ',
            'file_name' => $newFileName,
            'file_path' => $targetPath,
            'original_name' => $fileName
        ];
    } else {
        return ['success' => false, 'message' => 'ไม่สามารถอัพโหลดไฟล์ได้'];
    }
}

// ฟังก์ชันสำหรับลบไฟล์
function deleteFile($filePath) {
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    return true;
}

// ฟังก์ชันสำหรับแปลงขนาดไฟล์
function formatFileSize($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

// ฟังก์ชันสำหรับ Pagination
function createPagination($totalRecords, $recordsPerPage, $currentPage, $baseUrl) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $pagination = '';
    
    if ($totalPages > 1) {
        $pagination .= '<nav aria-label="Page navigation">';
        $pagination .= '<ul class="pagination justify-content-center">';
        
        // ปุ่มก่อนหน้า
        if ($currentPage > 1) {
            $pagination .= '<li class="page-item">';
            $pagination .= '<a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage - 1) . '">ก่อนหน้า</a>';
            $pagination .= '</li>';
        }
        
        // หน้าที่
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            $activeClass = ($i == $currentPage) ? ' active' : '';
            $pagination .= '<li class="page-item' . $activeClass . '">';
            $pagination .= '<a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a>';
            $pagination .= '</li>';
        }
        
        // ปุ่มถัดไป
        if ($currentPage < $totalPages) {
            $pagination .= '<li class="page-item">';
            $pagination .= '<a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage + 1) . '">ถัดไป</a>';
            $pagination .= '</li>';
        }
        
        $pagination .= '</ul>';
        $pagination .= '</nav>';
    }
    
    return $pagination;
}

// ฟังก์ชันสำหรับสร้างตัวเลือกใน Select
function createSelectOptions($options, $selectedValue = '', $emptyOption = 'เลือก...') {
    $html = '';
    
    if (!empty($emptyOption)) {
        $html .= '<option value="">' . $emptyOption . '</option>';
    }
    
    foreach ($options as $value => $text) {
        $selected = ($value == $selectedValue) ? ' selected' : '';
        $html .= '<option value="' . $value . '"' . $selected . '>' . $text . '</option>';
    }
    
    return $html;
}

// ฟังก์ชันสำหรับ Debug (ใช้ในการพัฒนา)
function debug($data, $die = false) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}
?>