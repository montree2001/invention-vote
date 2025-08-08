<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main>
    <h2>Chairman Dashboard</h2>
    <p>Review and approve scores for assigned categories.</p>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
