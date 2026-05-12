<?php
require_once 'config.php';
require_login();
$user = current_user();
$applicant = fetch_one('SELECT * FROM applicants WHERE user_id = ?', [$user['id']]);

$search = sanitize($_GET['search'] ?? '');
$skill_filter = sanitize($_GET['skill'] ?? '');

$params = [];
$sql = 'SELECT j.id, j.title, j.description, j.location, j.salary, j.posted_at, c.name AS company_name, '
    . 'GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ",") AS skills '
    . 'FROM jobs j '
    . 'JOIN companies c ON j.company_id = c.id '
    . 'LEFT JOIN job_skills jk ON jk.job_id = j.id '
    . 'LEFT JOIN skills s ON s.id = jk.skill_id ';

if ($skill_filter !== '') {
    $sql .= 'JOIN job_skills jkf ON jkf.job_id = j.id '
          . 'JOIN skills sf ON sf.id = jkf.skill_id AND sf.name = ? ';
    $params[] = $skill_filter;
}

$where = [];
if ($search !== '') {
    $where[] = '(j.title LIKE ? OR j.description LIKE ? OR j.location LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if (count($where) > 0) {
    $sql .= 'WHERE ' . implode(' AND ', $where) . ' ';
}

$sql .= 'GROUP BY j.id ORDER BY j.posted_at DESC';
$jobs = fetch_all($sql, $params);
$all_skills = fetch_all('SELECT * FROM skills ORDER BY name ASC');

$user_applied_job_ids = fetch_all('SELECT job_id FROM applications WHERE applicant_id = ?', [$applicant['id']]);
$applied_job_ids = array_map(fn($app) => (int)$app['job_id'], $user_applied_job_ids);
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
                                    <?php foreach (explode(',', $job['skills'] ?? '') as $skill): ?>
                                        <?php if (trim($skill) !== ''): ?>
                                            <span class="badge bg-secondary"><?= sanitize($skill) ?></span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php $job_id_int = (int)$job['id']; ?>
                            <?php if (in_array($job_id_int, $applied_job_ids, true)): ?>
                                <button class="btn btn-success" disabled>Already Applied</button>
                            <?php else: ?>
                                <a href="apply.php?job_id=<?= urlencode($job_id_int) ?>" class="btn btn-primary">Apply Now</a>
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
