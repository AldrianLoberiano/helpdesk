<?php
$page_title = 'Login';
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role_name'] ?? '';
    if ($role === 'Admin') {
        redirect('admin/dashboard.php');
    } elseif ($role === 'Technician') {
        redirect('technician/dashboard.php');
    } else {
        redirect('user/dashboard.php');
    }
}

$error = '';
$success = '';
$username = '';

if (isset($_GET['logout'])) {
    $success = 'You have been logged out successfully.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            try {
                $db = getDB();
                
                $stmt = $db->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE (u.username = ? OR u.email = ?) AND u.is_active = 1");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['role_name'] = $user['role_name'];
                    $_SESSION['department_id'] = $user['department_id'];
                    $_SESSION['profile_photo'] = $user['profile_photo'] ?? null;

                    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);

                    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user['id'], 'Login', 'User logged in successfully', $_SERVER['REMOTE_ADDR']]);

                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                        
                        $stmt = $db->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                        $stmt->execute([hash('sha256', $token), $user['id']]);
                    }

                    if ($user['role_name'] === 'Admin') {
                        redirect('admin/dashboard.php');
                    } elseif ($user['role_name'] === 'Technician') {
                        redirect('technician/dashboard.php');
                    } else {
                        redirect('user/dashboard.php');
                    }
                } else {
                    $error = 'Invalid username or password.';
                    
                    if ($user) {
                        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$user['id'], 'Failed Login', 'Invalid password attempt', $_SERVER['REMOTE_ADDR']]);
                    }
                }
            } catch (PDOException $e) {
                error_log("Login error: " . $e->getMessage());
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
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5 login-card">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold"><?php echo SITE_NAME; ?></h2>
                            <p class="text-muted">Sign in to your account</p>
                        </div>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="loginForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username or Email</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo sanitize($username ?? ''); ?>" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="position-relative">
                                    <input type="password" class="form-control pe-5" id="password" name="password" required>
                                    <button type="button" id="togglePassword" class="password-toggle-btn" title="Show/Hide password">
                                        <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
                        </form>

                        <div class="text-center">
                            <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const eyeOpen = this.querySelector('.eye-open');
            const eyeClosed = this.querySelector('.eye-closed');
            if (password.type === 'password') {
                password.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = '';
            } else {
                password.type = 'password';
                eyeOpen.style.display = '';
                eyeClosed.style.display = 'none';
            }
        });
    </script>
</body>
</html>
