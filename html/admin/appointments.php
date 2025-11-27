<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

try {
    // Fetch Pending Appointments
    $stmt = $pdo->query("
        SELECT a.appointment_id as id, a.appointment_date as date, a.appointment_time as time, 
               s.first_name, s.last_name, a.purpose as type, a.status,
               s.physical_wellness, s.intellectual_wellness, s.environmental_wellness
        FROM appointments a 
        JOIN students s ON a.student_id = s.student_id 
        WHERE a.status = 'Pending'
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
    $pending_db = $stmt->fetchAll();

    $appointmentsData = [];
    foreach ($pending_db as $appt) {
        $wellnessData = [];
        // Determine which wellness data to use based on type
        if (stripos($appt['type'], 'Physical') !== false) {
            $wellnessData = json_decode($appt['physical_wellness'], true) ?? [];
        } elseif (stripos($appt['type'], 'Intellectual') !== false) {
            $wellnessData = json_decode($appt['intellectual_wellness'], true) ?? [];
        } elseif (stripos($appt['type'], 'Environmental') !== false) {
            $wellnessData = json_decode($appt['environmental_wellness'], true) ?? [];
        } else {
            // Default or merge all? For now empty or maybe Physical as default
            $wellnessData = json_decode($appt['physical_wellness'], true) ?? [];
        }

        $appointmentsData[] = [
            'id' => $appt['id'],
            'date' => date('l, F d', strtotime($appt['date'])), // Format like "Monday, November 11"
            'time' => date('h:i A', strtotime($appt['time'])),
            'student' => $appt['first_name'] . ' ' . $appt['last_name'],
            'type' => $appt['type'],
            'status' => strtolower($appt['status']),
            'data' => $wellnessData
        ];
    }

    // Fetch History Appointments (Confirmed, Declined, Completed, Cancelled, Rescheduled)
    $stmt = $pdo->query("
        SELECT a.appointment_id as id, a.appointment_date as date, a.appointment_time as time, 
               s.first_name, s.last_name, a.purpose as type, a.status,
               s.physical_wellness, s.intellectual_wellness, s.environmental_wellness
        FROM appointments a 
        JOIN students s ON a.student_id = s.student_id 
        WHERE a.status != 'Pending'
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $history_db = $stmt->fetchAll();

    $historyData = [];
    foreach ($history_db as $appt) {
        $wellnessData = []; // History might not need full data but JS might expect it
         if (stripos($appt['type'], 'Physical') !== false) {
            $wellnessData = json_decode($appt['physical_wellness'], true) ?? [];
        } elseif (stripos($appt['type'], 'Intellectual') !== false) {
            $wellnessData = json_decode($appt['intellectual_wellness'], true) ?? [];
        } elseif (stripos($appt['type'], 'Environmental') !== false) {
            $wellnessData = json_decode($appt['environmental_wellness'], true) ?? [];
        }

        $historyData[] = [
            'id' => $appt['id'],
            'date' => date('l, F d', strtotime($appt['date'])),
            'time' => date('h:i A', strtotime($appt['time'])),
            'student' => $appt['first_name'] . ' ' . $appt['last_name'],
            'type' => $appt['type'],
            'status' => strtolower($appt['status']),
            'data' => $wellnessData
        ];
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Guidance Counseling System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css">
    <link rel="stylesheet" href="../../assets/styles/layouts/admin-appointments.css">
</head>
<body>
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <h4><i class="fas fa-graduation-cap"></i> Guidance Counseling</h4>
      </div>
      <ul class="sidebar-menu">
        <li>
          <a href="./dashboard.php" class=""><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li>
          <a href="./counselors.php" ><i class="fas fa-user-tie"></i> Counselors</a>
        </li>
        <li>
          <a href="#" class="active"
            ><i class="fa-solid fa-calendar-check"></i> Appointments</a
          >
        </li>
        <li>
          <a href="./notifications.php" class="notif-referrals"
            ><i class="fas fa-bell"></i> Notifications & Referrals</a
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-calendar-check"></i> Appointments Management</h2>
        </div>

        <!-- Tab Buttons -->
        <div class="tab-buttons">
            <button class="tab-btn active" onclick="switchTab('pending')">
                <i class="fas fa-clock"></i> List of Appointments
            </button>
            <button class="tab-btn" onclick="switchTab('history')">
                <i class="fas fa-history"></i> History of Appointments
            </button>
        </div>

        <!-- Pending Appointments Section -->
        <div id="pending-section" class="appointments-section active">
            <div id="pending-appointments"></div>
        </div>

        <!-- History Appointments Section -->
        <div id="history-section" class="appointments-section">
            <div id="history-appointments"></div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn-action btn-approve" id="approveBtn" disabled onclick="showConfirmModal('approve')">
                <i class="fas fa-check"></i> Approve
            </button>
            <button class="btn-action btn-decline" id="declineBtn" disabled onclick="showConfirmModal('decline')">
                <i class="fas fa-times"></i> Decline
            </button>
            <button class="btn-action btn-reschedule" id="rescheduleBtn" disabled onclick="showConfirmModal('reschedule')">
                <i class="fas fa-calendar-alt"></i> Reschedule
            </button>
        </div>
    </div>

    <!-- Appointment Details Modal -->
    <div class="modal fade" id="appointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Content will be dynamically inserted -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Confirmation Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Approve Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this appointment?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="processAction('approve')">Yes, Approve</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Decline Confirmation Modal -->
    <div class="modal fade" id="declineModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Decline Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to decline this appointment?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="processAction('decline')">Yes, Decline</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reschedule Confirmation Modal -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: #0074d9; color: white;">
                    <h5 class="modal-title">Reschedule Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reschedule this appointment?</p>
                    <div class="mb-3">
                        <label class="form-label">New Date & Time</label>
                        <input type="datetime-local" class="form-control" id="newDateTime">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="processAction('reschedule')">Confirm Reschedule</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inject Data -->
    <script>
        window.appointmentsData = <?php echo json_encode($appointmentsData); ?>;
        window.historyData = <?php echo json_encode($historyData); ?>;
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <script src="../../assets/scripts/admin-appointments.js"></script>

    <!-- AJAX Overrides -->
    <script>
        window.processAction = function(action) {
            if (!selectedAppointment) return;

            const formData = new FormData();
            formData.append('action', action);
            formData.append('id', selectedAppointment.id);

            if (action === 'reschedule') {
                const newDateTime = document.getElementById("newDateTime").value;
                if (!newDateTime) {
                    alert("Please select a new date and time.");
                    return;
                }
                formData.append('newDateTime', newDateTime);
            }

            fetch('manage_appointment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert(`Appointment has been ${action}d!`); // Simple alert
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            });

            // Close all modals
            document.querySelectorAll(".modal").forEach((modalEl) => {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            });
        };
    </script>
</body>
</html>
