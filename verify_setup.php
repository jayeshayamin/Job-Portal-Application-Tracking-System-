<?php
/**
 * Job Portal - Setup Verification Script
 * Run this to verify your installation is correct
 */

session_start();
$checks = [];
$errors = [];
$warnings = [];

// 1. Check PHP version
$checks['PHP Version'] = [
    'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? '✓' : '✗',
    'value' => PHP_VERSION,
    'required' => '7.4+'
];

// 2. Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'json'];
foreach ($required_extensions as $ext) {
    $checks["Extension: $ext"] = [
        'status' => extension_loaded($ext) ? '✓' : '✗',
        'value' => extension_loaded($ext) ? 'Loaded' : 'Not Loaded',
        'required' => 'Required'
    ];
}

// 3. Check file permissions
$files_to_check = [
    'config.php',
    'schema.sql',
    'seed_mysql.php',
    'index.php'
];

foreach ($files_to_check as $file) {
    $exists = file_exists($file);
    $checks["File: $file"] = [
        'status' => $exists ? '✓' : '✗',
        'value' => $exists ? 'Exists' : 'Not Found',
        'required' => 'Required'
    ];
}

// 4. Check directory structure
$dirs_to_check = [
    'Project Directory' => '.'
];

foreach ($dirs_to_check as $name => $dir) {
    $checks[$name] = [
        'status' => is_dir($dir) ? '✓' : '✗',
        'value' => $dir,
        'required' => 'Directory'
    ];
}

// 5. Try to connect to database
try {
    require_once 'config.php';
    $test = fetch_one('SELECT 1 as test');
    $checks['Database Connection'] = [
        'status' => $test ? '✓' : '✗',
        'value' => $test ? 'Connected' : 'Failed',
        'required' => 'Critical'
    ];
    
    // 6. Check if database has tables
    try {
        $tables = fetch_all('SELECT table_name FROM information_schema.tables WHERE table_schema = ?', [DB_NAME]);
        $table_names = array_column($tables, 'table_name');
        
        $required_tables = ['users', 'applicants', 'jobs', 'applications', 'companies'];
        $missing_tables = array_diff($required_tables, $table_names);
        
        if (empty($missing_tables)) {
            $checks['Database Tables'] = [
                'status' => '✓',
                'value' => count($table_names) . ' tables found',
                'required' => 'Critical'
            ];
        } else {
            $checks['Database Tables'] = [
                'status' => '✗',
                'value' => 'Missing: ' . implode(', ', $missing_tables),
                'required' => 'Critical'
            ];
            $errors[] = 'Missing tables! Run schema.sql first.';
        }
        
        // 7. Check sample data
        $user_count = fetch_one('SELECT COUNT(*) as count FROM users');
        $job_count = fetch_one('SELECT COUNT(*) as count FROM jobs');
        
        $checks['Sample Data'] = [
            'status' => ($user_count['count'] > 0 && $job_count['count'] > 0) ? '✓' : '⚠',
            'value' => $user_count['count'] . ' users, ' . $job_count['count'] . ' jobs',
            'required' => 'Recommended'
        ];
        
        if ($user_count['count'] === 0 || $job_count['count'] === 0) {
            $warnings[] = 'No sample data! Run seed_mysql.php to add test users.';
        }
        
    } catch (Exception $db_error) {
        $checks['Database Tables'] = [
            'status' => '✗',
            'value' => 'Cannot access tables',
            'required' => 'Critical'
        ];
        $errors[] = $db_error->getMessage();
    }
    
} catch (Exception $conn_error) {
    $checks['Database Connection'] = [
        'status' => '✗',
        'value' => 'Cannot connect',
        'required' => 'Critical'
    ];
    $errors[] = 'Database connection failed: ' . $conn_error->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Verification - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding: 2rem 0; }
        .container { max-width: 900px; }
        .status-pass { color: #28a745; font-weight: bold; }
        .status-fail { color: #dc3545; font-weight: bold; }
        .status-warn { color: #ffc107; font-weight: bold; }
        .section { margin-bottom: 2rem; }
        .check-row { padding: 1rem; border-bottom: 1px solid #dee2e6; }
        .check-row:last-child { border-bottom: none; }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4">🔍 Job Portal - Setup Verification</h1>
    
    <!-- Summary -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Summary</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <h6>❌ Critical Issues Found:</h6>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($warnings)): ?>
                <div class="alert alert-warning" role="alert">
                    <h6>⚠️ Warnings:</h6>
                    <ul class="mb-0">
                        <?php foreach ($warnings as $warning): ?>
                            <li><?= htmlspecialchars($warning) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (empty($errors) && empty($warnings)): ?>
                <div class="alert alert-success" role="alert">
                    ✅ <strong>All systems operational!</strong> Your Job Portal is ready to use.
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Detailed Checks -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Detailed Checks</h5>
        </div>
        <div class="card-body p-0">
            <?php foreach ($checks as $name => $check): ?>
                <div class="check-row">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0"><?= htmlspecialchars($name) ?></h6>
                        </div>
                        <div class="col-md-3">
                            <span class="<?php
                                if ($check['status'] === '✓') echo 'status-pass';
                                elseif ($check['status'] === '✗') echo 'status-fail';
                                else echo 'status-warn';
                            ?>">
                                <?= htmlspecialchars($check['status']) ?>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">
                                <?= htmlspecialchars($check['value']) ?>
                                <br>
                                <em><?= htmlspecialchars($check['required'] ?? '') ?></em>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Next Steps -->
    <div class="card mt-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">✅ Next Steps</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <p><strong>Please fix the errors above:</strong></p>
                <ol>
                    <li>Ensure MySQL is running</li>
                    <li>Import <code>schema.sql</code> via phpMyAdmin</li>
                    <li>Check database credentials in <code>config.php</code></li>
                    <li>Refresh this page</li>
                </ol>
            <?php else: ?>
                <p><strong>Your setup is complete! You can now:</strong></p>
                <div class="alert alert-info">
                    <p>1. <a href="index.php" class="btn btn-primary btn-sm">Go to Home Page</a></p>
                    <p>2. <a href="login.php" class="btn btn-primary btn-sm">Login (Unified)</a></p>
                    <p>3. <a href="applicant_index.php" class="btn btn-success btn-sm">Applicant Login</a></p>
                    <p>4. <a href="recruiter_index.php" class="btn btn-info btn-sm">Recruiter Login</a></p>
                    <p>5. <a href="admin_index.php" class="btn btn-danger btn-sm">Admin Login</a></p>
                </div>
                
                <?php if (isset($user_count) && $user_count['count'] > 0): ?>
                    <hr>
                    <h6>Default Test Credentials:</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Username</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Admin</td>
                                <td><code>admin</code></td>
                                <td><code>admin123</code></td>
                            </tr>
                            <tr>
                                <td>Applicant</td>
                                <td><code>applicant1</code></td>
                                <td><code>password123</code></td>
                            </tr>
                            <tr>
                                <td>Recruiter</td>
                                <td><code>recruiter1</code></td>
                                <td><code>password123</code></td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="text-center mt-4 text-muted">
        <p>Job Portal Application Tracking System - Verification Page</p>
        <p>Generated: <?= date('Y-m-d H:i:s') ?></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
