<?php
/**
 * User - Create Ticket
 * IT Helpdesk Ticketing System
 */

$page_title = 'Create New Ticket';
require_once __DIR__ . '/../includes/auth.php';
requireRole('Employee');

$db = getDB();
$user_id = getCurrentUserId();

// Get categories
$categories = $db->query("SELECT * FROM ticket_categories WHERE is_active = 1 ORDER BY category_name")->fetchAll();

// Get departments
$departments = $db->query("SELECT * FROM departments ORDER BY department_name")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $priority = $_POST['priority'] ?? 'Medium';
    $department_id = $_POST['department_id'] ?? null;
    
    // Validate
    if (empty($subject) || empty($description) || empty($category_id)) {
        setFlashMessage('danger', 'Please fill in all required fields.');
    } else {
        try {
            $ticket_number = generateTicketNumber();
            
            // Insert ticket
            $stmt = $db->prepare("INSERT INTO tickets (ticket_number, subject, description, category_id, priority, status, created_by, department_id) VALUES (?, ?, ?, ?, ?, 'Created', ?, ?)");
            $stmt->execute([$ticket_number, $subject, $description, $category_id, $priority, $user_id, $department_id]);
            
            $ticket_id = $db->lastInsertId();
            
            // Handle file upload
            if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
                $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'];
                
                foreach ($_FILES['attachments']['name'] as $key => $name) {
                    if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_name = time() . '_' . basename($name);
                        $file_path = '../uploads/' . $file_name;
                        $file_type = $_FILES['attachments']['type'][$key];
                        $file_size = $_FILES['attachments']['size'][$key];
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        
                        if (!in_array($ext, $allowed_exts)) {
                            continue;
                        }
                        
                        if ($file_size <= MAX_FILE_SIZE) {
                            if (move_uploaded_file($_FILES['attachments']['tmp_name'][$key], $file_path)) {
                                $stmt = $db->prepare("INSERT INTO ticket_attachments (ticket_id, user_id, file_name, file_path, file_size, file_type) VALUES (?, ?, ?, ?, ?, ?)");
                                $stmt->execute([$ticket_id, $user_id, $name, $file_path, $file_size, $file_type]);
                            }
                        }
                    }
                }
            }
            
            // Log activity
            $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, 'Create Ticket', "Created ticket #$ticket_number", $_SERVER['REMOTE_ADDR']]);
            
            // Notify admin
            $stmt = $db->prepare("SELECT id FROM users WHERE role_id = 1");
            $stmt->execute();
            $admins = $stmt->fetchAll();
            foreach ($admins as $admin) {
                $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
                $stmt->execute([$admin['id'], 'New Ticket Created', "Ticket #$ticket_number has been created", "../admin/view_ticket.php?id=$ticket_id"]);
            }
            
            setFlashMessage('success', 'Ticket created successfully!');
            redirect("view_ticket.php?id=$ticket_id");
        } catch (PDOException $e) {
            setFlashMessage('danger', 'Error creating ticket. Please try again.');
        }
    }
}

$csrf_token = generateCSRFToken();
$subject = '';
$description = '';

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="h3 mb-0 fw-bold">Create New Ticket</h1>
                <p class="text-muted mb-0 small">Submit a new support request to the helpdesk team</p>
            </div>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg> Back to Dashboard</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ticket Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   value="<?php echo sanitize($subject ?? ''); ?>" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo sanitize($cat['category_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority *</label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select class="form-select" id="department_id" name="department_id">
                                <option value="">Select Department (Optional)</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>"><?php echo sanitize($dept['department_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="6" required><?php echo sanitize($description ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="attachments" class="form-label">Attachments</label>
                            <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                            <div class="form-text">Max file size: 5MB. Allowed: Images, PDF, Documents.</div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Submit Ticket
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li class="mb-2">Provide a clear and concise subject line.</li>
                        <li class="mb-2">Include all relevant details in the description.</li>
                        <li class="mb-2">Select the appropriate category for faster routing.</li>
                        <li class="mb-2">Set the correct priority level based on urgency.</li>
                        <li class="mb-0">Attach screenshots if applicable.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>