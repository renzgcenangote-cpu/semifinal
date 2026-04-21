<?php
session_start();
include "db.php";
if(!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
$teacher_id = $_SESSION['teacher_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GatePass | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fc; }
        .sidebar { height: 100vh; width: 250px; position: fixed; background: #222e3c; color: #fff; padding-top: 20px; }
        .main { margin-left: 250px; padding: 30px; }
        .nav-link { color: #adb5bd; margin: 5px 15px; border-radius: 5px; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: #fff; }
        .stat-card { border: none; border-radius: 10px; box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.1); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="px-4 mb-4"><h4 class="fw-bold">GatePass</h4></div>
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="students.php" class="nav-link"><i class="bi bi-people me-2"></i> Students</a>
            <a href="settings.php" class="nav-link"><i class="bi bi-gear me-2"></i> Settings</a>
            <a href="archive.php" class="nav-link"><i class="bi bi-clock-history me-2"></i> History</a>
            <hr>
            <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </nav>
    </div>

    <div class="main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-gray-800">Attendance Overview</h3>
            <span class="badge bg-white text-dark shadow-sm p-2">Today: <?php echo date('M d, Y'); ?></span>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card border-start border-success border-4 p-3">
                    <div class="small fw-bold text-success mb-1">PRESENT</div>
                    <div class="h3 fw-bold mb-0" id="count-present">0</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card border-start border-warning border-4 p-3">
                    <div class="small fw-bold text-warning mb-1">LATE</div>
                    <div class="h3 fw-bold mb-0" id="count-late">0</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card border-start border-danger border-4 p-3">
                    <div class="small fw-bold text-danger mb-1">ABSENT</div>
                    <div class="h3 fw-bold mb-0" id="count-absent">0</div>
                </div>
            </div>
        </div>

        <div class="card stat-card p-4">
            <h5 class="fw-bold mb-3 text-gray-800">Live Scans</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr><th>Student Name</th><th>Status</th><th>Time In</th></tr>
                    </thead>
                    <tbody id="attendance-body">
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function refreshData() {
            $.get('fetch_attendance.php', function(data) { $('#attendance-body').html(data); });
            $.getJSON('fetch_stats.php', function(data) {
                $('#count-present').text(data.present);
                $('#count-late').text(data.late);
                $('#count-absent').text(data.absent);
            });
        }
        setInterval(refreshData, 2000);
        refreshData();
    </script>
</body>
</html>