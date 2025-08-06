<?php
// admin/includes/header.php
if (!defined('DB_HOST')) {
    require_once 'config.php';
}
checkAdminLogin();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    
    <!-- Google Font - Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        * {
            font-family: 'Kanit', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            font-size: 14px;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.4rem;
        }
        
        .sidebar {
            background: #ffffff;
            min-height: calc(100vh - 76px);
            box-shadow: 2px 0 4px rgba(0,0,0,0.1);
            border-right: 1px solid #e9ecef;
        }
        
        .sidebar .nav-link {
            color: #495057;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background-color: #e3f2fd;
            color: #1976d2;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            padding: 25px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
            font-weight: 500;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
        }
        
        .btn-outline-primary {
            border-color: #667eea;
            color: #667eea;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 20px;
        }
        
        .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 76px;
                left: -100%;
                width: 280px;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .stats-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="navbar-toggler d-lg-none" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <a class="navbar-brand" href="<?php echo ADMIN_URL; ?>/dashboard.php">
                <i class="bi bi-lightbulb-fill me-2"></i>
                <?php echo SITE_NAME; ?>
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i>
                        <?php echo $_SESSION['full_name'] ?? 'ผู้ดูแลระบบ'; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>ข้อมูลส่วนตัว</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>ตั้งค่า</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo getCurrentPage() == 'dashboard' ? 'active' : ''; ?>" 
                               href="<?php echo ADMIN_URL; ?>/dashboard.php">
                                <i class="bi bi-speedometer2"></i>
                                หน้าหลัก
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                การจัดการแข่งขัน
                            </h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo getCurrentPage() == 'competitions' ? 'active' : ''; ?>" 
                               href="<?php echo ADMIN_URL; ?>/competitions.php">
                                <i class="bi bi-trophy"></i>
                                รายการแข่งขัน
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo getCurrentPage() == 'categories' ? 'active' : ''; ?>" 
                               href="<?php echo ADMIN_URL; ?>/categories.php">
                                <i class="bi bi-tags"></i>
                                ประเภทสิ่งประดิษฐ์
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo getCurrentPage() == 'inventions' ? 'active' : ''; ?>" 
                               href="<?php echo ADMIN_URL; ?>/inventions.php">
                                <i class="bi bi-lightbulb"></i>
                                สิ่งประดิษฐ์
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                การจัดการผู้ใช้
                            </h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo getCurrentPage() == 'users' ? 'active' : ''; ?>" 
                               href="<?php echo ADMIN_URL; ?>/users.php">
                                <i class="bi bi-people"></i>
                                ผู้ใช้งานระบบ
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                การให้คะแนน
                            </h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo getCurrentPage() == 'scoring-criteria' ? 'active' : ''; ?>" 
                               href="<?php echo ADMIN_URL; ?>/scoring-criteria.php">
                                <i class="bi bi-list-check"></i>
                                เกณฑ์การให้คะแนน
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo getCurrentPage() == 'scoring-control' ? 'active' : ''; ?>" 
                               href="<?php echo ADMIN_URL; ?>/scoring-control.php">
                                <i class="bi bi-toggles"></i>
                                ควบคุมการให้คะแนน
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                รายงาน
                            </h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo getCurrentPage() == 'reports' ? 'active' : ''; ?>" 
                               href="<?php echo ADMIN_URL; ?>/reports.php">
                                <i class="bi bi-graph-up"></i>
                                รายงานผลคะแนน
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo getCurrentPage() == 'statistics' ? 'active' : ''; ?>" 
                               href="<?php echo ADMIN_URL; ?>/statistics.php">
                                <i class="bi bi-bar-chart"></i>
                                สถิติการใช้งาน
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <?php
                // Display messages
                $message = getMessage();
                if ($message):
                ?>
                <div class="alert alert-<?php echo $message['type'] == 'error' ? 'danger' : $message['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>