<?php
$page_title = 'Remote Desktop Documentation';
require_once __DIR__ . '/includes/auth.php';
requireRole(['Admin', 'Technician']);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 sticky-top" style="top: 80px;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Documentation</h6>
                    <nav class="nav flex-column nav-pills">
                        <a class="nav-link text-start active" href="#overview">Overview</a>
                        <a class="nav-link text-start" href="#user-list">User List</a>
                        <a class="nav-link text-start" href="#connecting">Connecting</a>
                        <a class="nav-link text-start" href="#remote-session">Remote Session</a>
                        <a class="nav-link text-start" href="#toolbar">Toolbar</a>
                        <a class="nav-link text-start" href="#disconnect">Disconnecting</a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="fw-bold mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -4px;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Remote Desktop Documentation
                    </h3>

                    <section id="overview" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Overview</h5>
                        <p>The Remote Desktop module allows administrators and technicians to connect to employee computers remotely for troubleshooting, support, and maintenance.</p>
                        <div class="alert alert-warning">
                            <strong>Permission:</strong> Remote access should only be used for legitimate IT support purposes.
                        </div>
                    </section>

                    <section id="user-list" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">User List</h5>
                        <p>The left panel shows all employees with their computers:</p>
                        <ul>
                            <li><strong>User Avatar</strong> - Profile picture or initials</li>
                            <li><strong>Full Name</strong> - Employee name</li>
                            <li><strong>Department</strong> - Assigned department</li>
                            <li><strong>Username</strong> - Login username</li>
                            <li><strong>Status Indicator</strong> - Green dot = Online</li>
                        </ul>
                        <h6 class="fw-bold mt-3">Search Users</h6>
                        <p>Use the search bar to filter users by name, department, or username.</p>
                    </section>

                    <section id="connecting" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Connecting to a Computer</h5>
                        <p>There are two ways to connect to a user's computer:</p>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="fw-bold">Method 1: Select and Connect</h6>
                                    <ol class="mb-0">
                                        <li>Click on a user card to select them</li>
                                        <li>Click "Open Remote Desktop" button</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="fw-bold">Method 2: Quick Connect</h6>
                                    <ol class="mb-0">
                                        <li>Click the "Connect" button on the user card</li>
                                        <li>Remote session starts automatically</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="remote-session" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Remote Session Window</h5>
                        <p>Once connected, a full-screen remote desktop window opens with:</p>
                        <ul>
                            <li><strong>Remote Screen</strong> - Live view of the user's desktop</li>
                            <li><strong>Taskbar</strong> - Simulated Windows taskbar</li>
                            <li><strong>Connection Timer</strong> - Shows session duration</li>
                            <li><strong>User Info</strong> - Current user being accessed</li>
                        </ul>
                    </section>

                    <section id="toolbar" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Toolbar Functions</h5>
                        <p>The toolbar at the bottom provides quick actions:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Button</th>
                                        <th>Function</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td><strong>Ctrl+Alt+Del</strong></td><td>Send Ctrl+Alt+Del command to remote computer</td></tr>
                                    <tr><td><strong>Task Manager</strong></td><td>Open Task Manager on remote computer</td></tr>
                                    <tr><td><strong>CMD</strong></td><td>Open Command Prompt on remote computer</td></tr>
                                    <tr><td><strong>Transfer</strong></td><td>Open file transfer dialog</td></tr>
                                    <tr><td><strong>Fullscreen</strong></td><td>Toggle fullscreen mode</td></tr>
                                    <tr><td><strong>Disconnect</strong></td><td>End the remote session</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section id="disconnect" class="mb-4">
                        <h5 class="fw-bold text-primary mb-3">Disconnecting</h5>
                        <p>To end a remote session:</p>
                        <ol>
                            <li>Click the <strong>Disconnect</strong> button in the toolbar</li>
                            <li>Or click the <strong>X</strong> button in the top-right corner</li>
                            <li>The session timer will stop</li>
                            <li>You'll return to the main Remote Desktop page</li>
                        </ol>
                        <div class="alert alert-info">
                            <strong>Tip:</strong> Always disconnect properly when done to ensure security.
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.nav-pills .nav-link').forEach(link => {
    link.addEventListener('click', function() {
        document.querySelectorAll('.nav-pills .nav-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
