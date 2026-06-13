<?php
/**
 * Admin - User Management
 * IT Helpdesk Ticketing System
 */

$page_title = 'User Management';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Admin');

$db = getDB();
$message = '';
$message_type = '';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $message = 'Invalid request. Please try again.';
        $message_type = 'danger';
    } else {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $full_name = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $role_id = $_POST['role_id'] ?? '';
            $department_id = $_POST['department_id'] ?? null;
            
            // Validate
            if (empty($username) || empty($email) || empty($password) || empty($full_name) || empty($role_id)) {
                $message = 'Please fill in all required fields.';
                $message_type = 'danger';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = 'Please enter a valid email address.';
                $message_type = 'danger';
            } elseif (strlen($password) < 6) {
                $message = 'Password must be at least 6 characters.';
                $message_type = 'danger';
            } else {
                try {
                    // Check if username or email exists
                    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    if ($stmt->fetch()) {
                        $message = 'Username or email already exists.';
                        $message_type = 'danger';
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, phone, role_id, department_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $role_id, $department_id]);
                        
                        $message = 'User added successfully.';
                        $message_type = 'success';
                    }
                } catch (PDOException $e) {
                    $message = 'Error adding user: ' . $e->getMessage();
                    $message_type = 'danger';
                }
            }
            break;
            
        case 'toggle_status':
            $user_id = $_POST['user_id'] ?? 0;
            if ($user_id) {
                try {
                    $stmt = $db->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $message = 'User status updated.';
                    $message_type = 'success';
                } catch (PDOException $e) {
                    $message = 'Error updating user.';
                    $message_type = 'danger';
                }
            }
            break;
            
        case 'delete':
            $user_id = $_POST['user_id'] ?? 0;
            if ($user_id && $user_id != $_SESSION['user_id']) {
                try {
                    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $message = 'User deleted successfully.';
                    $message_type = 'success';
                } catch (PDOException $e) {
                    $message = 'Error deleting user.';
                    $message_type = 'danger';
                }
            }
            break;
    }
    }
}

$csrf_token = generateCSRFToken();

// Get all users with roles and departments
$stmt = $db->query("SELECT u.*, r.role_name, d.department_name 
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id 
                    LEFT JOIN departments d ON u.department_id = d.id
                    ORDER BY u.created_at DESC");
$users = $stmt->fetchAll();

// Get roles
$roles = $db->query("SELECT * FROM roles ORDER BY role_name")->fetchAll();

// Get departments
$departments = $db->query("SELECT * FROM departments ORDER BY department_name")->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-0 fw-bold">User Management</h1>
                <p class="text-muted mb-0 small">Create, edit, and manage user accounts and roles</p>
            </div>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Add User
            </button>
        </div>
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
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <?php if (!empty($user['profile_photo'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/uploads/profile/<?php echo sanitize($user['profile_photo']); ?>" 
                                         alt="<?php echo sanitize($user['full_name']); ?>" class="user-list-photo">
                                <?php else: ?>
                                    <div class="user-list-avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo sanitize($user['username']); ?></td>
                            <td><?php echo sanitize($user['full_name']); ?></td>
                            <td><?php echo sanitize($user['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['role_name'] === 'Admin' ? 'danger' : ($user['role_name'] === 'Technician' ? 'primary' : 'success'); ?>">
                                    <?php echo $user['role_name']; ?>
                                </span>
                            </td>
                            <td><?php echo $user['department_name'] ? sanitize($user['department_name']) : 'N/A'; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                    <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td><?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Never'; ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <button type="submit" class="btn btn-sm btn-<?php echo $user['is_active'] ? 'warning' : 'success'; ?>" 
                                            title="<?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="<?php echo $user['is_active'] ? 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z' : 'M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24'; ?>"/><line x1="1" y1="1" x2="23" y2="23" class="<?php echo $user['is_active'] ? '' : 'hidden-line'; ?>"/></svg>
                                    </button>
                                </form>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role *</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo $role['role_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo $dept['department_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>