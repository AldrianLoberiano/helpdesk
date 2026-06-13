<?php
/**
 * Mark Notifications as Read - AJAX Endpoint
 * IT Helpdesk Ticketing System
 */

require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    $user_id = getCurrentUserId();
    
