<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการเกณฑ์การให้คะแนน - ระบบ Invention Vote</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sortablejs/1.15.0/Sortable.min.css" rel="stylesheet">
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
        
        .criteria-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .criteria-section:hover {
            border-color: #007bff;
            box-shadow: 0 5px 20px rgba(0,123,255,0.1);
        }
        
        .criteria-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 5px solid #007bff;
        }
        
        .sub-criteria {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border-left: 3px solid #28a745;
        }
        
        .question-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            transition: all 0.3s ease;
        }
        
        .question-item:hover {
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }
        
        .score-levels {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        
        .score-level {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .score-level.excellent { background: linear-gradient(135deg, #28a745, #20c997); }
        .score-level.good { background: linear-gradient(135deg, #007bff, #6610f2); }
        .score-level.fair { background: linear-gradient(135deg, #fd7e14, #e83e8c); }
        .score-level.poor { background: linear-gradient(135deg, #dc3545, #fd7e14); }
        
        .drag-handle {
            cursor: grab;
            color: #6c757d;
            font-size: 1.2em;
        }
        
        .drag-handle:active {
            cursor: grabbing;
        }
        
        .sortable-ghost {
            opacity: 0.4;
        }
        
        .add-button {
            border: 2px dashed #007bff;
            background: rgba(0,123,255,0.05);
            color: #007bff;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .add-button:hover {
            background: rgba(0,123,255,0.1);
            border-color: #0056b3;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .criteria-builder {
            max-height: 70vh;
            overflow-y: auto;
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
                    <a class="nav-link" href="#users">
                        <i class="fas fa-users me-2"></i>จัดการบัญชีผู้ใช้
                    </a>
                    <a class="nav-link active" href="#criteria">
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
                                <i class="fas fa-list-alt text-primary me-2"></i>
                                จัดการเกณฑ์การให้คะแนน
                            </h1>
                            <p class="text-muted mb-0">สร้างและจัดการเกณฑ์การให้คะแนนสำหรับแต่ละประเภทสิ่งประดิษฐ์</p>
                        </div>
                        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createCriteriaModal">
                            <i class="fas fa-plus me-2"></i>สร้างเกณฑ์ใหม่
                        </button>
                    </div>
                </div>

                <!-- Criteria Types Tabs -->
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="criteriaTypeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="science-tab" data-bs-toggle="tab" data-bs-target="#science" type="button" role="tab">
                                    <i class="fas fa-flask me-2"></i>วิทยาศาสตร์
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="technology-tab" data-bs-toggle="tab" data-bs-target="#technology" type="button" role="tab">
                                    <i class="fas fa-microchip me-2"></i>เทคโนโลยี
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="innovation-tab" data-bs-toggle="tab" data-bs-target="#innovation" type="button" role="tab">
                                    <i class="fas fa-lightbulb me-2"></i>นวัตกรรม
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="environment-tab" data-bs-toggle="tab" data-bs-target="#environment" type="button" role="tab">
                                    <i class="fas fa-leaf me-2"></i>สิ่งแวดล้อม
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="tab-content" id="criteriaTypeTabsContent">
                        <!-- Science Tab -->
                        <div class="tab-pane fade show active" id="science" role="tabpanel">
                            <div class="criteria-builder">
                                <!-- Criteria Section 1 -->
                                <div class="criteria-section" data-section-id="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-grip-vertical drag-handle me-2"></i>
                                            <h5 class="mb-0 text-primary">1. เอกสารประกอบการนำเสนอผลงานสิ่งประดิษฐ์ฯ และคู่มือประกอบการใช้งาน</h5>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <span class="badge bg-primary">รวม 15 คะแนน</span>
                                            <button class="btn btn-outline-warning btn-sm ms-2" onclick="editSection(1)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteSection(1)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Sub-criteria -->
                                    <div class="sub-criteria" data-subcriteria-id="1-1">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-grip-vertical drag-handle me-2"></i>
                                                <h6 class="mb-0">1.1 แบบเสนอโครงการวิจัยสิ่งประดิษฐ์ฯ ตามแบบ ว-สอศ-2</h6>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <span class="badge bg-success">2 คะแนน</span>
                                                <button class="btn btn-outline-warning btn-sm ms-2" onclick="editSubCriteria('1-1')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="deleteSubCriteria('1-1')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Questions -->
                                        <div class="question-item" data-question-id="1-1-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-grip-vertical drag-handle me-2"></i>
                                                        <strong>ความชัดเจนถูกต้องของข้อมูล/รายละเอียด</strong>
                                                    </div>
                                                    <div class="score-levels">
                                                        <div class="score-level excellent">
                                                            <span>ดีมาก</span>
                                                            <span class="badge bg-light text-dark ms-1">2</span>
                                                        </div>
                                                        <div class="score-level good">
                                                            <span>ดี</span>
                                                            <span class="badge bg-light text-dark ms-1">1.5</span>
                                                        </div>
                                                        <div class="score-level fair">
                                                            <span>พอใช้</span>
                                                            <span class="badge bg-light text-dark ms-1">1</span>
                                                        </div>
                                                        <div class="score-level poor">
                                                            <span>ปรับปรุง</span>
                                                            <span class="badge bg-light text-dark ms-1">0.5</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-outline-warning btn-sm" onclick="editQuestion('1-1-1')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteQuestion('1-1-1')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="add-button" onclick="addQuestion('1-1')">
                                            <i class="fas fa-plus me-2"></i>เพิ่มคำถามใหม่
                                        </div>
                                    </div>
                                    
                                    <!-- Sub-criteria 1.2 -->
                                    <div class="sub-criteria" data-subcriteria-id="1-2">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-grip-vertical drag-handle me-2"></i>
                                                <h6 class="mb-0">1.2 คู่มือการใช้งานสิ่งประดิษฐ์</h6>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <span class="badge bg-success">5 คะแนน</span>
                                                <button class="btn btn-outline-warning btn-sm ms-2" onclick="editSubCriteria('1-2')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="deleteSubCriteria('1-2')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="question-item" data-question-id="1-2-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-grip-vertical drag-handle me-2"></i>
                                                        <strong>ความครบถ้วนของคู่มือการใช้งาน</strong>
                                                    </div>
                                                    <div class="score-levels">
                                                        <div class="score-level excellent">
                                                            <span>ดีมาก</span>
                                                            <span class="badge bg-light text-dark ms-1">5</span>
                                                        </div>
                                                        <div class="score-level good">
                                                            <span>ดี</span>
                                                            <span class="badge bg-light text-dark ms-1">4</span>
                                                        </div>
                                                        <div class="score-level fair">
                                                            <span>พอใช้</span>
                                                            <span class="badge bg-light text-dark ms-1">3</span>
                                                        </div>
                                                        <div class="score-level poor">
                                                            <span>ปรับปรุง</span>
                                                            <span class="badge bg-light text-dark ms-1">2</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-outline-warning btn-sm" onclick="editQuestion('1-2-1')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteQuestion('1-2-1')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="add-button" onclick="addQuestion('1-2')">
                                            <i class="fas fa-plus me-2"></i>เพิ่มคำถามใหม่
                                        </div>
                                    </div>
                                    
                                    <div class="add-button" onclick="addSubCriteria(1)">
                                        <i class="fas fa-plus me-2"></i>เพิ่มหัวข้อย่อยใหม่
                                    </div>
                                </div>
                                
                                <!-- Add Section Button -->
                                <div class="add-button" onclick="addSection()">
                                    <i class="fas fa-plus me-2"></i>เพิ่มหัวข้อหลักใหม่
                                </div>
                            </div>
                        </div>
                        
                        <!-- Other tabs content would go here -->
                        <div class="tab-pane fade" id="technology" role="tabpanel">
                            <div class="criteria-builder">
                                <div class="text-center py-5">
                                    <i class="fas fa-microchip text-muted" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3 text-muted">เกณฑ์การให้คะแนนประเภทเทคโนโลยี</h4>
                                    <p class="text-muted">ยังไม่มีเกณฑ์การให้คะแนนสำหรับประเภทนี้</p>
                                    <button class="btn btn-primary" onclick="addSection()">
                                        <i class="fas fa-plus me-2"></i>สร้างเกณฑ์แรก
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="innovation" role="tabpanel">
                            <div class="criteria-builder">
                                <div class="text-center py-5">
                                    <i class="fas fa-lightbulb text-muted" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3 text-muted">เกณฑ์การให้คะแนนประเภทนวัตกรรม</h4>
                                    <p class="text-muted">ยังไม่มีเกณฑ์การให้คะแนนสำหรับประเภทนี้</p>
                                    <button class="btn btn-primary" onclick="addSection()">
                                        <i class="fas fa-plus me-2"></i>สร้างเกณฑ์แรก
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="environment" role="tabpanel">
                            <div class="criteria-builder">
                                <div class="text-center py-5">
                                    <i class="fas fa-leaf text-muted" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3 text-muted">เกณฑ์การให้คะแนนประเภทสิ่งแวดล้อม</h4>
                                    <p class="text-muted">ยังไม่มีเกณฑ์การให้คะแนนสำหรับประเภทนี้</p>
                                    <button class="btn btn-primary" onclick="addSection()">
                                        <i class="fas fa-plus me-2"></i>สร้างเกณฑ์แรก
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <button class="btn btn-outline-secondary" onclick="previewCriteria()">
                            <i class="fas fa-eye me-2"></i>ดูตัวอย่าง
                        </button>
                        <button class="btn btn-outline-info" onclick="exportCriteria()">
                            <i class="fas fa-download me-2"></i>ส่งออก
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-secondary me-2" onclick="saveDraft()">
                            <i class="fas fa-save me-2"></i>บันทึกร่าง
                        </button>
                        <button class="btn btn-success" onclick="publishCriteria()">
                            <i class="fas fa-check me-2"></i>เผยแพร่เกณฑ์
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Criteria Modal -->
    <div class="modal fade" id="createCriteriaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>สร้างเกณฑ์การให้คะแนนใหม่
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createCriteriaForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ประเภทสิ่งประดิษฐ์ <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="">เลือกประเภท</option>
                                <option value="science">วิทยาศาสตร์</option>
                                <option value="technology">เทคโนโลยี</option>
                                <option value="innovation">นวัตกรรม</option>
                                <option value="environment">สิ่งแวดล้อม</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อเกณฑ์การให้คะแนน <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="เช่น เกณฑ์การให้คะแนนสิ่งประดิษฐ์ประเภทวิทยาศาสตร์ ปี 2568" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">คำอธิบาย</label>
                            <textarea class="form-control" rows="3" placeholder="รายละเอียดของเกณฑ์การให้คะแนน..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">คะแนนเต็ม</label>
                                <input type="number" class="form-control" placeholder="100" min="1" max="1000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">สถานะ</label>
                                <select class="form-select">
                                    <option value="draft">ร่าง</option>
                                    <option value="active">ใช้งาน</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="createNewCriteria()">
                        <i class="fas fa-save me-1"></i>สร้างเกณฑ์
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sortablejs/1.15.0/Sortable.min.js"></script>
    <script>
        // Initialize drag and drop functionality
        document.addEventListener('DOMContentLoaded', function() {
            initializeSortable();
        });
        
        function initializeSortable() {
            // Make sections sortable
            const criteriaBuilder = document.querySelector('.criteria-builder');
            if (criteriaBuilder) {
                new Sortable(criteriaBuilder, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost'
                });
            }
            
            // Make sub-criteria sortable within each section
            document.querySelectorAll('.criteria-section').forEach(section => {
                new Sortable(section, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost'
                });
            });
            
            // Make questions sortable within each sub-criteria
            document.querySelectorAll('.sub-criteria').forEach(subCriteria => {
                new Sortable(subCriteria, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost'
                });
            });
        }
        
        // Section management functions
        function addSection() {
            console.log('Add new section');
            // Implementation for adding new section
        }
        
        function editSection(id) {
            console.log('Edit section:', id);
            // Implementation for editing section
        }
        
        function deleteSection(id) {
            if (confirm('ต้องการลบหัวข้อหลักนี้หรือไม่?')) {
                console.log('Delete section:', id);
                // Implementation for deleting section
            }
        }
        
        // Sub-criteria management functions
        function addSubCriteria(sectionId) {
            console.log('Add sub-criteria to section:', sectionId);
            // Implementation for adding sub-criteria
        }
        
        function editSubCriteria(id) {
            console.log('Edit sub-criteria:', id);
            // Implementation for editing sub-criteria
        }
        
        function deleteSubCriteria(id) {
            if (confirm('ต้องการลบหัวข้อย่อยนี้หรือไม่?')) {
                console.log('Delete sub-criteria:', id);
                // Implementation for deleting sub-criteria
            }
        }
        
        // Question management functions
        function addQuestion(subCriteriaId) {
            console.log('Add question to sub-criteria:', subCriteriaId);
            // Implementation for adding question
        }
        
        function editQuestion(id) {
            console.log('Edit question:', id);
            // Implementation for editing question
        }
        
        function deleteQuestion(id) {
            if (confirm('ต้องการลบคำถามนี้หรือไม่?')) {
                console.log('Delete question:', id);
                // Implementation for deleting question
            }
        }
        
        // Criteria management functions
        function createNewCriteria() {
            const form = document.getElementById('createCriteriaForm');
            if (form.checkValidity()) {
                console.log('Create new criteria');
                alert('สร้างเกณฑ์การให้คะแนนใหม่สำเร็จ!');
                bootstrap.Modal.getInstance(document.getElementById('createCriteriaModal')).hide();
                // Implementation for creating new criteria
            } else {
                form.reportValidity();
            }
        }
        
        function previewCriteria() {
            console.log('Preview criteria');
            // Implementation for previewing criteria
        }
        
        function exportCriteria() {
            console.log('Export criteria');
            alert('ส่งออกเกณฑ์การให้คะแนนสำเร็จ!');
        }
        
        function saveDraft() {
            console.log('Save draft');
            alert('บันทึกร่างสำเร็จ!');
        }
        
        function publishCriteria() {
            if (confirm('ต้องการเผยแพร่เกณฑ์การให้คะแนนหรือไม่? หลังจากเผยแพร่แล้วจะไม่สามารถแก้ไขได้')) {
                console.log('Publish criteria');
                alert('เผยแพร่เกณฑ์การให้คะแนนสำเร็จ!');
            }
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
    </script>
</body>
</html>