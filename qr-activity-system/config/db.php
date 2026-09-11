<?php
/**
 * Database connection configuration.
 * Adjust these values to match your XAMPP / MySQL setup.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // default XAMPP MySQL password is empty
define('DB_NAME', 'qr_activity_system');

// Create a mysqli connection reused across all pages
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
