<?php
// admin/competitions.php
$page_title = 'จัดการรายการแข่งขัน';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                $competition_id = (int)$_POST['competition_id'];
                try {
                    $stmt = $pdo->prepare("UPDATE competitions SET status = 'deleted' WHERE id = ?");
                    $stmt->execute([$competition_id]);
                    
                    logActivity($_SESSION['user_id'], 'ลบรายการแข่งขัน', "ลบรายการแข่งขัน ID: $competition_id", $pdo);
                    setMessage('ลบรายการแข่งขันเรียบร้อยแล้ว', 'success');
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการลบรายการแข่งขัน', 'error');
                }
                break;
                
            case 'toggle_status':
                $competition_id = (int)$_POST['competition_id'];
                $new_status = $_POST['new_status'];
                try {
                    $stmt = $pdo->prepare("UPDATE competitions SET status = ? WHERE id = ?");
                    $stmt->execute([$new_status, $competition_id]);
                    
                    logActivity($_SESSION['user_id'], 'เปลี่ยนสถานะรายการแข่งขัน', "เปลี่ยนสถานะเป็น: $new_status", $pdo);
                    setMessage('เปลี่ยนสถานะรายการแข่งขันเรียบร้อยแล้ว', 'success');
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ', 'error');
                }
                break;
        }
        
        header('Location: competitions.php');
        exit();
    }
}

// Pagination and search
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$level_filter = isset($_GET['level']) ? sanitize($_GET['level']) : '';

// Build query
$where_conditions = ["status != 'deleted'"];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if (!empty($level_filter)) {
    $where_conditions[] = "level = ?";
    $params[] = $level_filter;
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count
$count_sql = "SELECT COUNT(*) FROM competitions WHERE $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $per_page);

// Get competitions
$sql = "SELECT * FROM competitions WHERE $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$competitions = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<?php
echo generateBreadcrumb([
    ['title' => 'หน้าหลัก', 'url' => 'dashboard.php'],
    ['title' => 'จัดการรายการแข่งขัน']
]);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-trophy me-2"></i>จัดการรายการแข่งขัน</h2>
    <a href="competitions/create.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>สร้างรายการแข่งขัน
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">ค้นหา</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="ชื่อรายการหรือคำอธิบาย">
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">สถานะ</label>
                <select class="form-select" id="status" name="status">
                    <option value="">ทุกสถานะ</option>
                    <?php foreach (getCompetitionStatus() as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo $status_filter === $value ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="level" class="form-label">ระดับ</label>
                <select class="form-select" id="level" name="level">
                    <option value="">ทุกระดับ</option>
                    <?php foreach (getCompetitionLevels() as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo $level_filter === $value ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
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
            รายการแข่งขันทั้งหมด 
            <span class="badge bg-secondary"><?php echo number_format($total_records); ?> รายการ</span>
        </h5>
        
        <div class="btn-group" role="group">
            <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>" 
               class="btn btn-sm btn-outline-success">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($competitions)): ?>
            <div class="text-center py-5">
                <i class="bi bi-trophy display-1 text-muted"></i>
                <h4 class="text-muted mt-3">ไม่พบรายการแข่งขัน</h4>
                <p class="text-muted">ไม่มีรายการแข่งขันที่ตรงกับเงื่อนไขการค้นหา</p>
                <a href="competitions/create.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>สร้างรายการแข่งขันใหม่
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="competitionsTable">
                    <thead>
                        <tr>
                            <th>ชื่อรายการ</th>
                            <th>ระดับ</th>
                            <th>สถานะ</th>
                            <th>วันที่เริ่ม</th>
                            <th>วันที่สิ้นสุด</th>
                            <th>ผู้สร้าง</th>
                            <th>การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($competitions as $competition): ?>
                        <tr>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($competition['name']); ?></strong>
                                    <?php if (!empty($competition['description'])): ?>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars(substr($competition['description'], 0, 100)); ?>
                                            <?php echo strlen($competition['description']) > 100 ? '...' : ''; ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php 
                                $levels = getCompetitionLevels();
                                echo $levels[$competition['level']] ?? $competition['level']; 
                                ?>
                            </td>
                            <td>
                                <?php
                                $status_class = [
                                    'draft' => 'secondary',
                                    'active' => 'success',
                                    'judging' => 'warning',
                                    'completed' => 'info',
                                    'cancelled' => 'danger'
                                ];
                                $statuses = getCompetitionStatus();
                                ?>
                                <span class="badge bg-<?php echo $status_class[$competition['status']] ?? 'secondary'; ?>">
                                    <?php echo $statuses[$competition['status']] ?? $competition['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo $competition['start_date'] ? formatThaiDate($competition['start_date']) : '-'; ?>
                            </td>
                            <td>
                                <?php echo $competition['end_date'] ? formatThaiDate($competition['end_date']) : '-'; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo formatThaiDate($competition['created_at']); ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="competitions/view.php?id=<?php echo $competition['id']; ?>" 
                                       class="btn btn-sm btn-outline-info" title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <a href="competitions/edit.php?id=<?php echo $competition['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown" title="เพิ่มเติม">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php if ($competition['status'] !== 'active'): ?>
                                                <li>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="competition_id" value="<?php echo $competition['id']; ?>">
                                                        <input type="hidden" name="new_status" value="active">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-play-circle me-2"></i>เปิดใช้งาน
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php if ($competition['status'] === 'active'): ?>
                                                <li>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="competition_id" value="<?php echo $competition['id']; ?>">
                                                        <input type="hidden" name="new_status" value="draft">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-pause-circle me-2"></i>หยุดใช้งาน
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <li>
                                                <a href="inventions.php?competition_id=<?php echo $competition['id']; ?>" 
                                                   class="dropdown-item">
                                                    <i class="bi bi-lightbulb me-2"></i>จัดการสิ่งประดิษฐ์
                                                </a>
                                            </li>
                                            
                                            <li>
                                                <a href="reports.php?competition_id=<?php echo $competition['id']; ?>" 
                                                   class="dropdown-item">
                                                    <i class="bi bi-graph-up me-2"></i>รายงาน
                                                </a>
                                            </li>
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <li>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirmDelete('คุณแน่ใจหรือไม่ที่จะลบรายการแข่งขันนี้?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="competition_id" value="<?php echo $competition['id']; ?>">
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
                    echo generatePagination($page, $total_pages, 'competitions.php', [
                        'search' => $search,
                        'status' => $status_filter,
                        'level' => $level_filter
                    ]);
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

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
    document.getElementById("status").addEventListener("change", function() {
        this.form.submit();
    });
    
    document.getElementById("level").addEventListener("change", function() {
        this.form.submit();
    });
</script>
';

require_once 'includes/footer.php';
?>