# Job Portal - Quick Start (30 seconds setup)

## Fastest Way to Get Running

### Option 1: Using XAMPP (Windows)

```
1. Extract project to: C:\xampp\htdocs\Job-Portal-Application-Tracking-System-\
2. Open XAMPP Control Panel → Start Apache & MySQL
3. Open phpMyAdmin: http://localhost/phpmyadmin/
4. SQL tab → Paste contents of schema.sql → Go
5. Open terminal in project folder
6. Run: php seed_mysql.php
7. Open browser: http://localhost/Job-Portal-Application-Tracking-System-/
```

**Done! Login with:**
- Admin: admin / admin123
- Applicant: applicant1 / password123
- Recruiter: recruiter1 / password123

---

### Option 2: Using PHP Built-in Server (Any OS)

```bash
cd "path/to/Job-Portal-Application-Tracking-System-"
php -S localhost:8000
```

Then:
1. Import schema.sql via phpMyAdmin
2. Run: php seed_mysql.php
3. Open: http://localhost:8000/

---

## 🗺️ Site Map & URLs

### Public Pages
- `/` or `/index.php` - Home page with role selection

### Unified Login
- `/login.php` - Login for applicants (auto-detects role)

### Applicant Routes
- `/applicant_index.php` - Register or login
- `/dashboard.php` - Applicant dashboard
- `/jobs.php` - Search and filter jobs
- `/apply.php?job_id=X` - Apply for job
- `/profile.php` - Edit profile
- `/skills.php` - Manage skills
- `/history.php` - Application history
- `/logout.php` - Logout

### Recruiter Routes
- `/recruiter_index.php` - Register or login
- `/recruiter_dashboard.php` - Recruiter dashboard
- `/recruiter_post_job.php` - Post new job
- `/recruiter_jobs.php` - View posted jobs
- `/recruiter_edit_job.php?job_id=X` - Edit job
- `/recruiter_applicants.php?job_id=X` - View applicants
- `/recruiter_company.php` - Edit company profile
- `/recruiter_logout.php` - Logout

### Admin Routes
- `/admin_index.php` - Admin login
- `/admin_dashboard.php` - Admin dashboard
- `/admin_users.php` - Manage users
- `/admin_companies.php` - Manage companies
- `/admin_logout.php` - Logout

---

## ⚡ Key Features Quick Reference

### For Applicants
- ✅ Search jobs by title/location/skills
- ✅ Apply with cover letter
- ✅ Update profile & skills
- ✅ Track application status

### For Recruiters
- ✅ Post jobs with required skills
- ✅ Review applicant profiles
- ✅ Update application status
- ✅ Manage company information

### For Admins
- ✅ Create users (any role)
- ✅ Delete users
- ✅ Manage companies
- ✅ View system statistics

---

## 📊 Database

```
Database: job_portal_db
Tables: users, applicants, recruiters, companies, jobs, 
        applications, skills, applicant_skills, job_skills
Views: admin_dashboard_summary, company_job_application_summary
```

---

## 🔑 Credentials After Seeding

| User | Username | Password | Role |
|------|----------|----------|------|
| Admin User | admin | admin123 | admin |
| Test Applicant | applicant1 | password123 | applicant |
| Test Recruiter | recruiter1 | password123 | recruiter |

---

## ✅ Verification Checklist

After setup, verify these work:

- [ ] Home page loads
- [ ] Admin can login and see dashboard
- [ ] Applicant can login and search jobs
- [ ] Applicant can apply for jobs
- [ ] Recruiter can login and post jobs
- [ ] Recruiter can view applications
- [ ] Jobs appear in search
- [ ] Application status updates work
- [ ] Logout works

---

## 🆘 If Something Doesn't Work

1. **Check XAMPP/MySQL is running**
   ```
   XAMPP Control Panel → Apache & MySQL should be GREEN
   ```

2. **Verify database exists**
   - Go to http://localhost/phpmyadmin/
   - Look for `job_portal_db` in left sidebar
   - Check tables exist (users, jobs, etc.)

3. **Re-run setup**
   ```bash
   # Clear database
   mysql -u root -p < schema.sql
   
   # Re-seed data
   php seed_mysql.php
   ```

4. **Check PHP error log**
   ```
   C:\xampp\apache\logs\error.log
   ```

---

## 📝 Important Files

- `config.php` - Database connection (edit if needed)
- `schema.sql` - Database structure (run first!)
- `seed_mysql.php` - Create test data (run second!)
- `index.php` - Landing page
- `style.css` - All styling

---

## 🎯 Test Login Flows

### As Admin
1. Go to `/admin_index.php`
2. Username: `admin`
3. Password: `admin123`
4. Create new users or companies

### As Applicant
1. Go to `/applicant_index.php`
2. Login: `applicant1` / `password123`
3. Search jobs → Apply → Check history

### As Recruiter
1. Go to `/recruiter_index.php`
2. Login: `recruiter1` / `password123`
3. Post job → View applicants → Update status

---

## 🚀 You're Ready!

Everything is configured and ready to run. Just:

1. ✅ Import schema.sql
2. ✅ Run seed_mysql.php
3. ✅ Start your server
4. ✅ Open http://localhost/Job-Portal-Application-Tracking-System-/

**That's it! Enjoy the fully functional Job Portal!**
