<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการบัญชีผู้ใช้ - ระบบ Invention Vote</title>
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
        
        .modal-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff, #6610f2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
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
        
        .tab-content {
            padding: 20px 0;
        }
        
        .nav-tabs .nav-link {
            border: none;
            border-radius: 10px 10px 0 0;
            margin-right: 5px;
            color: #495057;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
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
                    <a class="nav-link" href="#competitions">
                        <i class="fas fa-trophy me-2"></i>จัดการรายการแข่งขัน
                    </a>
                    <a class="nav-link active" href="#users">
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
                                <i class="fas fa-users text-primary me-2"></i>
                                จัดการบัญชีผู้ใช้
                            </h1>
                            <p class="text-muted mb-0">สร้าง แก้ไข และจัดการบัญชีผู้ใช้ทุกประเภทในระบบ</p>
                        </div>
                        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้ใหม่
                        </button>
                    </div>
                </div>

                <!-- User Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #007bff, #6610f2);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                                <div class="stats-number">8</div>
                                <div>ผู้ดูแลระบบ</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="stats-number">24</div>
                                <div>ประธานกรรมการ</div>
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
                                <div>กรรมการ</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #dc3545, #fd7e14);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="stats-number">175</div>
                                <div>ใช้งานอยู่</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="search-box">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ค้นหาผู้ใช้</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="ชื่อ, อีเมล, หรือรหัสผู้ใช้..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">ประเภทผู้ใช้</label>
                            <select class="form-select" id="roleFilter">
                                <option value="">ทุกประเภท</option>
                                <option value="admin">ผู้ดูแลระบบ</option>
                                <option value="chairman">ประธานกรรมการ</option>
                                <option value="judge">กรรมการ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">สถานะ</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">ทุกสถานะ</option>
                                <option value="active">ใช้งานอยู่</option>
                                <option value="inactive">ไม่ใช้งาน</option>
                                <option value="suspended">ถูกระงับ</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button class="btn btn-outline-primary w-100" onclick="filterUsers()">
                                <i class="fas fa-filter me-1"></i>กรอง
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>รายชื่อผู้ใช้ทั้งหมด
                            </h5>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-light btn-sm" onclick="exportUsers()">
                                    <i class="fas fa-download me-1"></i>ส่งออก
                                </button>
                                <button class="btn btn-outline-light btn-sm" onclick="importUsers()">
                                    <i class="fas fa-upload me-1"></i>นำเข้า
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>ผู้ใช้</th>
                                        <th>ประเภท</th>
                                        <th>สถานะ</th>
                                        <th>เข้าใช้ล่าสุด</th>
                                        <th>การดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">SC</div>
                                                <div>
                                                    <div class="fw-bold">อ.สมชาย ใจดี</div>
                                                    <small class="text-muted">somchai@university.ac.th</small>
                                                    <br><small class="text-muted">มหาวิทยาลัยเชียงใหม่</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary">ผู้ดูแลระบบ</span></td>
                                        <td><span class="badge bg-success">ใช้งานอยู่</span></td>
                                        <td>
                                            <small>15 ม.ค. 2568<br>14:30 น.</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-primary btn-sm" onclick="viewUser(1)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-warning btn-sm" onclick="editUser(1)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-info btn-sm" onclick="resetPassword(1)">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="suspendUser(1)">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3" style="background: linear-gradient(135deg, #28a745, #20c997);">SH</div>
                                                <div>
                                                    <div class="fw-bold">อ.สมหญิง จิตดี</div>
                                                    <small class="text-muted">somying@university.ac.th</small>
                                                    <br><small class="text-muted">มหาวิทยาลัยเทคโนโลยีสุรนารี</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success">ประธานกรรมการ</span></td>
                                        <td><span class="badge bg-success">ใช้งานอยู่</span></td>
                                        <td>
                                            <small>14 ม.ค. 2568<br>16:45 น.</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-primary btn-sm" onclick="viewUser(2)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-warning btn-sm" onclick="editUser(2)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-info btn-sm" onclick="resetPassword(2)">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="suspendUser(2)">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3" style="background: linear-gradient(135deg, #fd7e14, #e83e8c);">WS</div>
                                                <div>
                                                    <div class="fw-bold">อ.วิเชียร ศรีสุข</div>
                                                    <small class="text-muted">wichian@university.ac.th</small>
                                                    <br><small class="text-muted">มหาวิทยาลัยเกษตรศาสตร์</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-warning text-dark">กรรมการ</span></td>
                                        <td><span class="badge bg-secondary">ไม่ใช้งาน</span></td>
                                        <td>
                                            <small>10 ม.ค. 2568<br>09:15 น.</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-primary btn-sm" onclick="viewUser(3)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-warning btn-sm" onclick="editUser(3)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-info btn-sm" onclick="resetPassword(3)">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <button class="btn btn-outline-success btn-sm" onclick="activateUser(3)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3" style="background: linear-gradient(135deg, #dc3545, #fd7e14);">PR</div>
                                                <div>
                                                    <div class="fw-bold">ดร.ประเสริฐ รุ่งเรือง</div>
                                                    <small class="text-muted">prasert@university.ac.th</small>
                                                    <br><small class="text-muted">จุฬาลงกรณ์มหาวิทยาลัย</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-warning text-dark">กรรมการ</span></td>
                                        <td><span class="badge bg-danger">ถูกระงับ</span></td>
                                        <td>
                                            <small>8 ม.ค. 2568<br>11:20 น.</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-primary btn-sm" onclick="viewUser(4)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-warning btn-sm" onclick="editUser(4)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-success btn-sm" onclick="unsuspendUser(4)">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="deleteUser(4)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <nav aria-label="หน้า" class="mt-4">
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้ใหม่
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ชื่อ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="ชื่อจริง" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">นามสกุล <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="นามสกุล" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">อีเมล <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" placeholder="email@domain.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">เบอร์โทรศัพท์</label>
                                <input type="tel" class="form-control" placeholder="0xx-xxx-xxxx">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ประเภทผู้ใช้ <span class="text-danger">*</span></label>
                                <select class="form-select" required>
                                    <option value="">เลือกประเภท</option>
                                    <option value="admin">ผู้ดูแลระบบ</option>
                                    <option value="chairman">ประธานกรรมการ</option>
                                    <option value="judge">กรรมการ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">สถานะเริ่มต้น</label>
                                <select class="form-select">
                                    <option value="active">ใช้งานอยู่</option>
                                    <option value="inactive">ไม่ใช้งาน</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">สถานศึกษา/หน่วยงาน</label>
                            <input type="text" class="form-control" placeholder="ชื่อสถานศึกษาหรือหน่วยงาน">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">รหัสผ่าน <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" placeholder="รหัสผ่าน" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" placeholder="ยืนยันรหัสผ่าน" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">หมายเหตุ</label>
                            <textarea class="form-control" rows="3" placeholder="หมายเหตุเพิ่มเติม..."></textarea>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sendEmail">
                            <label class="form-check-label" for="sendEmail">
                                ส่งอีเมลแจ้งข้อมูลการเข้าใช้งานให้ผู้ใช้
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">
                        <i class="fas fa-save me-1"></i>บันทึกข้อมูล
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter function
        function filterUsers() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const role = document.getElementById('roleFilter').value;
            const status = document.getElementById('statusFilter').value;
            
            console.log('Filtering users with:', { search, role, status });
        }
        
        // User management functions
        function viewUser(id) {
            console.log('View user:', id);
        }
        
        function editUser(id) {
            console.log('Edit user:', id);
        }
        
        function resetPassword(id) {
            if (confirm('ต้องการรีเซ็ตรหัสผ่านของผู้ใช้นี้หรือไม่?')) {
                console.log('Reset password for user:', id);
                alert('รีเซ็ตรหัสผ่านสำเร็จ!');
            }
        }
        
        function suspendUser(id) {
            if (confirm('ต้องการระงับการใช้งานของผู้ใช้นี้หรือไม่?')) {
                console.log('Suspend user:', id);
                alert('ระงับการใช้งานสำเร็จ!');
            }
        }
        
        function unsuspendUser(id) {
            if (confirm('ต้องการยกเลิกการระงับผู้ใช้นี้หรือไม่?')) {
                console.log('Unsuspend user:', id);
                alert('ยกเลิกการระงับสำเร็จ!');
            }
        }
        
        function activateUser(id) {
            if (confirm('ต้องการเปิดใช้งานผู้ใช้นี้หรือไม่?')) {
                console.log('Activate user:', id);
                alert('เปิดใช้งานสำเร็จ!');
            }
        }
        
        function deleteUser(id) {
            if (confirm('ต้องการลบผู้ใช้นี้หรือไม่? การดำเนินการนี้ไม่สามารถยกเลิกได้!')) {
                console.log('Delete user:', id);
                alert('ลบผู้ใช้สำเร็จ!');
            }
        }
        
        function exportUsers() {
            console.log('Export users');
            alert('ส่งออกข้อมูลผู้ใช้สำเร็จ!');
        }
        
        function importUsers() {
            console.log('Import users');
        }
        
        function saveUser() {
            const form = document.getElementById('addUserForm');
            if (form.checkValidity()) {
                console.log('Save new user');
                alert('บันทึกผู้ใช้ใหม่สำเร็จ!');
                bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
            } else {
                form.reportValidity();
            }
        }
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                filterUsers();
            }
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