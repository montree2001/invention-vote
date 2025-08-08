<?php
// includes/header.php - Modern Header with New Theme

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

// กำหนดค่า default
$pageTitle = $pageTitle ?? 'Dashboard';
$pageSubtitle = $pageSubtitle ?? '';
$breadcrumbs = $breadcrumbs ?? [];
$currentPage = $currentPage ?? '';

// คำนวณ base path
$currentDir = basename(dirname($_SERVER['SCRIPT_NAME']));
$basePath = '';

if (in_array($currentDir, ['super-admin', 'admin', 'chairman', 'judge'])) {
    $basePath = '../';
} else {
    $basePath = '';
}

// กำหนด active navigation
$navItems = [
    'dashboard' => [
        'title' => 'หน้าหลัก',
        'url' => $basePath . 'dashboard.php',
        'icon' => '🏠',
        'section' => 'main'
    ],
    'competitions' => [
        'title' => 'การแข่งขัน',
        'url' => $basePath . 'competitions/',
        'icon' => '🏆',
        'section' => 'manage'
    ],
    'inventions' => [
        'title' => 'สิ่งประดิษฐ์',
        'url' => $basePath . 'inventions/',
        'icon' => '💡',
        'section' => 'manage'
    ],
    'categories' => [
        'title' => 'ประเภท',
        'url' => $basePath . 'categories.php',
        'icon' => '📋',
        'section' => 'manage'
    ],
    'users' => [
        'title' => 'ผู้ใช้งาน',
        'url' => $basePath . 'users.php',
        'icon' => '👥',
        'section' => 'manage'
    ],
    'scoring' => [
        'title' => 'การลงคะแนน',
        'url' => $basePath . 'scoring/',
        'icon' => '⭐',
        'section' => 'judge'
    ],
    'reports' => [
        'title' => 'รายงาน',
        'url' => $basePath . 'reports.php',
        'icon' => '📊',
        'section' => 'judge'
    ],
    'settings' => [
        'title' => 'ตั้งค่า',
        'url' => $basePath . 'settings/',
        'icon' => '⚙️',
        'section' => 'system'
    ]
];

// Filter navigation based on user type
$allowedNavs = [];
switch ($_SESSION['user_type']) {
    case USER_TYPE_SUPER_ADMIN:
        $allowedNavs = array_keys($navItems);
        break;
    case USER_TYPE_ADMIN:
        $allowedNavs = ['dashboard', 'competitions', 'inventions', 'categories', 'users', 'scoring', 'reports'];
        break;
    case USER_TYPE_CHAIRMAN:
        $allowedNavs = ['dashboard', 'reports', 'scoring'];
        break;
    case USER_TYPE_JUDGE:
        $allowedNavs = ['dashboard', 'scoring'];
        break;
    default:
        $allowedNavs = ['dashboard'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - <?php echo SYSTEM_NAME; ?></title>
    
    <!-- Modern CSS Theme -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/modern-style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/responsive.css">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $basePath; ?>assets/images/favicon.ico">
    
    <!-- Meta tags -->
    <meta name="description" content="ระบบประเมินสิ่งประดิษฐ์คนรุ่นใหม่ - INVENTION-VOTE">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="INVENTION-VOTE System">
    <meta name="theme-color" content="#0ea5e9">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    
    <!-- Additional CSS -->
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $basePath . $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom CSS -->
    <?php if (isset($customCSS)): ?>
        <style><?php echo $customCSS; ?></style>
    <?php endif; ?>
    
    <!-- Preloader Styles -->
    <style>
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }
        
        .loader-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--gray-200);
            border-top: 3px solid var(--primary-500);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .page-loader.hide {
            opacity: 0;
            visibility: hidden;
        }
    </style>
</head>
<body>
    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
    </div>
    
    <!-- Overlay for mobile sidebar -->
    <div class="overlay" id="sidebarOverlay"></div>
    
    <!-- Top Navigation -->
    <nav class="navbar">
        <div class="navbar-content">
            <!-- Left side -->
            <div class="d-flex align-items-center">
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle" type="button" aria-label="Toggle navigation">
                    <i class="bi bi-list"></i>
                </button>
                
                <!-- Brand -->
                <a href="<?php echo $basePath; ?>dashboard.php" class="navbar-brand">
                    <div class="brand-logo">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <span class="d-none d-md-inline">INVENTION-VOTE</span>
                </a>
            </div>
            
            <!-- Right side -->
            <div class="navbar-nav">
                <!-- Notifications (placeholder for future) -->
                <div class="nav-item d-none d-md-block">
                    <button class="btn btn-outline-secondary btn-sm" type="button" title="การแจ้งเตือน">
                        <i class="bi bi-bell"></i>
                    </button>
                </div>
                
                <!-- User Dropdown -->
                <div class="user-dropdown" id="userDropdown">
                    <button class="user-trigger" type="button" aria-expanded="false">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></div>
                            <div class="user-role"><?php echo $userTypeText; ?></div>
                        </div>
                        <i class="bi bi-chevron-down dropdown-arrow"></i>
                    </button>
                    
                    <div class="dropdown-menu" id="userDropdownMenu">
                        <div class="dropdown-header">
                            <div class="fw-semibold"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></div>
                            <div class="text-muted"><?php echo $userTypeText; ?></div>
                            <?php if (isset($currentUser['institution_name']) && !empty($currentUser['institution_name'])): ?>
                                <div class="text-muted"><?php echo htmlspecialchars($currentUser['institution_name']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <a class="dropdown-item" href="<?php echo $basePath; ?>profile.php">
                            <i class="bi bi-person"></i>
                            ข้อมูลส่วนตัว
                        </a>
                        
                        <?php if ($_SESSION['user_type'] === USER_TYPE_SUPER_ADMIN): ?>
                        <a class="dropdown-item" href="<?php echo $basePath; ?>super-admin/settings/">
                            <i class="bi bi-gear"></i>
                            ตั้งค่าระบบ
                        </a>
                        <?php endif; ?>
                        
                        <div class="dropdown-divider"></div>
                        
                        <a class="dropdown-item text-danger" href="<?php echo $basePath; ?>logout.php" 
                           onclick="return confirm('คุณต้องการออกจากระบบหรือไม่?')">
                            <i class="bi bi-box-arrow-right"></i>
                            ออกจากระบบ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-title">INVENTION-VOTE</div>
            <div class="sidebar-subtitle"><?php echo $userTypeText; ?></div>
        </div>
        
        <nav class="sidebar-nav">
            <!-- Main Section -->
            <div class="nav-section">
                <div class="nav-section-title">หลัก</div>
                <ul class="nav-list">
                    <?php 
                    $mainNavs = array_filter($navItems, fn($item) => in_array($item['section'], ['main']) && in_array(array_search($item, $navItems), $allowedNavs));
                    foreach ($mainNavs as $key => $nav): ?>
                    <li class="nav-item">
                        <a href="<?php echo $nav['url']; ?>" 
                           class="nav-link <?php echo $currentPage === $key ? 'active' : ''; ?>">
                            <span class="nav-icon"><?php echo $nav['icon']; ?></span>
                            <?php echo $nav['title']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Management Section -->
            <?php 
            $manageNavs = array_filter($navItems, fn($item) => in_array($item['section'], ['manage']) && in_array(array_search($item, $navItems), $allowedNavs));
            if (!empty($manageNavs)): ?>
            <div class="nav-section">
                <div class="nav-section-title">จัดการ</div>
                <ul class="nav-list">
                    <?php foreach ($manageNavs as $key => $nav): ?>
                    <li class="nav-item">
                        <a href="<?php echo $nav['url']; ?>" 
                           class="nav-link <?php echo $currentPage === $key ? 'active' : ''; ?>">
                            <span class="nav-icon"><?php echo $nav['icon']; ?></span>
                            <?php echo $nav['title']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Judging Section -->
            <?php 
            $judgeNavs = array_filter($navItems, fn($item) => in_array($item['section'], ['judge']) && in_array(array_search($item, $navItems), $allowedNavs));
            if (!empty($judgeNavs)): ?>
            <div class="nav-section">
                <div class="nav-section-title">ตัดสิน</div>
                <ul class="nav-list">
                    <?php foreach ($judgeNavs as $key => $nav): ?>
                    <li class="nav-item">
                        <a href="<?php echo $nav['url']; ?>" 
                           class="nav-link <?php echo $currentPage === $key ? 'active' : ''; ?>">
                            <span class="nav-icon"><?php echo $nav['icon']; ?></span>
                            <?php echo $nav['title']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- System Section -->
            <?php 
            $systemNavs = array_filter($navItems, fn($item) => in_array($item['section'], ['system']) && in_array(array_search($item, $navItems), $allowedNavs));
            if (!empty($systemNavs)): ?>
            <div class="nav-section">
                <div class="nav-section-title">ระบบ</div>
                <ul class="nav-list">
                    <?php foreach ($systemNavs as $key => $nav): ?>
                    <li class="nav-item">
                        <a href="<?php echo $nav['url']; ?>" 
                           class="nav-link <?php echo $currentPage === $key ? 'active' : ''; ?>">
                            <span class="nav-icon"><?php echo $nav['icon']; ?></span>
                            <?php echo $nav['title']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-wrapper">
            <!-- Breadcrumb -->
            <?php if (!empty($breadcrumbs)): ?>
            <nav class="breadcrumb" aria-label="breadcrumb">
                <a href="<?php echo $basePath; ?>dashboard.php" class="breadcrumb-link">หน้าหลัก</a>
                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                    <?php if (isset($breadcrumb['url']) && !empty($breadcrumb['url'])): ?>
                        <a href="<?php echo htmlspecialchars($breadcrumb['url']); ?>" class="breadcrumb-link">
                            <?php echo htmlspecialchars($breadcrumb['title']); ?>
                        </a>
                    <?php else: ?>
                        <span class="breadcrumb-current"><?php echo htmlspecialchars($breadcrumb['title']); ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
            <?php endif; ?>
            
            <!-- Page Header -->
            <?php if (!empty($pageTitle)): ?>
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="page-header-content">
                        <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
                        <?php if (!empty($pageSubtitle)): ?>
                            <p class="page-subtitle"><?php echo htmlspecialchars($pageSubtitle); ?></p>
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
                    <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible" id="alertMessage" role="alert">
                        <?php if ($alert['type'] === 'success'): ?>
                            <i class="bi bi-check-circle me-2"></i>
                        <?php elseif ($alert['type'] === 'danger'): ?>
                            <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php elseif ($alert['type'] === 'warning'): ?>
                            <i class="bi bi-exclamation-circle me-2"></i>
                        <?php else: ?>
                            <i class="bi bi-info-circle me-2"></i>
                        <?php endif; ?>
                        <?php echo $alert['message']; ?>
                        <button type="button" class="btn-close" onclick="dismissAlert()" aria-label="Close">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Page Content Start -->
            <div class="page-content animate-fadeIn">

<script>
// Global JavaScript for the header and navigation
document.addEventListener('DOMContentLoaded', function() {
    // Hide page loader
    setTimeout(() => {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.classList.add('hide');
            setTimeout(() => loader.remove(), 300);
        }
    }, 500);
    
    // Mobile menu toggle
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (mobileToggle && sidebar) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            if (window.innerWidth > 1024) {
                mainContent.classList.toggle('shifted');
            } else {
                document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
            }
        });
    }
    
    // Close sidebar on overlay click (mobile)
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            document.body.style.overflow = '';
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            document.body.style.overflow = '';
            if (sidebar.classList.contains('show')) {
                mainContent.classList.add('shifted');
            }
        } else {
            mainContent.classList.remove('shifted');
            if (sidebar.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
            }
        }
    });
    
    // User dropdown toggle
    const userDropdown = document.getElementById('userDropdown');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    
    if (userDropdown && userDropdownMenu) {
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('show');
            userDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            userDropdownMenu.classList.remove('show');
            userDropdown.classList.remove('show');
        });
        
        userDropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Auto-dismiss alerts
    const alertMessage = document.getElementById('alertMessage');
    if (alertMessage) {
        setTimeout(() => {
            dismissAlert();
        }, 5000);
    }
    
    // Set active navigation
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        if (link.href === window.location.href || currentPath.includes(new URL(link.href).pathname)) {
            link.classList.add('active');
        }
    });
    
    // Add loading states to buttons and forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>กำลังดำเนินการ...';
            }
        });
    });
    
    // Initialize tooltips (if using Bootstrap tooltips)
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    if (typeof bootstrap !== 'undefined') {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Add smooth scrolling to anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Enhanced form validation
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearValidation);
    });
    
    // Add keyboard navigation support
    document.addEventListener('keydown', function(e) {
        // ESC key closes dropdowns and modals
        if (e.key === 'Escape') {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
            document.querySelectorAll('.modal.show').forEach(modal => {
                modal.classList.remove('show');
            });
        }
        
        // Ctrl+/ or Cmd+/ opens sidebar (if not mobile)
        if ((e.ctrlKey || e.metaKey) && e.key === '/') {
            if (window.innerWidth > 1024) {
                e.preventDefault();
                mobileToggle.click();
            }
        }
    });
});

// Utility functions
function dismissAlert() {
    const alert = document.getElementById('alertMessage');
    if (alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(() => alert.remove(), 300);
    }
}

function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    
    // Remove existing validation classes
    field.classList.remove('is-valid', 'is-invalid');
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        field.classList.add('is-invalid');
        return false;
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            field.classList.add('is-invalid');
            return false;
        }
    }
    
    // Phone validation (Thai format)
    if (field.type === 'tel' && value) {
        const phoneRegex = /^[0-9]{9,10}$/;
        if (!phoneRegex.test(value.replace(/[-\s]/g, ''))) {
            field.classList.add('is-invalid');
            return false;
        }
    }
    
    // If validation passes
    if (value) {
        field.classList.add('is-valid');
    }
    
    return true;
}

function clearValidation(e) {
    e.target.classList.remove('is-valid', 'is-invalid');
}

function showLoading(element, text = 'กำลังโหลด...') {
    element.disabled = true;
    element.innerHTML = `<i class="bi bi-hourglass-split me-2"></i>${text}`;
}

function hideLoading(element, originalText) {
    element.disabled = false;
    element.innerHTML = originalText;
}

// Confirmation dialogs
function confirmDelete(message = 'คุณแน่ใจหรือไม่ที่จะลบรายการนี้?') {
    return confirm(message);
}

function confirmAction(message = 'คุณแน่ใจหรือไม่ที่จะดำเนินการนี้?') {
    return confirm(message);
}

// CSRF token helper
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

// Toast notification system (simple implementation)
function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    `;
    toast.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()">
            <i class="bi bi-x"></i>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// Add slide animations for toasts
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>