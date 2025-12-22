<?php
session_start();
require_once 'config/database.php';
require_once 'config/email_helper.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'send_code') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Please enter your email address.']);
        exit;
    }
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT user_id, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
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
        
        // Store email in session for the reset process (just for reference, not for security)
        $_SESSION['reset_email'] = $email;
        
        // Get user's name for email
        $userName = 'User';
        if ($user['role'] === 'student') {
            $stmt = $pdo->prepare("SELECT first_name, last_name FROM students WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);
            $profile = $stmt->fetch();
            if ($profile) {
                $userName = $profile['first_name'] . ' ' . $profile['last_name'];
            }
        } elseif ($user['role'] === 'counselor') {
            $stmt = $pdo->prepare("SELECT first_name, last_name FROM counselors WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);
            $profile = $stmt->fetch();
            if ($profile) {
                $userName = $profile['first_name'] . ' ' . $profile['last_name'];
            }
        } elseif ($user['role'] === 'admin') {
            $stmt = $pdo->prepare("SELECT first_name, last_name FROM admins WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);
            $profile = $stmt->fetch();
            if ($profile) {
                $userName = $profile['first_name'] . ' ' . $profile['last_name'];
            }
        }
        
        // Prepare email content
        $subject = "Password Reset Code - Guidance Counseling System";
        $message = "
        <html>
        <head>
            <title>Password Reset Code</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4a90a4; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
                .code { font-size: 32px; font-weight: bold; color: #4a90a4; text-align: center; padding: 20px; background-color: #e8f4f8; border-radius: 5px; letter-spacing: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .warning { color: #e74c3c; font-size: 13px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Password Reset</h1>
                </div>
                <div class='content'>
                    <p>Hello <strong>{$userName}</strong>,</p>
                    <p>We received a request to reset your password for your Guidance Counseling System account.</p>
                    <p>Your verification code is:</p>
                    <div class='code'>{$code}</div>
                    <p>Enter this code on the password reset page to continue.</p>
                    <p class='warning'>⚠️ This code will expire in <strong>15 minutes</strong>.</p>
                    <p>If you did not request a password reset, please ignore this email or contact the administrator if you have concerns.</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message from Guidance Counseling System.</p>
                    <p>Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Send email
        $emailSent = sendEmail($email, $subject, $message);
        
        if ($emailSent) {
            echo json_encode(['success' => true, 'message' => 'Verification code has been sent to your email.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again or contact administrator.']);
        }
        } else {
            // User not found - show clear message that email is not registered
            echo json_encode(['success' => false, 'message' => 'This email address is not registered in our system. Please check your email or create a new account.']);
        }
    } catch (PDOException $e) {
        error_log("Password reset error (send_code): " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
    } catch (Exception $e) {
        error_log("Password reset error (send_code): " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
    }
} elseif ($action === 'verify_code') {
    $code = trim($_POST['code'] ?? '');
    $email = $_SESSION['reset_email'] ?? '';
    
    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Please enter the verification code.']);
        exit;
    }
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Session expired. Please start the password reset process again.']);
        exit;
    }
    
    try {
        // Check if code exists in database, is not used, and is not expired
        $stmt = $pdo->prepare("
            SELECT id, user_id, expires_at 
            FROM password_resets 
            WHERE email = ? AND reset_code = ? AND used = 0 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$email, $code]);
        $resetRecord = $stmt->fetch();
        
        if (!$resetRecord) {
            echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please try again.']);
            exit;
        }
        
        // Check if code is expired
        if (strtotime($resetRecord['expires_at']) < time()) {
            // Mark as used since it's expired
            $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
            $stmt->execute([$resetRecord['id']]);
            
            echo json_encode(['success' => false, 'message' => 'Verification code has expired. Please request a new code.']);
            exit;
        }
        
        // Code is valid - store the reset ID in session for the password reset step
        $_SESSION['reset_id'] = $resetRecord['id'];
        $_SESSION['reset_user_id'] = $resetRecord['user_id'];
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log("Verify code error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while verifying the code. Please try again.']);
    } catch (Exception $e) {
        error_log("Verify code error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while verifying the code. Please try again.']);
    }

} elseif ($action === 'reset_password') {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? $password;
    $resetId = $_SESSION['reset_id'] ?? null;
    $userId = $_SESSION['reset_user_id'] ?? null;
    
    // Validate
    if (empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a new password.']);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        exit;
    }
    
    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }
    
    if (empty($resetId) || empty($userId)) {
        echo json_encode(['success' => false, 'message' => 'Session expired. Please start the password reset process again.']);
        exit;
    }
    
    // Verify the reset record is still valid (double-check from database)
    $stmt = $pdo->prepare("SELECT id, user_id, expires_at FROM password_resets WHERE id = ? AND used = 0");
    $stmt->execute([$resetId]);
    $resetRecord = $stmt->fetch();
    
    if (!$resetRecord) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired reset request. Please start over.']);
        exit;
    }
    
    if (strtotime($resetRecord['expires_at']) < time()) {
        $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
        $stmt->execute([$resetId]);
        echo json_encode(['success' => false, 'message' => 'Reset code has expired. Please start over.']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Update the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->execute([$hashed_password, $userId]);
        
        // Mark the reset code as used
        $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
        $stmt->execute([$resetId]);
        
        // Invalidate all other unused codes for this user
        $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0");
        $stmt->execute([$userId]);
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'message' => 'Password has been reset successfully. You can now login with your new password.']);
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Password reset error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while resetting password. Please try again.']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Password reset error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while resetting password. Please try again.']);
    }

} elseif ($action === 'resend_code') {
    $email = $_SESSION['reset_email'] ?? '';
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Session expired. Please start over.']);
        exit;
    }
    
    try {
        // Get user info
        $stmt = $pdo->prepare("SELECT user_id, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found. Please start over.']);
            exit;
        }
        
        // Generate a new random 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', time() + (15 * 60));
        
        // Invalidate any existing unused codes for this user
        $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0");
        $stmt->execute([$user['user_id']]);
        
        // Store new code in database
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, email, reset_code, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user['user_id'], $email, $code, $expiresAt]);ot found. Please start over.']);
        exit;
    }
    
    // Generate a new random 6-digit code
    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', time() + (15 * 60));
    
    // Invalidate any existing unused codes for this user
    $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0");
    $stmt->execute([$user['user_id']]);
    
    // Store new code in database
    $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, email, reset_code, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user['user_id'], $email, $code, $expiresAt]);
    
    // Clear any previous verification
    unset($_SESSION['reset_id']);
    unset($_SESSION['reset_user_id']);
    
    // Get user's name
    $userName = 'User';
    if ($user['role'] === 'student') {
        $stmt = $pdo->prepare("SELECT first_name, last_name FROM students WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        $profile = $stmt->fetch();
        if ($profile) $userName = $profile['first_name'] . ' ' . $profile['last_name'];
    } elseif ($user['role'] === 'counselor') {
        $stmt = $pdo->prepare("SELECT first_name, last_name FROM counselors WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        $profile = $stmt->fetch();
        if ($profile) $userName = $profile['first_name'] . ' ' . $profile['last_name'];
    } elseif ($user['role'] === 'admin') {
        $stmt = $pdo->prepare("SELECT first_name, last_name FROM admins WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        $profile = $stmt->fetch();
        if ($profile) $userName = $profile['first_name'] . ' ' . $profile['last_name'];
    }
    
    // Prepare email
    $subject = "Password Reset Code - Guidance Counseling System";
    $message = "
    <html>
    <head>
        <title>Password Reset Code</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #4a90a4; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
            .code { font-size: 32px; font-weight: bold; color: #4a90a4; text-align: center; padding: 20px; background-color: #e8f4f8; border-radius: 5px; letter-spacing: 5px; margin: 20px 0; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            .warning { color: #e74c3c; font-size: 13px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🔐 Password Reset</h1>
            </div>
    $emailSent = sendEmail($email, $subject, $message);
    
    if ($emailSent) {
        echo json_encode(['success' => true, 'message' => 'A new verification code has been sent to your email.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again.']);
    }
    } catch (PDOException $e) {
        error_log("Resend code error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>  </body>
    </html>
    ";
    
    $emailSent = sendEmail($email, $subject, $message);
    
    if ($emailSent) {
        echo json_encode(['success' => true, 'message' => 'A new verification code has been sent to your email.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
