<?php
require_once 'config.php';
require_recruiter_login();
$recruiter = current_recruiter();
$company = fetch_one('SELECT * FROM companies WHERE id = ?', [$recruiter['company_id']]);
$all_skills = fetch_all('SELECT * FROM skills ORDER BY name ASC');

$job_id = (int)($_GET['job_id'] ?? 0);
if ($job_id <= 0) { header('Location: recruiter_jobs.php'); exit; }

$job = fetch_one('SELECT * FROM jobs WHERE id = ? AND company_id = ?', [$job_id, $company['id']]);
if (!$job) { header('Location: recruiter_jobs.php'); exit; }

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
                'UPDATE jobs SET title = ?, description = ?, location = ?, salary = ? WHERE id = ? AND company_id = ?',
                [$title, $description, $location, $salary, $job_id, $company['id']]
            );
            execute('DELETE FROM job_skills WHERE job_id = ?', [$job_id]);
            foreach ($skills as $skill_name) {
                $skill = fetch_one('SELECT id FROM skills WHERE name = ?', [$skill_name]);
                if ($skill) {
                    execute('INSERT INTO job_skills (job_id, skill_id) VALUES (?, ?)', [$job_id, $skill['id']]);
                }
            }
            commit_transaction();
            $job = fetch_one('SELECT * FROM jobs WHERE id = ?', [$job_id]);
            $message = '<div class="alert alert-success">Job updated successfully!</div>';
        } catch (Exception $e) {
            rollback_transaction();
            $message = '<div class="alert alert-danger">Failed to update the job. Please try again.</div>';
        }
    }
}

$job_skills = fetch_all('SELECT s.name FROM skills s JOIN job_skills jk ON s.id = jk.skill_id WHERE jk.job_id = ? ORDER BY s.name ASC', [$job_id]);
$job_skill_names = array_map(fn($s) => $s['name'], $job_skills);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job - Recruiter Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'recruiter_navbar.php'; ?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Edit Job</h4></div>
                <div class="card-body">
                    <?= $message ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?= sanitize($job['title']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" required><?= sanitize($job['description']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" value="<?= sanitize($job['location'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Monthly Salary (PKR)</label>
                                <input type="number" name="salary" class="form-control" value="<?= (int)($job['salary'] ?? 0) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Required Skills</label>
                            <div class="row">
                                <?php foreach ($all_skills as $skill): ?>
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="skills[]"
                                                value="<?= sanitize($skill['name']) ?>"
                                                id="sk_<?= sanitize($skill['id']) ?>"
                                                <?= in_array($skill['name'], $job_skill_names, true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="sk_<?= sanitize($skill['id']) ?>"><?= sanitize($skill['name']) ?></label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button class="btn btn-primary">Save Changes</button>
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
