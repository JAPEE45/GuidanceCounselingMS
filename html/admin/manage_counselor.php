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
        $name = trim($_POST['name'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $time = trim($_POST['time'] ?? '');
        
        // Validate required fields
        if (empty($name)) {
            throw new Exception("Name is required.");
        }
        if (empty($specialization)) {
            throw new Exception("Specialization is required.");
        }
        if (empty($email)) {
            throw new Exception("Email is required.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
        
        // Split name into first and last (simple split)
        $parts = array_filter(explode(' ', $name)); // array_filter removes empty elements
        $parts = array_values($parts); // Re-index array
        
        if (count($parts) < 2) {
            throw new Exception("Please enter both first name and last name (separated by space).");
        }
        
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
        $id = $_POST['id'] ?? ''; // Counselor ID
        $name = trim($_POST['name'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $email = trim($_POST['email'] ?? '');
        // $time = $_POST['time'];

        // Validate required fields
        if (empty($id)) {
            throw new Exception("Counselor ID is required.");
        }
        if (empty($name)) {
            throw new Exception("Name is required.");
        }
        if (empty($email)) {
            throw new Exception("Email is required.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }

        $parts = array_filter(explode(' ', $name));
        $parts = array_values($parts);
        
        if (count($parts) < 2) {
            throw new Exception("Please enter both first name and last name (separated by space).");
        }
        
        $lastName = array_pop($parts);
        $firstName = implode(' ', $parts);

        $pdo->beginTransaction();

        // Get User ID first
        $stmt = $pdo->prepare("SELECT user_id FROM counselors WHERE counselor_id = ?");
        $stmt->execute([$id]);
        $userId = $stmt->fetchColumn();
        
        if (!$userId) {
            throw new Exception("Counselor not found.");
        }

        // Check if email exists for another user
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND user_id != ?");
        $stmt->execute([$email, $userId]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("This email address is already registered to another user.");
        }

        // Update Counselor
        $stmt = $pdo->prepare("UPDATE counselors SET first_name = ?, last_name = ?, specialization = ? WHERE counselor_id = ?");
        $stmt->execute([$firstName, $lastName, $specialization, $id]);

        // Update User Email
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE user_id = ?");
        $stmt->execute([$email, $userId]);

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
