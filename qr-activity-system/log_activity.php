<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

$qr_value    = trim($_POST['qr_value'] ?? '');
$facility_id = (int)($_POST['facility_id'] ?? 0);
$scan_type   = $_POST['scan_type'] ?? '';

if ($qr_value === '' || $facility_id <= 0 || !in_array($scan_type, ['time_in', 'time_out'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid scan request.']);
    exit;
}

// Look up the student by their QR token
$stmt = $conn->prepare('SELECT student_id, full_name FROM students WHERE qr_code_value = ?');
$stmt->bind_param('s', $qr_value);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'QR code not recognized.']);
    exit;
}

$student = $result->fetch_assoc();

$insert = $conn->prepare(
    'INSERT INTO activity_logs (student_id, facility_id, scan_type) VALUES (?, ?, ?)'
);
$insert->bind_param('iis', $student['student_id'], $facility_id, $scan_type);

if ($insert->execute()) {
    $label = $scan_type === 'time_in' ? 'timed in' : 'timed out';
    echo json_encode([
        'success' => true,
        'message' => $student['full_name'] . ' successfully ' . $label . '.'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to log activity: ' . $conn->error]);
}
