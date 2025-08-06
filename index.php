<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบ Invention Vote - ผู้ดูแลระบบส่วนกลาง</title>
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
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        
        .stats-card {
            text-align: center;
            padding: 30px 20px;
        }
        
        .stats-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
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
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .badge {
            font-size: 0.9em;
            padding: 8px 12px;
            border-radius: 20px;
        }
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
                    <a class="nav-link active" href="#dashboard">
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
                    <a class="nav-link" href="#reports">
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
                    <h1 class="h3 mb-3">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i>
                        หน้าหลัก - ภาพรวมระบบ
                    </h1>
                    <p class="text-muted mb-0">ติดตามและจัดการระบบการให้คะแนนสิ่งประดิษฐ์ทั้งหมด</p>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="stats-number">12</div>
                                <div>รายการแข่งขันทั้งหมด</div>
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
                                <div>กรรมการทั้งหมด</div>
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
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-bolt me-2"></i>การดำเนินการด่วน
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <button class="btn btn-primary w-100 btn-lg">
                                            <i class="fas fa-plus me-2"></i>สร้างรายการแข่งขันใหม่
                                        </button>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <button class="btn btn-success w-100 btn-lg">
                                            <i class="fas fa-user-plus me-2"></i>เพิ่มบัญชีผู้ใช้
                                        </button>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <button class="btn btn-info w-100 btn-lg">
                                            <i class="fas fa-download me-2"></i>ดาวน์โหลดรายงาน
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Competitions -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-trophy me-2"></i>รายการแข่งขันล่าสุด
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ชื่อรายการ</th>
                                                <th>ระดับ</th>
                                                <th>สถานะ</th>
                                                <th>จำนวนผลงาน</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>การแข่งขันสิ่งประดิษฐ์ ปี 2568</td>
                                                <td><span class="badge bg-primary">ระดับชาติ</span></td>
                                                <td><span class="badge bg-success">เปิดรับสมัคร</span></td>
                                                <td>45</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">ดูรายละเอียด</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>การแข่งขันระดับภาคเหนือ</td>
                                                <td><span class="badge bg-warning text-dark">ระดับภาค</span></td>
                                                <td><span class="badge bg-info">กำลังให้คะแนน</span></td>
                                                <td>32</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">ดูรายละเอียด</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>การแข่งขันระดับจังหวัดเชียงใหม่</td>
                                                <td><span class="badge bg-secondary">ระดับจังหวัด</span></td>
                                                <td><span class="badge bg-danger">ปิดรับสมัคร</span></td>
                                                <td>28</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">ดูรายละเอียด</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- System Alerts -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-bell me-2"></i>การแจ้งเตือนระบบ
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning" role="alert">
                                    <strong>แจ้งเตือน!</strong> รายการแข่งขันระดับภาคเหนือใกล้ครบกำหนดให้คะแนนแล้ว
                                </div>
                                <div class="alert alert-info" role="alert">
                                    <strong>ข้อมูล:</strong> มีการเข้าใช้งานผิดปกติจาก IP: 192.168.1.100
                                </div>
                                <div class="alert alert-success" role="alert">
                                    <strong>สำเร็จ:</strong> ระบบได้รับการอัพเดทเมื่อ 2 ชั่วโมงที่แล้ว
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-server me-2"></i>สถานะระบบ
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <i class="fas fa-database text-success" style="font-size: 2rem;"></i>
                                            <h6 class="mt-2">ฐานข้อมูล</h6>
                                            <span class="badge bg-success">ปกติ</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <i class="fas fa-cloud text-success" style="font-size: 2rem;"></i>
                                            <h6 class="mt-2">เซิร์ฟเวอร์</h6>
                                            <span class="badge bg-success">ปกติ</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <i class="fas fa-shield-alt text-warning" style="font-size: 2rem;"></i>
                                            <h6 class="mt-2">ความปลอดภัย</h6>
                                            <span class="badge bg-warning text-dark">เฝ้าระวัง</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <i class="fas fa-sync-alt text-success" style="font-size: 2rem;"></i>
                                            <h6 class="mt-2">การซิงค์</h6>
                                            <span class="badge bg-success">ปกติ</span>
                                        </div>
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
    <script>
        // Auto refresh statistics every 30 seconds
        setInterval(function() {
            // Add AJAX call to refresh statistics
            console.log('Refreshing statistics...');
        }, 30000);

        // Sidebar navigation
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Here you would implement page loading logic
                console.log('Navigate to:', this.getAttribute('href'));
            });
        });
    </script>
</body>
</html>