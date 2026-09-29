<?php
// config/database.php - PDO Database Connection

declare(strict_types=1);

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3306';
            $dbName = getenv('DB_NAME') ?: 'waste_food_db';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // If database doesn't exist yet, connect to server to allow schema provisioning
                if (str_contains($e->getMessage(), "Unknown database")) {
                    $serverDsn = "mysql:host={$host};port={$port};charset={$charset}";
                    $serverPdo = new PDO($serverDsn, $user, $pass, $options);
                    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    self::$instance = new PDO($dsn, $user, $pass, $options);
                } else {
                    error_log("Database connection error: " . $e->getMessage());
                    die("Database connection failed. Please verify MySQL service is running.");
                }
            }
        }
        return self::$instance;
    }
}
