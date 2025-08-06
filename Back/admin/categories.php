<?php
// admin/categories.php
$page_title = 'จัดการประเภทสิ่งประดิษฐ์';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $name = sanitize($_POST['name']);
                $description = sanitize($_POST['description']);
                $max_participants = (int)$_POST['max_participants'];
                
                if (empty($name)) {
                    setMessage('กรุณากรอกชื่อประเภทสิ่งประดิษฐ์', 'error');
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO categories (name, description, max_participants, created_at) VALUES (?, ?, ?, NOW())");
                        $stmt->execute([$name, $description, $max_participants]);
                        
                        logActivity($_SESSION['user_id'], 'สร้างประเภทสิ่งประดิษฐ์', "สร้างประเภท: $name", $pdo);
                        setMessage('สร้างประเภทสิ่งประดิษฐ์เรียบร้อยแล้ว', 'success');
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            setMessage('ชื่อประเภทสิ่งประดิษฐ์นี้มีอยู่แล้ว', 'error');
                        } else {
                            setMessage('เกิดข้อผิดพลาดในการสร้างประเภทสิ่งประดิษฐ์', 'error');
                        }
                    }
                }
                break;
                
            case 'edit':
                $category_id = (int)$_POST['category_id'];
                $name = sanitize($_POST['name']);
                $description = sanitize($_POST['description']);
                $max_participants = (int)$_POST['max_participants'];
                $status = sanitize($_POST['status']);
                
                if (empty($name)) {
                    setMessage('กรุณากรอกชื่อประเภทสิ่งประดิษฐ์', 'error');
                } else {
                    try {
                        $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, max_participants = ?, status = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$name, $description, $max_participants, $status, $category_id]);
                        
                        logActivity($_SESSION['user_id'], 'แก้ไขประเภทสิ่งประดิษฐ์', "แก้ไขประเภท ID: $category_id", $pdo);
                        setMessage('แก้ไขประเภทสิ่งประดิษฐ์เรียบร้อยแล้ว', 'success');
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            setMessage('ชื่อประเภทสิ่งประดิษฐ์นี้มีอยู่แล้ว', 'error');
                        } else {
                            setMessage('เกิดข้อผิดพลาดในการแก้ไขประเภทสิ่งประดิษฐ์', 'error');
                        }
                    }
                }
                break;
                
            case 'delete':
                $category_id = (int)$_POST['category_id'];
                try {
                    // Check if category is being used
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM inventions WHERE category_id = ?");
                    $stmt->execute([$category_id]);
                    $usage_count = $stmt->fetchColumn();
                    
                    if ($usage_count > 0) {
                        setMessage('ไม่สามารถลบประเภทนี้ได้ เนื่องจากมีสิ่งประดิษฐ์ใช้งานอยู่', 'error');
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                        $stmt->execute([$category_id]);
                        
                        logActivity($_SESSION['user_id'], 'ลบประเภทสิ่งประดิษฐ์', "ลบประเภท ID: $category_id", $pdo);
                        setMessage('ลบประเภทสิ่งประดิษฐ์เรียบร้อยแล้ว', 'success');
                    }
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการลบประเภทสิ่งประดิษฐ์', 'error');
                }
                break;
        }
        
        header('Location: categories.php');
        exit();
    }
}

// Get categories with usage count
$sql = "SELECT c.*, 
               COUNT(i.id) as invention_count
        FROM categories c
        LEFT JOIN inventions i ON c.id = i.category_id
        GROUP BY c.id
        ORDER BY c.created_at DESC";
$stmt = $pdo->query($sql);
$categories = $stmt->fetchAll();

// Get edit category data if editing
$edit_category = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_category = $stmt->fetch();
}

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<?php
echo generateBreadcrumb([
    ['title' => 'หน้าหลัก', 'url' => 'dashboard.php'],
    ['title' => 'จัดการประเภทสิ่งประดิษฐ์']
]);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-tags me-2"></i>จัดการประเภทสิ่งประดิษฐ์</h2>
</div>

<div class="row">
    <!-- Form Section -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-<?php echo $edit_category ? 'pencil' : 'plus'; ?>-circle me-2"></i>
                    <?php echo $edit_category ? 'แก้ไขประเภท' : 'เพิ่มประเภทใหม่'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="categoryForm">
                    <input type="hidden" name="action" value="<?php echo $edit_category ? 'edit' : 'create'; ?>">
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อประเภท <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($edit_category['name'] ?? ''); ?>" 
                               required placeholder="เช่น นวัตกรรมด้านวิทยาศาสตร์">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">คำอธิบาย</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="อธิบายประเภทสิ่งประดิษฐ์นี้"><?php echo htmlspecialchars($edit_category['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="max_participants" class="form-label">จำนวนผู้เข้าร่วมสูงสุด</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" 
                               value="<?php echo $edit_category['max_participants'] ?? 0; ?>" 
                               min="0" placeholder="0 = ไม่จำกัด">
                        <div class="form-text">ใส่ 0 หากไม่ต้องการจำกัดจำนวน</div>
                    </div>
                    
                    <?php if ($edit_category): ?>
                    <div class="mb-3">
                        <label for="status" class="form-label">สถานะ</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?php echo ($edit_category['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>
                                ใช้งาน
                            </option>
                            <option value="inactive" <?php echo ($edit_category['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>
                                ไม่ใช้งาน
                            </option>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-<?php echo $edit_category ? 'check' : 'plus'; ?>-circle me-2"></i>
                            <?php echo $edit_category ? 'บันทึกการแก้ไข' : 'เพิ่มประเภท'; ?>
                        </button>
                        
                        <?php if ($edit_category): ?>
                        <a href="categories.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>ยกเลิก
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Categories List -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    รายการประเภทสิ่งประดิษฐ์
                    <span class="badge bg-secondary"><?php echo count($categories); ?> ประเภท</span>
                </h5>
                
                <div class="input-group" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm" id="searchCategories" 
                           placeholder="ค้นหาประเภท...">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (empty($categories)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-tags display-1 text-muted"></i>
                        <h4 class="text-muted mt-3">ยังไม่มีประเภทสิ่งประดิษฐ์</h4>
                        <p class="text-muted">เริ่มต้นด้วยการสร้างประเภทสิ่งประดิษฐ์แรกของคุณ</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th>ชื่อประเภท</th>
                                    <th>คำอธิบาย</th>
                                    <th>จำนวนผู้เข้าร่วมสูงสุด</th>
                                    <th>สิ่งประดิษฐ์ที่ลงทะเบียน</th>
                                    <th>สถานะ</th>
                                    <th>การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            สร้างเมื่อ: <?php echo formatThaiDate($category['created_at']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if (!empty($category['description'])): ?>
                                            <span data-bs-toggle="tooltip" 
                                                  title="<?php echo htmlspecialchars($category['description']); ?>">
                                                <?php echo htmlspecialchars(substr($category['description'], 0, 50)); ?>
                                                <?php echo strlen($category['description']) > 50 ? '...' : ''; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($category['max_participants'] > 0): ?>
                                            <span class="badge bg-info">
                                                <?php echo number_format($category['max_participants']); ?> คน
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">ไม่จำกัด</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($category['invention_count'] > 0): ?>
                                            <span class="badge bg-success">
                                                <?php echo number_format($category['invention_count']); ?> รายการ
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">0 รายการ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (($category['status'] ?? 'active') === 'active'): ?>
                                            <span class="badge bg-success">ใช้งาน</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">ไม่ใช้งาน</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="?edit=<?php echo $category['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <a href="scoring-criteria.php?category_id=<?php echo $category['id']; ?>" 
                                               class="btn btn-sm btn-outline-info" title="จัดการเกณฑ์การให้คะแนน">
                                                <i class="bi bi-list-check"></i>
                                            </a>
                                            
                                            <?php if ($category['invention_count'] == 0): ?>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirmDelete('คุณแน่ใจหรือไม่ที่จะลบประเภทนี้?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        title="ไม่สามารถลบได้ มีสิ่งประดิษฐ์ใช้งานอยู่" disabled>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$page_scripts = '
<script>
    // Form validation
    document.getElementById("categoryForm").addEventListener("submit", function(e) {
        if (!validateRequired(this)) {
            e.preventDefault();
            alert("กรุณากรอกข้อมูลที่จำเป็น");
        }
    });
    
    // Live search
    document.getElementById("searchCategories").addEventListener("input", function() {
        searchTable(this, "categoriesTable");
    });
    
    // Auto-focus on name field
    document.getElementById("name").focus();
</script>
';

require_once 'includes/footer.php';
?>