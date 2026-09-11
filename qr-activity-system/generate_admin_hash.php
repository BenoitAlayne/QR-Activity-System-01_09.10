<?php
/**
 * Run this once from your browser (e.g. http://localhost/qr-activity-system/generate_admin_hash.php)
 * to generate a password hash, then insert the admin manually:
 *
 *   INSERT INTO admins (username, password_hash, full_name)
 *   VALUES ('admin', '<paste hash here>', 'System Administrator');
 *
 * Delete this file once you're done setting up — it should not stay on a live server.
 */

$password = 'admin123'; // change this to whatever password you want
echo password_hash($password, PASSWORD_DEFAULT);
