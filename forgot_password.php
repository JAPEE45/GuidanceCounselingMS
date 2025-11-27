<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'send_code') {
    $email = $_POST['email'];
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        // Generate Mock Code
        $code = "123456"; 
        // In a real app, store this in DB or Session with expiry
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_email'] = $email;
        
        echo json_encode(['success' => true, 'message' => 'Code sent! (Dev: 123456)']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found.']);
    }

} elseif ($action === 'verify_code') {
    $code = $_POST['code'];
    
    if (isset($_SESSION['reset_code']) && $_SESSION['reset_code'] === $code) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid code.']);
    }

} elseif ($action === 'reset_password') {
    $password = $_POST['password'];
    $email = $_SESSION['reset_email'] ?? '';
    
    if ($email) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashed_password, $email]);
        
        // Clear session
        unset($_SESSION['reset_code']);
        unset($_SESSION['reset_email']);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Session expired.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
