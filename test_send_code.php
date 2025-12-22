<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Test the send_code action
$email = 'test@example.com'; // Replace with a real email from your database

try {
    // Check if email exists
    $stmt = $pdo->prepare("SELECT user_id, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'No user found with that email',
            'email_tested' => $email
        ], JSON_PRETTY_PRINT);
        exit;
    }

    // Generate a random 6-digit code
    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // Set expiry time (15 minutes from now)
    $expiresAt = date('Y-m-d H:i:s', time() + (15 * 60));

    // Invalidate any existing unused codes for this user
    $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0");
    $stmt->execute([$user['user_id']]);

    // Store code in database
    $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, email, reset_code, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user['user_id'], $email, $code, $expiresAt]);

    // Test email sending
    require_once 'config/email_helper.php';
    
    $userName = 'Test User';
    $subject = "Password Reset Verification Code";
    $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>Password Reset Request</h2>
            <p>Hello $userName,</p>
            <p>You requested to reset your password. Use the verification code below:</p>
            <div style='background-color: #f4f4f4; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                $code
            </div>
            <p><strong>This code will expire in 15 minutes.</strong></p>
            <p>If you didn't request this, please ignore this email.</p>
        </div>
    ";

    $emailSent = sendEmail($email, $subject, $message, 'Guidance Counseling System');

    echo json_encode([
        'success' => true,
        'message' => 'Test completed',
        'user_id' => $user['user_id'],
        'code_generated' => $code,
        'code_stored' => true,
        'email_sent' => $emailSent,
        'expires_at' => $expiresAt
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
