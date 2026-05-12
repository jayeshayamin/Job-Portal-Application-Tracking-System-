<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Welcome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h1 class="card-title mb-4">Welcome to Job Portal</h1>
                    <p class="lead text-muted mb-4">Your comprehensive job application tracking system</p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">For Job Seekers</h5>
                                    <p class="card-text">Find and apply to jobs, track your applications, and manage your profile.</p>
                                    <a href="login.php" class="btn btn-primary">Login as Applicant</a>
                                    <br><small class="text-muted mt-2 d-block">New? <a href="applicant_index.php">Register here</a></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">For Recruiters</h5>
                                    <p class="card-text">Post jobs, review applications, and manage your company profile.</p>
                                    <a href="login.php" class="btn btn-success">Login as Recruiter</a>
                                    <br><small class="text-muted mt-2 d-block">New? <a href="recruiter_index.php">Register here</a></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">For Administrators</h5>
                                    <p class="card-text">Manage users, companies, and oversee the entire platform.</p>
                                    <a href="login.php" class="btn btn-warning">Login as Admin</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <strong>Unified Login:</strong> You can use the same login page for all user types! Just enter your username and password, and you'll be automatically directed to the appropriate dashboard.
                    </div>

                    <hr>
                    <div class="text-center">
                        <a href="login.php" class="btn btn-lg btn-primary">Go to Unified Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>