<?php
// index.php
require_once 'config/config.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!is_logged_in()) {
    redirect('login.php');
}

// ดึงข้อมูลผู้ใช้
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'ผู้ใช้';
$user_type = $_SESSION['user_type'] ?? 'JUDGE';

require_once 'classes/Database.php';
$db = Database::getInstance();

// สถิติพื้นฐาน
$stats = [
    'competitions' => 0,
    'inventions' => 0,
    'judges' => 0
];

try {
    // นับรายการแข่งขัน
    $result = $db->selectOne("SELECT COUNT(*) as count FROM competitions");
    $stats['competitions'] = $result['count'] ?? 0;
    
    // นับสิ่งประดิษฐ์
    $result = $db->selectOne("SELECT COUNT(*) as count FROM inventions");
    $stats['inventions'] = $result['count'] ?? 0;
    
    // นับกรรมการ
    $result = $db->selectOne("SELECT COUNT(*) as count FROM users WHERE user_type = 'JUDGE'");
    $stats['judges'] = $result['count'] ?? 0;
    
} catch (Exception $e) {
    // ไม่ต้องทำอะไร ใช้ค่าเริ่มต้น
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts - Kanit -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Kanit', sans-serif;
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,.1);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="bi bi-lightbulb text-primary"></i>
            <span class="ms-2">INVENTION-VOTE</span>
        </a>
        
        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                    <?php echo htmlspecialchars($user_name); ?>
                </a>
                <ul class="dropdown-menu">
                    <li><h6 class="dropdown-header"><?php echo $user_types[$user_type] ?? $user_type; ?></h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="profile.php">
                        <i class="bi bi-gear"></i> ข้อมูลส่วนตัว
                    </a></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-4">
    
    <!-- Welcome -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">
                                <i class="bi bi-sun"></i>
                                สวัสดี, <?php echo htmlspecialchars($user_name); ?>
                            </h4>
                            <p class="mb-0 opacity-75">
                                <?php echo $user_types[$user_type] ?? $user_type; ?> |
                                เข้าสู่ระบบเมื่อ: <?php echo date('d/m/Y H:i'); ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="bi bi-lightbulb display-4 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-trophy text-primary display-4 mb-2"></i>
                    <h3><?php echo number_format($stats['competitions']); ?></h3>
                    <p class="text-muted mb-0">รายการแข่งขัน</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-lightbulb text-warning display-4 mb-2"></i>
                    <h3><?php echo number_format($stats['inventions']); ?></h3>
                    <p class="text-muted mb-0">สิ่งประดิษฐ์</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-people text-info display-4 mb-2"></i>
                    <h3><?php echo number_format($stats['judges']); ?></h3>
                    <p class="text-muted mb-0">กรรมการ</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-grid"></i> เมนูหลัก
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        
                        <?php if ($user_type === 'SUPER_ADMIN' || $user_type === 'ADMIN'): ?>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="admin/competitions.php" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-plus-circle d-block fs-3 mb-2"></i>
                                จัดการแข่งขัน
                            </a>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="admin/inventions.php" class="btn btn-outline-warning w-100 py-3">
                                <i class="bi bi-lightbulb d-block fs-3 mb-2"></i>
                                จัดการสิ่งประดิษฐ์
                            </a>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="admin/users.php" class="btn btn-outline-secondary w-100 py-3">
                                <i class="bi bi-people d-block fs-3 mb-2"></i>
                                จัดการผู้ใช้
                            </a>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="admin/reports.php" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-graph-up d-block fs-3 mb-2"></i>
                                รายงานผล
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($user_type === 'JUDGE'): ?>
                        <div class="col-lg-6 col-md-6 mb-3">
                            <a href="judge/voting.php" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-check-square d-block fs-3 mb-2"></i>
                                ลงคะแนน
                            </a>
                        </div>
                        
                        <div class="col-lg-6 col-md-6 mb-3">
                            <a href="judge/scores.php" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-clipboard-data d-block fs-3 mb-2"></i>
                                คะแนนของฉัน
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($user_type === 'CHAIRMAN'): ?>
                        <div class="col-lg-6 col-md-6 mb-3">
                            <a href="chairman/approval.php" class="btn btn-outline-warning w-100 py-3">
                                <i class="bi bi-check-circle d-block fs-3 mb-2"></i>
                                รับรองผล
                            </a>
                        </div>
                        
                        <div class="col-lg-6 col-md-6 mb-3">
                            <a href="chairman/reports.php" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-file-text d-block fs-3 mb-2"></i>
                                รายงาน
                            </a>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>