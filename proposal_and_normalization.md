# Job Portal Application Tracking System

## 1. Proposal

### i. Introduction and brief description
The system is an online Job Portal and Application Tracking System. It helps applicants search and apply for jobs while enabling recruiters to post jobs, manage applicants, and update application status. The system solves the problem of manual candidate tracking and fragmented communication between job seekers and recruiters.

### ii. System functionality
- Applicant registration and login
- Applicant profile management
- Applicant skill management
- Job search and filtering by keywords and skills
- Job application with cover letter submission
- Application history tracking
- Recruiter registration and login
- Company profile management
- Job posting, editing, and deletion
- Applicant review and application status updates
- Dashboard statistics for recruiters

### iii. Users of the system
- Applicant
  - Register and login
  - Manage personal profile
  - Manage skills
  - Search and filter jobs
  - Apply for jobs
  - View application history

- Recruiter
  - Register and login
  - Create and manage company profile
  - Post jobs
  - Edit or delete job listings
  - Review applicants
  - Update application status

### iv. Technologies used
- Front-End: HTML, CSS, Bootstrap, JavaScript
- Back-End: PHP
- Database: MySQL

### v. Scope of the System
Covers:
- Applicant and recruiter workflows
- SQL database implementation with normalized tables
- Views for application history and recruiter application summaries
- Dynamic user input handling via prepared statements
- Transaction handling for multi-step operations

Does not cover:
- Email notifications
- Resume upload / file storage
- Multi-factor authentication
- Advanced analytics beyond dashboard counts

### vi. Assumptions
- Only applicants and recruiters have separate login roles.
- Recruiters are linked to one company.
- Skills are stored centrally and linked via join tables.

## 2. Normalization

### Identified entities and tables
- users
- applicants
- recruiters
- companies
- jobs
- applications
- skills
- applicant_skills
- job_skills

### Functional Dependencies

#### users
- id -> username, password, role, created_at
- username -> password, role, created_at

#### applicants
- id -> user_id, full_name, email, phone, headline, bio, created_at
- user_id -> full_name, email, phone, headline, bio, created_at

#### recruiters
- id -> user_id, company_id, full_name, email, phone, created_at
- user_id -> company_id, full_name, email, phone, created_at

#### companies
- id -> name, email, industry, website, description, created_at

#### jobs
- id -> company_id, recruiter_id, title, description, location, salary, posted_at

#### applications
- id -> applicant_id, job_id, status, cover_letter, applied_at
- (applicant_id, job_id) -> status, cover_letter, applied_at

#### skills
- id -> name
- name -> id

#### applicant_skills
- (applicant_id, skill_id) -> (none additional)

#### job_skills
- (job_id, skill_id) -> (none additional)

### Normalization steps

#### First Normal Form (1NF)
All tables contain atomic values and have a primary key. Arrays and multi-valued skill lists are moved to separate join tables (`applicant_skills`, `job_skills`).

#### Second Normal Form (2NF)
Non-key columns depend only on the full primary key in tables with composite keys. For example, in `job_skills`, no non-key column depends on only `job_id` or `skill_id`.

#### Third Normal Form (3NF)
All non-key columns depend only on the primary key and there are no transitive dependencies. For example, company data is stored in `companies`, separate from `recruiters`.

#### Boyce-Codd Normal Form (BCNF)
Every determinant is a candidate key in every relation. All tables are in BCNF because:
- `users`: username is candidate key and determines attributes.
- `applicants`: user_id is unique and determines applicant details.
- `recruiters`: user_id is unique and determines recruiter details.
- `skills`: name is unique.
- `jobs`, `applications`, `applicant_skills`, `job_skills` all use primary keys with no extra determinants.

## 3. Entity-Relationship Diagram

Entities:
- `users`
- `applicants`
- `recruiters`
- `companies`
- `jobs`
- `applications`
- `skills`
- `applicant_skills`
- `job_skills`

Relationships:
- `users` 1-to-1 with `applicants`
- `users` 1-to-1 with `recruiters`
- `recruiters` many-to-1 with `companies`
- `companies` 1-to-many with `jobs`
- `jobs` 1-to-many with `applications`
- `applicants` 1-to-many with `applications`
- `applicants` many-to-many with `skills` via `applicant_skills`
- `jobs` many-to-many with `skills` via `job_skills`

## 4. Views

- `applicant_application_history`
- `recruiter_application_details`

These views simplify history and recruiter applicant queries while still using joins and built-in SQL functions.

## 5. SQL features used

- Joins: INNER JOIN, LEFT JOIN
- Aggregate functions: COUNT, SUM, GROUP_CONCAT
- Built-in functions: DATE_FORMAT, IFNULL
- Dynamic input handling: prepared statements via PDO
- Transaction handling: `begin_transaction`, `commit_transaction`, `rollback_transaction`
