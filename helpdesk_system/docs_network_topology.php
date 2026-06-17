<?php
$page_title = 'Network Topology Documentation';
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
                        <a class="nav-link text-start" href="#tiers">Network Tiers</a>
                        <a class="nav-link text-start" href="#edge">Edge / WAN</a>
                        <a class="nav-link text-start" href="#security">Security Layer</a>
                        <a class="nav-link text-start" href="#distribution">Distribution Layer</a>
                        <a class="nav-link text-start" href="#wireless">Wireless Access</a>
                        <a class="nav-link text-start" href="#legend">Legend</a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="fw-bold mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -4px;"><circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 010 8.49m-8.48-.01a6 6 0 010-8.49m11.31-2.82a10 10 0 010 14.14m-14.14 0a10 10 0 010-14.14"/></svg>
                        Network Topology Documentation
                    </h3>

                    <section id="overview" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Overview</h5>
                        <p>The Network Topology diagram shows the complete network infrastructure of the company, organized in a hierarchical tiered structure. Each tier represents a different layer of the network.</p>
                        <div class="alert alert-success">
                            <strong>Status:</strong> All devices show real-time status with green indicators when online.
                        </div>
                    </section>

                    <section id="tiers" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Network Tiers</h5>
                        <p>The network is organized into 4 tiers:</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="fw-bold">Tier 1: Edge / WAN</h6>
                                    <p class="small mb-0">Internet connection and core router</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="fw-bold">Tier 2: Security Layer</h6>
                                    <p class="small mb-0">Firewall and security devices</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="fw-bold">Tier 3: Distribution</h6>
                                    <p class="small mb-0">Network switches and access points</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="fw-bold">Tier 4: Wireless</h6>
                                    <p class="small mb-0">WiFi access points and clients</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="edge" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Tier 1: Edge / WAN</h5>
                        <p>This tier contains the internet connection and core routing:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Device</th>
                                        <th>Model</th>
                                        <th>IP Address</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Internet</strong></td>
                                        <td>WAN Connection</td>
                                        <td>N/A</td>
                                        <td>PLDT Enterprise, 500 Mbps Fiber</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Core Router</strong></td>
                                        <td>Cisco ISR 4331</td>
                                        <td>192.168.1.1</td>
                                        <td>Gateway: 192.168.1.254, 4 LAN Ports</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <strong>Connection:</strong> Internet connects to Router via 1 Gbps link.
                        </div>
                    </section>

                    <section id="security" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Tier 2: Security Layer</h5>
                        <p>The firewall provides network security:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Device</th>
                                        <th>Model</th>
                                        <th>IP Address</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Main Firewall</strong></td>
                                        <td>FortiGate 100F</td>
                                        <td>192.168.1.254</td>
                                        <td>NAT/Route mode, 256 rules, 12 VPN tunnels</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <strong>Features:</strong> Intrusion prevention, web filtering, VPN gateway, traffic monitoring.
                        </div>
                    </section>

                    <section id="distribution" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Tier 3: Distribution Layer</h5>
                        <p>Network switches provide wired connectivity:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Device</th>
                                        <th>Model</th>
                                        <th>IP Address</th>
                                        <th>Ports</th>
                                        <th>VLANs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Floor 1 Switch</strong></td>
                                        <td>Cisco 2960</td>
                                        <td>192.168.1.2</td>
                                        <td>24/48 (18 connected)</td>
                                        <td>10, 20</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Floor 2 Switch</strong></td>
                                        <td>Cisco 2960</td>
                                        <td>192.168.1.3</td>
                                        <td>24/48 (22 connected)</td>
                                        <td>10, 30</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Floor 3 Switch</strong></td>
                                        <td>Cisco 2960</td>
                                        <td>192.168.1.4</td>
                                        <td>24/48 (15 connected)</td>
                                        <td>10, 40</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <strong>Connection:</strong> Switches connect to Firewall via Trunk links.
                        </div>
                    </section>

                    <section id="wireless" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Tier 4: Wireless Access</h5>
                        <p>WiFi access points provide wireless connectivity:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Device</th>
                                        <th>Model</th>
                                        <th>IP Address</th>
                                        <th>SSID</th>
                                        <th>Clients</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Lobby WiFi AP</strong></td>
                                        <td>Ubiquiti U6-Pro</td>
                                        <td>192.168.1.101</td>
                                        <td>Company-WiFi</td>
                                        <td>24</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cafeteria WiFi AP</strong></td>
                                        <td>Ubiquiti U6-Pro</td>
                                        <td>192.168.1.102</td>
                                        <td>Company-WiFi</td>
                                        <td>18</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <strong>Features:</strong> Dual-band (2.4G/5G), PoE powered, signal strength monitoring.
                        </div>
                    </section>

                    <section id="legend" class="mb-4">
                        <h5 class="fw-bold text-primary mb-3">Legend</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-secondary rounded" style="width: 24px; height: 24px;"></div>
                                    <span>Internet</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded" style="width: 24px; height: 24px; background: var(--primary);"></div>
                                    <span>Router</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded" style="width: 24px; height: 24px; background: #e63757;"></div>
                                    <span>Firewall</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded" style="width: 24px; height: 24px; background: #17a673;"></div>
                                    <span>Switch</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded" style="width: 24px; height: 24px; background: var(--primary);"></div>
                                    <span>Access Point</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-success rounded-circle" style="width: 12px; height: 12px;"></div>
                                    <span>Online Status</span>
                                </div>
                            </div>
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
