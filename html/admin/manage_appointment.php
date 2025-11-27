<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$appointment_id = $_POST['id'] ?? '';

if (!$appointment_id) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

try {
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'Confirmed' WHERE appointment_id = ?");
        $stmt->execute([$appointment_id]);
        echo json_encode(['success' => true]);

    } elseif ($action === 'decline') {
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'Declined' WHERE appointment_id = ?");
        $stmt->execute([$appointment_id]);
        echo json_encode(['success' => true]);

    } elseif ($action === 'reschedule') {
        $newDateTime = $_POST['newDateTime'];
        if (!$newDateTime) {
            throw new Exception("Missing new date time");
        }
        
        $date = date('Y-m-d', strtotime($newDateTime));
        $time = date('H:i:s', strtotime($newDateTime));

        $stmt = $pdo->prepare("UPDATE appointments SET status = 'Rescheduled', appointment_date = ?, appointment_time = ? WHERE appointment_id = ?");
        $stmt->execute([$date, $time, $appointment_id]);
        echo json_encode(['success' => true]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
