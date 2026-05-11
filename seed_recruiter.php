<?php
// seed_recruiter.php — run this ONCE to add sample recruiter/company data
// Usage: .\php.exe "C:\xampp\htdocs\Job-Portal-Application-Tracking-System-\seed_recruiter.php"
require_once 'config.php';

echo "Starting recruiter seed...\n";

// Clear recruiter-side collections only (leave applicant data intact)
mongo_delete_many('companies',  []);
mongo_delete_many('recruiters', []);

echo "✓ Cleared companies and recruiters\n";

// Create sample company
$company_id = mongo_insert_one('companies', [
    'name'        => 'TechPk Solutions',
    'email'       => 'hr@techpk.com',
    'industry'    => 'Technology',
    'website'     => 'https://techpk.com',
    'description' => 'A leading software house based in Karachi.',
    'created_at'  => date('Y-m-d H:i:s'),
]);
echo "✓ Created company: TechPk Solutions\n";

// Create sample recruiter linked to company
mongo_insert_one('recruiters', [
    'username'   => 'recruiter1',
    'password'   => hash('sha256', 'password123'),
    'full_name'  => 'Sara Ahmed',
    'email'      => 'sara@techpk.com',
    'phone'      => '+92 300 9876543',
    'company_id' => mongo_object_id($company_id),
    'role'       => 'recruiter',
    'created_at' => date('Y-m-d H:i:s'),
]);
echo "✓ Created test recruiter (username: recruiter1, password: password123)\n";

// Update existing jobs to link to this company (optional – links the seeded jobs)
$jobs = mongo_find('jobs', []);
foreach ($jobs as $job) {
    mongo_update_one('jobs',
        ['_id' => mongo_object_id($job['_id'])],
        ['$set' => [
            'company_id'   => mongo_object_id($company_id),
            'company_name' => 'TechPk Solutions',
        ]]
    );
}
echo "✓ Linked " . count($jobs) . " existing jobs to TechPk Solutions\n";

// Also link existing applications to the company for dashboard stats
$applications = mongo_find('applications', []);
foreach ($applications as $app) {
    mongo_update_one('applications',
        ['_id' => mongo_object_id($app['_id'])],
        ['$set' => ['company_id' => mongo_object_id($company_id)]]
    );
}
echo "✓ Linked " . count($applications) . " existing applications\n";

echo "\n✅ Recruiter seed completed!\n";
echo "Recruiter Login: recruiter1 / password123\n";
echo "URL: http://localhost/Job-Portal-Application-Tracking-System-/recruiter_index.php\n";
