<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

try {
    // Fetch pending appointments without assigned counselor
    $stmt = $pdo->query("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.purpose, a.counseling_concerns,
               s.first_name, s.last_name, s.student_number, s.course, s.year_level
        FROM appointments a
        JOIN students s ON a.student_id = s.student_id
        WHERE a.status = 'Pending' AND a.counselor_id IS NULL
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
    $pendingAppointments = $stmt->fetchAll();
    
    // Fetch all active counselors
    $stmt = $pdo->query("
        SELECT counselor_id, first_name, last_name, specialization
        FROM counselors
        WHERE status = 'Active'
        ORDER BY first_name, last_name
    ");
    $counselors = $stmt->fetchAll();
    
    // Fetch notifications
    $stmt = $pdo->prepare("
        SELECT notification_id, message, is_read, created_at 
        FROM notifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications_db = $stmt->fetchAll();

    $notifications = [];
    foreach ($notifications_db as $n) {
        $notifications[] = [
            'id' => $n['notification_id'],
            'title' => 'Notification', // Default title since column doesn't exist
            'message' => $n['message'],
            'unread' => !$n['is_read'],
            'date' => date('F d, Y h:i A', strtotime($n['created_at']))
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
    <title>Notifications - Guidance Counseling System</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css" />
    <style>
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .page-header h2, .page-header h4 {
            margin: 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .page-header i {
            color: #3498db;
            font-size: 1.5rem;
        }
        
        /* Table Styling */
        .table-responsive {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .table {
            margin-bottom: 0;
        }
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .table-header th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .table-header th i {
            margin-right: 0.5rem;
        }
        .appointment-row {
            transition: all 0.3s ease;
        }
        .appointment-row:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: scale(1.01);
        }
        .appointment-row td {
            padding: 1rem;
            vertical-align: middle;
        }
        .concern-badge-sm {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        /* Pending Appointments Cards */
        .appointment-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 5px solid #f39c12;
            overflow: hidden;
        }
        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .appointment-card .card-body {
            padding: 1.5rem;
        }
        .appointment-card .card-title {
            color: #2c3e50;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .appointment-card .card-title i {
            color: #3498db;
        }
        .appointment-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        .appointment-info p {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .appointment-info strong {
            color: #2c3e50;
            font-weight: 600;
        }
        .concern-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        
        /* Counselor Selection */
        .counselor-select-wrapper {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px dashed #e9ecef;
        }
        .counselor-select-wrapper select {
            flex: 1;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        .counselor-select-wrapper select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            outline: none;
        }
        .btn-assign {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .btn-assign:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        /* Notifications Container */
        .notifications-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .notification-item {
            display: flex;
            align-items: flex-start;
            padding: 1.25rem;
            border-radius: 10px;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .notification-item:hover {
            background: linear-gradient(135deg, #e8f4ff 0%, #d4e9ff 100%);
            transform: translateX(5px);
        }
        .notification-item.unread {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
            border-left: 4px solid #ffc107;
        }
        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .notification-content {
            flex: 1;
        }
        .notification-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-size: 1.05rem;
        }
        .notification-message {
            color: #5a6c7d;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }
        .notification-time {
            font-size: 0.85rem;
            color: #95a5a6;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .badge-new {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        /* Section Headers */
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #e9ecef;
        }
        .section-header i {
            font-size: 1.75rem;
            color: #3498db;
        }
        .section-header h4 {
            margin: 0;
            color: #2c3e50;
            font-weight: 700;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #95a5a6;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
          <a href="./counselors.php" class=""
            ><i class="fas fa-user-tie"></i> Counselors</a
          >
        </li>
        <li>
          <a href="./appointments.php" class=""
            ><i class="fas fa-calendar-check"></i> Appointments</a
          >
        </li>
        <!-- <li>
          <a href="#" class="active"><i class="fas fa-bell"></i> Notifications</a>
        </li> -->
        <li>
          <a href="./profile.php"><i class="fa-solid fa-user-circle"></i> Profile</a>
        </li>
        <li>
          <a href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
      </ul>
    </aside>

    <button class="sidebar-toggle" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>

    <div class="main-content">
      <div class="page-header">
        <h2><i class="fas fa-bell"></i> Notifications & Counselor Assignment</h2>
      </div>

      <!-- Pending Appointments Section -->
      <?php if (count($pendingAppointments) > 0): ?>
      <div class="mb-4">
        <div class="section-header">
          <i class="fas fa-user-clock"></i>
          <h4>Pending Appointments - Assign Counselor</h4>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-header">
              <tr>
                <th><i class="fas fa-user-graduate"></i> Student Name</th>
                <th><i class="fas fa-id-card"></i> Student #</th>
                <th><i class="fas fa-graduation-cap"></i> Course & Year</th>
                <th><i class="fas fa-calendar"></i> Date</th>
                <th><i class="fas fa-clock"></i> Time</th>
                <th><i class="fas fa-heart"></i> Concerns</th>
                <th><i class="fas fa-user-tie"></i> Assign Counselor</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pendingAppointments as $appt): ?>
              <tr class="appointment-row" onclick="viewAppointment(<?php echo $appt['appointment_id']; ?>)" style="cursor: pointer;">
                <td class="fw-bold text-primary">
                  <?php echo htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']); ?>
                </td>
                <td><?php echo htmlspecialchars($appt['student_number']); ?></td>
                <td><?php echo htmlspecialchars($appt['course'] . ' - ' . $appt['year_level']); ?></td>
                <td><?php echo date('M d, Y', strtotime($appt['appointment_date'])); ?></td>
                <td><?php echo date('g:i A', strtotime($appt['appointment_time'])); ?></td>
                <td>
                  <span class="concern-badge-sm"><?php echo htmlspecialchars($appt['purpose']); ?></span>
                </td>
                <td>
                  <span class="text-muted fst-italic">Click Assign button</span>
                </td>
                <td>
                  <button class="btn btn-assign btn-sm" onclick="openAssignModal(<?php echo $appt['appointment_id']; ?>, event)">
                    <i class="fas fa-user-check"></i> Assign
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-check-circle"></i>
        <h4>All Appointments Assigned!</h4>
        <p>There are no pending appointments waiting for counselor assignment.</p>
      </div>
      <?php endif; ?>

      <!-- Notifications Section -->
      <div class="page-header">
        <h4><i class="fas fa-envelope"></i> Notifications</h4>
        <button class="btn btn-primary" onclick="markAllAsRead()">
            <i class="fas fa-check-double me-2"></i> Mark All as Read
        </button>
      </div>

      <div class="notifications-container">
        <div id="notificationsList">
          <!-- Notifications will be loaded here -->
        </div>
      </div>
    </div>

    <!-- View Appointment Details Modal -->
    <div class="modal fade" id="viewAppointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i> Appointment Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewModalBody">
                    <!-- Content loaded via AJAX -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Counselor Modal -->
    <div class="modal fade" id="assignCounselorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-user-check me-2"></i> Assign Counselor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="counselorSearch" placeholder="Search counselor by name or specialization..." onkeyup="filterCounselors()">
                        </div>
                    </div>
                    <div class="list-group counselor-list" id="counselorList">
                        <?php foreach ($counselors as $counselor): ?>
                        <div class="list-group-item counselor-item" data-search="<?php echo strtolower($counselor['first_name'] . ' ' . $counselor['last_name'] . ' ' . $counselor['specialization']); ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($counselor['first_name'] . ' ' . $counselor['last_name']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($counselor['specialization'] ?? 'General Counselor'); ?></small>
                                </div>
                                <button class="btn btn-sm btn-primary" onclick="confirmAssign(<?php echo $counselor['counselor_id']; ?>, '<?php echo htmlspecialchars($counselor['first_name'] . ' ' . $counselor['last_name']); ?>')">
                                    Assign
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div id="noCounselorFound" class="text-center py-3 text-muted" style="display: none;">
                            No counselors found matching your search.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <script>
        var notifications = <?php echo json_encode($notifications); ?>;
        var currentAppointmentId = null;

        function getStatusIcon(title) {
            if (title.includes("Request")) return "fa-user-plus";
            if (title.includes("Appointment")) return "fa-calendar";
            return "fa-bell";
        }

        // View Appointment Details
        function viewAppointment(id) {
            const modal = new bootstrap.Modal(document.getElementById('viewAppointmentModal'));
            const modalBody = document.getElementById('viewModalBody');
            
            modal.show();
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            fetch(`get_appointment_details.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const appt = data.data;
                        const concernsHtml = appt.concerns.map(c => `<span class="badge bg-info me-1 mb-1">${c}</span>`).join('');
                        const parentSection = appt.is_minor ? `
                            <div class="alert alert-warning mt-3">
                                <h6><i class="fas fa-users me-2"></i> Parent/Guardian (Minor)</h6>
                                <p class="mb-1"><strong>Name:</strong> ${appt.parent_name}</p>
                                <p class="mb-0"><strong>Contact:</strong> ${appt.parent_contact}</p>
                            </div>
                        ` : '';

                        modalBody.innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-user-graduate me-2"></i> Student Information</h6>
                                    <p class="mb-1"><strong>Name:</strong> ${appt.student_name}</p>
                                    <p class="mb-1"><strong>Student #:</strong> ${appt.student_number}</p>
                                    <p class="mb-1"><strong>Course/Year:</strong> ${appt.course_year}</p>
                                    <p class="mb-1"><strong>Contact:</strong> ${appt.contact}</p>
                                    <p class="mb-1"><strong>Email:</strong> ${appt.email}</p>
                                    <p class="mb-3"><strong>Address:</strong> ${appt.address}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-calendar-alt me-2"></i> Appointment Details</h6>
                                    <p class="mb-1"><strong>Date:</strong> ${appt.date}</p>
                                    <p class="mb-1"><strong>Time:</strong> ${appt.time}</p>
                                    <p class="mb-1"><strong>Purpose:</strong> ${appt.purpose}</p>
                                    <div class="mb-3"><strong>Concerns:</strong><br>${concernsHtml}</div>
                                </div>
                            </div>
                            
                            ${parentSection}

                            <div class="mt-3">
                                <h6 class="text-primary border-bottom pb-2"><i class="fas fa-notes-medical me-2"></i> Medical & Consent</h6>
                                <p class="mb-1"><strong>Medications:</strong> ${appt.medications || 'None'}</p>
                                <p class="mb-1"><strong>Conditions:</strong> ${appt.conditions || 'None'}</p>
                                <p class="mb-0"><strong>Consent Acknowledged:</strong> ${appt.consent ? '<span class="text-success"><i class="fas fa-check-circle"></i> Yes</span>' : '<span class="text-danger">No</span>'}</p>
                            </div>
                        `;
                    } else {
                        modalBody.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    modalBody.innerHTML = `<div class="alert alert-danger">Error loading details.</div>`;
                    console.error('Error:', error);
                });
        }

        // Open Assign Modal
        function openAssignModal(id, event) {
            event.stopPropagation(); // Prevent row click
            currentAppointmentId = id;
            const modal = new bootstrap.Modal(document.getElementById('assignCounselorModal'));
            modal.show();
        }

        // Filter Counselors
        function filterCounselors() {
            const input = document.getElementById('counselorSearch');
            const filter = input.value.toLowerCase();
            const items = document.getElementsByClassName('counselor-item');
            let hasVisible = false;

            for (let i = 0; i < items.length; i++) {
                const searchData = items[i].getAttribute('data-search');
                if (searchData.includes(filter)) {
                    items[i].style.display = "";
                    hasVisible = true;
                } else {
                    items[i].style.display = "none";
                }
            }
            
            document.getElementById('noCounselorFound').style.display = hasVisible ? "none" : "block";
        }

        // Confirm Assignment
        function confirmAssign(counselorId, counselorName) {
            if (!confirm(`Are you sure you want to assign ${counselorName} to this appointment?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('appointment_id', currentAppointmentId);
            formData.append('counselor_id', counselorId);
            
            fetch('assign_counselor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error assigning counselor.');
                console.error('Error:', error);
            });
        }

        function renderNotifications() {
            const container = document.getElementById("notificationsList");
            
            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h4>No Notifications</h4>
                        <p>You're all caught up! No new notifications at this time.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = notifications.map(n => `
                <div class="notification-item ${n.unread ? 'unread' : ''}" onclick="markAsRead(${n.id})">
                    <div class="notification-icon">
                        <i class="fas ${getStatusIcon(n.title)}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${n.title}</div>
                        <div class="notification-message">${n.message}</div>
                        <div class="notification-time">
                            <i class="far fa-clock"></i> ${n.date}
                        </div>
                    </div>
                    ${n.unread ? '<span class="badge-new">New</span>' : ''}
                </div>
            `).join("");
        }

        function markAsRead(id) {
            const n = notifications.find(n => n.id === id);
            if (n && n.unread) {
                const formData = new FormData();
                formData.append('id', id);

                fetch('mark_notification_read.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        n.unread = false;
                        renderNotifications();
                    }
                });
            }
        }

        function markAllAsRead() {
            fetch('mark_notification_read.php', {
                method: 'POST',
                body: new FormData()
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    notifications.forEach(n => n.unread = false);
                    renderNotifications();
                    alert("All notifications marked as read!");
                }
            });
        }

        function assignCounselor(appointmentId) {
            const selectElement = document.getElementById(`counselor_${appointmentId}`);
            const counselorId = selectElement.value;
            
            if (!counselorId) {
                alert('Please select a counselor first.');
                return;
            }
            
            if (!confirm('Are you sure you want to assign this counselor to the appointment?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('appointment_id', appointmentId);
            formData.append('counselor_id', counselorId);
            
            fetch('assign_counselor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error assigning counselor. Please try again.');
                console.error('Error:', error);
            });
        }

        renderNotifications();
    </script>
  </body>
</html>
