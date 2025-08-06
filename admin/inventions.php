<?php
// admin/inventions.php
$page_title = 'จัดการสิ่งประดิษฐ์';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'approve':
                $invention_id = (int)$_POST['invention_id'];
                try {
                    $stmt = $pdo->prepare("UPDATE inventions SET status = 'approved', approved_at = NOW(), approved_by = ? WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id'], $invention_id]);
                    
                    logActivity($_SESSION['user_id'], 'อนุมัติสิ่งประดิษฐ์', "อนุมัติสิ่งประดิษฐ์ ID: $invention_id", $pdo);
                    setMessage('อนุมัติสิ่งประดิษฐ์เรียบร้อยแล้ว', 'success');
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการอนุมัติ', 'error');
                }
                break;
                
            case 'reject':
                $invention_id = (int)$_POST['invention_id'];
                $reject_reason = sanitize($_POST['reject_reason']);
                try {
                    $stmt = $pdo->prepare("UPDATE inventions SET status = 'rejected', reject_reason = ?, rejected_at = NOW(), rejected_by = ? WHERE id = ?");
                    $stmt->execute([$reject_reason, $_SESSION['user_id'], $invention_id]);
                    
                    logActivity($_SESSION['user_id'], 'ปฏิเสธสิ่งประดิษฐ์', "ปฏิเสธสิ่งประดิษฐ์ ID: $invention_id", $pdo);
                    setMessage('ปฏิเสธสิ่งประดิษฐ์เรียบร้อยแล้ว', 'success');
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการปฏิเสธ', 'error');
                }
                break;
                
            case 'delete':
                $invention_id = (int)$_POST['invention_id'];
                try {
                    $stmt = $pdo->prepare("UPDATE inventions SET status = 'deleted' WHERE id = ?");
                    $stmt->execute([$invention_id]);
                    
                    logActivity($_SESSION['user_id'], 'ลบสิ่งประดิษฐ์', "ลบสิ่งประดิษฐ์ ID: $invention_id", $pdo);
                    setMessage('ลบสิ่งประดิษฐ์เรียบร้อยแล้ว', 'success');
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการลบ', 'error');
                }
                break;
        }
        
        header('Location: inventions.php' . (isset($_GET['competition_id']) ? '?competition_id=' . $_GET['competition_id'] : ''));
        exit();
    }
}

// Pagination and filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$competition_filter = isset($_GET['competition_id']) ? (int)$_GET['competition_id'] : 0;
$category_filter = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Build query
$where_conditions = ["i.status != 'deleted'"];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(i.name LIKE ? OR i.description LIKE ? OR i.school_name LIKE ? OR i.inventor_names LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($competition_filter > 0) {
    $where_conditions[] = "i.competition_id = ?";
    $params[] = $competition_filter;
}

if ($category_filter > 0) {
    $where_conditions[] = "i.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($status_filter)) {
    $where_conditions[] = "i.status = ?";
    $params[] = $status_filter;
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count
$count_sql = "SELECT COUNT(*) FROM inventions i WHERE $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $per_page);

// Get inventions
$sql = "SELECT i.*, c.name as competition_name, cat.name as category_name,
               u.full_name as approved_by_name
        FROM inventions i
        LEFT JOIN competitions c ON i.competition_id = c.id
        LEFT JOIN categories cat ON i.category_id = cat.id
        LEFT JOIN users u ON i.approved_by = u.id
        WHERE $where_clause 
        ORDER BY i.created_at DESC 
        LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inventions = $stmt->fetchAll();

// Get competitions for filter
$competitions_stmt = $pdo->query("SELECT id, name FROM competitions WHERE status != 'deleted' ORDER BY name");
$competitions = $competitions_stmt->fetchAll();

// Get categories for filter
$categories_stmt = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name");
$categories = $categories_stmt->fetchAll();

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<?php
echo generateBreadcrumb([
    ['title' => 'หน้าหลัก', 'url' => 'dashboard.php'],
    ['title' => 'จัดการสิ่งประดิษฐ์']
]);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-lightbulb me-2"></i>จัดการสิ่งประดิษฐ์</h2>
    <a href="inventions/create.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>ลงทะเบียนสิ่งประดิษฐ์
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">ค้นหา</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="ชื่อสิ่งประดิษฐ์, โรงเรียน, นักประดิษฐ์">
            </div>
            
            <div class="col-md-3">
                <label for="competition_id" class="form-label">รายการแข่งขัน</label>
                <select class="form-select" id="competition_id" name="competition_id">
                    <option value="">ทุกรายการ</option>
                    <?php foreach ($competitions as $comp): ?>
                        <option value="<?php echo $comp['id']; ?>" 
                                <?php echo $competition_filter == $comp['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($comp['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="category_id" class="form-label">ประเภท</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">ทุกประเภท</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                                <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">สถานะ</label>
                <select class="form-select" id="status" name="status">
                    <option value="">ทุกสถานะ</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>รออนุมัติ</option>
                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>อนุมัติแล้ว</option>
                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>ปฏิเสธ</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Results -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            รายการสิ่งประดิษฐ์ 
            <span class="badge bg-secondary"><?php echo number_format($total_records); ?> รายการ</span>
        </h5>
        
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-success" onclick="exportInventions()">
                <i class="bi bi-download"></i> Export
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" onclick="printInventions()">
                <i class="bi bi-printer"></i> พิมพ์
            </button>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($inventions)): ?>
            <div class="text-center py-5">
                <i class="bi bi-lightbulb display-1 text-muted"></i>
                <h4 class="text-muted mt-3">ไม่พบสิ่งประดิษฐ์</h4>
                <p class="text-muted">ไม่มีสิ่งประดิษฐ์ที่ตรงกับเงื่อนไขการค้นหา</p>
                <a href="inventions/create.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>ลงทะเบียนสิ่งประดิษฐ์ใหม่
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="inventionsTable">
                    <thead>
                        <tr>
                            <th style="width: 25%">ชื่อสิ่งประดิษฐ์</th>
                            <th style="width: 15%">โรงเรียน</th>
                            <th style="width: 15%">นักประดิษฐ์</th>
                            <th style="width: 10%">ประเภท</th>
                            <th style="width: 10%">รายการแข่งขัน</th>
                            <th style="width: 10%">สถานะ</th>
                            <th style="width: 15%">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventions as $invention): ?>
                        <tr>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($invention['name']); ?></strong>
                                    <?php if (!empty($invention['description'])): ?>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars(substr($invention['description'], 0, 80)); ?>
                                            <?php echo strlen($invention['description']) > 80 ? '...' : ''; ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($invention['school_name']); ?></td>
                            <td>
                                <div class="small">
                                    <?php 
                                    $inventors = explode(',', $invention['inventor_names']);
                                    foreach (array_slice($inventors, 0, 2) as $inventor): ?>
                                        <div><?php echo htmlspecialchars(trim($inventor)); ?></div>
                                    <?php endforeach; ?>
                                    <?php if (count($inventors) > 2): ?>
                                        <div class="text-muted">และอีก <?php echo count($inventors) - 2; ?> คน</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo htmlspecialchars($invention['category_name']); ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($invention['competition_name']); ?>
                                </small>
                            </td>
                            <td>
                                <?php
                                $status_class = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $status_text = [
                                    'pending' => 'รออนุมัติ',
                                    'approved' => 'อนุมัติแล้ว',
                                    'rejected' => 'ปฏิเสธ'
                                ];
                                ?>
                                <span class="badge bg-<?php echo $status_class[$invention['status']] ?? 'secondary'; ?>">
                                    <?php echo $status_text[$invention['status']] ?? $invention['status']; ?>
                                </span>
                                
                                <?php if ($invention['status'] === 'approved' && $invention['approved_by_name']): ?>
                                    <br>
                                    <small class="text-muted">
                                        โดย: <?php echo htmlspecialchars($invention['approved_by_name']); ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="inventions/view.php?id=<?php echo $invention['id']; ?>" 
                                       class="btn btn-sm btn-outline-info" title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <?php if ($invention['status'] === 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal<?php echo $invention['id']; ?>" 
                                                title="อนุมัติ">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal<?php echo $invention['id']; ?>" 
                                                title="ปฏิเสธ">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown" title="เพิ่มเติม">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="inventions/edit.php?id=<?php echo $invention['id']; ?>" 
                                                   class="dropdown-item">
                                                    <i class="bi bi-pencil me-2"></i>แก้ไข
                                                </a>
                                            </li>
                                            
                                            <?php if ($invention['status'] === 'approved'): ?>
                                            <li>
                                                <a href="scoring/invention.php?id=<?php echo $invention['id']; ?>" 
                                                   class="dropdown-item">
                                                    <i class="bi bi-star me-2"></i>ดูคะแนน
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <li>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirmDelete('คุณแน่ใจหรือไม่ที่จะลบสิ่งประดิษฐ์นี้?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="invention_id" value="<?php echo $invention['id']; ?>">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i>ลบ
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="mt-4">
                    <?php
                    echo generatePagination($page, $total_pages, 'inventions.php', [
                        'search' => $search,
                        'competition_id' => $competition_filter,
                        'category_id' => $category_filter,
                        'status' => $status_filter
                    ]);
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modals for approval and rejection -->
<?php foreach ($inventions as $invention): ?>
    <?php if ($invention['status'] === 'pending'): ?>
        <!-- Approve Modal -->
        <div class="modal fade" id="approveModal<?php echo $invention['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">อนุมัติสิ่งประดิษฐ์</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <p>คุณต้องการอนุมัติสิ่งประดิษฐ์ "<strong><?php echo htmlspecialchars($invention['name']); ?></strong>" หรือไม่?</p>
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="invention_id" value="<?php echo $invention['id']; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-success">อนุมัติ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal<?php echo $invention['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">ปฏิเสธสิ่งประดิษฐ์</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <p>สิ่งประดิษฐ์: "<strong><?php echo htmlspecialchars($invention['name']); ?></strong>"</p>
                            
                            <div class="mb-3">
                                <label for="reject_reason<?php echo $invention['id']; ?>" class="form-label">เหตุผลในการปฏิเสธ <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reject_reason<?php echo $invention['id']; ?>" 
                                          name="reject_reason" rows="3" required 
                                          placeholder="กรุณาระบุเหตุผลในการปฏิเสธ"></textarea>
                            </div>
                            
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="invention_id" value="<?php echo $invention['id']; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-warning">ปฏิเสธ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php
$page_scripts = '
<script>
    // Live search functionality
    let searchTimeout;
    document.getElementById("search").addEventListener("input", function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
    
    // Auto-submit when filter changes
    ["competition_id", "category_id", "status"].forEach(function(id) {
        document.getElementById(id).addEventListener("change", function() {
            this.form.submit();
        });
    });
    
    // Export functions
    function exportInventions() {
        const params = new URLSearchParams(window.location.search);
        params.set("export", "csv");
        window.location.href = "?" + params.toString();
    }
    
    function printInventions() {
        const params = new URLSearchParams(window.location.search);
        params.set("print", "1");
        window.open("?" + params.toString(), "_blank");
    }
</script>
';

require_once 'includes/footer.php';
?>