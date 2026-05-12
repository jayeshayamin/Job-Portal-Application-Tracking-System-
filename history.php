<?php
require_once 'config.php';
require_login();
$user = current_user();
$applicant = fetch_one('SELECT * FROM applicants WHERE user_id = ?', [$user['id']]);

$applications = fetch_all(
    'SELECT a.*, j.title AS job_title, j.location AS job_location, j.salary AS job_salary '
    . 'FROM applications a '
    . 'JOIN jobs j ON a.job_id = j.id '
    . 'WHERE a.applicant_id = ? '
    . 'ORDER BY a.applied_at DESC',
    [$applicant['id']]
);
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?= sanitize($app['job_title'] ?? '') ?></td>
                                    <td><?= sanitize($app['job_location'] ?? 'Remote') ?></td>
                                    <td>PKR <?= number_format($app['job_salary'] ?? 0) ?></td>
                                    <td><?= sanitize($app['applied_at'] ?? '') ?></td>
                                    <td>
                                        <?php
                                        $status = $app['status'] ?? 'pending';
                                        if ($status === 'pending') {
                                            $badge_class = 'bg-warning';
                                        } elseif ($status === 'shortlisted') {
                                            $badge_class = 'bg-info';
                                        } elseif ($status === 'accepted') {
                                            $badge_class = 'bg-success';
                                        } elseif ($status === 'rejected') {
                                            $badge_class = 'bg-danger';
                                        } else {
                                            $badge_class = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= sanitize(ucfirst($status)) ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#letterModal<?= $app['id'] ?>">View Letter</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modals outside the table to prevent glitching -->
<?php if (count($applications) > 0): ?>
    <?php foreach ($applications as $app): ?>
        <div class="modal fade" id="letterModal<?= $app['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cover Letter - <?= sanitize($app['job_title']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <pre class="letter-content"><?= sanitize($app['cover_letter']) ?></pre>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Prevent modal glitching when mouse is on page
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to all modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        // Prevent pointer events from passing through
        modal.addEventListener('mouseenter', function(e) {
            e.stopPropagation();
        });
        modal.addEventListener('mouseover', function(e) {
            e.stopPropagation();
        });
        modal.addEventListener('mousemove', function(e) {
            e.stopPropagation();
        });
        
        // Ensure modal stays visible
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.style.pointerEvents = 'auto';
        }
    });
    
    // Disable table hover when modal is active
    const viewButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            document.body.style.overflow = 'hidden';
        });
    });
    
    // Re-enable table hover when modal closes
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            document.body.style.overflow = 'auto';
        });
    });
});
</script>
</body>
</html>
