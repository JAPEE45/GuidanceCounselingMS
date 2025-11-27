<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/email_helper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'add') {
        $name = $_POST['name'];
        $specialization = $_POST['specialization'];
        $email = $_POST['email'];
        $time = $_POST['time'];
        
        // Split name into first and last (simple split)
        $parts = explode(' ', $name);
        $lastName = array_pop($parts);
        $firstName = implode(' ', $parts);

        $pdo->beginTransaction();

        // Check if email exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("This email address is already registered in the system. Please use a different email address.");
        }

        // Generate random password
        $randomPassword = bin2hex(random_bytes(4)); // Generates 8-character password
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
        
        // Create User
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'counselor')");
        $stmt->execute([$email, $hashedPassword]);
        $userId = $pdo->lastInsertId();

        // Create Counselor
        $stmt = $pdo->prepare("INSERT INTO counselors (user_id, first_name, last_name, specialization, status) VALUES (?, ?, ?, ?, 'Active')");
        $stmt->execute([$userId, $firstName, $lastName, $specialization]);
        
        $pdo->commit();
        
        // Get admin's name for email sender
        $stmt = $pdo->prepare("SELECT first_name, last_name FROM admins WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $admin = $stmt->fetch();
        $adminName = $admin ? $admin['first_name'] . ' ' . $admin['last_name'] : 'System Administrator';
        
        // Send email with credentials
        $subject = "Your Guidance Counseling System Account";
        $message = "
        <html>
        <head>
            <title>Welcome to Guidance Counseling System</title>
        </head>
        <body>
            <h2>Welcome, {$firstName} {$lastName}!</h2>
            <p>Your counselor account has been created by <strong>{$adminName}</strong>.</p>
            <h3>Login Credentials:</h3>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Password:</strong> {$randomPassword}</p>
            <p><strong>Login URL:</strong> <a href='http://localhost/GuidanceCounselingMS/'>http://localhost/GuidanceCounselingMS/</a></p>
            <hr>
            <p><em>Please change your password after your first login for security purposes.</em></p>
            <p>If you have any questions, please contact the system administrator.</p>
        </body>
        </html>
        ";
        
        // Attempt to send email using helper function with admin's name
        $emailSent = sendEmail($email, $subject, $message, $adminName);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Counselor added successfully!',
            'email_sent' => $emailSent,
            'temp_password' => $randomPassword // For admin to see if email fails
        ]);

    } elseif ($action === 'update') {
        $id = $_POST['id']; // Counselor ID
        $name = $_POST['name'];
        $specialization = $_POST['specialization'];
        $email = $_POST['email'];
        // $time = $_POST['time'];

        $parts = explode(' ', $name);
        $lastName = array_pop($parts);
        $firstName = implode(' ', $parts);

        $pdo->beginTransaction();

        // Update Counselor
        $stmt = $pdo->prepare("UPDATE counselors SET first_name = ?, last_name = ?, specialization = ? WHERE counselor_id = ?");
        $stmt->execute([$firstName, $lastName, $specialization, $id]);

        // Get User ID
        $stmt = $pdo->prepare("SELECT user_id FROM counselors WHERE counselor_id = ?");
        $stmt->execute([$id]);
        $userId = $stmt->fetchColumn();

        // Update User Email
        if ($userId) {
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE user_id = ?");
            $stmt->execute([$email, $userId]);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);

    } elseif ($action === 'toggle_status') {
        $id = $_POST['id'];
        $status = $_POST['status']; // 'Active' or 'Inactive'

        $stmt = $pdo->prepare("UPDATE counselors SET status = ? WHERE counselor_id = ?");
        $stmt->execute([$status, $id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
