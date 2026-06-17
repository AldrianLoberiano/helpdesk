<?php
$page_title = 'Server Room';
require_once __DIR__ . '/includes/auth.php';
requireRole(['Admin', 'Technician']);

$db = getDB();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="page-banner mb-4">
        <h1 class="h3 mb-0 fw-bold">Server Room</h1>
        <p class="text-muted mb-0 small">Infrastructure monitoring, device status, and network overview</p>
    </div>

    <!-- ISP Monitoring -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <h5 class="mb-0 fw-bold">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M5 12.55a11 11 0 0114.08 0"/><path d="M1.42 9a16 16 0 0121.16 0"/><path d="M8.53 16.11a6 6 0 016.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                        ISP Monitoring
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="text-muted small mb-1">Primary ISP</div>
                                <div class="fw-bold fs-5">PLDT Enterprise</div>
                                <div class="badge bg-success mt-1">Connected</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="text-muted small mb-1">Secondary ISP</div>
                                <div class="fw-bold fs-5">Globe Business</div>
                                <div class="badge bg-success mt-1">Connected</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="text-muted small mb-1">Primary Bandwidth</div>
                                <div class="fw-bold fs-5">500 Mbps</div>
                                <div class="text-muted small">Dedicated Fiber</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="text-muted small mb-1">Secondary Bandwidth</div>
                                <div class="fw-bold fs-5">200 Mbps</div>
                                <div class="text-muted small">Backup Fiber</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Network Topology - Dark Theme -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="topo-dark-container">
                <div class="topo-dark-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color:var(--text-primary)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 010 8.49m-8.48-.01a6 6 0 010-8.49m11.31-2.82a10 10 0 010 14.14m-14.14 0a10 10 0 010-14.14"/></svg>
                        Network Topology
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge" style="background:#fff;color:var(--primary);border:1px solid var(--border-color)">8 Devices</span>
                        <span class="badge topo-pulse" style="background:#fff;color:var(--primary);border:1px solid var(--border-color)">
                            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor" style="vertical-align: 1px;"><circle cx="4" cy="4" r="4"/></svg>
                            Online
                        </span>
                    </div>
                </div>
                <div class="topo-dark-body">
                    <!-- Internet Cloud -->
                    <div class="topo-row topo-row-internet">
                        <div class="topo-node topo-cloud" title="Internet - PLDT Enterprise Fiber">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.5"><path d="M18 10h-1.26A8 8 0 109 20h9a5 5 0 000-10z"/></svg>
                            <span class="topo-node-name">Internet</span>
                            <span class="topo-node-detail">PLDT Fiber</span>
                        </div>
                    </div>
                    <div class="topo-line topo-line-vertical">
                        <span class="topo-line-label">WAN</span>
                    </div>

                    <!-- Router -->
                    <div class="topo-row">
                        <div class="topo-node topo-router" title="Cisco ISR 4331 - 192.168.1.1">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.5"><rect x="2" y="14" width="20" height="7" rx="2"/><rect x="2" y="3" width="20" height="7" rx="2"/><line x1="12" y1="10" x2="12" y2="14"/></svg>
                            <span class="topo-node-name">Core Router</span>
                            <span class="topo-node-detail">192.168.1.1</span>
                        </div>
                    </div>
                    <div class="topo-line topo-line-vertical">
                        <span class="topo-line-label">Trunk</span>
                    </div>

                    <!-- Firewall -->
                    <div class="topo-row">
                        <div class="topo-node topo-firewall" title="FortiGate 100F - 192.168.1.254">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e63757" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span class="topo-node-name">Main Firewall</span>
                            <span class="topo-node-detail">FortiGate 100F</span>
                        </div>
                    </div>
                    <div class="topo-line topo-line-vertical">
                        <span class="topo-line-label">1 Gbps</span>
                    </div>

                    <!-- Core Switch -->
                    <div class="topo-row">
                        <div class="topo-node topo-switch topo-switch-core" title="Cisco Catalyst 9300 - 192.168.1.100">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#17a673" stroke-width="1.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><circle cx="6" cy="6" r="1" fill="#17a673"/><circle cx="10" cy="6" r="1" fill="#17a673"/><circle cx="6" cy="18" r="1" fill="#17a673"/><circle cx="10" cy="18" r="1" fill="#17a673"/></svg>
                            <span class="topo-node-name">Core Switch</span>
                            <span class="topo-node-detail">Cisco C9300</span>
                        </div>
                    </div>

                    <!-- Lines from Core Switch to Access Switches -->
                    <div class="topo-line-horizontal">
                        <div class="topo-line-h-segment"></div>
                        <div class="topo-line-label">Trunk</div>
                        <div class="topo-line-h-segment"></div>
                    </div>

                    <!-- Access Switches -->
                    <div class="topo-row topo-row-switches">
                        <div class="topo-node topo-switch topo-switch-access" title="Cisco 2960 - 192.168.1.2">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#17a673" stroke-width="1.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><circle cx="6" cy="6" r="1" fill="#17a673"/><circle cx="10" cy="6" r="1" fill="#17a673"/><circle cx="6" cy="18" r="1" fill="#17a673"/><circle cx="10" cy="18" r="1" fill="#17a673"/></svg>
                            <span class="topo-node-name">Floor 1 Switch</span>
                            <span class="topo-node-detail">192.168.1.2</span>
                            <span class="topo-node-badge bg-success-soft">18 clients</span>
                        </div>
                        <div class="topo-node topo-switch topo-switch-access" title="Cisco 2960 - 192.168.1.3">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#17a673" stroke-width="1.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><circle cx="6" cy="6" r="1" fill="#17a673"/><circle cx="10" cy="6" r="1" fill="#17a673"/><circle cx="6" cy="18" r="1" fill="#17a673"/><circle cx="10" cy="18" r="1" fill="#17a673"/></svg>
                            <span class="topo-node-name">Floor 2 Switch</span>
                            <span class="topo-node-detail">192.168.1.3</span>
                            <span class="topo-node-badge bg-success-soft">22 clients</span>
                        </div>
                        <div class="topo-node topo-switch topo-switch-access" title="Cisco 2960 - 192.168.1.4">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#17a673" stroke-width="1.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><circle cx="6" cy="6" r="1" fill="#17a673"/><circle cx="10" cy="6" r="1" fill="#17a673"/><circle cx="6" cy="18" r="1" fill="#17a673"/><circle cx="10" cy="18" r="1" fill="#17a673"/></svg>
                            <span class="topo-node-name">Floor 3 Switch</span>
                            <span class="topo-node-detail">192.168.1.4</span>
                            <span class="topo-node-badge bg-success-soft">15 clients</span>
                        </div>
                    </div>

                    <!-- Bottom Section: Server Room + Wireless -->
                    <div class="topo-line-horizontal topo-line-spread">
                        <div class="topo-line-h-segment"></div>
                        <div class="topo-line-label">PoE</div>
                        <div class="topo-line-h-segment"></div>
                    </div>

                    <div class="topo-row topo-row-bottom">
                        <!-- Server Room Group -->
                        <div class="topo-group">
                            <div class="topo-group-label">Server Room</div>
                            <div class="topo-group-items">
                                <div class="topo-node topo-server" title="SRV-DC01 - Domain Controller">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e6a817" stroke-width="1.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                                    <span class="topo-node-name">SRV-DC01</span>
                                    <span class="topo-node-detail">DC / DNS</span>
                                </div>
                                <div class="topo-node topo-server" title="SRV-FILE01 - File Server">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e6a817" stroke-width="1.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                                    <span class="topo-node-name">SRV-FILE01</span>
                                    <span class="topo-node-detail">File Server</span>
                                </div>
                                <div class="topo-node topo-server" title="SRV-WEB01 - Web Server">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e6a817" stroke-width="1.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                                    <span class="topo-node-name">SRV-WEB01</span>
                                    <span class="topo-node-detail">Web Server</span>
                                </div>
                                <div class="topo-node topo-server" title="SRV-DB01 - Database Server">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e6a817" stroke-width="1.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                                    <span class="topo-node-name">SRV-DB01</span>
                                    <span class="topo-node-detail">Database</span>
                                </div>
                            </div>
                        </div>

                        <!-- Wireless Group -->
                        <div class="topo-group">
                            <div class="topo-group-label">Wireless Network</div>
                            <div class="topo-group-items">
                                <div class="topo-node topo-ap" title="Ubiquiti U6-Pro - 192.168.1.101">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.5"><path d="M5 12.55a11 11 0 0114.08 0"/><path d="M1.42 9a16 16 0 0121.16 0"/><path d="M8.53 16.11a6 6 0 016.95 0"/><circle cx="12" cy="20" r="1" fill="var(--primary)"/></svg>
                                    <span class="topo-node-name">Lobby AP</span>
                                    <span class="topo-node-detail">24 clients</span>
                                    <span class="topo-node-badge bg-info-soft">-45 dBm</span>
                                </div>
                                <div class="topo-node topo-ap" title="Ubiquiti U6-Pro - 192.168.1.102">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.5"><path d="M5 12.55a11 11 0 0114.08 0"/><path d="M1.42 9a16 16 0 0121.16 0"/><path d="M8.53 16.11a6 6 0 016.95 0"/><circle cx="12" cy="20" r="1" fill="var(--primary)"/></svg>
                                    <span class="topo-node-name">Cafeteria AP</span>
                                    <span class="topo-node-detail">18 clients</span>
                                    <span class="topo-node-badge bg-info-soft">-52 dBm</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- VLAN Bar -->
                    <div class="topo-vlan-bar">
                        <div class="topo-vlan-item"><span class="topo-vlan-dot" style="background:var(--primary)"></span> VLAN 10 - Corporate</div>
                        <div class="topo-vlan-item"><span class="topo-vlan-dot" style="background:#e6a817"></span> VLAN 20 - Servers</div>
                        <div class="topo-vlan-item"><span class="topo-vlan-dot" style="background:#17a673"></span> VLAN 30 - Guest WiFi</div>
                        <div class="topo-vlan-item"><span class="topo-vlan-dot" style="background:#6c757d"></span> VLAN 40 - IoT</div>
                        <div class="topo-vlan-item"><span class="topo-vlan-dot" style="background:#e63757"></span> VLAN 50 - Management</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Network Load & Bandwidth -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        Network Load
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2 pulse-badge">
                            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor" style="vertical-align: 1px;"><circle cx="4" cy="4" r="4"/></svg>
                            LIVE
                        </span>
                        <small class="text-muted" id="lastUpdated">Updated just now</small>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 280px;">
                        <canvas id="networkLoadChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect x="1" y="6" width="22" height="12" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        Bandwidth Usage
                    </h5>
                    <span class="badge bg-success pulse-badge">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor" style="vertical-align: 1px;"><circle cx="4" cy="4" r="4"/></svg>
                        LIVE
                    </span>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Download</span>
                            <span class="fw-bold" id="downloadSpeed">324.5 Mbps</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" id="downloadBar" style="width: 65%;"></div>
                        </div>
                        <div class="text-muted small" id="downloadPercent">65% of 500 Mbps</div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Upload</span>
                            <span class="fw-bold" id="uploadSpeed">87.2 Mbps</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" id="uploadBar" style="width: 44%;"></div>
                        </div>
                        <div class="text-muted small" id="uploadPercent">44% of 200 Mbps</div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Latency</span>
                            <span class="fw-bold" id="latencyValue">12 ms</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" id="latencyBar" style="width: 12%;"></div>
                        </div>
                        <div class="text-muted small" id="latencyStatus">Excellent</div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-muted small">Peak Today</div>
                            <div class="fw-bold text-primary" id="peakToday">412 Mbps</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Avg Today</div>
                            <div class="fw-bold" id="avgToday">286 Mbps</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Total Data</div>
                            <div class="fw-bold" id="totalData">1.2 TB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Server Status -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                        Server Status
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success" id="onlineCount">4 Online</span>
                        <span class="badge bg-warning text-dark" id="warningCount">1 High Load</span>
                        <span class="badge bg-secondary" id="offlineCount">0 Offline</span>
                        <span class="badge bg-danger" id="crashedCount">0 Crashed</span>
                        <span class="badge bg-info" id="rebootingCount">0 Rebooting</span>
                    </div>
                        <span class="badge bg-success pulse-badge">
                            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor" style="vertical-align: 1px;"><circle cx="4" cy="4" r="4"/></svg>
                            LIVE
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Server Name</th>
                                    <th>IP Address</th>
                                    <th>Type</th>
                                    <th>CPU Usage</th>
                                    <th>Memory Usage</th>
                                    <th>Disk Usage</th>
                                    <th>Network I/O</th>
                                    <th>Uptime</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="serverTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Devices & Services -->
    <div class="row g-3 mb-4">
        <!-- Network Devices -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <h5 class="mb-0 fw-bold">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                        Network Devices
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Device</th>
                                    <th>IP Address</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">Core Router</td>
                                    <td>192.168.1.1</td>
                                    <td><span class="badge bg-primary">Router</span></td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Main Switch</td>
                                    <td>192.168.1.2</td>
                                    <td><span class="badge bg-info">Switch</span></td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Floor 2 Switch</td>
                                    <td>192.168.1.3</td>
                                    <td><span class="badge bg-info">Switch</span></td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">WiFi AP - Lobby</td>
                                    <td>192.168.1.101</td>
                                    <td><span class="badge bg-warning text-dark">Access Point</span></td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">WiFi AP - Office</td>
                                    <td>192.168.1.102</td>
                                    <td><span class="badge bg-warning text-dark">Access Point</span></td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Firewall</td>
                                    <td>192.168.1.254</td>
                                    <td><span class="badge bg-danger">Firewall</span></td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        Services
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success" id="svcOnlineCount">7 Running</span>
                        <span class="badge bg-warning text-dark" id="svcWarningCount" style="display:none">1 High Load</span>
                        <span class="badge bg-danger" id="svcStoppedCount" style="display:none">0 Stopped</span>
                        <span class="badge bg-info" id="svcRestartingCount" style="display:none">0 Restarting</span>
                        <span class="badge bg-success pulse-badge">
                            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor" style="vertical-align: 1px;"><circle cx="4" cy="4" r="4"/></svg>
                            LIVE
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Server</th>
                                    <th>Port</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="servicesTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pulse-badge {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.reboot-btn {
    transition: all 0.2s;
}
.reboot-btn:hover {
    transform: scale(1.05);
}

/* Network Topology - Dark Theme */
.topo-dark-container {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
}
.topo-dark-header {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border-color);
    background: #fff;
}
.topo-dark-body {
    padding: 24px 20px 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #fff;
}
.topo-pulse { animation: topoPulse 2s infinite; }
@keyframes topoPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.topo-row {
    display: flex;
    justify-content: center;
    gap: 14px;
    flex-wrap: wrap;
    width: 100%;
}
.topo-row-switches { gap: 10px; }
.topo-row-bottom { gap: 20px; margin-top: 0; justify-content: center; }

/* Node base */
.topo-node {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 10px 14px;
    border-radius: 8px;
    cursor: default;
    transition: all 0.2s;
    position: relative;
    min-width: 90px;
    background: #fff;
    border: 1px solid var(--border-color);
}
.topo-node:hover {
    border-color: var(--primary);
    background: #fff;
}

/* Cloud - use primary */
.topo-cloud {
    border-color: var(--primary);
    background: #fff;
}

/* Router - use primary */
.topo-router {
    border-color: var(--primary);
    background: #fff;
}

/* Firewall - use danger */
.topo-firewall {
    border-color: #e63757;
    background: #fff;
}
.topo-firewall:hover {
    border-color: #e63757;
    background: #fff;
}

/* Switch - use success */
.topo-switch {
    border-color: #17a673;
    background: #fff;
}
.topo-switch:hover {
    border-color: #17a673;
    background: #fff;
}
.topo-switch-core { padding: 12px 24px; }
.topo-switch-access { min-width: 85px; }

/* Server - use warning */
.topo-server {
    border-color: #e6a817;
    background: #fff;
    padding: 8px 12px;
}
.topo-server:hover {
    border-color: #e6a817;
    background: #fff;
}

/* Access Point - use primary */
.topo-ap {
    border-color: var(--primary);
    background: #fff;
    padding: 8px 12px;
}
.topo-ap:hover {
    border-color: var(--primary);
    background: #fff;
}

/* Node text */
.topo-node-name {
    font-weight: 700;
    font-size: 11px;
    color: var(--text-primary);
    margin-top: 5px;
    line-height: 1.2;
}
.topo-node-detail {
    font-size: 9px;
    color: var(--text-secondary);
    margin-top: 1px;
}
.topo-node-badge {
    font-size: 9px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    margin-top: 4px;
}
.bg-success-soft { background: #fff !important; color: #17a673; border: 1px solid #17a673; }
.bg-info-soft { background: #fff !important; color: var(--primary); border: 1px solid var(--primary); }

/* Connection lines */
.topo-line {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}
.topo-line-vertical {
    width: 1px;
    height: 24px;
    background: var(--border-color);
    margin: 0;
    position: relative;
}
.topo-line-vertical::before {
    content: '';
    position: absolute;
    top: -3px;
    left: 50%;
    transform: translateX(-50%);
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--text-secondary);
}
.topo-line-label {
    font-size: 9px;
    font-weight: 600;
    color: var(--text-secondary);
    background: #fff;
    padding: 1px 6px;
    border-radius: 3px;
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    white-space: nowrap;
    border: 1px solid var(--border-color);
}
.topo-line-horizontal {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    max-width: 460px;
    margin: 4px 0;
    position: relative;
}
.topo-line-h-segment {
    flex: 1;
    height: 1px;
    background: var(--border-color);
}
.topo-line-horizontal .topo-line-label {
    position: relative;
    left: auto;
    top: auto;
    transform: none;
    margin: 0 6px;
}

/* Groups */
.topo-group {
    border: 1px dashed var(--border-color);
    border-radius: 10px;
    padding: 14px;
    min-width: 180px;
    position: relative;
}
.topo-group-label {
    font-size: 9px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 1px;
    position: absolute;
    top: -7px;
    left: 12px;
    background: #fff;
    padding: 0 6px;
}
.topo-group-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
}

/* VLAN Bar */
.topo-vlan-bar {
    display: flex;
    justify-content: center;
    gap: 18px;
    flex-wrap: wrap;
    margin-top: 18px;
    padding: 10px 16px;
    border-top: 1px solid var(--border-color);
    width: 100%;
    background: #fff;
    border-radius: 8px;
    border: 1px solid var(--border-color);
}
.topo-vlan-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    font-weight: 600;
    color: var(--text-secondary);
}
.topo-vlan-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const servers = [
        { id: 'srv-dc01', name: 'SRV-DC01', ip: '192.168.1.10', type: 'Domain Controller', badge: 'bg-primary', cpu: 32, mem: 58, disk: 74, net: '125/45 Mbps', uptime: '45d 12h 30m', status: 'online', rebootProgress: 0 },
        { id: 'srv-file01', name: 'SRV-FILE01', ip: '192.168.1.20', type: 'File Server', badge: 'bg-info', cpu: 18, mem: 42, disk: 89, net: '85/30 Mbps', uptime: '30d 8h 15m', status: 'online', rebootProgress: 0 },
        { id: 'srv-web01', name: 'SRV-WEB01', ip: '192.168.1.30', type: 'Web Server', badge: 'bg-warning', cpu: 45, mem: 61, disk: 52, net: '210/65 Mbps', uptime: '15d 4h 50m', status: 'online', rebootProgress: 0 },
        { id: 'srv-db01', name: 'SRV-DB01', ip: '192.168.1.40', type: 'Database Server', badge: 'bg-secondary', cpu: 72, mem: 78, disk: 63, net: '95/40 Mbps', uptime: '60d 0h 10m', status: 'online', rebootProgress: 0 },
        { id: 'srv-bkp01', name: 'SRV-BKP01', ip: '192.168.1.50', type: 'Backup Server', badge: 'bg-dark', cpu: 8, mem: 25, disk: 41, net: '15/5 Mbps', uptime: '90d 6h 45m', status: 'online', rebootProgress: 0 },
        { id: 'srv-app01', name: 'SRV-APP01', ip: '192.168.1.60', type: 'Application Server', badge: 'bg-primary', cpu: 91, mem: 85, disk: 55, net: '180/70 Mbps', uptime: '20d 11h 22m', status: 'warning', rebootProgress: 0 }
    ];

    const services = [
        { id: 'svc-ad', name: 'Active Directory', server: 'SRV-DC01', port: '389, 636', status: 'running', progress: 0 },
        { id: 'svc-dns', name: 'DNS', server: 'SRV-DC01', port: '53', status: 'running', progress: 0 },
        { id: 'svc-dhcp', name: 'DHCP', server: 'SRV-DC01', port: '67, 68', status: 'running', progress: 0 },
        { id: 'svc-fileshare', name: 'File Share', server: 'SRV-FILE01', port: '445', status: 'running', progress: 0 },
        { id: 'svc-mysql', name: 'MySQL Database', server: 'SRV-DB01', port: '3306', status: 'running', progress: 0 },
        { id: 'svc-apache', name: 'Apache Web', server: 'SRV-WEB01', port: '80, 443', status: 'running', progress: 0 },
        { id: 'svc-backup', name: 'Backup Agent', server: 'SRV-BKP01', port: '10000', status: 'running', progress: 0 },
        { id: 'svc-helpdesk', name: 'Helpdesk App', server: 'SRV-APP01', port: '8080', status: 'warning', progress: 0 }
    ];

    const statusConfig = {
        'online':    { label: 'Online',       badge: 'bg-success',            icon: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' },
        'warning':   { label: 'High Load',    badge: 'bg-warning text-dark',  icon: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' },
        'offline':   { label: 'Offline',      badge: 'bg-secondary',          icon: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' },
        'crashed':   { label: 'Crashed',      badge: 'bg-danger',             icon: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' },
        'rebooting': { label: 'Rebooting...', badge: 'bg-info',               icon: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="spin"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>' }
    };

    function getBarClass(value) {
        if (value >= 80) return 'bg-danger';
        if (value >= 60) return 'bg-warning';
        return 'bg-success';
    }

    function fluctuate(base, range) {
        const delta = Math.floor(Math.random() * range * 2) - range;
        return Math.max(1, Math.min(99, base + delta));
    }

    function getStatusBadge(status) {
        const cfg = statusConfig[status] || statusConfig['online'];
        return `<span class="badge ${cfg.badge}">${cfg.icon} ${cfg.label}</span>`;
    }

    function getActionButton(s) {
        if (s.status === 'crashed') {
            return `<button class="btn btn-sm btn-danger reboot-btn" onclick="rebootServer('${s.id}')" title="Reboot Server">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                Reboot
            </button>`;
        }
        if (s.status === 'offline') {
            return `<button class="btn btn-sm btn-warning reboot-btn" onclick="rebootServer('${s.id}')" title="Power On">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 11-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                Power On
            </button>`;
        }
        if (s.status === 'rebooting') {
            return `<button class="btn btn-sm btn-info" disabled>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="spin"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                ${s.rebootProgress}%
            </button>`;
        }
        return `<button class="btn btn-sm btn-outline-secondary" onclick="rebootServer('${s.id}')" title="Restart Server">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
        </button>`;
    }

    function renderServers() {
        const tbody = document.getElementById('serverTableBody');
        let online = 0, warning = 0, offline = 0, crashed = 0, rebooting = 0;

        let html = '';
        servers.forEach(s => {
            if (s.status === 'online') online++;
            else if (s.status === 'warning') warning++;
            else if (s.status === 'offline') offline++;
            else if (s.status === 'crashed') crashed++;
            else if (s.status === 'rebooting') rebooting++;

            const isDown = (s.status === 'offline' || s.status === 'crashed' || s.status === 'rebooting');
            const cpuDisplay = isDown ? '--' : s.cpu + '%';
            const memDisplay = isDown ? '--' : s.mem + '%';
            const netDisplay = isDown ? '0/0 Mbps' : s.net;
            const uptimeDisplay = isDown ? 'N/A' : s.uptime;

            const cpuClass = isDown ? 'bg-secondary' : getBarClass(s.cpu);
            const memClass = isDown ? 'bg-secondary' : getBarClass(s.mem);
            const diskClass = getBarClass(s.disk);
            const cpuTextClass = (!isDown && s.cpu >= 80) ? ' text-danger fw-bold' : '';
            const memTextClass = (!isDown && s.mem >= 80) ? ' text-danger fw-bold' : '';
            const cpuWidth = isDown ? 0 : s.cpu;
            const memWidth = isDown ? 0 : s.mem;

            html += `<tr class="${s.status === 'crashed' ? 'table-danger' : s.status === 'rebooting' ? 'table-info' : ''}">
                <td class="fw-semibold">${s.name}</td>
                <td>${s.ip}</td>
                <td><span class="badge ${s.badge}">${s.type}</span></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                            <div class="progress-bar ${cpuClass}" style="width: ${cpuWidth}%;"></div>
                        </div>
                        <span class="small${cpuTextClass}">${cpuDisplay}</span>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                            <div class="progress-bar ${memClass}" style="width: ${memWidth}%;"></div>
                        </div>
                        <span class="small${memTextClass}">${memDisplay}</span>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                            <div class="progress-bar ${diskClass}" style="width: ${s.disk}%;"></div>
                        </div>
                        <span class="small">${s.disk}%</span>
                    </div>
                </td>
                <td><span class="small text-muted">${netDisplay}</span></td>
                <td><span class="small">${uptimeDisplay}</span></td>
                <td>${getStatusBadge(s.status)}</td>
                <td>${getActionButton(s)}</td>
            </tr>`;
        });

        tbody.innerHTML = html;

        document.getElementById('onlineCount').textContent = online + ' Online';
        document.getElementById('warningCount').textContent = warning + ' High Load';
        document.getElementById('offlineCount').textContent = offline + ' Offline';
        document.getElementById('crashedCount').textContent = crashed + ' Crashed';
        document.getElementById('rebootingCount').textContent = rebooting + ' Rebooting';

        document.getElementById('warningCount').style.display = warning > 0 ? '' : 'none';
        document.getElementById('offlineCount').style.display = offline > 0 ? '' : 'none';
        document.getElementById('crashedCount').style.display = crashed > 0 ? '' : 'none';
        document.getElementById('rebootingCount').style.display = rebooting > 0 ? '' : 'none';
    }

    window.rebootServer = function(id) {
        const s = servers.find(x => x.id === id);
        if (!s || s.status === 'rebooting') return;
        s.status = 'rebooting';
        s.rebootProgress = 0;
        renderServers();

        const interval = setInterval(() => {
            s.rebootProgress = Math.min(s.rebootProgress + Math.floor(Math.random() * 15) + 5, 100);
            renderServers();
            if (s.rebootProgress >= 100) {
                clearInterval(interval);
                s.status = 'online';
                s.cpu = Math.floor(Math.random() * 30) + 10;
                s.mem = Math.floor(Math.random() * 30) + 20;
                s.net = (Math.floor(Math.random() * 80) + 30) + '/' + (Math.floor(Math.random() * 40) + 10) + ' Mbps';
                s.uptime = '0d 0h 1m';
                s.rebootProgress = 0;
                renderServers();
            }
        }, 400);
    };

    function updateServers() {
        servers.forEach(s => {
            if (s.status === 'online' || s.status === 'warning') {
                s.cpu = fluctuate(s.cpu, 5);
                s.mem = fluctuate(s.mem, 3);
                s.net = fluctuate(parseInt(s.net.split('/')[0]), 10) + '/' + fluctuate(parseInt(s.net.split('/')[1].replace(' Mbps', '')), 5) + ' Mbps';

                if (s.cpu >= 85 || s.mem >= 85) {
                    s.status = 'warning';
                } else {
                    s.status = 'online';
                }
            }
        });

        if (Math.random() < 0.015) {
            const idx = Math.floor(Math.random() * servers.length);
            if (servers[idx].status === 'online' || servers[idx].status === 'warning') {
                servers[idx].status = 'crashed';
                servers[idx].cpu = 0;
                servers[idx].mem = 0;
                servers[idx].net = '0/0 Mbps';
            }
        }

        if (Math.random() < 0.01) {
            const idx = Math.floor(Math.random() * servers.length);
            if (servers[idx].status === 'online' || servers[idx].status === 'warning') {
                servers[idx].status = 'offline';
                servers[idx].cpu = 0;
                servers[idx].mem = 0;
                servers[idx].net = '0/0 Mbps';
            }
        }

        renderServers();
    }

    renderServers();
    setInterval(updateServers, 4000);

    // Services
    const serviceStatusConfig = {
        'running':  { label: 'Running',  badge: 'bg-success', icon: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' },
        'stopped':  { label: 'Stopped',  badge: 'bg-danger',  icon: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' },
        'warning':  { label: 'High Load', badge: 'bg-warning text-dark', icon: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' },
        'restarting': { label: 'Restarting...', badge: 'bg-info', icon: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="spin"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>' }
    };

    function renderServices() {
        const tbody = document.getElementById('servicesTableBody');
        let running = 0, stopped = 0, warning = 0, restarting = 0;
        let html = '';
        services.forEach(s => {
            const cfg = serviceStatusConfig[s.status] || serviceStatusConfig['running'];
            let rowClass = '';
            if (s.status === 'stopped') rowClass = 'table-danger';
            else if (s.status === 'warning') rowClass = 'table-warning';
            else if (s.status === 'restarting') rowClass = 'table-info';

            if (s.status === 'running') running++;
            else if (s.status === 'stopped') stopped++;
            else if (s.status === 'warning') warning++;
            else if (s.status === 'restarting') restarting++;

            let actionBtn = '';

            if (s.status === 'stopped') {
                actionBtn = `<button class="btn btn-sm btn-success" onclick="restartService('${s.id}')" title="Start Service">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg> Start
                </button>`;
            } else if (s.status === 'restarting') {
                actionBtn = `<button class="btn btn-sm btn-info" disabled>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="spin"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg> ${s.progress}%
                </button>`;
            } else {
                actionBtn = `<button class="btn btn-sm btn-outline-secondary" onclick="restartService('${s.id}')" title="Restart Service">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                </button>`;
            }

            html += `<tr class="${rowClass}">
                <td class="fw-semibold">${s.name}</td>
                <td>${s.server}</td>
                <td><code>${s.port}</code></td>
                <td><span class="badge ${cfg.badge}">${cfg.icon} ${cfg.label}</span></td>
                <td>${actionBtn}</td>
            </tr>`;
        });
        tbody.innerHTML = html;

        document.getElementById('svcOnlineCount').textContent = running + ' Running';
        document.getElementById('svcWarningCount').textContent = warning + ' High Load';
        document.getElementById('svcStoppedCount').textContent = stopped + ' Stopped';
        document.getElementById('svcRestartingCount').textContent = restarting + ' Restarting';

        document.getElementById('svcWarningCount').style.display = warning > 0 ? '' : 'none';
        document.getElementById('svcStoppedCount').style.display = stopped > 0 ? '' : 'none';
        document.getElementById('svcRestartingCount').style.display = restarting > 0 ? '' : 'none';
    }

    window.restartService = function(id) {
        const svc = services.find(x => x.id === id);
        if (!svc || svc.status === 'restarting') return;
        svc.status = 'restarting';
        svc.progress = 0;
        renderServices();

        const interval = setInterval(() => {
            svc.progress = Math.min(svc.progress + Math.floor(Math.random() * 20) + 10, 100);
            renderServices();
            if (svc.progress >= 100) {
                clearInterval(interval);
                svc.status = 'running';
                svc.progress = 0;
                renderServices();
            }
        }, 300);
    };

    function updateServices() {
        services.forEach(s => {
            if (s.status === 'running' || s.status === 'warning') {
                if (Math.random() < 0.01) {
                    s.status = 'stopped';
                } else if (Math.random() < 0.02) {
                    s.status = s.status === 'running' ? 'warning' : 'running';
                }
            }
        });
        renderServices();
    }

    renderServices();
    setInterval(updateServices, 5000);

    const ctx = document.getElementById('networkLoadChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(45, 90, 142, 0.3)');
    gradient.addColorStop(1, 'rgba(45, 90, 142, 0.0)');

    const maxLabels = 20;
    let downloadData = [324, 318, 330, 312, 340, 328, 335, 320, 345, 310, 338, 325, 342, 315, 332, 348, 308, 340, 326, 334];
    let uploadData = [87, 82, 90, 78, 95, 85, 88, 80, 92, 76, 89, 83, 91, 79, 86, 94, 77, 88, 84, 87];
    let labels = [];
    const now = new Date();
    for (let i = maxLabels; i > 0; i--) {
        const t = new Date(now.getTime() - i * 3000);
        labels.push(t.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' }));
    }

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Download (Mbps)',
                data: downloadData,
                borderColor: '#2d5a8e',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointRadius: 2,
                pointBackgroundColor: '#2d5a8e',
                borderWidth: 2
            }, {
                label: 'Upload (Mbps)',
                data: uploadData,
                borderColor: '#17a673',
                backgroundColor: 'transparent',
                borderDash: [5, 5],
                tension: 0.4,
                pointRadius: 2,
                pointBackgroundColor: '#17a673',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 500 },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, padding: 15 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 500,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { callback: function(value) { return value + ' Mbps'; } }
                },
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 8 }
                }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });

    function getBarColor(value, max) {
        const pct = (value / max) * 100;
        if (pct >= 80) return 'bg-danger';
        if (pct >= 60) return 'bg-warning';
        return 'bg-success';
    }

    function getLatencyStatus(ms) {
        if (ms <= 15) return { text: 'Excellent', class: 'bg-success' };
        if (ms <= 30) return { text: 'Good', class: 'bg-warning' };
        return { text: 'Poor', class: 'bg-danger' };
    }

    function updateBandwidth() {
        const download = Math.floor(Math.random() * 180) + 200;
        const upload = Math.floor(Math.random() * 70) + 50;
        const latency = Math.floor(Math.random() * 20) + 5;
        const dlPct = Math.round((download / 500) * 100);
        const ulPct = Math.round((upload / 200) * 100);
        const latPct = Math.min(latency * 2, 100);
        const latStatus = getLatencyStatus(latency);

        document.getElementById('downloadSpeed').textContent = download + '.0 Mbps';
        document.getElementById('downloadBar').style.width = dlPct + '%';
        document.getElementById('downloadBar').className = 'progress-bar ' + getBarColor(download, 500);
        document.getElementById('downloadPercent').textContent = dlPct + '% of 500 Mbps';

        document.getElementById('uploadSpeed').textContent = upload + '.0 Mbps';
        document.getElementById('uploadBar').style.width = ulPct + '%';
        document.getElementById('uploadBar').className = 'progress-bar ' + (ulPct >= 80 ? 'bg-danger' : ulPct >= 60 ? 'bg-warning' : 'bg-primary');
        document.getElementById('uploadPercent').textContent = ulPct + '% of 200 Mbps';

        document.getElementById('latencyValue').textContent = latency + ' ms';
        document.getElementById('latencyBar').style.width = latPct + '%';
        document.getElementById('latencyBar').className = 'progress-bar ' + latStatus.class;
        document.getElementById('latencyStatus').textContent = latStatus.text;

        return { download, upload };
    }

    function updateChart(speeds) {
        const time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        chart.data.labels.push(time);
        chart.data.labels.shift();
        chart.data.datasets[0].data.push(speeds.download);
        chart.data.datasets[0].data.shift();
        chart.data.datasets[1].data.push(speeds.upload);
        chart.data.datasets[1].data.shift();
        chart.update('none');
    }

    function updateTimestamp() {
        document.getElementById('lastUpdated').textContent = 'Updated ' + new Date().toLocaleTimeString();
    }

    setInterval(function() {
        const speeds = updateBandwidth();
        updateChart(speeds);
        updateTimestamp();
    }, 3000);

    updateTimestamp();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
