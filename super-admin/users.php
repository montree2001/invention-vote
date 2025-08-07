<?php
// super-admin/users.php
require_once '../config/config.php';
require_login(['SUPER_ADMIN']);

$page_title = 'จัดการผู้ใช้งาน - ' . SITE_NAME;
$current_user = get_current_user();

$db = Database::getInstance();
$user_model = new User();

$success_message = '';
$error_message = '';

// จัดการ Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_user') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $error_message = 'Invalid CSRF token';
        } else {
            $user_data = [
                'username' => clean_input($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'first_name' => clean_input($_POST['first_name'] ?? ''),
                'last_name' => clean_input($_POST['last_name'] ?? ''),
                'email' => clean_input($_POST['email'] ?? ''),
                'phone' => clean_input($_POST['phone'] ?? ''),
                'user_type' => clean_input($_POST['user_type'] ?? ''),
                'institution_name' => clean_input($_POST['institution_name'] ?? ''),
                'province' => clean_input($_POST['province'] ?? ''),
                'is_active' => 1,
                'created_by' => $current_user['id']
            ];
            
            $result = $user_model->createUser($user_data);
            if ($result['success']) {
                $success_message = $result['message'];
            } else {
                $error_message = $result['message'];
            }
        }
    }
    
    elseif ($action === 'update_user') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $error_message = 'Invalid CSRF token';
        } else {
            $user_id = (int)($_POST['user_id'] ?? 0);
            $user_data = [
                'username' => clean_input($_POST['username'] ?? ''),
                'first_name' => clean_input($_POST['first_name'] ?? ''),
                'last_name' => clean_input($_POST['last_name'] ?? ''),
                'email' => clean_input($_POST['email'] ?? ''),
                'phone' => clean_input($_POST['phone'] ?? ''),
                'user_type' => clean_input($_POST['user_type'] ?? ''),
                'institution_name' => clean_input($_POST['institution_name'] ?? ''),
                'province' => clean_input($_POST['province'] ?? ''),
                'is_active' => (int)($_POST['is_active'] ?? 1)
            ];
            
            // เพิ่มรหัสผ่านใหม่ถ้ามี
            if (!empty($_POST['password'])) {
                $user_data['password'] = $_POST['password'];
            }
            
            $result = $user_model->updateUser($user_id, $user_data);
            if ($result['success']) {
                $success_message = $result['message'];
            } else {
                $error_message = $result['message'];
            }
        }
    }
    
    elseif ($action === 'toggle_status') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $error_message = 'Invalid CSRF token';
        } else {
            $user_id = (int)($_POST['user_id'] ?? 0);
            $new_status = (int)($_POST['status'] ?? 0);
            
            $result = $user_model->updateUser($user_id, ['is_active' => $new_status]);
            if ($result['success']) {
                $success_message = 'เปลี่ยนสถานะผู้ใช้เรียบร้อยแล้ว';
            } else {
                $error_message = $result['message'];
            }
        }
    }
}

// การค้นหาและกรอง
$search = clean_input($_GET['search'] ?? '');
$user_type_filter = clean_input($_GET['user_type'] ?? '');
$status_filter = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;

// สร้างเงื่อนไขการค้นหา
$where_conditions = ['1=1'];
$params = [];

if (!empty($search)) {
    $where_conditions[] = '(username LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR institution_name LIKE ?)';
    $search_term = '%' . $search . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($user_type_filter)) {
    $where_conditions[] = 'user_type = ?';
    $params[] = $user_type_filter;
}

if ($status_filter !== '') {
    $where_conditions[] = 'is_active = ?';
    $params[] = (int)$status_filter;
}

// นับจำนวนรวม
$count_sql = "SELECT COUNT(*) as total FROM users WHERE " . implode(' AND ', $where_conditions);
$total_records = $db->selectOne($count_sql, $params)['total'];
$pagination = get_pagination($total_records, $per_page, $page);

// ดึงข้อมูลผู้ใช้
$sql = "SELECT * FROM users WHERE " . implode(' AND ', $where_conditions) . " ORDER BY created_at DESC LIMIT {$per_page} OFFSET {$pagination['offset']}";
$users = $db->select($sql, $params);

$show_navbar = true;
$show_footer = true;
?>

<?php include '../includes/header.php'; ?>

<div class="container-fluid px-4 py-4">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">จัดการผู้ใช้งาน</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-people-fill text-primary"></i>
                จัดการผู้ใช้งาน
            </h1>
            <p class="text-muted mb-0">เพิ่ม แก้ไข และจัดการบัญชีผู้ใช้งานในระบบ</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-lg"></i>
                เพิ่มผู้ใช้ใหม่
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if ($success_message): ?>
        <?php echo display_alert($success_message, 'success'); ?>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <?php echo display_alert($error_message, 'danger'); ?>
    <?php endif; ?>

    <!-- Search and Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">ค้นหา</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="ชื่อผู้ใช้, ชื่อ-นามสกุล, อีเมล, สถานศึกษา..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ประเภทผู้ใช้</label>
                    <select name="user_type" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <?php foreach (USER_TYPES as $type => $label): ?>
                            <option value="<?php echo $type; ?>" <?php echo $user_type_filter === $type ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">สถานะ</label>
                    <select name="status" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>ใช้งาน</option>
                        <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
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

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    รายการผู้ใช้งาน (<?php echo number_format($total_records); ?> คน)
                </h5>
                <small class="text-muted">
                    แสดง <?php echo number_format($pagination['offset'] + 1); ?> - 
                    <?php echo number_format(min($pagination['offset'] + $per_page, $total_records)); ?>
                    จาก <?php echo number_format($total_records); ?> รายการ
                </small>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($users)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted opacity-50"></i>
                    <h5 class="text-muted mt-3">ไม่พบข้อมูลผู้ใช้งาน</h5>
                    <p class="text-muted">ลองเปลี่ยนเงื่อนไขการค้นหาใหม่</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ผู้ใช้งาน</th>
                                <th>ประเภท</th>
                                <th>สถานศึกษา/หน่วยงาน</th>
                                <th>การเข้าสู่ระบบล่าสุด</th>
                                <th>สถานะ</th>
                                <th width="120">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-3">
                                                <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">
                                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="bi bi-person"></i>
                                                    <?php echo htmlspecialchars($user['username']); ?>
                                                </small>
                                                <?php if ($user['email']): ?>
                                                    <br><small class="text-muted">
                                                        <i class="bi bi-envelope"></i>
                                                        <?php echo htmlspecialchars($user['email']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo get_user_type_badge($user['user_type']); ?>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <?php if ($user['institution_name']): ?>
                                                <i class="bi bi-building"></i>
                                                <?php echo htmlspecialchars($user['institution_name']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($user['province']): ?>
                                            <div class="small text-muted">
                                                <i class="bi bi-geo-alt"></i>
                                                <?php echo htmlspecialchars($user['province']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($user['last_login']): ?>
                                            <small>
                                                <?php echo format_thai_date($user['last_login']); ?>
                                            </small>
                                            <?php if ($user['last_ip']): ?>
                                                <br><small class="text-muted">
                                                    <i class="bi bi-globe"></i>
                                                    <?php echo htmlspecialchars($user['last_ip']); ?>
                                                </small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <small class="text-muted">ยังไม่เคยเข้าสู่ระบบ</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo get_status_badge($user['is_active']); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-edit" 
                                                    data-user='<?php echo json_encode($user, JSON_UNESCAPED_UNICODE); ?>'>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-<?php echo $user['is_active'] ? 'danger' : 'success'; ?> btn-toggle-status"
                                                    data-user-id="<?php echo $user['id']; ?>"
                                                    data-current-status="<?php echo $user['is_active']; ?>"
                                                    data-user-name="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>">
                                                <i class="bi bi-<?php echo $user['is_active'] ? 'x-lg' : 'check-lg'; ?>"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="card-footer bg-light border-0">
                <nav>
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li class="page-item <?php echo !$pagination['has_prev'] ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&user_type=<?php echo urlencode($user_type_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                                ก่อนหน้า
                            </a>
                        </li>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($pagination['total_pages'], $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&user_type=<?php echo urlencode($user_type_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo !$pagination['has_next'] ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&user_type=<?php echo urlencode($user_type_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                                ถัดไป
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus"></i>
                    เพิ่มผู้ใช้ใหม่
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="addUserForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_user">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อผู้ใช้ *</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">รหัสผ่าน *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ชื่อ *</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">นามสกุล *</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">อีเมล</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ประเภทผู้ใช้ *</label>
                            <select name="user_type" class="form-select" required>
                                <option value="">เลือกประเภท</option>
                                <?php foreach (USER_TYPES as $type => $label): ?>
                                    <option value="<?php echo $type; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">จังหวัด</label>
                            <input type="text" name="province" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">สถานศึกษา/หน่วยงาน</label>
                            <input type="text" name="institution_name" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil"></i>
                    แก้ไขข้อมูลผู้ใช้
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editUserForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อผู้ใช้ *</label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">รหัสผ่านใหม่ (เว้นว่างหากไม่เปลี่ยน)</label>
                            <input type="password" name="password" id="edit_password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ชื่อ *</label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">นามสกุล *</label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">อีเมล</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ประเภทผู้ใช้ *</label>
                            <select name="user_type" id="edit_user_type" class="form-select" required>
                                <option value="">เลือกประเภท</option>
                                <?php foreach (USER_TYPES as $type => $label): ?>
                                    <option value="<?php echo $type; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">จังหวัด</label>