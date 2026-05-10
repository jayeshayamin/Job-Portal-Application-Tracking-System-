<?php
require_once 'config.php';

echo "Starting seed...\n";

$collections = ['users', 'applicants', 'jobs', 'skills', 'applications'];
foreach ($collections as $coll) {
    mongo_delete_many($coll, []);
}
echo "✓ Cleared collections\n";

$skill_names = ['PHP', 'JavaScript', 'MongoDB', 'SQL', 'Python', 'React', 'Node.js', 'HTML/CSS'];
foreach ($skill_names as $name) {
    mongo_insert_one('skills', ['name' => $name]);
}
echo "✓ Added " . count($skill_names) . " skills\n";

$applicant_user_id = mongo_insert_one('users', [
    'username' => 'applicant1',
    'password' => hash('sha256', 'password123'),
    'role' => 'applicant',
    'created_at' => date('Y-m-d H:i:s'),
]);

$applicant_id = mongo_insert_one('applicants', [
    'user_id' => mongo_object_id($applicant_user_id),
    'full_name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+92 300 1234567',
    'headline' => 'Software Engineer',
    'bio' => 'Passionate about web development and databases.',
    'skills' => ['PHP', 'JavaScript', 'MongoDB'],
    'created_at' => date('Y-m-d H:i:s'),
]);
echo "✓ Created test applicant (username: applicant1, password: password123)\n";

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
        'description' => 'Manage and optimize our MongoDB and SQL databases.',
        'location' => 'Islamabad',
        'salary' => 95000,
        'skills' => ['MongoDB', 'SQL', 'Python'],
    ],
];

foreach ($jobs as $job) {
    mongo_insert_one('jobs', array_merge($job, [
        'posted_at' => date('Y-m-d H:i:s'),
    ]));
}
echo "✓ Added " . count($jobs) . " sample jobs\n";

mongo_insert_one('applications', [
    'applicant_id' => mongo_object_id($applicant_id),
    'job_id' => mongo_find_one('jobs', ['title' => 'PHP Developer'])['_id'],
    'status' => 'pending',
    'cover_letter' => 'I am very interested in this PHP Developer position. I have 3 years of experience with PHP and MongoDB.',
    'applied_at' => date('Y-m-d H:i:s'),
]);
echo "✓ Added 1 sample application\n";

echo "\n✅ Seed completed successfully!\n";
echo "Test Login: applicant1 / password123\n";
