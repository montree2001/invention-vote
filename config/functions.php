<?php
// config/functions.php

/**
 * Helper Functions สำหรับระบบ INVENTION-VOTE
 */

/**
 * แสดงข้อความ Alert ในรูปแบบ Bootstrap
 * 
 * @param string $message - ข้อความที่จะแสดง
 * @param string $type - ประเภทของ alert (success, danger, warning, info)
 * @param boolean $dismissible - สามารถปิดได้หรือไม่
 * @return string
 */
function show_alert($message, $type = 'info', $dismissible = true) {
    $dismissible_class = $dismissible ? 'alert-dismissible' : '';
    $dismiss_button = $dismissible ? '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' : '';
    
    $icons = [
        'success' => 'bi-check-circle-fill',
        'danger' => 'bi-exclamation-triangle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'info' => 'bi-info-circle-fill'
    ];
    
    $icon = $icons[$type] ?? $icons['info'];
    
    return "
    <div class='alert alert-{$type} {$dismissible_class} d-flex align-items-center' role='alert'>
        <i class='bi {$icon} me-2'></i>
        <div>{$message}</div>
        {$dismiss_button}
    </div>";
}

/**
 * แสดง Badge สถานะ
 * 
 * @param string $status - สถานะ
 * @param array $config - การตั้งค่าสีและข้อความ
 * @return string
 */
function show_status_badge($status, $config = []) {
    $default_config = [
        'ACTIVE' => ['class' => 'bg-success', 'text' => 'ใช้งาน'],
        'INACTIVE' => ['class' => 'bg-secondary', 'text' => 'ไม่ใช้งาน'],
        'PREPARING' => ['class' => 'bg-warning', 'text' => 'กำลังเตรียม'],
        'REGISTRATION' => ['class' => 'bg-info', 'text' => 'เปิดรับสมัคร'],
        'VOTING' => ['class' => 'bg-primary', 'text' => 'กำลังลงคะแนน'],
        'COMPLETED' => ['class' => 'bg-success', 'text' => 'เสร็จสิ้น'],
        'DRAFT' => ['class' => 'bg-secondary', 'text' => 'ร่าง'],
        'SUBMITTED' => ['class' => 'bg-info', 'text' => 'ส่งแล้ว'],
        'APPROVED' => ['class' => 'bg-success', 'text' => 'อนุมัติ'],
        'REJECTED' => ['class' => 'bg-danger', 'text' => 'ไม่อนุมัติ']
    ];
    
    $merged_config = array_merge($default_config, $config);
    
    if (!isset($merged_config[$status])) {
        return "<span class='badge bg-secondary'>{$status}</span>";
    }
    
    $badge_config = $merged_config[$status];
    return "<span class='badge {$badge_config['class']}'>{$badge_config['text']}</span>";
}

/**
 * แสดง User Type Badge
 * 
 * @param string $user_type
 * @return string
 */
function show_user_type_badge($user_type) {
    $config = [
        'SUPER_ADMIN' => ['class' => 'bg-danger', 'text' => 'ผู้ดูแลระบบส่วนกลาง'],
        'ADMIN' => ['class' => 'bg-primary', 'text' => 'ผู้ดูแลระบบ'],
        'CHAIRMAN' => ['class' => 'bg-warning', 'text' => 'ประธานกรรมการ'],
        'JUDGE' => ['class' => 'bg-info', 'text' => 'กรรมการ']
    ];
    
    return show_status_badge($user_type, $config);
}

/**
 * สร้าง Pagination Links
 * 
 * @param int $current_page - หน้าปัจจุบัน
 * @param int $total_pages - จำนวนหน้าทั้งหมด
 * @param string $base_url - URL พื้นฐาน
 * @param array $params - พารามิเตอร์เพิ่มเติม
 * @return string
 */
function create_pagination($current_page, $total_pages, $base_url, $params = []) {
    if ($total_pages <= 1) return '';
    
    $query_string = http_build_query($params);
    $separator = strpos($base_url, '?') !== false ? '&' : '?';
    
    $pagination = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($current_page > 1) {
        $prev_page = $current_page - 1;
        $url = $base_url . $separator . $query_string . ($query_string ? '&' : '') . "page={$prev_page}";
        $pagination .= "<li class='page-item'><a class='page-link' href='{$url}'>ก่อนหน้า</a></li>";
    } else {
        $pagination .= "<li class='page-item disabled'><span class='page-link'>ก่อนหน้า</span></li>";
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    if ($start > 1) {
        $url = $base_url . $separator . $query_string . ($query_string ? '&' : '') . "page=1";
        $pagination .= "<li class='page-item'><a class='page-link' href='{$url}'>1</a></li>";
        if ($start > 2) {
            $pagination .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $url = $base_url . $separator . $query_string . ($query_string ? '&' : '') . "page={$i}";
        $active = $i == $current_page ? 'active' : '';
        $pagination .= "<li class='page-item {$active}'><a class='page-link' href='{$url}'>{$i}</a></li>";
    }
    
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $pagination .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
        }
        $url = $base_url . $separator . $query_string . ($query_string ? '&' : '') . "page={$total_pages}";
        $pagination .= "<li class='page-item'><a class='page-link' href='{$url}'>{$total_pages}</a></li>";
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $next_page = $current_page + 1;
        $url = $base_url . $separator . $query_string . ($query_string ? '&' : '') . "page={$next_page}";
        $pagination .= "<li class='page-item'><a class='page-link' href='{$url}'>ถัดไป</a></li>";
    } else {
        $pagination .= "<li class='page-item disabled'><span class='page-link'>ถัดไป</span></li>";
    }
    
    $pagination .= '</ul></nav>';
    
    return $pagination;
}

/**
 * แปลงวันที่เป็นรูปแบบไทย
 * 
 * @param string $date - วันที่ในรูปแบบ Y-m-d หรือ Y-m-d H:i:s
 * @param boolean $show_time - แสดงเวลาหรือไม่
 * @return string
 */
function thai_date($date, $show_time = false) {
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    
    $thai_months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = (int)date('n', $timestamp);
    $year = date('Y', $timestamp) + 543;
    
    $formatted = "{$day} {$thai_months[$month]} {$year}";
    
    if ($show_time) {
        $time = date('H:i', $timestamp);
        $formatted .= " เวลา {$time} น.";
    }
    
    return $formatted;
}

/**
 * คำนวณอายุจากวันเกิด
 * 
 * @param string $birthdate - วันเกิดในรูปแบบ Y-m-d
 * @return int
 */
function calculate_age($birthdate) {
    if (empty($birthdate) || $birthdate == '0000-00-00') {
        return 0;
    }
    
    $today = new DateTime();
    $birth = new DateTime($birthdate);
    return $today->diff($birth)->y;
}

/**
 * ตรวจสอบและสร้างโฟลเดอร์หากไม่มี
 * 
 * @param string $path - path ของโฟลเดอร์
 * @return boolean
 */
function ensure_directory_exists($path) {
    if (!is_dir($path)) {
        return mkdir($path, 0755, true);
    }
    return true;
}

/**
 * อัปโหลดไฟล์
 * 
 * @param array $file - ข้อมูลไฟล์จาก $_FILES
 * @param string $upload_dir - โฟลเดอร์ปลายทาง
 * @param array $allowed_types - ประเภทไฟล์ที่อนุญาต
 * @param int $max_size - ขนาดไฟล์สูงสุด (bytes)
 * @return array
 */
function upload_file($file, $upload_dir, $allowed_types = null, $max_size = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'ไม่พบไฟล์หรือเกิดข้อผิดพลาดในการอัปโหลด'];
    }
    
    // ตรวจสอบขนาดไฟล์
    $max_size = $max_size ?: MAX_FILE_SIZE;
    if ($file['size'] > $max_size) {
        $max_mb = round($max_size / 1024 / 1024, 2);
        return ['success' => false, 'message' => "ไฟล์มีขนาดเกิน {$max_mb} MB"];
    }
    
    // ตรวจสอบประเภทไฟล์
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_types = $allowed_types ?: ALLOWED_FILE_TYPES;
    
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'ประเภทไฟล์ไม่ได้รับอนุญาต'];
    }
    
    // สร้างโฟลเดอร์หากไม่มี
    if (!ensure_directory_exists($upload_dir)) {
        return ['success' => false, 'message' => 'ไม่สามารถสร้างโฟลเดอร์ปลายทางได้'];
    }
    
    // สร้างชื่อไฟล์ใหม่เพื่อป้องกันชื่อซ้ำ
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $filepath = $upload_dir . '/' . $filename;
    
    // ย้ายไฟล์ไปยังโฟลเดอร์ปลายทาง
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'original_name' => $file['name'],
            'size' => $file['size']
        ];
    }
    
    return ['success' => false, 'message' => 'ไม่สามารถบันทึกไฟล์ได้'];
}

/**
 * ลบไฟล์
 * 
 * @param string $filepath - path ของไฟล์
 * @return boolean
 */
function delete_file($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return true; // ถือว่าลบสำเร็จถ้าไฟล์ไม่มีอยู่แล้ว
}

/**
 * แปลงขนาดไฟล์เป็นรูปแบบที่อ่านง่าย
 * 
 * @param int $bytes - ขนาดไฟล์เป็น bytes
 * @return string
 */
function human_readable_filesize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * สร้าง Breadcrumb Navigation
 * 
 * @param array $links - array ของ links [['title' => 'หน้าแรก', 'url' => 'index.php'], ...]
 * @return string
 */
function create_breadcrumb($links) {
    if (empty($links)) return '';
    
    $breadcrumb = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    $total = count($links);
    foreach ($links as $index => $link) {
        $is_active = ($index == $total - 1);
        
        if ($is_active) {
            $breadcrumb .= "<li class='breadcrumb-item active' aria-current='page'>{$link['title']}</li>";
        } else {
            $breadcrumb .= "<li class='breadcrumb-item'><a href='{$link['url']}'>{$link['title']}</a></li>";
        }
    }
    
    $breadcrumb .= '</ol></nav>';
    
    return $breadcrumb;
}

/**
 * Log ข้อผิดพลาดลงไฟล์
 * 
 * @param string $message - ข้อความ error
 * @param string $file - ไฟล์ที่เกิด error
 * @param int $line - บรรทัดที่เกิด error
 */
function log_error($message, $file = '', $line = 0) {
    $error_log = "Error: {$message}";
    if ($file) {
        $error_log .= " in {$file}";
    }
    if ($line) {
        $error_log .= " on line {$line}";
    }
    
    error_log($error_log);
}

/**
 * ตรวจสอบ HTTP Method
 * 
 * @param string $method - HTTP method ที่ต้องการตรวจสอบ
 * @return boolean
 */
function is_request_method($method) {
    return $_SERVER['REQUEST_METHOD'] === strtoupper($method);
}

/**
 * ดึงค่าจาก POST หรือ GET
 * 
 * @param string $key - key ที่ต้องการ
 * @param mixed $default - ค่า default
 * @param string $method - POST หรือ GET
 * @return mixed
 */
function get_request_value($key, $default = '', $method = 'POST') {
    $source = strtoupper($method) === 'POST' ? $_POST : $_GET;
    return isset($source[$key]) ? sanitize_input($source[$key]) : $default;
}

/**
 * ตรวจสอบ AJAX request
 * 
 * @return boolean
 */
function is_ajax_request() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * ส่งข้อมูล JSON response
 * 
 * @param array $data - ข้อมูลที่จะส่ง
 * @param int $http_code - HTTP status code
 */
function json_response($data, $http_code = 200) {
    http_response_code($http_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * เข้ารหัส string เพื่อความปลอดภัย
 * 
 * @param string $data - ข้อมูลที่จะเข้ารหัส
 * @return string
 */
function encrypt_string($data) {
    $key = hash('sha256', 'INVENTION_VOTE_SECRET_KEY');
    $iv = substr(hash('sha256', 'INVENTION_VOTE_IV'), 0, 16);
    return base64_encode(openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv));
}

/**
 * ถอดรหัส string
 * 
 * @param string $encrypted_data - ข้อมูลที่เข้ารหัสแล้ว
 * @return string
 */
function decrypt_string($encrypted_data) {
    $key = hash('sha256', 'INVENTION_VOTE_SECRET_KEY');
    $iv = substr(hash('sha256', 'INVENTION_VOTE_IV'), 0, 16);
    return openssl_decrypt(base64_decode($encrypted_data), 'aes-256-cbc', $key, 0, $iv);
}
?>