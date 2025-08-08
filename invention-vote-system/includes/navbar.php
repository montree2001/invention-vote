<?php require_once __DIR__ . '/auth.php'; ?>
<nav>
    <a href="dashboard.php">Dashboard</a>
    <?php if (current_user()): ?>
        <span>Logged in as <?= htmlspecialchars(current_user()['username']) ?></span>
        <a href="logout.php">Logout</a>
    <?php endif; ?>
</nav>
