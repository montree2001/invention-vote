<?php
/* session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

// ตรวจสอบสิทธิ์การเข้าถึง
checkAdminAccess();

$admin_name = $_SESSION['user_name'] ?? 'ผู้ดูแลระบบ'; */
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดผู้ดูแลระบบ - ระบบลงคะแนนสิ่งประดิษฐ์</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Kanit -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Kanit', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar.active {
            left: 0;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        
        .sidebar-menu li {
            margin: 5px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(255,255,255,0.1);
            padding-left: 30px;
        }
        
        .sidebar-menu a.active {
            background-color: rgba(255,255,255,0.2);
            border-right: 3px solid white;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 0;
            padding: 0;
            transition: all 0.3s ease;
        }
        
        .main-content.shifted {
            margin-left: 250px;
        }
        
        .header {
            background: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .menu-toggle {
            background: none;
            border: none;
            font-size: 20px;
            color: #333;
            cursor: pointer;
        }
        
        .content-wrapper {
            padding: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            background: #f8f9fa;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }
        
        .action-btn:hover {
            background: #e9ecef;
            transform: translateX(5px);
            color: #333;
        }
        
        .action-btn i {
            margin-right: 15px;
            font-size: 18px;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .main-content.shifted {
                margin-left: 0;
            }
            
            .content-wrapper {
                padding: 15px;
            }
            
            .sidebar {
                width: 280px;
                left: -280px;
            }
        }
        
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        
        .overlay.active {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Overlay สำหรับมือถือ -->
    <div class="overlay" id="overlay"></div>
    
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-cogs"></i><br>ผู้ดูแลระบบ</h4>
            <small><?php echo htmlspecialchars($admin_name); ?></small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> แดชบอร์ด</a></li>
            <li><a href="admin_competitions.php"><i class="fas fa-trophy"></i> จัดการการแข่งขัน</a></li>
            <li><a href="admin_categories.php"><i class="fas fa-list"></i> ประเภทสิ่งประดิษฐ์</a></li>
            <li><a href="admin_inventions.php"><i class="fas fa-lightbulb"></i> รายการสิ่งประดิษฐ์</a></li>
            <li><a href="admin_users.php"><i class="fas fa-users"></i> จัดการผู้ใช้งาน</a></li>
            <li><a href="admin_scoring.php"><i class="fas fa-star"></i> จัดการการลงคะแนน</a></li>
            <li><a href="admin_reports.php"><i class="fas fa-chart-bar"></i> รายงานผล</a></li>
            <li><a href="admin_settings.php"><i class="fas fa-cog"></i> ตั้งค่าระบบ</a></li>
            <li><a href="admin_logs.php"><i class="fas fa-history"></i> บันทึกการใช้งาน</a></li>
            <li><a href="admin_profile.php"><i class="fas fa-user"></i> ข้อมูลส่วนตัว</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <div class="header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="menu-toggle" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 ms-3">แดชบอร์ดผู้ดูแลระบบ</h5>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3">วันที่: <?php echo date('d/m/Y H:i:s'); ?></span>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($admin_name); ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="admin_profile.php"><i class="fas fa-user"></i> ข้อมูลส่วนตัว</a></li>
                            <li><a class="dropdown-item" href="admin_settings.php"><i class="fas fa-cog"></i> ตั้งค่า</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- สถิติรวม -->
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="stat-number">5</div>
                        <div class="stat-label">การแข่งขันทั้งหมด</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="stat-number">248</div>
                        <div class="stat-label">สิ่งประดิษฐ์ทั้งหมด</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number">89</div>
                        <div class="stat-label">กรรมการทั้งหมด</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number">75%</div>
                        <div class="stat-label">ความคืบหน้าการลงคะแนน</div>
                    </div>
                </div>
            </div>

            <!-- การดำเนินการด่วน -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-4"><i class="fas fa-chart-line"></i> สถิติการใช้งานระบบ</h5>
                        <canvas id="usageChart" height="100"></canvas>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="quick-actions">
                        <h5 class="mb-4"><i class="fas fa-bolt"></i> การดำเนินการด่วน</h5>
                        <a href="admin_competitions.php?action=add" class="action-btn">
                            <i class="fas fa-plus"></i>
                            <span>เพิ่มการแข่งขันใหม่</span>
                        </a>
                        <a href="admin_users.php?action=add" class="action-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>เพิ่มผู้ใช้งาน</span>
                        </a>
                        <a href="admin_scoring.php" class="action-btn">
                            <i class="fas fa-star"></i>
                            <span>จัดการการลงคะแนน</span>
                        </a>
                        <a href="admin_reports.php" class="action-btn">
                            <i class="fas fa-file-alt"></i>
                            <span>ดูรายงานผล</span>
                        </a>
                        <a href="admin_backup.php" class="action-btn">
                            <i class="fas fa-download"></i>
                            <span>สำรองข้อมูล</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- การแข่งขันล่าสุด -->
            <div class="row">
                <div class="col-12">
                    <div class="chart-container">
                        <h5 class="mb-4"><i class="fas fa-clock"></i> การแข่งขันล่าสุด</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ชื่อการแข่งขัน</th>
                                        <th>ระดับ</th>
                                        <th>จำนวนสิ่งประดิษฐ์</th>
                                        <th>สถานะ</th>
                                        <th>ความคืบหน้า</th>
                                        <th>การดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>การแข่งขัน สอจ. ระดับชาติ 2567</td>
                                        <td><span class="badge bg-primary">ระดับชาติ</span></td>
                                        <td>156</td>
                                        <td><span class="badge bg-warning">กำลังลงคะแนน</span></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" style="width: 75%;">75%</div>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">จัดการ</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>การแข่งขัน สอจ. ระดับภาคใต้</td>
                                        <td><span class="badge bg-info">ระดับภาค</span></td>
                                        <td>92</td>
                                        <td><span class="badge bg-success">เสร็จสิ้น</span></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" style="width: 100%;">100%</div>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success">ดูผล</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Menu Toggle
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const overlay = document.getElementById('overlay');

        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            if (window.innerWidth > 768) {
                mainContent.classList.toggle('shifted');
            } else {
                overlay.classList.toggle('active');
            }
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Responsive handling
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                overlay.classList.remove('active');
            } else {
                mainContent.classList.remove('shifted');
            }
        });

        // Chart
        const ctx = document.getElementById('usageChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.'],
                datasets: [{
                    label: 'การเข้าใช้งาน',
                    data: [65, 78, 90, 81, 56, 95],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>