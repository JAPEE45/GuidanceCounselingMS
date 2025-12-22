<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $student = $stmt->fetch();

    if (!$student) {
        die("Student record not found.");
    }

    $profileData = [
        'firstName' => $student['first_name'],
        'middleName' => $student['middle_name'],
        'lastName' => $student['last_name'],
        'schoolId' => $student['student_number'],
        'birthday' => $student['birthday'],
        'gender' => $student['gender'],
        'phone' => $student['contact_number'],
        'department' => $student['department'],
        'course' => $student['course'],
        'yearLevel' => $student['year_level']
    ];

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Profile - Guidance Counseling System</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css" />
    <link
      rel="stylesheet"
      href="../../assets/styles/layouts/stud-profile.css"
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
          <a href="./dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li>
          <a href="./book-appointment.php"><i class="fas fa-calendar-check"></i> Book Appointment</a>
        </li>
        <li>
          <a href="./notifications.php"><i class="fas fa-bell"></i> Notifications</a>
        </li>
        <li>
          <a href="#" class="active"><i class="fa-solid fa-user-circle"></i> Profile</a>
        </li>
        <li>
          <a href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
      </ul>
    </aside>
    
    <button class="sidebar-toggle" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Profile Container -->
      <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
          <div class="profile-avatar">
            <i class="fas fa-user"></i>
          </div>
          <h3 id="displayName">--</h3>
          <p id="displayId">Student ID: --</p>
        </div>

        <!-- Profile Body -->
        <div class="profile-body">
          <!-- View Mode -->
          <div id="viewMode">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="section-title mb-0">Personal Information</h4>
              <button class="btn-edit" onclick="enableEdit()">
                <i class="fas fa-edit me-2"></i>Edit Profile
              </button>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="info-group">
                  <div class="info-label">Full Name</div>
                  <div class="info-value" id="viewName">--</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-group">
                  <div class="info-label">School ID</div>
                  <div class="info-value" id="viewSchoolId">--</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-group">
                  <div class="info-label">Birthday</div>
                  <div class="info-value" id="viewBirthday">
                    --
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-group">
                  <div class="info-label">Gender</div>
                  <div class="info-value" id="viewGender">--</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-group">
                  <div class="info-label">Phone Number</div>
                  <div class="info-value" id="viewPhone">--</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-group">
                  <div class="info-label">Department</div>
                  <div class="info-value" id="viewDepartment">
                    --
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-group">
                  <div class="info-label">Course</div>
                  <div class="info-value" id="viewCourse">
                    --
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-group">
                  <div class="info-label">Year Level</div>
                  <div class="info-value" id="viewYearLevel">--</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Mode -->
          <div id="editMode" style="display: none">
            <h4 class="section-title">Edit Personal Information</h4>
            <form id="profileForm">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" id="editFirstName" required />
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Middle Name</label>
                  <input type="text" class="form-control" id="editMiddleName" />
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="editLastName" required />
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">School ID</label>
                  <input
                    type="text"
                    class="form-control"
                    id="editSchoolId"
                    readonly
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Birthday</label>
                  <input
                    type="date"
                    class="form-control"
                    id="editBirthday"
                    required
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Gender</label>
                  <select class="form-control" id="editGender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Phone Number</label>
                  <input
                    type="tel"
                    class="form-control"
                    id="editPhone"
                    required
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Department</label>
                  <select class="form-control" id="editDepartment" required>
                    <option value="">Select Department</option>
                    <option value="College of Computer Studies">
                      College of Computer Studies
                    </option>
                    <option value="College of Engineering">
                      College of Engineering
                    </option>
                    <option value="College of Business">
                      College of Business
                    </option>
                    <option value="College of Arts and Sciences">
                      College of Arts and Sciences
                    </option>
                    <option value="College of Education">
                      College of Education
                    </option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Course</label>
                  <input
                    type="text"
                    class="form-control"
                    id="editCourse"
                    required
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Year Level</label>
                  <select class="form-control" id="editYearLevel" required>
                    <option value="">Select Year Level</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                  </select>
                </div>
              </div>
              <div class="mt-4">
                <button type="submit" class="btn-edit me-2">
                  <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <button type="button" class="btn-cancel" onclick="cancelEdit()">
                  <i class="fas fa-times me-2"></i>Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Inject Data -->
    <script>
        window.profileData = <?php echo json_encode($profileData); ?>;
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <script src="../../assets/scripts/stud-profile.js"></script>
  </body>
</html>
