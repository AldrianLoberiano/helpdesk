<?php
$page_title = 'Active Directory Documentation';
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
                        <a class="nav-link text-start" href="#view-users">Viewing Employees</a>
                        <a class="nav-link text-start" href="#search">Searching Users</a>
                        <a class="nav-link text-start" href="#user-info">User Information</a>
                        <a class="nav-link text-start" href="#status">Account Status</a>
                        <a class="nav-link text-start" href="#tips">Tips</a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="fw-bold mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -4px;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        Active Directory Documentation
                    </h3>

                    <section id="overview" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Overview</h5>
                        <p>The Active Directory module provides a comprehensive directory of all employees in the company. It allows administrators and technicians to view employee information, check account status, and manage user accounts.</p>
                        <div class="alert alert-info">
                            <strong>Access:</strong> Only Admin and Technician roles can access the Active Directory.
                        </div>
                    </section>

                    <section id="view-users" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Viewing Employees</h5>
                        <p>The employee list displays all users in the system with the following information:</p>
                        <ul>
                            <li><strong>Photo</strong> - Employee profile picture or initials</li>
                            <li><strong>Full Name</strong> - Complete employee name</li>
                            <li><strong>Username</strong> - Login username</li>
                            <li><strong>Email</strong> - Company email address</li>
                            <li><strong>Phone</strong> - Contact number</li>
                            <li><strong>Role</strong> - Admin, Technician, or Employee</li>
                            <li><strong>Department</strong> - Assigned department</li>
                            <li><strong>Status</strong> - Active or Inactive account</li>
                            <li><strong>Last Login</strong> - Last login timestamp</li>
                        </ul>
                    </section>

                    <section id="search" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Searching Users</h5>
                        <p>Use the search bar at the top of the directory to filter employees:</p>
                        <ol>
                            <li>Click on the search input field</li>
                            <li>Type the employee's name, username, email, or department</li>
                            <li>The list will automatically filter as you type</li>
                        </ol>
                        <div class="bg-light p-3 rounded">
                            <strong>Example:</strong> Typing "IT" will show all employees in the IT Department.
                        </div>
                    </section>

                    <section id="user-info" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">User Information</h5>
                        <p>Each employee card shows detailed information including:</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="fw-bold">Contact Details</h6>
                                    <ul class="mb-0">
                                        <li>Email address</li>
                                        <li>Phone number</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="fw-bold">Work Details</h6>
                                    <ul class="mb-0">
                                        <li>Department assignment</li>
                                        <li>Role/Position</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="status" class="mb-5">
                        <h5 class="fw-bold text-primary mb-3">Account Status</h5>
                        <p>Each employee has an account status that indicates whether they can access the system:</p>
                        <div class="d-flex gap-3 mb-3">
                            <span class="badge bg-success fs-6">Active</span>
                            <span class="badge bg-danger fs-6">Inactive</span>
                        </div>
                        <ul>
                            <li><strong>Active</strong> - User can log in and use the system</li>
                            <li><strong>Inactive</strong> - User account is disabled (terminated, on leave, etc.)</li>
                        </ul>
                    </section>

                    <section id="tips" class="mb-4">
                        <h5 class="fw-bold text-primary mb-3">Tips</h5>
                        <div class="bg-light p-3 rounded">
                            <ul class="mb-0">
                                <li>Click on column headers to sort the directory</li>
                                <li>Use the browser's Ctrl+F for quick text search</li>
                                <li>Check "Last Login" to identify inactive users</li>
                                <li>Contact HR for any user information updates</li>
                            </ul>
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
