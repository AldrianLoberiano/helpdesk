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

    <!-- Network Topology -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 010 8.49m-8.48-.01a6 6 0 010-8.49m11.31-2.82a10 10 0 010 14.14m-14.14 0a10 10 0 010-14.14"/></svg>
                        Network Topology
                    </h5>
                    <span class="badge bg-success pulse-badge">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor" style="vertical-align: 1px;"><circle cx="4" cy="4" r="4"/></svg>
                        All Systems Operational
                    </span>
                </div>
                <div class="card-body">
                    <div class="topology-container" id="topologyContainer">
                        <!-- Internet -->
                        <div class="topo-node topo-internet" style="left: 50%; top: 0;">
                            <div class="topo-icon topo-icon-internet">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                            </div>
                            <div class="topo-label">Internet</div>
                            <div class="topo-sublabel">WAN</div>
                        </div>

                        <!-- Core Router -->
                        <div class="topo-node topo-router" style="left: 50%; top: 18%;">
                            <div class="topo-icon topo-icon-router">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="14" width="20" height="7" rx="2" ry="2"/><rect x="2" y="3" width="20" height="7" rx="2" ry="2"/><line x1="12" y1="10" x2="12" y2="14"/></svg>
                            </div>
                            <div class="topo-label">Core Router</div>
                            <div class="topo-sublabel">192.168.1.1</div>
                            <span class="topo-status topo-status-online"></span>
                        </div>

                        <!-- Main Firewall -->
                        <div class="topo-node topo-firewall" style="left: 50%; top: 34%;">
                            <div class="topo-icon topo-icon-firewall">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <div class="topo-label">Main Firewall</div>
                            <div class="topo-sublabel">192.168.1.254</div>
                            <span class="topo-status topo-status-online"></span>
                        </div>

                        <!-- Server Room -->
                        <div class="topo-server-room" style="left: 50%; top: 50%;">
                            <div class="topo-server-room-label">Server Room</div>
                            <div class="topo-server-room-box">
                                <div class="topo-server" title="SRV-DC01 - Domain Controller">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                                    <span>DC01</span>
                                </div>
                                <div class="topo-server" title="SRV-FILE01 - File Server">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                                    <span>FILE01</span>
                                </div>
                                <div class="topo-server" title="SRV-WEB01 - Web Server">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                                    <span>WEB01</span>
                                </div>
                                <div class="topo-server" title="SRV-DB01 - Database Server">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                                    <span>DB01</span>
                                </div>
                                <div class="topo-server" title="SRV-BKP01 - Backup Server">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                                    <span>BKP01</span>
                                </div>
                                <div class="topo-server topo-server-warning" title="SRV-APP01 - Application Server (High Load)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                                    <span>APP01</span>
                                </div>
                            </div>
                        </div>

                        <!-- Floor 1 Switch -->
                        <div class="topo-node topo-switch" style="left: 20%; top: 62%;">
                            <div class="topo-icon topo-icon-switch">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                            </div>
                            <div class="topo-label">Floor 1 Switch</div>
                            <div class="topo-sublabel">192.168.1.2</div>
                            <span class="topo-status topo-status-online"></span>
                        </div>

                        <!-- Floor 2 Switch -->
                        <div class="topo-node topo-switch" style="left: 50%; top: 62%;">
                            <div class="topo-icon topo-icon-switch">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                            </div>
                            <div class="topo-label">Floor 2 Switch</div>
                            <div class="topo-sublabel">192.168.1.3</div>
                            <span class="topo-status topo-status-online"></span>
                        </div>

                        <!-- Floor 3 Switch -->
                        <div class="topo-node topo-switch" style="left: 80%; top: 62%;">
                            <div class="topo-icon topo-icon-switch">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                            </div>
                            <div class="topo-label">Floor 3 Switch</div>
                            <div class="topo-sublabel">192.168.1.4</div>
                            <span class="topo-status topo-status-online"></span>
                        </div>

                        <!-- Lobby WiFi AP -->
                        <div class="topo-node topo-ap" style="left: 30%; top: 80%;">
                            <div class="topo-icon topo-icon-ap">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0114.08 0"/><path d="M1.42 9a16 16 0 0121.16 0"/><path d="M8.53 16.11a6 6 0 016.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                            </div>
                            <div class="topo-label">Lobby WiFi AP</div>
                            <div class="topo-sublabel">192.168.1.101</div>
                            <span class="topo-status topo-status-online"></span>
                        </div>

                        <!-- Cafeteria WiFi AP -->
                        <div class="topo-node topo-ap" style="left: 70%; top: 80%;">
                            <div class="topo-icon topo-icon-ap">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0114.08 0"/><path d="M1.42 9a16 16 0 0121.16 0"/><path d="M8.53 16.11a6 6 0 016.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                            </div>
                            <div class="topo-label">Cafeteria WiFi AP</div>
                            <div class="topo-sublabel">192.168.1.102</div>
                            <span class="topo-status topo-status-online"></span>
                        </div>

                        <!-- Connection Lines (SVG) -->
                        <svg class="topo-lines" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <!-- Internet to Router -->
                            <line x1="50" y1="6" x2="50" y2="18" class="topo-line"/>
                            <!-- Router to Firewall -->
                            <line x1="50" y1="24" x2="50" y2="34" class="topo-line"/>
                            <!-- Firewall to Server Room -->
                            <line x1="50" y1="40" x2="50" y2="50" class="topo-line"/>
                            <!-- Firewall to Floor 1 Switch -->
                            <line x1="50" y1="40" x2="20" y2="62" class="topo-line"/>
                            <!-- Firewall to Floor 2 Switch -->
                            <line x1="50" y1="40" x2="50" y2="62" class="topo-line"/>
                            <!-- Firewall to Floor 3 Switch -->
                            <line x1="50" y1="40" x2="80" y2="62" class="topo-line"/>
                            <!-- Floor 1 Switch to Lobby AP -->
                            <line x1="20" y1="68" x2="30" y2="80" class="topo-line topo-line-wifi"/>
                            <!-- Floor 3 Switch to Cafeteria AP -->
                            <line x1="80" y1="68" x2="70" y2="80" class="topo-line topo-line-wifi"/>
                        </svg>
                    </div>

                    <!-- Legend -->
                    <div class="d-flex justify-content-center gap-4 mt-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <div class="topo-legend-dot topo-icon-router" style="width: 16px; height: 16px;"></div>
                            <span class="small text-muted">Router</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="topo-legend-dot topo-icon-firewall" style="width: 16px; height: 16px;"></div>
                            <span class="small text-muted">Firewall</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="topo-legend-dot topo-icon-switch" style="width: 16px; height: 16px;"></div>
                            <span class="small text-muted">Switch</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="topo-legend-dot topo-icon-ap" style="width: 16px; height: 16px;"></div>
                            <span class="small text-muted">Access Point</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="topo-legend-dot bg-dark" style="width: 16px; height: 16px;"></div>
                            <span class="small text-muted">Server</span>
                        </div>
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
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <h5 class="mb-0 fw-bold">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        Services
                    </h5>
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
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">Active Directory</td>
                                    <td>SRV-DC01</td>
                                    <td>389, 636</td>
                                    <td><span class="badge bg-success">Running</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">DNS</td>
                                    <td>SRV-DC01</td>
                                    <td>53</td>
                                    <td><span class="badge bg-success">Running</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">DHCP</td>
                                    <td>SRV-DC01</td>
                                    <td>67, 68</td>
                                    <td><span class="badge bg-success">Running</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">File Share</td>
                                    <td>SRV-FILE01</td>
                                    <td>445</td>
                                    <td><span class="badge bg-success">Running</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">MySQL Database</td>
                                    <td>SRV-DB01</td>
                                    <td>3306</td>
                                    <td><span class="badge bg-success">Running</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Apache Web</td>
                                    <td>SRV-WEB01</td>
                                    <td>80, 443</td>
                                    <td><span class="badge bg-success">Running</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Backup Agent</td>
                                    <td>SRV-BKP01</td>
                                    <td>10000</td>
                                    <td><span class="badge bg-success">Running</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Helpdesk App</td>
                                    <td>SRV-APP01</td>
                                    <td>8080</td>
                                    <td><span class="badge bg-warning text-dark">High Load</span></td>
                                </tr>
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

/* Network Topology */
.topology-container {
    position: relative;
    height: 450px;
    background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
    border-radius: 12px;
    overflow: hidden;
}
.topo-lines {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}
.topo-line {
    stroke: #adb5bd;
    stroke-width: 0.3;
    stroke-dasharray: 2 1;
}
.topo-line-wifi {
    stroke: #20c997;
    stroke-dasharray: 1 1;
}
.topo-node {
    position: absolute;
    transform: translateX(-50%);
    text-align: center;
    z-index: 10;
}
.topo-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 6px;
    color: white;
    position: relative;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: transform 0.2s, box-shadow 0.2s;
}
.topo-icon:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}
.topo-icon-internet { background: linear-gradient(135deg, #6c757d, #495057); }
.topo-icon-router { background: linear-gradient(135deg, #2d5a8e, #1a3a5c); }
.topo-icon-firewall { background: linear-gradient(135deg, #e63757, #c5283f); }
.topo-icon-switch { background: linear-gradient(135deg, #17a673, #0d8f5f); }
.topo-icon-ap { background: linear-gradient(135deg, #20c997, #12b886); }
.topo-label {
    font-weight: 600;
    font-size: 12px;
    color: #1a1d21;
    white-space: nowrap;
}
.topo-sublabel {
    font-size: 10px;
    color: #6c757d;
    white-space: nowrap;
}
.topo-status {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid white;
}
.topo-status-online { background: #28a745; box-shadow: 0 0 6px #28a745; }
.topo-status-offline { background: #dc3545; }
.topo-server-room {
    position: absolute;
    transform: translateX(-50%);
    z-index: 10;
}
.topo-server-room-label {
    text-align: center;
    font-weight: 700;
    font-size: 13px;
    color: #1a1d21;
    margin-bottom: 8px;
    background: rgba(255,255,255,0.9);
    padding: 4px 16px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.topo-server-room-box {
    background: rgba(26,29,33,0.95);
    border-radius: 10px;
    padding: 12px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: center;
    max-width: 320px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.topo-server {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 6px;
    padding: 4px 8px;
    display: flex;
    align-items: center;
    gap: 4px;
    color: #28a745;
    font-size: 10px;
    font-weight: 600;
    transition: all 0.2s;
}
.topo-server:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.05);
}
.topo-server-warning {
    color: #ffc107;
    border-color: rgba(255,193,7,0.3);
}
.topo-legend-dot {
    border-radius: 4px;
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
                s.net = Math.floor(Math.random() * 80) + 30 + '/' + Math.floor(Math.random() * 40) + 10 + ' Mbps';
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
