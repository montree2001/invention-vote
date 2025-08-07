<?php
// includes/sidebar.php - เมนูข้างสำหรับระบบ

// กำหนด base URL ตามตำแหน่งของไฟล์ปัจจุบัน
$currentDir = basename(dirname($_SERVER['SCRIPT_NAME']));
$baseUrlForSidebar = '';

if ($currentDir === 'super-admin' || $currentDir === 'admin' || $currentDir === 'chairman' || $currentDir === 'judge') {
    $baseUrlForSidebar = '../';
} else {
    $baseUrlForSidebar = '';
}

// เมนูตามสิทธิ์ผู้ใช้
function getSidebarMenu($userType) {
    global $baseUrlForSidebar;
    
    $menus = [];
    
    if ($userType === USER_TYPE_SUPER_ADMIN) {
        $menus = [
            [
                'title' => 'Dashboard',
                'icon' => '🏠',
                'url' => $baseUrlForSidebar . 'super-admin/',
                'active' => 'dashboard'
            ],
            [
                'title' => 'จัดการผู้ใช้งาน',
                'icon' => '👥',
                'submenu' => [
                    ['title' => 'ผู้ใช้งานทั้งหมด', 'url' => $baseUrlForSidebar . 'super-admin/users/', 'active' => 'users'],
                    ['title' => 'เพิ่มผู้ใช้ใหม่', 'url' => $baseUrlForSidebar . 'super-admin/users/add.php', 'active' => 'add_user']
                ]
            ],
            [
                'title' => 'จัดการการแข่งขัน',
                'icon' => '🏆',
                'submenu' => [
                    ['title' => 'การแข่งขันทั้งหมด', 'url' => $baseUrlForSidebar . 'super-admin/competitions/', 'active' => 'competitions'],
                    ['title' => 'สร้างการแข่งขันใหม่', 'url' => $baseUrlForSidebar . 'super-admin/competitions/add.php', 'active' => 'add_competition']
                ]
            ],
            [
                'title' => 'จัดการระดับการแข่งขัน',
                'icon' => '📊',
                'url' => $baseUrlForSidebar . 'super-admin/levels/',
                'active' => 'levels'
            ],
            [
                'title' => 'จัดการประเภทสิ่งประดิษฐ์',
                'icon' => '🔬',
                'url' => $baseUrlForSidebar . 'super-admin/categories/',
                'active' => 'categories'
            ],
            [
                'title' => 'จัดการเกณฑ์การให้คะแนน',
                'icon' => '📋',
                'url' => $baseUrlForSidebar . 'super-admin/scoring-criteria/',
                'active' => 'scoring_criteria'
            ],
            [
                'title' => 'รายงานและสถิติ',
                'icon' => '📈',
                'submenu' => [
                    ['title' => 'ภาพรวมระบบ', 'url' => $baseUrlForSidebar . 'super-admin/reports/overview.php', 'active' => 'overview'],
                    ['title' => 'สถิติผู้ใช้งาน', 'url' => $baseUrlForSidebar . 'super-admin/reports/user_statistics.php', 'active' => 'user_stats'],
                    ['title' => 'Log การใช้งาน', 'url' => $baseUrlForSidebar . 'super-admin/reports/system_logs.php', 'active' => 'system_logs']
                ]
            ],
            [
                'title' => 'ตั้งค่าระบบ',
                'icon' => '⚙️',
                'submenu' => [
                    ['title' => 'การตั้งค่าทั่วไป', 'url' => $baseUrlForSidebar . 'super-admin/settings/system.php', 'active' => 'system_settings'],
                    ['title' => 'สำรองข้อมูล', 'url' => $baseUrlForSidebar . 'super-admin/settings/backup.php', 'active' => 'backup'],
                    ['title' => 'โหมดบำรุงรักษา', 'url' => $baseUrlForSidebar . 'super-admin/settings/maintenance.php', 'active' => 'maintenance']
                ]
            ]
        ];
    }
    
    elseif ($userType === USER_TYPE_ADMIN) {
        $menus = [
            [
                'title' => 'Dashboard',
                'icon' => '🏠',
                'url' => $baseUrlForSidebar . 'admin/',
                'active' => 'dashboard'
            ],
            [
                'title' => 'จัดการการแข่งขัน',
                'icon' => '🏆',
                'url' => $baseUrlForSidebar . 'admin/competitions/manage.php',
                'active' => 'competitions'
            ],
            [
                'title' => 'จัดการสิ่งประดิษฐ์',
                'icon' => '🔬',
                'submenu' => [
                    ['title' => 'รายการสิ่งประดิษฐ์', 'url' => $baseUrlForSidebar . 'admin/inventions/', 'active' => 'inventions'],
                    ['title' => 'รอการอนุมัติ', 'url' => $baseUrlForSidebar . 'admin/inventions/approve.php', 'active' => 'approve_inventions']
                ]
            ],
            [
                'title' => 'จัดการกรรมการ',
                'icon' => '👨‍⚖️',
                'submenu' => [
                    ['title' => 'รายการกรรมการ', 'url' => $baseUrlForSidebar . 'admin/judges/', 'active' => 'judges'],
                    ['title' => 'มอบหมายกรรมการ', 'url' => $baseUrlForSidebar . 'admin/judges/assign.php', 'active' => 'assign_judges'],
                    ['title' => 'ความคืบหน้า', 'url' => $baseUrlForSidebar . 'admin/judges/progress.php', 'active' => 'judge_progress']
                ]
            ],
            [
                'title' => 'จัดการประธานกรรมการ',
                'icon' => '👑',
                'submenu' => [
                    ['title' => 'รายการประธาน', 'url' => $baseUrlForSidebar . 'admin/chairman/', 'active' => 'chairman'],
                    ['title' => 'มอบหมายประธาน', 'url' => $baseUrlForSidebar . 'admin/chairman/assign.php', 'active' => 'assign_chairman']
                ]
            ],
            [
                'title' => 'จัดการการให้คะแนน',
                'icon' => '📊',
                'submenu' => [
                    ['title' => 'ตั้งค่าการให้คะแนน', 'url' => $baseUrlForSidebar . 'admin/scoring/settings.php', 'active' => 'scoring_settings'],
                    ['title' => 'เปิด-ปิดจุดให้คะแนน', 'url' => $baseUrlForSidebar . 'admin/scoring/enable_disable.php', 'active' => 'scoring_control'],
                    ['title' => 'ยกเลิกการลงคะแนน', 'url' => $baseUrlForSidebar . 'admin/scoring/cancel_votes.php', 'active' => 'cancel_votes']
                ]
            ],
            [
                'title' => 'รายงาน',
                'icon' => '📈',
                'submenu' => [
                    ['title' => 'สรุปผลคะแนน', 'url' => $baseUrlForSidebar . 'admin/reports/scoring_summary.php', 'active' => 'scoring_summary'],
                    ['title' => 'ความคืบหน้ากรรมการ', 'url' => $baseUrlForSidebar . 'admin/reports/judge_progress.php', 'active' => 'judge_progress_report'],
                    ['title' => 'คะแนนรายละเอียด', 'url' => $baseUrlForSidebar . 'admin/reports/detailed_scores.php', 'active' => 'detailed_scores'],
                    ['title' => 'สรุปเหรียญ', 'url' => $baseUrlForSidebar . 'admin/reports/medals.php', 'active' => 'medals'],
                    ['title' => 'ประวัติการลงคะแนน', 'url' => $baseUrlForSidebar . 'admin/reports/voting_history.php', 'active' => 'voting_history']
                ]
            ],
            [
                'title' => 'ติดตามการใช้งาน',
                'icon' => '👁️',
                'submenu' => [
                    ['title' => 'กิจกรรมผู้ใช้', 'url' => $baseUrlForSidebar . 'admin/monitoring/user_activity.php', 'active' => 'user_activity'],
                    ['title' => 'Log IP Address', 'url' => $baseUrlForSidebar . 'admin/monitoring/ip_logs.php', 'active' => 'ip_logs']
                ]
            ]
        ];
    }
    
    elseif ($userType === USER_TYPE_CHAIRMAN) {
        $menus = [
            [
                'title' => 'Dashboard',
                'icon' => '🏠',
                'url' => $baseUrlForSidebar . 'chairman/',
                'active' => 'dashboard'
            ],
            [
                'title' => 'การแข่งขันที่รับผิดชอบ',
                'icon' => '🏆',
                'url' => $baseUrlForSidebar . 'chairman/competitions/my_competitions.php',
                'active' => 'my_competitions'
            ],
            [
                'title' => 'สิ่งประดิษฐ์ในหมวด',
                'icon' => '🔬',
                'url' => $baseUrlForSidebar . 'chairman/inventions/',
                'active' => 'inventions'
            ],
            [
                'title' => 'การให้คะแนน',
                'icon' => '📊',
                'submenu' => [
                    ['title' => 'ภาพรวมการให้คะแนน', 'url' => $baseUrlForSidebar . 'chairman/scoring/overview.php', 'active' => 'scoring_overview'],
                    ['title' => 'ความคืบหน้า', 'url' => $baseUrlForSidebar . 'chairman/scoring/progress.php', 'active' => 'scoring_progress'],
                    ['title' => 'รับรองผลคะแนน', 'url' => $baseUrlForSidebar . 'chairman/scoring/approve.php', 'active' => 'approve_results']
                ]
            ],
            [
                'title' => 'รายงาน',
                'icon' => '📈',
                'submenu' => [
                    ['title' => 'สรุปผลคะแนน', 'url' => $baseUrlForSidebar . 'chairman/reports/scoring_summary.php', 'active' => 'scoring_summary'],
                    ['title' => 'คะแนนเฉลี่ยกรรมการ', 'url' => $baseUrlForSidebar . 'chairman/reports/judge_scores.php', 'active' => 'judge_scores'],
                    ['title' => 'รายงานรายละเอียด', 'url' => $baseUrlForSidebar . 'chairman/reports/detailed_report.php', 'active' => 'detailed_report'],
                    ['title' => 'สรุปเหรียญ', 'url' => $baseUrlForSidebar . 'chairman/reports/medals.php', 'active' => 'medals'],
                    ['title' => 'ประวัติการลงคะแนน', 'url' => $baseUrlForSidebar . 'chairman/reports/voting_history.php', 'active' => 'voting_history']
                ]
            ]
        ];
    }
    
    elseif ($userType === USER_TYPE_JUDGE) {
        $menus = [
            [
                'title' => 'Dashboard',
                'icon' => '🏠',
                'url' => $baseUrlForSidebar . 'judge/',
                'active' => 'dashboard'
            ],
            [
                'title' => 'การแข่งขันที่ได้รับมอบหมาย',
                'icon' => '🏆',
                'url' => $baseUrlForSidebar . 'judge/competitions/my_assignments.php',
                'active' => 'my_assignments'
            ],
            [
                'title' => 'สิ่งประดิษฐ์ที่ต้องให้คะแนน',
                'icon' => '🔬',
                'url' => $baseUrlForSidebar . 'judge/inventions/',
                'active' => 'inventions'
            ],
            [
                'title' => 'การลงคะแนน',
                'icon' => '🗳️',
                'submenu' => [
                    ['title' => 'ลงคะแนน', 'url' => $baseUrlForSidebar . 'judge/scoring/vote.php', 'active' => 'vote'],
                    ['title' => 'คะแนนที่ลงแล้ว', 'url' => $baseUrlForSidebar . 'judge/scoring/my_votes.php', 'active' => 'my_votes'],
                    ['title' => 'ความคืบหน้า', 'url' => $baseUrlForSidebar . 'judge/scoring/progress.php', 'active' => 'progress']
                ]
            ]
        ];
    }
    
    return $menus;
}

$sidebarMenus = getSidebarMenu($_SESSION['user_type']);
?>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h4 class="sidebar-title">
            <span class="sidebar-icon">🔬</span>
            INVENTION-VOTE
        </h4>
        <p class="sidebar-subtitle"><?php echo getUserTypeText($_SESSION['user_type']); ?></p>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <?php foreach ($sidebarMenus as $menu): ?>
                <li class="menu-item <?php echo (isset($menu['submenu']) ? 'has-submenu' : ''); ?>">
                    <?php if (isset($menu['submenu'])): ?>
                        <a href="#" class="menu-link submenu-toggle <?php echo (isset($currentPage) && in_array($currentPage, array_column($menu['submenu'], 'active'))) ? 'active' : ''; ?>">
                            <span class="menu-icon"><?php echo $menu['icon']; ?></span>
                            <span class="menu-text"><?php echo $menu['title']; ?></span>
                            <span class="menu-arrow">▶</span>
                        </a>
                        <ul class="submenu <?php echo (isset($currentPage) && in_array($currentPage, array_column($menu['submenu'], 'active'))) ? 'show' : ''; ?>">
                            <?php foreach ($menu['submenu'] as $submenu): ?>
                                <li class="submenu-item">
                                    <a href="<?php echo $submenu['url']; ?>" class="submenu-link <?php echo (isset($currentPage) && $currentPage === $submenu['active']) ? 'active' : ''; ?>">
                                        <?php echo $submenu['title']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <a href="<?php echo $menu['url']; ?>" class="menu-link <?php echo (isset($currentPage) && $currentPage === $menu['active']) ? 'active' : ''; ?>">
                            <span class="menu-icon"><?php echo $menu['icon']; ?></span>
                            <span class="menu-text"><?php echo $menu['title']; ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            
            <!-- ข้อมูลส่วนตัว -->
            <li class="menu-item menu-divider">
                <a href="<?php echo $baseUrlForSidebar; ?>profile.php" class="menu-link <?php echo (isset($currentPage) && $currentPage === 'profile') ? 'active' : ''; ?>">
                    <span class="menu-icon">👤</span>
                    <span class="menu-text">ข้อมูลส่วนตัว</span>
                </a>
            </li>
            
            <!-- ออกจากระบบ -->
            <li class="menu-item">
                <a href="<?php echo $baseUrlForSidebar; ?>logout.php" class="menu-link text-danger" data-confirm="คุณต้องการออกจากระบบหรือไม่?">
                    <span class="menu-icon">🚪</span>
                    <span class="menu-text">ออกจากระบบ</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<style>
/* Sidebar Styles */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100vh;
    background: linear-gradient(180deg, #ffffff 0%, #f8faff 100%);
    border-right: 1px solid var(--gray-200);
    z-index: 998;
    overflow-y: auto;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}

.sidebar.show {
    transform: translateX(0);
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    background: var(--white);
}

.sidebar-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sidebar-icon {
    font-size: 1.5rem;
}

.sidebar-subtitle {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0.5rem 0 0 0;
}

.sidebar-nav {
    padding: 1rem 0;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-item {
    position: relative;
}

.menu-item.menu-divider {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.menu-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    color: var(--gray-700);
    text-decoration: none;
    transition: var(--transition);
    font-weight: 500;
}

.menu-link:hover {
    background-color: var(--gray-50);
    color: var(--primary-color);
    text-decoration: none;
}

.menu-link.active {
    background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    color: white;
    position: relative;
}

.menu-link.active::before {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 100%;
    background: rgba(255, 255, 255, 0.3);
}

.menu-icon {
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
}

.menu-text {
    flex: 1;
}

.menu-arrow {
    font-size: 0.75rem;
    transition: transform 0.3s ease;
    margin-left: auto;
}

.has-submenu .menu-link.active .menu-arrow,
.has-submenu:hover .menu-arrow {
    transform: rotate(90deg);
}

.submenu {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 0;
    overflow: hidden;
    background: var(--gray-50);
    transition: max-height 0.3s ease;
}

.submenu.show {
    max-height: 500px;
}

.submenu-item {
    border-left: 2px solid transparent;
}

.submenu-link {
    display: block;
    padding: 0.5rem 1.5rem 0.5rem 3rem;
    color: var(--gray-600);
    text-decoration: none;
    transition: var(--transition);
    font-size: 0.9rem;
}

.submenu-link:hover {
    background-color: var(--gray-100);
    color: var(--primary-color);
    text-decoration: none;
}

.submenu-link.active {
    color: var(--primary-color);
    background-color: var(--gray-100);
    font-weight: 600;
    border-left-color: var(--primary-color);
}

.text-danger {
    color: var(--danger-color) !important;
}

.text-danger:hover {
    color: #dc2626 !important;
    background-color: #fef2f2 !important;
}

/* Desktop Styles */
@media (min-width: 992px) {
    .sidebar {
        transform: translateX(0);
        position: fixed;
    }
    
    .main-content {
        margin-left: 280px;
    }
}

/* Mobile Styles */
@media (max-width: 991px) {
    .sidebar {
        z-index: 1001;
        box-shadow: var(--shadow-lg);
    }
    
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
    }
    
    .sidebar-overlay.show {
        opacity: 1;
        visibility: visible;
    }
    
    .main-content {
        margin-left: 0;
    }
}

/* Scrollbar Styling */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: var(--gray-100);
}

.sidebar::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: var(--gray-400);
}
</style>

<script>
// Submenu Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const submenu = this.nextElementSibling;
            const arrow = this.querySelector('.menu-arrow');
            
            if (submenu && submenu.classList.contains('submenu')) {
                submenu.classList.toggle('show');
                arrow.style.transform = submenu.classList.contains('show') ? 'rotate(90deg)' : 'rotate(0deg)';
            }
        });
    });
    
    // Mobile sidebar overlay
    if (window.innerWidth <= 991) {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
        
        // Show/hide overlay with sidebar
        const sidebar = document.querySelector('.sidebar');
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (sidebar.classList.contains('show')) {
                        overlay.classList.add('show');
                        document.body.style.overflow = 'hidden';
                    } else {
                        overlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }
            });
        });
        
        observer.observe(sidebar, { attributes: true });
        
        // Close sidebar when clicking overlay
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
        });
    }
});
</script>