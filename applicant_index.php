<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Username and password are required.';
        } else {
            $user = fetch_one('SELECT * FROM users WHERE username = ?', [$username]);
            if ($user && hash('sha256', $password) === $user['password']) {
                if ($user['role'] === 'applicant') {
                    $_SESSION['user'] = $user;
                    header('Location: dashboard.php');
                    exit;
                }
                if ($user['role'] === 'admin') {
                    $_SESSION['admin'] = $user;
                    header('Location: admin_dashboard.php');
                    exit;
                }
            }
            $error = 'Invalid username or password, or use the recruiter login page.';
        }
    } elseif ($_POST['action'] === 'register') {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = sanitize($_POST['full_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');

        if ($username === '' || $password === '' || $full_name === '' || $email === '') {
            $error = 'All fields are required.';
        } elseif (fetch_one('SELECT id FROM users WHERE username = ?', [$username])) {
            $error = 'Username already exists.';
        } else {
            try {
                begin_transaction();
                execute('INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, ?)', [
                    $username,
                    hash('sha256', $password),
                    'applicant',
                    date('Y-m-d H:i:s'),
                ]);
                $userId = last_insert_id();

                execute('INSERT INTO applicants (user_id, full_name, email, phone, headline, bio, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', [
                    $userId,
                    $full_name,
                    $email,
                    $phone,
                    '',
                    '',
                    date('Y-m-d H:i:s'),
                ]);
                commit_transaction();
                $success = 'Registration successful! Please login.';
            } catch (Exception $e) {
                rollback_transaction();
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

if (isset($_SESSION['admin'])) {
    header('Location: admin_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Applicant</title>
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
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= sanitize($error) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= sanitize($success) ?></div>
                    <?php endif; ?>

                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#register">Register</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="login" class="tab-pane fade show active">
                            <form method="post">
                                <input type="hidden" name="action" value="login">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button class="btn btn-primary w-100">Login</button>
                            </form>
                            <p class="text-center mt-3 small text-muted">Recruiter? <a href="recruiter_index.php">Login here</a> • Admin? <a href="admin_index.php">Login here</a> • <a href="login.php">Unified Login</a></p>
                        </div>

                        <div id="register" class="tab-pane fade">
                            <form method="post">
                                <input type="hidden" name="action" value="register">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button class="btn btn-success w-100">Register</button>
                            </form>
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
