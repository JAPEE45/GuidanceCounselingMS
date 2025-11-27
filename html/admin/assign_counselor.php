<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['appointment_id'] ?? null;
    $counselorId = $_POST['counselor_id'] ?? null;
    
    if (!$appointmentId || !$counselorId) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Get appointment and student details
        $stmt = $pdo->prepare("
            SELECT a.*, s.first_name as student_first, s.last_name as student_last, s.user_id as student_user_id
            FROM appointments a
            JOIN students s ON a.student_id = s.student_id
            WHERE a.appointment_id = ?
        ");
        $stmt->execute([$appointmentId]);
        $appointment = $stmt->fetch();
        
        if (!$appointment) {
            throw new Exception("Appointment not found");
        }
        
        // Get counselor details
        $stmt = $pdo->prepare("
            SELECT c.first_name, c.last_name, c.user_id
            FROM counselors c
            WHERE c.counselor_id = ?
        ");
        $stmt->execute([$counselorId]);
        $counselor = $stmt->fetch();
        
        if (!$counselor) {
            throw new Exception("Counselor not found");
        }
        
        // Update appointment with counselor
        $stmt = $pdo->prepare("UPDATE appointments SET counselor_id = ? WHERE appointment_id = ?");
        $stmt->execute([$counselorId, $appointmentId]);
        
        // Create notification for counselor
        $studentName = $appointment['student_first'] . ' ' . $appointment['student_last'];
        $formattedDate = date('F d, Y', strtotime($appointment['appointment_date']));
        $formattedTime = date('g:i A', strtotime($appointment['appointment_time']));
        
        $counselorMessage = "You have been assigned to {$studentName}'s appointment on {$formattedDate} at {$formattedTime}. Purpose: {$appointment['purpose']}";
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
        $stmt->execute([$counselor['user_id'], $counselorMessage]);
        
        // Create notification for student
        $counselorName = $counselor['first_name'] . ' ' . $counselor['last_name'];
        $studentMessage = "Counselor {$counselorName} has been assigned to your appointment on {$formattedDate} at {$formattedTime}.";
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
        $stmt->execute([$appointment['student_user_id'], $studentMessage]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => "Counselor {$counselorName} has been assigned to the appointment"
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
