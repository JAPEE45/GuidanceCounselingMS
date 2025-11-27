<?php
require_once 'config/database.php';

// Counselor account details - CHANGE THESE AS NEEDED
$email = 'counselor@gcms.edu';
$password = 'counselor123';  // Change this to a secure password
$firstName = 'Maria';
$lastName = 'Santos';
$department = 'Guidance Office';
$specialization = 'Academic & Career Counseling';
$contactNumber = '09123456789';

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
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'counselor')");
    $stmt->execute([$email, $hashedPassword]);
    $userId = $pdo->lastInsertId();
    
    // 2. Create counselor record
    $stmt = $pdo->prepare("
        INSERT INTO counselors (user_id, first_name, last_name, department, specialization, contact_number, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'Active')
    ");
    $stmt->execute([$userId, $firstName, $lastName, $department, $specialization, $contactNumber]);
    
    $pdo->commit();
    
    echo "<h2 style='color: green;'>✓ Counselor Account Created Successfully!</h2>";
    echo "<div style='border: 2px solid #4CAF50; padding: 20px; margin: 20px; background-color: #f0f9f0;'>";
    echo "<h3>Login Credentials:</h3>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>Password:</strong> " . htmlspecialchars($password) . "</p>";
    echo "<p><strong>Role:</strong> Counselor</p>";
    echo "<hr>";
    echo "<h3>Counselor Details:</h3>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($firstName . ' ' . $lastName) . "</p>";
    echo "<p><strong>Department:</strong> " . htmlspecialchars($department) . "</p>";
    echo "<p><strong>Specialization:</strong> " . htmlspecialchars($specialization) . "</p>";
    echo "<p><strong>Contact:</strong> " . htmlspecialchars($contactNumber) . "</p>";
    echo "</div>";
    echo "<p><a href='index.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "<p style='color: red;'>Error creating counselor account: " . $e->getMessage() . "</p>";
}
?>
