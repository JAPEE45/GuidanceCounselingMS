<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Appointment ID required']);
    exit;
}

$appointmentId = $_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT a.*, 
               s.first_name, s.last_name, s.student_number, s.course, s.year_level, 
               s.contact_number, s.address as student_address_profile, u.email
        FROM appointments a
        JOIN students s ON a.student_id = s.student_id
        JOIN users u ON s.user_id = u.user_id
        WHERE a.appointment_id = ?
    ");
    $stmt->execute([$appointmentId]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found']);
        exit;
    }

    // Format data for display
    $response = [
        'success' => true,
        'data' => [
            'id' => $appointment['appointment_id'],
            'student_name' => $appointment['first_name'] . ' ' . $appointment['last_name'],
            'student_number' => $appointment['student_number'],
            'course_year' => $appointment['course'] . ' - ' . $appointment['year_level'],
            'contact' => $appointment['contact_number'],
            'email' => $appointment['email'],
            'address' => $appointment['student_address'] ?: $appointment['student_address_profile'], // Use address from appointment if available (snapshot), else profile
            
            'date' => date('F d, Y', strtotime($appointment['appointment_date'])),
            'time' => date('g:i A', strtotime($appointment['appointment_time'])),
            'purpose' => $appointment['purpose'],
            'concerns' => json_decode($appointment['counseling_concerns'] ?? '[]'),
            
            'is_minor' => (bool)$appointment['is_minor'],
            'parent_name' => $appointment['parent_guardian_name'],
            'parent_contact' => $appointment['parent_guardian_contact'],
            
            'medications' => $appointment['current_medications'],
            'conditions' => $appointment['medical_conditions'],
            'consent' => (bool)$appointment['consent_acknowledged']
        ]
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
