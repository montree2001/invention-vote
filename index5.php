<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานภาพรวม - ระบบ Invention Vote</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Kanit', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        
        .sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            border-right: 2px solid #e9ecef;
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: #495057;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
            color: #007bff;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: white;
            box-shadow: 0 4px 15px rgba(0,123,255,0.3);
        }
        
        .main-content {
            padding: 30px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        }
        
        .header-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .navbar {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: none;
            font-weight: 600;
        }
        
        .badge {
            font-size: 0.9em;
            padding: 8px 12px;
            border-radius: 20px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 15px rgba(0,123,255,0.2);
        }
        
        .stats-card {
            text-align: center;
            padding: 25px 20px;
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .progress-container {
            margin: 15px 0;
        }
        
        .progress {
            height: 8px;
            border-radius: 10px;
        }
        
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .report-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .metric-box {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 10px 0;
        }
        
        .metric-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #007bff;
        }
        
        .metric-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .trend-up {
            color: #28a745;
        }
        
        .trend-down {
            color: #dc3545;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
        }
        
        .level-badge {
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 500;
        }
        
        .level-national { background-color: #007bff; color: white; }
        .level-regional { background-color: #ffc107; color: #212529; }
        .level-provincial { background-color: #6c757d; color: white; }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#" style="color: #007bff;">
                <i class="fas fa-cogs me-2"></i>ระบบ Invention Vote
            </a>
            <div class="d-flex align-items-center">
                <span class="me-3">ผู้ดูแลระบบส่วนกลาง</span>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>Admin
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-edit me-2"></i>แก้ไขข้อมูลส่วนตัว</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-key me-2"></i>เปลี่ยนรหัสผ่าน</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid" style="margin-top: 76px;">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <div class="nav flex-column nav-pills">
                    <a class="nav-link" href="#dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>หน้าหลัก
                    </a>
                    <a class="nav-link" href="#competitions">
                        <i class="fas fa-trophy me-2"></i>จัดการรายการแข่งขัน
                    </a>
                    <a class="nav-link" href="#users">
                        <i class="fas fa-users me-2"></i>จัดการบัญชีผู้ใช้
                    </a>
                    <a class="nav-link" href="#criteria">
                        <i class="fas fa-list-alt me-2"></i>เกณฑ์การให้คะแนน
                    </a>
                    <a class="nav-link active" href="#reports">
                        <i class="fas fa-chart-bar me-2"></i>รายงานภาพรวม
                    </a>
                    <a class="nav-link" href="#settings">
                        <i class="fas fa-cog me-2"></i>ตั้งค่าระบบ
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="header-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-3">
                                <i class="fas fa-chart-bar text-primary me-2"></i>
                                รายงานภาพรวม
                            </h1>
                            <p class="text-muted mb-0">ติดตามและวิเคราะห์ข้อมูลการดำเนินงานของระบบทั้งหมด</p>
                        </div>
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-primary" onclick="exportReport()">
                                <i class="fas fa-download me-2"></i>ส่งออกรายงาน
                            </button>
                            <button class="btn btn-primary" onclick="refreshData()">
                                <i class="fas fa-sync-alt me-2"></i>อัพเดทข้อมูล
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">ช่วงเวลา</label>
                            <select class="form-select" id="timeRange">
                                <option value="7">7 วันที่ผ่านมา</option>
                                <option value="30" selected>30 วันที่ผ่านมา</option>
                                <option value="90">90 วันที่ผ่านมา</option>
                                <option value="365">1 ปีที่ผ่านมา</option>
                                <option value="custom">กำหนดเอง</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">ระดับการแข่งขัน</label>
                            <select class="form-select" id="competitionLevel">
                                <option value="">ทุกระดับ</option>
                                <option value="national">ระดับชาติ</option>
                                <option value="regional">ระดับภาค</option>
                                <option value="provincial">ระดับจังหวัด</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">ประเภทสิ่งประดิษฐ์</label>
                            <select class="form-select" id="inventionType">
                                <option value="">ทุกประเภท</option>
                                <option value="science">วิทยาศาสตร์</option>
                                <option value="technology">เทคโนโลยี</option>
                                <option value="innovation">นวัตกรรม</option>
                                <option value="environment">สิ่งแวดล้อม</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                                <i class="fas fa-filter me-1"></i>ใช้ตัวกรอง
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="stats-number">12</div>
                                <div>รายการแข่งขันทั้งหมด</div>
                                <small class="mt-2 d-block">
                                    <i class="fas fa-arrow-up trend-up me-1"></i>
                                    เพิ่มขึ้น 20% จากเดือนก่อน
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #007bff, #6610f2);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <div class="stats-number">248</div>
                                <div>สิ่งประดิษฐ์ทั้งหมด</div>
                                <small class="mt-2 d-block">
                                    <i class="fas fa-arrow-up trend-up me-1"></i>
                                    เพิ่มขึ้น 15% จากเดือนก่อน
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #fd7e14, #e83e8c);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stats-number">156</div>
                                <div>กรรมการที่ใช้งาน</div>
                                <small class="mt-2 d-block">
                                    <i class="fas fa-arrow-up trend-up me-1"></i>
                                    เพิ่มขึ้น 8% จากเดือนก่อน
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #dc3545, #fd7e14);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stats-number">89%</div>
                                <div>ความคืบหน้าการให้คะแนน</div>
                                <small class="mt-2 d-block">
                                    <i class="fas fa-arrow-up trend-up me-1"></i>
                                    เพิ่มขึ้น 12% จากเดือนก่อน
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <!-- Competition Progress Chart -->
                    <div class="col-md-8">
                        <div class="report-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-area text-primary me-2"></i>ความคืบหน้าการแข่งขัน
                                </h5>
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="chartType" id="line" checked>
                                    <label class="btn btn-outline-primary" for="line">เส้น</label>
                                    <input type="radio" class="btn-check" name="chartType" id="bar">
                                    <label class="btn btn-outline-primary" for="bar">แท่ง</label>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="progressChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Invention Types Pie Chart -->
                    <div class="col-md-4">
                        <div class="report-card">
                            <h5 class="mb-3">
                                <i class="fas fa-chart-pie text-primary me-2"></i>ประเภทสิ่งประดิษฐ์
                            </h5>
                            <div class="chart-container">
                                <canvas id="inventionTypesChart" style="max-height: 250px;"></canvas>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><i class="fas fa-circle text-primary me-2"></i>วิทยาศาสตร์</span>
                                    <span class="fw-bold">35%</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><i class="fas fa-circle text-success me-2"></i>เทคโนโลยี</span>
                                    <span class="fw-bold">28%</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><i class="fas fa-circle text-warning me-2"></i>นวัตกรรม</span>
                                    <span class="fw-bold">22%</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-circle text-info me-2"></i>สิ่งแวดล้อม</span>
                                    <span class="fw-bold">15%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Competition Details and Activities -->
                <div class="row">
                    <!-- Competition Status Table -->
                    <div class="col-md-8">
                        <div class="report-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-list text-primary me-2"></i>สถานะรายการแข่งขัน
                                </h5>
                                <button class="btn btn-outline-primary btn-sm" onclick="viewAllCompetitions()">
                                    ดูทั้งหมด <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>รายการแข่งขัน</th>
                                            <th>ระดับ</th>
                                            <th>ผลงาน</th>
                                            <th>ความคืบหน้า</th>
                                            <th>สถานะ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="fw-bold">การแข่งขันสิ่งประดิษฐ์ ปี 2568</div>
                                                <small class="text-muted">สร้างเมื่อ 15 ม.ค. 2568</small>
                                            </td>
                                            <td><span class="level-badge level-national">ระดับชาติ</span></td>
                                            <td class="text-center">45</td>
                                            <td>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: 60%"></div>
                                                </div>
                                                <small class="text-muted">60%</small>
                                            </td>
                                            <td><span class="badge bg-success">เปิดรับสมัคร</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-bold">การแข่งขันระดับภาคเหนือ</div>
                                                <small class="text-muted">สร้างเมื่อ 10 ม.ค. 2568</small>
                                            </td>
                                            <td><span class="level-badge level-regional">ระดับภาค</span></td>
                                            <td class="text-center">32</td>
                                            <td>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-info" style="width: 85%"></div>
                                                </div>
                                                <small class="text-muted">85%</small>
                                            </td>
                                            <td><span class="badge bg-info">กำลังให้คะแนน</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-bold">การแข่งขันระดับจังหวัดเชียงใหม่</div>
                                                <small class="text-muted">สร้างเมื่อ 5 ม.ค. 2568</small>
                                            </td>
                                            <td><span class="level-badge level-provincial">ระดับจังหวัด</span></td>
                                            <td class="text-center">28</td>
                                            <td>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                                </div>
                                                <small class="text-muted">100%</small>
                                            </td>
                                            <td><span class="badge bg-success">เสร็จสิ้น</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activities -->
                    <div class="col-md-4">
                        <div class="report-card">
                            <h5 class="mb-3">
                                <i class="fas fa-clock text-primary me-2"></i>กิจกรรมล่าสุด
                            </h5>
                            <div class="activity-item">
                                <div class="activity-icon" style="background: linear-gradient(135deg, #28a745, #20c997);">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">สร้างรายการแข่งขันใหม่</div>
                                    <small class="text-muted">การแข่งขันสิ่งประดิษฐ์ ปี 2568</small>
                                    <br><small class="text-muted">2 ชั่วโมงที่แล้ว</small>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon" style="background: linear-gradient(135deg, #007bff, #6610f2);">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">เพิ่มกรรมการใหม่</div>
                                    <small class="text-muted">อ.สมชาย ใจดี - มหาวิทยาลัยเชียงใหม่</small>
                                    <br><small class="text-muted">4 ชั่วโมงที่แล้ว</small>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon" style="background: linear-gradient(135deg, #fd7e14, #e83e8c);">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">รับรองผลการแข่งขัน</div>
                                    <small class="text-muted">การแข่งขันระดับจังหวัดเชียงใหม่</small>
                                    <br><small class="text-muted">6 ชั่วโมงที่แล้ว</small>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon" style="background: linear-gradient(135deg, #dc3545, #fd7e14);">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">อัพเดทเกณฑ์การให้คะแนน</div>
                                    <small class="text-muted">เกณฑ์ประเภทวิทยาศาสตร์</small>
                                    <br><small class="text-muted">1 วันที่แล้ว</small>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button class="btn btn-outline-primary btn-sm" onclick="viewAllActivities()">
                                    ดูกิจกรรมทั้งหมด
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="report-card">
                            <h5 class="mb-4">
                                <i class="fas fa-tachometer-alt text-primary me-2"></i>ตัวชี้วัดประสิทธิภาพ
                            </h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="metric-box">
                                        <div class="metric-value">92%</div>
                                        <div class="metric-label">อัตราการใช้งานของกรรมการ</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-box">
                                        <div class="metric-value">4.2</div>
                                        <div class="metric-label">วันเฉลี่ยในการให้คะแนน</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-box">
                                        <div class="metric-value">98%</div>
                                        <div class="metric-label">ความพึงพอใจผู้ใช้งาน</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-box">
                                        <div class="metric-value">99.8%</div>
                                        <div class="metric-label">เวลาทำงานของระบบ</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });
        
        function initializeCharts() {
            // Progress Chart
            const progressCtx = document.getElementById('progressChart').getContext('2d');
            new Chart(progressCtx, {
                type: 'line',
                data: {
                    labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.'],
                    datasets: [
                        {
                            label: 'ระดับชาติ',
                            data: [2, 3, 4, 3, 4, 5, 4],
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'ระดับภาค',
                            data: [3, 4, 3, 5, 4, 6, 5],
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'ระดับจังหวัด',
                            data: [5, 4, 6, 4, 7, 5, 6],
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Invention Types Pie Chart
            const inventionCtx = document.getElementById('inventionTypesChart').getContext('2d');
            new Chart(inventionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['วิทยาศาสตร์', 'เทคโนโลยี', 'นวัตกรรม', 'สิ่งแวดล้อม'],
                    datasets: [{
                        data: [35, 28, 22, 15],
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#17a2b8'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
        
        // Filter and control functions
        function applyFilters() {
            const timeRange = document.getElementById('timeRange').value;
            const competitionLevel = document.getElementById('competitionLevel').value;
            const inventionType = document.getElementById('inventionType').value;
            
            console.log('Applying filters:', { timeRange, competitionLevel, inventionType });
            
            // Show loading and refresh data
            showLoading();
            setTimeout(() => {
                hideLoading();
                alert('ข้อมูลถูกกรองแล้ว!');
            }, 1000);
        }
        
        function refreshData() {
            console.log('Refreshing data...');
            showLoading();
            setTimeout(() => {
                hideLoading();
                alert('อัพเดทข้อมูลสำเร็จ!');
            }, 2000);
        }
        
        function exportReport() {
            console.log('Exporting report...');
            alert('กำลังเตรียมรายงาน... จะส่งไฟล์ให้ทางอีเมลในอีกสักครู่');
        }
        
        function viewAllCompetitions() {
            console.log('Navigate to competitions page');
            // Navigate to competitions management page
        }
        
        function viewAllActivities() {
            console.log('Navigate to activities log');
            // Show activities log modal or page
        }
        
        function showLoading() {
            // Show loading spinner
            document.body.style.cursor = 'wait';
        }
        
        function hideLoading() {
            // Hide loading spinner
            document.body.style.cursor = 'default';
        }
        
        // Auto refresh data every 5 minutes
        setInterval(function() {
            console.log('Auto refreshing data...');
            // Silently refresh data in background
        }, 300000);
        
        // Chart type toggle
        document.querySelectorAll('input[name="chartType"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.id === 'bar') {
                    // Switch to bar chart
                    console.log('Switch to bar chart');
                } else {
                    // Switch to line chart
                    console.log('Switch to line chart');
                }
            });
        });
        
        // Sidebar navigation
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                console.log('Navigate to:', this.getAttribute('href'));
            });
        });
    </script>
</body>
</html>