<?php
require_once 'config.php';
require_recruiter_login();
$recruiter = current_recruiter();
$company = fetch_one('SELECT * FROM companies WHERE id = ?', [$recruiter['company_id']]);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $industry = sanitize($_POST['industry'] ?? '');
    $website = sanitize($_POST['website'] ?? '');
    $description = sanitize($_POST['description'] ?? '');

    if ($name === '') {
        $message = '<div class="alert alert-danger">Company name is required.</div>';
    } else {
        execute(
            'UPDATE companies SET name = ?, email = ?, industry = ?, website = ?, description = ? WHERE id = ?',
            [$name, $email, $industry, $website, $description, $company['id']]
        );
        $company = fetch_one('SELECT * FROM companies WHERE id = ?', [$company['id']]);
        $message = '<div class="alert alert-success">Company profile updated successfully.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile - Recruiter Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'recruiter_navbar.php'; ?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Company Profile</h4></div>
                <div class="card-body">
                    <?= $message ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="name" class="form-control" value="<?= sanitize($company['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company Email</label>
                            <input type="email" name="email" class="form-control" value="<?= sanitize($company['email'] ?? '') ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Industry</label>
                                <input type="text" name="industry" class="form-control" value="<?= sanitize($company['industry'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website</label>
                                <input type="text" name="website" class="form-control" value="<?= sanitize($company['website'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="5"><?= sanitize($company['description'] ?? '') ?></textarea>
                        </div>
                        <button class="btn btn-primary">Save Company Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
