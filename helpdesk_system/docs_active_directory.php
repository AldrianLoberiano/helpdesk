<?php
$page_title = 'Active Directory Documentation';
require_once __DIR__ . '/includes/auth.php';
requireRole(['Admin', 'Technician']);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 sticky-top" style="top: 80px;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Documentation</h6>
                    <nav class="nav flex-column nav-pills">
                        <a class="nav-link text-start active" href="#overview">Overview</a>
                        <a class="nav-link text-start" href="#view-users">Viewing Employees</a>
                        <a class="nav-link text-start" href="#search">Searching Users</a>
                        <a class="nav-link text-start" href="#user-info">User Information</a>
                        <a class="nav-link text-start" href="#status">Account Status</a>
