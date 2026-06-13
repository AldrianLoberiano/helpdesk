<?php
/**
 * Notifications Page
 * IT Helpdesk Ticketing System
 */

$page_title = 'Notifications';
require_once __DIR__ . '/includes/auth.php';

$db = getDB();
$user_id = getCurrentUserId();

// Mark single notification as read
if (isset($_GET['mark_read'])) {
    $notif_id = intval($_GET['mark_read']);
    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$notif_id, $user_id]);
    
    if (isset($_GET['link'])) {
        redirect($_GET['link']);
    }
    redirect('notifications.php');
}

// Get all notifications
$stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="text-muted mb-0 small">Stay updated with your latest activity alerts</p>
        </div>
        <form method="POST" action="mark_notifications_read.php" class="d-inline">
            <button type="submit" class="btn btn-outline-primary">
                Mark All as Read
            </button>
        </form>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <h5 class="text-muted">No notifications</h5>
                    <p class="text-muted">You're all caught up!</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                <div class="d-flex align-items-start p-3 mb-2 rounded <?php echo !$notif['is_read'] ? 'bg-light' : ''; ?>">
                    <div class="me-3">
                        <?php if (!$notif['is_read']): ?>
                            <span class="badge bg-primary rounded-circle p-2"></span>
                        <?php else: ?>
                            <span class="text-muted"></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 <?php echo !$notif['is_read'] ? 'fw-bold' : ''; ?>"><?php echo sanitize($notif['title']); ?></h6>
                        <p class="mb-1 text-muted"><?php echo sanitize($notif['message']); ?></p>
                        <small class="text-muted"><?php echo formatDate($notif['created_at']); ?></small>
                    </div>
                    <div>
                        <?php if (!$notif['is_read']): ?>
                            <a href="notifications.php?mark_read=<?php echo $notif['id']; ?>&link=<?php echo urlencode($notif['link'] ?? '#'); ?>" 
                               class="btn btn-sm btn-outline-primary">
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <hr class="my-1">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
