<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main>
    <h2>Judge Dashboard</h2>
    <p>View assigned inventions and submit scores.</p>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
