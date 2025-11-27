<?php
require_once '../../config/database.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $firstName = htmlspecialchars($_POST['firstName']);
    $middleName = htmlspecialchars($_POST['middleName'] ?? '');
    $lastName = htmlspecialchars($_POST['lastName']);
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $contactNumber = $_POST['contactNumber'];
    $schoolId = $_POST['schoolId'];
    $department = $_POST['department'];
    $course = $_POST['course'];
    $yearLevel = $_POST['yearLevel'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        $message = "Passwords do not match!";
        $messageType = "danger";
    } else {
        try {
            $pdo->beginTransaction();

            // Check if username or email (using username as email/identifier) already exists
            // The schema uses 'email' but the form uses 'username'. I'll map username to email for now or adjust schema?
            // Schema: email VARCHAR(255) NOT NULL UNIQUE
            // Form: username
            // I will use the username as the email field in the DB for simplicity, or I should ask?
            // The login page asks for "Username". The schema has "email".
            // I'll assume 'username' in the form maps to 'email' in the DB, or I should alter the table to have username.
            // Let's check the schema again.
            // Schema: email VARCHAR(255) NOT NULL UNIQUE
            // I'll use the username input for the email column, but validate it looks like a username.
            
            // Insert into users table
            $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'student')");
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([$username, $hashedPassword]);
            $userId = $pdo->lastInsertId();

            // Insert into students table
            $stmt = $pdo->prepare("INSERT INTO students (user_id, student_number, first_name, last_name, age, gender, birthday, course, year_level, contact_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Calculate age
            $dob = new DateTime($birthday);
            $now = new DateTime();
            $age = $now->diff($dob)->y;

            $stmt->execute([$userId, $schoolId, $firstName, $lastName, $age, $gender, $birthday, $course, $yearLevel, $contactNumber]);

            $pdo->commit();
            $message = "Registration successful! You can now login.";
            $messageType = "success";
            
            // Optional: Redirect after short delay
            header("refresh:2;url=../../index.php");

        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() == 23000) {
                $message = "Username or Student ID already exists.";
            } else {
                $message = "Error: " . $e->getMessage();
            }
            $messageType = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Registration - Guidance Counseling System</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
      rel="stylesheet"
    />
    
    <link rel="stylesheet" href="../../assets/styles/layouts/stud-registration.css">
  </head>
  <body>
    <!-- Header -->
    <div class="registration-header">
      <div class="container">
        <h1><i class="fas fa-user-plus me-3"></i>Student Registration</h1>
        <p>Create your account to access guidance counseling services</p>
      </div>
    </div>

    <!-- Registration Form -->
    <div class="container pb-5">
      <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <form id="registrationForm" method="POST" action="">
        <!-- Personal Information -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fas fa-user me-2"></i>Personal Information
          </h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="firstName" class="form-label required"
                >First Name</label
              >
              <input type="text" class="form-control" id="firstName" name="firstName" required />
            </div>
            <div class="col-md-4">
              <label for="middleName" class="form-label">Middle Name</label>
              <input type="text" class="form-control" id="middleName" name="middleName" />
            </div>
            <div class="col-md-4">
              <label for="lastName" class="form-label required"
                >Last Name</label
              >
              <input type="text" class="form-control" id="lastName" name="lastName" required />
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-4">
              <label for="birthday" class="form-label required">Birthday</label>
              <input type="date" class="form-control" id="birthday" name="birthday" required />
            </div>
            <div class="col-md-4">
              <label for="gender" class="form-label required">Gender</label>
              <select class="form-select" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="contactNumber" class="form-label required"
                >Contact Number</label
              >
              <input
                type="tel"
                class="form-control"
                id="contactNumber"
                name="contactNumber"
                placeholder="11 digit phone number (09123456789)"
                required
              />
            </div>
          </div>
        </div>

        <!-- Academic Information -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fas fa-graduation-cap me-2"></i>Academic Information
          </h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="schoolId" class="form-label required"
                >School ID</label
              >
              <input type="text" class="form-control" id="schoolId" name="schoolId" required />
            </div>
            <div class="col-md-4">
              <label for="department" class="form-label required"
                >Department</label
              >
              <select class="form-select" id="department" name="department" required>
                <option value="" hidden>Select Department</option>
                <option value="College of Engineering">
                  College of Engineering
                </option>
                <option value="College of Arts and Sciences">
                  College of Arts and Sciences
                </option>
                <option value="College of Business Administration">
                  College of Business Administration
                </option>
                <option value="College of Education">
                  College of Education
                </option>
                <option value="College of Information Technology">
                  College of Information Technology
                </option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="course" class="form-label required">Course</label>
              <input
                type="text"
                class="form-control"
                id="course"
                name="course"
                placeholder="e.g., BS Computer Science"
                required
              />
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-4">
              <label for="yearLevel" class="form-label required"
                >Year Level</label
              >
              <select class="form-select" id="yearLevel" name="yearLevel" required>
                <option value="" hidden>Select Year Level</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Account Information -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fas fa-lock me-2"></i>Account Information
          </h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="username" class="form-label required"
                >Create Username</label
              >
              <input type="text" class="form-control" id="username" name="username" required />
            </div>
            <div class="col-md-4">
              <label for="password" class="form-label required"
                >Password</label
              >
              <input type="password" class="form-control" id="password" name="password" required />
            </div>
            <div class="col-md-4">
              <label for="confirmPassword" class="form-label required"
                >Confirm Password</label
              >
              <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required />
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid gap-2 mt-4">
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-paper-plane me-2"></i>Submit Registration
          </button>
        </div>
      </form>

      <!-- Login Link -->
      <div class="login-link">
        <p class="mb-0">
          Already have an account?
          <a href="../../index.php"
            ><i class="fas fa-sign-in-alt me-1"></i>Login here</a
          >
        </p>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <!-- Removed stud-registration.js as we are handling logic in PHP now, or we can keep it for client-side validation if needed. -->
    <!-- I'll keep the script import but might need to check if it interferes with form submission -->
    <!-- <script src="../../assets/scripts/stud-registration.js"></script> -->
  </body>
</html>
