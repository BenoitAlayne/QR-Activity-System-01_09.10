# QR Code-Based Student Activity Monitoring and Analytics System

Basic runnable skeleton for the thesis proposal, built with PHP + MySQL (XAMPP).

## What's included

- `database/schema.sql` — creates the database, tables, and sample facilities
- `register.php` — student registration form; generates each student's QR code
- `kiosk.php` — camera-based QR scanner (uses the `html5-qrcode` JS library) for time-in/time-out at a facility
- `log_activity.php` — backend endpoint that records each scan
- `login.php` / `logout.php` — admin authentication
- `dashboard.php` — analytics dashboard (totals, facility usage chart, recent activity table)
- `index.php` — landing page linking everything together
- `config/db.php` — database connection settings
- `includes/functions.php` — shared helpers (QR token generation, auth guard, etc.)

## Setup (XAMPP)

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Copy the whole `qr-activity-system` folder into `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac).
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`), go to the **Import** tab, and import `database/schema.sql`. This creates the `qr_activity_system` database with sample facilities.
4. Create your admin account:
   - Visit `http://localhost/qr-activity-system/generate_admin_hash.php` in your browser — it will print a password hash.
   - In phpMyAdmin, run:
     ```sql
     INSERT INTO admins (username, password_hash, full_name)
     VALUES ('admin', 'PASTE_HASH_HERE', 'System Administrator');
     ```
   - Delete `generate_admin_hash.php` afterward (it shouldn't stay on a live server).
5. Visit `http://localhost/qr-activity-system/` in your browser.

## How it works

1. **Register** a student → the system generates a random QR token and shows/prints the QR image.
2. At a **Kiosk**, staff pick the facility + scan type (time in/out), and a student scans their QR with the camera.
3. Every scan is saved to `activity_logs`.
4. The **Dashboard** (admin login required) shows total students, today's scans, facility usage chart, and a recent-activity table.

## Notes on scope (for your defense)

- QR images are generated via a free public API (`api.qrserver.com`) for simplicity. For an offline/production version, swap this for a local PHP QR code library (e.g. `endroid/qr-code` via Composer) so the system doesn't depend on internet access.
- This is intentionally a minimal skeleton: no CSV export, no per-facility occupancy limits, no email notifications yet. These match your "Expected Output" list and are natural next features to build on top of this foundation.
- Passwords are hashed with PHP's built-in `password_hash()`/`password_verify()` — never store plain-text passwords.
