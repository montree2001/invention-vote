<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$user = current_user();
switch ($user['role']) {
    case ROLE_SUPER_ADMIN:
        redirect('super-admin/index.php');
        break;
    case ROLE_ADMIN:
        redirect('admin/index.php');
        break;
    case ROLE_CHAIRMAN:
        redirect('chairman/index.php');
        break;
    case ROLE_JUDGE:
        redirect('judge/index.php');
        break;
    default:
        echo 'Unknown role';
}

?>
