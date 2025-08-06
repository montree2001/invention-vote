<?php
// admin/users.php
$page_title = 'จัดการผู้ใช้งานระบบ';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $username = sanitize($_POST['username']);
                $email = sanitize($_POST['email']);
                $full_name = sanitize($_POST['full_name']);
                $role = sanitize($_POST['role']);
                $school_name = sanitize($_POST['school_name']);
                $phone = sanitize($_POST['phone']);
                $password = generateRandomPassword();
                
                if (empty($username) || empty($email) || empty($full_name)) {
                    setMessage('กรุณากรอกข้อมูลที่จำเป็น', 'error');
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    setMessage('รูปแบบอีเมลไม่ถูกต้อง', 'error');
                } else {
                    try {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        $stmt = $pdo->prepare("
                            INSERT INTO users (username, email, password, full_name, role, school_name, phone, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                        ");
                        $stmt->execute([$username, $email, $hashed_password, $full_name, $role, $school_name, $phone]);
                        
                        logActivity($_SESSION['user_id'], 'สร้างผู้ใช้งาน', "สร้างผู้ใช้งาน: $username", $pdo);
                        setMessage("สร้างผู้ใช้งานเรียบร้อยแล้ว รหัสผ่าน: $password", 'success');
                        
                        // Send email with login credentials (if email function is implemented)
                        // sendEmail($email, 'ข้อมูลการเข้าสู่ระบบ', "Username: $username\nPassword: $password");
                        
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            setMessage('ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้ว', 'error');
                        } else {
                            setMessage('เกิดข้อผิดพลาดในการสร้างผู้ใช้งาน', 'error');
                        }
                    }
                }
                break;
                
            case 'edit':
                $user_id = (int)$_POST['user_id'];
                $username = sanitize($_POST['username']);
                $email = sanitize($_POST['email']);
                $full_name = sanitize($_POST['full_name']);
                $role = sanitize($_POST['role']);
                $school_name = sanitize($_POST['school_name']);
                $phone = sanitize($_POST['phone']);
                $status = sanitize($_POST['status']);
                
                if (empty($username) || empty($email) || empty($full_name)) {
                    setMessage('กรุณากรอกข้อมูลที่จำเป็น', 'error');
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    setMessage('รูปแบบอีเมลไม่ถูกต้อง', 'error');
                } else {
                    try {
                        $stmt = $pdo->prepare("
                            UPDATE users 
                            SET username = ?, email = ?, full_name = ?, role = ?, school_name = ?, phone = ?, status = ?, updated_at = NOW() 
                            WHERE id = ?
                        ");
                        $stmt->execute([$username, $email, $full_name, $role, $school_name, $phone, $status, $user_id]);
                        
                        logActivity($_SESSION['user_id'], 'แก้ไขผู้ใช้งาน', "แก้ไขผู้ใช้งาน ID: $user_id", $pdo);
                        setMessage('แก้ไขผู้ใช้งานเรียบร้อยแล้ว', 'success');
                        
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            setMessage('ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้ว', 'error');
                        } else {
                            setMessage('เกิดข้อผิดพลาดในการแก้ไขผู้ใช้งาน', 'error');
                        }
                    }
                }
                break;
                
            case 'reset_password':
                $user_id = (int)$_POST['user_id'];
                $new_password = generateRandomPassword();
                
                try {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$hashed_password, $user_id]);
                    
                    logActivity($_SESSION['user_id'], 'รีเซ็ตรหัสผ่าน', "รีเซ็ตรหัสผ่านผู้ใช้งาน ID: $user_id", $pdo);
                    setMessage("รีเซ็ตรหัสผ่านเรียบร้อยแล้ว รหัสผ่านใหม่: $new_password", 'success');
                    
                } catch (PDOException $e) {
                    setMessage('เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน', 'error');
                }
                break;
                
            case 'delete':
                $user_id = (int)$_POST['user_id'];
                
                // Check if user has related data
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM inventions WHERE created_by = ?");
                $stmt->execute([$user_id]);
                $invention_count = $stmt->fetchColumn();
                
                if ($invention_count > 0) {
                    setMessage('ไม่สามารถลบผู้ใช้งานนี้ได้ เนื่องจากมีข้อมูลที่เกี่ยวข้อง', 'error');
                } else {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        
                        logActivity($_SESSION['user_id'], 'ลบผู้ใช้งาน', "ลบผู้ใช้งาน ID: $user_id", $pdo);
                        setMessage('ลบผู้ใช้งานเรียบร้อยแล้ว', 'success');
                        
                    } catch (PDOException $e) {
                        setMessage('เกิดข้อผิดพลาดในการลบผู้ใช้งาน', 'error');
                    }
                }
                break;
        }
        
        header('Location: users.php');
        exit();
    }
}

// Pagination and filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? sanitize($_GET['role']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Build query
$where_conditions = ["1=1"];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(username LIKE ? OR email LIKE ? OR full_name LIKE ? OR school_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($role_filter)) {
    $where_conditions[] = "role = ?";
    $params[] = $role_filter;
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count
$count_sql = "SELECT COUNT(*) FROM users WHERE $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $per_page);

// Get users
$sql = "SELECT * FROM users WHERE $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get edit user data if editing
$edit_user = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_user = $stmt->fetch();
}

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<?php
echo generateBreadcrumb([
    ['title' => 'หน้าหลัก', 'url' => 'dashboard.php'],
    ['title' => 'จัดการผู้ใช้งานระบบ']
]);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people me-2"></i>จัดการผู้ใช้งานระบบ</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="bi bi-person-plus me-2"></i>เพิ่มผู้ใช้งาน
    </button>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">ค้นหา</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="ชื่อผู้ใช้, อีเมล, ชื่อเต็ม, โรงเรียน">
            </div>
            
            <div class="col-md-3">
                <label for="role" class="form-label">บทบาท</label>
                <select class="form-select" id="role" name="role">
                    <option value="">ทุกบทบาท</option>
                    <?php foreach (getUserRoles() as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo $role_filter === $value ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">สถานะ</label>
                <select class="form-select" id="status" name="status">
                    <option value="">ทุกสถานะ</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>ใช้งาน</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
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
            รายการผู้ใช้งาน 
            <span class="badge bg-secondary"><?php echo number_format($total_records); ?> คน</span>
        </h5>
        
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-success" onclick="exportUsers()">
                <i class="bi bi-download"></i> Export CSV
            </button>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h4 class="text-muted mt-3">ไม่พบผู้ใช้งาน</h4>
                <p class="text-muted">ไม่มีผู้ใช้งานที่ตรงกับเงื่อนไขการค้นหา</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>ชื่อผู้ใช้</th>
                            <th>ชื่อเต็ม</th>
                            <th>อีเมล</th>
                            <th>บทบาท</th>
                            <th>โรงเรียน</th>
                            <th>สถานะ</th>
                            <th>วันที่สร้าง</th>
                            <th>การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                <?php if ($user['phone']): ?>
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($user['phone']); ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </a>
                            </td>
                            <td>
                                <?php
                                $roles = getUserRoles();
                                $role_class = [
                                    'super_admin' => 'danger',
                                    'admin' => 'warning',
                                    'chairman' => 'info',
                                    'committee' => 'success'
                                ];
                                ?>
                                <span class="badge bg-<?php echo $role_class[$user['role']] ?? 'secondary'; ?>">
                                    <?php echo $roles[$user['role']] ?? $user['role']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($user['school_name'] ?? '-'); ?></td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="badge bg-success">ใช้งาน</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">ไม่ใช้งาน</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo formatThaiDate($user['created_at']); ?>
                                <?php if ($user['last_login']): ?>
                                    <br>
                                    <small class="text-muted">
                                        เข้าใช้ล่าสุด: <?php echo formatThaiDate($user['last_login']); ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['id']; ?>" 
                                            title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะรีเซ็ตรหัสผ่าน?')">
                                        <input type="hidden" name="action" value="reset_password">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="รีเซ็ตรหัสผ่าน">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    </form>
                                    
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirmDelete('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้งานนี้?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
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
                    echo generatePagination($page, $total_pages, 'users.php', [
                        'search' => $search,
                        'role' => $role_filter,
                        'status' => $status_filter
                    ]);
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มผู้ใช้งานใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="createUserForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">อีเมล <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">ชื่อเต็ม <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">บทบาท <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">เลือกบทบาท</option>
                                <?php foreach (getUserRoles() as $value => $label): ?>
                                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="school_name" class="form-label">ชื่อโรงเรียน/สถานศึกษา</label>
                        <input type="text" class="form-control" id="school_name" name="school_name">
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        รหัสผ่านจะถูกสร้างโดยอัตโนมัติและแสดงหลังจากสร้างผู้ใช้งานเรียบร้อย
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>สร้างผู้ใช้งาน
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
<?php foreach ($users as $user): ?>
<div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">แก้ไขผู้ใช้งาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_username<?php echo $user['id']; ?>" class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_username<?php echo $user['id']; ?>" 
                                   name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email<?php echo $user['id']; ?>" class="form-label">อีเมล <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email<?php echo $user['id']; ?>" 
                                   name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_full_name<?php echo $user['id']; ?>" class="form-label">ชื่อเต็ม <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_full_name<?php echo $user['id']; ?>" 
                               name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_role<?php echo $user['id']; ?>" class="form-label">บทบาท <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_role<?php echo $user['id']; ?>" name="role" required>
                                <?php foreach (getUserRoles() as $value => $label): ?>
                                    <option value="<?php echo $value; ?>" <?php echo $user['role'] === $value ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_phone<?php echo $user['id']; ?>" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="tel" class="form-control" id="edit_phone<?php echo $user['id']; ?>" 
                                   name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="edit_school_name<?php echo $user['id']; ?>" class="form-label">ชื่อโรงเรียน/สถานศึกษา</label>
                            <input type="text" class="form-control" id="edit_school_name<?php echo $user['id']; ?>" 
                                   name="school_name" value="<?php echo htmlspecialchars($user['school_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_status<?php echo $user['id']; ?>" class="form-label">สถานะ</label>
                            <select class="form-select" id="edit_status<?php echo $user['id']; ?>" name="status">
                                <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>ใช้งาน</option>
                                <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
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
    ["role", "status"].forEach(function(id) {
        document.getElementById(id).addEventListener("change", function() {
            this.form.submit();
        });
    });
    
    // Form validation
    document.getElementById("createUserForm").addEventListener("submit", function(e) {
        if (!validateRequired(this)) {
            e.preventDefault();
            alert("กรุณากรอกข้อมูลที่จำเป็น");
        }
    });
    
    // Export function
    function exportUsers() {
        const params = new URLSearchParams(window.location.search);
        params.set("export", "csv");
        window.location.href = "?" + params.toString();
    }
</script>
';

require_once 'includes/footer.php';
?>