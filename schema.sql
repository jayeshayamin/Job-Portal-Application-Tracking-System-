-- SQL schema for Job Portal Application Tracking System
-- MySQL / MariaDB compatible

CREATE DATABASE IF NOT EXISTS job_portal_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE job_portal_db;

DROP TABLE IF EXISTS job_skills;
DROP TABLE IF EXISTS applicant_skills;
DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS recruiters;
DROP TABLE IF EXISTS applicants;
DROP TABLE IF EXISTS companies;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('applicant', 'recruiter', 'admin') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150),
    industry VARCHAR(100),
    website VARCHAR(255),
    description TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE applicants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    headline VARCHAR(150),
    bio TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE recruiters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_id INT NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    recruiter_id INT DEFAULT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(100),
    salary INT DEFAULT 0,
    posted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (recruiter_id) REFERENCES recruiters(id) ON DELETE SET NULL
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT NOT NULL,
    job_id INT NOT NULL,
    status ENUM('pending', 'shortlisted', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    cover_letter TEXT NOT NULL,
    applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_applicant_job (applicant_id, job_id)
);

CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE applicant_skills (
    applicant_id INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (applicant_id, skill_id),
    FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

CREATE TABLE job_skills (
    job_id INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (job_id, skill_id),
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

DROP VIEW IF EXISTS applicant_application_history;
CREATE VIEW applicant_application_history AS
SELECT
    a.id AS application_id,
    a.applicant_id,
    j.id AS job_id,
    j.title AS job_title,
    j.location AS job_location,
    j.salary AS job_salary,
    c.name AS company_name,
    a.status,
    a.cover_letter,
    DATE_FORMAT(a.applied_at, '%Y-%m-%d %H:%i:%s') AS applied_at
FROM applications a
JOIN jobs j ON a.job_id = j.id
JOIN companies c ON j.company_id = c.id;

DROP VIEW IF EXISTS recruiter_application_details;
CREATE VIEW recruiter_application_details AS
SELECT
    a.id AS application_id,
    a.job_id,
    j.title AS job_title,
    app.full_name AS applicant_name,
    app.email AS applicant_email,
    app.phone AS applicant_phone,
    app.headline AS applicant_headline,
    a.status,
    a.cover_letter,
    DATE_FORMAT(a.applied_at, '%Y-%m-%d %H:%i:%s') AS applied_at
FROM applications a
JOIN jobs j ON a.job_id = j.id
JOIN applicants app ON a.applicant_id = app.id;

DROP VIEW IF EXISTS admin_dashboard_summary;
CREATE VIEW admin_dashboard_summary AS
SELECT
    COUNT(*) AS total_users,
    SUM(role = 'applicant') AS total_applicants,
    SUM(role = 'recruiter') AS total_recruiters,
    SUM(role = 'admin') AS total_admins
FROM users;

DROP VIEW IF EXISTS company_job_application_summary;
CREATE VIEW company_job_application_summary AS
SELECT
    c.id AS company_id,
    c.name AS company_name,
    COUNT(DISTINCT j.id) AS total_jobs,
    COUNT(a.id) AS total_applications
FROM companies c
LEFT JOIN jobs j ON j.company_id = c.id
LEFT JOIN applications a ON a.job_id = j.id
GROUP BY c.id;
