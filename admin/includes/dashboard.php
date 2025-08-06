<?php
// admin/dashboard.php
$page_title = 'หน้าหลัก';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get statistics
try {
    // Total competitions
    $stmt = $pdo->query("SELECT COUNT(*) FROM competitions WHERE status != 'deleted'");
    $total_competitions = $stmt->fetchColumn();
    
    // Active competitions
    $stmt = $pdo->query("SELECT COUNT(*) FROM competitions WHERE status = 'active'");
    $active_competitions = $stmt->fetchColumn();
    
    // Total inventions
    $stmt = $pdo->query("SELECT COUNT(*) FROM inventions WHERE status != 'deleted'");
    $total_inventions = $stmt->fetchColumn();
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'");
    $total_users = $stmt->fetchColumn();
    
    // Recent competitions
    $stmt = $pdo->prepare("SELECT * FROM competitions WHERE status != 'deleted' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_competitions = $stmt->fetchAll();
    
    // Recent activities
    $stmt = $pdo->prepare("
        SELECT al.*, u.full_name 
        FROM activity_logs al 
        LEFT JOIN users u ON al.user_id = u.id 
        ORDER BY al.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recent_activities = $stmt->fetchAll();
    
    // Competitions by status
    $stmt = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM competitions 
        WHERE status != 'deleted' 
        GROUP BY status
    ");
    $competitions_by_status = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $total_competitions = $active_competitions = $total_inventions = $total_users = 0;
    $recent_competitions = $recent_activities = $competitions_by_status = [];
}

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<?php
echo generateBreadcrumb([
    ['title' => 'หน้าหลัก']
]);
?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number"><?php echo number_format($total_competitions); ?></div>
                    <div>รายการแข่งขันทั้งหมด</div>
                </div>
                <div class="fs-1">
                    <i class="bi bi-trophy"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="display-6 text-success mb-2"><?php echo number_format($active_competitions); ?></div>
                <h6 class="card-title">กำลังดำเนินการ</h6>
                <small class="text-muted">รายการแข่งขันที่เปิดอยู่</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="display-6 text-info mb-2"><?php echo number_format($total_inventions); ?></div>
                <h6 class="card-title">สิ่งประดิษฐ์</h6>
                <small class="text-muted">ที่ลงทะเบียนทั้งหมด</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="display-6 text-warning mb-2"><?php echo number_format($total_users); ?></div>
                <h6 class="card-title">ผู้ใช้งาน</h6>
                <small class="text-muted">ที่ใช้งานระบบ</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Competitions -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">รายการแข่งขันล่าสุด</h5>
                <a href="competitions.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>จัดการทั้งหมด
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_competitions)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-trophy display-1 text-muted"></i>
                        <p class="text-muted mt-2">ยังไม่มีรายการแข่งขัน</p>
                        <a href="competitions.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>สร้างรายการแข่งขัน
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ชื่อรายการ</th>
                                    <th>ระดับ</th>
                                    <th>สถานะ</th>
                                    <th>วันที่สร้าง</th>
                                    <th>การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_competitions as $competition): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($competition['name']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($competition['description'] ?? ''); ?></small>
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
                                    <td><?php echo formatThaiDate($competition['created_at']); ?></td>
                                    <td>
                                        <a href="competitions/edit.php?id=<?php echo $competition['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                            <i class="bi bi-pencil"></i>
                                        </a>
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
    
    <!-- Quick Actions & Statistics -->
    <div class="col-lg-4 mb-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">การดำเนินการด่วน</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="competitions/create.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>สร้างรายการแข่งขัน
                    </a>
                    <a href="categories.php" class="btn btn-outline-primary">
                        <i class="bi bi-tags me-2"></i>จัดการประเภทสิ่งประดิษฐ์
                    </a>
                    <a href="users.php" class="btn btn-outline-primary">
                        <i class="bi bi-people me-2"></i>จัดการผู้ใช้งาน
                    </a>
                    <a href="reports.php" class="btn btn-outline-primary">
                        <i class="bi bi-graph-up me-2"></i>ดูรายงาน
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Competition Status Chart -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">สถานะรายการแข่งขัน</h5>
            </div>
            <div class="card-body">
                <?php if (empty($competitions_by_status)): ?>
                    <div class="text-center py-3">
                        <i class="bi bi-pie-chart display-4 text-muted"></i>
                        <p class="text-muted mt-2">ไม่มีข้อมูล</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($competitions_by_status as $status_data): ?>
                        <?php
                        $statuses = getCompetitionStatus();
                        $status_name = $statuses[$status_data['status']] ?? $status_data['status'];
                        $percentage = $total_competitions > 0 ? round(($status_data['count'] / $total_competitions) * 100, 1) : 0;
                        $progress_class = [
                            'draft' => 'secondary',
                            'active' => 'success',
                            'judging' => 'warning',
                            'completed' => 'info',
                            'cancelled' => 'danger'
                        ];
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><?php echo $status_name; ?></span>
                                <span><?php echo $status_data['count']; ?> รายการ</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-<?php echo $progress_class[$status_data['status']] ?? 'secondary'; ?>" 
                                     style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <small class="text-muted"><?php echo $percentage; ?>%</small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">กิจกรรมล่าสุด</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_activities)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-clock-history display-1 text-muted"></i>
                        <p class="text-muted mt-2">ไม่มีกิจกรรมล่าสุด</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ผู้ใช้</th>
                                    <th>การดำเนินการ</th>
                                    <th>รายละเอียด</th>
                                    <th>IP Address</th>
                                    <th>เวลา</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activities as $activity): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($activity['full_name'] ?? 'ไม่ระบุ'); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($activity['action']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($activity['details']); ?></td>
                                    <td><code><?php echo htmlspecialchars($activity['ip_address']); ?></code></td>
                                    <td>
                                        <small><?php echo formatThaiDate($activity['created_at']); ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="statistics.php" class="btn btn-outline-primary">
                            ดูกิจกรรมทั้งหมด
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$page_scripts = '
<script>
    // Auto refresh dashboard every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);
    
    // Add some interactivity to the statistics cards
    document.querySelectorAll(".stats-card, .card").forEach(function(card) {
        card.addEventListener("mouseenter", function() {
            this.style.transform = "translateY(-5px)";
        });
        
        card.addEventListener("mouseleave", function() {
            this.style.transform = "translateY(0)";
        });
    });
</script>
';

require_once 'includes/footer.php';
?>