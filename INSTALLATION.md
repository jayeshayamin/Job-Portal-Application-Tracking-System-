# Installation Guide - Job Portal ATS

## 🚀 Choose Your Platform

### [👉 WINDOWS with XAMPP](#windows-xampp) | [👉 WINDOWS with WAMP](#windows-wamp) | [👉 LINUX](#linux) | [👉 MAC](#mac) | [👉 Docker](#docker)

---

## Windows + XAMPP

### Prerequisites
- XAMPP installed (from apachefriends.org)
- PHP 7.4+
- MySQL/MariaDB

### Installation Steps

#### 1. Extract Project
```
Extract project folder to:
C:\xampp\htdocs\Job-Portal-Application-Tracking-System-\
```

#### 2. Start XAMPP
```
1. Open XAMPP Control Panel
2. Click "Start" button for:
   - Apache
   - MySQL
3. Wait for green indicators
```

#### 3. Create Database
```
1. Open browser: http://localhost/phpmyadmin/
2. Click "SQL" tab
3. Open schema.sql in text editor
4. Copy ALL content
5. Paste into SQL box in phpMyAdmin
6. Click "Go" button
7. Wait for success message
```

#### 4. Seed Sample Data
```
1. Open Command Prompt (cmd)
2. Navigate to project:
   cd C:\xampp\htdocs\Job-Portal-Application-Tracking-System-
3. Run seeder:
   php seed_mysql.php
4. Wait for completion message
```

#### 5. Verify Installation
```
Open browser:
http://localhost/Job-Portal-Application-Tracking-System-/verify_setup.php

Should show: ✅ All systems operational
```

#### 6. Access the Application
```
Main page:    http://localhost/Job-Portal-Application-Tracking-System-/
Admin:        http://localhost/Job-Portal-Application-Tracking-System-/admin_index.php
Applicant:    http://localhost/Job-Portal-Application-Tracking-System-/applicant_index.php
Recruiter:    http://localhost/Job-Portal-Application-Tracking-System-/recruiter_index.php
```

---

## Windows + WAMP

### Prerequisites
- WAMP installed (from wampserver.com)
- PHP 7.4+
- MySQL/MariaDB

### Installation Steps

#### 1. Extract Project
```
Extract to:
C:\wamp\www\Job-Portal-Application-Tracking-System-\
```

#### 2. Start WAMP
```
1. Click WAMP icon (system tray)
2. Hover "MySQL" → "Start/Resume"
3. Hover "Apache" → "Start/Resume"
4. Wait for green icon
```

#### 3. Create Database (Same as XAMPP)
```
1. http://localhost/phpmyadmin/
2. SQL tab
3. Import schema.sql
4. Click Go
```

#### 4. Seed Data
```
Command Prompt:
cd C:\wamp\www\Job-Portal-Application-Tracking-System-
php seed_mysql.php
```

#### 5. Access Application
```
http://localhost/Job-Portal-Application-Tracking-System-/
```

---

## Linux (Ubuntu/Debian)

### Prerequisites
```bash
sudo apt-get update
sudo apt-get install apache2 php php-mysql mysql-server
sudo apt-get install php-pdo php-json php-curl

# Start services
sudo systemctl start apache2
sudo systemctl start mysql
```

### Installation Steps

#### 1. Extract Project
```bash
cd /var/www/html
sudo git clone <project-url> Job-Portal-Application-Tracking-System-
# OR
sudo unzip project.zip
cd Job-Portal-Application-Tracking-System-
```

#### 2. Set Permissions
```bash
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
```

#### 3. Create Database
```bash
mysql -u root -p < schema.sql
# Enter MySQL password when prompted
```

#### 4. Seed Data
```bash
php seed_mysql.php
```

#### 5. Access Application
```
http://localhost/Job-Portal-Application-Tracking-System-/
```

---

## Mac (with MAMP)

### Prerequisites
- MAMP Pro installed (from mamp.info)
- PHP 7.4+

### Installation Steps

#### 1. Extract Project
```bash
cd /Applications/MAMP/htdocs
unzip ~/Downloads/Job-Portal-Application-Tracking-System-.zip
cd Job-Portal-Application-Tracking-System-
```

#### 2. Start MAMP
```
1. Open MAMP
2. Click "Start Servers"
3. Wait for green indicators
```

#### 3. Create Database
```
1. Open http://localhost:8888/phpmyadmin/
2. SQL tab
3. Import schema.sql
```

#### 4. Seed Data
```bash
php seed_mysql.php
```

#### 5. Access Application
```
http://localhost:8888/Job-Portal-Application-Tracking-System-/
```

---

## Docker (Advanced)

### Prerequisites
- Docker installed
- Docker Compose installed

### Installation Steps

#### 1. Create Dockerfile
```dockerfile
FROM php:7.4-apache

RUN docker-php-ext-install pdo pdo_mysql json

WORKDIR /var/www/html

EXPOSE 80
```

#### 2. Create docker-compose.yml
```yaml
version: '3'

services:
  web:
    build: .
    ports:
      - "80:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db

  db:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: job_portal_db
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql
      - ./schema.sql:/docker-entrypoint-initdb.d/schema.sql

volumes:
  db_data:
```

#### 3. Run Docker
```bash
docker-compose up -d
```

#### 4. Seed Data
```bash
docker-compose exec web php seed_mysql.php
```

#### 5. Access Application
```
http://localhost/Job-Portal-Application-Tracking-System-/
```

---

## Using PHP Built-in Server

### Requirements
- PHP 7.4+ installed locally

### Steps

```bash
# Navigate to project
cd Job-Portal-Application-Tracking-System-

# Start PHP server
php -S localhost:8000

# Open browser
http://localhost:8000/
```

**Note:** Requires MySQL running separately

---

## Troubleshooting Guide

### Problem: "Cannot connect to database"
```
Solution:
1. Verify MySQL is running
2. Check credentials in config.php
3. Ensure schema.sql was imported
4. Check database name: job_portal_db
```

### Problem: "Blank white page"
```
Solution:
1. Check PHP error log:
   Windows: C:\xampp\apache\logs\error.log
   Linux: /var/log/apache2/error.log
2. Enable error display in php.ini:
   display_errors = On
3. Check file permissions
4. Verify config.php exists
```

### Problem: "404 Not Found"
```
Solution:
1. Verify file path is correct
2. Check htdocs/www folder structure
3. Restart Apache/XAMPP
4. Clear browser cache
```

### Problem: "Tables don't exist"
```
Solution:
1. Go to phpMyAdmin
2. Select job_portal_db database
3. If empty, import schema.sql
4. Verify all 9 tables are present:
   users, applicants, recruiters, companies, jobs,
   applications, skills, applicant_skills, job_skills
```

### Problem: "No sample data"
```
Solution:
1. Open command prompt in project folder
2. Run: php seed_mysql.php
3. Check database has:
   - Admin user: admin / admin123
   - Test applicant: applicant1 / password123
   - Test recruiter: recruiter1 / password123
```

---

## Verification Checklist

After installation, verify:

- [ ] Can access home page
- [ ] Can open verify_setup.php and see all ✓
- [ ] Can login as admin
- [ ] Can login as applicant
- [ ] Can login as recruiter
- [ ] Can view jobs listing
- [ ] Can apply for job
- [ ] Can post job (recruiter)
- [ ] Can view applicants (recruiter)
- [ ] Database has 9 tables

---

## File Structure After Installation

```
Job-Portal-Application-Tracking-System-/
├── ✅ config.php                    (required)
├── ✅ schema.sql                    (already imported)
├── ✅ seed_mysql.php                (already run)
├── ✅ index.php
├── ✅ style.css
├── ✅ admin_*.php files             (5 files)
├── ✅ applicant_*.php / *.php        (8 files)
├── ✅ recruiter_*.php files         (8 files)
├── ✅ verify_setup.php
├── ✅ FEATURES.md
├── ✅ SETUP_GUIDE.md
├── ✅ QUICK_START.md
└── ✅ README.md
```

---

## Default Test Credentials

| User Type | Username | Password | First URL |
|-----------|----------|----------|-----------|
| Admin | admin | admin123 | /admin_index.php |
| Applicant | applicant1 | password123 | /applicant_index.php |
| Recruiter | recruiter1 | password123 | /recruiter_index.php |

---

## Configuration (if needed)

### Edit config.php for Custom Settings

```php
// Database
const DB_HOST = '127.0.0.1';      // Database server
const DB_NAME = 'job_portal_db';  // Database name
const DB_USER = 'root';           // Database user
const DB_PASS = '';               // Database password (if any)

// If you have a MySQL password:
const DB_PASS = 'your_password';

// If database is on different server:
const DB_HOST = '192.168.1.100';
```

---

## Performance Tips

1. **Close unused applications** to free RAM
2. **Use SSD** for faster database operations
3. **Clear browser cache** if experiencing issues
4. **Monitor MySQL** in XAMPP control panel
5. **Disable unnecessary services** in XAMPP

---

## Next Steps

1. ✅ Complete installation
2. ✅ Run verify_setup.php
3. ✅ Login with test accounts
4. ✅ Explore all features
5. ✅ Create production users
6. ✅ Deploy to server (optional)

---

## Getting Help

### Resources
- SETUP_GUIDE.md - Detailed guide
- FEATURES.md - Feature list
- QUICK_START.md - Quick reference
- verify_setup.php - Check setup

### Common Issues
Check "Troubleshooting Guide" section above

---

## Quick Reference

### Common Paths
```
XAMPP Root:  C:\xampp\htdocs\
WAMP Root:   C:\wamp\www\
Linux:       /var/www/html/
Mac (MAMP):  /Applications/MAMP/htdocs/
```

### Database Tools
```
phpMyAdmin: http://localhost/phpmyadmin/
MySQL CLI:  mysql -u root -p
```

### Service Commands
```bash
# Linux
sudo systemctl restart apache2
sudo systemctl restart mysql

# XAMPP / WAMP
GUI control panel
```

---

## ✅ You're Ready!

Once you see all ✅ in verify_setup.php, you're ready to:

1. Login as different users
2. Create jobs (recruiter)
3. Apply for jobs (applicant)
4. Manage users (admin)
5. Track applications

**Enjoy your fully functional Job Portal!**

---

**Version:** 1.0 | **Updated:** May 2026 | **Status:** ✅ PRODUCTION READY
