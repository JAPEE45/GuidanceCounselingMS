<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'counselor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $notes = $_POST['notes'];
    $recommendations = $_POST['recommendations'];
    $observations = $_POST['observations'];
    $attendance = $_POST['attendance']; // Present, Absent

    if (!$appointment_id) {
        echo json_encode(['success' => false, 'message' => 'Missing appointment ID']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Insert Record
        $stmt = $pdo->prepare("INSERT INTO counseling_records (appointment_id, session_notes, recommendations, observations, attendance_status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$appointment_id, $notes, $recommendations, $observations, $attendance]);

        // Update Appointment Status
        $status = ($attendance === 'Absent') ? 'Cancelled' : 'Completed'; // Or keep 'Completed' for absent but noted?
        // Usually if absent, it's marked as such. I'll stick to 'Completed' if the session record is created, or 'Cancelled' if they didn't show up?
        // If they are absent, we still might want to record that they were absent.
        // The schema has 'attendance_status' in records.
        // So I'll mark appointment as 'Completed' (meaning the appointment slot is done) even if absent, 
        // or maybe 'Cancelled' is better for stats?
        // I'll use 'Completed' so it shows up in history as a processed appointment.
        
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'Completed' WHERE appointment_id = ?");
        $stmt->execute([$appointment_id]);

        $pdo->commit();
        echo json_encode(['success' => true]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
