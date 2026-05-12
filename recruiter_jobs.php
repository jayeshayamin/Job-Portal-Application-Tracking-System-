<?php
require_once 'config.php';
require_recruiter_login();
$recruiter = current_recruiter();
$company = fetch_one('SELECT * FROM companies WHERE id = ?', [$recruiter['company_id']]);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $job_id = (int)($_POST['job_id'] ?? 0);
    if ($job_id > 0) {
        execute('DELETE FROM jobs WHERE id = ? AND company_id = ?', [$job_id, $company['id']]);
        $message = '<div class="alert alert-success">Job deleted successfully.</div>';
    }
}

$jobs = fetch_all(
    'SELECT j.id, j.title, j.description, j.location, j.salary, j.posted_at, '
    . '(SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) AS app_count, '
    . '(SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id AND a.status = ?) AS pending_cnt, '
    . 'GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ",") AS skills '
    . 'FROM jobs j '
    . 'LEFT JOIN job_skills jk ON jk.job_id = j.id '
    . 'LEFT JOIN skills s ON s.id = jk.skill_id '
    . 'WHERE j.company_id = ? '
    . 'GROUP BY j.id ORDER BY j.posted_at DESC',
    ['pending', $company['id']]
);
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
            <?php $job_id_int = (int)$job['id']; ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= sanitize($job['title']) ?></h5>
                        <p class="text-muted small"><?= sanitize($job['location'] ?? 'Remote') ?> &bull; PKR <?= number_format($job['salary'] ?? 0) ?>/mo</p>
                        <p><?= sanitize(substr($job['description'], 0, 100)) ?>...</p>
                        <div class="mb-2">
                            <?php foreach (explode(',', $job['skills'] ?? '') as $s): ?>
                                <?php if (trim($s) !== ''): ?>
                                    <span class="badge bg-secondary"><?= sanitize($s) ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <p class="small text-muted">
                            <strong><?= (int)$job['app_count'] ?></strong> application(s) &bull;
                            <strong><?= (int)$job['pending_cnt'] ?></strong> pending
                        </p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="recruiter_applicants.php?job_id=<?= urlencode($job_id_int) ?>" class="btn btn-primary btn-sm">View Applicants</a>
                        <a href="recruiter_edit_job.php?job_id=<?= urlencode($job_id_int) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form method="post" onsubmit="return confirm('Delete this job and all its applications?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="job_id" value="<?= $job_id_int ?>">
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
