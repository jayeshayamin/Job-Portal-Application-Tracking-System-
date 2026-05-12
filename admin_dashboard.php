<?php
require_once 'config.php';
require_admin_login();
$summary = fetch_one('SELECT * FROM admin_dashboard_summary');
$company_summary = fetch_all(
    'SELECT company_id, company_name, total_jobs, total_applications FROM company_job_application_summary ORDER BY total_applications DESC LIMIT 5'
);
$recent_applications = fetch_all(
    'SELECT a.id, app.full_name AS applicant_name, j.title AS job_title, c.name AS company_name, a.status, DATE_FORMAT(a.applied_at, "%Y-%m-%d %H:%i:%s") AS applied_at '
    . 'FROM applications a '
    . 'JOIN applicants app ON a.applicant_id = app.id '
    . 'JOIN jobs j ON a.job_id = j.id '
    . 'JOIN companies c ON j.company_id = c.id '
    . 'ORDER BY a.applied_at DESC LIMIT 5'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'admin_navbar.php'; ?>

<div class="container my-4">
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary text-center">
                <div class="card-body">
                    <h3><?= sanitize($summary['total_users'] ?? 0) ?></h3>
                    <p class="mb-0">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success text-center">
                <div class="card-body">
                    <h3><?= sanitize($summary['total_applicants'] ?? 0) ?></h3>
                    <p class="mb-0">Applicants</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info text-center">
                <div class="card-body">
                    <h3><?= sanitize($summary['total_recruiters'] ?? 0) ?></h3>
                    <p class="mb-0">Recruiters</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-secondary text-center">
                <div class="card-body">
                    <h3><?= sanitize($summary['total_admins'] ?? 0) ?></h3>
                    <p class="mb-0">Admins</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-warning text-center">
                <div class="card-body">
                    <h3><?= sanitize(count($company_summary)) ?></h3>
                    <p class="mb-0">Companies</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <a href="admin_users.php" class="card text-decoration-none text-dark">
                <div class="card-body py-4 text-center">
                    <h4>Manage Users</h4>
                    <p class="text-muted small">Create, update, and delete applicants, recruiters, and admins.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="admin_companies.php" class="card text-decoration-none text-dark">
                <div class="card-body py-4 text-center">
                    <h4>Manage Companies</h4>
                    <p class="text-muted small">Create and update company and hiring details.</p>
                </div>
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Top Company Activity</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Jobs</th>
                                    <th>Applications</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($company_summary as $company): ?>
                                    <tr>
                                        <td><?= sanitize($company['company_name']) ?></td>
                                        <td><?= sanitize($company['total_jobs']) ?></td>
                                        <td><?= sanitize($company['total_applications']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Recent Applications</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Applicant</th>
                                    <th>Job</th>
                                    <th>Status</th>
                                    <th>Applied</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_applications as $app): ?>
                                    <tr>
                                        <td><?= sanitize($app['applicant_name']) ?></td>
                                        <td><?= sanitize($app['job_title']) ?></td>
                                        <td><?= sanitize(ucfirst($app['status'])) ?></td>
                                        <td><?= sanitize($app['applied_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
