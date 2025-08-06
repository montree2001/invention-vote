<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการประเภทสิ่งประดิษฐ์ - ระบบ Invention Vote</title>
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
        
        .type-card {
            background: white;
            border-radius: 15px;
            padding: 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border-left: 5px solid #007bff;
            overflow: hidden;
        }
        
        .type-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .type-card-header {
            position: relative;
            height: 150px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .type-card-header.has-image {
            background: none;
        }
        
        .type-card-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .type-card-body {
            padding: 25px;
        }
        
        .type-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .type-card.science { border-left-color: #007bff; }
        .type-card.technology { border-left-color: #28a745; }
        .type-card.innovation { border-left-color: #ffc107; }
        .type-card.environment { border-left-color: #17a2b8; }
        .type-card.health { border-left-color: #dc3545; }
        .type-card.agriculture { border-left-color: #6f42c1; }
        
        .type-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .type-icon.science { background: linear-gradient(135deg, #007bff, #0056b3); }
        .type-icon.technology { background: linear-gradient(135deg, #28a745, #20c997); }
        .type-icon.innovation { background: linear-gradient(135deg, #ffc107, #fd7e14); }
        .type-icon.environment { background: linear-gradient(135deg, #17a2b8, #20c997); }
        .type-icon.health { background: linear-gradient(135deg, #dc3545, #fd7e14); }
        .type-icon.agriculture { background: linear-gradient(135deg, #6f42c1, #e83e8c); }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0;
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
        
        .color-picker {
            width: 50px;
            height: 40px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            cursor: pointer;
        }
        
        .criteria-link {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }
        
        .criteria-link:hover {
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            transform: translateX(5px);
        }
        
        .icon-selector {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin: 15px 0;
        }
        
        .icon-option {
            width: 50px;
            height: 50px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }
        
        .icon-option:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        
        .icon-option.selected {
            border-color: #007bff;
            background-color: #007bff;
            color: white;
        }
        
        .image-preview {
            width: 100%;
            height: 120px;
            border: 2px dashed #e9ecef;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .image-preview:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        
        .image-preview.has-image {
            border-style: solid;
            cursor: default;
        }
        
        .image-preview.has-image:hover {
            background-color: transparent;
        }
        
        .image-placeholder {
            text-align: center;
            color: #6c757d;
        }
        
        .image-placeholder i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .type-card-header:hover .upload-overlay {
            opacity: 1;
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
                    <a class="nav-link" href="#users">
                        <i class="fas fa-users me-2"></i>จัดการบัญชีผู้ใช้
                    </a>
                    <a class="nav-link active" href="#invention-types">
                        <i class="fas fa-tags me-2"></i>ประเภทสิ่งประดิษฐ์
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
                                <i class="fas fa-tags text-primary me-2"></i>
                                จัดการประเภทสิ่งประดิษฐ์
                            </h1>
                            <p class="text-muted mb-0">สร้าง แก้ไข และจัดการประเภทสิ่งประดิษฐ์สำหรับการแข่งขัน</p>
                        </div>
                        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                            <i class="fas fa-plus me-2"></i>เพิ่มประเภทใหม่
                        </button>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #007bff, #6610f2);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <div class="stats-number">6</div>
                                <div>ประเภททั้งหมด</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stats-number">5</div>
                                <div>ใช้งานอยู่</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <div class="stats-number">248</div>
                                <div>ผลงานทั้งหมด</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white" style="background: linear-gradient(135deg, #dc3545, #fd7e14);">
                            <div class="card-body stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-list-alt"></i>
                                </div>
                                <div class="stats-number">12</div>
                                <div>เกณฑ์การให้คะแนน</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="search-box">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ค้นหาประเภท</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="ชื่อประเภทสิ่งประดิษฐ์..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">สถานะ</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">ทุกสถานะ</option>
                                <option value="active">ใช้งานอยู่</option>
                                <option value="inactive">ไม่ใช้งาน</option>
                                <option value="draft">ร่าง</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">เรียงตาม</label>
                            <select class="form-select" id="sortFilter">
                                <option value="name">ชื่อประเภท</option>
                                <option value="created">วันที่สร้าง</option>
                                <option value="updated">วันที่อัพเดท</option>
                                <option value="usage">จำนวนการใช้งาน</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button class="btn btn-outline-primary w-100" onclick="filterTypes()">
                                <i class="fas fa-filter me-1"></i>กรอง
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Types Grid -->
                <div class="row" id="typesGrid">
                    <!-- Type Card 1 - Science -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="type-card science">
                            <div class="type-card-header has-image">
                                <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=400&h=150&fit=crop" alt="วิทยาศาสตร์">
                                <div class="type-overlay">
                                    <div class="type-icon science">
                                        <i class="fas fa-flask"></i>
                                    </div>
                                </div>
                                <div class="position-absolute top-0 end-0 p-2">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="editType(1)"><i class="fas fa-edit me-2"></i>แก้ไข</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="changeImage(1)"><i class="fas fa-image me-2"></i>เปลี่ยนภาพหน้าปก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="duplicateType(1)"><i class="fas fa-copy me-2"></i>คัดลอก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="viewStats(1)"><i class="fas fa-chart-bar me-2"></i>สถิติ</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteType(1)"><i class="fas fa-trash me-2"></i>ลบ</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="type-card-body">
                                <h5 class="card-title text-primary mb-2">วิทยาศาสตร์</h5>
                                <p class="text-muted mb-3">สิ่งประดิษฐ์ที่เกี่ยวข้องกับการค้นคว้าและการพัฒนาทางวิทยาศาสตร์</p>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">ผลงาน</small>
                                        <div class="fw-bold text-primary">87 ชิ้น</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">เกณฑ์การให้คะแนน</small>
                                        <div class="fw-bold text-success">3 ชุด</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge bg-success">ใช้งานอยู่</span>
                                    <span class="badge bg-info">มีเกณฑ์</span>
                                </div>
                                
                                <div class="criteria-link">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="fw-bold">เกณฑ์การให้คะแนนหลัก</small>
                                            <br><small class="text-muted">เกณฑ์มาตรฐานสำหรับสิ่งประดิษฐ์ประเภทวิทยาศาสตร์</small>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewCriteria(1)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <small class="text-muted">สร้างเมื่อ 10 ม.ค. 2568</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Type Card 2 - Technology -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="type-card technology">
                            <div class="type-card-header has-image">
                                <img src="https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=400&h=150&fit=crop" alt="เทคโนโลยี">
                                <div class="type-overlay">
                                    <div class="type-icon technology">
                                        <i class="fas fa-microchip"></i>
                                    </div>
                                </div>
                                <div class="position-absolute top-0 end-0 p-2">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="editType(2)"><i class="fas fa-edit me-2"></i>แก้ไข</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="changeImage(2)"><i class="fas fa-image me-2"></i>เปลี่ยนภาพหน้าปก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="duplicateType(2)"><i class="fas fa-copy me-2"></i>คัดลอก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="viewStats(2)"><i class="fas fa-chart-bar me-2"></i>สถิติ</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteType(2)"><i class="fas fa-trash me-2"></i>ลบ</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="type-card-body">
                                <h5 class="card-title text-success mb-2">เทคโนโลยี</h5>
                                <p class="text-muted mb-3">สิ่งประดิษฐ์ที่ใช้เทคโนโลยีใหม่หรือการประยุกต์ใช้เทคโนโลยี</p>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">ผลงาน</small>
                                        <div class="fw-bold text-success">69 ชิ้น</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">เกณฑ์การให้คะแนน</small>
                                        <div class="fw-bold text-success">2 ชุด</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge bg-success">ใช้งานอยู่</span>
                                    <span class="badge bg-info">มีเกณฑ์</span>
                                </div>
                                
                                <div class="criteria-link">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="fw-bold">เกณฑ์เทคโนโลยีขั้นสูง</small>
                                            <br><small class="text-muted">เกณฑ์สำหรับประเมินเทคโนโลยีและนวัตกรรม</small>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewCriteria(2)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <small class="text-muted">สร้างเมื่อ 8 ม.ค. 2568</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Type Card 3 - Innovation -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="type-card innovation">
                            <div class="type-card-header has-image">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=150&fit=crop" alt="นวัตกรรม">
                                <div class="type-overlay">
                                    <div class="type-icon innovation">
                                        <i class="fas fa-lightbulb"></i>
                                    </div>
                                </div>
                                <div class="position-absolute top-0 end-0 p-2">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="editType(3)"><i class="fas fa-edit me-2"></i>แก้ไข</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="changeImage(3)"><i class="fas fa-image me-2"></i>เปลี่ยนภาพหน้าปก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="duplicateType(3)"><i class="fas fa-copy me-2"></i>คัดลอก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="viewStats(3)"><i class="fas fa-chart-bar me-2"></i>สถิติ</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteType(3)"><i class="fas fa-trash me-2"></i>ลบ</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="type-card-body">
                                <h5 class="card-title text-warning mb-2">นวัตกรรม</h5>
                                <p class="text-muted mb-3">สิ่งประดิษฐ์ที่มีความคิดสร้างสรรค์และแนวทางใหม่</p>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">ผลงาน</small>
                                        <div class="fw-bold text-warning">54 ชิ้น</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">เกณฑ์การให้คะแนน</small>
                                        <div class="fw-bold text-success">2 ชุด</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge bg-success">ใช้งานอยู่</span>
                                    <span class="badge bg-info">มีเกณฑ์</span>
                                </div>
                                
                                <div class="criteria-link">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="fw-bold">เกณฑ์ความคิดสร้างสรรค์</small>
                                            <br><small class="text-muted">เกณฑ์สำหรับประเมินนวัตกรรมและความคิดสร้างสรรค์</small>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewCriteria(3)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <small class="text-muted">สร้างเมื่อ 5 ม.ค. 2568</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Type Card 4 - Environment -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="type-card environment">
                            <div class="type-card-header has-image">
                                <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=400&h=150&fit=crop" alt="สิ่งแวดล้อม">
                                <div class="type-overlay">
                                    <div class="type-icon environment">
                                        <i class="fas fa-leaf"></i>
                                    </div>
                                </div>
                                <div class="position-absolute top-0 end-0 p-2">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="editType(4)"><i class="fas fa-edit me-2"></i>แก้ไข</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="changeImage(4)"><i class="fas fa-image me-2"></i>เปลี่ยนภาพหน้าปก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="duplicateType(4)"><i class="fas fa-copy me-2"></i>คัดลอก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="viewStats(4)"><i class="fas fa-chart-bar me-2"></i>สถิติ</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteType(4)"><i class="fas fa-trash me-2"></i>ลบ</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="type-card-body">
                                <h5 class="card-title text-info mb-2">สิ่งแวดล้อม</h5>
                                <p class="text-muted mb-3">สิ่งประดิษฐ์ที่ช่วยอนุรักษ์หรือปรับปรุงสิ่งแวดล้อม</p>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">ผลงาน</small>
                                        <div class="fw-bold text-info">38 ชิ้น</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">เกณฑ์การให้คะแนน</small>
                                        <div class="fw-bold text-success">3 ชุด</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge bg-success">ใช้งานอยู่</span>
                                    <span class="badge bg-info">มีเกณฑ์</span>
                                </div>
                                
                                <div class="criteria-link">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="fw-bold">เกณฑ์ความยั่งยืน</small>
                                            <br><small class="text-muted">เกณฑ์สำหรับประเมินผลกระทบต่อสิ่งแวดล้อม</small>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewCriteria(4)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <small class="text-muted">สร้างเมื่อ 3 ม.ค. 2568</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Type Card 5 - Health -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="type-card health">
                            <div class="type-card-header has-image">
                                <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&h=150&fit=crop" alt="สาธารณสุข">
                                <div class="type-overlay">
                                    <div class="type-icon health">
                                        <i class="fas fa-heartbeat"></i>
                                    </div>
                                </div>
                                <div class="position-absolute top-0 end-0 p-2">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="editType(5)"><i class="fas fa-edit me-2"></i>แก้ไข</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="changeImage(5)"><i class="fas fa-image me-2"></i>เปลี่ยนภาพหน้าปก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="duplicateType(5)"><i class="fas fa-copy me-2"></i>คัดลอก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="viewStats(5)"><i class="fas fa-chart-bar me-2"></i>สถิติ</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteType(5)"><i class="fas fa-trash me-2"></i>ลบ</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="type-card-body">
                                <h5 class="card-title text-danger mb-2">สาธารณสุข</h5>
                                <p class="text-muted mb-3">สิ่งประดิษฐ์ที่เกี่ยวข้องกับการรักษาพยาบาลและสุขภาพ</p>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">ผลงาน</small>
                                        <div class="fw-bold text-danger">29 ชิ้น</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">เกณฑ์การให้คะแนน</small>
                                        <div class="fw-bold text-success">2 ชุด</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge bg-success">ใช้งานอยู่</span>
                                    <span class="badge bg-info">มีเกณฑ์</span>
                                </div>
                                
                                <div class="criteria-link">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="fw-bold">เกณฑ์การแพทย์</small>
                                            <br><small class="text-muted">เกณฑ์สำหรับประเมินประสิทธิภาพทางการแพทย์</small>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewCriteria(5)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <small class="text-muted">สร้างเมื่อ 1 ม.ค. 2568</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Type Card 6 - Agriculture (Draft) -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="type-card agriculture">
                            <div class="type-card-header">
                                <!-- No image, shows gradient background -->
                                <div class="type-icon agriculture">
                                    <i class="fas fa-seedling"></i>
                                </div>
                                <div class="position-absolute top-0 end-0 p-2">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="editType(6)"><i class="fas fa-edit me-2"></i>แก้ไข</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="changeImage(6)"><i class="fas fa-image me-2"></i>เพิ่มภาพหน้าปก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="duplicateType(6)"><i class="fas fa-copy me-2"></i>คัดลอก</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="activateType(6)"><i class="fas fa-play me-2"></i>เปิดใช้งาน</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteType(6)"><i class="fas fa-trash me-2"></i>ลบ</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="type-card-body">
                                <h5 class="card-title" style="color: #6f42c1;">เกษตรกรรม</h5>
                                <p class="text-muted mb-3">สิ่งประดิษฐ์ที่เกี่ยวข้องกับการเกษตรและการผลิตอาหาร</p>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">ผลงาน</small>
                                        <div class="fw-bold text-muted">0 ชิ้น</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">เกณฑ์การให้คะแนน</small>
                                        <div class="fw-bold text-muted">0 ชุด</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge bg-secondary">ร่าง</span>
                                    <span class="badge bg-warning text-dark">ยังไม่มีเกณฑ์</span>
                                </div>
                                
                                <div class="alert alert-info">
                                    <small><i class="fas fa-info-circle me-1"></i>ประเภทนี้ยังอยู่ในสถานะร่าง ต้องสร้างเกณฑ์การให้คะแนนก่อนเปิดใช้งาน</small>
                                </div>
                                
                                <div class="text-end">
                                    <small class="text-muted">สร้างเมื่อ 15 ม.ค. 2568</small>
                                </div>
                            </div>
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
                        <li class="page-item">
                            <a class="page-link" href="#">ถัดไป</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Add Type Modal -->
    <div class="modal fade" id="addTypeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>เพิ่มประเภทสิ่งประดิษฐ์ใหม่
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addTypeForm">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">ชื่อประเภท <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="เช่น วิทยาศาสตร์ เทคโนโลยี" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">สี</label>
                                <input type="color" class="form-control color-picker" value="#007bff">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">คำอธิบาย</label>
                            <textarea class="form-control" rows="3" placeholder="อธิบายลักษณะของสิ่งประดิษฐ์ในประเภทนี้..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">ภาพหน้าปก</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="image-preview" id="imagePreview">
                                        <div class="image-placeholder">
                                            <i class="fas fa-image"></i>
                                            <div>เลือกภาพหน้าปก</div>
                                            <small class="text-muted">JPG, PNG, GIF (สูงสุด 5MB)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <input type="file" class="form-control mb-2" id="coverImage" accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        แนะนำขนาดภาพ 400x150 พิกเซล หรืออัตราส่วน 8:3 เพื่อความสวยงาม
                                    </small>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeImage()" id="removeImageBtn" style="display: none;">
                                            <i class="fas fa-trash me-1"></i>ลบภาพ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">เลือกไอคอน</label>
                            <div class="icon-selector">
                                <div class="icon-option selected" data-icon="fas fa-flask">
                                    <i class="fas fa-flask"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-microchip">
                                    <i class="fas fa-microchip"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-lightbulb">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-leaf">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-heartbeat">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-seedling">
                                    <i class="fas fa-seedling"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-cog">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-rocket">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-atom">
                                    <i class="fas fa-atom"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-dna">
                                    <i class="fas fa-dna"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-brain">
                                    <i class="fas fa-brain"></i>
                                </div>
                                <div class="icon-option" data-icon="fas fa-robot">
                                    <i class="fas fa-robot"></i>
                                </div>
                            </div>
                            <small class="text-muted">ไอคอนจะแสดงเป็น overlay บนภาพหน้าปก หรือแสดงแทนภาพหากไม่มีภาพหน้าปก</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">สถานะเริ่มต้น</label>
                                <select class="form-select">
                                    <option value="draft" selected>ร่าง</option>
                                    <option value="active">ใช้งานอยู่</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ลำดับการแสดงผล</label>
                                <input type="number" class="form-control" value="1" min="1">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">คำค้นหา (Tags)</label>
                            <input type="text" class="form-control" placeholder="เช่น วิทยาศาสตร์, ฟิสิกส์, เคมี (คั่นด้วยเครื่องหมายจุลภาค)">
                            <small class="text-muted">ช่วยในการค้นหาและจัดกลุมประเภทสิ่งประดิษฐ์</small>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowMultiple">
                            <label class="form-check-label" for="allowMultiple">
                                อนุญาตให้สิ่งประดิษฐ์หนึ่งชิ้นอยู่ในหลายประเภท
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="saveType()">
                        <i class="fas fa-save me-1"></i>บันทึกประเภท
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Image Modal -->
    <div class="modal fade" id="changeImageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-image me-2"></i>เปลี่ยนภาพหน้าปก
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="current-image" id="currentImagePreview">
                            <img src="" alt="ภาพปัจจุบัน" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">เลือกภาพใหม่</label>
                        <input type="file" class="form-control" id="newCoverImage" accept="image/*" onchange="previewNewImage(this)">
                        <small class="text-muted">JPG, PNG, GIF (สูงสุด 5MB) แนะนำขนาด 400x150 พิกเซล</small>
                    </div>
                    <div class="text-center" id="newImagePreview" style="display: none;">
                        <strong>ตัวอย่างภาพใหม่:</strong>
                        <div class="mt-2">
                            <img src="" alt="ภาพใหม่" class="img-fluid rounded" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-danger me-2" onclick="removeTypeImage()">
                        <i class="fas fa-trash me-1"></i>ลบภาพ
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveNewImage()">
                        <i class="fas fa-save me-1"></i>บันทึก
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Icon selector functionality
        document.querySelectorAll('.icon-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
                // Add selected class to clicked option
                this.classList.add('selected');
            });
        });
        
        // Image preview functionality
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const removeBtn = document.getElementById('removeImageBtn');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="preview-image">`;
                    preview.classList.add('has-image');
                    removeBtn.style.display = 'inline-block';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function removeImage() {
            const preview = document.getElementById('imagePreview');
            const input = document.getElementById('coverImage');
            const removeBtn = document.getElementById('removeImageBtn');
            
            preview.innerHTML = `
                <div class="image-placeholder">
                    <i class="fas fa-image"></i>
                    <div>เลือกภาพหน้าปก</div>
                    <small class="text-muted">JPG, PNG, GIF (สูงสุด 5MB)</small>
                </div>
            `;
            preview.classList.remove('has-image');
            input.value = '';
            removeBtn.style.display = 'none';
        }
        
        // Change image modal functions
        let currentTypeId = null;
        
        function changeImage(typeId) {
            currentTypeId = typeId;
            
            // Get current image (this would normally come from database)
            const currentImages = {
                1: 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=400&h=150&fit=crop',
                2: 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=400&h=150&fit=crop',
                3: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=150&fit=crop',
                4: 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=400&h=150&fit=crop',
                5: 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&h=150&fit=crop',
                6: null
            };
            
            const currentImage = currentImages[typeId];
            const currentImagePreview = document.getElementById('currentImagePreview');
            
            if (currentImage) {
                currentImagePreview.innerHTML = `<img src="${currentImage}" alt="ภาพปัจจุบัน" class="img-fluid rounded" style="max-height: 200px;">`;
            } else {
                currentImagePreview.innerHTML = '<div class="text-muted"><i class="fas fa-image" style="font-size: 3rem;"></i><br>ยังไม่มีภาพหน้าปก</div>';
            }
            
            // Reset new image preview
            document.getElementById('newImagePreview').style.display = 'none';
            document.getElementById('newCoverImage').value = '';
            
            // Show modal
            new bootstrap.Modal(document.getElementById('changeImageModal')).show();
        }
        
        function previewNewImage(input) {
            const newPreview = document.getElementById('newImagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    newPreview.querySelector('img').src = e.target.result;
                    newPreview.style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                newPreview.style.display = 'none';
            }
        }
        
        function saveNewImage() {
            const newImageInput = document.getElementById('newCoverImage');
            
            if (newImageInput.files && newImageInput.files[0]) {
                console.log('Save new image for type:', currentTypeId);
                alert('บันทึกภาพหน้าปกใหม่สำเร็จ!');
                bootstrap.Modal.getInstance(document.getElementById('changeImageModal')).hide();
                
                // Here you would upload the image and update the UI
                // For demo purposes, we'll just update the preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update the type card image
                    const typeCard = document.querySelector(`[onclick="changeImage(${currentTypeId})"]`).closest('.type-card');
                    const headerImg = typeCard.querySelector('.type-card-header img');
                    if (headerImg) {
                        headerImg.src = e.target.result;
                    } else {
                        // Create new image element if it doesn't exist
                        const header = typeCard.querySelector('.type-card-header');
                        header.classList.add('has-image');
                        header.innerHTML = `
                            <img src="${e.target.result}" alt="ภาพหน้าปก">
                            <div class="type-overlay">
                                <div class="type-icon ${typeCard.classList[1]}">
                                    <i class="${typeCard.querySelector('.type-icon i').className}"></i>
                                </div>
                            </div>
                            ${header.querySelector('.position-absolute').outerHTML}
                        `;
                    }
                };
                reader.readAsDataURL(newImageInput.files[0]);
            } else {
                alert('กรุณาเลือกภาพใหม่');
            }
        }
        
        function removeTypeImage() {
            if (confirm('ต้องการลบภาพหน้าปกหรือไม่?')) {
                console.log('Remove image for type:', currentTypeId);
                alert('ลบภาพหน้าปกสำเร็จ!');
                bootstrap.Modal.getInstance(document.getElementById('changeImageModal')).hide();
                
                // Update the UI to show no image
                const typeCard = document.querySelector(`[onclick="changeImage(${currentTypeId})"]`).closest('.type-card');
                const header = typeCard.querySelector('.type-card-header');
                header.classList.remove('has-image');
                
                // Get the icon class from the type card
                const typeClass = typeCard.classList[1]; // science, technology, etc.
                const iconClass = typeCard.querySelector('.type-icon i').className;
                
                header.innerHTML = `
                    <div class="type-icon ${typeClass}">
                        <i class="${iconClass}"></i>
                    </div>
                    ${header.querySelector('.position-absolute').outerHTML}
                `;
            }
        }
        
        // Filter function
        function filterTypes() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const status = document.getElementById('statusFilter').value;
            const sort = document.getElementById('sortFilter').value;
            
            console.log('Filtering types with:', { search, status, sort });
            alert('กรองข้อมูลแล้ว!');
        }
        
        // Type management functions
        function editType(id) {
            console.log('Edit type:', id);
            // Open edit modal with type data
        }
        
        function duplicateType(id) {
            if (confirm('ต้องการคัดลอกประเภทสิ่งประดิษฐ์นี้หรือไม่?')) {
                console.log('Duplicate type:', id);
                alert('คัดลอกประเภทสิ่งประดิษฐ์สำเร็จ!');
            }
        }
        
        function deleteType(id) {
            if (confirm('ต้องการลบประเภทสิ่งประดิษฐ์นี้หรือไม่?\nหากมีผลงานที่ใช้ประเภทนี้อยู่ จะต้องเปลี่ยนประเภทก่อน')) {
                console.log('Delete type:', id);
                alert('ลบประเภทสิ่งประดิษฐ์สำเร็จ!');
            }
        }
        
        function activateType(id) {
            if (confirm('ต้องการเปิดใช้งานประเภทสิ่งประดิษฐ์นี้หรือไม่?\nควรมีเกณฑ์การให้คะแนนก่อนเปิดใช้งาน')) {
                console.log('Activate type:', id);
                alert('เปิดใช้งานประเภทสิ่งประดิษฐ์สำเร็จ!');
            }
        }
        
        function viewStats(id) {
            console.log('View stats for type:', id);
            // Show statistics modal
        }
        
        function viewCriteria(id) {
            console.log('View criteria for type:', id);
            // Navigate to criteria page or show criteria modal
        }
        
        function saveType() {
            const form = document.getElementById('addTypeForm');
            if (form.checkValidity()) {
                const selectedIcon = document.querySelector('.icon-option.selected').dataset.icon;
                console.log('Save new type with icon:', selectedIcon);
                alert('บันทึกประเภทสิ่งประดิษฐ์ใหม่สำเร็จ!');
                bootstrap.Modal.getInstance(document.getElementById('addTypeModal')).hide();
            } else {
                form.reportValidity();
            }
        }
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                filterTypes();
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
        
        // Auto-refresh statistics every 2 minutes
        setInterval(function() {
            console.log('Auto-refreshing type statistics...');
        }, 120000);
    </script>
</body>
</html>