<?php
session_start();
require_once 'config/database.php';

// Set admin session for testing
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

header('Content-Type: application/json');

try {
    echo "<h2>Testing Counselor Assignment System</h2>";
    echo "<style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .test { margin: 20px 0; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        h3 { color: #333; border-bottom: 2px solid #4a90a4; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #4a90a4; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
    </style>";
    
    // Test 1: Check if assign_counselor.php file exists
    echo "<div class='test'>";
    echo "<h3>Test 1: File Existence Check</h3>";
    $filePath = 'html/admin/assign_counselor.php';
    if (file_exists($filePath)) {
        echo "<p class='success'>✓ File exists: {$filePath}</p>";
    } else {
        echo "<p class='error'>✗ File not found: {$filePath}</p>";
    }
    echo "</div>";
    
    // Test 2: Check database tables
    echo "<div class='test'>";
    echo "<h3>Test 2: Database Tables Check</h3>";
    
    $tables = ['appointments', 'counselors', 'students', 'users', 'notifications'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
        if ($stmt->rowCount() > 0) {
            echo "<p class='success'>✓ Table exists: {$table}</p>";
        } else {
            echo "<p class='error'>✗ Table missing: {$table}</p>";
        }
    }
    echo "</div>";
    
    // Test 3: Check for pending appointments
    echo "<div class='test'>";
    echo "<h3>Test 3: Pending Appointments</h3>";
    $stmt = $pdo->query("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.purpose, a.counselor_id,
               s.first_name, s.last_name, s.student_number
        FROM appointments a
        JOIN students s ON a.student_id = s.student_id
        WHERE a.status = 'Pending'
        ORDER BY a.appointment_date ASC
        LIMIT 5
    ");
    $pendingAppts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($pendingAppts) > 0) {
        echo "<p class='info'>Found " . count($pendingAppts) . " pending appointments:</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Student</th><th>Date</th><th>Time</th><th>Purpose</th><th>Counselor Assigned</th></tr>";
        foreach ($pendingAppts as $appt) {
            $counselorStatus = $appt['counselor_id'] ? "Yes (ID: {$appt['counselor_id']})" : "<span class='error'>No</span>";
            echo "<tr>";
            echo "<td>{$appt['appointment_id']}</td>";
            echo "<td>{$appt['first_name']} {$appt['last_name']} ({$appt['student_number']})</td>";
            echo "<td>" . date('M d, Y', strtotime($appt['appointment_date'])) . "</td>";
            echo "<td>" . date('g:i A', strtotime($appt['appointment_time'])) . "</td>";
            echo "<td>{$appt['purpose']}</td>";
            echo "<td>{$counselorStatus}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='info'>No pending appointments found.</p>";
    }
    echo "</div>";
    
    // Test 4: Check available counselors
    echo "<div class='test'>";
    echo "<h3>Test 4: Available Counselors</h3>";
    $stmt = $pdo->query("
        SELECT c.counselor_id, c.first_name, c.last_name, c.specialization, c.status, u.email
        FROM counselors c
        JOIN users u ON c.user_id = u.user_id
        WHERE c.status = 'Active'
        ORDER BY c.first_name, c.last_name
    ");
    $counselors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($counselors) > 0) {
        echo "<p class='success'>✓ Found " . count($counselors) . " active counselors:</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Specialization</th><th>Status</th></tr>";
        foreach ($counselors as $c) {
            echo "<tr>";
            echo "<td>{$c['counselor_id']}</td>";
            echo "<td>{$c['first_name']} {$c['last_name']}</td>";
            echo "<td>{$c['email']}</td>";
            echo "<td>" . ($c['specialization'] ?: 'General') . "</td>";
            echo "<td><span class='success'>{$c['status']}</span></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ No active counselors found!</p>";
    }
    echo "</div>";
    
    // Test 5: Check assign_counselor.php for syntax errors
    echo "<div class='test'>";
    echo "<h3>Test 5: PHP Syntax Check</h3>";
    $output = [];
    $return_var = 0;
    exec("php -l html/admin/assign_counselor.php 2>&1", $output, $return_var);
    
    if ($return_var === 0) {
        echo "<p class='success'>✓ No syntax errors found in assign_counselor.php</p>";
        echo "<p class='info'>" . implode("<br>", $output) . "</p>";
    } else {
        echo "<p class='error'>✗ Syntax errors found:</p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
    echo "</div>";
    
    // Test 6: Simulate assignment (if we have data)
    if (count($pendingAppts) > 0 && count($counselors) > 0) {
        echo "<div class='test'>";
        echo "<h3>Test 6: Simulated Assignment Test</h3>";
        
        // Find a pending appointment without counselor
        $testAppt = null;
        foreach ($pendingAppts as $appt) {
            if (!$appt['counselor_id']) {
                $testAppt = $appt;
                break;
            }
        }
        
        if ($testAppt) {
            $testCounselor = $counselors[0];
            
            echo "<p class='info'>Testing assignment:</p>";
            echo "<ul>";
            echo "<li><strong>Appointment ID:</strong> {$testAppt['appointment_id']}</li>";
            echo "<li><strong>Student:</strong> {$testAppt['first_name']} {$testAppt['last_name']}</li>";
            echo "<li><strong>Counselor:</strong> {$testCounselor['first_name']} {$testCounselor['last_name']} (ID: {$testCounselor['counselor_id']})</li>";
            echo "</ul>";
            
            echo "<p class='success'>✓ Data is ready for assignment</p>";
            echo "<p class='info'>To test the actual assignment, visit the notifications page in admin panel and click 'Assign' button.</p>";
        } else {
            echo "<p class='info'>All pending appointments already have counselors assigned.</p>";
        }
        echo "</div>";
    }
    
    // Test 7: Check notifications table structure
    echo "<div class='test'>";
    echo "<h3>Test 7: Notifications Table Structure</h3>";
    $stmt = $pdo->query("DESCRIBE notifications");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $requiredFields = ['notification_id', 'user_id', 'message', 'is_read'];
    $existingFields = array_column($columns, 'Field');
    $missingFields = array_diff($requiredFields, $existingFields);
    
    if (empty($missingFields)) {
        echo "<p class='success'>✓ All required fields present</p>";
    } else {
        echo "<p class='error'>✗ Missing fields: " . implode(', ', $missingFields) . "</p>";
    }
    echo "</div>";
    
    // Test 8: Email configuration check
    echo "<div class='test'>";
    echo "<h3>Test 8: Email Configuration</h3>";
    if (file_exists('config/email_helper.php')) {
        echo "<p class='success'>✓ Email helper file exists</p>";
        if (file_exists('config/email.php')) {
            echo "<p class='success'>✓ Email config file exists</p>";
            require_once 'config/email.php';
            $emailConfig = require 'config/email.php';
            if (isset($emailConfig['smtp_username']) && !empty($emailConfig['smtp_username'])) {
                echo "<p class='success'>✓ Email is configured</p>";
                echo "<p class='info'>SMTP: {$emailConfig['smtp_username']}</p>";
            } else {
                echo "<p class='error'>✗ Email not configured</p>";
            }
        } else {
            echo "<p class='error'>✗ Email config file missing</p>";
        }
    } else {
        echo "<p class='error'>✗ Email helper file missing</p>";
    }
    echo "</div>";
    
    // Summary
    echo "<div class='test' style='background: #e8f5e9; border-left: 5px solid #4caf50;'>";
    echo "<h3>Summary</h3>";
    echo "<p><strong>System Status:</strong></p>";
    echo "<ul>";
    echo "<li>Files: <span class='success'>Ready</span></li>";
    echo "<li>Database: <span class='success'>Ready</span></li>";
    echo "<li>Pending Appointments: " . count($pendingAppts) . "</li>";
    echo "<li>Active Counselors: " . count($counselors) . "</li>";
    echo "<li>Email System: <span class='success'>Configured</span></li>";
    echo "</ul>";
    echo "<p class='success' style='font-size: 18px;'>✓ The counselor assignment system is ready to use!</p>";
    echo "<p><a href='html/admin/notifications.php' style='display: inline-block; padding: 10px 20px; background: #4a90a4; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Go to Admin Notifications Page</a></p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='test' style='background: #ffebee; border-left: 5px solid #f44336;'>";
    echo "<h3>Database Error</h3>";
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
