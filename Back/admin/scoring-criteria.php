<?php
// admin/scoring-criteria.php
$page_title = 'จัดการเกณฑ์การให้คะแนน';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get category_id from URL if exists
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$category_info = null;

if ($category_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category_info = $stmt->fetch();
    
    if (!$category_info) {
        setMessage('ไม่พบประเภทสิ่งประดิษฐ์ที่ระบุ', 'error');
        header('Location: categories.php');
        exit();
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_criteria':
                $name = sanitize($_POST['name']);
                $description = sanitize($_POST['description']);
                $category_id = (int)$_POST['category_id'];
                $max_score = (float)$_POST['max_score'];
                
                if (empty($name) || $category_id <= 0 || $max_score <= 0) {
                    setMessage('กรุณากรอกข้อมูลที่จำเป็น', 'error');
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO scoring_criteria (category_id, name, description, max_score, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $stmt->execute([$category_id, $name, $description, $max_score]);
                        $criteria_id = $pdo->lastInsertId();
                        
                        logActivity($_SESSION['user_id'], 'สร้างเกณฑ์การให้คะแนน', "สร้างเกณฑ์: $name", $pdo);
                        setMessage('สร้างเกณฑ์การให้คะแนนเรียบร้อยแล้ว', 'success');
                        
                        // Redirect to add sub-criteria
                        header("Location: scoring-criteria.php?category_id=$category_id&edit_criteria=$criteria_id");
                        exit();
                        
                    } catch (PDOException $e) {
                        setMessage('เกิดข้อผิดพลาดในการสร้างเกณฑ์การให้คะแนน', 'error');
                    }
                }
                break;
                
            case 'create_sub_criteria':
                $criteria_id = (int)$_POST['criteria_id'];
                $name = sanitize($_POST['name']);
                $description = sanitize($_POST['description']);
                $max_score = (float)$_POST['max_score'];
                
                if (empty($name) || $criteria_id <= 0 || $max_score <= 0) {
                    setMessage('กรุณากรอกข้อมูลที่จำเป็น', 'error');
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO scoring_sub_criteria (criteria_id, name, description, max_score, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $stmt->execute([$criteria_id, $name, $description, $max_score]);
                        $sub_criteria_id = $pdo->lastInsertId();
                        
                        logActivity($_SESSION['user_id'], 'สร้างหัวข้อย่อย', "สร้างหัวข้อย่อย: $name", $pdo);
                        setMessage('สร้างหัวข้อย่อยเรียบร้อยแล้ว', 'success');
                        
                        // Redirect to add scoring levels
                        header("Location: scoring-criteria.php?category_id=$category_id&edit_criteria=$criteria_id&edit_sub=$sub_criteria_id");
                        exit();
                        
                    } catch (PDOException $e) {
                        setMessage('เกิดข้อผิดพลาดในการสร้างหัวข้อย่อย', 'error');
                    }
                }
                break;
                
            case 'create_scoring_level':
                $sub_criteria_id = (int)$_POST['sub_criteria_id'];
                $level_name = sanitize($_POST['level_name']);
                $level_description = sanitize($_POST['level_description']);
                $score = (float)$_POST['score'];
                
                if (empty($level_name) || $sub_criteria_id <= 0 || $score < 0) {
                    setMessage('กรุณากรอกข้อมูลที่จำเป็น', 'error');
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO scoring_levels (sub_criteria_id, level_name, level_description, score, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $stmt->execute([$sub_criteria_id, $level_name, $level_description, $score]);
                        
                        logActivity($_SESSION['user_id'], 'สร้างระดับคะแนน', "สร้างระดับคะแนน: $level_name", $pdo);
                        setMessage('สร้างระดับคะแนนเรียบร้อยแล้ว', 'success');
                        
                    } catch (PDOException $e) {
                        setMessage('เกิดข้อผิดพลาดในการสร้างระดับคะแนน', 'error');
                    }
                }
                break;
                
            case 'delete_criteria':
                $criteria_id = (int)$_POST['criteria_id'];
                try {
                    // Delete related data first
                    $pdo->prepare("DELETE sl FROM scoring_levels sl 
                                   INNER JOIN scoring_sub_criteria ssc ON sl.sub_criteria_id = ssc.id 
                                   WHERE ssc.criteria_id = ?")->execute([$criteria_id]);
                    $pdo->prepare("DELETE FROM scoring_sub_criteria WHERE criteria_id = ?")->execute([$criteria_id]);
                    $pdo->prepare("DELETE FROM scoring_criteria WHERE id = ?")->execute([$criteria_id]);
                    
                    logActivity($_SESSION['user_id'], 'ลบเกณฑ์การให้คะแนน', "ลบเกณฑ์ ID: $criteria_id", $pdo);
                    setMessage('ลบเกณฑ์การให้คะแนนเรียบร้อยแล้ว', 'success');
                    
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการลบเกณฑ์การให้คะแนน', 'error');
                }
                break;
        }
        
        // Redirect to prevent form resubmission
        $redirect_url = 'scoring-criteria.php';
        if ($category_id > 0) {
            $redirect_url .= '?category_id=' . $category_id;
        }
        header("Location: $redirect_url");
        exit();
    }
}

// Get categories for dropdown
$categories_stmt = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name");
$categories = $categories_stmt->fetchAll();

// Get scoring criteria for selected category
$scoring_criteria = [];
if ($category_id > 0) {
    $sql = "SELECT sc.*, 
                   COUNT(ssc.id) as sub_criteria_count,
                   SUM(ssc.max_score) as total_sub_score
            FROM scoring_criteria sc
            LEFT JOIN scoring_sub_criteria ssc ON sc.id = ssc.criteria_id
            WHERE sc.category_id = ?
            GROUP BY sc.id
            ORDER BY sc.created_at";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category_id]);
    $scoring_criteria = $stmt->fetchAll();
}

// Get detailed criteria if editing
$edit_criteria_id = isset($_GET['edit_criteria']) ? (int)$_GET['edit_criteria'] : 0;
$edit_sub_criteria_id = isset($_GET['edit_sub']) ? (int)$_GET['edit_sub'] : 0;
$criteria_details = null;

if ($edit_criteria_id > 0) {
    // Get criteria with sub-criteria and scoring levels
    $sql = "SELECT sc.*,
                   ssc.id as sub_id, ssc.name as sub_name, ssc.description as sub_description, ssc.max_score as sub_max_score,
                   sl.id as level_id, sl.level_name, sl.level_description, sl.score
            FROM scoring_criteria sc
            LEFT JOIN scoring_sub_criteria ssc ON sc.id = ssc.criteria_id
            LEFT JOIN scoring_levels sl ON ssc.id = sl.sub_criteria_id
            WHERE sc.id = ?
            ORDER BY ssc.id, sl.score DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$edit_criteria_id]);
    $results = $stmt->fetchAll();
    
    if (!empty($results)) {
        $criteria_details = [
            'id' => $results[0]['id'],
            'name' => $results[0]['name'],
            'description' => $results[0]['description'],
            'max_score' => $results[0]['max_score'],
            'sub_criteria' => []
        ];
        
        foreach ($results as $row) {
            if ($row['sub_id']) {
                if (!isset($criteria_details['sub_criteria'][$row['sub_id']])) {
                    $criteria_details['sub_criteria'][$row['sub_id']] = [
                        'id' => $row['sub_id'],
                        'name' => $row['sub_name'],
                        'description' => $row['sub_description'],
                        'max_score' => $row['sub_max_score'],
                        'levels' => []
                    ];
                }
                
                if ($row['level_id']) {
                    $criteria_details['sub_criteria'][$row['sub_id']]['levels'][] = [
                        'id' => $row['level_id'],
                        'level_name' => $row['level_name'],
                        'level_description' => $row['level_description'],
                        'score' => $row['score']
                    ];
                }
            }
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<?php
$breadcrumb_items = [
    ['title' => 'หน้าหลัก', 'url' => 'dashboard.php'],
];

if ($category_info) {
    $breadcrumb_items[] = ['title' => 'จัดการประเภทสิ่งประดิษฐ์', 'url' => 'categories.php'];
    $breadcrumb_items[] = ['title' => 'เกณฑ์การให้คะแนน - ' . $category_info['name']];
} else {
    $breadcrumb_items[] = ['title' => 'จัดการเกณฑ์การให้คะแนน'];
}

echo generateBreadcrumb($breadcrumb_items);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-list-check me-2"></i>
        จัดการเกณฑ์การให้คะแนน
        <?php if ($category_info): ?>
            <small class="text-muted">- <?php echo htmlspecialchars($category_info['name']); ?></small>
        <?php endif; ?>
    </h2>
</div>

<!-- Category Selection -->
<?php if (!$category_id): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">เลือกประเภทสิ่งประดิษฐ์</h5>
    </div>
    <div class="card-body">
        <form method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="category_id" class="form-label">ประเภทสิ่งประดิษฐ์</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">เลือกประเภทสิ่งประดิษฐ์</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> ดูเกณฑ์การให้คะแนน
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($category_id > 0): ?>
<!-- Criteria Management -->
<div class="row">
    <!-- Criteria List -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">เกณฑ์การให้คะแนน</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCriteriaModal">
                    <i class="bi bi-plus-circle me-1"></i>เพิ่มเกณฑ์ใหม่
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($scoring_criteria)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-list-check display-1 text-muted"></i>
                        <h4 class="text-muted mt-3">ยังไม่มีเกณฑ์การให้คะแนน</h4>
                        <p class="text-muted">เริ่มต้นด้วยการสร้างเกณฑ์การให้คะแนนแรก</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCriteriaModal">
                            <i class="bi bi-plus-circle me-2"></i>สร้างเกณฑ์การให้คะแนน
                        </button>
                    </div>
                <?php else: ?>
                    <div class="accordion" id="criteriaAccordion">
                        <?php foreach ($scoring_criteria as $index => $criteria): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $criteria['id']; ?>">
                                <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" 
                                        type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo $criteria['id']; ?>">
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo htmlspecialchars($criteria['name']); ?></strong>
                                                <span class="badge bg-primary ms-2">
                                                    รวม <?php echo number_format($criteria['max_score'], 1); ?> คะแนน
                                                </span>
                                                <span class="badge bg-info ms-1">
                                                    <?php echo $criteria['sub_criteria_count']; ?> หัวข้อย่อย
                                                </span>
                                            </div>
                                        </div>
                                        <?php if ($criteria['description']): ?>
                                            <small class="text-muted d-block mt-1">
                                                <?php echo htmlspecialchars($criteria['description']); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $criteria['id']; ?>" 
                                 class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                                 data-bs-parent="#criteriaAccordion">
                                <div class="accordion-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted">หัวข้อย่อยการให้คะแนน</span>
                                        <div class="btn-group" role="group">
                                            <a href="?category_id=<?php echo $category_id; ?>&edit_criteria=<?php echo $criteria['id']; ?>" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye me-1"></i>จัดการ
                                            </a>
                                            <form method="POST" class="d-inline" 
                                                  onsubmit="return confirmDelete('คุณแน่ใจหรือไม่ที่จะลบเกณฑ์นี้? ข้อมูลทั้งหมดจะถูกลบ')">
                                                <input type="hidden" name="action" value="delete_criteria">
                                                <input type="hidden" name="criteria_id" value="<?php echo $criteria['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <?php if ($criteria['sub_criteria_count'] > 0): ?>
                                        <p class="small text-success mb-2">
                                            <i class="bi bi-check-circle me-1"></i>
                                            มี <?php echo $criteria['sub_criteria_count']; ?> หัวข้อย่อย 
                                            (คะแนนรวม <?php echo number_format($criteria['total_sub_score'], 1); ?> คะแนน)
                                        </p>
                                    <?php else: ?>
                                        <p class="small text-warning mb-2">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            ยังไม่มีหัวข้อย่อย กรุณาเพิ่มหัวข้อย่อยการให้คะแนน
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">สรุปข้อมูล</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="h3 text-primary mb-1"><?php echo count($scoring_criteria); ?></div>
                        <div class="small text-muted">เกณฑ์หลัก</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="h3 text-info mb-1">
                            <?php echo array_sum(array_column($scoring_criteria, 'sub_criteria_count')); ?>
                        </div>
                        <div class="small text-muted">หัวข้อย่อย</div>
                    </div>
                </div>
                <div class="text-center">
                    <div class="h4 text-success mb-1">
                        <?php echo number_format(array_sum(array_column($scoring_criteria, 'max_score')), 1); ?>
                    </div>
                    <div class="small text-muted">คะแนนเต็มรวม</div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">การดำเนินการ</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createCriteriaModal">
                        <i class="bi bi-plus-circle me-2"></i>เพิ่มเกณฑ์ใหม่
                    </button>
                    <a href="categories.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>กลับไปยังประเภท
                    </a>
                    <?php if (!empty($scoring_criteria)): ?>
                    <a href="scoring-criteria-preview.php?category_id=<?php echo $category_id; ?>" 
                       class="btn btn-outline-info" target="_blank">
                        <i class="bi bi-eye me-2"></i>ดูตัวอย่าง
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Criteria Editor -->
<?php if ($criteria_details): ?>
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-gear me-2"></i>จัดการเกณฑ์: <?php echo htmlspecialchars($criteria_details['name']); ?>
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Sub-criteria list -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6>หัวข้อย่อยการให้คะแนน</h6>
                    <button type="button" class="btn btn-success btn-sm" 
                            data-bs-toggle="modal" data-bs-target="#createSubCriteriaModal">
                        <i class="bi bi-plus-circle me-1"></i>เพิ่มหัวข้อย่อย
                    </button>
                </div>
                
                <?php if (empty($criteria_details['sub_criteria'])): ?>
                    <div class="text-center py-4 bg-light rounded">
                        <i class="bi bi-list display-4 text-muted"></i>
                        <p class="text-muted mt-2">ยังไม่มีหัวข้อย่อย</p>
                        <button type="button" class="btn btn-success" 
                                data-bs-toggle="modal" data-bs-target="#createSubCriteriaModal">
                            <i class="bi bi-plus-circle me-2"></i>เพิ่มหัวข้อย่อยแรก
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($criteria_details['sub_criteria'] as $sub): ?>
                    <div class="card mb-3 border-start border-4 border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title">
                                        <?php echo htmlspecialchars($sub['name']); ?>
                                        <span class="badge bg-info ms-2"><?php echo number_format($sub['max_score'], 1); ?> คะแนน</span>
                                    </h6>
                                    <?php if ($sub['description']): ?>
                                        <p class="card-text text-muted small">
                                            <?php echo htmlspecialchars($sub['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <!-- Scoring Levels -->
                                    <div class="mt-2">
                                        <small class="text-muted">ระดับการให้คะแนน:</small>
                                        <?php if (empty($sub['levels'])): ?>
                                            <div class="alert alert-warning alert-sm mt-1">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                ยังไม่มีระดับการให้คะแนน
                                            </div>
                                        <?php else: ?>
                                            <div class="row mt-1">
                                                <?php foreach ($sub['levels'] as $level): ?>
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="border rounded p-2 bg-light">
                                                        <div class="d-flex justify-content-between">
                                                            <strong class="text-primary"><?php echo htmlspecialchars($level['level_name']); ?></strong>
                                                            <span class="badge bg-primary"><?php echo number_format($level['score'], 1); ?></span>
                                                        </div>
                                                        <?php if ($level['level_description']): ?>
                                                            <small class="text-muted">
                                                                <?php echo htmlspecialchars($level['level_description']); ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="btn-group-vertical ms-3" role="group">
                                    <a href="?category_id=<?php echo $category_id; ?>&edit_criteria=<?php echo $edit_criteria_id; ?>&edit_sub=<?php echo $sub['id']; ?>" 
                                       class="btn btn-sm btn-outline-success" title="จัดการระดับคะแนน">
                                        <i class="bi bi-star"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Scoring Level Editor -->
            <div class="col-lg-4">
                <?php if ($edit_sub_criteria_id > 0): ?>
                    <?php
                    $edit_sub = isset($criteria_details['sub_criteria'][$edit_sub_criteria_id]) 
                        ? $criteria_details['sub_criteria'][$edit_sub_criteria_id] : null;
                    ?>
                    <?php if ($edit_sub): ?>
                    <div class="card bg-light">
                        <div class="card-header">
                            <h6 class="mb-0">จัดการระดับคะแนน</h6>
                            <small class="text-muted"><?php echo htmlspecialchars($edit_sub['name']); ?></small>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="create_scoring_level">
                                <input type="hidden" name="sub_criteria_id" value="<?php echo $edit_sub['id']; ?>">
                                
                                <div class="mb-3">
                                    <label for="level_name" class="form-label">ชื่อระดับ</label>
                                    <select class="form-select" id="level_name" name="level_name" required>
                                        <option value="">เลือกระดับ</option>
                                        <option value="ดีมาก">ดีมาก</option>
                                        <option value="ดี">ดี</option>
                                        <option value="พอใช้">พอใช้</option>
                                        <option value="ปรับปรุง">ปรับปรุง</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="score" class="form-label">คะแนน</label>
                                    <input type="number" class="form-control" id="score" name="score" 
                                           min="0" max="<?php echo $edit_sub['max_score']; ?>" 
                                           step="0.1" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="level_description" class="form-label">คำอธิบาย</label>
                                    <textarea class="form-control" id="level_description" name="level_description" rows="2"
                                              placeholder="คำอธิบายเกณฑ์การให้คะแนนในระดับนี้"></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-plus-circle me-2"></i>เพิ่มระดับคะแนน
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Create Criteria Modal -->
<div class="modal fade" id="createCriteriaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มเกณฑ์การให้คะแนนใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_criteria">
                    <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
                    
                    <div class="mb-3">
                        <label for="criteria_name" class="form-label">ชื่อเกณฑ์การให้คะแนน <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="criteria_name" name="name" required 
                               placeholder="เช่น เอกสารประกอบการนำเสนอผลงานสิ่งประดิษฐ์ฯ และคู่มือประกอบการใช้งาน">
                    </div>
                    
                    <div class="mb-3">
                        <label for="criteria_description" class="form-label">คำอธิบาย</label>
                        <textarea class="form-control" id="criteria_description" name="description" rows="3"
                                  placeholder="อธิบายรายละเอียดของเกณฑ์การให้คะแนนนี้"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="criteria_max_score" class="form-label">คะแนนเต็ม <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="criteria_max_score" name="max_score" 
                               min="1" max="100" step="0.1" required placeholder="15">
                        <div class="form-text">คะแนนเต็มของเกณฑ์นี้</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>สร้างเกณฑ์
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Sub-Criteria Modal -->
<?php if ($criteria_details): ?>
<div class="modal fade" id="createSubCriteriaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มหัวข้อย่อย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_sub_criteria">
                    <input type="hidden" name="criteria_id" value="<?php echo $criteria_details['id']; ?>">
                    
                    <div class="mb-3">
                        <label for="sub_name" class="form-label">ชื่อหัวข้อย่อย <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sub_name" name="name" required 
                               placeholder="เช่น แบบเสนอโครงการวิจัยสิ่งประดิษฐ์ฯ ตามแบบ ว-สอศ-2">
                    </div>
                    
                    <div class="mb-3">
                        <label for="sub_description" class="form-label">คำอธิบาย</label>
                        <textarea class="form-control" id="sub_description" name="description" rows="2"
                                  placeholder="ความชัดเจนถูกต้องของข้อมูล/รายละเอียด"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sub_max_score" class="form-label">คะแนนเต็ม <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="sub_max_score" name="max_score" 
                               min="0.1" max="<?php echo $criteria_details['max_score']; ?>" 
                               step="0.1" required placeholder="2">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>เพิ่มหัวข้อย่อย
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<?php
$page_scripts = '
<script>
    // Auto-submit category selection
    document.getElementById("category_id")?.addEventListener("change", function() {
        if (this.value) {
            this.form.submit();
        }
    });
</script>
';

require_once 'includes/footer.php';
?>