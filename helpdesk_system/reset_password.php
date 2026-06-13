<?php
/**
 * Reset Password Page
 * IT Helpdesk Ticketing System
 */

require_once __DIR__ . '/config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    redirect('forgot_password.php');
}

// Verify token
$db = getDB();
$stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
$stmt->execute([hash('sha256', $token)]);
$user = $stmt->fetch();

if (!$user) {
    redirect('forgot_password.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($password)) {
            $error = 'Please enter a new password.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
                $stmt->execute([$hashed_password, $user['id']]);
                
                $success = 'Your password has been reset successfully.';
                
                // Log activity
                $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user['id'], 'Password Reset', 'Password has been reset', $_SERVER['REMOTE_ADDR']]);
            } catch (PDOException $e) {
                $error = 'An error occurred. Please try again.';
            }
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Reset Password</h2>
                            <p class="text-muted">Enter your new password</p>
                        </div>

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                                <div class="mt-3">
                                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                </div>

                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    Reset Password
                                </button>
                            </form>
                        <?php endif; ?>

                        <div class="text-center">
                            <a href="login.php" class="text-decoration-none"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg> Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
