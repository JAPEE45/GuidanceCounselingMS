<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'counselor') {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Get Counselor ID
    $stmt = $pdo->prepare("SELECT counselor_id FROM counselors WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $counselor_id = $stmt->fetchColumn();

    // Fetch Active Sessions (Confirmed)
    $stmt = $pdo->prepare("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.purpose, 
               s.first_name, s.last_name, s.student_number
        FROM appointments a
        JOIN students s ON a.student_id = s.student_id
        WHERE a.counselor_id = ? AND a.status = 'Confirmed'
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
    $stmt->execute([$counselor_id]);
    $active_sessions = $stmt->fetchAll();

    // Fetch History (Completed)
    $stmt = $pdo->prepare("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.purpose, 
               s.first_name, s.last_name, s.student_number,
               r.session_notes, r.recommendations, r.observations, r.attendance_status
        FROM appointments a
        JOIN students s ON a.student_id = s.student_id
        LEFT JOIN counseling_records r ON a.appointment_id = r.appointment_id
        WHERE a.counselor_id = ? AND a.status = 'Completed'
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute([$counselor_id]);
    $history_sessions = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Session Management - Guidance Counseling</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css" />
    <style>
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 1rem 1.5rem;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom: 3px solid #0d6efd;
            background: none;
            font-weight: 600;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
  </head>
  <body>
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <h4><i class="fas fa-graduation-cap"></i> Guidance Counseling</h4>
      </div>
      <ul class="sidebar-menu">
        <li>
          <a href="./dashboard.php" class=""
            ><i class="fas fa-home"></i> Dashboard</a
          >
        </li>
        <li>
          <a href="./my-schedule.php" class=""
            ><i class="fas fa-calendar-alt"></i> My Schedule</a
          >
        </li>
        <li>
          <a href="#" class="active"
            ><i class="fas fa-clipboard-list"></i> Session Management</a
          >
        </li>
        <li>
          <a href="./student-record.php" class="notif-referrals"
            ><i class="fas fa-user-graduate"></i> Student Record</a
          >
        </li>
        <li>
          <a href="./profile.php"
            ><i class="fa-solid fa-user-circle"></i> Profile</a
          >
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
        <h2 class="mb-4"><i class="fas fa-clipboard-list me-2"></i>Session Management</h2>

        <div class="card">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs" id="sessionTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="active-tab" data-bs-toggle="tab" href="#active" role="tab">Active Sessions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history" role="tab">Session History</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="sessionTabsContent">
                    <!-- Active Sessions -->
                    <div class="tab-pane fade show active" id="active" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Student</th>
                                        <th>Purpose</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($active_sessions) > 0): ?>
                                        <?php foreach ($active_sessions as $session): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold"><?php echo date('M d, Y', strtotime($session['appointment_date'])); ?></div>
                                                    <div class="text-muted small"><?php echo date('h:i A', strtotime($session['appointment_time'])); ?></div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($session['first_name'] . ' ' . $session['last_name']); ?></div>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($session['student_number']); ?></div>
                                                </td>
                                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($session['purpose']); ?></span></td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" onclick="startSession(<?php echo htmlspecialchars(json_encode($session)); ?>)">
                                                        <i class="fas fa-play me-1"></i> Start Session
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No active sessions found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- History -->
                    <div class="tab-pane fade" id="history" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Student</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($history_sessions) > 0): ?>
                                        <?php foreach ($history_sessions as $session): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold"><?php echo date('M d, Y', strtotime($session['appointment_date'])); ?></div>
                                                    <div class="text-muted small"><?php echo date('h:i A', strtotime($session['appointment_time'])); ?></div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($session['first_name'] . ' ' . $session['last_name']); ?></div>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($session['student_number']); ?></div>
                                                </td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($session['purpose']); ?></span></td>
                                                <td>
                                                    <?php if ($session['attendance_status'] === 'Present'): ?>
                                                        <span class="badge bg-success">Completed</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Absent</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewSession(<?php echo htmlspecialchars(json_encode($session)); ?>)">
                                                        <i class="fas fa-eye me-1"></i> View Notes
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">No session history found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Notes Modal -->
    <div class="modal fade" id="sessionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Session Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="sessionForm">
                        <input type="hidden" id="appointmentId">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Student Name</label>
                                <p id="modalStudentName" class="form-control-plaintext">--</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date & Time</label>
                                <p id="modalDateTime" class="form-control-plaintext">--</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold required">Attendance</label>
                            <select class="form-select" id="attendance" required>
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Session Notes</label>
                            <textarea class="form-control" id="notes" rows="4" placeholder="Enter detailed session notes..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Recommendations</label>
                            <textarea class="form-control" id="recommendations" rows="3" placeholder="Enter recommendations..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Observations</label>
                            <textarea class="form-control" id="observations" rows="3" placeholder="Enter observations..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveSession()">Save Record</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <script>
        const sessionModal = new bootstrap.Modal(document.getElementById('sessionModal'));

        function startSession(session) {
            document.getElementById('modalTitle').textContent = 'New Session Record';
            document.getElementById('appointmentId').value = session.appointment_id;
            document.getElementById('modalStudentName').textContent = session.first_name + ' ' + session.last_name;
            document.getElementById('modalDateTime').textContent = session.appointment_date + ' ' + session.appointment_time;
            
            // Reset form
            document.getElementById('attendance').value = 'Present';
            document.getElementById('notes').value = '';
            document.getElementById('recommendations').value = '';
            document.getElementById('observations').value = '';
            
            // Enable inputs
            document.getElementById('attendance').disabled = false;
            document.getElementById('notes').disabled = false;
            document.getElementById('recommendations').disabled = false;
            document.getElementById('observations').disabled = false;
            document.getElementById('saveBtn').style.display = 'block';

            sessionModal.show();
        }

        function viewSession(session) {
            document.getElementById('modalTitle').textContent = 'View Session Record';
            document.getElementById('modalStudentName').textContent = session.first_name + ' ' + session.last_name;
            document.getElementById('modalDateTime').textContent = session.appointment_date + ' ' + session.appointment_time;
            
            // Fill form
            document.getElementById('attendance').value = session.attendance_status;
            document.getElementById('notes').value = session.session_notes || '';
            document.getElementById('recommendations').value = session.recommendations || '';
            document.getElementById('observations').value = session.observations || '';
            
            // Disable inputs
            document.getElementById('attendance').disabled = true;
            document.getElementById('notes').disabled = true;
            document.getElementById('recommendations').disabled = true;
            document.getElementById('observations').disabled = true;
            document.getElementById('saveBtn').style.display = 'none';

            sessionModal.show();
        }

        function saveSession() {
            const id = document.getElementById('appointmentId').value;
            const attendance = document.getElementById('attendance').value;
            const notes = document.getElementById('notes').value;
            const recommendations = document.getElementById('recommendations').value;
            const observations = document.getElementById('observations').value;

            if (!notes && attendance === 'Present') {
                alert("Please enter session notes.");
                return;
            }

            const formData = new FormData();
            formData.append('appointment_id', id);
            formData.append('attendance', attendance);
            formData.append('notes', notes);
            formData.append('recommendations', recommendations);
            formData.append('observations', observations);

            fetch('save_session_notes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert("Session record saved successfully!");
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            });
        }
    </script>
  </body>
</html>
