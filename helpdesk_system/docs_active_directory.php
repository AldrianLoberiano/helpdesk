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
                        <a class="nav-link text-start" href="#tips">Tips</a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="fw-bold mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -4px;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        Active Directory Documentation
                    </h3>

                    <section id="overview" class="mb-5">
