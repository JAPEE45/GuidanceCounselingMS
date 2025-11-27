<?php
require_once 'config/database.php';

// Handle password reset
if (isset($_GET['reset_user_id'])) {
    $userId = $_GET['reset_user_id'];
    $newPassword = bin2hex(random_bytes(4)); // 8-character password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->execute([$hashedPassword, $userId]);
    
    $stmt = $pdo->prepare("SELECT email, role FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 20px; margin: 20px; border-radius: 5px;'>";
    echo "<h3 style='color: #155724;'>✓ Password Reset Successful!</h3>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
    echo "<p><strong>New Password:</strong> <span style='font-size: 20px; color: #d32f2f; font-weight: bold; background: #fff3cd; padding: 5px 10px; border-radius: 3px;'>" . htmlspecialchars($newPassword) . "</span></p>";
    echo "<p><strong>Role:</strong> " . htmlspecialchars($user['role']) . "</p>";
    echo "<p style='margin-top: 15px;'><a href='list_all_users.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to User List</a></p>";
    echo "</div>";
    echo "<hr>";
}

try {
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #343a40; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f5f5f5; }
        .btn-reset { background: #dc3545; color: white; padding: 5px 15px; text-decoration: none; border-radius: 3px; font-size: 12px; }
        .btn-reset:hover { background: #c82333; }
        .role-badge { padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .role-student { background: #007bff; color: white; }
        .role-counselor { background: #28a745; color: white; }
        .role-admin { background: #ffc107; color: black; }
    </style>";
    
    echo "<h2>All Users in the System</h2>";
    echo "<p><em>Note: Passwords are hashed and cannot be displayed. Use 'Reset Password' to generate a new one.</em></p>";
    
    // Get all users
    $stmt = $pdo->query("SELECT user_id, email, role FROM users ORDER BY role, user_id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr>";
    echo "<th>User ID</th><th>Email</th><th>Role</th><th>Details</th><th>Action</th>";
    echo "</tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        
        // Role badge
        $roleClass = 'role-' . strtolower($user['role']);
        echo "<td><span class='role-badge {$roleClass}'>" . htmlspecialchars(strtoupper($user['role'])) . "</span></td>";
        
        // Get additional details based on role
        $details = "";
        if ($user['role'] === 'student') {
            $detailStmt = $pdo->prepare("SELECT first_name, last_name, student_number FROM students WHERE user_id = ?");
            $detailStmt->execute([$user['user_id']]);
            $student = $detailStmt->fetch(PDO::FETCH_ASSOC);
            if ($student) {
                $details = $student['first_name'] . ' ' . $student['last_name'] . ' (ID: ' . $student['student_number'] . ')';
            }
        } elseif ($user['role'] === 'counselor') {
            $detailStmt = $pdo->prepare("SELECT first_name, last_name, specialization FROM counselors WHERE user_id = ?");
            $detailStmt->execute([$user['user_id']]);
            $counselor = $detailStmt->fetch(PDO::FETCH_ASSOC);
            if ($counselor) {
                $details = $counselor['first_name'] . ' ' . $counselor['last_name'] . ' (' . ($counselor['specialization'] ?? 'No specialization') . ')';
            }
        } elseif ($user['role'] === 'admin') {
            $detailStmt = $pdo->prepare("SELECT first_name, last_name FROM admins WHERE user_id = ?");
            $detailStmt->execute([$user['user_id']]);
            $admin = $detailStmt->fetch(PDO::FETCH_ASSOC);
            if ($admin) {
                $details = $admin['first_name'] . ' ' . $admin['last_name'];
            }
        }
        
        echo "<td>" . htmlspecialchars($details) . "</td>";
        echo "<td><a href='?reset_user_id=" . $user['user_id'] . "' class='btn-reset' onclick='return confirm(\"Reset password for " . htmlspecialchars($user['email']) . "?\")'>Reset Password</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>Summary:</h3>";
    $roleCount = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role")->fetchAll(PDO::FETCH_ASSOC);
    echo "<ul>";
    foreach ($roleCount as $rc) {
        echo "<li><strong>" . htmlspecialchars($rc['role']) . ":</strong> " . $rc['count'] . " user(s)</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
