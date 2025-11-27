<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $admin = $stmt->fetch();

    if (!$admin) {
        die("Admin record not found.");
    }

    $profileData = [
        'firstName' => $admin['first_name'],
        'lastName' => $admin['last_name'],
        'email' => $admin['email']
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
    <title>Admin Profile - Guidance Counseling System</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css" />
    <style>
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .profile-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            padding: 3rem 2rem;
            color: white;
            text-align: center;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: #0d6efd;
            font-size: 3rem;
        }
        .profile-body {
            padding: 2rem;
        }
        .info-label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-weight: 600;
            color: #212529;
            font-size: 1.1rem;
        }
        .btn-edit {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .btn-edit:hover {
            background: #0b5ed7;
            color: white;
        }
    </style>
  </head>
  <body>
    <!-- Sidebar -->
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
          <a href="./notifications.php" class=""
            ><i class="fas fa-bell"></i> Notifications</a
          >
        </li> -->
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
      <div class="profile-container">
        <div class="profile-header">
          <div class="profile-avatar">
            <i class="fas fa-user-shield"></i>
          </div>
          <h3 id="displayName">--</h3>
          <p>Administrator</p>
        </div>

        <div class="profile-body">
          <!-- View Mode -->
          <div id="viewMode">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="mb-0">Admin Information</h4>
              <button class="btn-edit" onclick="enableEdit()">
                <i class="fas fa-edit me-2"></i>Edit Profile
              </button>
            </div>

            <div class="row g-4">
              <div class="col-md-6">
                <div class="info-label">First Name</div>
                <div class="info-value" id="viewFirstName">--</div>
              </div>
              <div class="col-md-6">
                <div class="info-label">Last Name</div>
                <div class="info-value" id="viewLastName">--</div>
              </div>
              <div class="col-md-12">
                <div class="info-label">Email Address</div>
                <div class="info-value" id="viewEmail">--</div>
              </div>
            </div>
          </div>

          <!-- Edit Mode -->
          <div id="editMode" style="display: none">
            <h4 class="mb-4">Edit Information</h4>
            <form id="profileForm">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" id="editFirstName" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="editLastName" required />
                </div>
                <div class="col-md-12">
                  <label class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="editEmail" required />
                </div>
              </div>
              <div class="mt-4">
                <button type="submit" class="btn btn-primary me-2">
                  <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                  <i class="fas fa-times me-2"></i>Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <script>
        let profileData = <?php echo json_encode($profileData); ?>;

        function updateView() {
            document.getElementById('displayName').textContent = profileData.firstName + ' ' + profileData.lastName;
            document.getElementById('viewFirstName').textContent = profileData.firstName;
            document.getElementById('viewLastName').textContent = profileData.lastName;
            document.getElementById('viewEmail').textContent = profileData.email;
        }

        function enableEdit() {
            document.getElementById('viewMode').style.display = 'none';
            document.getElementById('editMode').style.display = 'block';
            
            document.getElementById('editFirstName').value = profileData.firstName;
            document.getElementById('editLastName').value = profileData.lastName;
            document.getElementById('editEmail').value = profileData.email;
        }

        function cancelEdit() {
            document.getElementById('editMode').style.display = 'none';
            document.getElementById('viewMode').style.display = 'block';
        }

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const firstName = document.getElementById('editFirstName').value;
            const lastName = document.getElementById('editLastName').value;
            const email = document.getElementById('editEmail').value;

            const formData = new FormData();
            formData.append('firstName', firstName);
            formData.append('lastName', lastName);
            formData.append('email', email);

            fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    profileData.firstName = firstName;
                    profileData.lastName = lastName;
                    profileData.email = email;
                    updateView();
                    cancelEdit();
                    alert('Profile updated successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });

        updateView();
    </script>
  </body>
</html>
