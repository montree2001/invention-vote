<?php

/**
 * Simple PDO wrapper used throughout the application.
 * The class exposes a singleton style connection to avoid repeatedly
 * instantiating PDO objects.
 */
class Database {
    private static ?PDO $instance = null;

    /**
     * Return a PDO connection. Connection settings are read from
     * config/database.php which defines $db_host, $db_name, $db_user, $db_pass
     * variables.
     */
    public static function connection(): PDO {
        if (self::$instance === null) {
            require __DIR__ . '/../config/database.php';

            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $db_host, $db_name);
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            self::$instance = new PDO($dsn, $db_user, $db_pass, $options);
        }

        return self::$instance;
    }
}

?>
