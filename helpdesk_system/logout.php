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
