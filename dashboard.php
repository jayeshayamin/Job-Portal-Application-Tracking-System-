<?php
require_once 'config.php';
require_login();
$user = current_user();
$applicant = mongo_find_one('applicants', ['user_id' => mongo_object_id($user['_id'])]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Job Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="jobs.php">Search Jobs</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">My Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="history.php">Application History</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5>Welcome!</h5>
                    <p class="display-6"><?= sanitize($applicant['full_name'] ?? 'Applicant') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <a href="jobs.php" class="card text-white bg-success text-decoration-none">
                <div class="card-body">
                    <h5>Search Jobs</h5>
                    <p class="display-6">→</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="profile.php" class="card text-white bg-warning text-decoration-none">
                <div class="card-body">
                    <h5>Edit Profile</h5>
                    <p class="display-6">→</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="history.php" class="card text-white bg-danger text-decoration-none">
                <div class="card-body">
                    <h5>Applications</h5>
                    <p class="display-6">→</p>
                </div>
            </a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">Quick Actions</div>
        <div class="card-body">
            <p>Start by updating your profile and adding your skills, then search for jobs that match your qualifications.</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
