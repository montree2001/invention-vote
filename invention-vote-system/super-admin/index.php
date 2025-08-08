<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main>
    <h2>Super Admin Dashboard</h2>
    <p>Use the navigation to manage users and competitions.</p>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
