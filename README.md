# Applicant Module - Job Portal

## Overview

This repository contains only the **Applicant Module** of the Online Job Portal & Application Tracking System. It implements applicant registration/login, profile management, skills management, job search/filter, job application, and application history.

## Database Schema

This project currently uses MySQL / MariaDB.

- **users**: Stores login credentials and roles
- **applicants**: Applicant profile information
- **recruiters**: Recruiter profiles linked to companies
- **companies**: Company information for recruiter accounts
- **jobs**: Job postings published by recruiters
- **skills**: Available skill tags
- **applications**: Job applications submitted by applicants
- **applicant_skills**: Skills added by applicants
- **job_skills**: Skill requirements for jobs

## Features

✅ User Registration & Login  
✅ Recruiter Registration & Job Management  
✅ Admin Login & Management  
✅ Applicant Profile Management  
✅ Skills Management  
✅ Job Search & Filtering  
✅ Apply for Jobs with Cover Letter  
✅ Application History & Status Tracking  

---

## Setup Instructions

### Prerequisites

1. **MySQL/MariaDB** installed and running
2. **PHP 7.4+**
3. **XAMPP**, **WAMP**, or any PHP web server

### Step 1: Create the Database

Import the schema file into MySQL using phpMyAdmin or command line.

Using command line:
```bash
mysql -u root -p < schema.sql
```

### Step 2: Seed the Database

Use the provided SQL seeder to create sample data.

From your PHP installation directory (or using the PHP on PATH):
```bash
php seed_mysql.php
```

### Step 3: Place the Project in Your Web Root

Example XAMPP path:
```
C:\xampp\htdocs\Job-Portal-Application-Tracking-System-\
```

### Step 4: Start the Web Server

1. Open XAMPP Control Panel
2. Start Apache and MySQL
3. Open browser: `http://localhost/Job-Portal-Application-Tracking-System-/index.php`

---

## How to Use

### Login / Register

- **Register**: Fill out the registration form with your details
- **Login**: Use your username and password to log in

### Dashboard

After login, you'll see your dashboard with quick links to:
- Search Jobs
- Edit Profile
- View Applications

### Edit Profile

Click **My Profile** to update:
- Full Name
- Email
- Phone
- Headline (job title)
- Bio (about yourself)

### Manage Skills

Click **Manage Skills** to:
- Add new skills (e.g., PHP, JavaScript, MongoDB)
- Remove skills by clicking the X button
- Skills help match you with relevant jobs

### Search Jobs

Click **Search Jobs** to:
- Search by keyword (title, location, description)
- Filter by skill requirement
- View job details, salary, and location
- Click **Apply Now** to submit an application

### Apply for Jobs

When applying:
- Write a cover letter explaining why you're a good fit
- Submit your application
- You can only apply once per job

### View Application History

Click **Applications** to see:
- All your job applications
- Job title, location, and salary
- Current status (Pending, Shortlisted, Accepted, Rejected)
- Your cover letter

---

## Test Credentials

| Username | Password | Role |
|----------|----------|------|
| admin | admin123 | Administrator |
| applicant1 | password123 | Applicant |
| recruiter1 | password123 | Recruiter |

### Access URLs
- **Main Website**: http://localhost/Job-Portal-Application-Tracking-System-/index.php
- **Unified Login**: http://localhost/Job-Portal-Application-Tracking-System-/login.php
- **Applicant Registration**: http://localhost/Job-Portal-Application-Tracking-System-/applicant_index.php
- **Recruiter Registration**: http://localhost/Job-Portal-Application-Tracking-System-/recruiter_index.php
- **Admin Login**: http://localhost/Job-Portal-Application-Tracking-System-/admin_index.php

---

## Troubleshooting

### MongoDB Connection Error

**Error**: `MongoDB connection failed`

**Solution**:
1. Ensure MongoDB is running: Open PowerShell and run `mongosh`
2. If mongosh doesn't work, start MongoDB manually:
   - Windows: Use MongoDB Compass or MongoDB service in Services app
   - Mac/Linux: `brew services start mongodb-community`

### PHP MongoDB Extension Not Found

**Error**: `MongoDB PHP driver is not installed`

**Solution**:
1. Edit `C:\xampp\php\php.ini`
2. Find or add: `extension=mongodb`
3. Remove the semicolon (`;`) if it exists at the beginning
4. Save the file
5. Restart Apache in XAMPP Control Panel

### Blank Page
## Recruiter Module (Person 2 - Complete)

### New Files Added
- index.php - Welcome/landing page with role selection
- login.php - Unified login page for all user types (applicant, recruiter, admin)
- applicant_index.php - Applicant registration page
- recruiter_index.php - Login/Register for recruiters
- admin_index.php - Admin login page
- admin_dashboard.php - Admin homepage with system overview
- admin_users.php - User management (CRUD operations)
- admin_companies.php - Company management (CRUD operations)
- admin_navbar.php - Admin navigation bar
- admin_logout.php - Admin logout functionality
- recruiter_logout.php - Logout
- recruiter_navbar.php - Navigation bar
- seed_recruiter.php - Sample recruiter data

### Test Credentials
| Username | Password | Role |
|----------|----------|------|
| recruiter1 | password123 | Recruiter |

### Setup
Run this command after seed_mongo.php:
.\php.exe "C:\xampp\htdocs\Job-Portal-Application-Tracking-System--main\seed_recruiter.php"

### Recruiter URL
http://localhost/Job-Portal-Application-Tracking-System--main/recruiter_index.php

## How to Use (Recruiter)

### Login / Register
- **Register**: Fill out your details and company information
- **Login**: Use your username and password to log in

### Dashboard
After login you will see:
- Total jobs posted
- Total applications received
- Pending applications
- Accepted applications

### Post a Job
Click **Post Job** to:
- Add job title and description
- Set location and salary
- Select required skills
- Submit the listing

### Manage Jobs
Click **My Jobs** to:
- View all your posted jobs
- See how many people applied
- Edit job details
- Delete a job

### View Applicants
Click **View Applicants** on any job to:
- See all applicants with their name, email, phone
- View their skills
- Read their cover letter
- Update their status: Pending, Shortlisted, Accepted, Rejected

### Company Profile
Click **Company Profile** to:
- Update company name and email
- Set industry and website
- Add company description
