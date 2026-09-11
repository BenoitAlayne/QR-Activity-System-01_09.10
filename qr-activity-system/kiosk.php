<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Load active facilities for the dropdown
$facilities = $conn->query('SELECT facility_id, facility_name FROM facilities WHERE is_active = 1 ORDER BY facility_name');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Kiosk | QR Activity Monitoring System</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body>
<div class="container">
    <header>
        <h1>Facility Kiosk</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
            <a href="login.php">Admin Login</a>
        </nav>
    </header>

    <div class="card">
        <label>Select this kiosk's facility:
            <select id="facility_id" required>
                <option value="">-- Select Facility --</option>
                <?php while ($row = $facilities->fetch_assoc()): ?>
                    <option value="<?= (int)$row['facility_id'] ?>"><?= h($row['facility_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </label>

        <label>Scan type:
            <select id="scan_type" required>
                <option value="time_in">Time In</option>
                <option value="time_out">Time Out</option>
            </select>
        </label>

        <div id="reader" style="width: 320px; margin: 20px auto;"></div>
        <div id="result" class="alert" style="display:none;"></div>
    </div>
</div>

<script>
const resultBox = document.getElementById('result');

function showResult(message, isError = false) {
    resultBox.style.display = 'block';
    resultBox.className = 'alert ' + (isError ? 'alert-error' : 'alert-success');
    resultBox.textContent = message;
    setTimeout(() => { resultBox.style.display = 'none'; }, 3000);
}

function onScanSuccess(decodedText) {
    const facilityId = document.getElementById('facility_id').value;
    const scanType = document.getElementById('scan_type').value;

    if (!facilityId) {
        showResult('Please select a facility first.', true);
        return;
    }

    fetch('log_activity.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            qr_value: decodedText,
            facility_id: facilityId,
            scan_type: scanType
        })
    })
    .then(res => res.json())
    .then(data => showResult(data.message, !data.success))
    .catch(() => showResult('Network error. Please try again.', true));
}

// Note: onScanFailure fires continuously while no QR is in view — intentionally left empty (no-op).
const html5QrCode = new Html5Qrcode("reader");
html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 220 },
    onScanSuccess,
    () => {}
);
</script>
</body>
</html>
