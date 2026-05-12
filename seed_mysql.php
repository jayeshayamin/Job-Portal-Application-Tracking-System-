<?php
require_once 'config.php';

try {
    begin_transaction();

    echo "Seeding SQL database...\n";

    // Create default skills
    $skill_names = ['PHP', 'JavaScript', 'SQL', 'Python', 'MongoDB', 'React', 'Node.js', 'HTML/CSS'];
    foreach ($skill_names as $skill_name) {
        execute('INSERT IGNORE INTO skills (name) VALUES (?)', [$skill_name]);
    }
    echo "✓ Skills inserted\n";

    // Create admin user
    execute('INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, ?)', [
        'admin',
        hash('sha256', 'admin123'),
        'admin',
        date('Y-m-d H:i:s'),
    ]);
    echo "✓ Admin account created\n";

    // Create applicant user and profile
    execute('INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, ?)', [
        'applicant1',
        hash('sha256', 'password123'),
        'applicant',
        date('Y-m-d H:i:s'),
    ]);
    $applicant_user_id = last_insert_id();

    execute('INSERT INTO applicants (user_id, full_name, email, phone, headline, bio, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', [
        $applicant_user_id,
        'John Doe',
        'john@example.com',
        '+92 300 1234567',
        'Software Engineer',
        'Passionate about web development and databases.',
        date('Y-m-d H:i:s'),
    ]);
    $applicant_id = last_insert_id();

    echo "✓ Applicant profile created\n";

    // Create recruiter user, company, and recruiter profile
    execute('INSERT INTO companies (name, email, industry, website, description, created_at) VALUES (?, ?, ?, ?, ?, ?)', [
        'TechPk Solutions',
        'hr@techpk.com',
        'Technology',
        'https://techpk.com',
        'A leading software house based in Karachi.',
        date('Y-m-d H:i:s'),
    ]);
    $company_id = last_insert_id();

    execute('INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, ?)', [
        'recruiter1',
        hash('sha256', 'password123'),
        'recruiter',
        date('Y-m-d H:i:s'),
    ]);
    $recruiter_user_id = last_insert_id();

    execute('INSERT INTO recruiters (user_id, company_id, full_name, email, phone, created_at) VALUES (?, ?, ?, ?, ?, ?)', [
        $recruiter_user_id,
        $company_id,
        'Sara Ahmed',
        'sara@techpk.com',
        '+92 300 9876543',
        date('Y-m-d H:i:s'),
    ]);
    $recruiter_id = last_insert_id();

    echo "✓ Recruiter account created\n";

    // Create jobs
    $jobs = [
        [
            'title' => 'PHP Developer',
            'description' => 'We are looking for an experienced PHP developer to join our team.',
            'location' => 'Karachi',
            'salary' => 80000,
            'skills' => ['PHP', 'MongoDB', 'HTML/CSS'],
        ],
        [
            'title' => 'Frontend Developer',
            'description' => 'Build responsive web applications using React and modern JavaScript.',
            'location' => 'Remote',
            'salary' => 90000,
            'skills' => ['JavaScript', 'React', 'HTML/CSS'],
        ],
        [
            'title' => 'Full Stack Developer',
            'description' => 'Develop both frontend and backend for our web platform.',
            'location' => 'Lahore',
            'salary' => 100000,
            'skills' => ['PHP', 'JavaScript', 'MongoDB', 'React'],
        ],
        [
            'title' => 'Database Administrator',
            'description' => 'Manage and optimize our SQL database infrastructure and ensure system reliability.',
            'location' => 'Islamabad',
            'salary' => 95000,
            'skills' => ['MongoDB', 'SQL', 'Python'],
        ],
    ];

    foreach ($jobs as $job) {
        execute('INSERT INTO jobs (company_id, recruiter_id, title, description, location, salary, posted_at) VALUES (?, ?, ?, ?, ?, ?, ?)', [
            $company_id,
            $recruiter_id,
            $job['title'],
            $job['description'],
            $job['location'],
            $job['salary'],
            date('Y-m-d H:i:s'),
        ]);
        $job_id = last_insert_id();
        foreach ($job['skills'] as $skill_name) {
            $skill = fetch_one('SELECT id FROM skills WHERE name = ?', [$skill_name]);
            if ($skill) {
                execute('INSERT IGNORE INTO job_skills (job_id, skill_id) VALUES (?, ?)', [$job_id, $skill['id']]);
            }
        }
    }

    echo "✓ Jobs created\n";

    // Create an application
    $job = fetch_one('SELECT id FROM jobs WHERE title = ?', ['PHP Developer']);
    if ($job) {
        execute('INSERT INTO applications (applicant_id, job_id, status, cover_letter, applied_at) VALUES (?, ?, ?, ?, ?)', [
            $applicant_id,
            $job['id'],
            'pending',
            'I am very interested in this PHP Developer position. I have 3 years of experience with PHP and MongoDB.',
            date('Y-m-d H:i:s'),
        ]);
    }

    commit_transaction();
    echo "✓ Sample application created\n";
    echo "\nSQL seed completed successfully!\n";
    echo "Admin Login: admin / admin123\n";
    echo "Applicant Login: applicant1 / password123\n";
    echo "Recruiter Login: recruiter1 / password123\n";
} catch (Exception $e) {
    rollback_transaction();
    echo 'Seed failed: ' . $e->getMessage() . "\n";
}
