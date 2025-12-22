<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/email_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Fetch student info
try {
    $stmt = $pdo->prepare("SELECT s.*, u.email FROM students s JOIN users u ON s.user_id = u.user_id WHERE s.user_id = ?");
    $stmt->execute([$user_id]);
    $student = $stmt->fetch();
    
    // Calculate age
    if (!empty($student['birthday'])) {
        $birthDate = new DateTime($student['birthday']);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
    } else {
        $age = 0;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Appointment Details
    $appointmentDate = $_POST['appointmentDate'];
    $appointmentTime = $_POST['appointmentTime'];
    
    // Student Information
    $studentAddress = $_POST['studentAddress'] ?? '';
    $isMinor = isset($_POST['isMinor']) && $_POST['isMinor'] === 'yes' ? 1 : 0;
    
    // Parent/Guardian Information
    $parentGuardianName = $isMinor ? ($_POST['parentGuardianName'] ?? '') : null;
    $parentGuardianContact = $isMinor ? ($_POST['parentGuardianContact'] ?? '') : null;
    
    // Counseling Concerns
    $concerns = [];
    $concernFields = [
        'concern_academic' => 'Academic-Related Concerns',
        'concern_career' => 'Career-Related Concerns',
        'concern_abuse' => 'Abuse-Related Concerns',
        'concern_adjustment' => 'Adjustment/Adaptation Concerns',
        'concern_parents' => 'Parents/Guardian Concerns',
        'concern_relational' => 'Relational-Related Concerns',
        'concern_sexuality' => 'Sexuality-Related Concerns',
        'concern_psycho' => 'Psycho-Emotional Concerns',
        'concern_suicidal' => 'Suicidal Ideation/Tendencies',
        'concern_pregnancy' => 'Pregnancy-Related Concerns'
    ];
    
    foreach ($concernFields as $field => $label) {
        if (isset($_POST[$field])) {
            $concerns[] = $label;
        }
    }
    $counselingConcerns = json_encode($concerns);
    
    // Medical Information
    $currentMedications = $_POST['currentMedications'] ?? '';
    $medicalConditions = $_POST['medicalConditions'] ?? '';
    
    // Consent
    $consentAcknowledged = isset($_POST['consentAcknowledged']) ? 1 : 0;
    
    // Purpose
    $purpose = !empty($concerns) ? implode(', ', array_slice($concerns, 0, 3)) : "General Counseling";

    try {
        $pdo->beginTransaction();

        // Update student address if provided
        if (!empty($studentAddress)) {
            $stmt = $pdo->prepare("UPDATE students SET address = ? WHERE student_id = ?");
            $stmt->execute([$studentAddress, $student['student_id']]);
        }

        // Create Appointment
        $stmt = $pdo->prepare("
            INSERT INTO appointments (
                student_id, appointment_date, appointment_time, purpose, status,
                is_minor, parent_guardian_name, parent_guardian_contact,
                counseling_concerns, current_medications, medical_conditions,
                student_address, consent_acknowledged
            ) VALUES (?, ?, ?, ?, 'Pending', ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $student['student_id'], $appointmentDate, $appointmentTime, $purpose,
            $isMinor, $parentGuardianName, $parentGuardianContact,
            $counselingConcerns, $currentMedications, $medicalConditions,
            $studentAddress, $consentAcknowledged
        ]);
        $appointmentId = $pdo->lastInsertId();

        // Create notifications
        $studentName = $student['first_name'] . ' ' . $student['last_name'];
        $formattedDate = date('F d, Y', strtotime($appointmentDate));
        $formattedTime = date('g:i A', strtotime($appointmentTime));
        
        // Student notification
        $studentMessage = "Your appointment request for {$formattedDate} at {$formattedTime} has been submitted successfully. You will be notified once a counselor is assigned.";
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
        $stmt->execute([$user_id, $studentMessage]);
        
        // Counselor notifications
        $counselorMessage = "New appointment request from {$studentName} for {$formattedDate} at {$formattedTime}. Concerns: {$purpose}";
        $stmt = $pdo->prepare("SELECT user_id FROM counselors WHERE status = 'Active'");
        $stmt->execute();
        $counselors = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
        foreach ($counselors as $counselorUserId) {
            $stmt->execute([$counselorUserId, $counselorMessage]);
        }
        
        // Admin notifications
        $adminMessage = "New appointment request from {$studentName} for {$formattedDate} at {$formattedTime}. Appointment ID: {$appointmentId}";
        $stmt = $pdo->prepare("SELECT user_id FROM admins");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
        foreach ($admins as $adminUserId) {
            $stmt->execute([$adminUserId, $adminMessage]);
        }

        $pdo->commit();
        
        // Send confirmation email to student
        $studentEmail = $student['email'];
        $studentFullName = $student['first_name'] . ' ' . $student['last_name'];
        
        $emailSubject = "Appointment Confirmation - Guidance Counseling System";
        $emailMessage = "
        <html>
        <head>
            <title>Appointment Confirmation</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4a90a4; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
                .appointment-details { background-color: white; padding: 20px; border-left: 4px solid #4a90a4; margin: 20px 0; }
                .detail-row { padding: 8px 0; border-bottom: 1px solid #eee; }
                .detail-label { font-weight: bold; color: #555; }
                .detail-value { color: #333; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .status-badge { display: inline-block; padding: 5px 15px; background-color: #ffc107; color: #000; border-radius: 20px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📅 Appointment Confirmation</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$studentFullName}</strong>,</p>
                    <p>Your appointment request has been successfully submitted and is now pending review.</p>
                    
                    <div class='appointment-details'>
                        <h3 style='margin-top: 0; color: #4a90a4;'>Appointment Details</h3>
                        <div class='detail-row'>
                            <span class='detail-label'>Appointment ID:</span>
                            <span class='detail-value'>#{$appointmentId}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Date:</span>
                            <span class='detail-value'>{$formattedDate}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Time:</span>
                            <span class='detail-value'>{$formattedTime}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Purpose:</span>
                            <span class='detail-value'>{$purpose}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Status:</span>
                            <span class='status-badge'>Pending</span>
                        </div>
                    </div>
                    
                    <p><strong>What's Next?</strong></p>
                    <ul>
                        <li>An administrator will review your appointment request</li>
                        <li>A counselor will be assigned to your appointment</li>
                        <li>You will receive another email once your appointment is confirmed</li>
                    </ul>
                    
                    <p>If you have any questions or need to make changes, please contact the guidance office.</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message from Guidance Counseling System.</p>
                    <p>Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Send the email (non-blocking - don't fail if email fails)
        sendEmail($studentEmail, $emailSubject, $emailMessage);
        
        $message = "Appointment booked successfully! You will receive a notification once a counselor is assigned.";
        $messageType = "success";

    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "Error: " . $e->getMessage();
        $messageType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Appointment - Counseling Informed Consent</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e9ecef;
        }
        .section-header i {
            color: #0d6efd;
            font-size: 1.5rem;
        }
        .section-header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.25rem;
        }
        .required::after {
            content: " *";
            color: red;
        }
        .concern-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }
        .concern-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            transition: all 0.2s;
        }
        .concern-item:hover {
            background-color: #f8f9fa;
            border-color: #0d6efd;
        }
        .concern-item input[type="checkbox"] {
            margin-right: 0.5rem;
            width: 18px;
            height: 18px;
        }
        .parent-guardian-section {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 5px;
        }
        .parent-guardian-section.show {
            display: block;
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
            <li><a href="./dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="#" class="active"><i class="fas fa-calendar-check"></i> Book Appointment</a></li>
            <li><a href="./notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="./profile.php"><i class="fa-solid fa-user-circle"></i> Profile</a></li>
            <li><a href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content">
        <div class="mb-4">
            <h2><i class="fas fa-file-signature"></i> Counseling Informed Consent</h2>
            <p class="text-muted">Catanduanes State University - Guidance Counseling and Testing Office</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form id="appointmentForm" method="POST" action="">
            
            <!-- Student-Client Information -->
            <div class="form-container">
                <div class="section-header">
                    <i class="fas fa-user"></i>
                    <h3>Student-Client Information</h3>
                </div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label required">Complete Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name']); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Age</label>
                        <input type="text" class="form-control" value="<?php echo $age; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="text" class="form-control" value="<?php echo $student['birthday'] ? date('F d, Y', strtotime($student['birthday'])) : 'Not set'; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Contact Number</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['contact_number'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Course and Year Level</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['course'] . ' - ' . $student['year_level']); ?>" readonly>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label required">Address</label>
                        <textarea class="form-control" name="studentAddress" rows="2" required><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label required">Is the student-client minor?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="isMinor" id="minorYes" value="yes" onchange="toggleParentSection()">
                                <label class="form-check-label" for="minorYes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="isMinor" id="minorNo" value="no" checked onchange="toggleParentSection()">
                                <label class="form-check-label" for="minorNo">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent/Guardian Section (conditional) -->
                <div id="parentGuardianSection" class="parent-guardian-section">
                    <h5><i class="fas fa-users"></i> Parent/Guardian Information</h5>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label required">Parent/Guardian Name</label>
                            <input type="text" class="form-control" name="parentGuardianName" id="parentGuardianName">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Contact Number</label>
                            <input type="text" class="form-control" name="parentGuardianContact" id="parentGuardianContact">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Counseling Concerns -->
            <div class="form-container">
                <div class="section-header">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Counseling Concerns</h3>
                </div>
                <p class="text-muted mb-3">Please select all that apply:</p>
                <div class="concern-grid">
                    <div class="concern-item">
                        <input type="checkbox" name="concern_academic" id="concern_academic" value="1">
                        <label for="concern_academic">Academic-Related Concerns</label>
                    </div>
                    <div class="concern-item">
                        <input type="checkbox" name="concern_career" id="concern_career" value="1">
                        <label for="concern_career">Career-Related Concerns</label>
                    </div>
                    <div class="concern-item">
                        <input type="checkbox" name="concern_abuse" id="concern_abuse" value="1">
                        <label for="concern_abuse">Abuse-Related Concerns</label>
                    </div>
                    <div class="concern-item">
                        <input type="checkbox" name="concern_adjustment" id="concern_adjustment" value="1">
                        <label for="concern_adjustment">Adjustment/Adaptation Concerns</label>
                    </div>
                    <div class="concern-item">
                        <input type="checkbox" name="concern_parents" id="concern_parents" value="1">
                        <label for="concern_parents">Parents/Guardian Concerns</label>
                    </div>
                    <div class="concern-item">
                        <input type="checkbox" name="concern_relational" id="concern_relational" value="1">
                        <label for="concern_relational">Relational-Related Concerns</label>
                    </div>
                    <div class="concern-item">
                        <input type="checkbox" name="concern_sexuality" id="concern_sexuality" value="1">
                        <label for="concern_sexuality">Sexuality-Related Concerns</label>
                    </div>
                    <div class="concern-item">
                        <input type="checkbox" name="concern_psycho" id="concern_psycho" value="1">
                        <label for="concern_psycho">Psycho-Emotional Concerns</label>
                    </div>
                    <div class="concern-item">
                        <input type="checkbox" name="concern_suicidal" id="concern_suicidal" value="1">
                        <label for="concern_suicidal">Suicidal Ideation/Tendencies</label>
                    </div>
                    <div class="concern-item">
                        <input type="checkbox" name="concern_pregnancy" id="concern_pregnancy" value="1">
                        <label for="concern_pregnancy">Pregnancy-Related Concerns</label>
                    </div>
                </div>
            </div>

            <!-- Medical Information -->
            <div class="form-container">
                <div class="section-header">
                    <i class="fas fa-heartbeat"></i>
                    <h3>Medical Information</h3>
                </div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Are you currently taking any medications? If yes, please specify below:</label>
                        <textarea class="form-control" name="currentMedications" rows="3" placeholder="List any medications you are currently taking..."></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Do you have any medical conditions that we should be aware of? If yes, please specify below:</label>
                        <textarea class="form-control" name="medicalConditions" rows="3" placeholder="List any medical conditions..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Appointment Details -->
            <div class="form-container">
                <div class="section-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Appointment Details</h3>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label required">Preferred Date</label>
                        <input type="date" class="form-control" name="appointmentDate" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Preferred Time</label>
                        <input type="time" class="form-control" name="appointmentTime" required>
                    </div>
                </div>
            </div>

            <!-- Consent Acknowledgment -->
            <div class="form-container">
                <div class="section-header">
                    <i class="fas fa-check-circle"></i>
                    <h3>Acknowledgment</h3>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="consentAcknowledged" id="consentAcknowledged" required>
                    <label class="form-check-label" for="consentAcknowledged">
                        <strong>I acknowledge</strong> that all information I provided in this form is true and accurate. 
                        I have read and understood the Counseling Informed Consent Agreement and agree to participate in counseling.
                    </label>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> By submitting this form, you consent to counseling services and understand your rights as outlined in the Student-Client's Rights section of the consent form.
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-paper-plane me-2"></i>Submit Appointment Request
                </button>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <script>
        function toggleParentSection() {
            const isMinor = document.getElementById('minorYes').checked;
            const section = document.getElementById('parentGuardianSection');
            const parentName = document.getElementById('parentGuardianName');
            const parentContact = document.getElementById('parentGuardianContact');
            
            if (isMinor) {
                section.classList.add('show');
                parentName.required = true;
                parentContact.required = true;
            } else {
                section.classList.remove('show');
                parentName.required = false;
                parentContact.required = false;
                parentName.value = '';
                parentContact.value = '';
            }
        }

        // Form validation
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            const concerns = document.querySelectorAll('input[name^="concern_"]:checked');
            if (concerns.length === 0) {
                e.preventDefault();
                alert('Please select at least one counseling concern.');
                return false;
            }
        });
    </script>
</body>
</html>
