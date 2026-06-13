<?php
/**
 * Index Page - Landing/Redirect
 * IT Helpdesk Ticketing System
 */

require_once __DIR__ . '/config/database.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role_name'] ?? '';
    if ($role === 'Admin') {
        redirect('admin/dashboard.php');
    } elseif ($role === 'Technician') {
        redirect('technician/dashboard.php');
    } else {
        redirect('user/dashboard.php');
    }
}

