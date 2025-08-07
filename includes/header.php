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

// คำนวณ base path สำหรับ CSS และ JS
$currentDir = basename(dirname($_SERVER['SCRIPT_NAME']));
$basePath = '';

// ปรับ path ตามตำแหน่งของไฟล์
if (in_array($currentDir, ['super-admin', 'admin', 'chairman', 'judge'])) {
    $basePath = '../';
} else {
    $basePath = '';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SYSTEM_NAME; ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/responsive.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $basePath; ?>assets/images/favicon.ico">
    
    <!-- Meta tags -->
    <meta name="description" content="ระบบประมวลผลสิ่งประดิษฐ์คนรุ่นใหม่">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="INVENTION-VOTE System">
    <meta name="theme-color" content="#2563eb">
    
    <!-- CSRF Token Meta -->
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $basePath . $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom styles for this page -->
    <?php if (isset($customCSS)): ?>
        <style><?php echo $customCSS; ?></style>
    <?php endif; ?>
    
    <!-- Debug CSS Loading (เฉพาะ development) -->
    <?php if (ini_get('display_errors')): ?>
    <script>
    console.log('CSS Path Debug:');
    console.log('Base Path: <?php echo $basePath; ?>');
    console.log('Current Dir: <?php echo $currentDir; ?>');
    console.log('Full CSS Path: <?php echo $basePath; ?>assets/css/style.css');
    </script>
    <?php endif; ?>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar">
        <div class="container-fluid">
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle d-lg-none" type="button" aria-label="Toggle navigation">
                <span class="hamburger-icon">☰</span>
            </button>
            
            <!-- Brand -->
            <a href="<?php echo $basePath; ?>dashboard.php" class="navbar-brand">
                <span class="logo-icon">🔬</span>
                <span class="d-none d-md-inline">INVENTION-VOTE</span>
            </a>
            
            <!-- User Info & Actions -->
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-avatar">
                            <?php echo substr($currentUser['first_name'], 0, 1); ?>
                        </div>
                        <div class="user-info d-none d-md-block">
                            <div class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></div>
                            <div class="user-role"><?php echo $userTypeText; ?></div>
                        </div>
                        <span class="dropdown-arrow d-none d-md-inline">▼</span>
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <div class="dropdown-header">
                            <strong><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></strong><br>
                            <small class="text-muted"><?php echo $userTypeText; ?></small>
                            <?php if (isset($currentUser['institution_name']) && !empty($currentUser['institution_name'])): ?>
                                <br><small class="text-muted"><?php echo htmlspecialchars($currentUser['institution_name']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo $basePath; ?>profile.php">
                            <i class="icon">👤</i> ข้อมูลส่วนตัว
                        </a>
                        <?php if ($_SESSION['user_type'] === USER_TYPE_SUPER_ADMIN): ?>
                        <a class="dropdown-item" href="<?php echo $basePath; ?>super-admin/settings/system.php">
                            <i class="icon">⚙️</i> ตั้งค่าระบบ
                        </a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?php echo $basePath; ?>logout.php" data-confirm="คุณต้องการออกจากระบบหรือไม่?">
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
                        <a href="<?php echo $basePath; ?>dashboard.php">หน้าหลัก</a>
                    </li>
                    <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                        <?php if ($index === count($breadcrumbs) - 1): ?>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($breadcrumb['title']); ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo htmlspecialchars($breadcrumb['url']); ?>"><?php echo htmlspecialchars($breadcrumb['title']); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between align-items-start">
                <div class="page-header-content">
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
                <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible" data-auto-hide="5000" role="alert">
                    <?php echo $alert['message']; ?>
                    <button type="button" class="btn-close" aria-label="Close">×</button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Page Content Start -->
        <div class="page-content">

<style>
/* Additional styles for header improvements */
.navbar {
    background: linear-gradient(135deg, var(--white) 0%, #f8faff 100%);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(37, 99, 235, 0.1);
    position: sticky;
    top: 0;
    z-index: 1030;
}

.navbar-brand {
    font-weight: 700;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: var(--transition);
}

.navbar-brand:hover {
    color: var(--primary-dark);
    text-decoration: none;
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
    transition: var(--transition);
}

.navbar-brand:hover .logo-icon {
    background: var(--primary-dark);
    transform: rotate(10deg);
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
    display: none;
}

.mobile-menu-toggle:hover {
    background-color: var(--gray-100);
    color: var(--primary-color);
}

.mobile-menu-toggle:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

.hamburger-icon {
    font-size: 1.25rem;
}

.nav-link {
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
}

.nav-link:hover {
    text-decoration: none;
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
    transition: var(--transition);
}

.nav-link:hover .user-avatar {
    background: var(--primary-dark);
    transform: scale(1.05);
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

.dropdown-arrow {
    font-size: 0.75rem;
    margin-left: 0.5rem;
    color: var(--gray-500);
    transition: var(--transition);
}

.dropdown.show .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown {
    position: relative;
}

.dropdown-menu {
    min-width: 280px;
    border: none;
    box-shadow: var(--shadow-lg);
    border-radius: var(--border-radius-lg);
    padding: 0;
    margin-top: 0.5rem;
    background: var(--white);
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    z-index: 1000;
}

.dropdown-menu.show {
    display: block;
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
    text-decoration: none;
    background: none;
    width: 100%;
    text-align: left;
}

.dropdown-item:hover {
    background-color: var(--gray-50);
    color: var(--primary-color);
    text-decoration: none;
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
    border-top: 1px solid var(--gray-200);
}

.page-header {
    background: var(--white);
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.page-header-content {
    flex: 1;
}

.breadcrumb {
    background: none;
    padding: 0;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    list-style: none;
    display: flex;
    flex-wrap: wrap;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    padding: 0 0.5rem;
    color: var(--gray-400);
}

.breadcrumb-item a {
    color: var(--gray-500);
    text-decoration: none;
    transition: var(--transition);
}

.breadcrumb-item a:hover {
    color: var(--primary-color);
    text-decoration: none;
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
    font-size: 1rem;
}

.page-actions {
    display: flex;
    gap: 0.5rem;
    align-items: flex-start;
    flex-wrap: wrap;
}

.alert-container {
    position: relative;
    z-index: 100;
    margin-bottom: 1rem;
}

.alert {
    border: none;
    border-left: 4px solid transparent;
    border-radius: var(--border-radius);
    position: relative;
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

.alert-dismissible {
    padding-right: 3rem;
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
    position: absolute;
    top: 1rem;
    right: 1rem;
    transition: var(--transition);
}

.btn-close:hover {
    opacity: 1;
}

/* Mobile Specific Styles */
@media (max-width: 991px) {
    .mobile-menu-toggle {
        display: block;
    }
    
    .d-lg-none {
        display: none !important;
    }
}

@media (max-width: 767px) {
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
        justify-content: stretch;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .dropdown-menu {
        min-width: 250px;
        left: auto;
        right: 0;
    }
    
    .navbar {
        padding: 0.5rem 1rem;
    }
    
    .navbar-brand {
        font-size: 1.1rem;
    }
    
    .logo-icon {
        width: 36px;
        height: 36px;
        font-size: 1.25rem;
    }
}

@media (max-width: 480px) {
    .dropdown-menu {
        min-width: calc(100vw - 2rem);
        right: 1rem;
        left: 1rem;
    }
    
    .page-header {
        margin: 0.5rem;
        padding: 0.75rem;
    }
    
    .page-title {
        font-size: 1.25rem;
    }
}

/* Focus states for accessibility */
.dropdown-item:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: -2px;
}

.nav-link:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
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
                    menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                }
            });
            
            // Toggle current dropdown
            const menu = this.nextElementSibling;
            if (menu && menu.classList.contains('dropdown-menu')) {
                const isShowing = menu.classList.contains('show');
                menu.classList.toggle('show');
                this.setAttribute('aria-expanded', !isShowing);
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
            menu.previousElementSibling.setAttribute('aria-expanded', 'false');
        });
    });
    
    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
                menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                menu.previousElementSibling.focus();
            });
        }
    });
});
</script>