<?php
require_once 'config.php';
require_login();
$user = current_user();
$applicant = fetch_one('SELECT * FROM applicants WHERE user_id = ?', [$user['id']]);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $skill_name = trim($_POST['skill_name'] ?? '');
        if ($skill_name === '') {
            $message = '<div class="alert alert-danger">Skill name is required.</div>';
        } else {
            $skill = fetch_one('SELECT id FROM skills WHERE name = ?', [$skill_name]);
            if (!$skill) {
                execute('INSERT INTO skills (name) VALUES (?)', [$skill_name]);
                $skill = ['id' => last_insert_id()];
            }

            $existing = fetch_one(
                'SELECT 1 FROM applicant_skills WHERE applicant_id = ? AND skill_id = ?',
                [$applicant['id'], $skill['id']]
            );

            if ($existing) {
                $message = '<div class="alert alert-warning">You already have this skill.</div>';
            } else {
                execute(
                    'INSERT INTO applicant_skills (applicant_id, skill_id) VALUES (?, ?)',
                    [$applicant['id'], $skill['id']]
                );
                $message = '<div class="alert alert-success">Skill added!</div>';
            }
        }
    } elseif ($action === 'remove') {
        $skill_name = trim($_POST['skill_name'] ?? '');
        $skill = fetch_one('SELECT id FROM skills WHERE name = ?', [$skill_name]);
        if ($skill) {
            execute(
                'DELETE FROM applicant_skills WHERE applicant_id = ? AND skill_id = ?',
                [$applicant['id'], $skill['id']]
            );
            $message = '<div class="alert alert-success">Skill removed!</div>';
        }
    }
}

$all_skills = fetch_all('SELECT * FROM skills ORDER BY name ASC');
$user_skills = fetch_all(
    'SELECT s.name FROM skills s JOIN applicant_skills ak ON s.id = ak.skill_id WHERE ak.applicant_id = ? ORDER BY s.name ASC',
    [$applicant['id']]
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Job Portal</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Manage Your Skills</h4>
                </div>
                <div class="card-body">
                    <?= $message ?>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Add a Skill</h5>
                            <form method="post">
                                <input type="hidden" name="action" value="add">
                                <div class="mb-3">
                                    <label class="form-label">Skill Name</label>
                                    <input type="text" name="skill_name" class="form-control" placeholder="e.g., PHP, JavaScript, SQL" required>
                                </div>
                                <button class="btn btn-success">Add Skill</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h5>Your Skills</h5>
                            <div class="skill-badges">
                                <?php if (count($user_skills) === 0): ?>
                                    <p class="text-muted">No skills added yet.</p>
                                <?php else: ?>
                                    <?php foreach ($user_skills as $skill): ?>
                                        <div class="badge bg-primary mb-2" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            <?= sanitize($skill['name']) ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="skill_name" value="<?= sanitize($skill['name']) ?>">
                                                <button type="submit" class="btn-close btn-close-white" aria-label="Close" style="margin-left: 0.5rem;"></button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <a href="profile.php" class="btn btn-secondary">Back to Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
