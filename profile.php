<?php
require_once 'config.php';
require_login();
$user = current_user();
$applicant = fetch_one('SELECT * FROM applicants WHERE user_id = ?', [$user['id']]);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $headline = sanitize($_POST['headline'] ?? '');
    $bio = sanitize($_POST['bio'] ?? '');
    $cv_file = $applicant['cv_file'] ?? '';

    if ($full_name === '' || $email === '') {
        $message = '<div class="alert alert-danger">Name and email are required.</div>';
    } else {
        // Handle CV upload
        if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
            $allowed_ext = ['pdf', 'doc', 'docx'];
            $file_info = pathinfo($_FILES['cv_file']['name']);
            $file_ext = strtolower($file_info['extension']);

            if (in_array($file_ext, $allowed_ext) && $_FILES['cv_file']['size'] <= 5 * 1024 * 1024) {
                if (!is_dir('uploads')) mkdir('uploads', 0755, true);
                $cv_file = 'uploads/cv_' . $applicant['id'] . '_' . time() . '.' . $file_ext;
                move_uploaded_file($_FILES['cv_file']['tmp_name'], $cv_file);
            } else {
                $message = '<div class="alert alert-danger">Invalid CV file. Only PDF and Word documents (max 5MB) are allowed.</div>';
            }
        }

        if ($message === '') {
            execute(
                'UPDATE applicants SET full_name = ?, email = ?, phone = ?, headline = ?, bio = ?, cv_file = ? WHERE id = ?',
                [$full_name, $email, $phone, $headline, $bio, $cv_file, $applicant['id']]
            );
            $applicant = fetch_one('SELECT * FROM applicants WHERE id = ?', [$applicant['id']]);
            $message = '<div class="alert alert-success">Profile updated successfully!</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Job Portal</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Profile</h4>
                </div>
                <div class="card-body">
                    <?= $message ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= sanitize($applicant['full_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= sanitize($applicant['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= sanitize($applicant['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Headline (Job Title)</label>
                            <input type="text" name="headline" class="form-control" placeholder="e.g., Software Engineer" value="<?= sanitize($applicant['headline'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control" rows="5" placeholder="Tell us about yourself..."><?= sanitize($applicant['bio'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload CV</label>
                            <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx">
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX (max 5MB)</small>
                            <?php if (!empty($applicant['cv_file']) && file_exists($applicant['cv_file'])): ?>
                                <div class="mt-2">
                                    <small>Current CV: <a href="<?= sanitize($applicant['cv_file']) ?>" target="_blank" class="btn-link">Download</a></small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-primary">Save Changes</button>
                        <a href="skills.php" class="btn btn-secondary">Manage Skills</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
