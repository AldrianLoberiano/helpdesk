<?php
$page_title = 'My Dashboard';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Employee');

$db = getDB();
$user_id = getCurrentUserId();

$stats = [];

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE created_by = ?");
$stmt->execute([$user_id]);
$stats['total_tickets'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE created_by = ? AND status NOT IN ('Resolved', 'Closed')");
$stmt->execute([$user_id]);
$stats['open_tickets'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE created_by = ? AND status = 'Resolved'");
$stmt->execute([$user_id]);
$stats['resolved_tickets'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE created_by = ? AND status = 'Closed'");
$stmt->execute([$user_id]);
$stats['closed_tickets'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE created_by = ? AND DATE(created_at) = CURDATE()");
$stmt->execute([$user_id]);
$stats['today_tickets'] = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT t.*, tc.category_name, 
                    CASE WHEN t.assigned_to IS NOT NULL THEN tu.full_name ELSE 'Unassigned' END as assignee_name
                    FROM tickets t 
                    JOIN ticket_categories tc ON t.category_id = tc.id
                    LEFT JOIN users tu ON t.assigned_to = tu.id
                    WHERE t.created_by = ?
                    ORDER BY t.created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_tickets = $stmt->fetchAll();

$stmt = $db->prepare("SELECT status, COUNT(*) as count FROM tickets WHERE created_by = ? GROUP BY status");
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
                <p class="text-muted mb-0 small">Your ticket overview</p>
            </div>
            <a href="create_ticket.php" class="btn btn-outline-primary">Create New Ticket</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">My Tickets</div>
                    <div class="stat-value"><?php echo number_format($stats['total_tickets']); ?></div>
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
                    <div class="stat-label">Resolved</div>
                    <div class="stat-value"><?php echo number_format($stats['resolved_tickets']); ?></div>
                    <div class="stat-meta">Successfully handled</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Closed</div>
                    <div class="stat-value"><?php echo number_format($stats['closed_tickets']); ?></div>
                    <div class="stat-meta">Archived tickets</div>
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
                    <?php if (empty($recent_tickets)): ?>
                        <div class="text-center py-5">
                            <h5 class="text-muted">No tickets yet</h5>
                            <p class="text-muted small">Create your first support ticket to get started.</p>
                            <a href="create_ticket.php" class="btn btn-primary">Create Ticket</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Ticket</th>
                                        <th>Subject</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_tickets as $ticket): ?>
                                    <tr>
                                        <td><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="fw-semibold"><?php echo sanitize($ticket['ticket_number']); ?></a></td>
                                        <td><?php echo sanitize(substr($ticket['subject'], 0, 25)); ?></td>
                                        <td class="text-center"><span class="badge bg-<?php echo $ticket['priority'] === 'Critical' ? 'danger' : ($ticket['priority'] === 'High' ? 'warning' : ($ticket['priority'] === 'Medium' ? 'info' : 'secondary')); ?>"><?php echo $ticket['priority']; ?></span></td>
                                        <td><?php echo sanitize($ticket['assignee_name']); ?></td>
                                        <td><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary" title="View"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Ticket Status</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 200px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <?php 
                        $total_status = array_sum(array_column($status_data, 'count'));
                        $status_colors = ['Open' => '#2c7be5', 'In Progress' => '#2d5a8e', 'Pending' => '#e6a817', 'Resolved' => '#17a673', 'Closed' => '#95aac9', 'Created' => '#e63757'];
                        foreach ($status_data as $row): 
                            $pct = $total_status > 0 ? round(($row['count'] / $total_status) * 100, 1) : 0;
                        ?>
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="status-dot" style="background-color: <?php echo $status_colors[$row['status']] ?? '#95aac9'; ?>;"></span>
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
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="create_ticket.php" class="btn btn-primary w-100 mb-2">Create New Ticket</a>
                    <a href="tickets.php" class="btn btn-outline-primary w-100">View All Tickets</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
