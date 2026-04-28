<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Auth.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $db   = new Database();
        $conn = $db->getConnection();
        $auth = new Auth($conn);

        if ($auth->login($username, $password)) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Smart Order Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="login-brand">
            <div class="login-brand-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            <h1>Smart Order System</h1>
            <p>Sign in to access your dashboard</p>
        </div>
        <div class="login-divider"></div>

        <?php if ($error): ?>
            <div class="alert-custom alert-danger mb-3">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label-custom">Username</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-right:0; background:#f8fafc;">
                        <i class="bi bi-person" style="color:#718096;"></i>
                    </span>
                    <input type="text"
                           name="username"
                           class="form-control"
                           placeholder="Enter your username"
                           style="border-left:0;"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label-custom">Password</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-right:0; background:#f8fafc;">
                        <i class="bi bi-lock" style="color:#718096;"></i>
                    </span>
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Enter your password"
                           style="border-left:0;"
                           required>
                </div>
            </div>
            <button type="submit" class="btn-primary-custom w-100 justify-content-center" style="padding:12px;">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>
        </form>

        <p class="text-center mt-4 mb-0" style="font-size:12px; color:#a0aec0;">
            Smart Order Management System &mdash; ITEL 203
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
