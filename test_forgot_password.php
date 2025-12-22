<?php
/**
 * Test Forgot Password Functionality
 * Access: http://localhost/GuidanceCounselingMS/test_forgot_password.php
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Forgot Password</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>
    <style>
        body { padding: 40px; background-color: #f8f9fa; }
        .container { max-width: 900px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #dee2e6; border-radius: 5px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #0d6efd; }
    </style>
</head>
<body>
    <div class='container'>
        <h2><i class='fas fa-vial'></i> Forgot Password System Test</h2>
        <p class='text-muted'>This page tests all components of the forgot password functionality.</p>
        <hr>";

$allTestsPassed = true;
$testResults = [];

// Test 1: Check if password_resets table exists
echo "<div class='test-section'>";
echo "<h5><i class='fas fa-database'></i> Test 1: Database Table Check</h5>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'password_resets'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "<p class='success'><i class='fas fa-check-circle'></i> ✓ password_resets table exists</p>";
        $testResults[] = ['test' => 'Table Exists', 'status' => 'PASS'];
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE password_resets");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $requiredColumns = ['id', 'user_id', 'email', 'reset_code', 'expires_at', 'used', 'created_at'];
        
        $missingColumns = array_diff($requiredColumns, $columns);
        if (empty($missingColumns)) {
            echo "<p class='success'><i class='fas fa-check-circle'></i> ✓ All required columns present</p>";
            $testResults[] = ['test' => 'Table Structure', 'status' => 'PASS'];
        } else {
            echo "<p class='error'><i class='fas fa-times-circle'></i> ✗ Missing columns: " . implode(', ', $missingColumns) . "</p>";
            $testResults[] = ['test' => 'Table Structure', 'status' => 'FAIL'];
            $allTestsPassed = false;
        }
    } else {
        echo "<p class='error'><i class='fas fa-times-circle'></i> ✗ password_resets table does NOT exist</p>";
        echo "<p class='info'><i class='fas fa-info-circle'></i> Run <a href='create_password_resets_table.php'>create_password_resets_table.php</a> to create the table.</p>";
        $testResults[] = ['test' => 'Table Exists', 'status' => 'FAIL'];
        $allTestsPassed = false;
    }
} catch (PDOException $e) {
    echo "<p class='error'><i class='fas fa-times-circle'></i> ✗ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $testResults[] = ['test' => 'Database Connection', 'status' => 'FAIL'];
    $allTestsPassed = false;
}
echo "</div>";

// Test 2: Check if forgot_password.php exists and is accessible
echo "<div class='test-section'>";
echo "<h5><i class='fas fa-file-code'></i> Test 2: Required Files Check</h5>";

$requiredFiles = [
    'forgot_password.php' => 'Password reset handler',
    'config/email_helper.php' => 'Email sending function',
    'config/email.php' => 'Email configuration',
    'assets/scripts/index.js' => 'Frontend JavaScript'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<p class='success'><i class='fas fa-check-circle'></i> ✓ {$file} ({$description})</p>";
        $testResults[] = ['test' => $file, 'status' => 'PASS'];
    } else {
        echo "<p class='error'><i class='fas fa-times-circle'></i> ✗ {$file} NOT FOUND</p>";
        $testResults[] = ['test' => $file, 'status' => 'FAIL'];
        $allTestsPassed = false;
    }
}
echo "</div>";

// Test 3: Check email configuration
echo "<div class='test-section'>";
echo "<h5><i class='fas fa-envelope'></i> Test 3: Email Configuration Check</h5>";
try {
    $emailConfig = require 'config/email.php';
    
    if (!empty($emailConfig['smtp_username']) && $emailConfig['smtp_username'] !== 'your-email@gmail.com') {
        echo "<p class='success'><i class='fas fa-check-circle'></i> ✓ SMTP username configured: {$emailConfig['smtp_username']}</p>";
        $testResults[] = ['test' => 'SMTP Username', 'status' => 'PASS'];
    } else {
        echo "<p class='error'><i class='fas fa-times-circle'></i> ✗ SMTP username not configured</p>";
        $testResults[] = ['test' => 'SMTP Username', 'status' => 'FAIL'];
        $allTestsPassed = false;
    }
    
    if (!empty($emailConfig['smtp_password']) && $emailConfig['smtp_password'] !== 'your-app-password') {
        echo "<p class='success'><i class='fas fa-check-circle'></i> ✓ SMTP password configured (hidden)</p>";
        $testResults[] = ['test' => 'SMTP Password', 'status' => 'PASS'];
    } else {
        echo "<p class='error'><i class='fas fa-times-circle'></i> ✗ SMTP password not configured</p>";
        $testResults[] = ['test' => 'SMTP Password', 'status' => 'FAIL'];
        $allTestsPassed = false;
    }
    
    echo "<p class='info'><i class='fas fa-info-circle'></i> SMTP Host: {$emailConfig['smtp_host']}</p>";
    echo "<p class='info'><i class='fas fa-info-circle'></i> SMTP Port: {$emailConfig['smtp_port']}</p>";
    
} catch (Exception $e) {
    echo "<p class='error'><i class='fas fa-times-circle'></i> ✗ Email config error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $testResults[] = ['test' => 'Email Config', 'status' => 'FAIL'];
    $allTestsPassed = false;
}
echo "</div>";

// Test 4: Check if there are users to test with
echo "<div class='test-section'>";
echo "<h5><i class='fas fa-users'></i> Test 4: Test Users Check</h5>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    
    if ($userCount > 0) {
        echo "<p class='success'><i class='fas fa-check-circle'></i> ✓ {$userCount} user(s) found in database</p>";
        $testResults[] = ['test' => 'Users Exist', 'status' => 'PASS'];
        
        // Show sample users
        $stmt = $pdo->query("SELECT u.email, u.role FROM users u LIMIT 3");
        echo "<p class='info'><i class='fas fa-info-circle'></i> Sample users for testing:</p>";
        echo "<ul>";
        while ($user = $stmt->fetch()) {
            echo "<li><code>{$user['email']}</code> ({$user['role']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='error'><i class='fas fa-times-circle'></i> ✗ No users in database to test with</p>";
        $testResults[] = ['test' => 'Users Exist', 'status' => 'FAIL'];
        $allTestsPassed = false;
    }
} catch (PDOException $e) {
    echo "<p class='error'><i class='fas fa-times-circle'></i> ✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $testResults[] = ['test' => 'User Query', 'status' => 'FAIL'];
    $allTestsPassed = false;
}
echo "</div>";

// Test 5: Check password hashing
echo "<div class='test-section'>";
echo "<h5><i class='fas fa-lock'></i> Test 5: Password Hashing Test</h5>";
$testPassword = "testPassword123";
$hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
if (password_verify($testPassword, $hashedPassword)) {
    echo "<p class='success'><i class='fas fa-check-circle'></i> ✓ Password hashing and verification working correctly</p>";
    $testResults[] = ['test' => 'Password Hashing', 'status' => 'PASS'];
} else {
    echo "<p class='error'><i class='fas fa-times-circle'></i> ✗ Password hashing/verification failed</p>";
    $testResults[] = ['test' => 'Password Hashing', 'status' => 'FAIL'];
    $allTestsPassed = false;
}
echo "</div>";

// Summary
echo "<div class='test-section " . ($allTestsPassed ? 'bg-success' : 'bg-danger') . " bg-opacity-10'>";
echo "<h4>Test Summary</h4>";
echo "<table class='table table-sm'>";
echo "<thead><tr><th>Test</th><th>Status</th></tr></thead><tbody>";
foreach ($testResults as $result) {
    $badge = $result['status'] === 'PASS' ? 'badge bg-success' : 'badge bg-danger';
    echo "<tr><td>{$result['test']}</td><td><span class='{$badge}'>{$result['status']}</span></td></tr>";
}
echo "</tbody></table>";

if ($allTestsPassed) {
    echo "<div class='alert alert-success'>";
    echo "<h5><i class='fas fa-check-circle'></i> All Tests Passed!</h5>";
    echo "<p>Your forgot password system is properly configured and ready to use.</p>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to the <a href='index.php'>login page</a></li>";
    echo "<li>Click 'Forgot Password?'</li>";
    echo "<li>Enter a registered email address</li>";
    echo "<li>Check your email for the verification code</li>";
    echo "<li>Enter the code and reset your password</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div class='alert alert-danger'>";
    echo "<h5><i class='fas fa-times-circle'></i> Some Tests Failed</h5>";
    echo "<p>Please fix the issues above before using the forgot password feature.</p>";
    echo "<p><strong>Common fixes:</strong></p>";
    echo "<ul>";
    echo "<li>Run <a href='create_password_resets_table.php'>create_password_resets_table.php</a> to create the database table</li>";
    echo "<li>Configure email settings in <code>config/email.php</code></li>";
    echo "<li>Make sure all required files are present</li>";
    echo "</ul>";
    echo "</div>";
}
echo "</div>";

echo "<div class='mt-4'>";
echo "<a href='index.php' class='btn btn-primary'>Go to Login Page</a> ";
echo "<a href='#' onclick='location.reload()' class='btn btn-secondary'>Re-run Tests</a> ";
echo "<a href='create_password_resets_table.php' class='btn btn-success'>Create Password Resets Table</a>";
echo "</div>";

echo "</div>
</body>
</html>";
?>
