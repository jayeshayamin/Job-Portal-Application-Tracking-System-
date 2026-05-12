<?php
require_once 'config.php';
require_recruiter_login();
$recruiter = current_recruiter();
$company = fetch_one('SELECT * FROM companies WHERE id = ?', [$recruiter['company_id']]);
$all_skills = fetch_all('SELECT * FROM skills ORDER BY name ASC');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = sanitize($_POST['title']       ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $location    = sanitize($_POST['location']    ?? '');
    $salary      = (int)($_POST['salary']         ?? 0);
    $skills      = $_POST['skills']               ?? [];
    $skills      = array_map('sanitize', $skills);

    if ($title === '' || $description === '') {
        $message = '<div class="alert alert-danger">Title and description are required.</div>';
    } else {
        try {
            begin_transaction();
            execute(
                'INSERT INTO jobs (company_id, recruiter_id, title, description, location, salary, posted_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$company['id'], $recruiter['id'], $title, $description, $location, $salary, date('Y-m-d H:i:s')]
            );
            $job_id = last_insert_id();

            foreach ($skills as $skill_name) {
                $skill = fetch_one('SELECT id FROM skills WHERE name = ?', [$skill_name]);
                if ($skill) {
                    execute('INSERT INTO job_skills (job_id, skill_id) VALUES (?, ?)', [$job_id, $skill['id']]);
                }
            }

            commit_transaction();
            $message = '<div class="alert alert-success">Job posted successfully! <a href="recruiter_jobs.php">View all jobs</a></div>';
        } catch (Exception $e) {
            rollback_transaction();
            $message = '<div class="alert alert-danger">Failed to post the job. Please try again.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job - Recruiter Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'recruiter_navbar.php'; ?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Post a New Job</h4></div>
                <div class="card-body">
                    <?= $message ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. PHP Developer" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Describe the role, responsibilities, requirements..." required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Karachi, Remote">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Monthly Salary (PKR)</label>
                                <input type="number" name="salary" class="form-control" placeholder="e.g. 80000" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Required Skills</label>
                            <div class="row">
                                <?php foreach ($all_skills as $skill): ?>
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skills[]" value="<?= sanitize($skill['name']) ?>" id="skill_<?= sanitize($skill['id']) ?>">
                                            <label class="form-check-label" for="skill_<?= sanitize($skill['id']) ?>"><?= sanitize($skill['name']) ?></label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button class="btn btn-success">Post Job</button>
                        <a href="recruiter_jobs.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
