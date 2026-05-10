<?php
require_once 'config.php';
require_login();
$user = current_user();
$applicant = mongo_find_one('applicants', ['user_id' => mongo_object_id($user['_id'])]);

$search = sanitize($_GET['search'] ?? '');
$skill_filter = sanitize($_GET['skill'] ?? '');

$filter = [];
if ($search !== '') {
    $filter['$or'] = [
        ['title' => ['$regex' => $search, '$options' => 'i']],
        ['description' => ['$regex' => $search, '$options' => 'i']],
        ['location' => ['$regex' => $search, '$options' => 'i']],
    ];
}
if ($skill_filter !== '') {
    $filter['skills'] = ['$in' => [$skill_filter]];
}

$jobs = mongo_find('jobs', $filter, ['sort' => ['posted_at' => -1]]);
$all_skills = mongo_find('skills', [], ['sort' => ['name' => 1]]);
$user_applied_jobs = mongo_find('applications', ['applicant_id' => mongo_object_id($applicant['_id'])], ['projection' => ['job_id' => 1]]);
$applied_job_ids = array_map(fn($app) => (string)$app['job_id'], $user_applied_jobs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Jobs - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Job Portal</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="history.php">Applications</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="card mb-4">
        <div class="card-header">
            <h4>Search & Filter Jobs</h4>
        </div>
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by title, description, or location..." value="<?= sanitize($search) ?>">
                </div>
                <div class="col-md-4">
                    <select name="skill" class="form-select">
                        <option value="">Filter by skill...</option>
                        <?php foreach ($all_skills as $skill): ?>
                            <option value="<?= sanitize($skill['name']) ?>" <?= $skill_filter === $skill['name'] ? 'selected' : '' ?>>
                                <?= sanitize($skill['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (count($jobs) === 0): ?>
        <div class="alert alert-info">No jobs found. Try adjusting your filters.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($jobs as $job): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= sanitize($job['title']) ?></h5>
                            <p class="card-text text-muted"><?= sanitize($job['location'] ?? 'Remote') ?></p>
                            <p class="card-text"><strong>Salary:</strong> PKR <?= number_format($job['salary'] ?? 0) ?></p>
                            <p class="card-text"><?= sanitize(substr($job['description'], 0, 100)) ?>...</p>
                            
                            <div class="mb-3">
                                <strong>Required Skills:</strong>
                                <div>
                                    <?php foreach ($job['skills'] ?? [] as $skill): ?>
                                        <span class="badge bg-secondary"><?= sanitize($skill) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php $job_id_str = (string)$job['_id']; ?>
                            <?php if (in_array($job_id_str, $applied_job_ids, true)): ?>
                                <button class="btn btn-success" disabled>Already Applied</button>
                            <?php else: ?>
                                <a href="apply.php?job_id=<?= urlencode($job_id_str) ?>" class="btn btn-primary">Apply Now</a>
                            <?php endif; ?>
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
