<?php
/**
 * User - View Ticket
 * IT Helpdesk Ticketing System
 */

$page_title = 'View Ticket';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Employee');

$db = getDB();
$user_id = getCurrentUserId();
$ticket_id = $_GET['id'] ?? ($_POST['ticket_id'] ?? 0);

if (!$ticket_id) {
    redirect('tickets.php');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('danger', 'Invalid request. Please try again.');
        redirect("view_ticket.php?id=$ticket_id");
    }

    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_comment') {
        $comment = trim($_POST['comment'] ?? '');
        
        if (!empty($comment)) {
            try {
                $stmt = $db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
                $stmt->execute([$ticket_id, $user_id, $comment]);
                
                $stmt = $db->prepare("SELECT assigned_to FROM tickets WHERE id = ?");
                $stmt->execute([$ticket_id]);
                $ticket = $stmt->fetch();
                
                if ($ticket && $ticket['assigned_to']) {
                    $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $ticket['assigned_to'],
                        'New Comment',
                        'A user has replied to the ticket',
                        "../technician/view_ticket.php?id=$ticket_id"
                    ]);
                }
                
                setFlashMessage('success', 'Comment added successfully.');
            } catch (PDOException $e) {
                setFlashMessage('danger', 'Error adding comment.');
            }
        }
        redirect("view_ticket.php?id=$ticket_id");
    }
    
    if ($action === 'rate_ticket') {
        $rating = $_POST['rating'] ?? 0;
        $feedback = trim($_POST['feedback'] ?? '');
        
        if ($rating >= 1 && $rating <= 5) {
            try {
                $stmt = $db->prepare("SELECT id FROM feedbacks WHERE ticket_id = ? AND user_id = ?");
                $stmt->execute([$ticket_id, $user_id]);
                
                if ($stmt->fetch()) {
                    $stmt = $db->prepare("UPDATE feedbacks SET rating = ?, comment = ? WHERE ticket_id = ? AND user_id = ?");
                    $stmt->execute([$rating, $feedback, $ticket_id, $user_id]);
                } else {
                    $stmt = $db->prepare("INSERT INTO feedbacks (ticket_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$ticket_id, $user_id, $rating, $feedback]);
                }
                
                setFlashMessage('success', 'Thank you for your feedback!');
            } catch (PDOException $e) {
                setFlashMessage('danger', 'Error submitting feedback.');
            }
        }
        redirect("view_ticket.php?id=$ticket_id");
    }
    
    if ($action === 'close_ticket') {
        try {
            $stmt = $db->prepare("UPDATE tickets SET status = 'Closed', closed_at = NOW() WHERE id = ? AND created_by = ?");
            $stmt->execute([$ticket_id, $user_id]);
            setFlashMessage('success', 'Ticket closed successfully.');
        } catch (PDOException $e) {
            setFlashMessage('danger', 'Error closing ticket.');
        }
        redirect("view_ticket.php?id=$ticket_id");
    }
}

// Check if we should show resolve/close modal
$show_resolve_modal = false;
$flash_check = $_SESSION['flash_message'] ?? null;
if ($flash_check && $flash_check['type'] === 'success' && (strpos($flash_check['message'], 'esolved') !== false || strpos($flash_check['message'], 'losed') !== false)) {
    $show_resolve_modal = true;
}

// Get ticket details
$stmt = $db->prepare("SELECT t.*, tc.category_name, u.full_name as creator_name,
                    CASE WHEN t.assigned_to IS NOT NULL THEN tu.full_name ELSE 'Unassigned' END as assignee_name
                    FROM tickets t 
                    JOIN ticket_categories tc ON t.category_id = tc.id
                    JOIN users u ON t.created_by = u.id
                    LEFT JOIN users tu ON t.assigned_to = tu.id
                    WHERE t.id = ? AND t.created_by = ?");
$stmt->execute([$ticket_id, $user_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    setFlashMessage('danger', 'Ticket not found or access denied.');
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

// Check feedback
$stmt = $db->prepare("SELECT * FROM feedbacks WHERE ticket_id = ? AND user_id = ?");
$stmt->execute([$ticket_id, $user_id]);
$feedback = $stmt->fetch();

// Status helpers
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
    <?php $flash = getFlashMessage(); if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                        <div class="ticket-meta-item">
                            <span class="ticket-meta-label">Last Updated</span>
                            <span class="ticket-meta-value">
                                <?php echo formatDate($ticket['updated_at']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="ticket-description mt-3 pt-3 border-top">
                        <h6 class="ticket-description-title">Description</h6>
                        <div class="ticket-description-content">
                            <?php echo nl2br(sanitize($ticket['description'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Close Ticket -->
            <?php if (!in_array($ticket['status'], ['Closed', 'Resolved'])): ?>
            <div class="mb-4">
                <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="close_ticket">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirmCloseTicket()">
                        Close Ticket
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Comments -->
            <div class="card ticket-card mb-4">
                <div class="ticket-card-header">
                    Comments
                    <span class="badge bg-secondary ms-2"><?php echo count($comments); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($comments)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No comments yet. Start the conversation.</p>
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
                                            <?php if ($comment['role_name'] !== 'Employee'): ?>
                                                <span class="comment-role-badge role-<?php echo strtolower($comment['role_name']); ?>">
                                                    <?php echo $comment['role_name']; ?>
                                                </span>
                                            <?php endif; ?>
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

                    <!-- Add Comment -->
                    <form method="POST" class="mt-4 pt-3 border-top">
                        <input type="hidden" name="action" value="add_comment">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
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
            <?php if (in_array($ticket['status'], ['Resolved', 'Closed'])): ?>
            <div class="card ticket-card mb-4">
                <div class="ticket-card-header">
                    Rate Service
                </div>
                <div class="card-body">
                    <?php if ($feedback): ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-2">You have already rated this ticket</p>
                            <div class="mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="<?php echo $i <= $feedback['rating'] ? 'text-warning' : 'text-muted'; ?>">★</span>
                                <?php endfor; ?>
                                <span class="ms-2 fw-bold"><?php echo $feedback['rating']; ?>/5</span>
                            </div>
                            <?php if ($feedback['comment']): ?>
                                <p class="text-muted mb-0 fst-italic">"<?php echo sanitize($feedback['comment']); ?>"</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="rate_ticket">
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="mb-3 text-center">
                                <label class="form-label fw-semibold">Your Rating</label>
                                <div class="rating">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                    <label for="star<?php echo $i; ?>">★</label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="feedback" class="form-label fw-semibold">Feedback (Optional)</label>
                                <textarea class="form-control" id="feedback" name="feedback" rows="3" 
                                          placeholder="Tell us about your experience..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-sm">
                                Submit Feedback
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Attachments -->
            <div class="card ticket-card mb-4">
                <div class="ticket-card-header">
                    Attachments
                    <span class="badge bg-secondary ms-2"><?php echo count($attachments); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($attachments)): ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0 small">No attachments uploaded</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($attachments as $file): ?>
                        <div class="attachment-item">
                            <div class="attachment-icon">
                                <span class="badge bg-secondary"><?php echo strtoupper(pathinfo($file['file_name'], PATHINFO_EXTENSION)); ?></span>
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

            <!-- Ticket Timeline -->
            <div class="card ticket-card">
                <div class="ticket-card-header">
                    Timeline
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot bg-primary"></div>
                            <div class="timeline-content">
                                <div class="timeline-date"><?php echo formatDate($ticket['created_at'], 'M d, h:i A'); ?></div>
                                <div class="timeline-text">Ticket created</div>
                            </div>
                        </div>
                        <?php if ($ticket['resolved_at']): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot bg-success"></div>
                            <div class="timeline-content">
                                <div class="timeline-date"><?php echo formatDate($ticket['resolved_at'], 'M d, h:i A'); ?></div>
                                <div class="timeline-text">Ticket resolved</div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($ticket['closed_at']): ?>
                        <div class="timeline-item last">
                            <div class="timeline-dot bg-secondary"></div>
                            <div class="timeline-content">
                                <div class="timeline-date"><?php echo formatDate($ticket['closed_at'], 'M d, h:i A'); ?></div>
                                <div class="timeline-text">Ticket closed</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
