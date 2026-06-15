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
                            <span class="badge bg-danger" id="offlineCount">0 Offline</span>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const servers = [
        { id: 'srv-dc01', name: 'SRV-DC01', ip: '192.168.1.10', type: 'Domain Controller', badge: 'bg-primary', cpu: 32, mem: 58, disk: 74, net: '125/45 Mbps', uptime: '45d 12h 30m', status: 'online' },
        { id: 'srv-file01', name: 'SRV-FILE01', ip: '192.168.1.20', type: 'File Server', badge: 'bg-info', cpu: 18, mem: 42, disk: 89, net: '85/30 Mbps', uptime: '30d 8h 15m', status: 'online' },
        { id: 'srv-web01', name: 'SRV-WEB01', ip: '192.168.1.30', type: 'Web Server', badge: 'bg-warning', cpu: 45, mem: 61, disk: 52, net: '210/65 Mbps', uptime: '15d 4h 50m', status: 'online' },
        { id: 'srv-db01', name: 'SRV-DB01', ip: '192.168.1.40', type: 'Database Server', badge: 'bg-secondary', cpu: 72, mem: 78, disk: 63, net: '95/40 Mbps', uptime: '60d 0h 10m', status: 'online' },
        { id: 'srv-bkp01', name: 'SRV-BKP01', ip: '192.168.1.50', type: 'Backup Server', badge: 'bg-dark', cpu: 8, mem: 25, disk: 41, net: '15/5 Mbps', uptime: '90d 6h 45m', status: 'online' },
        { id: 'srv-app01', name: 'SRV-APP01', ip: '192.168.1.60', type: 'Application Server', badge: 'bg-primary', cpu: 91, mem: 85, disk: 55, net: '180/70 Mbps', uptime: '20d 11h 22m', status: 'warning' }
    ];

    function getBarClass(value) {
        if (value >= 80) return 'bg-danger';
        if (value >= 60) return 'bg-warning';
        return 'bg-success';
    }

    function getStatusBadge(status) {
        const map = {
            'online': '<span class="badge bg-success">Online</span>',
            'warning': '<span class="badge bg-warning text-dark">High Load</span>',
            'offline': '<span class="badge bg-danger">Offline</span>'
        };
        return map[status] || map['online'];
    }

    function fluctuate(base, range) {
        const delta = Math.floor(Math.random() * range * 2) - range;
        return Math.max(1, Math.min(99, base + delta));
    }

    function renderServers() {
        const tbody = document.getElementById('serverTableBody');
        let online = 0, warning = 0, offline = 0;

        let html = '';
        servers.forEach(s => {
            if (s.status === 'online') online++;
            else if (s.status === 'warning') warning++;
            else offline++;

            const cpuClass = getBarClass(s.cpu);
            const memClass = getBarClass(s.mem);
            const diskClass = getBarClass(s.disk);
            const cpuTextClass = s.cpu >= 80 ? ' text-danger fw-bold' : '';
            const memTextClass = s.mem >= 80 ? ' text-danger fw-bold' : '';

            html += `<tr>
                <td class="fw-semibold">${s.name}</td>
                <td>${s.ip}</td>
                <td><span class="badge ${s.badge}">${s.type}</span></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                            <div class="progress-bar ${cpuClass}" style="width: ${s.cpu}%;"></div>
                        </div>
                        <span class="small${cpuTextClass}">${s.cpu}%</span>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                            <div class="progress-bar ${memClass}" style="width: ${s.mem}%;"></div>
                        </div>
                        <span class="small${memTextClass}">${s.mem}%</span>
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
                <td><span class="small text-muted">${s.net}</span></td>
                <td><span class="small">${s.uptime}</span></td>
                <td>${getStatusBadge(s.status)}</td>
            </tr>`;
        });

        tbody.innerHTML = html;

        document.getElementById('onlineCount').textContent = online + ' Online';
        document.getElementById('warningCount').textContent = warning + ' High Load';
        document.getElementById('offlineCount').textContent = offline + ' Offline';

        document.getElementById('warningCount').style.display = warning > 0 ? '' : 'none';
        document.getElementById('offlineCount').style.display = offline > 0 ? '' : 'none';
    }

    function updateServers() {
        servers.forEach(s => {
            if (s.status !== 'offline') {
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

        if (Math.random() < 0.02) {
            const idx = Math.floor(Math.random() * servers.length);
            if (servers[idx].status !== 'offline') {
                servers[idx].status = 'offline';
                servers[idx].cpu = 0;
                servers[idx].mem = 0;
                servers[idx].net = '0/0 Mbps';
            }
        }

        if (Math.random() < 0.05) {
            const idx = Math.floor(Math.random() * servers.length);
            if (servers[idx].status === 'offline') {
                servers[idx].status = 'online';
                servers[idx].cpu = Math.floor(Math.random() * 40) + 10;
                servers[idx].mem = Math.floor(Math.random() * 40) + 20;
                servers[idx].net = Math.floor(Math.random() * 100) + 20 + '/' + Math.floor(Math.random() * 50) + 10 + ' Mbps';
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
