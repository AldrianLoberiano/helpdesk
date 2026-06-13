<?php
/**
 * Profile Page
 * IT Helpdesk Ticketing System
 */

$page_title = 'My Profile';
require_once __DIR__ . '/includes/auth.php';

$db = getDB();
$user_id = getCurrentUserId();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('danger', 'Invalid request.');
        redirect('profile.php');
    }

    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if (empty($full_name) || empty($email)) {
            setFlashMessage('danger', 'Name and email are required.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('danger', 'Invalid email address.');
        } else {
            try {
                // Check email uniqueness
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $user_id]);
                if ($stmt->fetch()) {
                    setFlashMessage('danger', 'Email already in use.');
                } else {
                    $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
                    $stmt->execute([$full_name, $email, $phone, $user_id]);
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['email'] = $email;
                    setFlashMessage('success', 'Profile updated successfully.');
                }
            } catch (PDOException $e) {
                setFlashMessage('danger', 'Error updating profile.');
            }
        }
        redirect('profile.php');
    }
    
    if ($action === 'upload_photo') {
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_photo'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($ext, $allowed)) {
                setFlashMessage('danger', 'Only JPG, PNG, and GIF images are allowed.');
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                setFlashMessage('danger', 'Image must be under 2MB.');
            } else {
                $dir = __DIR__ . '/uploads/profile/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
                $destination = $dir . $filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    // Delete old photo if exists
                    $stmt = $db->prepare("SELECT profile_photo FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $old = $stmt->fetchColumn();
                    if ($old) {
                        $oldPath = __DIR__ . '/uploads/profile/' . $old;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $stmt = $db->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
                    $stmt->execute([$filename, $user_id]);
                    $_SESSION['profile_photo'] = $filename;
                    setFlashMessage('success', 'Profile photo updated.');
                } else {
                    setFlashMessage('danger', 'Failed to upload image.');
                }
            }
        } else {
            setFlashMessage('danger', 'Please select an image to upload.');
        }
        redirect('profile.php');
    }

    if ($action === 'remove_photo') {
        $stmt = $db->prepare("SELECT profile_photo FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $old = $stmt->fetchColumn();
        if ($old) {
            $oldPath = __DIR__ . '/uploads/profile/' . $old;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $stmt = $db->prepare("UPDATE users SET profile_photo = NULL WHERE id = ?");
            $stmt->execute([$user_id]);
            unset($_SESSION['profile_photo']);
            setFlashMessage('success', 'Profile photo removed.');
        }
        redirect('profile.php');
    }

    if ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($current_password) || empty($new_password)) {
            setFlashMessage('danger', 'Please fill in all password fields.');
        } elseif ($new_password !== $confirm_password) {
            setFlashMessage('danger', 'New passwords do not match.');
        } elseif (strlen($new_password) < 6) {
            setFlashMessage('danger', 'Password must be at least 6 characters.');
        } else {
            try {
                $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
                if (password_verify($current_password, $user['password'])) {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed, $user_id]);
                    setFlashMessage('success', 'Password changed successfully.');
                } else {
                    setFlashMessage('danger', 'Current password is incorrect.');
                }
            } catch (PDOException $e) {
                setFlashMessage('danger', 'Error changing password.');
            }
        }
        redirect('profile.php');
    }
}

// Get user info
$stmt = $db->prepare("SELECT u.*, r.role_name, d.department_name 
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id 
                    LEFT JOIN departments d ON u.department_id = d.id 
                    WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$csrf_token = generateCSRFToken();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-banner">
            <h1 class="h3 mb-0 fw-bold">My Profile</h1>
            <p class="text-muted mb-0 small">View and update your personal information</p>
        </div>
    </div>

        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <div class="profile-photo-wrap mb-3">
                        <?php if (!empty($user['profile_photo'])): ?>
                            <img src="<?php echo SITE_URL; ?>/uploads/profile/<?php echo sanitize($user['profile_photo']); ?>" 
                                 alt="Profile" class="profile-photo-lg">
                        <?php else: ?>
                            <div class="profile-photo-placeholder-lg">
                                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h4 class="mb-1"><?php echo sanitize($user['full_name']); ?></h4>
                    <span class="badge bg-<?php echo $user['role_name'] === 'Admin' ? 'danger' : ($user['role_name'] === 'Technician' ? 'primary' : 'success'); ?> mb-2">
                        <?php echo $user['role_name']; ?>
                    </span>
                    <p class="text-muted mb-0">
                        <?php echo sanitize($user['department_name'] ?? 'No Department'); ?>
                    </p>
                    <p class="text-muted">
                        <?php echo sanitize($user['email']); ?>
                    </p>
                    <hr>
                    <div class="text-start">
                        <p class="mb-1"><strong>Username:</strong> <?php echo sanitize($user['username']); ?></p>
                        <p class="mb-1"><strong>Phone:</strong> <?php echo sanitize($user['phone'] ?: 'N/A'); ?></p>
                        <p class="mb-1"><strong>Member Since:</strong> <?php echo formatDate($user['created_at'], 'M d, Y'); ?></p>
                        <p class="mb-0"><strong>Last Login:</strong> <?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Never'; ?></p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Photo</h6>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="photoForm">
                        <input type="hidden" name="action" value="upload_photo">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Upload new photo</label>
                            <input type="file" class="form-control" id="profile_photo" name="profile_photo" 
                                   accept="image/jpeg,image/png,image/gif" onchange="previewPhoto(this)">
                            <div class="form-text">JPG, PNG, or GIF. Max 2MB.</div>
                        </div>
                        <div id="photoPreview" class="text-center mb-3" style="display:none;">
                            <img id="previewImg" src="" alt="Preview" class="profile-photo-preview">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">Upload Photo</button>
                    </form>
                    <?php if (!empty($user['profile_photo'])): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="remove_photo">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100" 
                                    onclick="return confirm('Remove profile photo?')">Remove Photo</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Edit Profile -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Profile</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo sanitize($user['full_name']); ?>" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo sanitize($user['email']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo sanitize($user['phone']); ?>">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password *</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">New Password *</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
function previewPhoto(input) {
    const preview = document.getElementById('photoPreview');
    const img = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
