<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'counselor') {
    header("Location: ../../index.php");
    exit;
}

try {
    // Fetch all students
    $stmt = $pdo->query("SELECT * FROM students ORDER BY last_name, first_name");
    $students_db = $stmt->fetchAll();

    $studentData = [];

    foreach ($students_db as $student) {
        $student_id = $student['student_id'];

        // Fetch Counseling History
        $stmt = $pdo->prepare("
            SELECT a.appointment_date, a.purpose, a.status, 
                   r.session_notes, r.recommendations, r.observations
            FROM appointments a
            LEFT JOIN counseling_records r ON a.appointment_id = r.appointment_id
            WHERE a.student_id = ?
            ORDER BY a.appointment_date DESC
        ");
        $stmt->execute([$student_id]);
        $history = $stmt->fetchAll();

        $counseling = [];
        $latest_notes = "No records found.";
        $latest_referrals = "No records found.";
        $latest_remarks = "No records found.";

        if (count($history) > 0) {
            // Get latest notes from the most recent completed session (first in list due to DESC sort)
            // Find first record with notes
            foreach ($history as $h) {
                if ($h['session_notes']) {
                    $latest_notes = $h['session_notes'];
                    $latest_referrals = $h['recommendations'];
                    $latest_remarks = $h['observations'];
                    break;
                }
            }

            foreach ($history as $h) {
                $counseling[] = [
                    'date' => date('F d, Y', strtotime($h['appointment_date'])),
                    'type' => $h['purpose'],
                    'status' => ucfirst($h['status'])
                ];
            }
        }

        $studentData[] = [
            'name' => $student['first_name'] . ' ' . $student['last_name'],
            'year' => $student['year_level'],
            'course' => $student['course'],
            'counseling' => $counseling,
            'notes' => $latest_notes,
            'referrals' => $latest_referrals,
            'remarks' => $latest_remarks
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
    <title>Student Record - Guidance Counseling System</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css">
    <link rel="stylesheet" href="../../assets/styles/layouts/counselor-student-record.css">
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
          <a href="./my-schedule.php" class=""><i class="fas fa-calendar-alt"></i> My Schedule</a>
        </li>
        <li>
          <a href="./session-management.php"
            ><i class="fas fa-clipboard-list"></i> Session Management</a
          >
        </li>
        <li>
          <a href="#" class="notif-referrals active"
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
      <div class="page-header">
        <h1><i class="fas fa-user-graduate"></i> Student Record</h1>
        <p>
          Access comprehensive information about individual students including
          appointment history, counselor notes, and progress tracking
        </p>
      </div>

      <div class="card">
        <div class="card-header"><i class="fas fa-list"></i> Student List</div>
        <div class="card-body">
          <div class="search-box">
            <input
              type="text"
              class="form-control"
              id="searchInput"
              placeholder="Search students..."
              onkeyup="searchStudents()"
            />
          </div>
          <div class="table-responsive">
            <table class="table table-hover" id="studentTable">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Year Level</th>
                  <th>Course</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($studentData as $index => $student): ?>
                <tr>
                  <td><?php echo htmlspecialchars($student['name']); ?></td>
                  <td><?php echo htmlspecialchars($student['year']); ?></td>
                  <td><?php echo htmlspecialchars($student['course']); ?></td>
                  <td>
                    <button class="btn btn-view" onclick="viewStudent(<?php echo $index; ?>)">
                      <i class="fas fa-eye"></i> View
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-user-graduate"></i> Student Details
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="info-section">
              <h6><i class="fas fa-info-circle"></i> Personal Information</h6>
              <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value" id="modalName"></span>
              </div>
              <div class="info-row">
                <span class="info-label">Year Level:</span>
                <span class="info-value" id="modalYear"></span>
              </div>
              <div class="info-row">
                <span class="info-label">Course:</span>
                <span class="info-value" id="modalCourse"></span>
              </div>
            </div>

            <div class="info-section">
              <h6><i class="fas fa-calendar-check"></i> Counseling History</h6>
              <div class="counseling-history" id="counselingHistory"></div>
            </div>

            <div class="info-section">
              <h6><i class="fas fa-sticky-note"></i> Counselor Notes</h6>
              <p class="info-value" id="counselorNotes"></p>
            </div>

            <div class="info-section">
              <h6><i class="fas fa-share-square"></i> Referrals</h6>
              <p class="info-value" id="referrals"></p>
            </div>

            <div class="info-section">
              <h6><i class="fas fa-comment-dots"></i> Remarks</h6>
              <p class="info-value" id="remarks"></p>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Inject Data -->
    <script>
        window.studentData = <?php echo json_encode($studentData); ?>;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <script src="../../assets/scripts/counselor-student-record.js"></script>
  </body>
</html>
