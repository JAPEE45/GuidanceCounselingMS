<?php
require_once __DIR__ . '/../config/database.php';

function runTest($name, $callback) {
    echo "Testing $name... ";
    try {
        $result = $callback();
        if ($result) {
            echo "PASSED\n";
        } else {
            echo "FAILED\n";
        }
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}

// Global variables to hold IDs for cleanup
$testUserId = null;
$testStudentId = null;
$testCounselorId = null;
$testAppointmentId = null;

// 1. Test Database Connection
runTest("Database Connection", function() use ($pdo) {
    return $pdo !== null;
});

// 2. Test User Creation (Student)
runTest("Create Test Student User", function() use ($pdo, &$testUserId, &$testStudentId) {
    $email = 'test_student_' . time() . '@example.com';
    $password = password_hash('password123', PASSWORD_DEFAULT);

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'student')");
    $stmt->execute([$email, $password]);
    $testUserId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO students (user_id, student_number, first_name, last_name, birthday, gender, contact_number, course, year_level) VALUES (?, ?, 'Test', 'Student', '2000-01-01', 'Male', '09123456789', 'BSIT', '1st Year')");
    $stmt->execute([$testUserId, 'TEST-' . time()]);
    $testStudentId = $pdo->lastInsertId();
    $pdo->commit();

    return $testUserId > 0 && $testStudentId > 0;
});

// 3. Test User Creation (Counselor) - Needed for appointment
runTest("Create Test Counselor User", function() use ($pdo, &$testCounselorId) {
    $email = 'test_counselor_' . time() . '@example.com';
    $password = password_hash('password123', PASSWORD_DEFAULT);

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'counselor')");
    $stmt->execute([$email, $password]);
    $cUserId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO counselors (user_id, first_name, last_name, specialization, status) VALUES (?, 'Test', 'Counselor', 'General', 'Active')");
    $stmt->execute([$cUserId]);
    $testCounselorId = $pdo->lastInsertId();
    $pdo->commit();

    return $testCounselorId > 0;
});

// 4. Test Appointment Creation
runTest("Create Appointment", function() use ($pdo, $testStudentId, $testCounselorId, &$testAppointmentId) {
    if (!$testStudentId || !$testCounselorId) return false;

    $stmt = $pdo->prepare("INSERT INTO appointments (student_id, counselor_id, appointment_date, appointment_time, purpose, status) VALUES (?, ?, CURDATE(), '10:00:00', 'Academic Counseling', 'Pending')");
    $stmt->execute([$testStudentId, $testCounselorId]);
    $testAppointmentId = $pdo->lastInsertId();

    return $testAppointmentId > 0;
});

// 5. Test Appointment Status Update
runTest("Update Appointment Status", function() use ($pdo, $testAppointmentId) {
    if (!$testAppointmentId) return false;

    $stmt = $pdo->prepare("UPDATE appointments SET status = 'Confirmed' WHERE appointment_id = ?");
    $stmt->execute([$testAppointmentId]);

    $stmt = $pdo->prepare("SELECT status FROM appointments WHERE appointment_id = ?");
    $stmt->execute([$testAppointmentId]);
    $status = $stmt->fetchColumn();

    return $status === 'Confirmed';
});

// 6. Test Session Record Creation
runTest("Create Session Record", function() use ($pdo, $testAppointmentId) {
    if (!$testAppointmentId) return false;

    $stmt = $pdo->prepare("INSERT INTO counseling_records (appointment_id, session_notes, recommendations, attendance_status) VALUES (?, 'Test notes', 'Test reco', 'Present')");
    $stmt->execute([$testAppointmentId]);
    
    return $pdo->lastInsertId() > 0;
});

// Cleanup
echo "\nCleaning up test data...\n";
if ($testAppointmentId) {
    $pdo->exec("DELETE FROM counseling_records WHERE appointment_id = $testAppointmentId");
    $pdo->exec("DELETE FROM appointments WHERE appointment_id = $testAppointmentId");
}
if ($testStudentId) {
    // Get user_id for student
    $stmt = $pdo->query("SELECT user_id FROM students WHERE student_id = $testStudentId");
    $uid = $stmt->fetchColumn();
    $pdo->exec("DELETE FROM students WHERE student_id = $testStudentId");
    if ($uid) $pdo->exec("DELETE FROM users WHERE user_id = $uid");
}
if ($testCounselorId) {
    // Get user_id for counselor
    $stmt = $pdo->query("SELECT user_id FROM counselors WHERE counselor_id = $testCounselorId");
    $uid = $stmt->fetchColumn();
    $pdo->exec("DELETE FROM counselors WHERE counselor_id = $testCounselorId");
    if ($uid) $pdo->exec("DELETE FROM users WHERE user_id = $uid");
}
echo "Cleanup complete.\n";
?>
