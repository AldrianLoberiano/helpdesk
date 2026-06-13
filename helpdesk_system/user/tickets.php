<?php
$page_title = 'My Tickets';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Employee');

$db = getDB();
$user_id = getCurrentUserId();

$status = $_GET['status'] ?? '';
$priority = $_GET['priority'] ?? '';
$search = $_GET['search'] ?? '';

$where = "WHERE t.created_by = ?";
$params = [$user_id];

if ($status) {
    $where .= " AND t.status = ?";
    $params[] = $status;
} else {
    $where .= " AND t.status NOT IN ('Resolved', 'Closed')";
}
if ($priority) {
    $where .= " AND t.priority = ?";
    $params[] = $priority;
}
if ($search) {
    $where .= " AND (t.ticket_number LIKE ? OR t.subject LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $db->prepare("SELECT t.*, tc.category_name,
                    CASE WHEN t.assigned_to IS NOT NULL THEN tu.full_name ELSE 'Unassigned' END as assignee_name
                    FROM tickets t 
                    JOIN ticket_categories tc ON t.category_id = tc.id
                    LEFT JOIN users tu ON t.assigned_to = tu.id
                    $where
                    ORDER BY t.created_at DESC");
$stmt->execute($params);
$tickets = $stmt->fetchAll();

$csrf_token = generateCSRFToken();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-1 fw-bold">My Tickets</h1>
                <p class="text-muted mb-0 small">Track and manage your submitted support requests</p>
            </div>
            <a href="create_ticket.php" class="btn btn-outline-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M12 5v14M5 12h14"/></svg>
                New Ticket
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search tickets..." value="<?php echo sanitize($search); ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="Created" <?php echo $status === 'Created' ? 'selected' : ''; ?>>Created</option>
                        <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Assigned" <?php echo $status === 'Assigned' ? 'selected' : ''; ?>>Assigned</option>
                        <option value="In Progress" <?php echo $status === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Resolved" <?php echo $status === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                        <option value="Closed" <?php echo $status === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="priority">
                        <option value="">All Priority</option>
                        <option value="Critical" <?php echo $priority === 'Critical' ? 'selected' : ''; ?>>Critical</option>
                        <option value="High" <?php echo $priority === 'High' ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo $priority === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Low" <?php echo $priority === 'Low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($tickets)): ?>
                <div class="text-center py-5">
                    <h5 class="text-muted">No tickets found</h5>
                    <p class="text-muted small">Create your first support ticket.</p>
                    <a href="create_ticket.php" class="btn btn-primary">Create Ticket</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="fw-semibold"><?php echo sanitize($ticket['ticket_number']); ?></a></td>
                                <td><?php echo sanitize(substr($ticket['subject'], 0, 30)); ?></td>
                                <td><?php echo sanitize($ticket['category_name']); ?></td>
                                <td><span class="badge bg-<?php echo $ticket['status'] === 'Resolved' ? 'success' : ($ticket['status'] === 'Closed' ? 'secondary' : ($ticket['status'] === 'In Progress' ? 'primary' : 'warning')); ?>"><?php echo $ticket['status']; ?></span></td>
                                <td><?php echo sanitize($ticket['assignee_name']); ?></td>
                                <td><?php echo formatDate($ticket['created_at']); ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary" title="View">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <?php if ($ticket['status'] === 'Closed'): ?>
                                        <form method="POST" action="reopen_ticket.php" class="d-inline">
                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Reopen">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
