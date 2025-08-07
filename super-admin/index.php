<?php
// super-admin/index.php - Dashboard Super Admin
require_once '../config/database.php';
require_once '../config/settings.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// ตรวจสอบสิทธิ์ Super Admin
$allowedRoles = [USER_TYPE_SUPER_ADMIN];
$auth->requireLogin($allowedRoles);

// ตั้งค่าหน้า
$pageTitle = 'Dashboard ผู้ดูแลระบบส่วนกลาง';
$pageSubtitle = 'ภาพรวมและสถิติการใช้งานระบบ';
$currentPage = 'dashboard';
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '']
];

// เพิ่ม CSS และ JS เพิ่มเติมสำหรับหน้านี้
$additionalCSS = [];
$additionalJS = ['assets/js/charts.js']; // ถ้ามี

$userStats = $competitionStats = $inventionStats = $categoryStats = [];
$recentCompetitions = $recentUsers = $loginStats = [];
$systemStats = [];
$error = '';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // ตรวจสอบการเชื่อมต่อฐานข้อมูล
    if (!$db) {
        throw new Exception('ไม่สามารถเชื่อมต่อฐานข้อมูลได้');
    }
    
    // สถิติผู้ใช้งาน
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_users,
            SUM(CASE WHEN user_type = 'SUPER_ADMIN' THEN 1 ELSE 0 END) as super_admin_count,
            SUM(CASE WHEN user_type = 'ADMIN' THEN 1 ELSE 0 END) as admin_count,
            SUM(CASE WHEN user_type = 'CHAIRMAN' THEN 1 ELSE 0 END) as chairman_count,
            SUM(CASE WHEN user_type = 'JUDGE' THEN 1 ELSE 0 END) as judge_count,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users,
            SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as active_last_month
        FROM users
    ");
    $stmt->execute();
    $userStats = $stmt->fetch() ?: [
        'total_users' => 0, 'super_admin_count' => 0, 'admin_count' => 0,
        'chairman_count' => 0, 'judge_count' => 0, 'active_users' => 0, 'active_last_month' => 0
    ];
    
    // สถิติการแข่งขัน
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_competitions,
            SUM(CASE WHEN status = 'PREPARING' THEN 1 ELSE 0 END) as preparing_count,
            SUM(CASE WHEN status = 'REGISTRATION' THEN 1 ELSE 0 END) as registration_count,
            SUM(CASE WHEN status = 'VOTING' THEN 1 ELSE 0 END) as voting_count,
            SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_count,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_competitions
        FROM competitions
    ");
    $stmt->execute();
    $competitionStats = $stmt->fetch() ?: [
        'total_competitions' => 0, 'preparing_count' => 0, 'registration_count' => 0,
        'voting_count' => 0, 'completed_count' => 0, 'active_competitions' => 0
    ];
    
    // สถิติสิ่งประดิษฐ์
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_inventions,
            SUM(CASE WHEN status = 'DRAFT' THEN 1 ELSE 0 END) as draft_count,
            SUM(CASE WHEN status = 'SUBMITTED' THEN 1 ELSE 0 END) as submitted_count,
            SUM(CASE WHEN status = 'APPROVED' THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN status = 'REJECTED' THEN 1 ELSE 0 END) as rejected_count
        FROM inventions
    ");
    $stmt->execute();
    $inventionStats = $stmt->fetch() ?: [
        'total_inventions' => 0, 'draft_count' => 0, 'submitted_count' => 0,
        'approved_count' => 0, 'rejected_count' => 0
    ];
    
    // สถิติประเภทสิ่งประดิษฐ์
    $stmt = $db->prepare("
        SELECT COUNT(*) as total_categories
        FROM invention_categories 
        WHERE is_active = 1
    ");
    $stmt->execute();
    $categoryStats = $stmt->fetch() ?: ['total_categories' => 0];
    
    // การแข่งขันล่าสุด
    $stmt = $db->prepare("
        SELECT c.*, cl.level_name, 
               COALESCE((SELECT COUNT(*) FROM inventions WHERE competition_id = c.id), 0) as invention_count
        FROM competitions c
        LEFT JOIN competition_levels cl ON c.level_id = cl.id
        ORDER BY c.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $recentCompetitions = $stmt->fetchAll() ?: [];
    
    // ผู้ใช้งานล่าสุด
    $stmt = $db->prepare("
        SELECT username, first_name, last_name, user_type, created_at, last_login, is_active
        FROM users 
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $recentUsers = $stmt->fetchAll() ?: [];
    
    // สถิติการเข้าใช้งานรายวัน (7 วันล่าสุด)
    $stmt = $db->prepare("
        SELECT 
            DATE(created_at) as login_date,
            COUNT(*) as login_count
        FROM audit_logs 
        WHERE action = 'LOGIN' 
        AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY login_date DESC
        LIMIT 7
    ");
    $stmt->execute();
    $loginStats = $stmt->fetchAll() ?: [];
    
    // สถิติระบบ
    $phpVersion = PHP_VERSION;
    
    $stmt = $db->query('SELECT VERSION() as version');
    $mysqlVersion = $stmt->fetch()['version'] ?? 'Unknown';
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE()");
    $totalTables = $stmt->fetch()['count'] ?? 0;
    
    $stmt = $db->query("
        SELECT (
            COALESCE((SELECT COUNT(*) FROM users), 0) +
            COALESCE((SELECT COUNT(*) FROM competitions), 0) +
            COALESCE((SELECT COUNT(*) FROM inventions), 0) +
            COALESCE((SELECT COUNT(*) FROM voting_scores), 0) +
            COALESCE((SELECT COUNT(*) FROM audit_logs), 0)
        ) as total
    ");
    $totalRecords = $stmt->fetch()['total'] ?? 0;
    
    $systemStats = [
        'php_version' => $phpVersion,
        'mysql_version' => $mysqlVersion,
        'total_tables' => $totalTables,
        'total_records' => $totalRecords
    ];
    
} catch(Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $error = "เกิดข้อผิดพลาดในการโหลดข้อมูล: " . $e->getMessage();
    
    // กำหนดค่า default เพื่อป้องกัน error
    $userStats = ['total_users' => 0, 'super_admin_count' => 0, 'admin_count' => 0, 'chairman_count' => 0, 'judge_count' => 0, 'active_users' => 0, 'active_last_month' => 0];
    $competitionStats = ['total_competitions' => 0, 'preparing_count' => 0, 'registration_count' => 0, 'voting_count' => 0, 'completed_count' => 0, 'active_competitions' => 0];
    $inventionStats = ['total_inventions' => 0, 'draft_count' => 0, 'submitted_count' => 0, 'approved_count' => 0, 'rejected_count' => 0];
    $categoryStats = ['total_categories' => 0];
    $systemStats = ['php_version' => PHP_VERSION, 'mysql_version' => 'Unknown', 'total_tables' => 0, 'total_records' => 0];
}

// ฟังก์ชันช่วยสำหรับแสดงผล
function getStatusBadgeClass($status) {
    return match($status) {
        'PREPARING' => 'secondary',
        'REGISTRATION' => 'info',
        'VOTING' => 'warning',
        'COMPLETED' => 'success',
        default => 'secondary'
    };
}

function calculatePercentage($value, $total) {
    return $total > 0 ? round(($value / $total) * 100, 1) : 0;
}

// Include header
include '../includes/header.php';
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <strong>เกิดข้อผิดพลาด!</strong> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Dashboard Content -->
<div class="dashboard-container">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Users -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                    👥
                </div>
                <div class="stats-content">
                    <div class="stats-number"><?php echo number_format($userStats['total_users']); ?></div>
                    <div class="stats-label">ผู้ใช้งานทั้งหมด</div>
                    <div class="stats-detail">
                        <small class="text-success">
                            <?php echo number_format($userStats['active_users']); ?> คนใช้งานอยู่
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Competitions -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    🏆
                </div>
                <div class="stats-content">
                    <div class="stats-number"><?php echo number_format($competitionStats['total_competitions']); ?></div>
                    <div class="stats-label">การแข่งขันทั้งหมด</div>
                    <div class="stats-detail">
                        <small class="text-primary">
                            <?php echo number_format($competitionStats['voting_count']); ?> กำลังลงคะแนน
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Inventions -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    🔬
                </div>
                <div class="stats-content">
                    <div class="stats-number"><?php echo number_format($inventionStats['total_inventions']); ?></div>
                    <div class="stats-label">สิ่งประดิษฐ์ทั้งหมด</div>
                    <div class="stats-detail">
                        <small class="text-success">
                            <?php echo number_format($inventionStats['approved_count']); ?> ได้รับการอนุมัติ
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Active Categories -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-info">
                    📋
                </div>
                <div class="stats-content">
                    <div class="stats-number"><?php echo number_format($categoryStats['total_categories']); ?></div>
                    <div class="stats-label">ประเภทสิ่งประดิษฐ์</div>
                    <div class="stats-detail">
                        <small class="text-muted">
                            ประเภทที่ใช้งานอยู่
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Type Distribution -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">การกระจายประเภทผู้ใช้งาน</h5>
                </div>
                <div class="card-body">
                    <div class="user-type-stats">
                        <div class="user-type-item">
                            <div class="user-type-info">
                                <div class="user-type-label">ผู้ดูแลระบบส่วนกลาง</div>
                                <div class="user-type-count"><?php echo number_format($userStats['super_admin_count']); ?></div>
                            </div>
                            <div class="user-type-bar">
                                <div class="user-type-progress bg-danger" style="width: <?php echo calculatePercentage($userStats['super_admin_count'], $userStats['total_users']); ?>%"></div>
                            </div>
                        </div>
                        <div class="user-type-item">
                            <div class="user-type-info">
                                <div class="user-type-label">ผู้ดูแลระบบ</div>
                                <div class="user-type-count"><?php echo number_format($userStats['admin_count']); ?></div>
                            </div>
                            <div class="user-type-bar">
                                <div class="user-type-progress bg-warning" style="width: <?php echo calculatePercentage($userStats['admin_count'], $userStats['total_users']); ?>%"></div>
                            </div>
                        </div>
                        <div class="user-type-item">
                            <div class="user-type-info">
                                <div class="user-type-label">ประธานกรรมการ</div>
                                <div class="user-type-count"><?php echo number_format($userStats['chairman_count']); ?></div>
                            </div>
                            <div class="user-type-bar">
                                <div class="user-type-progress bg-info" style="width: <?php echo calculatePercentage($userStats['chairman_count'], $userStats['total_users']); ?>%"></div>
                            </div>
                        </div>
                        <div class="user-type-item">
                            <div class="user-type-info">
                                <div class="user-type-label">กรรมการ</div>
                                <div class="user-type-count"><?php echo number_format($userStats['judge_count']); ?></div>
                            </div>
                            <div class="user-type-bar">
                                <div class="user-type-progress bg-success" style="width: <?php echo calculatePercentage($userStats['judge_count'], $userStats['total_users']); ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Competition Status -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">สถานะการแข่งขัน</h5>
                </div>
                <div class="card-body">
                    <div class="competition-status-grid">
                        <div class="status-item">
                            <div class="status-icon bg-secondary">⏳</div>
                            <div class="status-content">
                                <div class="status-count"><?php echo number_format($competitionStats['preparing_count']); ?></div>
                                <div class="status-label">กำลังเตรียมการ</div>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-icon bg-info">📝</div>
                            <div class="status-content">
                                <div class="status-count"><?php echo number_format($competitionStats['registration_count']); ?></div>
                                <div class="status-label">เปิดรับสมัคร</div>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-icon bg-warning">🗳️</div>
                            <div class="status-content">
                                <div class="status-count"><?php echo number_format($competitionStats['voting_count']); ?></div>
                                <div class="status-label">กำลังลงคะแนน</div>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-icon bg-success">✅</div>
                            <div class="status-content">
                                <div class="status-count"><?php echo number_format($competitionStats['completed_count']); ?></div>
                                <div class="status-label">เสร็จสิ้น</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Competitions -->
        <div class="col-12 col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">การแข่งขันล่าสุด</h5>
                    <a href="competitions/" class="btn btn-outline-primary btn-sm">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentCompetitions)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ชื่อการแข่งขัน</th>
                                    <th class="d-none d-md-table-cell">ระดับ</th>
                                    <th>สถานะ</th>
                                    <th class="d-none d-lg-table-cell">จำนวนสิ่งประดิษฐ์</th>
                                    <th class="d-none d-md-table-cell">วันที่สร้าง</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentCompetitions as $competition): ?>
                                <tr>
                                    <td>
                                        <div class="competition-info">
                                            <div class="fw-bold"><?php echo htmlspecialchars($competition['competition_name']); ?></div>
                                            <small class="text-muted">ปี <?php echo $competition['competition_year']; ?></small>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell"><?php echo htmlspecialchars($competition['level_name'] ?? 'ไม่ระบุ'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo getStatusBadgeClass($competition['status']); ?>">
                                            <?php echo getCompetitionStatusText($competition['status']); ?>
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell"><?php echo number_format($competition['invention_count']); ?></td>
                                    <td class="d-none d-md-table-cell"><?php echo formatDate($competition['created_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">🏆</div>
                        <div class="empty-state-title">ยังไม่มีการแข่งขัน</div>
                        <div class="empty-state-text">เริ่มต้นสร้างการแข่งขันแรกของคุณ</div>
                        <a href="competitions/add.php" class="btn btn-primary">สร้างการแข่งขัน</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Users -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">ผู้ใช้งานใหม่</h5>
                    <a href="users/" class="btn btn-outline-primary btn-sm">ดูทั้งหมด</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentUsers)): ?>
                    <div class="user-list">
                        <?php foreach ($recentUsers as $user): ?>
                        <div class="user-item">
                            <div class="user-avatar-list">
                                <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                            </div>
                            <div class="user-info-list">
                                <div class="user-name-list">
                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                    <?php if (!$user['is_active']): ?>
                                    <span class="badge badge-danger badge-sm">ระงับ</span>
                                    <?php endif; ?>
                                </div>
                                <div class="user-meta-list">
                                    <small class="text-muted">
                                        <?php echo getUserTypeText($user['user_type']); ?>
                                        <span class="mx-1">•</span>
                                        <?php echo formatDate($user['created_at']); ?>
                                    </small>
                                </div>
                                <?php if ($user['last_login']): ?>
                                <div class="user-last-login">
                                    <small class="text-success">
                                        เข้าใช้ล่าสุด: <?php echo formatDateTime($user['last_login']); ?>
                                    </small>
                                </div>
                                <?php else: ?>
                                <div class="user-last-login">
                                    <small class="text-warning">ยังไม่เข้าใช้</small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">👥</div>
                        <div class="empty-state-title">ไม่มีผู้ใช้งานใหม่</div>
                        <div class="empty-state-text">ยังไม่มีผู้ใช้งานที่ลงทะเบียนใหม่</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">ข้อมูลระบบ</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="system-info-item">
                                <div class="system-info-icon">🐘</div>
                                <div class="system-info-label">PHP Version</div>
                                <div class="system-info-value"><?php echo $systemStats['php_version']; ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="system-info-item">
                                <div class="system-info-icon">🗄️</div>
                                <div class="system-info-label">MySQL Version</div>
                                <div class="system-info-value"><?php echo explode('-', $systemStats['mysql_version'])[0]; ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="system-info-item">
                                <div class="system-info-icon">📊</div>
                                <div class="system-info-label">จำนวนตาราง</div>
                                <div class="system-info-value"><?php echo number_format($systemStats['total_tables']); ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="system-info-item">
                                <div class="system-info-icon">📈</div>
                                <div class="system-info-label">จำนวนข้อมูล</div>
                                <div class="system-info-value"><?php echo number_format($systemStats['total_records']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Specific Styles */
.dashboard-container {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.stats-card {
    position: relative;
    background: var(--white);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    transition: var(--transition);
    overflow: hidden;
    height: 140px;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
}

.stats-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.stats-icon {
    position: absolute;
    top: 1.25rem;
    right: 1.25rem;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    opacity: 0.9;
}

.stats-icon.bg-primary { background: var(--primary-color); }
.stats-icon.bg-success { background: var(--success-color); }
.stats-icon.bg-warning { background: var(--warning-color); }
.stats-icon.bg-info { background: var(--info-color); }

.stats-content {
    position: relative;
    z-index: 1;
    padding-right: 60px;
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 0.25rem;
    line-height: 1;
}

.stats-label {
    color: var(--gray-600);
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.stats-detail {
    margin-top: 0.75rem;
}

/* User Type Stats */
.user-type-stats {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.user-type-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-type-info {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.user-type-label {
    font-weight: 500;
    color: var(--gray-700);
}

.user-type-count {
    font-weight: 700;
    color: var(--gray-800);
    font-size: 1.1rem;
}

.user-type-bar {
    width: 120px;
    height: 8px;
    background: var(--gray-200);
    border-radius: 4px;
    overflow: hidden;
}

.user-type-progress {
    height: 100%;
    border-radius: 4px;
    transition: width 0.8s ease-out;
    min-width: 2px;
}

/* Competition Status Grid */
.competition-status-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.status-item:hover {
    background: var(--gray-100);
    transform: translateY(-1px);
}

.status-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}

.status-icon.bg-secondary { background: var(--secondary-color); }

.status-content {
    flex: 1;
}

.status-count {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
    line-height: 1;
}

.status-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-top: 0.25rem;
}

/* Table Enhancements */
.competition-info {
    min-width: 200px;
}

.table th {
    font-weight: 600;
    color: var(--gray-800);
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-300);
    padding: 1rem 0.75rem;
    white-space: nowrap;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.table tbody tr:hover {
    background-color: var(--gray-50);
}

/* User List */
.user-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.user-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-200);
    transition: var(--transition);
}

.user-item:last-child {
    border-bottom: none;
}

.user-item:hover {
    background-color: var(--gray-50);
    margin: 0 -1rem;
    padding: 0.75rem 1rem;
    border-radius: var(--border-radius);
}

.user-avatar-list {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.user-info-list {
    flex: 1;
    min-width: 0;
}

.user-name-list {
    font-weight: 600;
    color: var(--gray-800);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    line-height: 1.3;
    margin-bottom: 0.25rem;
}

.user-meta-list,
.user-last-login {
    line-height: 1.2;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}

/* System Info */
.system-info-item {
    text-align: center;
    padding: 1rem 0;
    transition: var(--transition);
}

.system-info-item:hover {
    transform: translateY(-2px);
}

.system-info-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.system-info-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-bottom: 0.5rem;
}

.system-info-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-800);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--gray-500);
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    background-color: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--gray-400);
}

.empty-state-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}

.empty-state-text {
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

/* Badge improvements */
.badge {
    font-weight: 500;
    padding: 0.375em 0.75em;
    font-size: 0.8em;
}

/* Responsive Improvements */
@media (max-width: 768px) {
    .stats-card {
        height: auto;
        min-height: 120px;
        text-align: center;
        padding-bottom: 2rem;
    }
    
    .stats-icon {
        position: relative;
        top: auto;
        right: auto;
        margin: 0 auto 1rem;
    }
    
    .stats-content {
        padding-right: 0;
    }
    
    .competition-status-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .user-type-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .user-type-info {
        width: 100%;
    }
    
    .user-type-bar {
        width: 100%;
        max-width: 200px;
    }
    
    .system-info-item {
        padding: 0.75rem 0;
    }
    
    .system-info-icon {
        font-size: 1.5rem;
    }
    
    .system-info-value {
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    .stats-number {
        font-size: 1.75rem;
    }
    
    .status-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
    
    .status-count {
        font-size: 1.25rem;
    }
    
    .user-avatar-list {
        width: 32px;
        height: 32px;
        font-size: 0.9rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>