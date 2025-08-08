<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/functions.php';

/**
 * Attempt to log the user in with the provided credentials.
 * Returns true on success otherwise false.
 */
function login(string $username, string $password): bool {
    $user = User::findByUsername($username);
    if ($user && $user->verifyPassword($password)) {
        $_SESSION[SESSION_USER_KEY] = [
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
        ];
        return true;
    }
    return false;
}

function current_user(): ?array {
    return $_SESSION[SESSION_USER_KEY] ?? null;
}

function require_login(): void {
    if (!current_user()) {
        redirect('login.php');
    }
}

function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

?>
