<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'counselor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $specialization = $_POST['specialization'];
    // Counselors don't have email in counselors table, it's in users table.
    // But I'll allow updating email too.
    $email = $_POST['email'];

    try {
        $pdo->beginTransaction();

        // Update counselors table
        $stmt = $pdo->prepare("UPDATE counselors SET first_name = ?, last_name = ?, specialization = ? WHERE user_id = ?");
        $stmt->execute([$firstName, $lastName, $specialization, $user_id]);

        // Update users table (email)
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE user_id = ?");
        $stmt->execute([$email, $user_id]);

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
