<?php
require_once 'config.php';
require_recruiter_login();
$recruiter = current_recruiter();
$company   = mongo_find_one('companies', ['_id' => mongo_object_id($recruiter['company_id'])]);

$job_id = sanitize($_GET['job_id'] ?? '');
if ($job_id === '') { header('Location: recruiter_jobs.php'); exit; }

$job = mongo_find_one('jobs', ['_id' => mongo_object_id($job_id), 'company_id' => mongo_object_id($company['_id'])]);
if (!$job) { header('Location: recruiter_jobs.php'); exit; }

$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $app_id    = sanitize($_POST['app_id']  ?? '');
    $new_status = sanitize($_POST['status'] ?? '');
    $allowed   = ['pending', 'shortlisted', 'accepted', 'rejected'];

    if ($app_id !== '' && in_array($new_status, $allowed, true)) {
        mongo_update_one('applications',
            ['_id' => mongo_object_id($app_id)],
            ['$set' => ['status' => $new_status]]
        );
        $message = '<div class="alert alert-success">Application status updated to <strong>' . ucfirst($new_status) . '</strong>.</div>';
    }
}

// Fetch applications with applicant details via aggregation
$applications = mongo_aggregate('applications', [
    ['$match' => ['job_id' => mongo_object_id($job_id)]],
    ['$lookup' => [
        'from'         => 'applicants',
        'localField'   => 'applicant_id',
        'foreignField' => '_id',
        'as'           => 'applicant',
    ]],
    ['$unwind' => '$applicant'],
    ['$sort'   => ['applied_at' => -1]],
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicants - <?= sanitize($job['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'recruiter_navbar.php'; ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4>Applicants for: <?= sanitize($job['title']) ?></h4>
            <p class="text-muted mb-0"><?= sanitize($job['location'] ?? 'Remote') ?> &bull; PKR <?= number_format($job['salary'] ?? 0) ?>/mo</p>
        </div>
        <a href="recruiter_jobs.php" class="btn btn-secondary">← Back to Jobs</a>
    </div>

    <?= $message ?>

    <?php if (count($applications) === 0): ?>
        <div class="alert alert-info">No applications received yet for this job.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Applicant</th>
                                <th>Contact</th>
                                <th>Skills</th>
                                <th>Applied</th>
                                <th>Status</th>
                                <th>Cover Letter</th>
                                <th>Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($applications as $app):
                            $status      = $app['status'] ?? 'pending';
                            $badge_class = match($status) {
                                'pending'     => 'bg-warning text-dark',
                                'shortlisted' => 'bg-info',
                                'accepted'    => 'bg-success',
                                'rejected'    => 'bg-danger',
                                default       => 'bg-secondary',
                            };
                            $applicant = $app['applicant'];
                            $app_id_str = (string)$app['_id'];
                        ?>
                            <tr>
                                <td>
                                    <strong><?= sanitize($applicant['full_name'] ?? 'N/A') ?></strong><br>
                                    <small class="text-muted"><?= sanitize($applicant['headline'] ?? '') ?></small>
                                </td>
                                <td>
                                    <?= sanitize($applicant['email'] ?? '') ?><br>
                                    <small><?= sanitize($applicant['phone'] ?? '') ?></small>
                                </td>
                                <td>
                                    <?php foreach ($applicant['skills'] ?? [] as $s): ?>
                                        <span class="badge bg-secondary"><?= sanitize($s) ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td class="small"><?= sanitize($app['applied_at'] ?? '') ?></td>
                                <td><span class="badge <?= $badge_class ?>"><?= sanitize(ucfirst($status)) ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#letter_<?= $app_id_str ?>">
                                        View
                                    </button>
                                </td>
                                <td>
                                    <form method="post" class="d-flex gap-1">
                                        <input type="hidden" name="action"  value="update_status">
                                        <input type="hidden" name="app_id" value="<?= $app_id_str ?>">
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="pending"     <?= $status==='pending'     ? 'selected':'' ?>>Pending</option>
                                            <option value="shortlisted" <?= $status==='shortlisted' ? 'selected':'' ?>>Shortlisted</option>
                                            <option value="accepted"    <?= $status==='accepted'    ? 'selected':'' ?>>Accepted</option>
                                            <option value="rejected"    <?= $status==='rejected'    ? 'selected':'' ?>>Rejected</option>
                                        </select>
                                        <button class="btn btn-sm btn-primary">Save</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Cover letter modal -->
                            <div class="modal fade" id="letter_<?= $app_id_str ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Cover Letter — <?= sanitize($applicant['full_name'] ?? '') ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><?= nl2br(sanitize($app['cover_letter'] ?? '')) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
