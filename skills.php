<?php
require_once 'config.php';
require_login();
$user = current_user();
$applicant = mongo_find_one('applicants', ['user_id' => mongo_object_id($user['_id'])]);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $skill_name = trim($_POST['skill_name'] ?? '');
        if ($skill_name === '') {
            $message = '<div class="alert alert-danger">Skill name is required.</div>';
        } elseif (in_array($skill_name, $applicant['skills'] ?? [], true)) {
            $message = '<div class="alert alert-warning">You already have this skill.</div>';
        } else {
            $skills = $applicant['skills'] ?? [];
            $skills[] = $skill_name;
            mongo_update_one('applicants', ['_id' => mongo_object_id($applicant['_id'])], ['$set' => ['skills' => $skills]]);
            $applicant = mongo_find_one('applicants', ['_id' => mongo_object_id($applicant['_id'])]);
            $message = '<div class="alert alert-success">Skill added!</div>';
        }
    } elseif ($action === 'remove') {
        $skill_name = trim($_POST['skill_name'] ?? '');
        $skills = $applicant['skills'] ?? [];
        $skills = array_filter($skills, fn($s) => $s !== $skill_name);
        $skills = array_values($skills);
        mongo_update_one('applicants', ['_id' => mongo_object_id($applicant['_id'])], ['$set' => ['skills' => $skills]]);
        $applicant = mongo_find_one('applicants', ['_id' => mongo_object_id($applicant['_id'])]);
        $message = '<div class="alert alert-success">Skill removed!</div>';
    }
}

$all_skills = mongo_find('skills', [], ['sort' => ['name' => 1]]);
$user_skills = $applicant['skills'] ?? [];
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
                                    <input type="text" name="skill_name" class="form-control" placeholder="e.g., PHP, JavaScript, MongoDB" required>
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
                                            <?= sanitize($skill) ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="skill_name" value="<?= sanitize($skill) ?>">
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
