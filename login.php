<?php
// login.php
require_once 'config/config.php';

// ถ้าเข้าสู่ระบบแล้วให้ไปหน้าหลัก
if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$success = '';

// ตรวจสอบ messages
if (isset($_GET['logout'])) {
    $success = 'ออกจากระบบเรียบร้อยแล้ว';
}
if (isset($_GET['timeout'])) {
    $error = 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่';
}

// ประมวลผล Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } else {
        try {
            require_once 'classes/Database.php';
            $db = Database::getInstance();
            
            $user = $db->selectOne(
                "SELECT * FROM users WHERE username = ? AND is_active = 1",
                [$username]
            );
            
            if ($user && password_verify($password, $user['password'])) {
                // Login สำเร็จ
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_time'] = time();
                
                redirect('index.php');
            } else {
                $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
            }
        } catch (Exception $e) {
            $error = 'เกิดข้อผิดพลาดในระบบ';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - <?php echo SITE_NAME; ?></title>
    
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
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Header -->
    <div class="login-header">
        <i class="bi bi-lightbulb display-4 mb-3"></i>
        <h4 class="mb-1">INVENTION-VOTE</h4>
        <p class="mb-0 opacity-75">ระบบประมวลผลสิ่งประดิษฐ์คนรุ่นใหม่</p>
    </div>
    
    <!-- Body -->
    <div class="login-body">
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <?php echo $success; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">
                    <i class="bi bi-person"></i> ชื่อผู้ใช้
                </label>
                <input type="text" 
                       name="username" 
                       class="form-control form-control-lg" 
                       value="<?php echo htmlspecialchars($username ?? ''); ?>"
                       required>
            </div>
            
            <div class="mb-4">
                <label class="form-label">
                    <i class="bi bi-lock"></i> รหัสผ่าน
                </label>
                <input type="password" 
                       name="password" 
                       class="form-control form-control-lg" 
                       required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-box-arrow-in-right"></i>
                เข้าสู่ระบบ
            </button>
        </form>
        
        <hr class="my-4">
        
        <div class="text-center">
            <small class="text-muted">
                <i class="bi bi-info-circle"></i>
                ไม่มีการลงทะเบียน กรุณาติดต่อผู้ดูแลระบบ
            </small>
        </div>
        
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>