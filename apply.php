<?php
require_once 'config.php';
require_login();
$user = current_user();
$applicant = mongo_find_one('applicants', ['user_id' => mongo_object_id($user['_id'])]);

$job_id = sanitize($_GET['job_id'] ?? '');
if ($job_id === '') {
    header('Location: jobs.php');
    exit;
}

$job = mongo_find_one('jobs', ['_id' => mongo_object_id($job_id)]);
if (!$job) {
    header('Location: jobs.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cover_letter = sanitize($_POST['cover_letter'] ?? '');
    
    if ($cover_letter === '') {
        $message = '<div class="alert alert-danger">Cover letter is required.</div>';
    } else {
        $existing = mongo_find_one('applications', [
            'applicant_id' => mongo_object_id($applicant['_id']),
            'job_id' => mongo_object_id($job_id)
        ]);
        
        if ($existing) {
            $message = '<div class="alert alert-warning">You have already applied for this job.</div>';
        } else {
            mongo_insert_one('applications', [
                'applicant_id' => mongo_object_id($applicant['_id']),
                'job_id' => mongo_object_id($job_id),
                'status' => 'pending',
                'cover_letter' => $cover_letter,
                'applied_at' => date('Y-m-d H:i:s'),
            ]);
            $message = '<div class="alert alert-success">Application submitted successfully!</div>';
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
                        <p><strong>Location:</strong> <?= sanitize($job['location'] ?? 'Remote') ?></p>
                        <p><strong>Salary:</strong> PKR <?= number_format($job['salary'] ?? 0) ?></p>
                        <p><strong>Description:</strong> <?= sanitize($job['description']) ?></p>
                        <p><strong>Required Skills:</strong> 
                            <?php foreach ($job['skills'] ?? [] as $skill): ?>
                                <span class="badge bg-secondary"><?= sanitize($skill) ?></span>
                            <?php endforeach; ?>
                        </p>
                    </div>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Cover Letter</label>
                            <textarea name="cover_letter" class="form-control" rows="6" placeholder="Tell the recruiter why you're a great fit for this role..." required></textarea>
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
