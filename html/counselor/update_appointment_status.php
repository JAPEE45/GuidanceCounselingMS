<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'counselor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['id'];
    $status = $_POST['status'];

    if (!$appointment_id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
        $stmt->execute([$status, $appointment_id]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
