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
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-0 fw-bold">Notifications</h1>
                <p class="text-muted mb-0 small">Stay updated with your latest activity alerts</p>
            </div>
            <button type="button" class="btn btn-outline-primary" id="markAllReadBtn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Mark All as Read
            </button>
        </div>
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
                            <span class="badge bg-primary rounded-circle p-2">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            </span>
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
                               class="btn btn-sm btn-outline-primary" title="Mark as read">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
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

<script>
document.getElementById('markAllReadBtn').addEventListener('click', function() {
    fetch(window.SITE_BASE + '/mark_notifications_read.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
