<?php
require_once 'config/database.php';

if (!isset($_GET['user_id'])) {
    die("User ID required");
}

$userId = $_GET['user_id'];

// Generate new random password
$newPassword = bin2hex(random_bytes(4)); // 8-character password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update password
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
$stmt->execute([$hashedPassword, $userId]);

// Get user email
$stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$email = $stmt->fetchColumn();

echo "<h2>Password Reset Successful!</h2>";
echo "<div style='border: 2px solid #4CAF50; padding: 20px; margin: 20px; background-color: #f0f9f0;'>";
echo "<h3>New Login Credentials:</h3>";
echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
echo "<p><strong>New Password:</strong> <span style='font-size: 18px; color: #d32f2f; font-weight: bold;'>" . htmlspecialchars($newPassword) . "</span></p>";
echo "<p><strong>Login URL:</strong> <a href='index.php'>http://localhost/GuidanceCounselingMS/</a></p>";
echo "</div>";
echo "<p><a href='view_counselor_passwords.php'>← Back to Counselor List</a></p>";
echo "<hr>";
echo "<p><strong>Important:</strong> Please provide these credentials to the counselor manually (via phone, SMS, or in person).</p>";
?>
