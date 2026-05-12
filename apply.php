<?php
require_once 'config.php';
require_login();
$user = current_user();
$applicant = fetch_one('SELECT * FROM applicants WHERE user_id = ?', [$user['id']]);

$job_id = (int)($_GET['job_id'] ?? 0);
if ($job_id <= 0) {
    header('Location: jobs.php');
    exit;
}

$job = fetch_one(
    'SELECT j.*, c.name AS company_name FROM jobs j JOIN companies c ON j.company_id = c.id WHERE j.id = ?',
    [$job_id]
);
if (!$job) {
    header('Location: jobs.php');
    exit;
}

$job_skills = fetch_all(
    'SELECT s.name FROM skills s JOIN job_skills jk ON s.id = jk.skill_id WHERE jk.job_id = ?',
    [$job_id]
);

$message = '';
$cv_file = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cover_letter = sanitize($_POST['cover_letter'] ?? '');

    if ($cover_letter === '') {
        $message = '<div class="alert alert-danger">Cover letter is required.</div>';
    } else {
        $existing = fetch_one(
            'SELECT id FROM applications WHERE applicant_id = ? AND job_id = ?',
            [$applicant['id'], $job_id]
        );

        if ($existing) {
            $message = '<div class="alert alert-warning">You have already applied for this job.</div>';
        } else {
            // Handle CV upload
            if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                $allowed_ext = ['pdf', 'doc', 'docx'];
                $file_info = pathinfo($_FILES['cv_file']['name']);
                $file_ext = strtolower($file_info['extension']);
                $file_type = mime_content_type($_FILES['cv_file']['tmp_name']);

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
                    'INSERT INTO applications (applicant_id, job_id, status, cover_letter, cv_file, applied_at) VALUES (?, ?, ?, ?, ?, ?)',
                    [$applicant['id'], $job_id, 'pending', $cover_letter, $cv_file, date('Y-m-d H:i:s')]
                );
                $message = '<div class="alert alert-success">Application submitted successfully!</div>';
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
    <title>Apply for Job - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Job Portal</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="jobs.php">Back to Jobs</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Apply for: <?= sanitize($job['title']) ?></h4>
                </div>
                <div class="card-body">
                    <?= $message ?>

                    <div class="alert alert-info mb-4">
                        <strong>Job Details</strong>
                        <p><strong>Company:</strong> <?= sanitize($job['company_name']) ?></p>
                        <p><strong>Location:</strong> <?= sanitize($job['location'] ?? 'Remote') ?></p>
                        <p><strong>Salary:</strong> PKR <?= number_format($job['salary'] ?? 0) ?></p>
                        <p><strong>Description:</strong> <?= sanitize($job['description']) ?></p>
                        <p><strong>Required Skills:</strong>
                            <?php foreach ($job_skills as $skill): ?>
                                <span class="badge bg-secondary"><?= sanitize($skill['name']) ?></span>
                            <?php endforeach; ?>
                        </p>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Cover Letter</label>
                            <textarea name="cover_letter" class="form-control" rows="6" placeholder="Tell the recruiter why you're a great fit for this role..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload CV (Optional)</label>
                            <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx">
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX (max 5MB)</small>
                        </div>
                        <button class="btn btn-success">Submit Application</button>
                        <a href="jobs.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
