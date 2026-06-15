<?php
$page_title = 'Active Directory';
require_once __DIR__ . '/includes/auth.php';
requireRole(['Admin', 'Technician']);

$db = getDB();

// Get all users with roles and departments
$stmt = $db->query("SELECT u.*, r.role_name, d.department_name 
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id 
                    LEFT JOIN departments d ON u.department_id = d.id
                    ORDER BY u.full_name ASC");
$users = $stmt->fetchAll();

// Get counts
$total_users = count($users);
$active_users = count(array_filter($users, fn($u) => $u['is_active']));
$inactive_users = $total_users - $active_users;

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="page-banner mb-4">
        <h1 class="h3 mb-0 fw-bold">Active Directory</h1>
        <p class="text-muted mb-0 small">Company employee directory and information</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small">Total Employees</div>
                            <div class="h4 mb-0 fw-bold"><?php echo $total_users; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#17a673" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small">Active</div>
                            <div class="h4 mb-0 fw-bold text-success"><?php echo $active_users; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#e63757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small">Inactive</div>
                            <div class="h4 mb-0 fw-bold text-danger"><?php echo $inactive_users; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="directoryTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Last Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <?php if (!empty($user['profile_picture'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/uploads/profile/<?php echo sanitize($user['profile_picture']); ?>" 
                                         alt="<?php echo sanitize($user['full_name']); ?>" class="user-list-photo">
                                <?php else: ?>
                                    <div class="user-list-avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold"><?php echo sanitize($user['full_name']); ?></td>
                            <td><?php echo sanitize($user['username']); ?></td>
                            <td><?php echo sanitize($user['email']); ?></td>
                            <td><?php echo $user['phone'] ? sanitize($user['phone']) : 'N/A'; ?></td>
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
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
