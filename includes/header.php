<?php
// includes/header.php - Header ส่วนหัวของหน้าเว็บ

// ตรวจสอบว่ามีการ include config files แล้วหรือยัง
if (!defined('SYSTEM_NAME')) {
    require_once dirname(__DIR__) . '/config/settings.php';
}

if (!isset($auth)) {
    require_once dirname(__DIR__) . '/includes/auth.php';
}

// ตรวจสอบการเข้าสู่ระบบ
$auth->requireLogin($allowedRoles ?? []);

// ดึงข้อมูลผู้ใช้ปัจจุบัน
$currentUser = $auth->getUserData($_SESSION['user_id']);
$userTypeText = getUserTypeText($_SESSION['user_type']);

// กำหนดค่า default ถ้าไม่ได้ส่งมา
$pageTitle = $pageTitle ?? 'Dashboard';
$breadcrumbs = $breadcrumbs ?? [];
$currentPage = $currentPage ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SYSTEM_NAME; ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/responsive.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>assets/images/favicon.ico">
    
    <!-- Meta tags -->
    <meta name="description" content="ระบบประมวลผลสิ่งประดิษฐ์คนรุ่นใหม่">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="INVENTION-VOTE System">
    
    <!-- CSRF Token Meta -->
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL . $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom styles for this page -->
    <?php if (isset($customCSS)): ?>
        <style><?php echo $customCSS; ?></style>
    <?php endif; ?>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar">
        <div class="container-fluid">
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle d-lg-none" type="button">
                ☰
            </button>
            
            <!-- Brand -->
            <a href="<?php echo BASE_URL; ?>dashboard.php" class="navbar-brand">
                <span class="logo-icon">🔬</span>
                <span class="d-none d-md-inline">INVENTION-VOTE</span>
            </a>
            
            <!-- User Info & Actions -->
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                        <div class="user-avatar">
                            <?php echo substr($currentUser['first_name'], 0, 1); ?>
                        </div>
                        <div class="user-info d-none d-md-block">
                            <div class="user-name"><?php echo $currentUser['first_name'] . ' ' . $currentUser['last_name']; ?></div>
                            <div class="user-role"><?php echo $userTypeText; ?></div>
                        </div>
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header">
                            <strong><?php echo $currentUser['first_name'] . ' ' . $currentUser['last_name']; ?></strong><br>
                            <small class="text-muted"><?php echo $userTypeText; ?></small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo BASE_URL; ?>profile.php">
                            <i class="icon">👤</i> ข้อมูลส่วนตัว
                        </a>
                        <?php if ($_SESSION['user_type'] === USER_TYPE_SUPER_ADMIN): ?>
                        <a class="dropdown-item" href="<?php echo BASE_URL; ?>super-admin/settings/system.php">
                            <i class="icon">⚙️</i> ตั้งค่าระบบ
                        </a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout.php" data-confirm="คุณต้องการออกจากระบบหรือไม่?">
                            <i class="icon">🚪</i> ออกจากระบบ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <?php include dirname(__FILE__) . '/sidebar.php'; ?>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <?php if (!empty($pageTitle) || !empty($breadcrumbs)): ?>
        <div class="page-header">
            <?php if (!empty($breadcrumbs)): ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?php echo BASE_URL; ?>dashboard.php">หน้าหลัก</a>
                    </li>
                    <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                        <?php if ($index === count($breadcrumbs) - 1): ?>
                            <li class="breadcrumb-item active"><?php echo $breadcrumb['title']; ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo $breadcrumb['url']; ?>"><?php echo $breadcrumb['title']; ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title"><?php echo $pageTitle; ?></h1>
                    <?php if (isset($pageSubtitle)): ?>
                        <p class="page-subtitle"><?php echo $pageSubtitle; ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($pageActions)): ?>
                <div class="page-actions">
                    <?php echo $pageActions; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Alert Messages -->
        <div class="alert-container">
            <?php 
            $alert = getAlert();
            if ($alert): 
            ?>
                <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible" data-auto-hide="5000">
                    <?php echo $alert['message']; ?>
                    <button type="button" class="btn-close" aria-label="Close">×</button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Page Content Start -->
        <div class="page-content">

<style>
/* Additional styles for header */
.navbar {
    background: linear-gradient(135deg, var(--white) 0%, #f8faff 100%);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(37, 99, 235, 0.1);
}

.navbar-brand {
    font-weight: 700;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.navbar-brand:hover {
    color: var(--primary-dark);
    transform: translateY(-1px);
}

.logo-icon {
    font-size: 1.5rem;
    padding: 0.25rem;
    background: var(--primary-color);
    color: white;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.1rem;
}

.user-info {
    margin-left: 0.75rem;
    text-align: left;
}

.user-name {
    font-weight: 600;
    color: var(--gray-800);
    font-size: 0.9rem;
    line-height: 1.2;
}

.user-role {
    font-size: 0.8rem;
    color: var(--gray-500);
    line-height: 1.2;
}

.dropdown-menu {
    min-width: 250px;
    border: none;
    box-shadow: var(--shadow-lg);
    border-radius: var(--border-radius-lg);
    padding: 0;
    margin-top: 0.5rem;
}

.dropdown-header {
    background-color: var(--gray-50);
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
    border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
}

.dropdown-item {
    padding: 0.75rem 1rem;
    color: var(--gray-700);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border: none;
    transition: var(--transition);
}

.dropdown-item:hover {
    background-color: var(--gray-50);
    color: var(--primary-color);
}

.dropdown-item.text-danger:hover {
    background-color: #fef2f2;
    color: var(--danger-color);
}

.dropdown-item .icon {
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
}

.dropdown-divider {
    margin: 0.5rem 0;
    border-color: var(--gray-200);
}

.mobile-menu-toggle {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--gray-700);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.mobile-menu-toggle:hover {
    background-color: var(--gray-100);
    color: var(--primary-color);
}

.page-header {
    background: var(--white);
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.breadcrumb {
    background: none;
    padding: 0;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.breadcrumb-item a {
    color: var(--gray-500);
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: var(--primary-color);
}

.breadcrumb-item.active {
    color: var(--gray-700);
    font-weight: 500;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 0.25rem;
}

.page-subtitle {
    color: var(--gray-600);
    margin-bottom: 0;
}

.page-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.alert-container {
    position: relative;
    z-index: 100;
}

.alert {
    border: none;
    border-left: 4px solid transparent;
}

.alert-success {
    border-left-color: var(--success-color);
    background-color: #f0fdf4;
    color: #166534;
}

.alert-danger {
    border-left-color: var(--danger-color);
    background-color: #fef2f2;
    color: #991b1b;
}

.alert-warning {
    border-left-color: var(--warning-color);
    background-color: #fffbeb;
    color: #92400e;
}

.alert-info {
    border-left-color: var(--info-color);
    background-color: #f0f9ff;
    color: #155e75;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    line-height: 1;
    opacity: 0.5;
    cursor: pointer;
    padding: 0;
    margin-left: auto;
}

.btn-close:hover {
    opacity: 1;
}

/* Dropdown functionality */
.dropdown {
    position: relative;
}

.dropdown-toggle {
    cursor: pointer;
    user-select: none;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    display: none;
    background-color: var(--white);
    z-index: 1000;
}

.dropdown-menu.show {
    display: block;
}

@media (max-width: 768px) {
    .page-header {
        margin: 1rem;
        padding: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .page-actions {
        margin-top: 1rem;
        width: 100%;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
    }
}
</style>

<script>
// Dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggles = document.querySelectorAll('[data-toggle="dropdown"]');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if (menu !== this.nextElementSibling) {
                    menu.classList.remove('show');
                }
            });
            
            // Toggle current dropdown
            const menu = this.nextElementSibling;
            if (menu && menu.classList.contains('dropdown-menu')) {
                menu.classList.toggle('show');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    });
});
</script>