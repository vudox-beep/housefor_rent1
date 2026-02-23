<?php
/**
 * Database Configuration File
 * Central database connection management for TasteBud
 */

if (!class_exists('Database')) {
class Database {
    // Database configuration
    private static $host = '127.0.0.1';
    private static $dbname = 'rent';
    private static $username = 'root';
    private static $password = '';
    private static $charset = 'utf8mb4';
    
    // PDO instance
    private static $pdo = null;
    
    /**
     * Get database connection
     * @return PDO
     */
    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=" . self::$charset;
                
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                self::$pdo = new PDO($dsn, self::$username, self::$password, $options);
                
                // Log successful connection (optional)
                error_log("Database connection established successfully");
                
            } catch (PDOException $e) {
                // Log error
                error_log("Database connection failed: " . $e->getMessage());
                
                // Display user-friendly error
                die("Database connection failed. Please try again later.");
            }
        }
        
        return self::$pdo;
    }
    
    /**
     * Test database connection
     * @return bool
     */
    public static function testConnection() {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->query("SELECT 1");
            return $stmt !== false;
        } catch (Exception $e) {
            error_log("Database test failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Close database connection
     */
    public static function closeConnection() {
        self::$pdo = null;
    }
    
    /**
     * Get database configuration for debugging
     * @return array
     */
    public static function getConfig() {
        return [
            'host' => self::$host,
            'database' => self::$dbname,
            'username' => self::$username,
            'charset' => self::$charset
        ];
    }
    
    /**
     * Execute a prepared statement with parameters
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public static function execute($sql, $params = []) {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query execution failed: " . $e->getMessage() . " | SQL: " . $sql);
            throw new Exception("Database query failed");
        }
    }
    
    /**
     * Get single record
     * @param string $sql
     * @param array $params
     * @return array|false
     */
    public static function fetchOne($sql, $params = []) {
        $stmt = self::execute($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Get multiple records
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function fetchAll($sql, $params = []) {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get last inserted ID
     * @return string
     */
    public static function lastInsertId() {
        return self::getConnection()->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public static function beginTransaction() {
        self::getConnection()->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public static function commit() {
        self::getConnection()->commit();
    }
    
    /**
     * Rollback transaction
     */
    public static function rollback() {
        self::getConnection()->rollBack();
    }
}
}

// Initialize connection on first load
// Database::getConnection();
?>
