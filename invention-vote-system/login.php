<?php
require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    redirect('dashboard.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (login($username, $password)) {
        redirect('dashboard.php');
    } else {
        $error = 'Invalid username or password';
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main>
    <h2>Login</h2>
    <?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <label>Username <input type="text" name="username" required></label><br>
        <label>Password <input type="password" name="password" required></label><br>
        <button type="submit">Login</button>
    </form>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
