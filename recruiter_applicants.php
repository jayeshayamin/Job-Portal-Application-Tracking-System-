<?php
require_once 'config.php';
require_recruiter_login();
$recruiter = current_recruiter();
$company = fetch_one('SELECT * FROM companies WHERE id = ?', [$recruiter['company_id']]);

$job_id = (int)($_GET['job_id'] ?? 0);
if ($job_id <= 0) { header('Location: recruiter_jobs.php'); exit; }

$job = fetch_one('SELECT * FROM jobs WHERE id = ? AND company_id = ?', [$job_id, $company['id']]);
if (!$job) { header('Location: recruiter_jobs.php'); exit; }

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $app_id     = (int)($_POST['app_id'] ?? 0);
    $new_status = sanitize($_POST['status'] ?? '');
    $allowed    = ['pending', 'shortlisted', 'accepted', 'rejected'];

    if ($app_id > 0 && in_array($new_status, $allowed, true)) {
        execute('UPDATE applications SET status = ? WHERE id = ? AND job_id = ?', [$new_status, $app_id, $job_id]);
        $message = '<div class="alert alert-success">Application status updated to <strong>' . ucfirst($new_status) . '</strong>.</div>';
    }
}

$applications = fetch_all(
    'SELECT a.*, app.full_name, app.email, app.phone, app.headline, '
    . 'GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ",") AS skills '
    . 'FROM applications a '
    . 'JOIN applicants app ON a.applicant_id = app.id '
    . 'LEFT JOIN applicant_skills ak ON ak.applicant_id = app.id '
    . 'LEFT JOIN skills s ON s.id = ak.skill_id '
    . 'WHERE a.job_id = ? '
    . 'GROUP BY a.id ORDER BY a.applied_at DESC',
    [$job_id]
);
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
        <?php $modalHtml = ''; ?>
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
                            $status = $app['status'] ?? 'pending';
                            if ($status === 'pending') {
                                $badge_class = 'bg-warning text-dark';
                            } elseif ($status === 'shortlisted') {
                                $badge_class = 'bg-info';
                            } elseif ($status === 'accepted') {
                                $badge_class = 'bg-success';
                            } elseif ($status === 'rejected') {
                                $badge_class = 'bg-danger';
                            } else {
                                $badge_class = 'bg-secondary';
                            }
                            $app_id_int = (int)$app['id'];
                            $modalHtml .= '<div class="modal fade" id="letter_' . $app_id_int . '" tabindex="-1" aria-hidden="true">'
                                . '<div class="modal-dialog modal-dialog-scrollable">'
                                . '<div class="modal-content">'
                                . '<div class="modal-header">'
                                . '<h5 class="modal-title">Cover Letter — ' . sanitize($app['full_name'] ?? '') . '</h5>'
                                . '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>'
                                . '</div>'
                                . '<div class="modal-body"><pre class="letter-content">' . sanitize($app['cover_letter'] ?? '') . '</pre></div>'
                                . '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>'
                                . '</div></div></div>';
                        ?>
                            <tr>
                                <td>
                                    <strong><?= sanitize($app['full_name'] ?? 'N/A') ?></strong><br>
                                    <small class="text-muted"><?= sanitize($app['headline'] ?? '') ?></small>
                                </td>
                                <td>
                                    <?= sanitize($app['email'] ?? '') ?><br>
                                    <small><?= sanitize($app['phone'] ?? '') ?></small>
                                </td>
                                <td>
                                    <?php foreach (explode(',', $app['skills'] ?? '') as $s): ?>
                                        <?php if (trim($s) !== ''): ?>
                                            <span class="badge bg-secondary"><?= sanitize($s) ?></span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                                <td class="small"><?= sanitize($app['applied_at'] ?? '') ?></td>
                                <td><span class="badge <?= $badge_class ?>"><?= sanitize(ucfirst($status)) ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#letter_<?= $app_id_int ?>">
                                        View
                                    </button>
                                </td>
                                <td>
                                    <form method="post" class="d-flex gap-1">
                                        <input type="hidden" name="action"  value="update_status">
                                        <input type="hidden" name="app_id" value="<?= $app_id_int ?>">
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
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?= $modalHtml ?>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            body.style.overflow = 'hidden';
        });
        modal.addEventListener('hidden.bs.modal', function() {
            body.style.overflow = 'auto';
        });
    });
});
</script>
</body>
</html>
