-- ============================================================
-- QR Code-Based Student Activity Monitoring and Analytics System
-- Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS qr_activity_system;
USE qr_activity_system;

-- ------------------------------------------------------------
-- Table: students
-- Stores registered student info and their unique QR code value
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS students (
    student_id      INT AUTO_INCREMENT PRIMARY KEY,
    student_number  VARCHAR(20)  NOT NULL UNIQUE,
    full_name       VARCHAR(100) NOT NULL,
    course          VARCHAR(100) NOT NULL,
    year_level      VARCHAR(20)  NOT NULL,
    email           VARCHAR(100),
    qr_code_value   VARCHAR(64)  NOT NULL UNIQUE,  -- random token encoded in the QR image
    date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Table: facilities
-- Campus locations that can be monitored (library, lab, clinic, etc.)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS facilities (
    facility_id     INT AUTO_INCREMENT PRIMARY KEY,
    facility_name   VARCHAR(100) NOT NULL UNIQUE,
    facility_type   VARCHAR(50)  NOT NULL,        -- e.g. Library, Laboratory, Clinic
    is_active       TINYINT(1) DEFAULT 1
);

-- ------------------------------------------------------------
-- Table: activity_logs
-- Every QR scan event (a "time in" or "time out" at a facility)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS activity_logs (
    log_id          INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT NOT NULL,
    facility_id     INT NOT NULL,
    scan_type       ENUM('time_in', 'time_out') NOT NULL,
    scan_timestamp  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id)  REFERENCES students(student_id)   ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(facility_id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Table: admins
-- Staff/admin accounts that can log in to the dashboard
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    admin_id        INT AUTO_INCREMENT PRIMARY KEY,
    username        VARCHAR(50) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    full_name       VARCHAR(100),
    date_created    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Seed data: a few sample facilities so the kiosk has options
-- ------------------------------------------------------------
INSERT INTO facilities (facility_name, facility_type) VALUES
('Main Library', 'Library'),
('Computer Laboratory 1', 'Laboratory'),
('Clinic', 'Clinic'),
('Guidance Office', 'Administrative Office'),
('Registrar''s Office', 'Administrative Office');

-- ------------------------------------------------------------
-- Seed data: default admin account
-- Username: admin | Password: admin123
-- (password_hash below is generated with PHP password_hash('admin123', PASSWORD_DEFAULT))
-- Replace this after first login for real use.
-- ------------------------------------------------------------
-- NOTE: run generate_admin_hash.php (included) once to create a fresh hash,
-- then INSERT it here or directly into phpMyAdmin.
