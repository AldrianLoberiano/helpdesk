<?php
/**
 * Admin - Category Management
 * IT Helpdesk Ticketing System
 */

$page_title = 'Category Management';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Admin');

$db = getDB();
$message = '';
$message_type = '';

// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $message = 'Invalid request. Please try again.';
        $message_type = 'danger';
    } else {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $category_name = trim($_POST['category_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($category_name)) {
                $message = 'Category name is required.';
                $message_type = 'danger';
            } else {
                try {
                    $stmt = $db->prepare("INSERT INTO ticket_categories (category_name, description) VALUES (?, ?)");
                    $stmt->execute([$category_name, $description]);
                    $message = 'Category added successfully.';
                    $message_type = 'success';
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $message = 'Category name already exists.';
                    } else {
                        $message = 'Error adding category.';
                    }
                    $message_type = 'danger';
                }
            }
            break;
            
        case 'edit':
            $id = $_POST['id'] ?? 0;
            $category_name = trim($_POST['category_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($category_name) || empty($id)) {
                $message = 'Category name is required.';
                $message_type = 'danger';
            } else {
                try {
                    $stmt = $db->prepare("UPDATE ticket_categories SET category_name = ?, description = ? WHERE id = ?");
                    $stmt->execute([$category_name, $description, $id]);
                    $message = 'Category updated successfully.';
                    $message_type = 'success';
                } catch (PDOException $e) {
                    $message = 'Error updating category.';
                    $message_type = 'danger';
                }
            }
            break;
            
        case 'toggle_status':
            $id = $_POST['id'] ?? 0;
            if ($id) {
                try {
                    $stmt = $db->prepare("UPDATE ticket_categories SET is_active = NOT is_active WHERE id = ?");
                    $stmt->execute([$id]);
                    $message = 'Category status updated.';
                    $message_type = 'success';
                } catch (PDOException $e) {
                    $message = 'Error updating category.';
                    $message_type = 'danger';
                }
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if ($id) {
                try {
                    // Check if category has tickets
                    $stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE category_id = ?");
                    $stmt->execute([$id]);
                    if ($stmt->fetchColumn() > 0) {
                        $message = 'Cannot delete category with existing tickets.';
                        $message_type = 'danger';
                    } else {
                        $stmt = $db->prepare("DELETE FROM ticket_categories WHERE id = ?");
                        $stmt->execute([$id]);
                        $message = 'Category deleted successfully.';
                        $message_type = 'success';
                    }
                } catch (PDOException $e) {
                    $message = 'Error deleting category.';
                    $message_type = 'danger';
                }
            }
            break;
    }
    }
}

$csrf_token = generateCSRFToken();

// Get all categories with ticket count
$stmt = $db->query("SELECT tc.*, COUNT(t.id) as ticket_count 
                    FROM ticket_categories tc 
                    LEFT JOIN tickets t ON tc.id = t.category_id
                    GROUP BY tc.id
                    ORDER BY tc.category_name");
$categories = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-0 fw-bold">Category Management</h1>
                <p class="text-muted mb-0 small">Define ticket categories for better organization</p>
            </div>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
Add Category
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Tickets</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td><?php echo sanitize($cat['category_name']); ?></td>
                            <td><?php echo sanitize($cat['description'] ?: 'N/A'); ?></td>
                            <td><?php echo $cat['ticket_count']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $cat['is_active'] ? 'success' : 'danger'; ?>">
                                    <?php echo $cat['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" 
                                        data-bs-target="#editCategoryModal<?php echo $cat['id']; ?>">
                                </button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-<?php echo $cat['is_active'] ? 'warning' : 'success'; ?>">
                                    </button>
                                </form>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Category Modal -->
                        <div class="modal fade" id="editCategoryModal<?php echo $cat['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            
                                            <div class="mb-3">
                                                <label for="category_name" class="form-label">Category Name *</label>
                                                <input type="text" class="form-control" id="category_name" name="category_name" 
                                                       value="<?php echo sanitize($cat['category_name']); ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo sanitize($cat['description']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Update Category</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>