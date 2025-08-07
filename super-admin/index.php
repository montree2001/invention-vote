<?php
// super-admin/index.php
require_once '../config/config.php';
require_login(['SUPER_ADMIN']);

$page_title = 'Dashboard ผู้ดูแลระบบส่วนกลาง - ' . SITE_NAME;
$current_user = get_current_user();

// ดึงสถิติต่างๆ
$db = Database::getInstance();

try {
    // สถิติผู้ใช้
    $user_stats = $db->select("
        SELECT user_type, COUNT(*) as count 
        FROM users 
        WHERE is_active = 1 
        GROUP BY user_type
    ");
    
    $users_by_type = [];
    $total_users = 0;
    foreach ($user_stats as $stat) {
        $users_by_type[$stat['user_type']] = $stat['count'];
        $total_users += $stat['count'];
    }
    
    // สถิติการแข่งขัน
    $competition_stats = $db->select("
        SELECT 
            cl.level_name,
            COUNT(c.id) as count,
            SUM(CASE WHEN c.status = 'ACTIVE' OR c.is_active = 1 THEN 1 ELSE 0 END) as active_count
        FROM competition_levels cl
        LEFT JOIN competitions c ON cl.id = c.level_id
        GROUP BY cl.id, cl.level_name
        ORDER BY cl.level_order
    ");
    
    // สถิติสิ่งประดิษฐ์
    $invention_stats = $db->selectOne("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'APPROVED' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'SUBMITTED' THEN 1 ELSE 0 END) as submitted,
            SUM(CASE WHEN status = 'DRAFT' THEN 1 ELSE 0 END) as draft
        FROM inventions
    ");
    
    // กิจกรรมล่าสุด
    $recent_activities = $db->select("
        SELECT 
            al.*,
            u.first_name,
            u.last_name,
            u.user_type
        FROM audit_logs al
        LEFT JOIN users u ON al.user_id = u.id
        ORDER BY al.created_at DESC
        LIMIT 10
    ");
    
    // การแข่งขันที่กำลังดำเนินการ
    $active_competitions = $db->select("
        SELECT 
            c.*,
            cl.level_name,
            COUNT(i.id) as invention_count
        FROM competitions c
        JOIN competition_levels cl ON c.level_id = cl.id
        LEFT JOIN inventions i ON c.id = i.competition_id
        WHERE c.is_active = 1 AND c.status IN ('REGISTRATION', 'VOTING')
        GROUP BY c.id
        ORDER BY c.created_at DESC
        LIMIT 5
    ");

} catch (Exception $e) {
    $error_message = "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage();
}

$show_navbar = true;
$show_footer = true;

// Additional CSS สำหรับหน้านี้
$additional_css = '
<style>
/* Modern Dashboard Styles */
body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.container-fluid {
    background: transparent;
}

/* Enhanced Card Styles */
.card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    overflow: hidden;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem;
    border-radius: 20px 20px 0 0 !important;
}

/* Gradient Stats Cards */
.stats-card-1 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-card-2 {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stats-card-3 {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.stats-card-4 {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.stats-card {
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.1);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stats-card:hover::before {
    opacity: 1;
}

.stats-icon {
    font-size: 3.5rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.stats-number {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Enhanced Buttons */
.btn {
    border-radius: 12px;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    border: none;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
}

.btn-outline-primary {
    border: 2px solid #667eea;
    color: #667eea;
    background: transparent;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    color: white;
    transform: translateY(-2px);
}

/* Menu Cards */
.menu-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 2px solid transparent;
    text-decoration: none;
    color: inherit;
    display: block;
    position: relative;
    overflow: hidden;
}

.menu-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.menu-card:hover {
    transform: translateY(-8px);
    border-color: #667eea;
    color: white;
    text-decoration: none;
}

.menu-card:hover::before {
    opacity: 1;
}

.menu-card i {
    font-size: 3rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.menu-card:hover i {
    transform: scale(1.1);
}

/* Table Enhancements */
.table {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    background: white;
}

.table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    font-weight: 600;
    padding: 1rem;
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    transform: scale(1.01);
}

/* Badge Enhancements */
.badge {
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    font-size: 0.85rem;
}

.bg-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
}

.bg-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
}

.bg-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important;
}

.bg-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ffa726 100%) !important;
}

/* Progress Bar */
.progress {
    height: 8px;
    border-radius: 10px;
    background: rgba(0,0,0,0.1);
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

/* Activity Cards */
.activity-item {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border-left: 4px solid #667eea;
}

.activity-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

/* Responsive Design */
@media (max-width: 768px) {
    .stats-number {
        font-size: 2rem;
    }
    
    .stats-icon {
        font-size: 2.5rem;
    }
    
    .menu-card {
        margin-bottom: 1rem;
    }
}

/* Animation Classes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease forwards;
}

/* Loading Animation */
.loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
';
?>

<?php include '../includes/header.php'; ?>

<div class="container-fluid px-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-speedometer2 text-primary"></i>
                Dashboard ผู้ดูแลระบบส่วนกลาง
            </h1>
            <p class="text-muted mb-0">ภาพรวมของระบบ INVENTION-VOTE</p>
        </div>
        <div class="text-end">
            <small class="text-muted">
                <i class="bi bi-clock"></i>
                อัปเดตล่าสุด: <?php echo format_thai_date(date('Y-m-d H:i:s')); ?>
            </small>
        </div>
    </div>

    <?php if (isset($error_message)): ?>
        <?php echo display_alert($error_message, 'danger'); ?>
    <?php endif; ?>

    <!-- สถิติหลัก -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card stats-card-1 fade-in-up">
                <div class="stats-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($total_users); ?></div>
                <h5 class="mb-0">ผู้ใช้งานระบบ</h5>
                <small class="opacity-75">คนทั้งหมด</small>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card stats-card-2 fade-in-up" style="animation-delay: 0.1s;">
                <div class="stats-icon">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div class="stats-number"><?php echo count($active_competitions); ?></div>
                <h5 class="mb-0">การแข่งขัน</h5>
                <small class="opacity-75">กำลังดำเนินการ</small>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card stats-card-3 fade-in-up" style="animation-delay: 0.2s;">
                <div class="stats-icon">
                    <i class="bi bi-lightbulb-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($invention_stats['total'] ?? 0); ?></div>
                <h5 class="mb-0">สิ่งประดิษฐ์</h5>
                <small class="opacity-75">ชิ้นทั้งหมด</small>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card stats-card-4 fade-in-up" style="animation-delay: 0.3s;">
                <div class="stats-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($invention_stats['approved'] ?? 0); ?></div>
                <h5 class="mb-0">อนุมัติแล้ว</h5>
                <small class="opacity-75">สิ่งประดิษฐ์</small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- เมนูหลัก -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-grid-3x3-gap-fill me-2"></i>
                        เมนูจัดการหลัก
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <a href="competitions.php" class="menu-card">
                                <i class="bi bi-trophy text-primary"></i>
                                <h5 class="fw-semibold">จัดการการแข่งขัน</h5>
                                <p class="text-muted mb-0">สร้าง แก้ไข การแข่งขันทุกระดับ</p>
                            </a>
                        </div>
                        
                        <div class="col-md-6">
                            <a href="users.php" class="menu-card">
                                <i class="bi bi-people text-success"></i>
                                <h5 class="fw-semibold">จัดการผู้ใช้งาน</h5>
                                <p class="text-muted mb-0">เพิ่ม ลบ แก้ไข บัญชีผู้ใช้</p>
                            </a>
                        </div>
                        
                        <div class="col-md-6">
                            <a href="inventions.php" class="menu-card">
                                <i class="bi bi-lightbulb text-warning"></i>
                                <h5 class="fw-semibold">จัดการสิ่งประดิษฐ์</h5>
                                <p class="text-muted mb-0">ดูและจัดการสิ่งประดิษฐ์ทั้งหมด</p>
                            </a>
                        </div>
                        
                        <div class="col-md-6">
                            <a href="reports.php" class="menu-card">
                                <i class="bi bi-graph-up text-info"></i>
                                <h5 class="fw-semibold">รายงานและสถิติ</h5>
                                <p class="text-muted mb-0">ดูรายงานทุกรายการแข่งขัน</p>
                            </a>
                        </div>
                        
                        <div class="col-md-6">
                            <a href="categories.php" class="menu-card">
                                <i class="bi bi-tags text-secondary"></i>
                                <h5 class="fw-semibold">จัดการประเภทสิ่งประดิษฐ์</h5>
                                <p class="text-muted mb-0">เพิ่ม แก้ไข ประเภทและเกณฑ์</p>
                            </a>
                        </div>
                        
                        <div class="col-md-6">
                            <a href="settings.php" class="menu-card">
                                <i class="bi bi-gear text-dark"></i>
                                <h5 class="fw-semibold">ตั้งค่าระบบ</h5>
                                <p class="text-muted mb-0">การตั้งค่าและระดับการแข่งขัน</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- การแข่งขันที่กำลังดำเนินการ -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-activity me-2"></i>
                        การแข่งขันที่กำลังดำเนินการ
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($active_competitions)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox display-1 opacity-50"></i>
                            <h6 class="mt-3 text-muted">ไม่มีการแข่งขันที่กำลังดำเนินการ</h6>
                        </div>
                    <?php else: ?>
                        <div class="d-grid gap-3">
                            <?php foreach ($active_competitions as $comp): ?>
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2"><?php echo htmlspecialchars($comp['competition_name']); ?></h6>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-geo-alt text-primary me-1"></i>
                                                <small class="text-muted"><?php echo htmlspecialchars($comp['level_name']); ?></small>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-lightbulb text-warning me-1"></i>
                                                <small class="text-muted"><?php echo number_format($comp['invention_count']); ?> ชิ้น</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <?php echo get_status_badge($comp['status'], 'competition'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-4">
                            <a href="competitions.php" class="btn btn-outline-primary btn-sm">
                                ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- สถิติผู้ใช้งานแยกตามประเภท -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart text-primary"></i>
                        สถิติผู้ใช้งานแยกตามประเภท
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($users_by_type)): ?>
                        <?php foreach ($users_by_type as $type => $count): ?>
                            <?php $percentage = $total_users > 0 ? round(($count / $total_users) * 100, 1) : 0; ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small"><?php echo USER_TYPES[$type] ?? $type; ?></span>
                                    <span class="small text-muted"><?php echo number_format($count); ?> คน (<?php echo $percentage; ?>%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-inbox display-4 opacity-50"></i>
                            <p class="mt-2 mb-0">ไม่มีข้อมูลผู้ใช้งาน</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- สถิติการแข่งขันแยกตามระดับ -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart text-success"></i>
                        สถิติการแข่งขันแยกตามระดับ
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($competition_stats)): ?>
                        <?php foreach ($competition_stats as $stat): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small"><?php echo htmlspecialchars($stat['level_name']); ?></span>
                                    <span class="small text-muted">
                                        <?php echo number_format($stat['count']); ?> รายการ
                                        (<?php echo number_format($stat['active_count']); ?> ใช้งาน)
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo $stat['count'] > 0 ? 100 : 0; ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-inbox display-4 opacity-50"></i>
                            <p class="mt-2 mb-0">ไม่มีข้อมูลการแข่งขัน</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- กิจกรรมล่าสุด -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        กิจกรรมล่าสุดในระบบ
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_activities)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th class="border-0">เวลา</th>
                                        <th class="border-0">ผู้ใช้</th>
                                        <th class="border-0">การกระทำ</th>
                                        <th class="border-0">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recent_activities, 0, 10) as $activity): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle p-2 me-3">
                                                        <i class="bi bi-clock text-muted"></i>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo format_thai_date($activity['created_at']); ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-3">
                                                        <?php echo strtoupper(substr($activity['first_name'] ?? 'U', 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">
                                                            <?php echo htmlspecialchars(($activity['first_name'] ?? '') . ' ' . ($activity['last_name'] ?? '')); ?>
                                                        </div>
                                                        <?php if ($activity['user_type']): ?>
                                                            <?php echo get_user_type_badge($activity['user_type']); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?php echo htmlspecialchars($activity['action']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <code class="text-muted small">
                                                    <?php echo htmlspecialchars($activity['ip_address'] ?? '-'); ?>
                                                </code>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-4">
                            <a href="activity-logs.php" class="btn btn-outline-primary">
                                ดูกิจกรรมทั้งหมด <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox display-1 opacity-50"></i>
                            <h6 class="mt-3 text-muted">ไม่มีกิจกรรมล่าสุด</h6>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>