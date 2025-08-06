<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งค่าระบบ - ระบบ Invention Vote</title>
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
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 15px rgba(0,123,255,0.2);
        }
        
        .settings-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .settings-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .settings-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #007bff;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-online { background-color: #28a745; }
        .status-offline { background-color: #dc3545; }
        .status-warning { background-color: #ffc107; }
        
        .system-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .backup-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            transition: all 0.3s ease;
        }
        
        .backup-item:hover {
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }
        
        .log-entry {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 5px;
        }
        
        .log-entry.error {
            border-left-color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }
        
        .log-entry.warning {
            border-left-color: #ffc107;
            background: rgba(255, 193, 7, 0.1);
        }
        
        .log-entry.success {
            border-left-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }
        
        .nav-pills .nav-link {
            border-radius: 10px;
            margin: 0 5px;
            transition: all 0.3s ease;
            color: #495057;
        }
        
        .nav-pills .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        
        .tab-content {
            padding: 20px 0;
        }
        
        .progress-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 15px 0;
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
                    <a class="nav-link" href="#criteria">
                        <i class="fas fa-list-alt me-2"></i>เกณฑ์การให้คะแนน
                    </a>
                    <a class="nav-link" href="#reports">
                        <i class="fas fa-chart-bar me-2"></i>รายงานภาพรวม
                    </a>
                    <a class="nav-link active" href="#settings">
                        <i class="fas fa-cog me-2"></i>ตั้งค่าระบบ
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="header-section">
                    <h1 class="h3 mb-3">
                        <i class="fas fa-cog text-primary me-2"></i>
                        ตั้งค่าระบบ
                    </h1>
                    <p class="text-muted mb-0">จัดการการตั้งค่าระบบ การรักษาความปลอดภัย และการบำรุงรักษา</p>
                </div>

                <!-- Settings Navigation Tabs -->
                <ul class="nav nav-pills mb-4" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                            <i class="fas fa-sliders-h me-2"></i>ทั่วไป
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab">
                            <i class="fas fa-shield-alt me-2"></i>ความปลอดภัย
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="email-tab" data-bs-toggle="pill" data-bs-target="#email" type="button" role="tab">
                            <i class="fas fa-envelope me-2"></i>อีเมล
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="backup-tab" data-bs-toggle="pill" data-bs-target="#backup" type="button" role="tab">
                            <i class="fas fa-database me-2"></i>สำรองข้อมูล
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="system-tab" data-bs-toggle="pill" data-bs-target="#system" type="button" role="tab">
                            <i class="fas fa-server me-2"></i>ระบบ
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="logs-tab" data-bs-toggle="pill" data-bs-target="#logs" type="button" role="tab">
                            <i class="fas fa-file-alt me-2"></i>บันทึกการทำงาน
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="settingsTabsContent">
                    <!-- General Settings Tab -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="settings-section">
                            <div class="settings-header">
                                <h5><i class="fas fa-info-circle text-primary me-2"></i>ข้อมูลพื้นฐานของระบบ</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">ชื่อระบบ</label>
                                    <input type="text" class="form-control" value="ระบบ Invention Vote">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">เวอร์ชั่น</label>
                                    <input type="text" class="form-control" value="2.1.0" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">หน่วยงานเจ้าของระบบ</label>
                                    <input type="text" class="form-control" value="สำนักงาน กศน.">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">เว็บไซต์หลัก</label>
                                    <input type="url" class="form-control" value="https://www.nfe.go.th">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">คำอธิบายระบบ</label>
                                <textarea class="form-control" rows="3">ระบบการให้คะแนนสิ่งประดิษฐ์และนวัตกรรมสำหรับการแข่งขันในระดับต่างๆ</textarea>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-header">
                                <h5><i class="fas fa-cogs text-primary me-2"></i>การตั้งค่าทั่วไป</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong>เปิดใช้งานระบบ</strong>
                                            <br><small class="text-muted">เปิด/ปิดการใช้งานระบบทั้งหมด</small>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong>โหมดบำรุงรักษา</strong>
                                            <br><small class="text-muted">ปิดระบบชั่วคราวเพื่อบำรุงรักษา</small>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong>อนุญาตการสมัครใหม่</strong>
                                            <br><small class="text-muted">เปิด/ปิดการสร้างบัญชีผู้ใช้ใหม่</small>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">เขตเวลา</label>
                                    <select class="form-select">
                                        <option value="Asia/Bangkok" selected>Asia/Bangkok (UTC+7)</option>
                                        <option value="UTC">UTC (UTC+0)</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">ภาษาเริ่มต้น</label>
                                    <select class="form-select">
                                        <option value="th" selected>ไทย</option>
                                        <option value="en">English</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">จำนวนรายการต่อหน้า</label>
                                    <select class="form-select">
                                        <option value="10">10</option>
                                        <option value="25" selected>25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-success" onclick="saveGeneralSettings()">
                                    <i class="fas fa-save me-2"></i>บันทึกการตั้งค่า
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings Tab -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <div class="settings-section">
                            <div class="settings-header">
                                <h5><i class="fas fa-lock text-primary me-2"></i>นโยบายรหัสผ่าน</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">ความยาวรหัสผ่านขั้นต่ำ</label>
                                    <input type="number" class="form-control" value="8" min="6" max="20">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">อายุรหัสผ่าน (วัน)</label>
                                    <input type="number" class="form-control" value="90" min="30" max="365">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong>ต้องมีตัวเลข</strong>
                                            <br><small class="text-muted">รหัสผ่านต้องประกอบด้วยตัวเลข</small>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong>ต้องมีตัวอักษรพิมพ์เล็กและใหญ่</strong>
                                            <br><small class="text-muted">รหัสผ่านต้องมีตัวอักษรทั้ง a-z และ A-Z</small>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong>ต้องมีอักขระพิเศษ</strong>
                                            <br><small class="text-muted">รหัสผ่านต้องมีอักขระพิเศษ เช่น !@#$%</small>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-header">
                                <h5><i class="fas fa-user-shield text-primary me-2"></i>การรักษาความปลอดภัยการเข้าสู่ระบบ</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">จำนวนครั้งที่พยายามเข้าสู่ระบบผิด</label>
                                    <input type="number" class="form-control" value="5" min="3" max="10">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">ระยะเวลาล็อคบัญชี (นาที)</label>
                                    <input type="number" class="form-control" value="15" min="5" max="60">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">ระยะเวลา Session (ชั่วโมง)</label>
                                    <input type="number" class="form-control" value="8" min="1" max="24">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">ระยะเวลา Idle ก่อนออกจากระบบ (นาที)</label>
                                    <input type="number" class="form-control" value="60" min="15" max="120">
                                </div>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-success" onclick="saveSecuritySettings()">
                                    <i class="fas fa-save me-2"></i>บันทึกการตั้งค่าความปลอดภัย
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings Tab -->
                    <div class="tab-pane fade" id="email" role="tabpanel">
                        <div class="settings-section">
                            <div class="settings-header">
                                <h5><i class="fas fa-envelope-open text-primary me-2"></i>การตั้งค่า SMTP</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">SMTP Server</label>
                                    <input type="text" class="form-control" value="smtp.gmail.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">SMTP Port</label>
                                    <input type="number" class="form-control" value="587">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">ชื่อผู้ใช้ SMTP</label>
                                    <input type="email" class="form-control" value="system@invention-vote.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">รหัสผ่าน SMTP</label>
                                    <input type="password" class="form-control" value="••••••••••">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong>เปิดใช้งาน SSL/TLS</strong>
                                            <br><small class="text-muted">ใช้การเข้ารหัสในการส่งอีเมล</small>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-outline-primary me-2" onclick="testEmail()">
                                    <i class="fas fa-paper-plane me-2"></i>ทดสอบการส่งอีเมล
                                </button>
                                <button class="btn btn-success" onclick="saveEmailSettings()">
                                    <i class="fas fa-save me-2"></i>บันทึกการตั้งค่า
                                </button>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-header">
                                <h5><i class="fas fa-mail-bulk text-primary me-2"></i>เทมเพลตอีเมล</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">อีเมลต้อนรับผู้ใช้ใหม่</label>
                                    <button class="btn btn-outline-primary w-100" onclick="editEmailTemplate('welcome')">
                                        <i class="fas fa-edit me-2"></i>แก้ไขเทมเพลต
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">อีเมลรีเซ็ตรหัสผ่าน</label>
                                    <button class="btn btn-outline-primary w-100" onclick="editEmailTemplate('reset')">
                                        <i class="fas fa-edit me-2"></i>แก้ไขเทมเพลต
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">อีเมลแจ้งเตือนการให้คะแนน</label>
                                    <button class="btn btn-outline-primary w-100" onclick="editEmailTemplate('scoring')">
                                        <i class="fas fa-edit me-2"></i>แก้ไขเทมเพลต
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">อีเมลแจ้งผลการแข่งขัน</label>
                                    <button class="btn btn-outline-primary w-100" onclick="editEmailTemplate('results')">
                                        <i class="fas fa-edit me-2"></i>แก้ไขเทมเพลต
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Settings Tab -->
                    <div class="tab-pane fade" id="backup" role="tabpanel">
                        <div class="settings-section">
                            <div class="settings-header">
                                <h5><i class="fas fa-clock text-primary me-2"></i>การสำรองข้อมูลอัตโนมัติ</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong>เปิดใช้งานการสำรองข้อมูลอัตโนมัติ</strong>
                                            <br><small class="text-muted">สำรองข้อมูลตามกำหนดเวลาที่ตั้งไว้</small>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">ความถี่ในการสำรองข้อมูล</label>
                                    <select class="form-select">
                                        <option value="daily" selected>รายวัน</option>
                                        <option value="weekly">รายสัปดาห์</option>
                                        <option value="monthly">รายเดือน</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">เวลาในการสำรองข้อมูล</label>
                                    <input type="time" class="form-control" value="02:00">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">เก็บสำรองไว้ (วัน)</label>
                                    <input type="number" class="form-control" value="30" min="7" max="365">
                                </div>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-warning me-2" onclick="createBackupNow()">
                                    <i class="fas fa-save me-2"></i>สำรองข้อมูลทันที
                                </button>
                                <button class="btn btn-success" onclick="saveBackupSettings()">
                                    <i class="fas fa-save me-2"></i>บันทึกการตั้งค่า
                                </button>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-header">
                                <h5><i class="fas fa-history text-primary me-2"></i>ประวัติการสำรองข้อมูล</h5>
                            </div>
                            <div class="backup-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>backup_2568-01-15_02-00.sql</strong>
                                        <br><small class="text-muted">ขนาด: 125.8 MB | สร้างเมื่อ: 15 ม.ค. 2568 02:00 น.</small>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="downloadBackup('backup_2568-01-15_02-00.sql')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="restoreBackup('backup_2568-01-15_02-00.sql')">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteBackup('backup_2568-01-15_02-00.sql')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="backup-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>backup_2568-01-14_02-00.sql</strong>
                                        <br><small class="text-muted">ขนาด: 124.2 MB | สร้างเมื่อ: 14 ม.ค. 2568 02:00 น.</small>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="downloadBackup('backup_2568-01-14_02-00.sql')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="restoreBackup('backup_2568-01-14_02-00.sql')">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteBackup('backup_2568-01-14_02-00.sql')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info Tab -->
                    <div class="tab-pane fade" id="system" role="tabpanel">
                        <div class="settings-section">
                            <div class="settings-header">
                                <h5><i class="fas fa-info-circle text-primary me-2"></i>ข้อมูลระบบ</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="system-info">
                                        <strong>เซิร์ฟเวอร์เว็บ</strong>
                                        <div class="mt-2">
                                            <div>Apache/2.4.41 (Ubuntu)</div>
                                            <div>PHP 8.2.5</div>
                                            <div>MySQL 8.0.33</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="system-info">
                                        <strong>ระบบปฏิบัติการ</strong>
                                        <div class="mt-2">
                                            <div>Ubuntu 22.04.2 LTS</div>
                                            <div>Kernel: 5.15.0-72-generic</div>
                                            <div>Uptime: 15 วัน 8 ชั่วโมง 32 นาที</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="system-info">
                                        <strong>หน่วยความจำ</strong>
                                        <div class="mt-2">
                                            <div>RAM: 8 GB (ใช้งาน 4.2 GB)</div>
                                            <div class="progress mt-2">
                                                <div class="progress-bar bg-info" style="width: 52.5%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="system-info">
                                        <strong>พื้นที่จัดเก็บข้อมูล</strong>
                                        <div class="mt-2">
                                            <div>Disk: 500 GB (ใช้งาน 125 GB)</div>
                                            <div class="progress mt-2">
                                                <div class="progress-bar bg-success" style="width: 25%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-header">
                                <h5><i class="fas fa-heartbeat text-primary me-2"></i>สถานะบริการ</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-3">
                                        <div>
                                            <span class="status-indicator status-online"></span>
                                            <strong>เซิร์ฟเวอร์เว็บ</strong>
                                        </div>
                                        <span class="badge bg-success">ปกติ</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-3">
                                        <div>
                                            <span class="status-indicator status-online"></span>
                                            <strong>ฐานข้อมูล</strong>
                                        </div>
                                        <span class="badge bg-success">ปกติ</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-3">
                                        <div>
                                            <span class="status-indicator status-warning"></span>
                                            <strong>บริการอีเมล</strong>
                                        </div>
                                        <span class="badge bg-warning text-dark">เฝ้าระวัง</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-3">
                                        <div>
                                            <span class="status-indicator status-online"></span>
                                            <strong>การสำรองข้อมูล</strong>
                                        </div>
                                        <span class="badge bg-success">ปกติ</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-outline-primary" onclick="checkSystemHealth()">
                                    <i class="fas fa-sync-alt me-2"></i>ตรวจสอบสถานะ
                                </button>
                                <button class="btn btn-warning" onclick="restartServices()">
                                    <i class="fas fa-redo me-2"></i>รีสตาร์ทบริการ
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- System Logs Tab -->
                    <div class="tab-pane fade" id="logs" role="tabpanel">
                        <div class="settings-section">
                            <div class="settings-header d-flex justify-content-between align-items-center">
                                <h5><i class="fas fa-file-alt text-primary me-2"></i>บันทึกการทำงานของระบบ</h5>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="refreshLogs()">
                                        <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="clearLogs()">
                                        <i class="fas fa-trash me-1"></i>ล้างบันทึก
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select class="form-select" id="logLevel">
                                            <option value="">ทุกระดับ</option>
                                            <option value="error">ข้อผิดพลาด</option>
                                            <option value="warning">คำเตือน</option>
                                            <option value="info">ข้อมูล</option>
                                            <option value="success">สำเร็จ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" class="form-control" id="logDate">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" placeholder="ค้นหาในบันทึก..." id="logSearch">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-outline-primary w-100" onclick="filterLogs()">
                                            <i class="fas fa-filter"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="log-entry error">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>[ERROR]</strong> ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์อีเมลได้
                                        <br><small class="text-muted">15 ม.ค. 2568 14:35:22 | IP: 192.168.1.10 | User: admin</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewLogDetail(1)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="log-entry warning">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>[WARNING]</strong> พื้นที่จัดเก็บข้อมูลใกล้เต็ม (85% ใช้งาน)
                                        <br><small class="text-muted">15 ม.ค. 2568 12:00:00 | System Auto Check</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewLogDetail(2)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="log-entry success">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>[SUCCESS]</strong> สำรองข้อมูลอัตโนมัติเสร็จสิ้น
                                        <br><small class="text-muted">15 ม.ค. 2568 02:00:05 | System Auto Backup</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewLogDetail(3)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="log-entry">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>[INFO]</strong> ผู้ใช้ใหม่ลงทะเบียนเข้าระบบ: อ.สมชาย ใจดี
                                        <br><small class="text-muted">14 ม.ค. 2568 16:45:33 | IP: 203.154.123.45</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewLogDetail(4)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="log-entry success">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>[SUCCESS]</strong> ประธานกรรมการรับรองผลการแข่งขัน: การแข่งขันระดับจังหวัดเชียงใหม่
                                        <br><small class="text-muted">14 ม.ค. 2568 15:20:10 | IP: 192.168.1.15 | User: chairman_01</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewLogDetail(5)">
                                        <i class="fas fa-eye"></i>
                                    </button>
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
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Settings functions
        function saveGeneralSettings() {
            console.log('Saving general settings...');
            alert('บันทึกการตั้งค่าทั่วไปสำเร็จ!');
        }
        
        function saveSecuritySettings() {
            console.log('Saving security settings...');
            alert('บันทึกการตั้งค่าความปลอดภัยสำเร็จ!');
        }
        
        function saveEmailSettings() {
            console.log('Saving email settings...');
            alert('บันทึกการตั้งค่าอีเมลสำเร็จ!');
        }
        
        function testEmail() {
            console.log('Testing email configuration...');
            alert('ส่งอีเมลทดสอบแล้ว! กรุณาตรวจสอบกล่องจดหมาย');
        }
        
        function editEmailTemplate(type) {
            console.log('Edit email template:', type);
            // Open email template editor modal
        }
        
        function saveBackupSettings() {
            console.log('Saving backup settings...');
            alert('บันทึกการตั้งค่าสำรองข้อมูลสำเร็จ!');
        }
        
        function createBackupNow() {
            if (confirm('ต้องการสำรองข้อมูลทันทีหรือไม่?')) {
                console.log('Creating backup now...');
                alert('กำลังสำรองข้อมูล... จะแจ้งเตือนเมื่อเสร็จสิ้น');
            }
        }
        
        function downloadBackup(filename) {
            console.log('Download backup:', filename);
            alert('กำลังดาวน์โหลด: ' + filename);
        }
        
        function restoreBackup(filename) {
            if (confirm('ต้องการคืนค่าข้อมูลจากไฟล์ ' + filename + ' หรือไม่?\nข้อมูลปัจจุบันจะถูกแทนที่!')) {
                console.log('Restore backup:', filename);
                alert('กำลังคืนค่าข้อมูล... กรุณารอสักครู่');
            }
        }
        
        function deleteBackup(filename) {
            if (confirm('ต้องการลบไฟล์สำรองข้อมูล ' + filename + ' หรือไม่?')) {
                console.log('Delete backup:', filename);
                alert('ลบไฟล์สำรองข้อมูลสำเร็จ!');
            }
        }
        
        function checkSystemHealth() {
            console.log('Checking system health...');
            alert('ตรวจสอบสถานะระบบเสร็จสิ้น - ทุกบริการทำงานปกติ');
        }
        
        function restartServices() {
            if (confirm('ต้องการรีสตาร์ทบริการระบบหรือไม่? ผู้ใช้อาจไม่สามารถเข้าใช้งานได้ชั่วคราว')) {
                console.log('Restarting services...');
                alert('กำลังรีสตาร์ทบริการ... กรุณารอสักครู่');
            }
        }
        
        function refreshLogs() {
            console.log('Refreshing logs...');
            alert('อัพเดทบันทึกการทำงานล่าสุดแล้ว!');
        }
        
        function clearLogs() {
            if (confirm('ต้องการล้างบันทึกการทำงานทั้งหมดหรือไม่?')) {
                console.log('Clearing logs...');
                alert('ล้างบันทึกการทำงานสำเร็จ!');
            }
        }
        
        function filterLogs() {
            const level = document.getElementById('logLevel').value;
            const date = document.getElementById('logDate').value;
            const search = document.getElementById('logSearch').value;
            
            console.log('Filtering logs:', { level, date, search });
            alert('กรองบันทึกการทำงานแล้ว!');
        }
        
        function viewLogDetail(id) {
            console.log('View log detail:', id);
            // Open log detail modal
        }
        
        // Sidebar navigation
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                console.log('Navigate to:', this.getAttribute('href'));
            });
        });
        
        // Auto-refresh system status every 30 seconds
        setInterval(function() {
            console.log('Auto-refreshing system status...');
        }, 30000);
    </script>
</body>
</html>