<?php
$page_title = 'Edit Announcement';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Admin');

$db = getDB();
$id = intval($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM announcements WHERE id = ?");
$stmt->execute([$id]);
$announcement = $stmt->fetch();

if (!$announcement) {
    setFlashMessage('danger', 'Announcement not found');
    redirect('announcements.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $priority = $_POST['priority'] ?? 'Medium';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($title) || empty($content)) {
        setFlashMessage('danger', 'Title and content are required');
        redirect('edit_announcement.php?id=' . $id);
    }

    $stmt = $db->prepare("UPDATE announcements SET title = ?, content = ?, priority = ?, is_active = ? WHERE id = ?");
    $stmt->execute([$title, $content, $priority, $is_active, $id]);

    setFlashMessage('success', 'Announcement updated successfully');
    redirect('announcements.php');
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-1 fw-bold">Edit Announcement</h1>
                <p class="text-muted mb-0 small">Update announcement details</p>
            </div>
            <a href="announcements.php" class="btn btn-outline-secondary">Back to List</a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required value="<?php echo htmlspecialchars($announcement['title']); ?>">
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="6" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="Low" <?php echo $announcement['priority'] === 'Low' ? 'selected' : ''; ?>>Low</option>
                            <option value="Medium" <?php echo $announcement['priority'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="High" <?php echo $announcement['priority'] === 'High' ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo $announcement['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">Active (visible to users)</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Announcement</button>
                    <a href="announcements.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
