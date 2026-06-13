<?php
/**
 * Logout Handler
 * IT Helpdesk Ticketing System
 */

require_once __DIR__ . '/config/database.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
