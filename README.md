# SuperviseHub

A complete PHP/MySQL student-supervisor session, project monitoring and progress-tracking web application for the Department of Computer Science, Crawford University.

## Major modules

- Secure role-based authentication for students, supervisors and project coordinators
- Supervisor assignment and project register
- Session requests, approval/rescheduling, structured feedback, decisions and action items
- Weighted milestone submission, revision and approval workflow
- Automatic project progress calculation
- Project document upload and version history
- Student-supervisor project messaging
- Supervisor workload, cohort progress and at-risk student analytics
- Responsive UI for desktop, tablet and mobile devices

## Requirements

- PHP 8.1 or newer with PDO and PDO MySQL
- MySQL 8.0+ or MariaDB 10.6+
- Apache/Nginx, XAMPP/WAMP/LAMP, or the PHP built-in server

## MySQL installation

1. Copy the project into your web root, for example `htdocs/supervisehub`.
2. Import `database/schema.sql` into MySQL.
3. Import `database/seed.sql` to install representative demo records.
4. Set the environment variables shown in `.env.example`, or edit the defaults in `config/database.php`.
5. Ensure the `uploads` directory is writable by the web server.
6. Start the application and open `http://localhost/supervisehub/`.

### Built-in PHP server

```bash
cd supervisehub
export APP_URL=http://localhost:8000
export DB_DRIVER=mysql
export DB_DATABASE=supervisehub
export DB_USERNAME=root
export DB_PASSWORD='your-password'
php -S 127.0.0.1:8000
```

## SQLite demonstration mode

SQLite is included only as a portable demonstration option; the production design and supplied relational schema target MySQL.

```bash
cd supervisehub
DB_DRIVER=sqlite DB_DATABASE="$PWD/storage/supervisehub.sqlite" php demo_init.php
APP_URL=http://localhost:8000 DB_DRIVER=sqlite DB_DATABASE="$PWD/storage/supervisehub.sqlite" php -S 127.0.0.1:8000
```

## Demo accounts

All demo passwords are `Password123!`.

- Student: `student@demo.test`
- Supervisor: `supervisor@demo.test`
- Project coordinator: `admin@demo.test`

## Security controls implemented

- PDO prepared statements
- PHP password hashing and verification
- Session regeneration after authentication
- CSRF tokens on state-changing forms
- Role-level authorisation checks
- Output escaping to reduce cross-site scripting risk
- Upload extension and size validation
- Foreign-key relationships and indexed monitoring fields

For production deployment, also enforce HTTPS, configure secure cookie flags, store environment values outside the web root, add malware scanning for uploads, and implement routine encrypted backups.

## Additional protected workflows

- Supervisors can open a detailed portfolio for each assigned project, including milestone, session and document history.
- Project documents are served through `download.php`, which verifies the current user's project relationship before returning a file.
- Supervisor messaging supports selection among multiple assigned student projects.
- `database/setup.sql` provides a one-step schema-and-demo-data import.
