<?php
$page_title = 'Manage Announcements';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Admin');

$db = getDB();

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->execute([$id]);
    setFlashMessage('success', 'Announcement deleted successfully');
    redirect('announcements.php');
}

// Get all announcements
$announcements = $db->query("SELECT a.*, u.full_name as author_name 
    FROM announcements a 
    JOIN users u ON a.created_by = u.id 
    ORDER BY a.created_at DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-1 fw-bold">Announcements</h1>
                <p class="text-muted mb-0 small">Manage system announcements</p>
            </div>
            <a href="create_announcement.php" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Announcement
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            <?php if (empty($announcements)): ?>
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
                    <h5>No announcements yet</h5>
                    <p>Create your first announcement to notify users.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Author</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($announcements as $ann): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo sanitize($ann['title']); ?></div>
                                    <small class="text-muted"><?php echo sanitize(substr($ann['content'], 0, 60)); ?>...</small>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $ann['priority'] === 'High' ? 'danger' : ($ann['priority'] === 'Medium' ? 'warning' : 'secondary'); ?>">
                                        <?php echo $ann['priority']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $ann['is_active'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $ann['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo sanitize($ann['author_name']); ?></td>
                                <td><?php echo formatDate($ann['created_at']); ?></td>
                                <td class="table-actions">
                                    <a href="edit_announcement.php?id=<?php echo $ann['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <a href="?delete=<?php echo $ann['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Are you sure you want to delete this announcement?" title="Delete">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                    </a>
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
