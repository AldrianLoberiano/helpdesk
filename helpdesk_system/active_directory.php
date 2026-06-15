<?php
$page_title = 'Active Directory';
require_once __DIR__ . '/includes/auth.php';
requireRole(['Admin', 'Technician']);

$db = getDB();

// Get all users with roles and departments
$stmt = $db->query("SELECT u.*, r.role_name, d.department_name 
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id 
                    LEFT JOIN departments d ON u.department_id = d.id
                    ORDER BY u.full_name ASC");
$users = $stmt->fetchAll();

// Get counts
$total_users = count($users);
$active_users = count(array_filter($users, fn($u) => $u['is_active']));
$inactive_users = $total_users - $active_users;

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="page-banner mb-4">
        <h1 class="h3 mb-0 fw-bold">Active Directory</h1>
        <p class="text-muted mb-0 small">Company employee directory and information</p>
    </div>

    <div class="row g-3 mb-4">
