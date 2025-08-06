<?php
// admin/reports.php
$page_title = 'รายงานผลคะแนน';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get filter parameters
$competition_id = isset($_GET['competition_id']) ? (int)$_GET['competition_id'] : 0;
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$report_type = isset($_GET['type']) ? sanitize($_GET['type']) : 'progress';

// Handle export requests
if (isset($_GET['export'])) {
    $export_type = sanitize($_GET['export']);
    // Export logic will be implemented here
    exit();
}

// Get competitions for dropdown
$competitions_stmt = $pdo->query("SELECT id, name FROM competitions WHERE status != 'deleted' ORDER BY created_at DESC");
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

// Get report data based on type
$report_data = [];
$competition_info = null;
$category_info = null;

if ($competition_id > 0) {
    // Get competition info
    $stmt = $pdo->prepare("SELECT * FROM competitions WHERE id = ?");
    $stmt->execute([$competition_id]);
    $competition_info = $stmt->fetch();
    
    if ($category_id > 0) {
        // Get category info
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        $category_info = $stmt->fetch();
    }
    
    switch ($report_type) {
        case 'progress':
            // Voting progress report
            $sql = "SELECT 
                        i.id,
                        i.name as invention_name,
                        i.school_name,
                        cat.name as category_name,
                        COUNT(DISTINCT vs.judge_id) as judges_voted,
                        (SELECT COUNT(*) FROM judge_assignments ja 
                         JOIN users u ON ja.judge_id = u.id 
                         WHERE ja.competition_id = ? AND ja.category_id = i.category_id 
                         AND ja.is_active = 1) as total_judges,
                        AVG(vs.score_value) as average_score,
                        MAX(vs.voted_at) as last_voted
                    FROM inventions i
                    JOIN categories cat ON i.category_id = cat.id
                    LEFT JOIN voting_scores vs ON i.id = vs.invention_id
                    WHERE i.competition_id = ?";
            
            $params = [$competition_id, $competition_id];
            
            if ($category_id > 0) {
                $sql .= " AND i.category_id = ?";
                $params[] = $category_id;
            }
            
            $sql .= " AND i.status = 'approved'
                     GROUP BY i.id, i.name, i.school_name, cat.name
                     ORDER BY cat.name, i.name";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $report_data = $stmt->fetchAll();
            break;
            
        case 'scores':
            // Detailed scores report
            $sql = "SELECT 
                        i.id,
                        i.name as invention_name,
                        i.school_name,
                        cat.name as category_name,
                        u.full_name as judge_name,
                        u.school_name as judge_school,
                        scs.name as criteria_name,
                        scss.name as sub_criteria_name,
                        sl.level_name,
                        vs.score_value,
                        vs.voted_at
                    FROM inventions i
                    JOIN categories cat ON i.category_id = cat.id
                    JOIN voting_scores vs ON i.id = vs.invention_id
                    JOIN users u ON vs.judge_id = u.id
                    JOIN scoring_criteria_sub scss ON vs.sub_criteria_id = scss.id
                    JOIN scoring_criteria_main scs ON scss.main_criteria_id = scs.id
                    JOIN scoring_levels sl ON vs.scoring_level_id = sl.id
                    WHERE i.competition_id = ?";
            
            $params = [$competition_id];
            
            if ($category_id > 0) {
                $sql .= " AND i.category_id = ?";
                $params[] = $category_id;
            }
            
            $sql .= " AND i.status = 'approved'
                     ORDER BY cat.name, i.name, u.full_name, scs.order_no, scss.order_no";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $report_data = $stmt->fetchAll();
            break;
            
        case 'summary':
            // Competition summary report
            $sql = "SELECT 
                        i.id,
                        i.name as invention_name,
                        i.school_name,
                        i.inventor_names,
                        cat.name as category_name,
                        COUNT(DISTINCT vs.judge_id) as total_judges,
                        SUM(vs.score_value) as total_score,
                        AVG(vs.score_value) as average_score,
                        RANK() OVER (PARTITION BY i.category_id ORDER BY AVG(vs.score_value) DESC) as category_rank
                    FROM inventions i
                    JOIN categories cat ON i.category_id = cat.id
                    LEFT JOIN voting_scores vs ON i.id = vs.invention_id
                    WHERE i.competition_id = ?";
            
            $params = [$competition_id];
            
            if ($category_id > 0) {
                $sql .= " AND i.category_id = ?";
                $params[] = $category_id;
            }
            
            $sql .= " AND i.status = 'approved'
                     GROUP BY i.id, i.name, i.school_name, i.inventor_names, cat.name
                     HAVING COUNT(DISTINCT vs.judge_id) > 0
                     ORDER BY cat.name, average_score DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $report_data = $stmt->fetchAll();
            break;
            
        case 'medals':
            // Medal summary report
            $sql = "SELECT 
                        cat.name as category_name,
                        i.name as invention_name,
                        i.school_name,
                        i.inventor_names,
                        AVG(vs.score_value) as average_score,
                        RANK() OVER (PARTITION BY i.category_id ORDER BY AVG(vs.score_value) DESC) as category_rank,
                        CASE 
                            WHEN RANK() OVER (PARTITION BY i.category_id ORDER BY AVG(vs.score_value) DESC) = 1 THEN 'ทอง'
                            WHEN RANK() OVER (PARTITION BY i.category_id ORDER BY AVG(vs.score_value) DESC) = 2 THEN 'เงิน'
                            WHEN RANK() OVER (PARTITION BY i.category_id ORDER BY AVG(vs.score_value) DESC) = 3 THEN 'ทองแดง'
                            ELSE 'เข้าร่วม'
                        END as medal_type
                    FROM inventions i
                    JOIN categories cat ON i.category_id = cat.id
                    LEFT JOIN voting_scores vs ON i.id = vs.invention_id
                    WHERE i.competition_id = ?";
            
            $params = [$competition_id];
            
            if ($category_id > 0) {
                $sql .= " AND i.category_id = ?";
                $params[] = $category_id;
            }
            
            $sql .= " AND i.status = 'approved'
                     GROUP BY i.id, i.name, i.school_name, i.inventor_names, cat.name
                     HAVING COUNT(DISTINCT vs.judge_id) > 0
                     ORDER BY cat.name, average_score DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $report_data = $stmt->fetchAll();
            break;
    }
}

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<?php
echo generateBreadcrumb([
    ['title' => 'หน้าหลัก', 'url' => 'dashboard.php'],
    ['title' => 'รายงานผลคะแนน']
]);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-graph-up me-2"></i>รายงานผลคะแนน</h2>
    
    <?php if (!empty($report_data)): ?>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-success" onclick="exportReport('excel')">
            <i class="bi bi-file-excel me-1"></i>Excel
        </button>
        <button type="button" class="btn btn-outline-danger" onclick="exportReport('pdf')">
            <i class="bi bi-file-pdf me-1"></i>PDF
        </button>
        <button type="button" class="btn btn-outline-info" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>พิมพ์
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="competition_id" class="form-label">รายการแข่งขัน <span class="text-danger">*</span></label>
                <select class="form-select" id="competition_id" name="competition_id" required>
                    <option value="">เลือกรายการแข่งขัน</option>
                    <?php foreach ($competitions as $comp): ?>
                        <option value="<?php echo $comp['id']; ?>" 
                                <?php echo $competition_id == $comp['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($comp['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="category_id" class="form-label">ประเภท</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">ทุกประเภท</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                                <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="type" class="form-label">ประเภทรายงาน</label>
                <select class="form-select" id="type" name="type">
                    <option value="progress" <?php echo $report_type === 'progress' ? 'selected' : ''; ?>>
                        ความคืบหน้าการลงคะแนน
                    </option>
                    <option value="scores" <?php echo $report_type === 'scores' ? 'selected' : ''; ?>>
                        รายงานคะแนนรายละเอียด
                    </option>
                    <option value="summary" <?php echo $report_type === 'summary' ? 'selected' : ''; ?>>
                        สรุปผลการแข่งขัน
                    </option>
                    <option value="medals" <?php echo $report_type === 'medals' ? 'selected' : ''; ?>>
                        สรุปเหรียญรางวัล
                    </option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> ดูรายงาน
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($competition_info): ?>
<!-- Report Header -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-info-circle me-2"></i>
            ข้อมูลรายงาน
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>รายการแข่งขัน:</strong> <?php echo htmlspecialchars($competition_info['name']); ?><br>
                <?php if ($category_info): ?>
                    <strong>ประเภท:</strong> <?php echo htmlspecialchars($category_info['name']); ?><br>
                <?php endif; ?>
                <strong>ประเภทรายงาน:</strong> 
                <?php
                $report_names = [
                    'progress' => 'ความคืบหน้าการลงคะแนน',
                    'scores' => 'รายงานคะแนนรายละเอียด',
                    'summary' => 'สรุปผลการแข่งขัน',
                    'medals' => 'สรุปเหรียญรางวัล'
                ];
                echo $report_names[$report_type];
                ?>
            </div>
            <div class="col-md-6 text-end">
                <strong>วันที่สร้างรายงาน:</strong> <?php echo formatThaiDate(date('Y-m-d H:i:s')); ?><br>
                <strong>จำนวนข้อมูล:</strong> <?php echo number_format(count($report_data)); ?> รายการ
            </div>
        </div>
    </div>
</div>

<!-- Report Content -->
<div class="card">
    <div class="card-body">
        <?php if (empty($report_data)): ?>
            <div class="text-center py-5">
                <i class="bi bi-graph-up display-1 text-muted"></i>
                <h4 class="text-muted mt-3">ไม่มีข้อมูลรายงาน</h4>
                <p class="text-muted">ไม่พบข้อมูลที่ตรงกับเงื่อนไขที่เลือก</p>
            </div>
        <?php else: ?>
            
            <?php if ($report_type === 'progress'): ?>
                <!-- Progress Report -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ลำดับ</th>
                                <th>ชื่อสิ่งประดิษฐ์</th>
                                <th>โรงเรียน</th>
                                <th>ประเภท</th>
                                <th>กรรมการลงคะแนนแล้ว</th>
                                <th>ความคืบหน้า</th>
                                <th>คะแนนเฉลี่ย</th>
                                <th>ลงคะแนนล่าสุด</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $index => $row): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['invention_name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($row['school_name']); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($row['category_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $row['judges_voted']; ?> / <?php echo $row['total_judges']; ?> คน
                                </td>
                                <td>
                                    <?php 
                                    $progress = $row['total_judges'] > 0 ? ($row['judges_voted'] / $row['total_judges']) * 100 : 0;
                                    $progress_class = $progress >= 100 ? 'success' : ($progress >= 50 ? 'warning' : 'danger');
                                    ?>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-<?php echo $progress_class; ?>" 
                                             style="width: <?php echo $progress; ?>%">
                                            <?php echo number_format($progress, 1); ?>%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($row['average_score']): ?>
                                        <span class="badge bg-primary">
                                            <?php echo number_format($row['average_score'], 2); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['last_voted']): ?>
                                        <small><?php echo formatThaiDate($row['last_voted']); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">ยังไม่มี</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($report_type === 'summary'): ?>
                <!-- Summary Report -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>อันดับ</th>
                                <th>ชื่อสิ่งประดิษฐ์</th>
                                <th>โรงเรียน</th>
                                <th>ผู้ประดิษฐ์</th>
                                <th>ประเภท</th>
                                <th>จำนวนกรรมการ</th>
                                <th>คะแนนรวม</th>
                                <th>คะแนนเฉลี่ย</th>
                                <th>อันดับในประเภท</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $index => $row): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $index + 1; ?></span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['invention_name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($row['school_name']); ?></td>
                                <td>
                                    <small>
                                        <?php 
                                        $inventors = explode(',', $row['inventor_names']);
                                        foreach (array_slice($inventors, 0, 2) as $inventor) {
                                            echo htmlspecialchars(trim($inventor)) . '<br>';
                                        }
                                        if (count($inventors) > 2) {
                                            echo '<span class="text-muted">และอีก ' . (count($inventors) - 2) . ' คน</span>';
                                        }
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($row['category_name']); ?>
                                    </span>
                                </td>
                                <td><?php echo $row['total_judges']; ?> คน</td>
                                <td>
                                    <strong><?php echo number_format($row['total_score'], 2); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo number_format($row['average_score'], 2); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $rank_class = [1 => 'warning', 2 => 'secondary', 3 => 'dark'];
                                    $rank_bg = $rank_class[$row['category_rank']] ?? 'light';
                                    ?>
                                    <span class="badge bg-<?php echo $rank_bg; ?>">
                                        <?php echo $row['category_rank']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($report_type === 'medals'): ?>
                <!-- Medal Report -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>เหรียญ</th>
                                <th>ชื่อสิ่งประดิษฐ์</th>
                                <th>โรงเรียน</th>
                                <th>ผู้ประดิษฐ์</th>
                                <th>ประเภท</th>
                                <th>คะแนนเฉลี่ย</th>
                                <th>อันดับ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $row): ?>
                            <tr>
                                <td>
                                    <?php
                                    $medal_class = [
                                        'ทอง' => 'warning',
                                        'เงิน' => 'secondary', 
                                        'ทองแดง' => 'dark',
                                        'เข้าร่วม' => 'light'
                                    ];
                                    $medal_icon = [
                                        'ทอง' => 'bi-award-fill',
                                        'เงิน' => 'bi-award-fill',
                                        'ทองแดง' => 'bi-award-fill',
                                        'เข้าร่วม' => 'bi-award'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $medal_class[$row['medal_type']]; ?>">
                                        <i class="<?php echo $medal_icon[$row['medal_type']]; ?> me-1"></i>
                                        <?php echo $row['medal_type']; ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['invention_name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($row['school_name']); ?></td>
                                <td>
                                    <small>
                                        <?php 
                                        $inventors = explode(',', $row['inventor_names']);
                                        foreach (array_slice($inventors, 0, 2) as $inventor) {
                                            echo htmlspecialchars(trim($inventor)) . '<br>';
                                        }
                                        if (count($inventors) > 2) {
                                            echo '<span class="text-muted">และอีก ' . (count($inventors) - 2) . ' คน</span>';
                                        }
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($row['category_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo number_format($row['average_score'], 2); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo $row['category_rank']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($report_type === 'scores'): ?>
                <!-- Detailed Scores Report -->
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    รายงานคะแนนรายละเอียดจะแสดงการให้คะแนนของกรรมการแต่ละคนในแต่ละหัวข้อ
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>สิ่งประดิษฐ์</th>
                                <th>กรรมการ</th>
                                <th>เกณฑ์หลัก</th>
                                <th>หัวข้อย่อย</th>
                                <th>ระดับ</th>
                                <th>คะแนน</th>
                                <th>วันที่ลงคะแนน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $row): ?>
                            <tr>
                                <td>
                                    <small>
                                        <strong><?php echo htmlspecialchars($row['invention_name']); ?></strong><br>
                                        <span class="text-muted"><?php echo htmlspecialchars($row['school_name']); ?></span>
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        <?php echo htmlspecialchars($row['judge_name']); ?><br>
                                        <span class="text-muted"><?php echo htmlspecialchars($row['judge_school']); ?></span>
                                    </small>
                                </td>
                                <td><small><?php echo htmlspecialchars($row['criteria_name']); ?></small></td>
                                <td><small><?php echo htmlspecialchars($row['sub_criteria_name']); ?></small></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo htmlspecialchars($row['level_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo number_format($row['score_value'], 2); ?></strong>
                                </td>
                                <td>
                                    <small><?php echo formatThaiDate($row['voted_at']); ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>

<?php
$page_scripts = '
<script>
    // Auto-submit when competition changes
    document.getElementById("competition_id").addEventListener("change", function() {
        // Clear category selection when competition changes
        document.getElementById("category_id").value = "";
        this.form.submit();
    });
    
    // Auto-submit when other filters change
    ["category_id", "type"].forEach(function(id) {
        document.getElementById(id).addEventListener("change", function() {
            this.form.submit();
        });
    });
    
    // Export functions
    function exportReport(format) {
        const params = new URLSearchParams(window.location.search);
        params.set("export", format);
        window.location.href = "?" + params.toString();
    }
</script>
';

require_once 'includes/footer.php';
?>