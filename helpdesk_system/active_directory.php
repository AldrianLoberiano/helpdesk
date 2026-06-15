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
