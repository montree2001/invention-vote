<?php
// login.php
require_once 'config/config.php';
require_once 'classes/User.php';

// ถ้าเข้าสู่ระบบแล้วให้ redirect ไปหน้าหลัก
if (is_logged_in()) {
    $redirect_url = $_SESSION['redirect_url'] ?? 'index.php';
    unset($_SESSION['redirect_url']);
    redirect($redirect_url);
}

$error_message = '';
$success_message = '';

// ตรวจสอบ query parameters
if (isset($_GET['timeout'])) {
    $error_message = 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่';
} elseif (isset($_GET['disabled'])) {
    $error_message = 'บัญชีของท่านถูกปิดการใช้งาน กรุณาติดต่อผู้ดูแลระบบ';
} elseif (isset($_GET['lockout'])) {
    $error_message = 'บัญชีของท่านถูกล็อคชั่วคราวเนื่องจากการกระทำที่น่าสงสัย';
} elseif (isset($_GET['logout'])) {
    $success_message = 'ออกจากระบบเรียบร้อยแล้ว';
}

// ประมวลผลการ login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);
    
    // ตรวจสอบ CSRF token
    if (!verify_csrf()) {
        $error_message = 'Invalid request. Please try again.';
    } elseif (empty($username) || empty($password)) {
        $error_message = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } else {
        $userClass = new User();
        $result = $userClass->authenticate(
            $username, 
            $password, 
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );
        
        if ($result['success']) {
            // สร้าง session
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];
            $_SESSION['user_type'] = $result['user']['user_type'];
            $_SESSION['full_name'] = $result['user']['first_name'] . ' ' . $result['user']['last_name'];
            $_SESSION['last_activity'] = time();
            $_SESSION['login_time'] = time();
            
            // Remember Me functionality
            if ($remember_me) {
                $expire = time() + (30 * 24 * 60 * 60); // 30 days
                setcookie('remember_token', bin2hex(random_bytes(32)), $expire, '/', '', true, true);
            }
            
            // Redirect to intended page or dashboard
            $redirect_url = $_SESSION['redirect_url'] ?? 'index.php';
            unset($_SESSION['redirect_url']);
            redirect($redirect_url);
            
        } else {
            $error_message = $result['message'];
        }
    }
}

// Set page variables
$page_title = 'เข้าสู่ระบบ - ' . SITE_NAME;
$hide_container = true;
$additional_css = '';
?>

<?php include 'includes/header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <i class="bi bi-lightbulb display-4 mb-3"></i>
            <h4 class="mb-1">INVENTION-VOTE</h4>
            <p class="mb-0 opacity-75">ระบบประมวลผลสิ่งประดิษฐ์คนรุ่นใหม่</p>
        </div>
        
        <!-- Body -->
        <div class="login-body">
            <?php if ($error_message): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?php echo htmlspecialchars($error_message); ?></div>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div><?php echo htmlspecialchars($success_message); ?></div>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-person me-1"></i>ชื่อผู้ใช้
                    </label>
                    <input type="text" 
                           class="form-control form-control-lg" 
                           id="username" 
                           name="username" 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>"
                           required 
                           autofocus 
                           autocomplete="username">
                    <div class="invalid-feedback">
                        กรุณากรอกชื่อผู้ใช้
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i>รหัสผ่าน
                    </label>
                    <div class="input-group">
                        <input type="password" 
                               class="form-control form-control-lg" 
                               id="password" 
                               name="password" 
                               required 
                               autocomplete="current-password">
                        <button class="btn btn-outline-secondary" 
                                type="button" 
                                id="togglePassword" 
                                title="แสดง/ซ่อนรหัสผ่าน">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">
                        กรุณากรอกรหัสผ่าน
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="remember_me" 
                               name="remember_me">
                        <label class="form-check-label" for="remember_me">
                            จดจำการเข้าสู่ระบบ (30 วัน)
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>เข้าสู่ระบบ
                </button>
            </form>
            
            <hr class="my-4">
            
            <div class="text-center">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    ไม่มีการลงทะเบียน กรุณาติดต่อผู้ดูแลระบบ
                </small>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="login-footer">
            <small>
                <i class="bi bi-shield-check me-1"></i>
                ระบบปลอดภัย SSL/TLS Encryption
            </small>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        toggleIcon.className = 'bi bi-eye-slash';
    } else {
        password.type = 'password';
        toggleIcon.className = 'bi bi-eye';
    }
});

// Form validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    let isValid = true;
    
    // Reset validation state
    username.classList.remove('is-invalid');
    password.classList.remove('is-invalid');
    
    // Validate username
    if (!username.value.trim()) {
        username.classList.add('is-invalid');
        isValid = false;
    }
    
    // Validate password
    if (!password.value.trim()) {
        password.classList.add('is-invalid');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
        return false;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>กำลังเข้าสู่ระบบ...';
});

// Auto-focus on page load
document.addEventListener('DOMContentLoaded', function() {
    const username = document.getElementById('username');
    if (username && !username.value) {
        username.focus();
    } else {
        document.getElementById('password').focus();
    }
});

// Enter key handling
document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('loginForm').dispatchEvent(new Event('submit', {bubbles: true}));
    }
});
</script>

<?php 
$additional_js = '';
include 'includes/footer.php'; 
?>