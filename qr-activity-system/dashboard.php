<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_admin_login();

// --- Summary stats ---
$total_students = $conn->query('SELECT COUNT(*) AS c FROM students')->fetch_assoc()['c'];
$today_scans    = $conn->query("SELECT COUNT(*) AS c FROM activity_logs WHERE DATE(scan_timestamp) = CURDATE()")->fetch_assoc()['c'];
$total_scans    = $conn->query('SELECT COUNT(*) AS c FROM activity_logs')->fetch_assoc()['c'];

// --- Facility usage counts (for chart) ---
$facility_usage = $conn->query(
    'SELECT f.facility_name, COUNT(l.log_id) AS visits
     FROM facilities f
     LEFT JOIN activity_logs l ON f.facility_id = l.facility_id
     GROUP BY f.facility_id
     ORDER BY visits DESC'
);
$facility_labels = [];
$facility_counts  = [];
while ($row = $facility_usage->fetch_assoc()) {
    $facility_labels[] = $row['facility_name'];
    $facility_counts[] = (int)$row['visits'];
}

// --- Recent activity logs ---
$recent_logs = $conn->query(
    'SELECT s.full_name, s.student_number, f.facility_name, l.scan_type, l.scan_timestamp
     FROM activity_logs l
     JOIN students s   ON l.student_id  = s.student_id
     JOIN facilities f ON l.facility_id = f.facility_id
     ORDER BY l.scan_timestamp DESC
     LIMIT 20'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard | QR Activity Monitoring System</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
    <header>
        <h1>Analytics Dashboard</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
            <a href="kiosk.php">Kiosk</a>
            <a href="logout.php">Log Out (<?= h($_SESSION['admin_name'] ?? 'Admin') ?>)</a>
        </nav>
    </header>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-number"><?= (int)$total_students ?></span>
            <span class="stat-label">Registered Students</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?= (int)$today_scans ?></span>
            <span class="stat-label">Scans Today</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?= (int)$total_scans ?></span>
            <span class="stat-label">Total Scans Logged</span>
        </div>
    </div>

    <div class="card">
        <h2>Facility Usage</h2>
        <canvas id="facilityChart" height="100"></canvas>
    </div>

    <div class="card">
        <h2>Recent Activity</h2>
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Student #</th>
                    <th>Facility</th>
                    <th>Type</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $recent_logs->fetch_assoc()): ?>
                <tr>
                    <td><?= h($log['full_name']) ?></td>
                    <td><?= h($log['student_number']) ?></td>
                    <td><?= h($log['facility_name']) ?></td>
                    <td><?= $log['scan_type'] === 'time_in' ? 'Time In' : 'Time Out' ?></td>
                    <td><?= h($log['scan_timestamp']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
new Chart(document.getElementById('facilityChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($facility_labels) ?>,
        datasets: [{
            label: 'Total Visits',
            data: <?= json_encode($facility_counts) ?>,
            backgroundColor: '#4a7fdb'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
</body>
</html>
