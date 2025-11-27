<?php
require_once 'config/database.php';

// Admin account details - CHANGE THESE AS NEEDED
$email = 'admin@gcms.edu';
$password = 'admin123';  // Change this to a secure password
$firstName = 'Admin';
$lastName = 'User';

try {
    $pdo->beginTransaction();
    
    // Check if email already exists
    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        die("<p style='color: red;'>Error: Email already exists! Please use a different email.</p>");
    }
    
    // 1. Create user account
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'admin')");
    $stmt->execute([$email, $hashedPassword]);
    $userId = $pdo->lastInsertId();
    
    // 2. Create admin record
    $stmt = $pdo->prepare("INSERT INTO admins (user_id, first_name, last_name) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $firstName, $lastName]);
    
    $pdo->commit();
    
    echo "<h2 style='color: green;'>✓ Admin Account Created Successfully!</h2>";
    echo "<div style='border: 2px solid #4CAF50; padding: 20px; margin: 20px; background-color: #f0f9f0;'>";
    echo "<h3>Login Credentials:</h3>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>Password:</strong> " . htmlspecialchars($password) . "</p>";
    echo "<p><strong>Role:</strong> Admin</p>";
    echo "<hr>";
    echo "<h3>Admin Details:</h3>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($firstName . ' ' . $lastName) . "</p>";
    echo "</div>";
    echo "<p><a href='index.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    echo "<hr>";
    echo "<h3>What you can do as Admin:</h3>";
    echo "<ul>";
    echo "<li>Manage student accounts</li>";
    echo "<li>Manage counselor accounts</li>";
    echo "<li>View and manage all appointments</li>";
    echo "<li>Access system settings</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "<p style='color: red;'>Error creating admin account: " . $e->getMessage() . "</p>";
}
?>
