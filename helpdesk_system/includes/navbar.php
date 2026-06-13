<?php
$role = $_SESSION['role_name'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);

$role_dir = match($role) {
    'Admin' => 'admin',
    'Technician' => 'technician',
    'Employee' => 'user',
    default => 'user'
};

$in_role_dir = in_array(basename(dirname($_SERVER['PHP_SELF'])), ['admin', 'technician', 'user']);
$base = $in_role_dir ? '../' : '';

$full_name = $_SESSION['full_name'] ?? 'User';
$profile_photo = $_SESSION['profile_photo'] ?? null;
$name_parts = explode(' ', $full_name);
$initials = '';
foreach (array_slice($name_parts, 0, 2) as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $base . $role_dir; ?>/dashboard.php">
            <?php echo SITE_NAME; ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>" 
                       href="<?php echo $base . $role_dir; ?>/dashboard.php">Dashboard</a>
                </li>
                
                <?php if ($role === 'Admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array($current_page, ['users.php', 'departments.php', 'categories.php']) ? 'active' : ''; ?>" 
                           href="#" role="button" data-bs-toggle="dropdown">Management</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base; ?>admin/users.php">Users</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base; ?>admin/departments.php">Departments</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base; ?>admin/categories.php">Categories</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'reports.php' ? 'active' : ''; ?>" 
                           href="<?php echo $base; ?>admin/reports.php">Reports</a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'tickets.php' ? 'active' : ''; ?>" 
                       href="<?php echo $base . $role_dir; ?>/tickets.php">Tickets</a>
                </li>
                
                <?php if ($role === 'Employee'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'create_ticket.php' ? 'active' : ''; ?>" 
                           href="<?php echo $base; ?>user/create_ticket.php">New Ticket</a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav navbar-right-gap">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle notif-link" href="#" role="button" data-bs-toggle="dropdown">
                        <span class="notif-bell-wrap">
                            <svg class="notif-bell" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            <?php if ($unread_count > 0): ?>
                                <span class="badge bg-danger notif-badge"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 300px;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center">
                            <span>Notifications</span>
                            <a href="#" class="text-decoration-none mark-all-read">Mark all read</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <?php if (empty($notifications)): ?>
                            <li class="text-center py-3 text-muted">No notifications</li>
                        <?php else: ?>
                            <?php foreach ($notifications as $notif): ?>
                                <li>
                                    <a class="dropdown-item notification-item <?php echo !$notif['is_read'] ? 'unread' : ''; ?>" 
                                       href="#"
                                       data-notif-id="<?php echo $notif['id']; ?>"
                                       data-notif-title="<?php echo htmlspecialchars($notif['title']); ?>"
                                       data-notif-message="<?php echo htmlspecialchars($notif['message']); ?>"
                                       data-notif-time="<?php echo formatDate($notif['created_at'], 'M d, Y h:i A'); ?>"
                                       data-notif-link="<?php echo $notif['link'] ? SITE_URL . '/' . ltrim($notif['link'], './') : '#'; ?>"
                                        <div class="notification-title"><?php echo sanitize($notif['title']); ?></div>
                                        <div class="notification-message text-muted small"><?php echo sanitize(substr($notif['message'], 0, 50)); ?>...</div>
                                        <div class="notification-time text-muted small"><?php echo formatDate($notif['created_at'], 'M d, h:i A'); ?></div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li class="text-center">
                            <a href="<?php echo $base; ?>notifications.php" class="text-decoration-none">View all notifications</a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link nav-user-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <?php if ($profile_photo): ?>
                            <img src="<?php echo SITE_URL; ?>/uploads/profile/<?php echo sanitize($profile_photo); ?>" 
                                 alt="Profile" class="nav-user-photo">
                        <?php else: ?>
                            <div class="nav-user-avatar"><?php echo $initials; ?></div>
                        <?php endif; ?>
                        <span class="nav-user-name"><?php echo sanitize($full_name); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo $base; ?>profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="<?php echo $base; ?>settings.php">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
