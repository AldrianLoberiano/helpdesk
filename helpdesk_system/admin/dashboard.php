<?php
$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Admin');

$db = getDB();

$stats = [];

$stmt = $db->query("SELECT COUNT(*) FROM tickets");
$stats['total_tickets'] = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM tickets WHERE status NOT IN ('Resolved', 'Closed')");
$stats['open_tickets'] = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM tickets WHERE status = 'Pending'");
$stats['pending_tickets'] = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM tickets WHERE status = 'Resolved'");
$stats['resolved_tickets'] = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM tickets WHERE status = 'Closed'");
$stats['closed_tickets'] = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM users WHERE is_active = 1");
$stats['total_users'] = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE r.role_name = 'Technician' AND u.is_active = 1");
$stats['total_technicians'] = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM tickets WHERE DATE(created_at) = CURDATE()");
$stats['today_tickets'] = $stmt->fetchColumn();

// Month-over-month changes
$this_month = $db->query("SELECT COUNT(*) FROM tickets WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())")->fetchColumn();
$last_month = $db->query("SELECT COUNT(*) FROM tickets WHERE MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))")->fetchColumn();
$stats['tickets_change'] = $last_month > 0 ? round((($this_month - $last_month) / $last_month) * 100) : ($this_month > 0 ? 100 : 0);

$this_month_resolved = $db->query("SELECT COUNT(*) FROM tickets WHERE status IN ('Resolved','Closed') AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())")->fetchColumn();
$last_month_resolved = $db->query("SELECT COUNT(*) FROM tickets WHERE status IN ('Resolved','Closed') AND MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))")->fetchColumn();
$stats['resolved_change'] = $last_month_resolved > 0 ? round((($this_month_resolved - $last_month_resolved) / $last_month_resolved) * 100) : ($this_month_resolved > 0 ? 100 : 0);

$this_month_open = $db->query("SELECT COUNT(*) FROM tickets WHERE status NOT IN ('Resolved','Closed') AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())")->fetchColumn();
$last_month_open = $db->query("SELECT COUNT(*) FROM tickets WHERE status NOT IN ('Resolved','Closed') AND MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))")->fetchColumn();
$stats['open_change'] = $last_month_open > 0 ? round((($this_month_open - $last_month_open) / $last_month_open) * 100) : ($this_month_open > 0 ? 100 : 0);

$this_month_pending = $db->query("SELECT COUNT(*) FROM tickets WHERE status = 'Pending' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())")->fetchColumn();
$last_month_pending = $db->query("SELECT COUNT(*) FROM tickets WHERE status = 'Pending' AND MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))")->fetchColumn();
$stats['pending_change'] = $last_month_pending > 0 ? round((($this_month_pending - $last_month_pending) / $last_month_pending) * 100) : 0;

$stmt = $db->query("SELECT t.*, tc.category_name, u.full_name as creator_name, 
                    CASE WHEN t.assigned_to IS NOT NULL THEN tu.full_name ELSE 'Unassigned' END as assignee_name
                    FROM tickets t 
                    JOIN ticket_categories tc ON t.category_id = tc.id
                    JOIN users u ON t.created_by = u.id
                    LEFT JOIN users tu ON t.assigned_to = tu.id
                    ORDER BY t.created_at DESC LIMIT 10");
$recent_tickets = $stmt->fetchAll();

$stmt = $db->query("SELECT status, COUNT(*) as count FROM tickets GROUP BY status");
$status_data = $stmt->fetchAll();

$stmt = $db->query("SELECT priority, COUNT(*) as count FROM tickets GROUP BY priority");
$priority_data = $stmt->fetchAll();

$stmt = $db->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                    FROM tickets 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY month ORDER BY month");
$monthly_data = $stmt->fetchAll();

$stmt = $db->query("SELECT u.full_name, 
                    COUNT(t.id) as total_tickets,
                    SUM(CASE WHEN t.status = 'Resolved' OR t.status = 'Closed' THEN 1 ELSE 0 END) as resolved_tickets
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id
                    LEFT JOIN tickets t ON u.id = t.assigned_to
                    WHERE r.role_name = 'Technician' AND u.is_active = 1
                    GROUP BY u.id, u.full_name
                    ORDER BY resolved_tickets DESC LIMIT 5");
$technician_performance = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-1 fw-bold">Dashboard</h1>
                <p class="text-muted mb-0 small">Overview of your helpdesk system</p>
            </div>
            <a href="reports.php" class="btn btn-outline-primary">View Reports</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Total Tickets</div>
                    <div class="stat-value"><?php echo number_format($stats['total_tickets']); ?></div>
                    <div class="stat-change <?php echo $stats['tickets_change'] >= 0 ? 'up' : 'down'; ?>">
                        <?php echo $stats['tickets_change'] >= 0 ? '+' : ''; ?><?php echo $stats['tickets_change']; ?>% this month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Open Tickets</div>
                    <div class="stat-value"><?php echo number_format($stats['open_tickets']); ?></div>
                    <div class="stat-change <?php echo $stats['open_change'] >= 0 ? 'up' : 'down'; ?>">
                        <?php echo $stats['open_change'] >= 0 ? '+' : ''; ?><?php echo $stats['open_change']; ?>% this month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Resolved</div>
                    <div class="stat-value"><?php echo number_format($stats['resolved_tickets']); ?></div>
                    <div class="stat-change <?php echo $stats['resolved_change'] >= 0 ? 'up' : 'down'; ?>">
                        <?php echo $stats['resolved_change'] >= 0 ? '+' : ''; ?><?php echo $stats['resolved_change']; ?>% this month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Active Users</div>
                    <div class="stat-value"><?php echo number_format($stats['total_users']); ?></div>
                    <div class="stat-change neutral">Registered accounts</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Pending</div>
                    <div class="stat-value"><?php echo number_format($stats['pending_tickets']); ?></div>
                    <div class="stat-change <?php echo $stats['pending_change'] >= 0 ? 'up' : 'down'; ?>">
                        <?php echo $stats['pending_change'] >= 0 ? '+' : ''; ?><?php echo $stats['pending_change']; ?>% this month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Closed</div>
                    <div class="stat-value"><?php echo number_format($stats['closed_tickets']); ?></div>
                    <div class="stat-change neutral">Archived tickets</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Technicians</div>
                    <div class="stat-value"><?php echo number_format($stats['total_technicians']); ?></div>
                    <div class="stat-change neutral">Support staff</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Today</div>
                    <div class="stat-value"><?php echo number_format($stats['today_tickets']); ?></div>
                    <div class="stat-change neutral">Created today</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-semibold">Monthly Ticket Trend</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 280px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Status Distribution</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 200px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="status-summary mt-3">
                        <?php 
                        $total_tickets = array_sum(array_column($status_data, 'count'));
                        foreach ($status_data as $row): 
                            $pct = $total_tickets > 0 ? round(($row['count'] / $total_tickets) * 100, 1) : 0;
                        ?>
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="status-dot" style="background-color: <?php 
                                    echo match($row['status']) {
                                        'Open' => '#2c7be5',
                                        'In Progress' => '#2d5a8e',
                                        'Pending' => '#e6a817',
                                        'Resolved' => '#17a673',
                                        'Closed' => '#95aac9',
                                        default => '#e63757'
                                    };
                                ?>;"></span>
                                <span class="small fw-medium"><?php echo sanitize($row['status']); ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="small text-muted"><?php echo number_format($row['count']); ?> tickets</span>
                                <span class="badge bg-light text-dark fw-semibold"><?php echo $pct; ?>%</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-semibold">Recent Tickets</h6>
                    <a href="tickets.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Subject</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_tickets as $ticket): ?>
                                <tr>
                                    <td><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="fw-semibold"><?php echo sanitize($ticket['ticket_number']); ?></a></td>
                                    <td><?php echo sanitize(substr($ticket['subject'], 0, 30)); ?></td>
                                    <td><?php echo sanitize($ticket['category_name']); ?></td>
                                    <td><span class="badge bg-<?php echo $ticket['priority'] === 'Critical' ? 'danger' : ($ticket['priority'] === 'High' ? 'warning' : ($ticket['priority'] === 'Medium' ? 'info' : 'secondary')); ?>"><?php echo $ticket['priority']; ?></span></td>
                                    <td><span class="badge bg-<?php echo $ticket['status'] === 'Resolved' ? 'success' : ($ticket['status'] === 'Closed' ? 'secondary' : ($ticket['status'] === 'In Progress' ? 'primary' : 'warning')); ?>"><?php echo $ticket['status']; ?></span></td>
                                    <td><?php echo formatDate($ticket['created_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Technician Performance</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($technician_performance)): ?>
                        <p class="text-muted text-center">No data available</p>
                    <?php else: ?>
                        <?php foreach ($technician_performance as $tech): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-semibold small"><?php echo sanitize($tech['full_name']); ?></div>
                                <small class="text-muted"><?php echo $tech['resolved_tickets']; ?> / <?php echo $tech['total_tickets']; ?> resolved</small>
                            </div>
                            <div class="progress" style="width: 100px; height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?php echo $tech['total_tickets'] > 0 ? round(($tech['resolved_tickets'] / $tech['total_tickets']) * 100) : 0; ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Quick Links</h6>
                </div>
                <div class="card-body p-2">
                    <a href="tickets.php" class="quick-link-item">
                        <div class="quick-link-icon">TK</div>
                        <div>
                            <div class="fw-semibold small">All Tickets</div>
                            <small class="text-muted">View and manage tickets</small>
                        </div>
                    </a>
                    <a href="users.php" class="quick-link-item">
                        <div class="quick-link-icon">US</div>
                        <div>
                            <div class="fw-semibold small">User Management</div>
                            <small class="text-muted">Add or edit users</small>
                        </div>
                    </a>
                    <a href="departments.php" class="quick-link-item">
                        <div class="quick-link-icon">DP</div>
                        <div>
                            <div class="fw-semibold small">Departments</div>
                            <small class="text-muted">Manage departments</small>
                        </div>
                    </a>
                    <a href="categories.php" class="quick-link-item">
                        <div class="quick-link-icon">CT</div>
                        <div>
                            <div class="fw-semibold small">Categories</div>
                            <small class="text-muted">Manage ticket categories</small>
                        </div>
                    </a>
                    <a href="reports.php" class="quick-link-item">
                        <div class="quick-link-icon">RP</div>
                        <div>
                            <div class="fw-semibold small">Reports</div>
                            <small class="text-muted">View analytics</small>
                        </div>
                    </a>
                    <a href="notifications.php" class="quick-link-item">
                        <div class="quick-link-icon">NF</div>
                        <div>
                            <div class="fw-semibold small">Notifications</div>
                            <small class="text-muted">View all alerts</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyLabels = <?php echo json_encode(array_column($monthly_data, 'month')); ?>;
const monthlyData = <?php echo json_encode(array_column($monthly_data, 'count')); ?>;

const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const formattedLabels = monthlyLabels.map(function(m) {
    var parts = m.split('-');
    return monthNames[parseInt(parts[1]) - 1] + ' ' + parts[0];
});

new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: formattedLabels,
        datasets: [{
            label: 'Tickets',
            data: monthlyData,
            borderColor: '#2d5a8e',
            backgroundColor: 'rgba(45, 90, 142, 0.1)',
            tension: 0.3,
            fill: true,
            pointRadius: 5,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#2d5a8e',
            pointBorderWidth: 2,
            pointHoverRadius: 7,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1a1d21',
                titleFont: { size: 13, weight: '600' },
                bodyFont: { size: 12 },
                padding: 10,
                cornerRadius: 6,
                callbacks: {
                    label: function(ctx) { return ctx.parsed.y + ' tickets'; }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 }, color: '#6b7280' }
            },
            y: {
                beginAtZero: true,
                grid: { color: '#f0f2f5' },
                ticks: {
                    font: { size: 11 },
                    color: '#6b7280',
                    stepSize: 1,
                    callback: function(val) { return Number.isInteger(val) ? val : ''; }
                }
            }
        }
    }
});

const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusLabels = <?php echo json_encode(array_column($status_data, 'status')); ?>;
const statusData = <?php echo json_encode(array_column($status_data, 'count')); ?>;
const statusColors = ['#2c7be5', '#2d5a8e', '#e6a817', '#17a673', '#95aac9', '#e63757'];
const totalTickets = statusData.reduce(function(a, b) { return a + b; }, 0);

new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusLabels.map(function(l, i) {
            var pct = totalTickets > 0 ? Math.round((statusData[i] / totalTickets) * 100) : 0;
            return l + ' (' + statusData[i] + ' - ' + pct + '%)';
        }),
        datasets: [{
            data: statusData,
            backgroundColor: statusColors.slice(0, statusData.length),
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 10,
                    font: { size: 11 }
                }
            },
            tooltip: {
                backgroundColor: '#1a1d21',
                padding: 10,
                cornerRadius: 6,
                callbacks: {
                    label: function(ctx) {
                        var pct = totalTickets > 0 ? Math.round((ctx.parsed / totalTickets) * 100) : 0;
                        return ctx.label + ': ' + ctx.parsed + ' tickets (' + pct + '%)';
                    }
                }
            }
        }
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
