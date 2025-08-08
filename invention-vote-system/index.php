<?php
require_once __DIR__ . '/includes/auth.php';

// If user already logged in redirect to dashboard otherwise to login page
if (current_user()) {
    redirect('dashboard.php');
} else {
    redirect('login.php');
}

?>
