<?php
session_start();
require_once '../../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch student details
    $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $student = $stmt->fetch();

    if (!$student) {
        // Handle case where student record doesn't exist yet (shouldn't happen if registration works)
        die("Student record not found.");
    }

    // Fetch upcoming appointments
    $stmt = $pdo->prepare("
        SELECT a.*, c.first_name as counselor_first, c.last_name as counselor_last 
        FROM appointments a 
        LEFT JOIN counselors c ON a.counselor_id = c.counselor_id 
        WHERE a.student_id = ? AND a.appointment_date >= CURDATE() 
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
    $stmt->execute([$student['student_id']]);
    $upcoming_appointments = $stmt->fetchAll();

    // Fetch next session (first one)
    $next_session = $upcoming_appointments[0] ?? null;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Dashboard - Guidance Counseling</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
      rel="stylesheet"
    />

    <link
      rel="stylesheet"
      href="../../assets/styles/layouts/stud-dashboard.css"
    />
    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css" />
  </head>
  <body>
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <h4><i class="fas fa-graduation-cap"></i> Guidance Counseling</h4>
      </div>
      <ul class="sidebar-menu">
        <li>
          <a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li>
          <a href="./book-appointment.php" class=""
            ><i class="fas fa-calendar-check"></i> Book Appointment</a
          >
        </li>
        <li>
          <a href="./notifications.php"
            ><i class="fas fa-bell"></i> Notification</a
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

    <button class="sidebar-toggle" id="toggleBtn" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>

    <div class="main-content" id="mainContent">
      <div class="header">
        <h2><i class="fas fa-th-large"></i> Student Dashboard</h2>
        <p class="text-muted mb-0">
          Welcome back, <?php echo htmlspecialchars($student['first_name']); ?>! Here's your counseling overview
        </p>
      </div>

      <div class="row">
        <div class="col-lg-8 col-md-12">
          <div class="dashboard-card appointment-status">
            <h5><i class="fas fa-calendar-check"></i> My Appointment Status</h5>
            <hr style="border-color: rgba(255, 255, 255, 0.3)" />
            <div class="row mt-4">
              <?php if ($next_session): ?>
                  <div class="col-md-6">
                    <h6><i class="fas fa-clock"></i> Next Session</h6>
                    <h4><?php echo date('F d, Y', strtotime($next_session['appointment_date'])); ?></h4>
                    <p class="mb-1">
                      <i class="fas fa-stopwatch"></i> <?php echo date('h:i A', strtotime($next_session['appointment_time'])); ?>
                    </p>
                    <p><i class="fas fa-user-tie"></i> 
                        <?php echo $next_session['counselor_first'] ? 'Dr. ' . htmlspecialchars($next_session['counselor_first'] . ' ' . $next_session['counselor_last']) : 'Pending Assignment'; ?>
                    </p>
                  </div>
                  <div class="col-md-6 text-md-end">
                    <p class="mb-2">Status:</p>
                    <span class="status-badge status-<?php echo strtolower($next_session['status']); ?>"><?php echo htmlspecialchars($next_session['status']); ?></span>
                    <p class="mt-3 mb-0">
                      <small
                        ><i class="fas fa-info-circle"></i> Guidance Office</small
                      >
                    </p>
                  </div>
              <?php else: ?>
                  <div class="col-12 text-center">
                      <p>No upcoming appointments.</p>
                      <a href="book-appointment.php" class="btn btn-light btn-sm">Book Now</a>
                  </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Session History -->
          <div class="dashboard-card">
            <div class="card-header-custom">
              <h5><i class="fas fa-history"></i> My Session History</h5>
            </div>
            <div class="session-container" id="sessionContainer">
                <!-- We can fetch past sessions here if needed, for now just placeholder or empty -->
                 <p class="text-center text-muted mt-3">No session history available yet.</p>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4 col-md-12">
          <!-- Profile Snapshot -->
          <div class="dashboard-card profile-snapshot">
            <div class="card-header-custom">
              <h5><i class="fas fa-user-circle"></i> My Profile</h5>
            </div>
            <div class="">
              <div class="profile-avatar">
                <i class="fas fa-user"></i>
              </div>
              <h5 class="text-center"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h5>
              <p class="text-muted mb-5 text-center"><?php echo htmlspecialchars($student['student_number']); ?></p>
              <p class="text-muted mb-1">
                <span>DEPARTMENT:</span> <?php echo htmlspecialchars($student['course']); // Assuming course maps roughly to dept or we can fetch dept ?>
              </p>
              <p class="text-muted mb-1">
                <span>COURSE:</span> <?php echo htmlspecialchars($student['course']); ?>
              </p>
              <p class="text-muted mb-1"><span>YEAR LEVEL:</span> <?php echo htmlspecialchars($student['year_level']); ?></p>
              <p class="text-muted mb-1"><span>GENDER:</span> <?php echo htmlspecialchars($student['gender']); ?></p>
              <a href="./profile.php" class="btn btn-outline-primary mt-2 w-100">
                <i class="fas fa-edit"></i> Edit Profile
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <!-- <script src="../../assets/scripts/stud-dashboard.js"></script> -->
  </body>
</html>
