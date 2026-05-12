<?php
require_once 'config.php';
require_admin_login();

$message = '';
$error = '';
$edit_company = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_company') {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $industry = sanitize($_POST['industry'] ?? '');
        $website = sanitize($_POST['website'] ?? '');
        $description = sanitize($_POST['description'] ?? '');

        if ($name === '') {
            $error = 'Company name is required.';
        } else {
            execute('INSERT INTO companies (name, email, industry, website, description, created_at) VALUES (?, ?, ?, ?, ?, ?)', [
                $name,
                $email,
                $industry,
                $website,
                $description,
                date('Y-m-d H:i:s'),
            ]);
            $message = 'Company created successfully.';
        }
    }

    if ($_POST['action'] === 'update_company') {
        $company_id = (int)($_POST['company_id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $industry = sanitize($_POST['industry'] ?? '');
        $website = sanitize($_POST['website'] ?? '');
        $description = sanitize($_POST['description'] ?? '');

        if ($company_id <= 0 || $name === '') {
            $error = 'Company and name are required.';
        } else {
            execute('UPDATE companies SET name = ?, email = ?, industry = ?, website = ?, description = ? WHERE id = ?', [
                $name,
                $email,
                $industry,
                $website,
                $description,
                $company_id,
            ]);
            $message = 'Company updated successfully.';
        }
    }

    if ($_POST['action'] === 'delete_company') {
        $company_id = (int)($_POST['company_id'] ?? 0);
        if ($company_id > 0) {
            execute('DELETE FROM companies WHERE id = ?', [$company_id]);
            $message = 'Company deleted successfully.';
        }
    }
}

if (isset($_GET['edit_company_id'])) {
    $edit_company_id = (int)$_GET['edit_company_id'];
    if ($edit_company_id > 0) {
        $edit_company = fetch_one('SELECT * FROM companies WHERE id = ?', [$edit_company_id]);
    }
}

$companies = fetch_all('SELECT c.id, c.name, c.email, c.industry, c.website, COUNT(DISTINCT j.id) AS total_jobs, COUNT(a.id) AS total_applications FROM companies c LEFT JOIN jobs j ON j.company_id = c.id LEFT JOIN applications a ON a.job_id = j.id GROUP BY c.id ORDER BY c.created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Companies - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'admin_navbar.php'; ?>
<div class="container my-4">
    <div class="row gy-4">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><?= $edit_company ? 'Edit Company' : 'Create Company' ?></div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= sanitize($error) ?></div>
                    <?php endif; ?>
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?= sanitize($message) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="action" value="<?= $edit_company ? 'update_company' : 'create_company' ?>">
                        <?php if ($edit_company): ?>
                            <input type="hidden" name="company_id" value="<?= sanitize($edit_company['id']) ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="name" class="form-control" value="<?= sanitize($edit_company['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= sanitize($edit_company['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Industry</label>
                            <input type="text" name="industry" class="form-control" value="<?= sanitize($edit_company['industry'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="text" name="website" class="form-control" value="<?= sanitize($edit_company['website'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"><?= sanitize($edit_company['description'] ?? '') ?></textarea>
                        </div>
                        <button class="btn btn-<?= $edit_company ? 'primary' : 'success' ?> w-100"><?= $edit_company ? 'Update Company' : 'Create Company' ?></button>
                        <?php if ($edit_company): ?>
                            <a href="admin_companies.php" class="btn btn-secondary w-100 mt-2">Create New Company</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Company List</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Industry</th>
                                    <th>Jobs</th>
                                    <th>Applications</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($companies as $company): ?>
                                    <tr>
                                        <td><?= sanitize($company['name']) ?></td>
                                        <td><?= sanitize($company['industry'] ?? '-') ?></td>
                                        <td><?= sanitize($company['total_jobs']) ?></td>
                                        <td><?= sanitize($company['total_applications']) ?></td>
                                        <td>
                                            <a href="admin_companies.php?edit_company_id=<?= sanitize($company['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form method="post" class="d-inline-flex gap-1 align-items-center">
                                                <input type="hidden" name="action" value="delete_company">
                                                <input type="hidden" name="company_id" value="<?= sanitize($company['id']) ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this company?');">Delete</button>
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
