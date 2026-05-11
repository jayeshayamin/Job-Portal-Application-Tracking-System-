# Applicant Module - Job Portal

## Overview

This repository contains only the **Applicant Module** of the Online Job Portal & Application Tracking System. It implements applicant registration/login, profile management, skills management, job search/filter, job application, and application history.

## MongoDB Collections

- **users**: Stores login credentials for applicants
- **applicants**: Applicant profile information and skills array
- **jobs**: Job postings with requirements
- **skills**: Available skills in the system
- **applications**: Records of job applications with status

## Features

✅ User Registration & Login  
✅ Profile Management (name, email, headline, bio)  
✅ Add/Remove Skills  
✅ Search & Filter Jobs  
✅ Apply for Jobs with Cover Letter  
✅ View Application History & Status  

---

## Setup Instructions

### Prerequisites

1. **MongoDB** installed and running on `mongodb://127.0.0.1:27017`
2. **PHP 7.4+** with MongoDB extension enabled
3. **XAMPP** or any PHP web server

### Step 1: Install MongoDB

If you don't have MongoDB installed:
- Download from: https://www.mongodb.com/try/download/community
- Install and ensure MongoDB is running in the background
- Check it's running: `mongosh` should connect successfully

### Step 2: Enable MongoDB PHP Extension

1. Find `php.ini` in your XAMPP installation (typically `C:\xampp\php\php.ini`)
2. Uncomment or add: `extension=mongodb`
3. Save and restart Apache in XAMPP

To verify MongoDB is enabled:
```bash
cd C:\xampp\php
php -r "echo extension_loaded('mongodb') ? 'MongoDB enabled' : 'MongoDB disabled';"
```

### Step 3: Copy Project to XAMPP

Place this folder in XAMPP's document root:
```
C:\xampp\htdocs\Job-Portal-Application-Tracking-System-\
```

### Step 4: Populate Database

Open PowerShell and run:
```bash
cd "C:\xampp\php"
.\php.exe "C:\xampp\htdocs\Job-Portal-Application-Tracking-System-\seed_mongo.php"
```

Expected output:
```
Starting seed...
✓ Cleared collections
✓ Added 8 skills
✓ Created test applicant (username: applicant1, password: password123)
✓ Added 4 sample jobs
✓ Added 1 sample application

✅ Seed completed successfully!
Test Login: applicant1 / password123
```

### Step 5: Start XAMPP

1. Open XAMPP Control Panel
2. Click **Start** next to Apache
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
| applicant1 | password123 | Applicant |

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
- recruiter_index.php - Login/Register for recruiters
- recruiter_dashboard.php - Recruiter homepage with stats
- recruiter_post_job.php - Post a new job
- recruiter_jobs.php - View/edit/delete jobs
- recruiter_edit_job.php - Edit existing job
- recruiter_applicants.php - View applicants & update status
- recruiter_company.php - Edit company profile
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
