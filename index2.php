<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการรายการแข่งขัน - ระบบ Invention Vote</title>
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
        
        .search-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .competition-card {
            border-left: 5px solid #007bff;
            transition: all 0.3s ease;
        }
        
        .competition-card:hover {
            border-left-color: #0056b3;
            transform: scale(1.02);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
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
                    <a class="nav-link" href="#dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>หน้าหลัก
                    </a>
                    <a class="nav-link active" href="#competitions">
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-3">
                                <i class="fas fa-trophy text-primary me-2"></i>
                                จัดการรายการแข่งขัน
                            </h1>
                            <p class="text-muted mb-0">สร้าง แก้ไข และจัดการรายการแข่งขันสิ่งประดิษฐ์ทุกระดับ</p>
                        </div>
                        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addCompetitionModal">
                            <i class="fas fa-plus me-2"></i>สร้างรายการแข่งขันใหม่
                        </button>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="search-box">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ค้นหารายการแข่งขัน</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="ชื่อรายการแข่งขัน..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">ระดับการแข่งขัน</label>
                            <select class="form-select" id="levelFilter">
                                <option value="">ทุกระดับ</option>
                                <option value="national">ระดับชาติ</option>
                                <option value="regional">ระดับภาค</option>
                                <option value="provincial">ระดับจังหวัด</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">สถานะ</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">ทุกสถานะ</option>
                                <option value="draft">ร่าง</option>
                                <option value="open">เปิดรับสมัคร</option>
                                <option value="scoring">กำลังให้คะแนน</option>
                                <option value="completed">เสร็จสิ้น</option>
                                <option value="closed">ปิดการแข่งขัน</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button class="btn btn-outline-primary w-100" onclick="filterData()">
                                <i class="fas fa-filter me-1"></i>กรอง
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Competitions List -->
                <div class="row" id="competitionsList">
                    <!-- Competition Card 1 -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card competition-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title text-primary">การแข่งขันสิ่งประดิษฐ์ ปี 2568</h5>
                                    <span class="badge bg-primary">ระดับชาติ</span>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>วันที่สร้าง: 15 ม.ค. 2568<br>
                                        <i class="fas fa-user me-1"></i>ผู้ดูแล: อ.สมชาย ใจดี<br>
                                        <i class="fas fa-lightbulb me-1"></i>ผลงาน: 45 ชิ้น
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <span class="badge bg-success mb-1">เปิดรับสมัคร</span>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-success" style="width: 60%"></div>
                                    </div>
                                    <small class="text-muted">ความคืบหน้า 60%</small>
                                </div>
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewCompetition(1)">
                                        <i class="fas fa-eye me-1"></i>ดู
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="editCompetition(1)">
                                        <i class="fas fa-edit me-1"></i>แก้ไข
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="manageCompetition(1)">
                                        <i class="fas fa-cogs me-1"></i>จัดการ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Competition Card 2 -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card competition-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title text-primary">การแข่งขันระดับภาคเหนือ</h5>
                                    <span class="badge bg-warning text-dark">ระดับภาค</span>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>วันที่สร้าง: 10 ม.ค. 2568<br>
                                        <i class="fas fa-user me-1"></i>ผู้ดูแล: อ.สมหญิง จิตดี<br>
                                        <i class="fas fa-lightbulb me-1"></i>ผลงาน: 32 ชิ้น
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <span class="badge bg-info mb-1">กำลังให้คะแนน</span>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-info" style="width: 85%"></div>
                                    </div>
                                    <small class="text-muted">ความคืบหน้า 85%</small>
                                </div>
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewCompetition(2)">
                                        <i class="fas fa-eye me-1"></i>ดู
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="editCompetition(2)">
                                        <i class="fas fa-edit me-1"></i>แก้ไข
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="manageCompetition(2)">
                                        <i class="fas fa-cogs me-1"></i>จัดการ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Competition Card 3 -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card competition-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title text-primary">การแข่งขันระดับจังหวัดเชียงใหม่</h5>
                                    <span class="badge bg-secondary">ระดับจังหวัด</span>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>วันที่สร้าง: 5 ม.ค. 2568<br>
                                        <i class="fas fa-user me-1"></i>ผู้ดูแล: อ.วิเชียร ศรีสุข<br>
                                        <i class="fas fa-lightbulb me-1"></i>ผลงาน: 28 ชิ้น
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <span class="badge bg-success mb-1">เสร็จสิ้น</span>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                    </div>
                                    <small class="text-muted">ความคืบหน้า 100%</small>
                                </div>
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewCompetition(3)">
                                        <i class="fas fa-eye me-1"></i>ดู
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="viewResults(3)">
                                        <i class="fas fa-trophy me-1"></i>ผลการแข่งขัน
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="downloadReport(3)">
                                        <i class="fas fa-download me-1"></i>รายงาน
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add more competition cards as needed -->
                </div>

                <!-- Pagination -->
                <nav aria-label="หน้า">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">ก่อนหน้า</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">ถัดไป</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Add Competition Modal -->
    <div class="modal fade" id="addCompetitionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>สร้างรายการแข่งขันใหม่
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addCompetitionForm">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">ชื่อรายการแข่งขัน <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="ระบุชื่อรายการแข่งขัน" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">ระดับการแข่งขัน <span class="text-danger">*</span></label>
                                <select class="form-select" required>
                                    <option value="">เลือกระดับ</option>
                                    <option value="national">ระดับชาติ</option>
                                    <option value="regional">ระดับภาค</option>
                                    <option value="provincial">ระดับจังหวัด</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">วันที่เริ่มรับสมัคร</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">วันที่ปิดรับสมัคร</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">วันที่เริ่มให้คะแนน</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">วันที่สิ้นสุดการให้คะแนน</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">ผู้ดูแลระบบ <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="">เลือกผู้ดูแลระบบ</option>
                                <option value="1">อ.สมชาย ใจดี</option>
                                <option value="2">อ.สมหญิง จิตดี</option>
                                <option value="3">อ.วิเชียร ศรีสุข</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">คำอธิบายรายการแข่งขัน</label>
                            <textarea class="form-control" rows="4" placeholder="รายละเอียดของรายการแข่งขัน..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">สถานะเริ่มต้น</label>
                                <select class="form-select">
                                    <option value="draft">ร่าง</option>
                                    <option value="open">เปิดรับสมัคร</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">จำนวนผลงานสูงสุด</label>
                                <input type="number" class="form-control" placeholder="ไม่จำกัด" min="1">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="saveCompetition()">
                        <i class="fas fa-save me-1"></i>บันทึกรายการแข่งขัน
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter function
        function filterData() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const level = document.getElementById('levelFilter').value;
            const status = document.getElementById('statusFilter').value;
            
            // Here you would implement filtering logic
            console.log('Filtering with:', { search, level, status });
        }
        
        // Competition management functions
        function viewCompetition(id) {
            console.log('View competition:', id);
            // Implement view competition logic
        }
        
        function editCompetition(id) {
            console.log('Edit competition:', id);
            // Implement edit competition logic
        }
        
        function manageCompetition(id) {
            console.log('Manage competition:', id);
            // Implement competition management logic
        }
        
        function viewResults(id) {
            console.log('View results for competition:', id);
            // Implement view results logic
        }
        
        function downloadReport(id) {
            console.log('Download report for competition:', id);
            // Implement download report logic
        }
        
        function saveCompetition() {
            // Validate form
            const form = document.getElementById('addCompetitionForm');
            if (form.checkValidity()) {
                // Here you would implement save logic
                alert('บันทึกรายการแข่งขันสำเร็จ!');
                bootstrap.Modal.getInstance(document.getElementById('addCompetitionModal')).hide();
                // Refresh the competitions list
            } else {
                form.reportValidity();
            }
        }
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                filterData();
            }
        });
        
        // Sidebar navigation
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                console.log('Navigate to:', this.getAttribute('href'));
            });
        });
    </script>
</body>
</html>