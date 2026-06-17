<?php
$page_title = 'Server Room Documentation';
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
                        <a class="nav-link text-start" href="#isp">ISP Monitoring</a>
                        <a class="nav-link text-start" href="#network">Network Load</a>
                        <a class="nav-link text-start" href="#servers">Server Status</a>
                        <a class="nav-link text-start" href="#reboot">Reboot Servers</a>
                        <a class="nav-link text-start" href="#devices">Network Devices</a>
                        <a class="nav-link text-start" href="#services">Services</a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="fw-bold mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -4px;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                        Server Room Documentation
                    </h3>

                    <section id="overview" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Overview</h5>
                        <p>The Server Room module provides real-time monitoring of all IT infrastructure including servers, network devices, and services. It displays live data for ISP connectivity, network load, server status, and device health.</p>
                        <div class="alert alert-warning">
                            <strong>Note:</strong> All data updates automatically every few seconds. No manual refresh needed.
                        </div>
                    </section>

                    <section id="isp" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">ISP Monitoring</h5>
                        <p>The ISP Monitoring section shows internet connectivity status:</p>
                        <ul>
                            <li><strong>Primary ISP</strong> - PLDT Enterprise (500 Mbps)</li>
                            <li><strong>Secondary ISP</strong> - Globe Business (200 Mbps)</li>
                            <li><strong>Connection Type</strong> - Dedicated Fiber</li>
                        </ul>
                        <div class="bg-light p-3 rounded">
                            <strong>Indicator:</strong> Green "Connected" badge means the ISP is online. Red "Disconnected" means outage.
                        </div>
                    </section>

                    <section id="network" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Network Load</h5>
                        <p>The Network Load chart displays real-time bandwidth usage:</p>
                        <ul>
                            <li><strong>Download Speed</strong> - Current download throughput</li>
                            <li><strong>Upload Speed</strong> - Current upload throughput</li>
                            <li><strong>Latency</strong> - Network response time (Excellent/Good/Poor)</li>
                        </ul>
                        <p>The chart updates every 3 seconds with a sliding window of 20 data points.</p>
                    </section>

                    <section id="servers" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Server Status</h5>
                        <p>The Server Status table shows all servers with their current metrics:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Column</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td><strong>Server Name</strong></td><td>Hostname of the server</td></tr>
                                    <tr><td><strong>IP Address</strong></td><td>Network IP address</td></tr>
                                    <tr><td><strong>Type</strong></td><td>Server role (DC, File, Web, etc.)</td></tr>
                                    <tr><td><strong>CPU Usage</strong></td><td>Current CPU utilization (color-coded)</td></tr>
                                    <tr><td><strong>Memory Usage</strong></td><td>Current RAM utilization</td></tr>
                                    <tr><td><strong>Disk Usage</strong></td><td>Storage utilization</td></tr>
                                    <tr><td><strong>Network I/O</strong></td><td>Download/Upload speed</td></tr>
                                    <tr><td><strong>Uptime</strong></td><td>Time since last restart</td></tr>
                                    <tr><td><strong>Status</strong></td><td>Online, High Load, Offline, Crashed</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section id="reboot" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Reboot Servers</h5>
                        <p>You can restart servers directly from the interface:</p>
                        <ol>
                            <li>Find the server in the Server Status table</li>
                            <li>Click the <strong>Action button</strong> in the Actions column:</li>
                        </ol>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <span class="badge bg-success">Online</span>
                                    <p class="mt-2 mb-0 small">Shows a restart icon. Click to restart the server.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <span class="badge bg-warning text-dark">High Load</span>
                                    <p class="mt-2 mb-0 small">Shows a restart icon. Click to restart the server.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <span class="badge bg-danger">Crashed</span>
                                    <p class="mt-2 mb-0 small">Shows red "Reboot" button. Click to reboot the server.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <span class="badge bg-secondary">Offline</span>
                                    <p class="mt-2 mb-0 small">Shows yellow "Power On" button. Click to power on.</p>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <strong>Reboot Progress:</strong> During reboot, you'll see a progress percentage (0-100%). The server will automatically come back online when complete.
                        </div>
                    </section>

                    <section id="devices" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Network Devices</h5>
                        <p>Shows all network infrastructure devices:</p>
                        <ul>
                            <li><strong>Core Router</strong> - 192.168.1.1 (Gateway)</li>
                            <li><strong>Main Firewall</strong> - 192.168.1.254 (Security)</li>
                            <li><strong>Floor Switches</strong> - 192.168.1.2-4 (Access layer)</li>
                            <li><strong>WiFi Access Points</strong> - 192.168.1.101-102 (Wireless)</li>
                        </ul>
                    </section>

                    <section id="services" class="mb-4">
                        <h5 class="fw-bold text-primary mb-3">Services</h5>
                        <p>Monitors critical services running on servers:</p>
                        <ul>
                            <li><strong>Active Directory</strong> - User authentication</li>
                            <li><strong>DNS</strong> - Domain name resolution</li>
                            <li><strong>DHCP</strong> - IP address assignment</li>
                            <li><strong>File Share</strong> - File storage access</li>
                            <li><strong>MySQL Database</strong> - Database service</li>
                            <li><strong>Apache Web</strong> - Web server</li>
                            <li><strong>Backup Agent</strong> - Backup service</li>
                            <li><strong>Helpdesk App</strong> - This application</li>
                        </ul>
                        <div class="bg-light p-3 rounded">
                            <strong>Restart Service:</strong> Click the "Restart" button to restart any stopped or running service. The service will show progress during restart.
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.nav-pills .nav-link').forEach(link => {
    link.addEventListener('click', function() {
        document.querySelectorAll('.nav-pills .nav-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
