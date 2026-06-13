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
        setFlashMessage('danger', 'Invalid request. Please try again.');
        redirect('categories.php');
    } else {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $category_name = trim($_POST['category_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($category_name)) {
                setFlashMessage('danger', 'Category name is required.');
                redirect('categories.php');
            } else {
                try {
                    $stmt = $db->prepare("INSERT INTO ticket_categories (category_name, description) VALUES (?, ?)");
                    $stmt->execute([$category_name, $description]);
                    setFlashMessage('success', 'Category added successfully.');
                    redirect('categories.php');
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        setFlashMessage('danger', 'Category name already exists.');
                    } else {
                        setFlashMessage('danger', 'Error adding category.');
                    }
                    redirect('categories.php');
                }
            }
            break;
            
        case 'edit':
            $id = $_POST['id'] ?? 0;
            $category_name = trim($_POST['category_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($category_name) || empty($id)) {
                setFlashMessage('danger', 'Category name is required.');
                redirect('categories.php');
            } else {
                try {
                    $stmt = $db->prepare("UPDATE ticket_categories SET category_name = ?, description = ? WHERE id = ?");
                    $stmt->execute([$category_name, $description, $id]);
                    setFlashMessage('success', 'Category updated successfully.');
                    redirect('categories.php');
                } catch (PDOException $e) {
                    setFlashMessage('danger', 'Error updating category.');
                    redirect('categories.php');
                }
            }
            break;
            
        case 'toggle_status':
            $id = $_POST['id'] ?? 0;
            if ($id) {
                try {
                    $stmt = $db->prepare("UPDATE ticket_categories SET is_active = NOT is_active WHERE id = ?");
                    $stmt->execute([$id]);
                    setFlashMessage('success', 'Category status updated.');
                    redirect('categories.php');
                } catch (PDOException $e) {
                    setFlashMessage('danger', 'Error updating category.');
                    redirect('categories.php');
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
                        setFlashMessage('danger', 'Cannot delete category with existing tickets.');
                        redirect('categories.php');
                    } else {
                        $stmt = $db->prepare("DELETE FROM ticket_categories WHERE id = ?");
                        $stmt->execute([$id]);
                        setFlashMessage('success', 'Category deleted successfully.');
                        redirect('categories.php');
                    }
                } catch (PDOException $e) {
                    setFlashMessage('danger', 'Error deleting category.');
                    redirect('categories.php');
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
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M12 5v14M5 12h14"/></svg>
                Add Category
            </button>
        </div>
    </div>

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
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-<?php echo $cat['is_active'] ? 'warning' : 'success'; ?>">
                                        <?php if ($cat['is_active']): ?>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <?php else: ?>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                        <?php endif; ?>
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