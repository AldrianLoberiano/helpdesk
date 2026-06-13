<?php
/**
 * Logout Handler
 * IT Helpdesk Ticketing System
 */

require_once __DIR__ . '/config/database.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    try {
        $db = getDB();
        
        // Clear remember token
        $stmt = $db->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
        $stmt->execute([$user_id]);
        
        // Log activity
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, 'Logout', 'User logged out', $_SERVER['REMOTE_ADDR']]);
    } catch (PDOException $e) {
        error_log("Logout error: " . $e->getMessage());
    }
}

// Destroy session
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

