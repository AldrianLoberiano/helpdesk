<?php
/**
 * Admin - Department Management
 * IT Helpdesk Ticketing System
 */

$page_title = 'Department Management';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Admin');

$db = getDB();
$message = '';
$message_type = '';

// Handle department actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    } else {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $department_name = trim($_POST['department_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($department_name)) {
                $message = 'Department name is required.';
                $message_type = 'danger';
            } else {
                try {
                    $stmt = $db->prepare("INSERT INTO departments (department_name, description) VALUES (?, ?)");
                    $stmt->execute([$department_name, $description]);
                    $message = 'Department added successfully.';
                    $message_type = 'success';
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $message = 'Department name already exists.';
                    } else {
                        $message = 'Error adding department.';
                    }
                    $message_type = 'danger';
                }
            }
            break;
            
        case 'edit':
            $id = $_POST['id'] ?? 0;
            $department_name = trim($_POST['department_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($department_name) || empty($id)) {
                $message = 'Department name is required.';
                $message_type = 'danger';
            } else {
                try {
                    $stmt = $db->prepare("UPDATE departments SET department_name = ?, description = ? WHERE id = ?");
                    $stmt->execute([$department_name, $description, $id]);
                    $message = 'Department updated successfully.';
                    $message_type = 'success';
                } catch (PDOException $e) {
                    $message = 'Error updating department.';
                    $message_type = 'danger';
                }
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if ($id) {
                try {
                    // Check if department has users
                    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE department_id = ?");
                    $stmt->execute([$id]);
                    if ($stmt->fetchColumn() > 0) {
                        $message = 'Cannot delete department with assigned users.';
                        $message_type = 'danger';
                    } else {
                        $stmt = $db->prepare("DELETE FROM departments WHERE id = ?");
                        $stmt->execute([$id]);
                        $message = 'Department deleted successfully.';
                        $message_type = 'success';
                    }
                } catch (PDOException $e) {
                    $message = 'Error deleting department.';
                    $message_type = 'danger';
                }
            }
            break;
    }
    }
}

$csrf_token = generateCSRFToken();

// Get all departments with user count
$stmt = $db->query("SELECT d.*, COUNT(u.id) as user_count 
                    FROM departments d 
                    LEFT JOIN users u ON d.id = u.department_id
                    GROUP BY d.id
                    ORDER BY d.department_name");
$departments = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-0 fw-bold">Department Management</h1>
                <p class="text-muted mb-0 small">Organize your teams by managing departments</p>
            </div>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M12 5v14M5 12h14"/></svg>
                Add Department
            </button>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($departments as $dept): ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <?php echo sanitize($dept['department_name']); ?>
                    </h5>
                    <p class="card-text text-muted">
                        <?php echo sanitize($dept['description'] ?: 'No description'); ?>
                    </p>
                    <p class="card-text">
                        <small class="text-muted">
                            <?php echo $dept['user_count']; ?> user(s)
                        </small>
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" 
                            data-bs-target="#editDepartmentModal<?php echo $dept['id']; ?>">
                        Edit
                    </button>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $dept['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Department Modal -->
        <div class="modal fade" id="editDepartmentModal<?php echo $dept['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $dept['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="mb-3">
                                <label for="department_name" class="form-label">Department Name *</label>
                                <input type="text" class="form-control" id="department_name" name="department_name" 
                                       value="<?php echo sanitize($dept['department_name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo sanitize($dept['description']); ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Department</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($departments)): ?>
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <h5 class="text-muted">No departments found</h5>
                <p class="text-muted">Click "Add Department" to create your first department.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="mb-3">
                        <label for="department_name" class="form-label">Department Name *</label>
                        <input type="text" class="form-control" id="department_name" name="department_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>