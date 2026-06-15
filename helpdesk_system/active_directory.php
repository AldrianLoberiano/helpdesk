<?php
$page_title = 'Active Directory';
require_once __DIR__ . '/includes/auth.php';
requireRole(['Admin', 'Technician']);

$db = getDB();

// Get all users with roles and departments
