<?php
$page_title = 'Announcements';
require_once __DIR__ . '/includes/auth.php';

$db = getDB();

// Mark announcement notifications as read
$stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND link = 'announcements.php' AND is_read = 0");
$stmt->execute([getCurrentUserId()]);

$announcements = $db->query("SELECT a.*, u.full_name as author_name 
    FROM announcements a 
    JOIN users u ON a.created_by = u.id 
    WHERE a.is_active = 1 
    ORDER BY FIELD(a.priority, 'High', 'Medium', 'Low'), a.created_at DESC")->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-1 fw-bold">Announcements</h1>
                <p class="text-muted mb-0 small">Latest updates and important notices</p>
            </div>
        </div>
    </div>

    <?php if (empty($announcements)): ?>
        <div class="card shadow">
            <div class="card-body">
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
                    <h5>No announcements</h5>
                    <p>There are no active announcements at this time.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($announcements as $ann): ?>
            <div class="col-lg-8">
                <div class="card shadow mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-<?php echo $ann['priority'] === 'High' ? 'danger' : ($ann['priority'] === 'Medium' ? 'warning' : 'secondary'); ?>">
                                <?php echo $ann['priority']; ?>
                            </span>
                            <h6 class="mb-0 fw-bold"><?php echo sanitize($ann['title']); ?></h6>
                        </div>
                        <small class="text-muted"><?php echo formatDate($ann['created_at']); ?></small>
                    </div>
                    <div class="card-body">
                        <p class="mb-2" style="white-space: pre-wrap; line-height: 1.7;"><?php echo sanitize($ann['content']); ?></p>
                        <hr>
                        <small class="text-muted">Posted by <?php echo sanitize($ann['author_name']); ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
