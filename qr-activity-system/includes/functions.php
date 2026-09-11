<?php
/**
 * Shared helper functions.
 */

session_start();

/**
 * Generate a random, unique token to embed in a student's QR code.
 * This value (not the raw student number) is what gets encoded,
 * so the QR itself doesn't leak readable student info.
 */
function generate_qr_token(): string {
    return bin2hex(random_bytes(16)); // 32-character hex string
}

/**
 * Simple redirect helper.
 */
function redirect(string $path): void {
    header("Location: $path");
    exit;
}

/**
 * Require an admin to be logged in; otherwise send them to the login page.
 */
function require_admin_login(): void {
    if (empty($_SESSION['admin_id'])) {
        redirect('login.php');
    }
}

/**
 * Escape output for safe HTML display.
 */
function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
