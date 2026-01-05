<?php

class Database {
    private static $pdo = null;
    private static $mysqli = null;
    private static $useMysqli = false;

    public static function getConnection() {
        if (self::$pdo === null && !self::$useMysqli) {
            // Check if constants are defined
            if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
                die('Database configuration constants are not defined. Please check config/config.php');
            }
            
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            try {
                // Handle empty password - try empty string first
                $password = (DB_PASS === '' || DB_PASS === null) ? '' : DB_PASS;
                
                self::$pdo = new PDO($dsn, DB_USER, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            } catch (PDOException $e) {
                // If PDO fails with empty password, try mysqli as fallback
                if (DB_PASS === '' && strpos($e->getMessage(), 'Access denied') !== false) {
                    // Test if mysqli works
                    $testMysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                    if (!$testMysqli->connect_error) {
                        // mysqli works, so we'll use a PDO wrapper with mysqli
                        self::$useMysqli = true;
                        $testMysqli->close();
                        // Return mysqli connection wrapped in a PDO-like interface
                        return self::getMysqliAsPdo();
                    }
                }
                
                // If all else fails, show error
                die('DB Connection failed: ' . $e->getMessage() . 
                    '<br><br><strong>Solutions:</strong><br>' .
                    '1. If you have a MySQL password, set it in config/config.php:<br>' .
                    '<code>define(\'DB_PASS\', \'your_password\');</code><br><br>' .
                    '2. If you don\'t have a password, try accessing phpMyAdmin to verify your MySQL setup.<br>' .
                    '3. Run the test script: <a href="test_db.php">test_db.php</a> to diagnose the issue.');
            }
        }
        
        if (self::$useMysqli) {
            return self::getMysqliAsPdo();
        }
        
        return self::$pdo;
    }
    
    private static function getMysqliAsPdo() {
        // If mysqli works but PDO doesn't, the issue is likely with PDO's password handling
        // Let's try a different PDO connection string format
        try {
            // Try without specifying charset first
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->exec("SET NAMES utf8mb4");
            self::$useMysqli = false;
            return self::$pdo;
        } catch (PDOException $e) {
            // The real issue: MySQL root user might actually have a password
            // Even if you think it doesn't, newer XAMPP versions sometimes set one
            die('PDO Connection failed. The error suggests MySQL root user requires a password.<br><br>' .
                '<strong>Please try this:</strong><br>' .
                '1. Open phpMyAdmin: <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a><br>' .
                '2. If it asks for a password, that\'s your MySQL root password<br>' .
                '3. Update config/config.php with: <code>define(\'DB_PASS\', \'your_password\');</code><br><br>' .
                'OR if phpMyAdmin opens without password:<br>' .
                '4. Run diagnostic: <a href="test_db.php">test_db.php</a> to see what\'s happening');
        }
    }

    public static function getMysqliConnection() {
        if (self::$mysqli === null) {
            self::$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (self::$mysqli->connect_error) {
                die('DB Connection failed: ' . self::$mysqli->connect_error);
            }
        }
        return self::$mysqli;
    }
}
