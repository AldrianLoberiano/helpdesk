<?php
/**
 * Header Include
 * IT Helpdesk Ticketing System
 */

// Get notifications for current user
$notifications = [];
$unread_count = 0;
$user_settings = [];
if (isset($_SESSION['user_id'])) {
    $notifications = getUserNotifications(5);
    $unread_count = getUnreadNotificationCount();
    $user_settings = getAllUserSettings($_SESSION['user_id']);
}

// Theme settings
$theme_color = $user_settings['theme_color'] ?? '#2d5a8e';
$theme_mode = $user_settings['theme_mode'] ?? 'light';
$compact_mode = ($user_settings['compact_mode'] ?? '0') === '1';

// Derive lighter/darker shades from theme color
function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2))
    ];
}
function rgbToHex($r, $g, $b) {
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

$rgb = hexToRgb($theme_color);
$color_light = rgbToHex(min(255, $rgb['r'] + 40), min(255, $rgb['g'] + 40), min(255, $rgb['b'] + 40));
$color_lighter = rgbToHex(min(255, $rgb['r'] + 80), min(255, $rgb['g'] + 80), min(255, $rgb['b'] + 80));
$color_dark = rgbToHex(max(0, $rgb['r'] - 20), max(0, $rgb['g'] - 20), max(0, $rgb['b'] - 20));
$color_alpha = "rgba({$rgb['r']}, {$rgb['g']}, {$rgb['b']}, 0.08)";
$color_alpha_border = "rgba({$rgb['r']}, {$rgb['g']}, {$rgb['b']}, 0.15)";

// Dark mode colors
$bg_primary = $theme_mode === 'dark' ? '#1a1d21' : '#f0f2f5';
$bg_card = $theme_mode === 'dark' ? '#2d3748' : '#fff';
$bg_navbar = $theme_mode === 'dark' ? '#2d3748' : $theme_color;
$text_primary = $theme_mode === 'dark' ? '#e4e7ec' : '#1a1d21';
$text_secondary = $theme_mode === 'dark' ? '#95aac9' : '#6c757d';
$border_color = $theme_mode === 'dark' ? '#4a5568' : '#e4e7ec';
$bg_body = $theme_mode === 'dark' ? '#111827' : '#f0f2f5';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary: <?php echo $theme_color; ?>;
            --primary-light: <?php echo $color_light; ?>;
            --primary-lighter: <?php echo $color_lighter; ?>;
            --primary-dark: <?php echo $color_dark; ?>;
            --primary-alpha: <?php echo $color_alpha; ?>;
            --primary-alpha-border: <?php echo $color_alpha_border; ?>;
            --bg-body: <?php echo $bg_body; ?>;
            --bg-card: <?php echo $bg_card; ?>;
            --bg-navbar: <?php echo $bg_navbar; ?>;
            --text-primary: <?php echo $text_primary; ?>;
            --text-secondary: <?php echo $text_secondary; ?>;
            --border-color: <?php echo $border_color; ?>;
        }
        body { background-color: var(--bg-body); color: var(--text-primary); }
        .navbar { background: var(--bg-navbar) !important; }
        .nav-user-avatar { background-color: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.3); }
        .card { background: var(--bg-card); border-color: var(--border-color); }
        .ticket-card-header { background: <?php echo $theme_mode === 'dark' ? '#374151' : '#f8f9fc'; ?>; border-color: var(--border-color); }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-outline-primary { color: var(--primary); border-color: var(--primary); }
        .btn-outline-primary:hover { background: var(--primary); border-color: var(--primary); color: #fff; }
        .text-primary { color: var(--primary) !important; }
        .badge.bg-primary { background-color: var(--primary) !important; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 0.2rem var(--primary-alpha-border); }
        .ticket-status-badge.status-open { background-color: var(--primary-alpha); color: var(--primary); }
        .ticket-status-badge.status-pending { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(230,168,23,0.2)' : '#fef3d6'; ?>; color: #b8860b; }
        .ticket-status-badge.status-assigned { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(45,90,142,0.2)' : '#e8f0fe'; ?>; color: #2d5a8e; }
        .ticket-status-badge.status-in-progress { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(230,168,23,0.2)' : '#fff3cd'; ?>; color: #856404; }
        .ticket-status-badge.status-resolved { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(23,166,115,0.2)' : '#d4f5e9'; ?>; color: #17a673; }
        .ticket-status-badge.status-closed { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(149,170,201,0.2)' : '#f0f2f5'; ?>; color: #6c757d; }
        .ticket-priority-badge.priority-critical { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(230,55,87,0.2)' : '#fde2e8'; ?>; color: #e63757; }
        .ticket-priority-badge.priority-high { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(230,168,23,0.2)' : '#fef3d6'; ?>; color: #e6a817; }
        .ticket-priority-badge.priority-medium { background-color: var(--primary-alpha); color: var(--primary); }
        .ticket-priority-badge.priority-low { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(23,166,115,0.2)' : '#d4f5e9'; ?>; color: #17a673; }
        .ticket-priority-badge.priority-done { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(149,170,201,0.2)' : '#f0f2f5'; ?>; color: #6c757d; }
        .comment-body { background: <?php echo $theme_mode === 'dark' ? '#374151' : '#f8f9fc'; ?>; border-color: var(--border-color); }
        .comment-role-badge.role-admin { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(230,55,87,0.2)' : '#fde2e8'; ?>; color: #e63757; }
        .comment-role-badge.role-technician { background-color: var(--primary-alpha); color: var(--primary); }
        .comment-role-badge.role-employee { background-color: <?php echo $theme_mode === 'dark' ? 'rgba(23,166,115,0.2)' : '#d4f5e9'; ?>; color: #17a673; }
        .table thead th { background: <?php echo $theme_mode === 'dark' ? '#374151' : '#f8f9fc'; ?>; border-color: var(--border-color); color: var(--text-secondary); }
        .table td { border-color: var(--border-color); }
        .table-hover tbody tr:hover { background-color: var(--primary-alpha); }
        .dropdown-menu { background: var(--bg-card); border-color: var(--border-color); }
        .dropdown-item { color: var(--text-primary); }
        .dropdown-item:hover { background: var(--primary-alpha); color: var(--primary); }
        .notification-item.unread { background-color: var(--primary-alpha); border-left: 3px solid var(--primary); }
        .form-control, .form-select { background: var(--bg-card); color: var(--text-primary); border-color: var(--border-color); }
        .input-group-text { background: <?php echo $theme_mode === 'dark' ? '#374151' : '#f8f9fc'; ?>; border-color: var(--border-color); color: var(--text-secondary); }
        .footer { background: var(--bg-card); border-color: var(--border-color); }
        .settings-nav .nav-link.active { background: var(--primary); }
        .theme-mode-option.active { border-color: var(--primary); }
        .attachment-icon { background: var(--primary-alpha); color: var(--primary); }
        <?php if ($compact_mode): ?>
        .card-body { padding: 0.85rem !important; }
        .ticket-card-header { padding: 0.65rem 1rem !important; }
        .container-fluid.py-4 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
        <?php endif; ?>
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="wrapper">