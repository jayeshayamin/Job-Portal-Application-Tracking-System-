<?php
require_once 'config.php';
require_login();
$user = current_user();
$applicant = mongo_find_one('applicants', ['user_id' => mongo_object_id($user['_id'])]);

$applications = mongo_aggregate('applications', [
    ['$match' => ['applicant_id' => mongo_object_id($applicant['_id'])]],
    ['$lookup' => ['from' => 'jobs', 'localField' => 'job_id', 'foreignField' => '_id', 'as' => 'job']],
    ['$unwind' => '$job'],
    ['$sort' => ['applied_at' => -1]],
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application History - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Job Portal</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="jobs.php">Search Jobs</a>
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="card">
        <div class="card-header">
            <h4>Your Application History</h4>
        </div>
        <div class="card-body">
            <?php if (count($applications) === 0): ?>
                <div class="alert alert-info">You haven't applied for any jobs yet. <a href="jobs.php">Search jobs now</a></div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Job Title</th>
                                <th>Location</th>
                                <th>Salary</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th>Cover Letter</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?= sanitize($app['job']['title'] ?? '') ?></td>
                                    <td><?= sanitize($app['job']['location'] ?? 'Remote') ?></td>
                                    <td>PKR <?= number_format($app['job']['salary'] ?? 0) ?></td>
                                    <td><?= sanitize($app['applied_at'] ?? '') ?></td>
                                    <td>
                                        <?php
                                        $status = $app['status'] ?? 'pending';
                                        $badge_class = match($status) {
                                            'pending' => 'bg-warning',
                                            'shortlisted' => 'bg-info',
                                            'accepted' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= sanitize(ucfirst($status)) ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#letter<?= $app['_id'] ?>">View</button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="letter<?= $app['_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Cover Letter</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><?= sanitize($app['cover_letter']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
