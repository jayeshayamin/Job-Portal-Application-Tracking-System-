<?php
require_once 'config.php';
require_admin_login();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_user') {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'applicant';
        $full_name = sanitize($_POST['full_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $company_name = sanitize($_POST['company_name'] ?? '');
        $company_email = sanitize($_POST['company_email'] ?? '');
        $industry = sanitize($_POST['industry'] ?? '');
        $website = sanitize($_POST['website'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Username and password are required.';
        } elseif (fetch_one('SELECT id FROM users WHERE username = ?', [$username])) {
            $error = 'Username already exists.';
        } else {
            try {
                begin_transaction();
                execute('INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, ?)', [
                    $username,
                    hash('sha256', $password),
                    $role,
                    date('Y-m-d H:i:s'),
                ]);
                $user_id = last_insert_id();

                if ($role === 'applicant') {
                    if ($full_name === '' || $email === '') {
                        throw new Exception('Applicant name and email are required.');
                    }
                    execute('INSERT INTO applicants (user_id, full_name, email, phone, headline, bio, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', [
                        $user_id,
                        $full_name,
                        $email,
                        $phone,
                        '',
                        '',
                        date('Y-m-d H:i:s'),
                    ]);
                } elseif ($role === 'recruiter') {
                    if ($full_name === '' || $email === '' || $company_name === '') {
                        throw new Exception('Recruiter name, email, and company name are required.');
                    }
                    execute('INSERT INTO companies (name, email, industry, website, description, created_at) VALUES (?, ?, ?, ?, ?, ?)', [
                        $company_name,
                        $company_email,
                        $industry,
                        $website,
                        '',
                        date('Y-m-d H:i:s'),
                    ]);
                    $company_id = last_insert_id();
                    execute('INSERT INTO recruiters (user_id, company_id, full_name, email, phone, created_at) VALUES (?, ?, ?, ?, ?, ?)', [
                        $user_id,
                        $company_id,
                        $full_name,
                        $email,
                        $phone,
                        date('Y-m-d H:i:s'),
                    ]);
                }

                commit_transaction();
                $message = 'User created successfully.';
            } catch (Exception $e) {
                rollback_transaction();
                $error = 'Failed to create user: ' . sanitize($e->getMessage());
            }
        }
    }

    if ($_POST['action'] === 'update_user') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $username = sanitize($_POST['username'] ?? '');
        if ($user_id <= 0 || $username === '') {
            $error = 'Valid user and username are required.';
        } else {
            execute('UPDATE users SET username = ? WHERE id = ?', [$username, $user_id]);
            $message = 'User updated successfully.';
        }
    }

    if ($_POST['action'] === 'delete_user') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        if ($user_id > 0) {
            execute('DELETE FROM users WHERE id = ?', [$user_id]);
            $message = 'User deleted successfully.';
        }
    }
}

$users = fetch_all(
    'SELECT u.*, a.full_name AS applicant_name, r.full_name AS recruiter_name, c.name AS company_name '
    . 'FROM users u '
    . 'LEFT JOIN applicants a ON a.user_id = u.id '
    . 'LEFT JOIN recruiters r ON r.user_id = u.id '
    . 'LEFT JOIN companies c ON r.company_id = c.id '
    . 'ORDER BY u.created_at DESC'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'admin_navbar.php'; ?>
<div class="container my-4">
    <div class="row gy-4">
        <div class="col-12">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= sanitize($error) ?></div>
            <?php endif; ?>
            <?php if ($message): ?>
                <div class="alert alert-success"><?= sanitize($message) ?></div>
            <?php endif; ?>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Create New User</div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="action" value="create_user">
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="applicant">Applicant</option>
                                <option value="recruiter">Recruiter</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <hr>
                        <h6 class="text-muted">Recruiter Company (optional)</h6>
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company Email</label>
                            <input type="email" name="company_email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Industry</label>
                            <input type="text" name="industry" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="text" name="website" class="form-control">
                        </div>
                        <button class="btn btn-success w-100">Create User</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Users</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Profile</th>
                                    <th>Company</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= sanitize($user['username']) ?></td>
                                        <td><?= sanitize(ucfirst($user['role'])) ?></td>
                                        <td>
                                            <?php if ($user['role'] === 'applicant'): ?>
                                                <?= sanitize($user['applicant_name'] ?? '-') ?>
                                            <?php elseif ($user['role'] === 'recruiter'): ?>
                                                <?= sanitize($user['recruiter_name'] ?? '-') ?>
                                            <?php else: ?>
                                                Admin
                                            <?php endif; ?>
                                        </td>
                                        <td><?= sanitize($user['company_name'] ?? '-') ?></td>
                                        <td><?= sanitize($user['created_at']) ?></td>
                                        <td>
                                            <form method="post" class="d-inline-flex gap-1 align-items-center">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="user_id" value="<?= sanitize($user['id']) ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
