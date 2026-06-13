<?php
$page_title = 'All Tickets';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Admin');

$db = getDB();

$status = $_GET['status'] ?? '';
$priority = $_GET['priority'] ?? '';
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$where = "WHERE 1=1";
$params = [];

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
if ($category) {
    $where .= " AND t.category_id = ?";
    $params[] = $category;
}
if ($search) {
    $where .= " AND (t.ticket_number LIKE ? OR t.subject LIKE ? OR u.full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $db->prepare("SELECT t.*, tc.category_name, u.full_name as creator_name, 
                    CASE WHEN t.assigned_to IS NOT NULL THEN tu.full_name ELSE 'Unassigned' END as assignee_name
                    FROM tickets t 
                    JOIN ticket_categories tc ON t.category_id = tc.id
                    JOIN users u ON t.created_by = u.id
                    LEFT JOIN users tu ON t.assigned_to = tu.id
                    $where
                    ORDER BY t.created_at DESC");
$stmt->execute($params);
$tickets = $stmt->fetchAll();

$categories = $db->query("SELECT * FROM ticket_categories WHERE is_active = 1 ORDER BY category_name")->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-1 fw-bold">All Tickets</h1>
                <p class="text-muted mb-0 small">View and manage all support tickets across the system</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Search tickets..." value="<?php echo sanitize($search); ?>">
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <select class="form-select" name="priority">
                        <option value="">All Priority</option>
                        <option value="Critical" <?php echo $priority === 'Critical' ? 'selected' : ''; ?>>Critical</option>
                        <option value="High" <?php echo $priority === 'High' ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo $priority === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Low" <?php echo $priority === 'Low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>><?php echo sanitize($cat['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="tickets.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($tickets)): ?>
                <div class="text-center py-5">
                    <h5 class="text-muted">No tickets found</h5>
                    <p class="text-muted small">No tickets match your criteria.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Created By</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="fw-semibold text-decoration-none"><?php echo sanitize($ticket['ticket_number']); ?></a></td>
                                <td><?php echo sanitize(substr($ticket['subject'], 0, 35)); ?></td>
                                <td><?php echo sanitize($ticket['category_name']); ?></td>
                                <td><?php echo sanitize($ticket['creator_name']); ?></td>
                                <td><?php echo $ticket['assignee_name'] === 'Unassigned' ? '<span class="text-muted">Unassigned</span>' : sanitize($ticket['assignee_name']); ?></td>
                                <td><?php echo formatDate($ticket['created_at']); ?></td>
                                <td><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a></td>
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
