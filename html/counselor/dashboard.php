<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'counselor') {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch counselor details
    $stmt = $pdo->prepare("SELECT * FROM counselors WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $counselor = $stmt->fetch();

    if (!$counselor) {
        // Handle case where counselor record doesn't exist
        die("Counselor record not found.");
    }
    $counselor_id = $counselor['counselor_id'];

    // Stats: Today's Schedules
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE counselor_id = ? AND appointment_date = CURDATE() AND status != 'Cancelled'");
    $stmt->execute([$counselor_id]);
    $today_schedules = $stmt->fetchColumn();

    // Stats: Total Sessions (Completed)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE counselor_id = ? AND status = 'Completed'");
    $stmt->execute([$counselor_id]);
    $total_sessions = $stmt->fetchColumn();

    // Stats: Total Students (Unique students assigned/counseled)
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT student_id) FROM appointments WHERE counselor_id = ?");
    $stmt->execute([$counselor_id]);
    $total_students = $stmt->fetchColumn();

    // Upcoming Appointments (Next 3)
    $stmt = $pdo->prepare("
        SELECT a.*, s.first_name, s.last_name 
        FROM appointments a 
        JOIN students s ON a.student_id = s.student_id 
        WHERE a.counselor_id = ? AND a.appointment_date >= CURDATE() AND a.status = 'Confirmed'
        ORDER BY a.appointment_date ASC, a.appointment_time ASC 
        LIMIT 3
    ");
    $stmt->execute([$counselor_id]);
    $upcoming_appointments = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Counselor Dashboard - Guidance Counseling Management System</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css" />
    <link
      rel="stylesheet"
      href="../../assets/styles/layouts/counselor-dashboard.css"
    />
  </head>
  <body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <h4><i class="fas fa-graduation-cap"></i> Guidance Counseling</h4>
      </div>
      <ul class="sidebar-menu">
        <li>
          <a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li>
          <a href="./my-schedule.php" class=""><i class="fas fa-calendar-alt"></i> My Schedule</a>
        </li>
        <li>
          <a href="./session-management.php"
            ><i class="fas fa-clipboard-list"></i> Session Management</a
          >
        </li>
        <li>
          <a href="./student-record.php" class="notif-referrals"
            ><i class="fas fa-user-graduate"></i> Student Record</a
          >
        </li>
        <li>
          <a href="./profile.php"><i class="fa-solid fa-user-circle"></i> Profile</a>
        </li>
        <li>
          <a href="../../logout.php"
            ><i class="fas fa-sign-out-alt"></i> Logout</a
          >
        </li>
      </ul>
    </aside>
    
    <button class="sidebar-toggle" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <h2><i class="fas fa-chart-line"></i> Counselor Dashboard</h2>
        <div class="user-info">
          <div>
            <strong style="color: var(--navy-blue)">Dr. <?php echo htmlspecialchars($counselor['first_name'] . ' ' . $counselor['last_name']); ?></strong>
            <p style="margin: 0; font-size: 12px; color: #6c757d">
              <?php echo htmlspecialchars($counselor['specialization'] ?? 'Guidance Counselor'); ?>
            </p>
          </div>
          <div class="user-avatar"><?php echo substr($counselor['first_name'], 0, 1) . substr($counselor['last_name'], 0, 1); ?></div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-container">
        <div class="stat-card">
          <div class="stat-icon schedule">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="stat-info">
            <h3 id="todaySchedules"><?php echo $today_schedules; ?></h3>
            <p>Today's Schedules</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon sessions">
            <i class="fas fa-comments"></i>
          </div>
          <div class="stat-info">
            <h3 id="totalSessions"><?php echo $total_sessions; ?></h3>
            <p>Total Sessions</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon students">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-info">
            <h3 id="totalStudents"><?php echo $total_students; ?></h3>
            <p>Total Students</p>
          </div>
        </div>
      </div>

      <!-- Upcoming Appointments -->
      <div class="appointments-section">
        <div class="section-header">
          <h3><i class="fas fa-clock"></i> Upcoming Appointments</h3>
          <span style="color: #6c757d; font-size: 14px"
            >Next 3 appointments</span
          >
        </div>
        <div id="appointmentsList">
            <?php if (count($upcoming_appointments) > 0): ?>
                <?php foreach ($upcoming_appointments as $appt): ?>
                    <div class="appointment-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5><?php echo htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']); ?></h5>
                                <p class="mb-0 text-muted">
                                    <i class="fas fa-clock me-2"></i>
                                    <?php echo date('F d, Y', strtotime($appt['appointment_date'])) . ' at ' . date('h:i A', strtotime($appt['appointment_time'])); ?>
                                </p>
                                <small class="text-primary"><?php echo htmlspecialchars($appt['purpose']); ?></small>
                            </div>
                            <span class="badge bg-primary">Confirmed</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted p-4">No upcoming appointments.</p>
            <?php endif; ?>
        </div>
        <button class="see-more-btn" onclick="window.location.href='my-schedule.php'">
          See More Appointments <i class="fas fa-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- Modal and Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <!-- <script src="../../assets/scripts/counselor-dashboard.js"></script> -->
  </body>
</html>
