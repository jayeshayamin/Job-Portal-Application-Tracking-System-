<?php
require_once 'config.php';

$error = '';
$success = '';

// Test database connection
try {
    $test = fetch_one('SELECT 1 as test');
    // Database connection works
} catch (Exception $e) {
    $error = 'Database connection error: ' . $e->getMessage();
}

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}
if (isset($_SESSION['recruiter'])) {
    header('Location: recruiter_dashboard.php');
    exit;
}
if (isset($_SESSION['admin'])) {
    header('Location: admin_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Username and password are required.';
        } else {
            $user = fetch_one('SELECT * FROM users WHERE username = ?', [$username]);
            if ($user && hash('sha256', $password) === $user['password']) {
                switch ($user['role']) {
                    case 'applicant':
                        $_SESSION['user'] = $user;
                        header('Location: dashboard.php');
                        exit;
                    case 'recruiter':
                        $recruiter = fetch_one(
                            'SELECT r.*, u.username FROM recruiters r JOIN users u ON r.user_id = u.id WHERE u.id = ?',
                            [$user['id']]
                        );
                        if ($recruiter) {
                            $_SESSION['recruiter'] = $recruiter;
                            header('Location: recruiter_dashboard.php');
                            exit;
                        } else {
                            $error = 'Recruiter profile not found. Please contact administrator.';
                        }
                        break;
                    case 'admin':
                        $_SESSION['admin'] = $user;
                        header('Location: admin_dashboard.php');
                        exit;
                }
            }
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
    <title>Job Portal - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Job Portal</h3>
                    <p class="text-center text-muted mb-4">Unified Login for All Users</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= sanitize($error) ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= sanitize($success) ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button class="btn btn-primary w-100">Login</button>
                    </form>

                    <hr>
                    <div class="text-center">
                        <p class="mb-2"><strong>New to Job Portal?</strong></p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="applicant_index.php" class="btn btn-outline-primary btn-sm">Register as Applicant</a>
                            <a href="recruiter_index.php" class="btn btn-outline-success btn-sm">Register as Recruiter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>