<?php

require_once __DIR__ . '/Database.php';

/**
 * User model providing basic authentication and retrieval utilities.
 */
class User {
    public int $id;
    public string $username;
    public string $role;

    public static function findByUsername(string $username): ?self {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $data = $stmt->fetch();
        return $data ? self::fromArray($data) : null;
    }

    public static function findById(int $id): ?self {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        return $data ? self::fromArray($data) : null;
    }

    /**
     * Validate the supplied password with the stored hash.
     */
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }

    /**
     * Populate object from database array.
     */
    private static function fromArray(array $data): self {
        $u = new self();
        $u->id = (int)$data['id'];
        $u->username = $data['username'];
        $u->role = $data['user_type'];
        $u->password = $data['password'];
        return $u;
    }

    // Prevent direct construction; use factory methods instead.
    private function __construct() {}
    private string $password;
}

?>
