<?php
// index.php
require_once 'config/config.php';
require_once 'includes/auth_check.php';

// ต้องเข้าสู่ระบบก่อน
require_login();


// ดึงข้อมูลผู้ใช้ปัจจุบัน
$current_user = get_current_user();
$user_type = $current_user['user_type'];

// ดึงข้อมูลสถิติต่างๆ ตาม user type
$db = Database::getInstance();

// ข้อมูลสถิติพื้นฐาน
$stats = [
    'total_competitions' => 0,
    'active_competitions' => 0,
    'total_inventions' => 0,
    'total_judges' => 0,
    'pending_approvals' => 0,
    'my_assignments' => 0
];

try {
    // สถิติรายการแข่งขัน
    $competitions = $db->selectOne("SELECT COUNT(*) as total, 
                                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active 
                                    FROM competitions");
    $stats['total_competitions'] = $competitions['total'] ?? 0;
    $stats['active_competitions'] = $competitions['active'] ?? 0;
    
    // สถิติสิ่งประดิษฐ์
    $inventions = $db->selectOne("SELECT COUNT(*) as total FROM inventions");
    $stats['total_inventions'] = $inventions['total'] ?? 0;
    
    // สถิติกรรมการ
    $judges = $db->selectOne("SELECT COUNT(*) as total FROM users WHERE user_type = 'JUDGE' AND is_active = 1");
    $stats['total_judges'] = $judges['total'] ?? 0;
    
    // ข้อมูลเฉพาะตาม user type
    if ($user_type === 'JUDGE') {
        // งานที่ได้รับมอบหมาย
        $assignments = $db->selectOne(
            "SELECT COUNT(*) as total FROM judge_assignments 
             WHERE judge_id = ? AND is_active = 1",
            [$current_user['id']]
        );
        $stats['my_assignments'] = $assignments['total'] ?? 0;
    } elseif ($user_type === 'CHAIRMAN') {
        // ผลรอการรับรอง
        $pending = $db->selectOne(
            "SELECT COUNT(*) as total FROM competitions c
             JOIN competition_admins ca ON c.id = ca.competition_id
             WHERE ca.user_id = ? AND ca.role = 'CHAIRMAN' 
             AND c.status = 'VOTING' AND c.is_active = 1",
            [$current_user['id']]
        );
        $stats['pending_approvals'] = $pending['total'] ?? 0;
    }
    
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
}

// ข้อมูลกิจกรรมล่าสุด
$recent_activities = [];
try {
    if (has_permission(['ADMIN', 'SUPER_ADMIN'])) {
        $recent_activities = $db->select(
            "SELECT al.*, u.first_name, u.last_name 
             FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC 
             LIMIT 10"
        );
    }
} catch (Exception $e) {
    error_log("Recent activities error: " . $e->getMessage());
}

$page_title = 'หน้าหลัก - ' . SITE_NAME;
$show_navbar = true;
$show_footer = true;
?>

<?php include 'includes/header.php'; ?>

<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white py-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="mb-2">
                            <i class="bi bi-sun me-2"></i>
                            สวัสดี, คุณ<?php echo htmlspecialchars($current_user['full_name']); ?>
                        </h3>
                        <p class="mb-0 opacity-75">
                            <?php echo USER_TYPES[$user_type]; ?> | 
                            เข้าสู่ระบบล่าสุด: <?php echo date('d/m/Y H:i', $_SESSION['login_time']); ?> น.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="display-4 opacity-50">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-muted small text-uppercase">รายการแข่งขัน</div>
                        <div class="h4 mb-0"><?php echo number_format($stats['total_competitions']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-trophy text-primary fs-2"></i>
                    </div>
                </div>
                <hr class="my-2">
                <small class="text-success">
                    <i class="bi bi-check-circle me-1"></i>
                    ใช้งานอยู่ <?php echo number_format($stats['active_competitions']); ?> รายการ
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-muted small text-uppercase">สิ่งประดิษฐ์</div>
                        <div class="h4 mb-0"><?php echo number_format($stats['total_inventions']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-lightbulb text-warning fs-2"></i>
                    </div>
                </div>
                <hr class="my-2">
                <small class="text-info">
                    <i class="bi bi-info-circle me-1"></i>
                    ทั้งหมดในระบบ
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-muted small text-uppercase">กรรมการ</div>
                        <div class="h4 mb-0"><?php echo number_format($stats['total_judges']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people text-info fs-2"></i>
                    </div>
                </div>
                <hr class="my-2">
                <small class="text-muted">
                    <i class="bi bi-person-check me-1"></i>
                    ที่ใช้งานอยู่
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-muted small text-uppercase">
                            <?php if ($user_type === 'JUDGE'): ?>
                                งานที่ได้รับมอบหมาย
                            <?php elseif ($user_type === 'CHAIRMAN'): ?>
                                รอการรับรอง
                            <?php else: ?>
                                รายการที่เปิดลงคะแนน
                            <?php endif; ?>
                        </div>
                        <div class="h4 mb-0">
                            <?php 
                            if ($user_type === 'JUDGE') {
                                echo number_format($stats['my_assignments']);
                            } elseif ($user_type === 'CHAIRMAN') {
                                echo number_format($stats['pending_approvals']);
                            } else {
                                echo number_format($stats['active_competitions']);
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <?php if ($user_type === 'JUDGE'): ?>
                            <i class="bi bi-clipboard-check text-success fs-2"></i>
                        <?php elseif ($user_type === 'CHAIRMAN'): ?>
                            <i class="bi bi-hourglass-split text-warning fs-2"></i>
                        <?php else: ?>
                            <i class="bi bi-play-circle text-success fs-2"></i>
                        <?php endif; ?>
                    </div>
                </div>
                <hr class="my-2">
                <small class="text-muted">
                    <i class="bi bi-calendar-event me-1"></i>
                    อัปเดตล่าสุด: วันนี้
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>เมนูด่วน
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (has_permission(['SUPER_ADMIN'])): ?>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="admin/competitions.php" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-plus-circle d-block fs-2 mb-2"></i>
                            <div>สร้างรายการแข่งขัน</div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="admin/users.php" class="btn btn-outline-secondary w-100 py-3">
                            <i class="bi bi-person-plus d-block fs-2 mb-2"></i>
                            <div>จัดการผู้ใช้งาน</div>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (has_permission(['ADMIN', 'SUPER_ADMIN'])): ?>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="admin/inventions.php" class="btn btn-outline-warning w-100 py-3">
                            <i class="bi bi-lightbulb d-block fs-2 mb-2"></i>
                            <div>จัดการสิ่งประดิษฐ์</div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="admin/reports.php" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-graph-up d-block fs-2 mb-2"></i>
                            <div>รายงานผล</div>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (has_permission(['JUDGE'])): ?>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="judge/voting.php" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-check2-square d-block fs-2 mb-2"></i>
                            <div>ลงคะแนน</div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="judge/my-votes.php" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-clipboard-data d-block fs-2 mb-2"></i>
                            <div>คะแนนของฉัน</div>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (has_permission(['CHAIRMAN'])): ?>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="chairman/approval.php" class="btn btn-outline-warning w-100 py-3">
                            <i class="bi bi-check-circle d-block fs-2 mb-2"></i>
                            <div>รับรองผล</div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="chairman/reports.php" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-file-earmark-text d-block fs-2 mb-2"></i>
                            <div>รายงาน</div>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities (for Admin/Super Admin only) -->
<?php if (has_permission(['ADMIN', 'SUPER_ADMIN']) && !empty($recent_activities)): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-activity me-2"></i>กิจกรรมล่าสุด
                </h5>
                <a href="admin/audit-logs.php" class="btn btn-sm btn-outline-primary">
                    ดูทั้งหมด
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($recent_activities, 0, 5) as $activity): ?>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar avatar-sm bg-light rounded-circle p-2">
                                    <?php
                                    $icon = match($activity['action']) {
                                        'LOGIN' => 'bi-box-arrow-in-right text-success',
                                        'LOGOUT' => 'bi-box-arrow-left text-secondary',
                                        'CREATE_USER' => 'bi-person-plus text-primary',
                                        'UPDATE_USER' => 'bi-person-gear text-info',
                                        'VOTE' => 'bi-check-square text-success',
                                        'APPROVE' => 'bi-check-circle text-warning',
                                        default => 'bi-activity text-muted'
                                    };
                                    ?>
                                    <i class="bi <?php echo $icon; ?>"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="fw-semibold">
                                    <?php 
                                    $user_name = $activity['first_name'] && $activity['last_name'] 
                                        ? $activity['first_name'] . ' ' . $activity['last_name']
                                        : 'ผู้ใช้ที่ไม่ระบุ';
                                    echo htmlspecialchars($user_name); 
                                    ?>
                                </div>
                                <div class="text-muted small">
                                    <?php
                                    $action_text = match($activity['action']) {
                                        'LOGIN' => 'เข้าสู่ระบบ',
                                        'LOGOUT' => 'ออกจากระบบ',
                                        'CREATE_USER' => 'สร้างผู้ใช้งานใหม่',
                                        'UPDATE_USER' => 'แก้ไขข้อมูลผู้ใช้งาน',
                                        'VOTE' => 'ลงคะแนน',
                                        'APPROVE' => 'รับรองผล',
                                        default => $activity['action']
                                    };
                                    echo $action_text;
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted">
                                    <?php echo date('H:i', strtotime($activity['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>