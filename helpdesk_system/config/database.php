<?php
/**
 * Database Configuration and Connection
 * IT Helpdesk Ticketing System
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'helpdesk_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_NAME', 'IT Helpdesk');
define('SITE_URL', 'http://localhost/helpdesk/helpdesk_system');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Database Connection using PDO
 */
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->connection;
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}

/**
 * Helper function to get database connection
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Sanitize input
 */
function sanitize($input) {
    if ($input === null) return '';
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect function
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y h:i A') {
    return date($format, strtotime($date));
}

/**
 * Generate ticket number
 */
function generateTicketNumber() {
    $prefix = 'TKT';
    $date = date('Ymd');
    $random = strtoupper(substr(uniqid(), -4));
    return $prefix . '-' . $date . '-' . $random;
}

/**
 * Get a user setting
 */
function getUserSetting($user_id, $key, $default = null) {
    static $cache = [];
    $cache_key = $user_id . '_' . $key;
    
    if (isset($cache[$cache_key])) {
        return $cache[$cache_key];
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM user_settings WHERE user_id = ? AND setting_key = ?");
    $stmt->execute([$user_id, $key]);
    $row = $stmt->fetch();
    
    $value = $row ? $row['setting_value'] : $default;
    $cache[$cache_key] = $value;
    return $value;
}

/**
 * Set a user setting
 */
function setUserSetting($user_id, $key, $value) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO user_settings (user_id, setting_key, setting_value) VALUES (?, ?, ?) 
                         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()");
    $stmt->execute([$user_id, $key, $value]);
}

/**
 * Get all user settings as associative array
 */
function getAllUserSettings($user_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_key, setting_value FROM user_settings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}
?>