<?php
require_once 'config.php';
require_recruiter_login();
$recruiter = current_recruiter();
$company   = mongo_find_one('companies', ['_id' => mongo_object_id($recruiter['company_id'])]);

$message = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $job_id = sanitize($_POST['job_id'] ?? '');
    if ($job_id !== '') {
        mongo_delete_one('jobs', ['_id' => mongo_object_id($job_id), 'company_id' => mongo_object_id($company['_id'])]);
        // Also delete all applications for this job
        mongo_delete_many('applications', ['job_id' => mongo_object_id($job_id)]);
        $message = '<div class="alert alert-success">Job deleted successfully.</div>';
    }
}

$jobs = mongo_find('jobs', ['company_id' => mongo_object_id($company['_id'])], ['sort' => ['posted_at' => -1]]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Jobs - Recruiter Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'recruiter_navbar.php'; ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>My Job Listings</h4>
        <a href="recruiter_post_job.php" class="btn btn-success">+ Post New Job</a>
    </div>

    <?= $message ?>

    <?php if (count($jobs) === 0): ?>
        <div class="alert alert-info">You haven't posted any jobs yet. <a href="recruiter_post_job.php">Post your first job</a></div>
    <?php else: ?>
        <div class="row">
        <?php foreach ($jobs as $job): ?>
            <?php
                $job_id_str  = (string)$job['_id'];
                $app_count   = mongo_count('applications', ['job_id' => mongo_object_id($job_id_str)]);
                $pending_cnt = mongo_count('applications', ['job_id' => mongo_object_id($job_id_str), 'status' => 'pending']);
            ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= sanitize($job['title']) ?></h5>
                        <p class="text-muted small"><?= sanitize($job['location'] ?? 'Remote') ?> &bull; PKR <?= number_format($job['salary'] ?? 0) ?>/mo</p>
                        <p><?= sanitize(substr($job['description'], 0, 100)) ?>...</p>
                        <div class="mb-2">
                            <?php foreach ($job['skills'] ?? [] as $s): ?>
                                <span class="badge bg-secondary"><?= sanitize($s) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <p class="small text-muted">
                            <strong><?= $app_count ?></strong> application(s) &bull;
                            <strong><?= $pending_cnt ?></strong> pending
                        </p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="recruiter_applicants.php?job_id=<?= urlencode($job_id_str) ?>" class="btn btn-primary btn-sm">View Applicants</a>
                        <a href="recruiter_edit_job.php?job_id=<?= urlencode($job_id_str) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <!-- Delete with confirm -->
                        <form method="post" onsubmit="return confirm('Delete this job and all its applications?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="job_id" value="<?= $job_id_str ?>">
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
