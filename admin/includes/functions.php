<?php
// admin/includes/functions.php

/**
 * Helper functions for the invention-vote system
 */

// Sanitize input data
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Generate pagination
function generatePagination($current_page, $total_pages, $base_url, $params = []) {
    if ($total_pages <= 1) return '';
    
    $pagination = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous button
    $prev_disabled = $current_page <= 1 ? 'disabled' : '';
    $prev_page = max(1, $current_page - 1);
    $prev_url = $base_url . '?' . http_build_query(array_merge($params, ['page' => $prev_page]));
    
    $pagination .= '<li class="page-item ' . $prev_disabled . '">';
    $pagination .= '<a class="page-link" href="' . $prev_url . '">ก่อนหน้า</a>';
    $pagination .= '</li>';
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $current_page ? 'active' : '';
        $page_url = $base_url . '?' . http_build_query(array_merge($params, ['page' => $i]));
        
        $pagination .= '<li class="page-item ' . $active . '">';
        $pagination .= '<a class="page-link" href="' . $page_url . '">' . $i . '</a>';
        $pagination .= '</li>';
    }
    
    // Next button
    $next_disabled = $current_page >= $total_pages ? 'disabled' : '';
    $next_page = min($total_pages, $current_page + 1);
    $next_url = $base_url . '?' . http_build_query(array_merge($params, ['page' => $next_page]));
    
    $pagination .= '<li class="page-item ' . $next_disabled . '">';
    $pagination .= '<a class="page-link" href="' . $next_url . '">ถัดไป</a>';
    $pagination .= '</li>';
    
    $pagination .= '</ul></nav>';
    
    return $pagination;
}

// Get competition levels
function getCompetitionLevels() {
    return [
        'provincial' => 'ระดับจังหวัด',
        'regional' => 'ระดับภาค',
        'national' => 'ระดับชาติ'
    ];
}

// Get competition status options
function getCompetitionStatus() {
    return [
        'draft' => 'ร่าง',
        'active' => 'เปิดรับสมัคร',
        'judging' => 'ระหว่างการตัดสิน',
        'completed' => 'เสร็จสิ้น',
        'cancelled' => 'ยกเลิก'
    ];
}

// Get user roles
function getUserRoles() {
    return [
        'super_admin' => 'ผู้ดูแลระบบส่วนกลาง',
        'admin' => 'ผู้ดูแลระบบ',
        'chairman' => 'ประธานกรรมการ',
        'committee' => 'กรรมการ'
    ];
}

// Get scoring criteria types
function getScoringCriteriaTypes() {
    return [
        'rating_scale' => 'มาตราส่วนคะแนน',
        'checklist' => 'รายการตรวจสอบ',
        'ranking' => 'การจัดอันดับ'
    ];
}

// Get scoring levels
function getScoringLevels() {
    return [
        'excellent' => 'ดีมาก',
        'good' => 'ดี',
        'fair' => 'พอใช้',
        'poor' => 'ปรับปรุง'
    ];
}

// Check if user can access competition
function canAccessCompetition($user_id, $competition_id, $pdo) {
    global $pdo;
    
    $sql = "SELECT COUNT(*) FROM competition_users 
            WHERE user_id = ? AND competition_id = ? AND status = 'active'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $competition_id]);
    
    return $stmt->fetchColumn() > 0;
}

// Check if committee can score invention (not same school)
function canScoreInvention($committee_id, $invention_id, $pdo) {
    global $pdo;
    
    $sql = "SELECT i.school_name, u.school_name as committee_school
            FROM inventions i, users u
            WHERE i.id = ? AND u.id = ? AND i.school_name != u.school_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$invention_id, $committee_id]);
    
    return $stmt->fetchColumn() > 0;
}

// Get invention categories for competition
function getInventionCategories($competition_id, $pdo) {
    global $pdo;
    
    $sql = "SELECT c.* FROM categories c
            JOIN competition_categories cc ON c.id = cc.category_id
            WHERE cc.competition_id = ? AND c.status = 'active'
            ORDER BY c.name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$competition_id]);
    
    return $stmt->fetchAll();
}

// Calculate average score
function calculateAverageScore($invention_id, $category_id, $pdo) {
    global $pdo;
    
    $sql = "SELECT AVG(total_score) as avg_score
            FROM scores 
            WHERE invention_id = ? AND category_id = ? AND status = 'submitted'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$invention_id, $category_id]);
    
    $result = $stmt->fetch();
    return $result ? round($result['avg_score'], 2) : 0;
}

// Get scoring progress
function getScoringProgress($competition_id, $category_id, $pdo) {
    global $pdo;
    
    // Total inventions in category
    $sql = "SELECT COUNT(*) FROM inventions 
            WHERE competition_id = ? AND category_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$competition_id, $category_id]);
    $total_inventions = $stmt->fetchColumn();
    
    // Total committees for category
    $sql = "SELECT COUNT(*) FROM competition_users cu
            JOIN users u ON cu.user_id = u.id
            WHERE cu.competition_id = ? AND u.role = 'committee' 
            AND JSON_CONTAINS(cu.categories, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$competition_id, '"' . $category_id . '"']);
    $total_committees = $stmt->fetchColumn();
    
    // Expected total scores
    $expected_scores = $total_inventions * $total_committees;
    
    // Actual submitted scores
    $sql = "SELECT COUNT(*) FROM scores s
            JOIN inventions i ON s.invention_id = i.id
            WHERE i.competition_id = ? AND s.category_id = ? AND s.status = 'submitted'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$competition_id, $category_id]);
    $submitted_scores = $stmt->fetchColumn();
    
    return [
        'total_inventions' => $total_inventions,
        'total_committees' => $total_committees,
        'expected_scores' => $expected_scores,
        'submitted_scores' => $submitted_scores,
        'progress_percentage' => $expected_scores > 0 ? round(($submitted_scores / $expected_scores) * 100, 1) : 0
    ];
}

// Generate breadcrumb
function generateBreadcrumb($items) {
    $breadcrumb = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    $total = count($items);
    foreach ($items as $index => $item) {
        if ($index === $total - 1) {
            // Last item (current page)
            $breadcrumb .= '<li class="breadcrumb-item active" aria-current="page">' . $item['title'] . '</li>';
        } else {
            // Link items
            if (isset($item['url'])) {
                $breadcrumb .= '<li class="breadcrumb-item"><a href="' . $item['url'] . '">' . $item['title'] . '</a></li>';
            } else {
                $breadcrumb .= '<li class="breadcrumb-item">' . $item['title'] . '</li>';
            }
        }
    }
    
    $breadcrumb .= '</ol></nav>';
    return $breadcrumb;
}

// Log user activity
function logActivity($user_id, $action, $details, $pdo) {
    global $pdo;
    
    $sql = "INSERT INTO activity_logs (user_id, action, details, ip_address, created_at)
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $action, $details, $_SERVER['REMOTE_ADDR']]);
}

// Upload file helper
function uploadFile($file, $upload_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf']) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'ข้อผิดพลาดในการอัพโหลดไฟล์'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'ข้อผิดพลาดในการอัพโหลดไฟล์'];
    }
    
    if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
        return ['success' => false, 'message' => 'ไฟล์ใหญ่เกินไป (สูงสุด 10MB)'];
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'ประเภทไฟล์ไม่ถูกต้อง'];
    }
    
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . '/' . $new_filename;
    
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => false, 'message' => 'ไม่สามารถบันทึกไฟล์ได้'];
    }
    
    return ['success' => true, 'filename' => $new_filename, 'path' => $upload_path];
}

// Generate random password
function generateRandomPassword($length = 8) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $password;
}

// Send email (basic implementation)
function sendEmail($to, $subject, $message) {
    // This is a basic implementation
    // In production, use a proper email library like PHPMailer or SwiftMailer
    $headers = "From: " . SITE_NAME . " <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
    $headers .= "Reply-To: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Export to CSV
function exportToCSV($data, $filename, $headers = []) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Add BOM for UTF-8
    echo "\xEF\xBB\xBF";
    
    $output = fopen('php://output', 'w');
    
    if (!empty($headers)) {
        fputcsv($output, $headers);
    }
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}
?>