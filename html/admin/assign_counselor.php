<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/email_helper.php';

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
            SELECT a.*, s.first_name as student_first, s.last_name as student_last, s.user_id as student_user_id, u.email as student_email
            FROM appointments a
            JOIN students s ON a.student_id = s.student_id
            JOIN users u ON s.user_id = u.user_id
            WHERE a.appointment_id = ?
        ");
        $stmt->execute([$appointmentId]);
        $appointment = $stmt->fetch();
        
        if (!$appointment) {
            throw new Exception("Appointment not found");
        }
        
        // Get counselor details
        $stmt = $pdo->prepare("
            SELECT c.first_name, c.last_name, c.user_id, c.specialization, u.email as counselor_email
            FROM counselors c
            JOIN users u ON c.user_id = u.user_id
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
        $counselorName = $counselor['first_name'] . ' ' . $counselor['last_name'];
        $formattedDate = date('F d, Y', strtotime($appointment['appointment_date']));
        $formattedTime = date('g:i A', strtotime($appointment['appointment_time']));
        
        $counselorMessage = "You have been assigned to {$studentName}'s appointment on {$formattedDate} at {$formattedTime}. Purpose: {$appointment['purpose']}";
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
        $stmt->execute([$counselor['user_id'], $counselorMessage]);
        
        // Create notification for student
        $studentMessage = "Counselor {$counselorName} has been assigned to your appointment on {$formattedDate} at {$formattedTime}.";
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
        $stmt->execute([$appointment['student_user_id'], $studentMessage]);
        
        $pdo->commit();
        
        // Send email to student about counselor assignment
        $studentEmail = $appointment['student_email'];
        $studentFullName = $appointment['student_first'] . ' ' . $appointment['student_last'];
        
        $emailSubject = "Counselor Assigned to Your Appointment - Guidance Counseling System";
        $emailMessage = "
        <html>
        <head>
            <title>Counselor Assigned</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #28a745; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
                .appointment-details { background-color: white; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0; }
                .detail-row { padding: 8px 0; border-bottom: 1px solid #eee; }
                .detail-label { font-weight: bold; color: #555; }
                .detail-value { color: #333; }
                .counselor-info { background-color: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .status-badge { display: inline-block; padding: 5px 15px; background-color: #28a745; color: white; border-radius: 20px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Counselor Assigned</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$studentFullName}</strong>,</p>
                    <p>Great news! A counselor has been assigned to your appointment.</p>
                    
                    <div class='counselor-info'>
                        <h3 style='margin-top: 0; color: #28a745;'>👤 Your Assigned Counselor</h3>
                        <p style='font-size: 18px; margin: 10px 0;'><strong>{$counselorName}</strong></p>
                        <p style='margin: 5px 0;'>Specialization: {$counselor['specialization']}</p>
                    </div>
                    
                    <div class='appointment-details'>
                        <h3 style='margin-top: 0; color: #28a745;'>Appointment Details</h3>
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
                            <span class='detail-value'>{$appointment['purpose']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Status:</span>
                            <span class='status-badge'>Confirmed</span>
                        </div>
                    </div>
                    
                    <p><strong>Important Reminders:</strong></p>
                    <ul>
                        <li>Please arrive 10 minutes before your scheduled time</li>
                        <li>Bring any relevant documents or information</li>
                        <li>If you need to cancel or reschedule, please inform us in advance</li>
                    </ul>
                    
                    <p>We look forward to seeing you at your appointment!</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message from Guidance Counseling System.</p>
                    <p>Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Send email to student (non-blocking)
        sendEmail($studentEmail, $emailSubject, $emailMessage);
        
        // Send email to counselor about assignment
        $counselorEmail = $counselor['counselor_email'];
        $counselorEmailSubject = "New Appointment Assignment - Guidance Counseling System";
        $counselorEmailMessage = "
        <html>
        <head>
            <title>New Appointment Assignment</title>
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
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📋 New Appointment Assignment</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$counselorName}</strong>,</p>
                    <p>You have been assigned to a new counseling appointment.</p>
                    
                    <div class='appointment-details'>
                        <h3 style='margin-top: 0; color: #4a90a4;'>Appointment Details</h3>
                        <div class='detail-row'>
                            <span class='detail-label'>Student:</span>
                            <span class='detail-value'>{$studentFullName}</span>
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
                            <span class='detail-value'>{$appointment['purpose']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Appointment ID:</span>
                            <span class='detail-value'>#{$appointmentId}</span>
                        </div>
                    </div>
                    
                    <p>Please log in to the system to view complete appointment details and prepare accordingly.</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message from Guidance Counseling System.</p>
                    <p>Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Send email to counselor (non-blocking)
        sendEmail($counselorEmail, $counselorEmailSubject, $counselorEmailMessage);
        
        echo json_encode([
            'success' => true,
            'message' => "Counselor {$counselorName} has been assigned to the appointment successfully!"
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
