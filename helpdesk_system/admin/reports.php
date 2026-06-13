<?php
/**
 * Admin - Reports
 * IT Helpdesk Ticketing System
 */

$page_title = 'Reports & Analytics';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Admin');

$db = getDB();

// Get filter parameters
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');
$department_id = $_GET['department_id'] ?? '';
$category_id = $_GET['category_id'] ?? '';

// Build query
$where = "WHERE t.created_at BETWEEN ? AND ?";
$params = [$date_from . ' 00:00:00', $date_to . ' 23:59:59'];

if ($department_id) {
    $where .= " AND t.department_id = ?";
    $params[] = $department_id;
}
if ($category_id) {
    $where .= " AND t.category_id = ?";
    $params[] = $category_id;
}

// Get summary statistics
$stmt = $db->prepare("SELECT 
    COUNT(*) as total_tickets,
    SUM(CASE WHEN t.status = 'Created' THEN 1 ELSE 0 END) as created,
    SUM(CASE WHEN t.status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN t.status = 'Assigned' THEN 1 ELSE 0 END) as assigned,
    SUM(CASE WHEN t.status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN t.status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
    SUM(CASE WHEN t.status = 'Closed' THEN 1 ELSE 0 END) as closed,
    SUM(CASE WHEN t.priority = 'Critical' THEN 1 ELSE 0 END) as critical,
    SUM(CASE WHEN t.priority = 'High' THEN 1 ELSE 0 END) as high,
    SUM(CASE WHEN t.priority = 'Medium' THEN 1 ELSE 0 END) as medium,
    SUM(CASE WHEN t.priority = 'Low' THEN 1 ELSE 0 END) as low
    FROM tickets t $where");
$stmt->execute($params);
$summary = $stmt->fetch();

// Get category breakdown
$stmt = $db->prepare("SELECT tc.category_name, COUNT(t.id) as count
    FROM tickets t
    JOIN ticket_categories tc ON t.category_id = tc.id
    $where
    GROUP BY tc.id, tc.category_name
    ORDER BY count DESC");
$stmt->execute($params);
$category_breakdown = $stmt->fetchAll();

// Get department breakdown
$stmt = $db->prepare("SELECT d.department_name, COUNT(t.id) as count
    FROM tickets t
    LEFT JOIN departments d ON t.department_id = d.id
    $where
    GROUP BY d.id, d.department_name
    ORDER BY count DESC");
$stmt->execute($params);
$department_breakdown = $stmt->fetchAll();

// Get technician performance
$stmt = $db->prepare("SELECT 
    u.full_name,
    COUNT(t.id) as total_assigned,
    SUM(CASE WHEN t.status IN ('Resolved', 'Closed') THEN 1 ELSE 0 END) as resolved,
    AVG(TIMESTAMPDIFF(HOUR, t.created_at, t.resolved_at)) as avg_resolution_hours
    FROM tickets t
    JOIN users u ON t.assigned_to = u.id
    $where AND t.assigned_to IS NOT NULL
    GROUP BY u.id, u.full_name
    ORDER BY resolved DESC");
$stmt->execute($params);
$technician_stats = $stmt->fetchAll();

// Get daily ticket trend
$stmt = $db->prepare("SELECT DATE(t.created_at) as date, COUNT(*) as count
    FROM tickets t
    $where
    GROUP BY date
    ORDER BY date");
$stmt->execute($params);
$daily_trend = $stmt->fetchAll();

// Get departments and categories for filters
$departments = $db->query("SELECT * FROM departments ORDER BY department_name")->fetchAll();
$categories = $db->query("SELECT * FROM ticket_categories ORDER BY category_name")->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-0 fw-bold">Reports & Analytics</h1>
                <p class="text-muted mb-0 small">Track performance metrics and generate insights</p>
            </div>
            <button class="btn btn-outline-primary" onclick="window.print()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Report
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>" <?php echo $department_id == $dept['id'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($dept['department_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($cat['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        Apply Filters
                    </button>
                    <a href="reports.php" class="btn btn-outline-secondary">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tickets</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($summary['total_tickets']); ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Resolved</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($summary['resolved'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($summary['pending'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Critical</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($summary['critical'] ?? 0); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Charts -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Ticket Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="dailyTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Distribution</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 200px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <?php
                    $total_tickets = $summary['total_tickets'];
                    $status_arr = [
                        ['status' => 'Created', 'count' => $summary['created']],
                        ['status' => 'Pending', 'count' => $summary['pending']],
                        ['status' => 'Assigned', 'count' => $summary['assigned']],
                        ['status' => 'In Progress', 'count' => $summary['in_progress']],
                        ['status' => 'Resolved', 'count' => $summary['resolved']],
                        ['status' => 'Closed', 'count' => $summary['closed']],
                    ];
                    $status_colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
                    ?>
                    <div class="status-summary mt-3">
                        <?php foreach ($status_arr as $i => $row): ?>
                        <?php $pct = $total_tickets > 0 ? round(($row['count'] / $total_tickets) * 100, 1) : 0; ?>
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="status-dot" style="background-color: <?php echo $status_colors[$i]; ?>;"></span>
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

    <div class="row">
        <!-- Category Breakdown -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tickets by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Priority Distribution -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tickets by Priority</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 200px;">
                        <canvas id="priorityChart"></canvas>
                    </div>
                    <?php
                    $priority_arr = [
                        ['priority' => 'Critical', 'count' => $summary['critical']],
                        ['priority' => 'High', 'count' => $summary['high']],
                        ['priority' => 'Medium', 'count' => $summary['medium']],
                        ['priority' => 'Low', 'count' => $summary['low']],
                    ];
                    $priority_colors = ['#e74a3b', '#f6c23e', '#36b9cc', '#1cc88a'];
                    ?>
                    <div class="status-summary mt-3">
                        <?php foreach ($priority_arr as $i => $row): ?>
                        <?php $pct = $total_tickets > 0 ? round(($row['count'] / $total_tickets) * 100, 1) : 0; ?>
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="status-dot" style="background-color: <?php echo $priority_colors[$i]; ?>;"></span>
                                <span class="small fw-medium"><?php echo sanitize($row['priority']); ?></span>
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

    <!-- Technician Performance Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Technician Performance</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Technician</th>
                            <th>Total Assigned</th>
                            <th>Resolved</th>
                            <th>Resolution Rate</th>
                            <th>Avg. Resolution Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($technician_stats as $tech): ?>
                        <tr>
                            <td><?php echo sanitize($tech['full_name']); ?></td>
                            <td><?php echo $tech['total_assigned']; ?></td>
                            <td><?php echo $tech['resolved']; ?></td>
                            <td>
                                <?php 
                                $rate = $tech['total_assigned'] > 0 ? round(($tech['resolved'] / $tech['total_assigned']) * 100) : 0;
                                echo $rate . '%';
                                ?>
                            </td>
                            <td>
                                <?php 
                                echo $tech['avg_resolution_hours'] ? round($tech['avg_resolution_hours'], 1) . ' hours' : 'N/A';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="<?php echo SITE_URL; ?>/assets/js/chart.js"></script>
<script>
// Daily Trend Chart
const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_map(function($d) { return date('M d', strtotime($d['date'])); }, $daily_trend)); ?>,
        datasets: [{
            label: 'Tickets',
            data: <?php echo json_encode(array_column($daily_trend, 'count')); ?>,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1,
            fill: false
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusLabels = ['Created', 'Pending', 'Assigned', 'In Progress', 'Resolved', 'Closed'];
const statusData = [
    <?php echo $summary['created']; ?>,
    <?php echo $summary['pending']; ?>,
    <?php echo $summary['assigned']; ?>,
    <?php echo $summary['in_progress']; ?>,
    <?php echo $summary['resolved']; ?>,
    <?php echo $summary['closed']; ?>
];
const statusColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
const totalStatus = statusData.reduce(function(a, b) { return a + b; }, 0);

new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusLabels.map(function(l, i) {
            var pct = totalStatus > 0 ? Math.round((statusData[i] / totalStatus) * 100) : 0;
            return l + ' (' + statusData[i] + ' - ' + pct + '%)';
        }),
        datasets: [{
            data: statusData,
            backgroundColor: statusColors,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1a1d21',
                padding: 10,
                cornerRadius: 6,
                callbacks: {
                    label: function(ctx) {
                        var pct = totalStatus > 0 ? Math.round((ctx.parsed / totalStatus) * 100) : 0;
                        return ctx.label + ': ' + ctx.parsed + ' tickets (' + pct + '%)';
                    }
                }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($category_breakdown, 'category_name')); ?>,
        datasets: [{
            label: 'Tickets',
            data: <?php echo json_encode(array_column($category_breakdown, 'count')); ?>,
            backgroundColor: 'rgba(78, 115, 223, 0.8)'
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y' }
});

// Priority Chart
const priorityCtx = document.getElementById('priorityChart').getContext('2d');
const priorityLabels = ['Critical', 'High', 'Medium', 'Low'];
const priorityData = [
    <?php echo $summary['critical']; ?>,
    <?php echo $summary['high']; ?>,
    <?php echo $summary['medium']; ?>,
    <?php echo $summary['low']; ?>
];
const priorityColors = ['#e74a3b', '#f6c23e', '#36b9cc', '#1cc88a'];
const totalPriority = priorityData.reduce(function(a, b) { return a + b; }, 0);

new Chart(priorityCtx, {
    type: 'pie',
    data: {
        labels: priorityLabels.map(function(l, i) {
            var pct = totalPriority > 0 ? Math.round((priorityData[i] / totalPriority) * 100) : 0;
            return l + ' (' + priorityData[i] + ' - ' + pct + '%)';
        }),
        datasets: [{
            data: priorityData,
            backgroundColor: priorityColors,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1a1d21',
                padding: 10,
                cornerRadius: 6,
                callbacks: {
                    label: function(ctx) {
                        var pct = totalPriority > 0 ? Math.round((ctx.parsed / totalPriority) * 100) : 0;
                        return ctx.label + ': ' + ctx.parsed + ' tickets (' + pct + '%)';
                    }
                }
            }
        }
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>