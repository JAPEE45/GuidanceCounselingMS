<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];

    try {
        $pdo->beginTransaction();

        // Update admins table
        $stmt = $pdo->prepare("UPDATE admins SET first_name = ?, last_name = ?, email = ? WHERE user_id = ?");
        $stmt->execute([$firstName, $lastName, $email, $user_id]);

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
