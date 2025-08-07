<?php
// login.php - หน้าเข้าสู่ระบบ
require_once 'config/settings.php';
require_once 'includes/auth.php';

// ถ้าเข้าสู่ระบบแล้วให้ redirect ไปหน้าที่เหมาะสม
if ($auth->isLoggedIn()) {
    $userType = $_SESSION['user_type'];
    switch ($userType) {
        case USER_TYPE_SUPER_ADMIN:
            header('Location: super-admin/');
            break;
        case USER_TYPE_ADMIN:
            header('Location: admin/');
            break;
        case USER_TYPE_CHAIRMAN:
            header('Location: chairman/');
            break;
        case USER_TYPE_JUDGE:
            header('Location: judge/');
            break;
        default:
            header('Location: dashboard.php');
    }
    exit();
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'โทเค็นความปลอดภัยไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
    } else {
        // Validate input
        if (empty($username)) {
            $error = 'กรุณากรอกชื่อผู้ใช้';
        } elseif (empty($password)) {
            $error = 'กรุณากรอกรหัสผ่าน';
        } else {
            // Attempt login
            $loginResult = $auth->login($username, $password);
            
            if ($loginResult['success']) {
                // Redirect based on user type
                $userType = $loginResult['user_type'];
                switch ($userType) {
                    case USER_TYPE_SUPER_ADMIN:
                        header('Location: super-admin/');
                        break;
                    case USER_TYPE_ADMIN:
                        header('Location: admin/');
                        break;
                    case USER_TYPE_CHAIRMAN:
                        header('Location: chairman/');
                        break;
                    case USER_TYPE_JUDGE:
                        header('Location: judge/');
                        break;
                    default:
                        header('Location: dashboard.php');
                }
                exit();
            } else {
                $error = $loginResult['message'];
            }
        }
    }
}

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - <?php echo SYSTEM_NAME; ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Meta tags -->
    <meta name="description" content="ระบบประมวลผลสิ่งประดิษฐ์คนรุ่นใหม่">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
    <div class="login-container">
        <div class="card login-card">
            <div class="card-body">
                <div class="login-header">
                    <div class="login-logo">
                        <i class="icon-invention">🔬</i>
                    </div>
                    <h1 class="login-title">เข้าสู่ระบบ</h1>
                    <p class="login-subtitle"><?php echo SYSTEM_NAME; ?></p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mb-3">
                        <strong>ข้อผิดพลาด!</strong> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php 
                $alert = getAlert();
                if ($alert): 
                ?>
                    <div class="alert alert-<?php echo $alert['type']; ?> mb-3">
                        <?php echo $alert['message']; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" data-validate="true" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="username" class="form-label">ชื่อผู้ใช้</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="username" 
                            name="username" 
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                            required
                            autocomplete="username"
                            placeholder="กรอกชื่อผู้ใช้"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">รหัสผ่าน</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            required
                            autocomplete="current-password"
                            placeholder="กรอกรหัสผ่าน"
                        >
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg d-block w-100">
                            เข้าสู่ระบบ
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        หากคุณลืมรหัสผ่าน กรุณาติดต่อผู้ดูแลระบบ<br>
                        Version <?php echo SYSTEM_VERSION; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <script>
    // Auto-focus on username field
    document.addEventListener('DOMContentLoaded', function() {
        const usernameField = document.getElementById('username');
        if (usernameField && !usernameField.value) {
            usernameField.focus();
        }
    });
    
    // Show/hide password toggle (optional enhancement)
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
    }
    </script>
</body>
</html>