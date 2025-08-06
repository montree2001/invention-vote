<?php
// admin/scoring-control.php
$page_title = 'ควบคุมการให้คะแนน';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'toggle_voting':
                $competition_id = (int)$_POST['competition_id'];
                $status = sanitize($_POST['status']);
                
                try {
                    if ($status === 'start') {
                        $stmt = $pdo->prepare("UPDATE competitions SET voting_start = NOW(), status = 'judging' WHERE id = ?");
                    } else {
                        $stmt = $pdo->prepare("UPDATE competitions SET voting_end = NOW(), status = 'completed' WHERE id = ?");
                    }
                    $stmt->execute([$competition_id]);
                    
                    $action_text = $status === 'start' ? 'เปิด' : 'ปิด';
                    logActivity($_SESSION['user_id'], "${action_text}การลงคะแนน", "รายการแข่งขัน ID: $competition_id", $pdo);
                    setMessage("${action_text}การลงคะแนนเรียบร้อยแล้ว", 'success');
                    
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ', 'error');
                }
                break;
                
            case 'toggle_scoring_point':
                $competition_id = (int)$_POST['competition_id'];
                $category_id = (int)$_POST['category_id'];
                $sub_criteria_id = (int)$_POST['sub_criteria_id'];
                $is_enabled = (int)$_POST['is_enabled'];
                
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO scoring_point_settings (competition_id, category_id, sub_criteria_id, is_enabled, updated_by, updated_at) 
                        VALUES (?, ?, ?, ?, ?, NOW()) 
                        ON DUPLICATE KEY UPDATE is_enabled = ?, updated_by = ?, updated_at = NOW()
                    ");
                    $stmt->execute([$competition_id, $category_id, $sub_criteria_id, $is_enabled, $_SESSION['user_id'], $is_enabled, $_SESSION['user_id']]);
                    
                    $action_text = $is_enabled ? 'เปิด' : 'ปิด';
                    logActivity($_SESSION['user_id'], "${action_text}จุดให้คะแนน", "จุดให้คะแนน ID: $sub_criteria_id", $pdo);
                    setMessage("${action_text}จุดให้คะแนนเรียบร้อยแล้ว", 'success');
                    
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการตั้งค่าจุดให้คะแนน', 'error');
                }
                break;
                
            case 'restrict_judge':
                $invention_id = (int)$_POST['invention_id'];
                $judge_id = (int)$_POST['judge_id'];
                $restriction_type = sanitize($_POST['restriction_type']);
                $reason = sanitize($_POST['reason']);
                
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO voting_restrictions (invention_id, judge_id, restriction_type, reason, restricted_by, created_at) 
                        VALUES (?, ?, ?, ?, ?, NOW())
                        ON DUPLICATE KEY UPDATE restriction_type = ?, reason = ?, restricted_by = ?, created_at = NOW()
                    ");
                    $stmt->execute([$invention_id, $judge_id, $restriction_type, $reason, $_SESSION['user_id'], $restriction_type, $reason, $_SESSION['user_id']]);
                    
                    logActivity($_SESSION['user_id'], 'จำกัดสิทธิ์การลงคะแนน', "กรรมการ ID: $judge_id, สิ่งประดิษฐ์ ID: $invention_id", $pdo);
                    setMessage('จำกัดสิทธิ์การลงคะแนนเรียบร้อยแล้ว', 'success');
                    
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการจำกัดสิทธิ์', 'error');
                }
                break;
                
            case 'cancel_vote':
                $invention_id = (int)$_POST['invention_id'];
                $judge_id = (int)$_POST['judge_id'];
                
                try {
                    $stmt = $pdo->prepare("DELETE FROM voting_scores WHERE invention_id = ? AND judge_id = ?");
                    $stmt->execute([$invention_id, $judge_id]);
                    
                    logActivity($_SESSION['user_id'], 'ยกเลิกการลงคะแนน', "กรรมการ ID: $judge_id, สิ่งประดิษฐ์ ID: $invention_id", $pdo);
                    setMessage('ยกเลิกการลงคะแนนเรียบร้อยแล้ว', 'success');
                    
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการยกเลิกการลงคะแนน', 'error');
                }
                break;
                
            case 'cancel_approval':
                $competition_id = (int)$_POST['competition_id'];
                $category_id = (int)$_POST['category_id'];
                
                try {
                    $stmt = $pdo->prepare("UPDATE result_approvals SET is_active = 0 WHERE competition_id = ? AND category_id = ?");
                    $stmt->execute([$competition_id, $category_id]);
                    
                    logActivity($_SESSION['user_id'], 'ยกเลิกการรับรองผล', "รายการแข่งขัน ID: $competition_id, ประเภท ID: $category_id", $pdo);
                    setMessage('ยกเลิกการรับรองผลเรียบร้อยแล้ว', 'success');
                    
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการยกเลิกการรับรองผล', 'error');
                }
                break;
        }
        
        header('Location: scoring-control.php');
        exit();
    }
}

// Get filter parameters
$competition_id = isset($_GET['competition_id']) ? (int)$_GET['competition_id'] : 0;
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Get competitions
$competitions_stmt = $pdo->query("SELECT id, name, status, voting_start, voting_end FROM competitions WHERE status != 'deleted' ORDER BY created_at DESC");
$competitions = $competitions_stmt->fetchAll();

// Get categories for selected competition
$categories = [];
if ($competition_id > 0) {
    $stmt = $pdo->prepare("
        SELECT DISTINCT c.id, c.name 
        FROM categories c 
        JOIN inventions i ON c.id = i.category_id 
        WHERE i.competition_id = ? AND i.status = 'approved'
        ORDER BY c.name
    ");
    $stmt->execute([$competition_id]);
    $categories = $stmt->fetchAll();
}

// Get competition info
$competition_info = null;
if ($competition_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM competitions WHERE id = ?");
    $stmt->execute([$competition_id]);
    $competition_info = $stmt->fetch();
}

// Get voting progress for selected competition
$voting_progress = [];
if ($competition_id > 0) {
    $sql = "SELECT 
                jvp.*,
                (SELECT COUNT(*) FROM result_approvals ra 
                 WHERE ra.competition_id = jvp.competition_id 
                 AND ra.category_id = jvp.category_id 
                 AND ra.is_active = 1) as is_approved
            FROM judge_voting_progress jvp 
            WHERE jvp.competition_id = ?";
    
    $params = [$competition_id];
    
    if ($category_id > 0) {
        $sql .= " AND jvp.category_id = ?";
        $params[] = $category_id;
    }
    
    $sql .= " ORDER BY jvp.category_id, jvp.voting_percentage DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $voting_progress = $stmt->fetchAll();
}

// Get scoring point settings
$scoring_points = [];
if ($competition_id > 0 && $category_id > 0) {
    $sql = "SELECT 
                scm.id as main_criteria_id,
                scm.criteria_name as main_criteria_name,
                scs.id as sub_criteria_id,
                scs.sub_criteria_name,
                scs.max_score,
                COALESCE(sps.is_enabled, 1) as is_enabled
            FROM scoring_criteria_main scm
            JOIN scoring_criteria_sub scs ON scm.id = scs.main_criteria_id
            LEFT JOIN scoring_point_settings sps ON scs.id = sps.sub_criteria_id 
                AND sps.competition_id = ? AND sps.category_id = ?
            WHERE scm.category_id = ? AND scm.is_active = 1
            ORDER BY scm.order_no, scs.order_no";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$competition_id, $category_id, $category_id]);
    $scoring_points = $stmt->fetchAll();
}

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<?php
echo generateBreadcrumb([
    ['title' => 'หน้าหลัก', 'url' => 'dashboard.php'],
    ['title' => 'ควบคุมการให้คะแนน']
]);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-toggles me-2"></i>ควบคุมการให้คะแนน</h2>
</div>

<!-- Competition Selection -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">เลือกรายการแข่งขัน</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="competition_id" class="form-label">รายการแข่งขัน</label>
                <select class="form-select" id="competition_id" name="competition_id">
                    <option value="">เลือกรายการแข่งขัน</option>
                    <?php foreach ($competitions as $comp): ?>
                        <option value="<?php echo $comp['id']; ?>" 
                                <?php echo $competition_id == $comp['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($comp['name']); ?>
                            <span class="text-muted">
                                (<?php echo getCompetitionStatus()[$comp['status']] ?? $comp['status']; ?>)
                            </span>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="category_id" class="form-label">ประเภท (สำหรับจุดคะแนน)</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">เลือกประเภท</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                                <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> ดูข้อมูล
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($competition_info): ?>
<!-- Competition Control -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-play-circle me-2"></i>ควบคุมการลงคะแนน
                </h5>
            </div>
            <div class="card-body">
                <h6><?php echo htmlspecialchars($competition_info['name']); ?></h6>
                
                <div class="mb-3">
                    <strong>สถานะปัจจุบัน:</strong>
                    <?php
                    $status_class = [
                        'draft' => 'secondary',
                        'active' => 'success', 
                        'judging' => 'warning',
                        'completed' => 'info'
                    ];
                    $statuses = getCompetitionStatus();
                    ?>
                    <span class="badge bg-<?php echo $status_class[$competition_info['status']] ?? 'secondary'; ?>">
                        <?php echo $statuses[$competition_info['status']] ?? $competition_info['status']; ?>
                    </span>
                </div>
                
                <?php if ($competition_info['voting_start']): ?>
                    <p><strong>เริ่มลงคะแนน:</strong> <?php echo formatThaiDate($competition_info['voting_start']); ?></p>
                <?php endif; ?>
                
                <?php if ($competition_info['voting_end']): ?>
                    <p><strong>สิ้นสุดลงคะแนน:</strong> <?php echo formatThaiDate($competition_info['voting_end']); ?></p>
                <?php endif; ?>
                
                <div class="d-grid gap-2">
                    <?php if ($competition_info['status'] === 'active'): ?>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="toggle_voting">
                            <input type="hidden" name="competition_id" value="<?php echo $competition_info['id']; ?>">
                            <input type="hidden" name="status" value="start">
                            <button type="submit" class="btn btn-success w-100" 
                                    onclick="return confirm('คุณแน่ใจหรือไม่ที่จะเปิดการลงคะแนน?')">
                                <i class="bi bi-play-fill me-2"></i>เปิดการลงคะแนน
                            </button>
                        </form>
                    <?php elseif ($competition_info['status'] === 'judging'): ?>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="toggle_voting">
                            <input type="hidden" name="competition_id" value="<?php echo $competition_info['id']; ?>">
                            <input type="hidden" name="status" value="stop">
                            <button type="submit" class="btn btn-warning w-100" 
                                    onclick="return confirm('คุณแน่ใจหรือไม่ที่จะปิดการลงคะแนน?')">
                                <i class="bi bi-pause-fill me-2"></i>ปิดการลงคะแนน
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart me-2"></i>สถิติการลงคะแนน
                </h5>
            </div>
            <div class="card-body">
                <?php
                // Calculate overall statistics
                $total_judges = count($voting_progress);
                $completed_judges = 0;
                $total_percentage = 0;
                
                foreach ($voting_progress as $progress) {
                    if ($progress['voting_percentage'] >= 100) {
                        $completed_judges++;
                    }
                    $total_percentage += $progress['voting_percentage'];
                }
                
                $avg_percentage = $total_judges > 0 ? $total_percentage / $total_judges : 0;
                ?>
                
                <div class="row text-center">
                    <div class="col-4">
                        <div class="h4 text-primary"><?php echo $total_judges; ?></div>
                        <small class="text-muted">กรรมการทั้งหมด</small>
                    </div>
                    <div class="col-4">
                        <div class="h4 text-success"><?php echo $completed_judges; ?></div>
                        <small class="text-muted">ลงครบแล้ว</small>
                    </div>
                    <div class="col-4">
                        <div class="h4 text-warning"><?php echo number_format($avg_percentage, 1); ?>%</div>
                        <small class="text-muted">ความคืบหน้าเฉลี่ย</small>
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" style="width: <?php echo $avg_percentage; ?>%">
                            <?php echo number_format($avg_percentage, 1); ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scoring Points Control -->
<?php if ($category_id > 0 && !empty($scoring_points)): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-toggles2 me-2"></i>จัดการจุดให้คะแนน
        </h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            คุณสามารถเปิด-ปิดจุดให้คะแนนแต่ละหัวข้อได้ กรรมการจะมองเห็นเฉพาะจุดที่เปิดเท่านั้น
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>เกณฑ์หลัก</th>
                        <th>หัวข้อย่อย</th>
                        <th>คะแนนเต็ม</th>
                        <th>สถานะ</th>
                        <th>การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $current_main = '';
                    foreach ($scoring_points as $point):
                    ?>
                    <tr>
                        <td>
                            <?php if ($current_main !== $point['main_criteria_name']): ?>
                                <strong><?php echo htmlspecialchars($point['main_criteria_name']); ?></strong>
                                <?php $current_main = $point['main_criteria_name']; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($point['sub_criteria_name']); ?></td>
                        <td>
                            <span class="badge bg-primary"><?php echo number_format($point['max_score'], 1); ?></span>
                        </td>
                        <td>
                            <?php if ($point['is_enabled']): ?>
                                <span class="badge bg-success">เปิด</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">ปิด</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="toggle_scoring_point">
                                <input type="hidden" name="competition_id" value="<?php echo $competition_id; ?>">
                                <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
                                <input type="hidden" name="sub_criteria_id" value="<?php echo $point['sub_criteria_id']; ?>">
                                <input type="hidden" name="is_enabled" value="<?php echo $point['is_enabled'] ? 0 : 1; ?>">
                                
                                <?php if ($point['is_enabled']): ?>
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="ปิดจุดคะแนน">
                                        <i class="bi bi-toggle-on"></i> ปิด
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="เปิดจุดคะแนน">
                                        <i class="bi bi-toggle-off"></i> เปิด
                                    </button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Judge Progress -->
<?php if (!empty($voting_progress)): ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-people me-2"></i>ความคืบหน้าของกรรมการ
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>กรรมการ</th>
                        <th>สถานศึกษา</th>
                        <th>ประเภท</th>
                        <th>สิ่งประดิษฐ์ทั้งหมด</th>
                        <th>ลงคะแนนแล้ว</th>
                        <th>ความคืบหน้า</th>
                        <th>สถานะการรับรอง</th>
                        <th>การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($voting_progress as $progress): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($progress['first_name'] . ' ' . $progress['last_name']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($progress['institution_name']); ?></td>
                        <td>
                            <span class="badge bg-info">
                                <?php
                                // Get category name
                                foreach ($categories as $cat) {
                                    if ($cat['id'] == $progress['category_id']) {
                                        echo htmlspecialchars($cat['name']);
                                        break;
                                    }
                                }
                                ?>
                            </span>
                        </td>
                        <td><?php echo $progress['total_inventions']; ?></td>
                        <td><?php echo $progress['voted_inventions']; ?></td>
                        <td>
                            <?php
                            $percentage = $progress['voting_percentage'];
                            $progress_class = $percentage >= 100 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                            ?>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-<?php echo $progress_class; ?>" 
                                     style="width: <?php echo $percentage; ?>%">
                                    <?php echo number_format($percentage, 1); ?>%
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($progress['is_approved'] > 0): ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>รับรองแล้ว
                                </span>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="cancel_approval">
                                    <input type="hidden" name="competition_id" value="<?php echo $competition_id; ?>">
                                    <input type="hidden" name="category_id" value="<?php echo $progress['category_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning ms-1" 
                                            onclick="return confirm('คุณแน่ใจหรือไม่ที่จะยกเลิกการรับรองผล?')" 
                                            title="ยกเลิกการรับรอง">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-secondary">ยังไม่รับรอง</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        onclick="viewJudgeDetails(<?php echo $progress['judge_id']; ?>, <?php echo $progress['category_id']; ?>)" 
                                        title="ดูรายละเอียด">
                                    <i class="bi bi-eye"></i>
                                </button>
                                
                                <?php if ($progress['voted_inventions'] > 0): ?>
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#resetVotesModal" 
                                            onclick="setResetData(<?php echo $progress['judge_id']; ?>, '<?php echo htmlspecialchars($progress['first_name'] . ' ' . $progress['last_name']); ?>')" 
                                            title="รีเซ็ตการลงคะแนน">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                        onclick="restrictJudge(<?php echo $progress['judge_id']; ?>)" 
                                        title="จำกัดสิทธิ์">
                                    <i class="bi bi-person-x"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reset Votes Modal -->
<div class="modal fade" id="resetVotesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รีเซ็ตการลงคะแนน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    การดำเนินการนี้จะลบการลงคะแนนทั้งหมดของกรรมการและไม่สามารถยกเลิกได้
                </div>
                <p>คุณต้องการรีเซ็ตการลงคะแนนของ <strong id="judgeName"></strong> หรือไม่?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning" onclick="confirmResetVotes()">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>รีเซ็ตการลงคะแนน
                </button>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php endif; ?>

<?php
$page_scripts = '
<script>
    let selectedJudgeId = null;
    
    // Auto-submit when competition changes
    document.getElementById("competition_id").addEventListener("change", function() {
        document.getElementById("category_id").value = "";
        this.form.submit();
    });
    
    // Auto-submit when category changes
    document.getElementById("category_id").addEventListener("change", function() {
        this.form.submit();
    });
    
    // View judge details
    function viewJudgeDetails(judgeId, categoryId) {
        // Implementation for viewing judge details
        // This could open a modal or redirect to a detailed page
        console.log("View details for judge:", judgeId, "category:", categoryId);
    }
    
    // Set data for reset votes modal
    function setResetData(judgeId, judgeName) {
        selectedJudgeId = judgeId;
        document.getElementById("judgeName").textContent = judgeName;
    }
    
    // Confirm reset votes
    function confirmResetVotes() {
        if (selectedJudgeId) {
            // Create form and submit
            const form = document.createElement("form");
            form.method = "POST";
            form.innerHTML = `
                <input type="hidden" name="action" value="reset_judge_votes">
                <input type="hidden" name="judge_id" value="${selectedJudgeId}">
                <input type="hidden" name="competition_id" value="<?php echo $competition_id; ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Restrict judge
    function restrictJudge(judgeId) {
        // Implementation for restricting judge access
        // This could open a modal for setting restrictions
        console.log("Restrict judge:", judgeId);
    }
</script>
';

require_once 'includes/footer.php';
?>