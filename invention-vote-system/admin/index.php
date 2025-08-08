<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main>
    <h2>Admin Dashboard</h2>
    <p>Manage competitions and judges from here.</p>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
