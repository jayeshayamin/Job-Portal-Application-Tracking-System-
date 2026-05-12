<?php
require_once 'config.php';
require_recruiter_login();
$recruiter = current_recruiter();
$company = fetch_one('SELECT * FROM companies WHERE id = ?', [$recruiter['company_id']]);

$total_jobs = fetch_one('SELECT COUNT(*) AS count FROM jobs WHERE company_id = ?', [$company['id']])['count'];
$total_applications = fetch_one(
    'SELECT COUNT(*) AS count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.company_id = ?',
    [$company['id']]
)['count'];
$pending_count = fetch_one(
    'SELECT COUNT(*) AS count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.company_id = ? AND a.status = ?',
    [$company['id'], 'pending']
)['count'];
$accepted_count = fetch_one(
    'SELECT COUNT(*) AS count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.company_id = ? AND a.status = ?',
    [$company['id'], 'accepted']
)['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Dashboard - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'recruiter_navbar.php'; ?>

<div class="container my-4">

    <!-- Welcome banner -->
    <div class="alert alert-primary d-flex align-items-center mb-4">
        <div>
            <h5 class="mb-0">Welcome, <?= sanitize($recruiter['full_name']) ?>!</h5>
            <small class="text-muted"><?= sanitize($company['name']) ?> &mdash; <?= sanitize($company['industry'] ?? '') ?></small>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary text-center">
                <div class="card-body">
                    <h2 class="display-5"><?= sanitize($total_jobs) ?></h2>
                    <p class="mb-0">Jobs Posted</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info text-center">
                <div class="card-body">
                    <h2 class="display-5"><?= sanitize($total_applications) ?></h2>
                    <p class="mb-0">Total Applications</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning text-center">
                <div class="card-body">
                    <h2 class="display-5"><?= sanitize($pending_count) ?></h2>
                    <p class="mb-0">Pending Review</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success text-center">
                <div class="card-body">
                    <h2 class="display-5"><?= sanitize($accepted_count) ?></h2>
                    <p class="mb-0">Accepted</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick action buttons -->
    <div class="row g-3">
        <div class="col-md-4">
            <a href="recruiter_post_job.php" class="card text-decoration-none text-dark">
                <div class="card-body text-center py-4">
                    <h3>➕</h3>
                    <h5>Post a Job</h5>
                    <p class="text-muted small">Create a new job listing</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="recruiter_jobs.php" class="card text-decoration-none text-dark">
                <div class="card-body text-center py-4">
                    <h3>📋</h3>
                    <h5>My Job Listings</h5>
                    <p class="text-muted small">Edit or delete your jobs</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="recruiter_company.php" class="card text-decoration-none text-dark">
                <div class="card-body text-center py-4">
                    <h3>🏢</h3>
                    <h5>Company Profile</h5>
                    <p class="text-muted small">Update company info</p>
                </div>
            </a>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
