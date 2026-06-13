<?php
/**
 * Settings Page
 * IT Helpdesk Ticketing System
 */

$page_title = 'Settings';
require_once __DIR__ . '/includes/auth.php';

$db = getDB();
$user_id = getCurrentUserId();

// Get current settings
$settings = getAllUserSettings($user_id);

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('danger', 'Invalid request.');
        redirect('settings.php');
    }

    $section = $_POST['section'] ?? '';

    if ($section === 'theme') {
        $color = $_POST['theme_color'] ?? '#2d5a8e';
        $mode = $_POST['theme_mode'] ?? 'light';
        $sidebar = isset($_POST['sidebar_collapsed']) ? '1' : '0';
        $compact = isset($_POST['compact_mode']) ? '1' : '0';

        // Validate hex color
        if (!preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
            $color = '#2d5a8e';
        }

        setUserSetting($user_id, 'theme_color', $color);
        setUserSetting($user_id, 'theme_mode', $mode);
        setUserSetting($user_id, 'sidebar_collapsed', $sidebar);
        setUserSetting($user_id, 'compact_mode', $compact);

        setFlashMessage('success', 'Theme settings saved successfully.');
    }

    if ($section === 'notifications') {
        setUserSetting($user_id, 'email_notifications', isset($_POST['email_notifications']) ? '1' : '0');
        setUserSetting($user_id, 'browser_notifications', isset($_POST['browser_notifications']) ? '1' : '0');
        setUserSetting($user_id, 'ticket_updates', isset($_POST['ticket_updates']) ? '1' : '0');
        setUserSetting($user_id, 'comment_notifications', isset($_POST['comment_notifications']) ? '1' : '0');

        setFlashMessage('success', 'Notification preferences saved.');
    }

    redirect('settings.php');
}

$csrf_token = generateCSRFToken();

// Get department name
$dept_name = 'N/A';
if ($_SESSION['department_id'] ?? null) {
    $stmt = $db->prepare("SELECT department_name FROM departments WHERE id = ?");
    $stmt->execute([$_SESSION['department_id']]);
    $dept = $stmt->fetch();
    $dept_name = $dept ? $dept['department_name'] : 'N/A';
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';

// Current theme values
$current_color = $settings['theme_color'] ?? '#2d5a8e';
$current_mode = $settings['theme_mode'] ?? 'light';
$sidebar_collapsed = ($settings['sidebar_collapsed'] ?? '0') === '1';
$compact_mode = ($settings['compact_mode'] ?? '0') === '1';

// Preset colors
$presets = [
    '#2d5a8e' => 'Ocean Blue',
    '#6366f1' => 'Indigo',
    '#8b5cf6' => 'Violet',
    '#ec4899' => 'Pink',
    '#e63757' => 'Crimson',
    '#17a673' => 'Emerald',
    '#e6a817' => 'Amber',
    '#0891b2' => 'Cyan',
    '#64748b' => 'Slate',
    '#1a1d21' => 'Charcoal',
];
?>

<style>
.settings-section { display: none; }
.settings-section.active { display: block; }
.color-swatch {
    width: 42px; height: 42px; border-radius: 50%; border: 3px solid transparent;
    cursor: pointer; transition: all 0.2s; position: relative;
}
.color-swatch:hover { transform: scale(1.1); }
.color-swatch.active { border-color: #1a1d21; box-shadow: 0 0 0 3px rgba(0,0,0,0.1); }
.color-swatch.active::after {
    content: '\f00c'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
    position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 0.8rem;
}
.settings-nav .nav-link {
    border-radius: 8px; padding: 0.7rem 1rem; color: #5a5c69; font-weight: 500;
    margin-bottom: 0.25rem; transition: all 0.2s;
}
.settings-nav .nav-link:hover { background: #f0f2f5; color: #1a1d21; }
.settings-nav .nav-link.active { background: #2d5a8e; color: #fff; }
.settings-nav .nav-link i { width: 20px; text-align: center; margin-right: 0.5rem; }
.preview-card {
    border: 2px dashed #e4e7ec; border-radius: 12px; padding: 1.5rem;
    text-align: center; transition: all 0.3s;
}
.preview-navbar { background: var(--preview-color, #2d5a8e); color: #fff; padding: 0.75rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
.preview-btn { background: var(--preview-color, #2d5a8e); color: #fff; border: none; padding: 0.4rem 1rem; border-radius: 6px; font-size: 0.8rem; }
.preview-badge { background: var(--preview-color, #2d5a8e); color: #fff; padding: 0.2rem 0.6rem; border-radius: 10px; font-size: 0.7rem; }
.form-check-input:checked { background-color: var(--preview-color, #2d5a8e); border-color: var(--preview-color, #2d5a8e); }
</style>

<div class="container-fluid py-4">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">Settings</h4>
        <p class="text-muted mb-0">Customize your experience</p>
    </div>

        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Settings Navigation -->
        <div class="col-lg-3">
            <div class="card ticket-card">
                <div class="card-body p-3">
                    <nav class="settings-nav">
                        <a class="nav-link active" href="#" data-section="appearance">
                            Appearance
                        </a>
                        <a class="nav-link" href="#" data-section="notifications">
                            Notifications
                        </a>
                        <a class="nav-link" href="#" data-section="account">
                            Account
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="col-lg-9">
            <!-- Appearance Section -->
            <div class="settings-section active" id="section-appearance">
                <form method="POST" id="themeForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="section" value="theme">

                    <!-- Theme Color -->
                    <div class="card ticket-card mb-4">
                        <div class="ticket-card-header">
                            Theme Color
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Choose your primary color. This will be applied to the navbar, buttons, links, and accent elements.</p>
                            
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <?php foreach ($presets as $hex => $name): ?>
                                <div class="text-center">
                                    <div class="color-swatch <?php echo $current_color === $hex ? 'active' : ''; ?>" 
                                         style="background-color: <?php echo $hex; ?>"
                                         data-color="<?php echo $hex; ?>"
                                         title="<?php echo $name; ?>"
                                         onclick="selectColor(this, '<?php echo $hex; ?>')">
                                    </div>
                                    <small class="text-muted d-block mt-1"><?php echo $name; ?></small>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-semibold">Custom Color</label>
                                <div class="input-group" style="max-width: 300px;">
                                    <input type="color" class="form-control form-control-color" id="customColor" 
                                           value="<?php echo htmlspecialchars($current_color); ?>" 
                                           onchange="selectColor(null, this.value)">
                                    <input type="text" class="form-control" id="colorHex" name="theme_color" 
                                           value="<?php echo htmlspecialchars($current_color); ?>" 
                                           maxlength="7" pattern="#[a-fA-F0-9]{6}" 
                                           oninput="selectColor(null, this.value)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Theme Mode -->
                    <div class="card ticket-card mb-4">
                        <div class="ticket-card-header">
                            Theme Mode
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="theme-mode-option <?php echo $current_mode === 'light' ? 'active' : ''; ?>" for="modeLight">
                                        <input type="radio" class="d-none" name="theme_mode" value="light" id="modeLight" <?php echo $current_mode === 'light' ? 'checked' : ''; ?>>
                                        <div class="preview-card" style="background: #fff;">
                                            <div class="preview-navbar mb-2">Navbar</div>
                                            <div class="text-start small">
                                                <div class="mb-1"><span class="preview-badge">Badge</span></div>
                                                <div class="text-muted">Light background</div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-2 fw-semibold">Light</div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="theme-mode-option <?php echo $current_mode === 'dark' ? 'active' : ''; ?>" for="modeDark">
                                        <input type="radio" class="d-none" name="theme_mode" value="dark" id="modeDark" <?php echo $current_mode === 'dark' ? 'checked' : ''; ?>>
                                        <div class="preview-card" style="background: #1a1d21; color: #e4e7ec;">
                                            <div class="preview-navbar mb-2" style="background: #2d3748;">Navbar</div>
                                            <div class="text-start small">
                                                <div class="mb-1"><span class="preview-badge">Badge</span></div>
                                                <div style="color: #95aac9;">Dark background</div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-2 fw-semibold">Dark</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Display Options -->
                    <div class="card ticket-card mb-4">
                        <div class="ticket-card-header">
                            Display Options
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="compactMode" name="compact_mode" value="1" <?php echo $compact_mode ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="compactMode">
                                        <strong>Compact Mode</strong>
                                        <div class="text-muted small">Reduce spacing and padding for a denser layout</div>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sidebarCollapsed" name="sidebar_collapsed" value="1" <?php echo $sidebar_collapsed ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="sidebarCollapsed">
                                        <strong>Collapse Sidebar</strong>
                                        <div class="text-muted small">Start with navigation sidebar collapsed</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview -->
                    <div class="card ticket-card mb-4">
                        <div class="ticket-card-header">
                            Live Preview
                        </div>
                        <div class="card-body">
                            <div class="preview-area p-3 rounded" id="previewArea" style="background: #f0f2f5; border: 1px solid #e4e7ec;">
                                <div style="border-radius: 8px; overflow: hidden; border: 1px solid #e4e7ec; background: #fff;">
                                    <div id="previewTopBar" style="height: 3px; background: <?php echo $current_color; ?>;"></div>
                                    <div style="padding: 0.6rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                                        <div style="font-weight: 600; font-size: 0.85rem; color: #1a1d21;">IT Helpdesk</div>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="font-size: 0.75rem; color: #5a5c69;">Notifications</span>
                                            <div style="width: 28px; height: 28px; border-radius: 6px; background: <?php echo $current_color; ?>; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 700;">AD</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" class="btn btn-sm" id="previewBtnPrimary" style="background: <?php echo $current_color; ?>; color: #fff;">
                                        New Ticket
                                    </button>
                                    <span class="badge" id="previewBadge" style="background: <?php echo $current_color; ?>;">Active</span>
                                </div>
                                <div class="p-2 rounded mt-2" style="background: #fff; border: 1px solid #e4e7ec;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold small">TK-2026-001</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">WiFi not connecting</div>
                                        </div>
                                        <span class="badge" id="previewStatus" style="background: <?php echo $current_color; ?>;">In Progress</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            Save Theme Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notifications Section -->
            <div class="settings-section" id="section-notifications">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="section" value="notifications">

                    <div class="card ticket-card mb-4">
                        <div class="ticket-card-header">
                            Notification Preferences
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="emailNotif" name="email_notifications" value="1" <?php echo ($settings['email_notifications'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="emailNotif">
                                        <strong>Email Notifications</strong>
                                        <div class="text-muted small">Receive email alerts for important updates</div>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="browserNotif" name="browser_notifications" value="1" <?php echo ($settings['browser_notifications'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="browserNotif">
                                        <strong>Browser Notifications</strong>
                                        <div class="text-muted small">Show desktop push notifications</div>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ticketUpdates" name="ticket_updates" value="1" <?php echo ($settings['ticket_updates'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="ticketUpdates">
                                        <strong>Ticket Updates</strong>
                                        <div class="text-muted small">Notify when your tickets are updated</div>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="commentNotif" name="comment_notifications" value="1" <?php echo ($settings['comment_notifications'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="commentNotif">
                                        <strong>New Comments</strong>
                                        <div class="text-muted small">Notify when someone comments on your ticket</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            Save Notification Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Account Section -->
            <div class="settings-section" id="section-account">
                <div class="card ticket-card mb-4">
                    <div class="ticket-card-header">
                        Account Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Full Name</label>
                                <p class="fw-semibold mb-0"><?php echo sanitize($_SESSION['full_name'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Email</label>
                                <p class="fw-semibold mb-0"><?php echo sanitize($_SESSION['email'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Username</label>
                                <p class="fw-semibold mb-0"><?php echo sanitize($_SESSION['username'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Role</label>
                                <p class="mb-0">
                                    <span class="badge <?php echo match($_SESSION['role_name'] ?? '') {
                                        'Admin' => 'bg-danger',
                                        'Technician' => 'bg-primary',
                                        default => 'bg-success'
                                    }; ?>"><?php echo sanitize($_SESSION['role_name'] ?? 'N/A'); ?></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Department</label>
                                <p class="fw-semibold mb-0"><?php echo sanitize($dept_name); ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Account Status</label>
                                <p class="mb-0"><span class="badge bg-success">Active</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card ticket-card">
                    <div class="ticket-card-header">
                        About
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">System Version</label>
                                <p class="fw-semibold mb-0">1.0.0</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Last Login</label>
                                <p class="fw-semibold mb-0"><?php echo date('M d, Y h:i A'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.theme-mode-option {
    display: block; cursor: pointer; border-radius: 12px; border: 2px solid #e4e7ec;
    padding: 0.5rem; transition: all 0.2s;
}
.theme-mode-option:hover { border-color: #95aac9; }
.theme-mode-option.active { border-color: <?php echo $current_color; ?>; box-shadow: 0 0 0 3px rgba(45, 90, 142, 0.15); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Section navigation
    document.querySelectorAll('.settings-nav .nav-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var section = this.dataset.section;
            
            document.querySelectorAll('.settings-nav .nav-link').forEach(function(l) { l.classList.remove('active'); });
            this.classList.add('active');
            
            document.querySelectorAll('.settings-section').forEach(function(s) { s.classList.remove('active'); });
            document.getElementById('section-' + section).classList.add('active');
        });
    });

    // Theme mode toggle
    document.querySelectorAll('.theme-mode-option').forEach(function(opt) {
        opt.addEventListener('click', function() {
            document.querySelectorAll('.theme-mode-option').forEach(function(o) { o.classList.remove('active'); });
            this.classList.add('active');
        });
    });
});

function selectColor(el, color) {
    if (!/^#[a-fA-F0-9]{6}$/.test(color)) return;

    document.querySelectorAll('.color-swatch').forEach(function(s) { s.classList.remove('active'); });
    if (el) el.classList.add('active');
    
    document.getElementById('colorHex').value = color;
    document.getElementById('customColor').value = color;
    document.querySelector('input[name="theme_color"]').value = color;

    // Update live preview
    var preview = document.getElementById('previewArea');
    preview.style.setProperty('--preview-color', color);
    document.getElementById('previewTopBar').style.background = color;
    document.getElementById('previewBtnPrimary').style.background = color;
    document.getElementById('previewBadge').style.background = color;
    document.getElementById('previewStatus').style.background = color;

    // Update mode option borders
    document.querySelectorAll('.theme-mode-option').forEach(function(opt) {
        opt.style.borderColor = '#e4e7ec';
    });
    var activeMode = document.querySelector('.theme-mode-option.active');
    if (activeMode) activeMode.style.borderColor = color;
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
