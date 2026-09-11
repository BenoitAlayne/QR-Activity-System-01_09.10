<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$success = false;
$error   = '';
$qr_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_number = trim($_POST['student_number'] ?? '');
    $full_name      = trim($_POST['full_name'] ?? '');
    $course         = trim($_POST['course'] ?? '');
    $year_level     = trim($_POST['year_level'] ?? '');
    $email          = trim($_POST['email'] ?? '');

    if ($student_number === '' || $full_name === '' || $course === '' || $year_level === '') {
        $error = 'Please fill in all required fields.';
    } else {
        // Check for duplicate student number
        $check = $conn->prepare('SELECT student_id FROM students WHERE student_number = ?');
        $check->bind_param('s', $student_number);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = 'A student with that student number is already registered.';
        } else {
            $qr_value = generate_qr_token();

            $stmt = $conn->prepare(
                'INSERT INTO students (student_number, full_name, course, year_level, email, qr_code_value)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('ssssss', $student_number, $full_name, $course, $year_level, $email, $qr_value);

            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = 'Registration failed: ' . $conn->error;
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Registration | QR Activity Monitoring System</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Student Registration</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="kiosk.php">Kiosk</a>
            <a href="login.php">Admin Login</a>
        </nav>
    </header>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <p>Student registered successfully! Here is their QR code:</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?= urlencode($qr_value) ?>"
                 alt="Student QR Code">
            <p><strong>QR token:</strong> <?= h($qr_value) ?></p>
            <p><em>Print or save this QR code — the student presents it at any kiosk to log activity.</em></p>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="card">
        <label>Student Number *
            <input type="text" name="student_number" required>
        </label>
        <label>Full Name *
            <input type="text" name="full_name" required>
        </label>
        <label>Course *
            <input type="text" name="course" required>
        </label>
        <label>Year Level *
            <select name="year_level" required>
                <option value="">-- Select --</option>
                <option>1st Year</option>
                <option>2nd Year</option>
                <option>3rd Year</option>
                <option>4th Year</option>
            </select>
        </label>
        <label>Email
            <input type="email" name="email">
        </label>
        <button type="submit">Register Student</button>
    </form>
</div>
</body>
</html>
