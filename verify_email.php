<?php
require_once 'config/database.php';

if (!isset($_GET['token'])) {
    die('Invalid verification link.');
}

$token = $_GET['token'];

try {
    // Find user with this token
    $stmt = $pdo->prepare("SELECT user_id, email, email_verified FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $error = 'Invalid or expired verification link.';
    } elseif ($user['email_verified'] == 1) {
        $message = 'Your email has already been verified. You can now login.';
    } else {
        // Mark email as verified
        $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, verification_token = NULL WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        
        $success = 'Email verified successfully! You can now login to your account.';
    }
} catch (PDOException $e) {
    $error = 'An error occurred. Please try again or contact support.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Guidance Counseling System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verification-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 500px;
            text-align: center;
        }
        .icon-wrapper {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .success-icon {
            color: #28a745;
        }
        .error-icon {
            color: #dc3545;
        }
        .info-icon {
            color: #17a2b8;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #333;
        }
        p {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            font-size: 16px;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: scale(1.05);
            color: white;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <?php if (isset($success)): ?>
            <div class="icon-wrapper">
                <i class="fas fa-check-circle success-icon"></i>
            </div>
            <h1>Email Verified!</h1>
            <p><?php echo $success; ?></p>
            <a href="index.php" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Login Now
            </a>
        <?php elseif (isset($message)): ?>
            <div class="icon-wrapper">
                <i class="fas fa-info-circle info-icon"></i>
            </div>
            <h1>Already Verified</h1>
            <p><?php echo $message; ?></p>
            <a href="index.php" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Go to Login
            </a>
        <?php else: ?>
            <div class="icon-wrapper">
                <i class="fas fa-times-circle error-icon"></i>
            </div>
            <h1>Verification Failed</h1>
            <p><?php echo $error ?? 'An error occurred.'; ?></p>
            <a href="index.php" class="btn-login">
                <i class="fas fa-home me-2"></i>Back to Home
            </a>
        <?php endif; ?>
    </div>
</body>
</html>
