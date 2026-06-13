<?php
/**
 * Admin - View Ticket
 * IT Helpdesk Ticketing System
 */

$page_title = 'View Ticket';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Admin');

$db = getDB();
$admin_id = getCurrentUserId();
$ticket_id = $_GET['id'] ?? ($_POST['ticket_id'] ?? 0);

if (!$ticket_id) {
    redirect('tickets.php');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('danger', 'Invalid request.');
        redirect("view_ticket.php?id=$ticket_id");
    }

    $action = $_POST['action'] ?? '';
    
    if ($action === 'assign_ticket') {
        $assigned_to = $_POST['assigned_to'] ?? '';
        
        if (!empty($assigned_to)) {
            try {
                $stmt = $db->prepare("UPDATE tickets SET assigned_to = ?, status = 'Assigned', updated_at = NOW() WHERE id = ?");
                $stmt->execute([$assigned_to, $ticket_id]);
                
                $stmt = $db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
                $stmt->execute([$ticket_id, $admin_id, "Ticket assigned to technician"]);
                
                $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $assigned_to,
                    'Ticket Assigned',
                    "A new ticket has been assigned to you",
                    "../technician/view_ticket.php?id=$ticket_id"
                ]);
                
                $stmt = $db->prepare("SELECT created_by FROM tickets WHERE id = ?");
                $stmt->execute([$ticket_id]);
                $ticket = $stmt->fetch();
                
                $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $ticket['created_by'],
                    'Ticket Assigned',
                    'Your ticket has been assigned to a technician',
                    "../user/view_ticket.php?id=$ticket_id"
                ]);
                
                setFlashMessage('success', 'Ticket assigned successfully.');
            } catch (PDOException $e) {
                setFlashMessage('danger', 'Error assigning ticket.');
            }
        } else {
            setFlashMessage('danger', 'Please select a technician.');
        }
        redirect("view_ticket.php?id=$ticket_id");
    }
    
    if ($action === 'update_status') {
        $new_status = $_POST['status'] ?? '';
        $valid_statuses = ['Created', 'Pending', 'Assigned', 'In Progress', 'Resolved', 'Closed'];
        
        if (in_array($new_status, $valid_statuses)) {
            try {
                $extra = '';
                if ($new_status === 'Resolved') {
                    $extra = ', resolved_at = NOW()';
                } elseif ($new_status === 'Closed') {
                    $extra = ', closed_at = NOW()';
                }
                
                $stmt = $db->prepare("UPDATE tickets SET status = ?$extra, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$new_status, $ticket_id]);
                
                $stmt = $db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
                $stmt->execute([$ticket_id, $admin_id, "Status changed to $new_status by admin"]);
                
                $stmt = $db->prepare("SELECT created_by, assigned_to FROM tickets WHERE id = ?");
                $stmt->execute([$ticket_id]);
                $ticket = $stmt->fetch();
                
                $notify_users = array_filter([$ticket['created_by'], $ticket['assigned_to']]);
                foreach ($notify_users as $uid) {
                    if ($uid != $admin_id) {
                        $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$uid, 'Ticket Updated', "Ticket status changed to $new_status", "../user/view_ticket.php?id=$ticket_id"]);
                    }
                }
                
                setFlashMessage('success', 'Ticket status updated.');
            } catch (PDOException $e) {
                setFlashMessage('danger', 'Error updating status.');
            }
        }
        redirect("view_ticket.php?id=$ticket_id");
    }
    
    if ($action === 'add_comment') {
        $comment = trim($_POST['comment'] ?? '');
        
        if (!empty($comment)) {
            try {
                $stmt = $db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
                $stmt->execute([$ticket_id, $admin_id, $comment]);
                
                $stmt = $db->prepare("SELECT created_by, assigned_to FROM tickets WHERE id = ?");
                $stmt->execute([$ticket_id]);
                $ticket = $stmt->fetch();
                
                $notify_users = array_filter([$ticket['created_by'], $ticket['assigned_to']]);
                foreach ($notify_users as $uid) {
                    if ($uid != $admin_id) {
                        $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$uid, 'Admin Comment', 'An admin has commented on the ticket', "../user/view_ticket.php?id=$ticket_id"]);
                    }
                }
                
                setFlashMessage('success', 'Comment added successfully.');
            } catch (PDOException $e) {
                setFlashMessage('danger', 'Error adding comment.');
            }
        }
        redirect("view_ticket.php?id=$ticket_id");
    }
}

// Check if we should show resolve modal
$show_resolve_modal = false;
$flash_check = $_SESSION['flash_message'] ?? null;
if ($flash_check && $flash_check['type'] === 'success' && strpos($flash_check['message'], 'esolved') !== false) {
    $show_resolve_modal = true;
}

// Get ticket details
$stmt = $db->prepare("SELECT t.*, tc.category_name, u.full_name as creator_name, u.email as creator_email,
                    CASE WHEN t.assigned_to IS NOT NULL THEN tu.full_name ELSE 'Unassigned' END as assignee_name,
                    d.department_name
                    FROM tickets t 
                    JOIN ticket_categories tc ON t.category_id = tc.id
                    JOIN users u ON t.created_by = u.id
                    LEFT JOIN users tu ON t.assigned_to = tu.id
                    LEFT JOIN departments d ON t.department_id = d.id
                    WHERE t.id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    setFlashMessage('danger', 'Ticket not found.');
    redirect('tickets.php');
}

// Get comments
$stmt = $db->prepare("SELECT tc.*, u.full_name as user_name, r.role_name
                    FROM ticket_comments tc
                    JOIN users u ON tc.user_id = u.id
                    JOIN roles r ON u.role_id = r.id
                    WHERE tc.ticket_id = ?
                    ORDER BY tc.created_at ASC");
$stmt->execute([$ticket_id]);
$comments = $stmt->fetchAll();

// Get attachments
$stmt = $db->prepare("SELECT ta.*, u.full_name as user_name
                    FROM ticket_attachments ta
                    JOIN users u ON ta.user_id = u.id
                    WHERE ta.ticket_id = ?
                    ORDER BY ta.created_at DESC");
$stmt->execute([$ticket_id]);
$attachments = $stmt->fetchAll();

// Get feedback
$stmt = $db->prepare("SELECT f.*, u.full_name as user_name FROM feedbacks f JOIN users u ON f.user_id = u.id WHERE f.ticket_id = ?");
$stmt->execute([$ticket_id]);
$feedback = $stmt->fetch();

// Get technicians
$technicians = $db->query("SELECT u.id, u.full_name, u.department_id,
                    COUNT(t.id) as active_tickets
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id
                    LEFT JOIN tickets t ON u.id = t.assigned_to AND t.status NOT IN ('Resolved', 'Closed')
                    WHERE r.role_name = 'Technician' AND u.is_active = 1
                    GROUP BY u.id, u.full_name, u.department_id
                    ORDER BY active_tickets ASC")->fetchAll();

// Get activity history
$stmt = $db->prepare("SELECT * FROM activity_logs WHERE description LIKE ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute(["%ticket #$ticket_id%"]);
$history = $stmt->fetchAll();

$csrf_token = generateCSRFToken();

$statusClass = match($ticket['status']) {
    'Created' => 'status-open',
    'Pending' => 'status-pending',
    'Assigned' => 'status-assigned',
    'In Progress' => 'status-in-progress',
    'Resolved' => 'status-resolved',
    'Closed' => 'status-closed',
    default => 'status-open'
};

$priorityClass = match($ticket['status']) {
    'Resolved', 'Closed' => 'priority-done',
    default => match($ticket['priority']) {
        'Critical' => 'priority-critical',
        'High' => 'priority-high',
        'Medium' => 'priority-medium',
        'Low' => 'priority-low',
        default => 'priority-medium'
    }
};

function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach (array_slice($words, 0, 2) as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }
    return $initials;
}

function getAvatarColor($role) {
    return match($role) {
        'Admin' => '#e63757',
        'Technician' => '#2d5a8e',
        default => '#17a673'
    };
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
        </div>
    <?php endif; ?>

    <!-- Ticket Header -->
    <div class="ticket-header mb-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <a href="tickets.php" class="btn btn-sm btn-outline-secondary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                    </a>
                    <div>
                        <h4 class="ticket-number mb-0"><?php echo sanitize($ticket['ticket_number']); ?></h4>
                        <p class="ticket-subject mb-0"><?php echo sanitize($ticket['subject']); ?></p>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="ticket-status-badge <?php echo $statusClass; ?>">
                    <?php echo $ticket['status']; ?>
                </span>
                <span class="ticket-priority-badge <?php echo $priorityClass; ?>">
                    <?php echo in_array($ticket['status'], ['Resolved', 'Closed']) ? $ticket['status'] : $ticket['priority']; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Ticket Info -->
            <div class="card ticket-card mb-4">
                <div class="ticket-card-header">
                    Ticket Information
                </div>
                <div class="card-body">
                    <div class="ticket-meta-grid">
                        <div class="ticket-meta-item">
                            <span class="ticket-meta-label">Category</span>
                            <span class="ticket-meta-value">
                                <?php echo sanitize($ticket['category_name']); ?>
                            </span>
                        </div>
                        <div class="ticket-meta-item">
                            <span class="ticket-meta-label">Department</span>
                            <span class="ticket-meta-value">
                                <?php echo sanitize($ticket['department_name'] ?? 'N/A'); ?>
                            </span>
                        </div>
                        <div class="ticket-meta-item">
                            <span class="ticket-meta-label">Created By</span>
                            <span class="ticket-meta-value">
                                <?php echo sanitize($ticket['creator_name']); ?>
                            </span>
                        </div>
                        <div class="ticket-meta-item">
                            <span class="ticket-meta-label">Email</span>
                            <span class="ticket-meta-value">
                                <?php echo sanitize($ticket['creator_email']); ?>
                            </span>
                        </div>
                        <div class="ticket-meta-item">
                            <span class="ticket-meta-label">Assigned To</span>
                            <span class="ticket-meta-value">
                                <?php echo sanitize($ticket['assignee_name']); ?>
                            </span>
                        </div>
                        <div class="ticket-meta-item">
                            <span class="ticket-meta-label">Created</span>
                            <span class="ticket-meta-value">
                                <?php echo formatDate($ticket['created_at']); ?>
                            </span>
                        </div>
                        <?php if ($ticket['resolved_at']): ?>
                        <div class="ticket-meta-item">
                            <span class="ticket-meta-label">Resolved</span>
                            <span class="ticket-meta-value text-success">
                                <?php echo formatDate($ticket['resolved_at']); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if ($ticket['closed_at']): ?>
                        <div class="ticket-meta-item">
                            <span class="ticket-meta-label">Closed</span>
                            <span class="ticket-meta-value text-secondary">
                                <?php echo formatDate($ticket['closed_at']); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="ticket-description mt-3 pt-3 border-top">
                        <h6 class="ticket-description-title">Description</h6>
                        <div class="ticket-description-content">
                            <?php echo nl2br(sanitize($ticket['description'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Section -->
            <div class="card ticket-card mb-4">
                <div class="ticket-card-header">
                    Ticket Assignment
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="assign_ticket">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label for="assigned_to" class="form-label fw-semibold">Assign to Technician</label>
                                <select class="form-select" id="assigned_to" name="assigned_to" required>
                                    <option value="">Select Technician...</option>
                                    <?php foreach ($technicians as $tech): ?>
                                        <option value="<?php echo $tech['id']; ?>" <?php echo $ticket['assigned_to'] == $tech['id'] ? 'selected' : ''; ?>>
                                            <?php echo sanitize($tech['full_name']); ?> (<?php echo $tech['active_tickets']; ?> active tickets)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    Assign Ticket
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Status Update -->
            <div class="card ticket-card mb-4">
                <div class="ticket-card-header">
                    Update Status
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Change Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="">Select status...</option>
                                    <option value="Created">Created</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Assigned">Assigned</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Resolved">Resolved</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Comments -->
            <div class="card ticket-card mb-4">
                <div class="ticket-card-header">
                    Comments
                    <span class="badge bg-secondary ms-2"><?php echo count($comments); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($comments)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No comments yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="comment-timeline">
                            <?php foreach ($comments as $index => $comment): ?>
                            <div class="comment-item <?php echo $index === count($comments) - 1 ? 'last' : ''; ?>">
                                <div class="comment-avatar" style="background-color: <?php echo getAvatarColor($comment['role_name']); ?>">
                                    <?php echo getInitials($comment['user_name']); ?>
                                </div>
                                <div class="comment-body">
                                    <div class="comment-header">
                                        <div>
                                            <span class="comment-author"><?php echo sanitize($comment['user_name']); ?></span>
                                            <span class="comment-role-badge role-<?php echo strtolower($comment['role_name']); ?>">
                                                <?php echo $comment['role_name']; ?>
                                            </span>
                                        </div>
                                        <span class="comment-time"><?php echo formatDate($comment['created_at']); ?></span>
                                    </div>
                                    <div class="comment-text">
                                        <?php echo nl2br(sanitize($comment['comment'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="mt-4 pt-3 border-top">
                        <input type="hidden" name="action" value="add_comment">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="mb-3">
                            <label for="comment" class="form-label fw-semibold">Add a Comment</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" 
                                      placeholder="Type your comment here..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            Post Comment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Feedback Section -->
            <?php if ($feedback): ?>
            <div class="card ticket-card mb-4">
                <div class="ticket-card-header">
                    User Feedback
                </div>
                <div class="card-body text-center">
                    <div class="mb-2">
                        <span class="ms-2 fw-bold fs-5"><?php echo $feedback['rating']; ?>/5</span>
                    </div>
                    <p class="mb-1"><strong><?php echo sanitize($feedback['user_name']); ?></strong></p>
                    <p class="text-muted mb-0 fst-italic">"<?php echo sanitize($feedback['comment'] ?: 'No comment'); ?>"</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Attachments -->
            <div class="card ticket-card mb-4">
                <div class="ticket-card-header d-flex justify-content-between align-items-center">
                    <span>Attachments</span>
                    <span class="badge bg-secondary"><?php echo count($attachments); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($attachments)): ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0 small">No attachments</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($attachments as $file): ?>
                        <div class="attachment-item">
                            <div class="attachment-icon">
                                <?php echo strtoupper(pathinfo($file['file_name'], PATHINFO_EXTENSION)); ?>
                            </div>
                            <div class="attachment-info">
                                <a href="<?php echo $file['file_path']; ?>" target="_blank" class="attachment-name">
                                    <?php echo sanitize($file['file_name']); ?>
                                </a>
                                <span class="attachment-meta">
                                    <?php echo round($file['file_size'] / 1024); ?> KB
                                    &middot;
                                    <?php echo sanitize($file['user_name']); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activity History -->
            <div class="card ticket-card">
                <div class="ticket-card-header">
                    Activity History
                </div>
                <div class="card-body">
                    <?php if (empty($history)): ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0 small">No activity yet</p>
                        </div>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($history as $log): ?>
                            <div class="timeline-item">
                                <div class="timeline-dot bg-info"></div>
                                <div class="timeline-content">
                                    <div class="timeline-date"><?php echo formatDate($log['created_at'], 'M d, h:i A'); ?></div>
                                    <div class="timeline-text"><?php echo sanitize($log['description']); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
