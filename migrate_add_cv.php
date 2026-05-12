<?php
require_once 'config.php';

echo "Migrating database to add CV support...\n";

try {
    // Add cv_file column to applicants table
    $sql1 = "ALTER TABLE applicants ADD COLUMN cv_file VARCHAR(255) DEFAULT NULL";
    db()->exec($sql1);
    echo "✓ Added cv_file column to applicants table\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false) {
        echo "! Error adding cv_file to applicants: " . $e->getMessage() . "\n";
    } else {
        echo "✓ cv_file column already exists in applicants table\n";
    }
}

try {
    // Add cv_file column to applications table
    $sql2 = "ALTER TABLE applications ADD COLUMN cv_file VARCHAR(255) DEFAULT NULL";
    db()->exec($sql2);
    echo "✓ Added cv_file column to applications table\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false) {
        echo "! Error adding cv_file to applications: " . $e->getMessage() . "\n";
    } else {
        echo "✓ cv_file column already exists in applications table\n";
    }
}

// Create uploads directory
if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
    echo "✓ Created uploads directory\n";
} else {
    echo "✓ Uploads directory already exists\n";
}

echo "\n✅ Migration completed successfully!\n";
?>
