<?php
// install.php
require_once 'config/config.php';

$message = '';
$error = '';

// ตรวจสอบว่ามี admin แล้วหรือยัง
try {
    require_once 'classes/Database.php';
    $db = Database::getInstance();
    $admin = $db->selectOne("SELECT id FROM users WHERE user_type = 'SUPER_ADMIN' LIMIT 1");
    
    if ($admin && !isset($_GET['force'])) {
        $message = 'มีผู้ดูแลระบบอยู่แล้ว หากต้องการสร้างใหม่ให้เพิ่ม ?force=1';
    }
} catch (Exception $e) {
    $error = 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้: ' . $e->getMessage();
}

// สร้าง Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$message) {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $first_name = clean_input($_POST['first_name'] ?? '');
    $last_name = clean_input($_POST['last_name'] ?? '');
    
    if (empty($username) || empty($password) || empty($first_name) || empty($last_name)) {
        $error = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    } elseif (strlen($password) < 6) {
        $error = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
    } else {
        try {
            // ตรวจสอบ username ซ้ำ
            $existing = $db->selectOne("SELECT id FROM users WHERE username = ?", [$username]);
            if ($existing) {
                $error = 'ชื่อผู้ใช้นี้มีอยู่แล้ว';
            } else {
                // สร้างผู้ใช้
                $data = [
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'user_type' => 'SUPER_ADMIN',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $user_id = $db->insert('users', $data);
                if ($user_id) {
                    $message = 'สร้างผู้ดูแลระบบสำเร็จ! สามารถเข้าสู่ระบบได้แล้ว';
                } else {
                    $error = 'ไม่สามารถสร้างผู้ใช้ได้';
                }
            }
        } catch (Exception $e) {
            $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ติดตั้งระบบ - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts - Kanit -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Kanit', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .install-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .install-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .install-body {
            padding: 2rem;
        }
    </style>
</head>
<body>

<div class="install-card">
    <!-- Header -->
    <div class="install-header">
        <i class="bi bi-gear display-4 mb-3"></i>
        <h4 class="mb-1">ติดตั้งระบบ</h4>
        <p class="mb-0 opacity-75"><?php echo SITE_NAME; ?></p>
    </div>
    
    <!-- Body -->
    <div class="install-body">
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <?php echo $message; ?>
        </div>
        
        <?php if (strpos($message, 'สำเร็จ') !== false): ?>
        <div class="text-center">
            <a href="login.php" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right"></i>
                ไปยังหน้าเข้าสู่ระบบ
            </a>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        
        <h5 class="mb-3">สร้างผู้ดูแลระบบส่วนกลาง</h5>
        
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="first_name" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                           required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="last_name" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                           required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                <input type="text" 
                       name="username" 
                       class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                       required>
            </div>
            
            <div class="mb-4">
                <label class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                <input type="password" 
                       name="password" 
                       class="form-control" 
                       required>
                <div class="form-text">ต้องมีอย่างน้อย 6 ตัวอักษร</div>
            </div>
            
            <button type="submit" class="btn btn-success btn-lg w-100">
                <i class="bi bi-person-plus"></i>
                สร้างผู้ดูแลระบบ
            </button>
        </form>
        
        <?php endif; ?>
        
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>