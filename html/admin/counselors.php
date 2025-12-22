<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

try {
    // Fetch Counselors
    $stmt = $pdo->query("
        SELECT c.counselor_id as id, c.first_name, c.last_name, c.specialization, c.status, u.email 
        FROM counselors c 
        JOIN users u ON c.user_id = u.user_id
    ");
    $counselors_db = $stmt->fetchAll();

    $counselors_data = [];
    foreach ($counselors_db as $c) {
        $counselors_data[] = [
            'id' => $c['id'],
            'name' => $c['first_name'] . ' ' . $c['last_name'],
            'specialization' => $c['specialization'],
            'email' => $c['email'],
            'consultationTime' => 'Mon-Fri, 9AM-5PM', // Placeholder
            'status' => strtolower($c['status'])
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
    <title>Counselors - Guidance Management System</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css">
    <link rel="stylesheet" href="../../assets/styles/layouts/admin-counselors.css">
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
          <a href="#" class="active"><i class="fas fa-user-tie"></i> Counselors</a>
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
      <!-- Header -->
      <div class="header">
        <h2><i class="fas fa-user-tie"></i> Counselors Management</h2>
        <button
          class="btn btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#addCounselorModal"
        >
          <i class="fas fa-plus"></i> Add New Counselor
        </button>
      </div>

      <!-- Table Container -->
      <div class="table-container">
        <div class="row g-3 pb-4 border-bottom border-gray">
          <div class="col-md-6">
            <input
              type="text"
              class="form-control"
              id="searchInput"
              placeholder="Search by Counselor Name, Specialization or Email..."
            />
          </div>
          <div class="col-md-4">
            <select class="form-select" id="filterSpecialization">
              <option value="">All Specializations</option>
              <option value="Academic">Academic</option>
              <option value="Career">Career</option>
              <option value="Personal">Personal</option>
              <option value="Mental Health">Mental Health</option>
              <option value="Crisis">Crisis</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-secondary w-100" onclick="resetFilters()">
              Reset Filters
            </button>
          </div>
        </div>
        <h5 class="mt-3">Counselors List</h5>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Specialization</th>
                <th>Email</th>
                <th>Consultation Time</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="counselorTableBody">
              <!-- Data will be populated by JavaScript -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Add Counselor Modal -->
    <div class="modal fade" id="addCounselorModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Add New Counselor
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form id="addCounselorForm">
              <div class="mb-3">
                <label class="form-label">Full Name *</label>
                <input type="text" class="form-control" id="addName" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Specialization *</label>
                <select class="form-select" id="addSpecialization" required>
                  <option value="">Select Specialization</option>
                  <option value="Academic">Academic</option>
                  <option value="Career">Career</option>
                  <option value="Personal">Personal</option>
                  <option value="Mental Health">Mental Health</option>
                  <option value="Crisis">Crisis</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Email *</label>
                <input
                  type="email"
                  class="form-control"
                  id="addEmail"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Consultation Time *</label>
                <input
                  type="text"
                  class="form-control"
                  id="addTime"
                  placeholder="e.g., Monday-Friday 9:00 AM - 5:00 PM"
                  required
                />
                <small class="text-muted">Enter available consultation days and hours</small>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-primary"
              onclick="addCounselor()"
            >
              Add Counselor
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Counselor Modal -->
    <div class="modal fade" id="editCounselorModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Edit Counselor
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form id="editCounselorForm">
              <input type="hidden" id="editId" />
              <div class="mb-3">
                <label class="form-label">Full Name *</label>
                <input
                  type="text"
                  class="form-control"
                  id="editName"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Specialization *</label>
                <select class="form-select" id="editSpecialization" required>
                  <option value="">Select Specialization</option>
                  <option value="Academic">Academic</option>
                  <option value="Career">Career</option>
                  <option value="Personal">Personal</option>
                  <option value="Mental Health">Mental Health</option>
                  <option value="Crisis">Crisis</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Email *</label>
                <input
                  type="email"
                  class="form-control"
                  id="editEmail"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Consultation Time *</label>
                <input
                  type="text"
                  class="form-control"
                  id="editTime"
                  required
                />
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-primary"
              onclick="updateCounselor()"
            >
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Deactivate Warning Modal -->
    <div class="modal fade" id="deactivateModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">
              <i class="fas fa-exclamation-triangle"></i> Confirm Deactivation
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <p>
              Are you sure you want to
              <strong id="deactivateAction">deactivate</strong> this counselor?
            </p>
            <p class="text-muted" id="deactivateName"></p>
            <input type="hidden" id="deactivateId" />
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-danger"
              onclick="confirmDeactivation()"
            >
              <span id="deactivateButtonText">Deactivate</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Inject Data -->
    <script>
        window.counselors = <?php echo json_encode($counselors_data); ?>;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <script src="../../assets/scripts/admin-counselors.js"></script>

    <!-- AJAX Overrides -->
    <script>
        // Override addCounselor
        window.addCounselor = function() {
            const name = document.getElementById("addName").value.trim();
            const specialization = document.getElementById("addSpecialization").value;
            const email = document.getElementById("addEmail").value.trim();
            const time = document.getElementById("addTime").value.trim();

            // Validation
            if (!name) {
                alert("Please enter the counselor's full name.");
                return;
            }
            
            // Check if name has at least two parts (first and last name)
            const nameParts = name.split(' ').filter(part => part.length > 0);
            if (nameParts.length < 2) {
                alert("Please enter both first name and last name (separated by space).");
                return;
            }
            
            if (!specialization) {
                alert("Please select a specialization.");
                return;
            }
            
            if (!email) {
                alert("Please enter an email address.");
                return;
            }
            
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                return;
            }
            
            if (!time) {
                alert("Please enter the consultation time.");
                return;
            }

            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('name', name);
            formData.append('specialization', specialization);
            formData.append('email', email);
            formData.append('time', time);

            // Disable button and show loading state
            const addBtn = document.querySelector('#addCounselorModal .btn-primary');
            const originalText = addBtn.textContent;
            addBtn.disabled = true;
            addBtn.textContent = 'Adding...';

            fetch('manage_counselor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    let message = 'Counselor added successfully!\n\n';
                    message += 'Login Credentials:\n';
                    message += 'Email: ' + email + '\n';
                    message += 'Password: ' + data.temp_password + '\n\n';
                    
                    if (data.email_sent) {
                        message += '✓ Email sent to counselor successfully!';
                    } else {
                        message += '⚠ Email could not be sent. Please provide the credentials manually.';
                    }
                    
                    alert(message);
                    location.reload();
                } else {
                    alert("Error: " + (data.message || "Unknown error occurred"));
                    addBtn.disabled = false;
                    addBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Error: " + error.message);
                addBtn.disabled = false;
                addBtn.textContent = originalText;
            });
        };

        // Override updateCounselor
        window.updateCounselor = function() {
            const id = document.getElementById("editId").value;
            const name = document.getElementById("editName").value.trim();
            const specialization = document.getElementById("editSpecialization").value;
            const email = document.getElementById("editEmail").value.trim();
            const time = document.getElementById("editTime").value.trim();

            // Validation
            if (!name) {
                alert("Please enter the counselor's full name.");
                return;
            }
            
            // Check if name has at least two parts (first and last name)
            const nameParts = name.split(' ').filter(part => part.length > 0);
            if (nameParts.length < 2) {
                alert("Please enter both first name and last name (separated by space).");
                return;
            }
            
            if (!email) {
                alert("Please enter an email address.");
                return;
            }
            
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                return;
            }

            const formData = new FormData();
            formData.append('action', 'update');
            formData.append('id', id);
            formData.append('name', name);
            formData.append('specialization', specialization);
            formData.append('email', email);
            formData.append('time', time);

            // Disable button and show loading state
            const updateBtn = document.querySelector('#editCounselorModal .btn-primary');
            const originalText = updateBtn.textContent;
            updateBtn.disabled = true;
            updateBtn.textContent = 'Saving...';

            fetch('manage_counselor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    alert("Error: " + (data.message || "Unknown error occurred"));
                    updateBtn.disabled = false;
                    updateBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Error: " + error.message);
                updateBtn.disabled = false;
                updateBtn.textContent = originalText;
            });
        };

        // Override confirmDeactivation
        window.confirmDeactivation = function() {
            const id = document.getElementById("deactivateId").value;
            const action = document.getElementById("deactivateAction").textContent; // 'deactivate' or 'activate'
            const status = action === 'deactivate' ? 'Inactive' : 'Active';

            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('id', id);
            formData.append('status', status);

            // Disable button and show loading state
            const actionBtn = document.querySelector('#deactivateModal .btn-danger');
            const originalText = actionBtn.textContent;
            actionBtn.disabled = true;
            actionBtn.textContent = 'Processing...';

            fetch('manage_counselor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    alert("Error: " + (data.message || "Unknown error occurred"));
                    actionBtn.disabled = false;
                    actionBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Error: " + error.message);
                actionBtn.disabled = false;
                actionBtn.textContent = originalText;
            });
        };
    </script>
  </body>
</html>
