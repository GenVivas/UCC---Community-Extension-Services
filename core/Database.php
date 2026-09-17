<?php

/**
 * Database Singleton
 * Returns a shared PDO instance across the entire request lifecycle.
 */
class Database
{
    private static ?PDO $instance = null;

    /** Private constructor — use getInstance(). */
    private function __construct() {}

    /**
     * Return the shared PDO connection, creating it on first call.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
            );

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                ]);
            } catch (PDOException $e) {
                // Log the error and show a safe message
                error_log('[DB ERROR] ' . $e->getMessage());
                http_response_code(500);
                exit('<h1>Database connection failed.</h1><p>Please contact the system administrator.</p>');
            }
        }

        return self::$instance;
    }

    /** Prevent cloning of the singleton. */
    private function __clone() {}
}
