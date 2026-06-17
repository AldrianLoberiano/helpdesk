<?php
/**
 * Authentication Check
 * IT Helpdesk Ticketing System
 */

require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('/helpdesk/helpdesk_system/login.php');
}

// Check if session is still valid (optional: check timeout)
$session_timeout = 1800; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_unset();
    session_destroy();
    redirect('/helpdesk/helpdesk_system/login.php?timeout=1');
}
$_SESSION['last_activity'] = time();

/**
 * Check if user has specific role
 */
function hasRole($roles) {
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    return in_array($_SESSION['role_name'] ?? '', $roles);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return hasRole('Admin');
}

/**
 * Check if user is technician
 */
function isTechnician() {
    return hasRole('Technician');
}

/**
 * Check if user is employee
 */
function isEmployee() {
    return hasRole('Employee');
}

/**
 * Require specific role
 */
function requireRole($roles) {
    if (!hasRole($roles)) {
        redirect('/helpdesk/helpdesk_system/login.php?unauthorized=1');
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getCurrentUserRole() {
    return $_SESSION['role_name'] ?? null;
}

/**
 * Get user notifications
 */
function getUserNotifications($limit = 10) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([getCurrentUserId(), (int)$limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get unread notification count
 */
function getUnreadNotificationCount() {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([getCurrentUserId()]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}
?>