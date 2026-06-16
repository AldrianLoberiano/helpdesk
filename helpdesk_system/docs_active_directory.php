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
