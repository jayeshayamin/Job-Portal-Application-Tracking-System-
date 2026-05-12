# Job Portal Application Tracking System - Complete Setup Guide

## 📋 Project Overview

This is a **fully functional Job Portal & Application Tracking System** built with PHP and MySQL. It provides three main modules:

- **Applicant Module**: Job seekers can register, search jobs, apply, and track applications
- **Recruiter Module**: Companies can post jobs, review applicants, and update application status
- **Admin Module**: System administrators can manage users, companies, and oversee the platform

---

## ✅ Prerequisites

Before you begin, ensure you have:

1. **XAMPP / WAMP / LAMP** installed (PHP 7.4+ required)
2. **MySQL / MariaDB** running
3. **phpMyAdmin** (for database management)
4. A **text editor** or IDE (VS Code, PHPStorm, etc.)
5. **Browser** (Chrome, Firefox, Edge, etc.)

---

## 🚀 Quick Start (5 Minutes)

### Step 1: Clone/Download the Project

```bash
# If using XAMPP on Windows:
# Extract to: C:\xampp\htdocs\Job-Portal-Application-Tracking-System-\

# If using WAMP on Windows:
# Extract to: C:\wamp\www\Job-Portal-Application-Tracking-System-\

# If using Linux/Mac:
# Extract to: /var/www/html/Job-Portal-Application-Tracking-System-\
```

### Step 2: Create the Database

Open your browser and go to **phpMyAdmin**:

```
http://localhost/phpmyadmin/
```

1. Click on **"SQL"** tab
2. Copy the contents of `schema.sql` from the project folder
3. Paste it into the SQL input area
4. Click **"Go"** to execute

**OR** Using command line:

```bash
mysql -u root -p < schema.sql
```

### Step 3: Seed Sample Data

From the project directory, run:

```bash
php seed_mysql.php
```

This creates:
- Admin account (username: `admin` / password: `admin123`)
- Test applicant (username: `applicant1` / password: `password123`)
- Test recruiter (username: `recruiter1` / password: `password123`)
- Sample jobs and skills

### Step 4: Start the Server

**Using XAMPP:**
1. Open XAMPP Control Panel
2. Click **"Start"** for Apache and MySQL
3. Open browser: `http://localhost/Job-Portal-Application-Tracking-System-/`

**Using WAMP:**
1. Click WAMP icon → MySQL → Start
2. Open browser: `http://localhost/Job-Portal-Application-Tracking-System-/`

**Using built-in PHP server:**

```bash
cd Job-Portal-Application-Tracking-System-
php -S localhost:8000
```

Then open: `http://localhost:8000/`

---

## 📝 Default Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Applicant | `applicant1` | `password123` |
| Recruiter | `recruiter1` | `password123` |

---

## 📁 Project Structure

```
Job-Portal-Application-Tracking-System-/
├── config.php                    # Database configuration & functions
├── schema.sql                    # Database schema (run this first!)
├── seed_mysql.php               # Sample data seeder
│
├── index.php                    # Landing page
├── login.php                    # Unified login for all roles
│
├── Admin Panel/
│   ├── admin_index.php          # Admin login
│   ├── admin_dashboard.php      # Admin dashboard
│   ├── admin_users.php          # Manage users
│   ├── admin_companies.php      # Manage companies
│   ├── admin_navbar.php         # Admin navigation
│   └── admin_logout.php
│
├── Applicant Module/
│   ├── applicant_index.php      # Applicant login/register
│   ├── dashboard.php            # Applicant dashboard
│   ├── jobs.php                 # Search & filter jobs
│   ├── apply.php                # Submit job application
│   ├── profile.php              # Edit applicant profile
│   ├── skills.php               # Manage skills
│   ├── history.php              # Application history
│   └── logout.php
│
├── Recruiter Module/
│   ├── recruiter_index.php      # Recruiter login/register
│   ├── recruiter_dashboard.php  # Recruiter dashboard
│   ├── recruiter_post_job.php   # Post new job
│   ├── recruiter_jobs.php       # View posted jobs
│   ├── recruiter_edit_job.php   # Edit job listing
│   ├── recruiter_applicants.php # View applicants
│   ├── recruiter_company.php    # Edit company profile
│   ├── recruiter_navbar.php     # Recruiter navigation
│   └── recruiter_logout.php
│
├── style.css                    # Global styling
├── README.md                    # Original documentation
└── SETUP_GUIDE.md              # This file
```

---

## 🔧 Configuration

### Database Configuration

Edit `config.php` if needed:

```php
const DB_HOST = '127.0.0.1';    // Database server
const DB_NAME = 'job_portal_db'; // Database name
const DB_USER = 'root';          // Database user
const DB_PASS = '';              // Database password
```

### Common Issues

**"Database connection failed"**
- Check MySQL is running
- Verify credentials in `config.php`
- Ensure `job_portal_db` database exists

**"Access denied for user 'root'"**
- If your MySQL root has a password, update `DB_PASS` in `config.php`

**"Cannot find database"**
- Run `schema.sql` in phpMyAdmin first
- Verify the database name matches `DB_NAME` in config

---

## 🎯 Features Walkthrough

### For Applicants

1. **Register/Login**
   - Go to `http://localhost/Job-Portal-Application-Tracking-System-/applicant_index.php`
   - Create account or login

2. **Search Jobs**
   - View all available jobs
   - Search by title, description, or location
   - Filter by required skills

3. **Apply for Jobs**
   - Click "Apply Now" on any job card
   - Write a cover letter
   - Submit application

4. **Manage Profile**
   - Update name, email, phone
   - Add professional headline
   - Write bio

5. **Manage Skills**
   - Add skills from predefined list or create new ones
   - Remove skills as needed
   - Match your skills with job requirements

6. **Track Applications**
   - View all submitted applications
   - Check application status (Pending, Shortlisted, Accepted, Rejected)
   - Read cover letters you submitted

### For Recruiters

1. **Register/Login**
   - Go to `http://localhost/Job-Portal-Application-Tracking-System-/recruiter_index.php`
   - Create account with company details

2. **Post Jobs**
   - Add job title, description, location, salary
   - Select required skills
   - Publish job listing

3. **Review Applicants**
   - View all applications for each job
   - Read cover letters
   - View applicant profile and skills

4. **Update Application Status**
   - Change status: Pending → Shortlisted → Accepted/Rejected
   - Applicants see status updates immediately

5. **Manage Company Profile**
   - Update company information
   - Edit industry, website, description
   - View company statistics

### For Admins

1. **Login**
   - Go to `http://localhost/Job-Portal-Application-Tracking-System-/admin_index.php`
   - Use admin credentials

2. **Manage Users**
   - Create applicant, recruiter, or admin accounts
   - Delete users
   - View user list with roles

3. **Manage Companies**
   - Create new companies
   - Edit company information
   - Delete companies (cascades to recruiters and jobs)

4. **View Statistics**
   - Total users, applicants, recruiters, admins
   - Top companies by activity
   - Recent applications

---

## 🗄️ Database Schema

### Tables

**users** - User accounts for all roles
**applicants** - Applicant profile information
**recruiters** - Recruiter profiles linked to companies
**companies** - Company information
**jobs** - Job postings
**applications** - Job applications tracking
**skills** - Skill tags
**applicant_skills** - Skills per applicant (many-to-many)
**job_skills** - Required skills per job (many-to-many)

### Views (For complex queries)

**admin_dashboard_summary** - Total users by role
**company_job_application_summary** - Company statistics
**applicant_application_history** - Formatted application history
**recruiter_application_details** - Applicant details per application

---

## 🔐 Security Features

✅ **Password Hashing**: SHA-256 algorithm
✅ **SQL Injection Prevention**: Parameterized queries with PDO
✅ **Input Sanitization**: htmlspecialchars() on all user input
✅ **Session Management**: Secure session handling per role
✅ **Authentication Checks**: require_login() on protected pages

---

## 📊 Sample Data Included

After running `seed_mysql.php`, you get:

- **8 Predefined Skills**: PHP, JavaScript, SQL, Python, MongoDB, React, Node.js, HTML/CSS
- **4 Sample Jobs**: PHP Developer, Frontend Developer, Full Stack, Database Admin
- **1 Applicant**: With one test application
- **1 Company**: TechPk Solutions
- **1 Recruiter**: Sara Ahmed

---

## 🛠️ Troubleshooting

| Issue | Solution |
|-------|----------|
| Blank page | Check PHP error log, ensure config.php is accessible |
| 404 error | Verify file path and folder structure |
| Database error | Run schema.sql and check credentials in config.php |
| Cannot login | Verify credentials, ensure seed_mysql.php was run |
| Jobs not showing | Check jobs table has data in phpMyAdmin |
| Applicants can't apply | Verify they're logged in (session) |
| Recruiter can't see applications | Make sure applicants applied to their company's jobs |

---

## 🎓 Code Structure

### Authentication Flow

```
login.php
  ├─ Checks role (applicant/recruiter/admin)
  ├─ Redirects to respective dashboard
  └─ Sets $_SESSION['user']/['recruiter']/['admin']

Protected pages check:
  ├─ require_login() → for applicants
  ├─ require_recruiter_login() → for recruiters
  └─ require_admin_login() → for admins
```

### Database Operations

```php
// Fetch single record
$user = fetch_one('SELECT * FROM users WHERE id = ?', [$id]);

// Fetch multiple records
$jobs = fetch_all('SELECT * FROM jobs WHERE company_id = ?', [$cid]);

// Execute insert/update/delete
execute('INSERT INTO jobs (...) VALUES (...)', $params);

// Get last inserted ID
$id = last_insert_id();

// Transactions
begin_transaction();
try {
    execute(...);
    commit_transaction();
} catch (Exception $e) {
    rollback_transaction();
}
```

---

## 🚦 Testing Checklist

After setup, verify:

- [ ] Can access home page at `/index.php`
- [ ] Can login as each role (admin/applicant/recruiter)
- [ ] Can register new applicant account
- [ ] Can register new recruiter with company
- [ ] Applicant can search and filter jobs
- [ ] Applicant can apply for jobs
- [ ] Recruiter can post jobs
- [ ] Recruiter can view applications
- [ ] Recruiter can update application status
- [ ] Admin can create/delete users
- [ ] Admin can manage companies
- [ ] Logout works for all roles

---

## 📱 Browser Compatibility

- ✅ Chrome 80+
- ✅ Firefox 75+
- ✅ Safari 13+
- ✅ Edge 80+
- ✅ Mobile browsers (responsive design)

---

## 📧 Support

For issues, check:

1. **Browser Console** (F12 → Console)
2. **PHP Error Log** (check XAMPP/WAMP logs)
3. **Database** (phpMyAdmin → check tables)
4. **File Permissions** (ensure PHP can read/write)

---

## 📄 License

This project is provided as-is for educational purposes.

---

## ✨ What's Included

✅ Complete PHP/MySQL backend
✅ Responsive Bootstrap UI
✅ User authentication & authorization
✅ Job posting & management
✅ Application tracking
✅ Database schema with views
✅ Sample data seeder
✅ Security best practices
✅ Error handling
✅ Transaction support

**The project is now fully functional and ready to use!**

---

## 🎉 Next Steps

1. Run schema.sql to create database
2. Run seed_mysql.php to populate sample data
3. Start your web server
4. Login and explore!

**Enjoy your fully functional Job Portal!**
