<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'counselor') {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch counselor ID
    $stmt = $pdo->prepare("SELECT counselor_id FROM counselors WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $counselor = $stmt->fetch();
    $counselor_id = $counselor['counselor_id'];

    // Fetch appointments
    $stmt = $pdo->prepare("
        SELECT a.appointment_id as id, a.appointment_date as date, a.appointment_time as time, 
               s.first_name, s.last_name, a.purpose as type, a.status 
        FROM appointments a 
        JOIN students s ON a.student_id = s.student_id 
        WHERE a.counselor_id = ?
    ");
    $stmt->execute([$counselor_id]);
    $appointments = $stmt->fetchAll();

    // Format for JS
    $sessionsData = [];
    foreach ($appointments as $appt) {
        $sessionsData[] = [
            'id' => $appt['id'],
            'date' => $appt['date'],
            'time' => date('h:i A', strtotime($appt['time'])),
            'studentName' => $appt['first_name'] . ' ' . $appt['last_name'],
            'type' => $appt['type'],
            'status' => strtolower($appt['status'])
        ];
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Schedule - Guidance Counseling Management System</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css">
    <link rel="stylesheet" href="../../assets/styles/layouts/counselor-my-schedule.css">
  </head>
  <body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <h4><i class="fas fa-graduation-cap"></i> Guidance Counseling</h4>
      </div>
      <ul class="sidebar-menu">
        <li>
          <a href="./dashboard.php" class=""><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li>
          <a href="#" class="active"><i class="fas fa-calendar-alt"></i> My Schedule</a>
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

    <div class="main-content">
      <div class="header">
        <h2><i class="bi bi-calendar-check"></i> My Schedule</h2>
      </div>

      <div class="calendar-container">
        <div class="calendar-header">
          <h3 id="currentMonth"></h3>
          <div class="calendar-nav">
            <button id="prevMonth">
              <i class="bi bi-chevron-left"></i> Previous
            </button>
            <button id="today">Today</button>
            <button id="nextMonth">
              Next <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>

        <div class="calendar">
          <div class="calendar-weekdays">
            <div class="weekday">Sun</div>
            <div class="weekday">Mon</div>
            <div class="weekday">Tue</div>
            <div class="weekday">Wed</div>
            <div class="weekday">Thu</div>
            <div class="weekday">Fri</div>
            <div class="weekday">Sat</div>
          </div>
          <div class="calendar-days" id="calendarDays"></div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="sessionsModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"></h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body" id="modalBody"></div>
        </div>
      </div>
    </div>

    <!-- Inject Data -->
    <script>
        window.sessionsData = <?php echo json_encode($sessionsData); ?>;
        console.log('Debug: User ID:', <?php echo $user_id; ?>);
        console.log('Debug: Counselor ID:', <?php echo $counselor_id ?? 'null'; ?>);
        console.log('Debug: Sessions Data:', window.sessionsData);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <script src="../../assets/scripts/counselor-my-schedule.js"></script>
    
    <!-- AJAX Logic for Confirm/Cancel -->
    <script>
        // Override or extend the functions to add AJAX
        const originalConfirmSession = window.confirmSession;
        const originalCancelSession = window.cancelSession;

        window.confirmSession = function(sessionId) {
            if(confirm("Are you sure you want to confirm this session?")) {
                fetch('update_appointment_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + sessionId + '&status=Confirmed'
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Update local data and re-render
                        const session = sessions.find(s => s.id == sessionId);
                        if(session) {
                            session.status = 'confirmed';
                            renderCalendar(); // Re-render calendar
                            
                            // Also update the modal view if open (simulated by re-opening or manipulating DOM)
                            // The original function does DOM manipulation, let's call it or replicate it?
                            // The original function updates the DOM element directly.
                            // Let's just reload the page for simplicity or rely on the original function logic if we can.
                            // But original function relies on 'sessions' variable which is local to the script?
                            // No, 'sessions' is defined with 'let' at top level in the script, so it's global?
                            // Wait, 'let' at top level is NOT attached to window.
                            // So I cannot access 'sessions' variable from here easily unless I modify the script to expose it.
                            
                            // Since I cannot easily access the 'sessions' variable inside the other script (due to 'let'),
                            // I should probably just reload the page to reflect changes.
                            location.reload(); 
                        }
                    } else {
                        alert("Error updating status: " + data.message);
                    }
                });
            }
        };

        window.cancelSession = function(sessionId) {
            if(confirm("Are you sure you want to cancel this session?")) {
                fetch('update_appointment_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + sessionId + '&status=Cancelled'
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert("Error updating status: " + data.message);
                    }
                });
            }
        };
    </script>
  </body>
</html>
