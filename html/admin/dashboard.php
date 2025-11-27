<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

try {
    // Stats: Active Users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $active_users = $stmt->fetchColumn();

    // Stats: Appointments Today
    $stmt = $pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()");
    $appointments_today = $stmt->fetchColumn();

    // Stats: Pending Requests
    $stmt = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Pending'");
    $pending_requests = $stmt->fetchColumn();

    // Recent Requests (Latest 5 Pending)
    $stmt = $pdo->query("
        SELECT a.*, s.first_name, s.last_name, s.student_number 
        FROM appointments a 
        JOIN students s ON a.student_id = s.student_id 
        WHERE a.status = 'Pending' 
        ORDER BY a.created_at DESC 
        LIMIT 5
    ");
    $recent_requests = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Guidance Counseling System</title>
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
      href="../../assets/styles/layouts/admin-dashboard.css"
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
          <a href="./counselors.php" class=""><i class="fas fa-user-tie"></i> Counselors</a>
        </li>
        <li>
          <a href="./appointments.php"
            ><i class="fa-solid fa-calendar-check"></i> Appointments</a
          >
        </li>
        <li>
          <a href="./notifications.php" class="notif-referrals"
            ><i class="fas fa-bell"></i> Notifications & Referrals</a
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
    <div class="main-content" id="mainContent">
      <!-- Dashboard Content -->
      <div class="dashboard-content">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">
          Welcome back! Here's what's happening today.
        </p>

        <!-- Metrics Row -->
        <div class="row g-4">
          <div class="col-md-6 col-lg-4">
            <div class="metric-card">
              <div class="metric-icon blue">
                <i class="fas fa-users"></i>
              </div>
              <div class="metric-value"><?php echo $active_users; ?></div>
              <div class="metric-label">Active Users</div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="metric-card">
              <div class="metric-icon green">
                <i class="fas fa-calendar-check"></i>
              </div>
              <div class="metric-value"><?php echo $appointments_today; ?></div>
              <div class="metric-label">Appointments Today</div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="metric-card">
              <div class="metric-icon orange">
                <i class="fas fa-clock"></i>
              </div>
              <div class="metric-value"><?php echo $pending_requests; ?></div>
              <div class="metric-label">Pending Requests</div>
            </div>
          </div>
        </div>

        <!-- New Requests Section -->
        <div class="request-card">
          <div class="request-header">
            <h5><i class="fas fa-inbox me-2"></i>New Requests</h5>
            <a href="appointments.php" class="see-more-btn">See More</a>
          </div>
          <div id="requestsContainer">
            <?php if (count($recent_requests) > 0): ?>
                <?php foreach ($recent_requests as $req): ?>
                    <div class="request-item border-bottom p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($req['first_name'] . ' ' . $req['last_name']); ?></h6>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-calendar me-1"></i> <?php echo date('M d, Y', strtotime($req['appointment_date'])); ?>
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-clock me-1"></i> <?php echo date('h:i A', strtotime($req['appointment_time'])); ?>
                                </p>
                                <small class="text-primary"><?php echo htmlspecialchars($req['purpose']); ?></small>
                            </div>
                            <span class="badge bg-warning text-dark">Pending</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted p-4">No new requests.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <!-- <script src="../../assets/scripts/admin-dashboard.js"></script> -->
  </body>
</html>
