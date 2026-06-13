<?php
$page_title = 'Create Announcement';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Admin');

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $priority = $_POST['priority'] ?? 'Medium';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($title) || empty($content)) {
        setFlashMessage('danger', 'Title and content are required');
        redirect('create_announcement.php');
    }

    $stmt = $db->prepare("INSERT INTO announcements (title, content, priority, is_active, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $priority, $is_active, getCurrentUserId()]);
    $announcement_id = $db->lastInsertId();

    // Notify all active users
    $users = $db->query("SELECT id FROM users WHERE is_active = 1 AND id != " . getCurrentUserId())->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($users)) {
        $notifStmt = $db->prepare("INSERT INTO notifications (user_id, title, message, link, is_read) VALUES (?, ?, ?, ?, 0)");
        $priorityLabel = $priority === 'High' ? '[URGENT] ' : '';
        foreach ($users as $uid) {
            $notifStmt->execute([
                $uid,
                $priorityLabel . 'New Announcement: ' . $title,
                substr($content, 0, 100) . (strlen($content) > 100 ? '...' : ''),
                'announcements.php'
            ]);
        }
    }

    setFlashMessage('success', 'Announcement created and ' . count($users) . ' users notified');
    redirect('announcements.php');
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-1 fw-bold">Create Announcement</h1>
                <p class="text-muted mb-0 small">Post a new announcement for users</p>
            </div>
            <a href="announcements.php" class="btn btn-outline-secondary">Back to List</a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required placeholder="Announcement title">
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="6" required placeholder="Write your announcement content here..."></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Active (visible to users)</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Announcement</button>
                    <a href="announcements.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
