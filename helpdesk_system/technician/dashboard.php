<?php
$page_title = 'Technician Dashboard';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Technician');

$db = getDB();
$user_id = getCurrentUserId();

$stats = [];

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE assigned_to = ?");
$stmt->execute([$user_id]);
$stats['total_assigned'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE assigned_to = ? AND status NOT IN ('Resolved', 'Closed')");
$stmt->execute([$user_id]);
$stats['open_tickets'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE assigned_to = ? AND status = 'In Progress'");
$stmt->execute([$user_id]);
$stats['in_progress'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE assigned_to = ? AND status = 'Resolved'");
$stmt->execute([$user_id]);
$stats['resolved_tickets'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE assigned_to = ? AND status = 'Closed'");
$stmt->execute([$user_id]);
$stats['closed_tickets'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE assigned_to = ? AND DATE(created_at) = CURDATE()");
$stmt->execute([$user_id]);
$stats['today_tickets'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT t.*, tc.category_name, u.full_name as creator_name
                    FROM tickets t 
                    JOIN ticket_categories tc ON t.category_id = tc.id
                    JOIN users u ON t.created_by = u.id
                    WHERE t.assigned_to = ?
                    ORDER BY 
                        CASE t.priority 
                            WHEN 'Critical' THEN 1 
                            WHEN 'High' THEN 2 
                            WHEN 'Medium' THEN 3 
                            WHEN 'Low' THEN 4 
                        END,
                        t.created_at DESC
                    LIMIT 10");
$stmt->execute([$user_id]);
$recent_tickets = $stmt->fetchAll();

$stmt = $db->prepare("SELECT priority, COUNT(*) as count FROM tickets WHERE assigned_to = ? GROUP BY priority");
$stmt->execute([$user_id]);
$priority_data = $stmt->fetchAll();

$stmt = $db->prepare("SELECT status, COUNT(*) as count FROM tickets WHERE assigned_to = ? GROUP BY status");
$stmt->execute([$user_id]);
$status_data = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-1 fw-bold">Dashboard</h1>
                <p class="text-muted mb-0 small">Your assigned tickets overview</p>
            </div>
            <a href="tickets.php" class="btn btn-outline-primary">View All Tickets</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Total Assigned</div>
                    <div class="stat-value"><?php echo number_format($stats['total_assigned']); ?></div>
                    <div class="stat-meta">All time</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Open</div>
                    <div class="stat-value"><?php echo number_format($stats['open_tickets']); ?></div>
                    <div class="stat-meta">Awaiting resolution</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value"><?php echo number_format($stats['in_progress']); ?></div>
                    <div class="stat-meta">Currently working</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Resolved</div>
                    <div class="stat-value"><?php echo number_format($stats['resolved_tickets']); ?></div>
                    <div class="stat-meta">Successfully handled</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-semibold">Recent Assigned Tickets</h6>
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
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_tickets as $ticket): ?>
                                <tr>
                                    <td><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="fw-semibold"><?php echo sanitize($ticket['ticket_number']); ?></a></td>
                                    <td><?php echo sanitize(substr($ticket['subject'], 0, 25)); ?></td>
                                    <td><?php echo sanitize($ticket['category_name']); ?></td>
                                    <td class="text-center"><span class="badge bg-<?php echo $ticket['priority'] === 'Critical' ? 'danger' : ($ticket['priority'] === 'High' ? 'warning' : ($ticket['priority'] === 'Medium' ? 'info' : 'secondary')); ?>"><?php echo $ticket['priority']; ?></span></td>
                                    <td><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a></td>
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
                    <h6 class="m-0 fw-semibold">Tickets by Priority</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 200px;">
                        <canvas id="priorityChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <?php 
                        $total_priority = array_sum(array_column($priority_data, 'count'));
                        $priority_colors = ['Critical' => '#e63757', 'High' => '#e6a817', 'Medium' => '#2c7be5', 'Low' => '#17a673'];
                        foreach ($priority_data as $row): 
                            $pct = $total_priority > 0 ? round(($row['count'] / $total_priority) * 100, 1) : 0;
                        ?>
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="status-dot" style="background-color: <?php echo $priority_colors[$row['priority']] ?? '#95aac9'; ?>;"></span>
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
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Tickets by Status</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 200px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <?php 
                        $total_status = array_sum(array_column($status_data, 'count'));
                        $status_colors = ['Open' => '#2c7be5', 'In Progress' => '#2d5a8e', 'Pending' => '#e6a817', 'Resolved' => '#17a673', 'Closed' => '#95aac9', 'Assigned' => '#2d5a8e'];
                        foreach ($status_data as $row): 
                            $pct = $total_status > 0 ? round(($row['count'] / $total_status) * 100, 1) : 0;
                        ?>
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="status-dot" style="background-color: <?php echo $status_colors[$row['status']] ?? '#e63757'; ?>;"></span>
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
</div>

<script>
const priorityCtx = document.getElementById('priorityChart').getContext('2d');
const priorityLabels = <?php echo json_encode(array_column($priority_data, 'priority')); ?>;
const priorityData = <?php echo json_encode(array_column($priority_data, 'count')); ?>;
const totalPriority = priorityData.reduce(function(a, b) { return a + b; }, 0);

new Chart(priorityCtx, {
    type: 'pie',
    data: {
        labels: priorityLabels.map(function(l, i) {
            var pct = totalPriority > 0 ? Math.round((priorityData[i] / totalPriority) * 100) : 0;
            return l + ' (' + priorityData[i] + ' - ' + pct + '%)';
        }),
        datasets: [{ data: priorityData, backgroundColor: ['#e63757', '#e6a817', '#2c7be5', '#17a673'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '0%',
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } },
            tooltip: {
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

const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusLabels = <?php echo json_encode(array_column($status_data, 'status')); ?>;
const statusData = <?php echo json_encode(array_column($status_data, 'count')); ?>;
const totalStatus = statusData.reduce(function(a, b) { return a + b; }, 0);

new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusLabels.map(function(l, i) {
            var pct = totalStatus > 0 ? Math.round((statusData[i] / totalStatus) * 100) : 0;
            return l + ' (' + statusData[i] + ' - ' + pct + '%)';
        }),
        datasets: [{ data: statusData, backgroundColor: ['#2c7be5', '#17a673', '#2d5a8e', '#e6a817', '#e63757', '#95aac9'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } },
            tooltip: {
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
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
