<?php
require_once 'config.php';

// Redirect if already logged in as recruiter
if (isset($_SESSION['recruiter'])) {
    header('Location: recruiter_dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Username and password are required.';
        } else {
            $recruiter = fetch_one(
                'SELECT r.*, u.id AS user_id, u.username, u.password FROM recruiters r JOIN users u ON r.user_id = u.id WHERE u.username = ? AND u.role = ?',
                [$username, 'recruiter']
            );

            if ($recruiter && hash('sha256', $password) === $recruiter['password']) {
                $_SESSION['recruiter'] = $recruiter;
                unset($_SESSION['recruiter']['password']);
                header('Location: recruiter_dashboard.php');
                exit;
            }
            $error = 'Invalid username or password.';
        }
    } elseif ($_POST['action'] === 'register') {
        $username      = sanitize($_POST['username']      ?? '');
        $password      = $_POST['password']               ?? '';
        $full_name     = sanitize($_POST['full_name']     ?? '');
        $email         = sanitize($_POST['email']         ?? '');
        $phone         = sanitize($_POST['phone']         ?? '');
        $company_name  = sanitize($_POST['company_name']  ?? '');
        $company_email = sanitize($_POST['company_email'] ?? '');
        $industry      = sanitize($_POST['industry']      ?? '');
        $website       = sanitize($_POST['website']       ?? '');

        if ($username === '' || $password === '' || $full_name === '' || $email === '' || $company_name === '') {
            $error = 'Username, password, full name, email, and company name are required.';
        } elseif (fetch_one('SELECT id FROM users WHERE username = ?', [$username])) {
            $error = 'Username already exists.';
        } else {
            try {
                begin_transaction();

                execute(
                    'INSERT INTO companies (name, email, industry, website, description, created_at) VALUES (?, ?, ?, ?, ?, ?)',
                    [$company_name, $company_email, $industry, $website, '', date('Y-m-d H:i:s')]
                );
                $company_id = last_insert_id();

                execute(
                    'INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, ?)',
                    [$username, hash('sha256', $password), 'recruiter', date('Y-m-d H:i:s')]
                );
                $user_id = last_insert_id();

                execute(
                    'INSERT INTO recruiters (user_id, company_id, full_name, email, phone, created_at) VALUES (?, ?, ?, ?, ?, ?)',
                    [$user_id, $company_id, $full_name, $email, $phone, date('Y-m-d H:i:s')]
                );

                commit_transaction();
                $success = 'Registration successful! Please login.';
            } catch (Exception $e) {
                rollback_transaction();
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Portal - Login / Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center mb-1">Job Portal</h3>
                    <p class="text-center text-muted mb-4">Recruiter / Company Portal</p>

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

                        <!-- LOGIN TAB -->
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
                        </div>

                        <!-- REGISTER TAB -->
                        <div id="register" class="tab-pane fade">
                            <form method="post">
                                <input type="hidden" name="action" value="register">
                                <h6 class="text-muted mb-3">Your Details</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="full_name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="text-muted mb-3">Company Details</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" name="company_name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Company Email</label>
                                        <input type="email" name="company_email" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Industry</label>
                                        <input type="text" name="industry" class="form-control" placeholder="e.g. Technology, Finance">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Website</label>
                                        <input type="text" name="website" class="form-control" placeholder="https://...">
                                    </div>
                                </div>
                                <button class="btn btn-success w-100">Register</button>
                            </form>
                        </div>

                    </div><!-- end tab-content -->
                    <hr>
                    <p class="text-center text-muted small mb-0">Are you an applicant? <a href="applicant_index.php">Go to Applicant Login</a></p>
                    <p class="text-center text-muted small">Admin? <a href="admin_index.php">Go to Admin Login</a> • <a href="login.php">Unified Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
