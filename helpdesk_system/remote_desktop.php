<?php
$page_title = 'Remote Desktop';
require_once __DIR__ . '/includes/auth.php';
requireRole(['Admin', 'Technician']);

$db = getDB();

// Get all users with their departments
$stmt = $db->prepare("SELECT u.id, u.full_name, u.username, u.email, u.is_active, u.last_login, d.department_name 
                    FROM users u 
                    LEFT JOIN departments d ON u.department_id = d.id
                    WHERE u.id != ?
                    ORDER BY u.full_name ASC");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<style>
.desktop-preview {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    border-radius: 8px;
    aspect-ratio: 16/9;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s;
}
.desktop-preview:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}
.desktop-preview .taskbar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 28px;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    padding: 0 8px;
    gap: 6px;
}
.desktop-preview .taskbar-icon {
    width: 18px;
    height: 18px;
    background: rgba(255,255,255,0.2);
    border-radius: 3px;
}
.desktop-preview .window {
    position: absolute;
    top: 10%;
    left: 10%;
    right: 10%;
    bottom: 20%;
    background: rgba(255,255,255,0.95);
    border-radius: 6px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.desktop-preview .window-titlebar {
    height: 22px;
    background: #e0e0e0;
    border-radius: 6px 6px 0 0;
    display: flex;
    align-items: center;
    padding: 0 6px;
    gap: 4px;
}
.desktop-preview .window-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}
.desktop-preview .window-dot.red { background: #ff5f56; }
.desktop-preview .window-dot.yellow { background: #ffbd2e; }
.desktop-preview .window-dot.green { background: #27c93f; }
.desktop-preview .desktop-icons {
    position: absolute;
    top: 8px;
    right: 8px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.desktop-preview .desktop-icon {
    width: 24px;
    height: 24px;
    background: rgba(255,255,255,0.3);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.desktop-preview .desktop-icon svg {
    width: 14px;
    height: 14px;
    stroke: white;
    fill: none;
    stroke-width: 2;
}
.connection-status {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}
.connection-status.online { background: #28a745; box-shadow: 0 0 6px #28a745; }
.connection-status.offline { background: #6c757d; }
.connection-status.connected { background: #007bff; box-shadow: 0 0 8px #007bff; animation: pulse-dot 1.5s infinite; }
@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 4px #007bff; }
    50% { box-shadow: 0 0 12px #007bff; }
}
.remote-modal .modal-body {
    padding: 0;
}
.remote-screen {
    background: #000;
    aspect-ratio: 16/9;
    position: relative;
    overflow: hidden;
}
.remote-screen .desktop {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    display: flex;
    flex-direction: column;
}
.remote-screen .desktop-wallpaper {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.1);
    font-size: 48px;
    font-weight: bold;
}
.remote-screen .remote-taskbar {
    height: 36px;
    background: rgba(0,0,0,0.85);
    display: flex;
    align-items: center;
    padding: 0 12px;
    gap: 8px;
}
.remote-screen .taskbar-btn {
    padding: 4px 10px;
    background: rgba(255,255,255,0.1);
    border: none;
    border-radius: 4px;
    color: white;
    font-size: 11px;
    cursor: pointer;
}
.remote-screen .taskbar-btn:hover {
    background: rgba(255,255,255,0.2);
}
.remote-screen .taskbar-time {
    margin-left: auto;
    color: white;
    font-size: 11px;
}
.remote-toolbar {
    background: #2d3748;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.remote-toolbar .btn {
    padding: 4px 10px;
    font-size: 12px;
}
.user-computer-card {
    transition: all 0.3s;
    border-left: 3px solid transparent;
}
.user-computer-card:hover {
    border-left-color: var(--primary);
    transform: translateX(4px);
}
.user-computer-card.selected {
    border-left-color: var(--primary);
    background: var(--primary-alpha);
}
</style>

<div class="container-fluid py-4">
    <div class="page-banner mb-4">
        <h1 class="h3 mb-0 fw-bold">Remote Desktop</h1>
        <p class="text-muted mb-0 small">Connect and manage employee computers remotely</p>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small">Total Computers</div>
                            <div class="h4 mb-0 fw-bold"><?php echo count($users); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#17a673" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small">Online</div>
                            <div class="h4 mb-0 fw-bold text-success" id="onlineStats">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#17a2b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small">Connected</div>
                            <div class="h4 mb-0 fw-bold text-info" id="connectedStats">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-secondary bg-opacity-10 rounded-3 p-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small">Offline</div>
                            <div class="h4 mb-0 fw-bold text-secondary" id="offlineStats">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- User List -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Computers</h5>
                        <div class="input-group" style="width: 180px;">
                            <input type="text" class="form-control form-control-sm" id="searchUser" placeholder="Search...">
                        </div>
                    </div>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <div id="userList">
                        <?php foreach ($users as $user): ?>
                        <div class="user-computer-card p-2 mb-2 rounded cursor-pointer" 
                             data-user-id="<?php echo $user['id']; ?>"
                             data-name="<?php echo sanitize($user['full_name']); ?>"
                             data-dept="<?php echo sanitize($user['department_name'] ?? 'N/A'); ?>"
                             data-email="<?php echo sanitize($user['email']); ?>"
                             data-username="<?php echo sanitize($user['username']); ?>">
                            <div class="d-flex align-items-center">
                                <div class="position-relative me-2">
                                    <div class="user-list-avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                                    <span class="connection-status online position-absolute" 
                                          style="bottom: 0; right: 0; border: 2px solid white;"></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small"><?php echo sanitize($user['full_name']); ?></div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        <?php echo sanitize($user['department_name'] ?? 'N/A'); ?> &bull; <?php echo sanitize($user['username']); ?>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-primary connect-btn" 
                                        onclick="connectToUser(<?php echo $user['id']; ?>, '<?php echo sanitize($user['full_name']); ?>', '<?php echo sanitize($user['username']); ?>')">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                    Connect
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Preview -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold" id="previewTitle">Select a computer to preview</h5>
                        <span class="badge bg-secondary" id="previewStatus">No Connection</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="desktopPreview" class="desktop-preview">
                        <div class="window">
                            <div class="window-titlebar">
                                <span class="window-dot red"></span>
                                <span class="window-dot yellow"></span>
                                <span class="window-dot green"></span>
                            </div>
                        </div>
                        <div class="desktop-icons">
                            <div class="desktop-icon">
                                <svg viewBox="0 0 24 24"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            </div>
                            <div class="desktop-icon">
                                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                            <div class="desktop-icon">
                                <svg viewBox="0 0 24 24"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>
                            </div>
                        </div>
                        <div class="taskbar">
                            <div class="taskbar-icon"></div>
                            <div class="taskbar-icon"></div>
                            <div class="taskbar-icon"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span class="text-muted small">Click "Connect" on a user to start remote session</span>
                        </div>
                        <button class="btn btn-primary" id="connectBtn" onclick="openRemoteSession()" disabled>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            Open Remote Desktop
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remote Desktop Modal -->
<div class="modal fade" id="remoteDesktopModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content remote-modal">
            <div class="modal-header py-2">
                <div class="d-flex align-items-center">
                    <span class="connection-status connected me-2"></span>
                    <h6 class="mb-0 fw-bold" id="remoteTitle">Remote Desktop - User</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success" id="connDuration">00:00:00</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" title="Fullscreen" onclick="toggleFullscreen()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="disconnectRemote()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><line x1="1" y1="1" x2="23" y2="23"/><path d="M16.72 11.06A10.94 10.94 0 0119 12.55"/><path d="M5 12.55a10.94 10.94 0 015.17-2.39"/><path d="M10.71 5.05A16 16 0 0122.56 9"/><path d="M1.42 9a15.91 15.91 0 014.7-2.88"/><path d="M8.53 16.11a6 6 0 016.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                        Disconnect
                    </button>
                    <button type="button" class="btn btn-sm btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="remote-screen" id="remoteScreen">
                    <div class="desktop">
                        <div class="desktop-wallpaper" id="remoteDesktop">
                            <div class="text-center">
                                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.3;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                <div class="mt-2" style="opacity: 0.3; font-size: 14px;">Remote Desktop Connected</div>
                            </div>
                        </div>
                        <div class="remote-taskbar">
                            <button class="taskbar-btn">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            </button>
                            <button class="taskbar-btn">File Explorer</button>
                            <button class="taskbar-btn">Browser</button>
                            <button class="taskbar-btn">Terminal</button>
                            <span class="taskbar-time" id="remoteTime"></span>
                        </div>
                    </div>
                </div>
                <div class="remote-toolbar">
                    <button class="btn btn-sm btn-outline-light" title="Ctrl+Alt+Del">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        Ctrl+Alt+Del
                    </button>
                    <button class="btn btn-sm btn-outline-light" title="Task Manager">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Task Manager
                    </button>
                    <button class="btn btn-sm btn-outline-light" title="Command Prompt">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>
                        CMD
                    </button>
                    <button class="btn btn-sm btn-outline-light" title="File Transfer">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Transfer
                    </button>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <span class="text-light small" id="remoteUserInfo"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedUser = null;
let remoteConnected = false;
let connectionTimer = null;
let connectionSeconds = 0;

// Search filter
document.getElementById('searchUser').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.user-computer-card').forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const dept = card.dataset.dept.toLowerCase();
        const username = card.dataset.username.toLowerCase();
        card.style.display = (name.includes(query) || dept.includes(query) || username.includes(query)) ? '' : 'none';
    });
});

// Select user card
document.querySelectorAll('.user-computer-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.closest('.connect-btn')) return;
        document.querySelectorAll('.user-computer-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        
        const name = this.dataset.name;
        const dept = this.dataset.dept;
        document.getElementById('previewTitle').textContent = name + ' - ' + dept;
        document.getElementById('previewStatus').textContent = 'Ready to Connect';
        document.getElementById('previewStatus').className = 'badge bg-warning text-dark';
        document.getElementById('connectBtn').disabled = false;
        
        selectedUser = {
            id: this.dataset.userId,
            name: name,
            username: this.dataset.username
        };
    });
});

function connectToUser(id, name, username) {
    selectedUser = { id, name, username };
    document.getElementById('previewTitle').textContent = name;
    document.getElementById('previewStatus').textContent = 'Connecting...';
    document.getElementById('previewStatus').className = 'badge bg-info';
    document.getElementById('connectBtn').disabled = false;
    
    setTimeout(() => openRemoteSession(), 500);
}

function openRemoteSession() {
    if (!selectedUser) return;
    
    const modal = new bootstrap.Modal(document.getElementById('remoteDesktopModal'));
    document.getElementById('remoteTitle').textContent = 'Remote Desktop - ' + selectedUser.name;
    document.getElementById('remoteUserInfo').textContent = selectedUser.username + '@' + selectedUser.name;
    document.getElementById('remoteTime').textContent = new Date().toLocaleTimeString();
    
    remoteConnected = true;
    connectionSeconds = 0;
    
    document.getElementById('previewStatus').textContent = 'Connected';
    document.getElementById('previewStatus').className = 'badge bg-success';
    
    updateConnectionStatus(1);
    
    modal.show();
    
    connectionTimer = setInterval(() => {
        connectionSeconds++;
        const h = String(Math.floor(connectionSeconds / 3600)).padStart(2, '0');
        const m = String(Math.floor((connectionSeconds % 3600) / 60)).padStart(2, '0');
        const s = String(connectionSeconds % 60).padStart(2, '0');
        document.getElementById('connDuration').textContent = h + ':' + m + ':' + s;
        document.getElementById('remoteTime').textContent = new Date().toLocaleTimeString();
    }, 1000);
}

function disconnectRemote() {
    remoteConnected = false;
    if (connectionTimer) clearInterval(connectionTimer);
    
    document.getElementById('previewStatus').textContent = 'Disconnected';
    document.getElementById('previewStatus').className = 'badge bg-secondary';
    
    updateConnectionStatus(0);
    
    bootstrap.Modal.getInstance(document.getElementById('remoteDesktopModal')).hide();
}

function toggleFullscreen() {
    const el = document.getElementById('remoteScreen');
    if (document.fullscreenElement) {
        document.exitFullscreen();
    } else {
        el.requestFullscreen();
    }
}

function updateConnectionStatus(count) {
    document.getElementById('connectedStats').textContent = count;
}

// Update time
setInterval(() => {
    document.querySelectorAll('.taskbar-time').forEach(el => {
        el.textContent = new Date().toLocaleTimeString();
    });
}, 1000);

// Stats
function updateStats() {
    const total = document.querySelectorAll('.user-computer-card').length;
    const online = Math.floor(total * 0.85);
    const offline = total - online;
    document.getElementById('onlineStats').textContent = online;
    document.getElementById('offlineStats').textContent = offline;
}
updateStats();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
