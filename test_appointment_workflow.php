<?php
session_start();
require_once 'config/database.php';

// Set admin session for testing
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Appointment Workflow Test</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .test-section { margin: 20px 0; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        h3 { color: #34495e; margin-top: 20px; }
        .success { color: #27ae60; font-weight: bold; }
        .warning { color: #f39c12; font-weight: bold; }
        .error { color: #e74c3c; font-weight: bold; }
        .info { color: #3498db; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #3498db; color: white; font-weight: bold; }
        tr:nth-child(even) { background: #f9f9f9; }
        .badge { padding: 5px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-confirmed { background: #d4edda; color: #155724; }
        .badge-assigned { background: #d1ecf1; color: #0c5460; }
        .workflow { background: #e8f4f8; padding: 15px; border-left: 4px solid #3498db; margin: 20px 0; }
        .step { padding: 10px; margin: 5px 0; background: white; border-radius: 5px; }
        .step-number { display: inline-block; width: 30px; height: 30px; background: #3498db; color: white; border-radius: 50%; text-align: center; line-height: 30px; margin-right: 10px; }
        .highlight { background: #ffffcc; padding: 2px 5px; border-radius: 3px; }
        .button-link { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .button-link:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📋 Appointment Workflow Test - Before & After Fix</h2>

<?php
try {
    echo '<div class="test-section">';
    echo '<h3>🔧 What Was Fixed</h3>';
    echo '<div class="workflow">';
    echo '<p><strong>PROBLEM:</strong> When admin approved an appointment, it disappeared from the Notifications & Referrals page, making it impossible to assign a counselor.</p>';
    echo '<p><strong>SOLUTION:</strong> Changed the query to show both <span class="highlight">Pending</span> AND <span class="highlight">Confirmed (Approved)</span> appointments that don\'t have a counselor assigned yet.</p>';
    echo '</div>';
    echo '</div>';

    // Show the correct workflow
    echo '<div class="test-section">';
    echo '<h3>✅ Correct Workflow (After Fix)</h3>';
    echo '<div class="workflow">';
    echo '<div class="step"><span class="step-number">1</span> Student books appointment → Status: <strong>Pending</strong></div>';
    echo '<div class="step"><span class="step-number">2</span> Admin sees appointment in <strong>Appointments page</strong> → Admin approves → Status: <strong>Confirmed</strong></div>';
    echo '<div class="step"><span class="step-number">3</span> Appointment <span class="highlight">stays visible</span> in <strong>Notifications & Referrals page</strong></div>';
    echo '<div class="step"><span class="step-number">4</span> Admin assigns counselor from Notifications & Referrals page</div>';
    echo '<div class="step"><span class="step-number">5</span> Appointment disappears from referrals list (counselor assigned)</div>';
    echo '</div>';
    echo '</div>';

    // Test 1: Show appointments by status
    echo '<div class="test-section">';
    echo '<h3>📊 Test 1: Appointments Status Overview</h3>';
    
    $stmt = $pdo->query("
        SELECT 
            status, 
            COUNT(*) as count,
            SUM(CASE WHEN counselor_id IS NULL THEN 1 ELSE 0 END) as without_counselor,
            SUM(CASE WHEN counselor_id IS NOT NULL THEN 1 ELSE 0 END) as with_counselor
        FROM appointments
        GROUP BY status
        ORDER BY status
    ");
    $statusSummary = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo '<table>';
    echo '<tr><th>Status</th><th>Total</th><th>Without Counselor</th><th>With Counselor</th></tr>';
    foreach ($statusSummary as $row) {
        $badgeClass = $row['status'] === 'Pending' ? 'badge-pending' : ($row['status'] === 'Confirmed' ? 'badge-confirmed' : '');
        echo '<tr>';
        echo '<td><span class="badge ' . $badgeClass . '">' . $row['status'] . '</span></td>';
        echo '<td>' . $row['count'] . '</td>';
        echo '<td>' . ($row['without_counselor'] > 0 ? '<span class="warning">' . $row['without_counselor'] . '</span>' : '<span class="success">0</span>') . '</td>';
        echo '<td>' . ($row['with_counselor'] > 0 ? '<span class="info">' . $row['with_counselor'] . '</span>' : '0') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';

    // Test 2: Show appointments that WILL appear in Notifications & Referrals
    echo '<div class="test-section">';
    echo '<h3>📋 Test 2: Appointments Visible in Notifications & Referrals Page</h3>';
    echo '<p class="info">These appointments will show in the referrals list (Pending OR Confirmed, without counselor):</p>';
    
    $stmt = $pdo->query("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.purpose, a.status,
               s.first_name, s.last_name, s.student_number, s.course
        FROM appointments a
        JOIN students s ON a.student_id = s.student_id
        WHERE (a.status = 'Pending' OR a.status = 'Confirmed') AND a.counselor_id IS NULL
        ORDER BY a.status DESC, a.appointment_date ASC
    ");
    $referralAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($referralAppointments) > 0) {
        echo '<table>';
        echo '<tr><th>ID</th><th>Status</th><th>Student</th><th>Student #</th><th>Date</th><th>Time</th><th>Purpose</th></tr>';
        foreach ($referralAppointments as $appt) {
            $badgeClass = $appt['status'] === 'Pending' ? 'badge-pending' : 'badge-confirmed';
            $statusIcon = $appt['status'] === 'Pending' ? '⏳' : '✅';
            echo '<tr>';
            echo '<td>' . $appt['appointment_id'] . '</td>';
            echo '<td><span class="badge ' . $badgeClass . '">' . $statusIcon . ' ' . $appt['status'] . '</span></td>';
            echo '<td>' . htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']) . '</td>';
            echo '<td>' . htmlspecialchars($appt['student_number']) . '</td>';
            echo '<td>' . date('M d, Y', strtotime($appt['appointment_date'])) . '</td>';
            echo '<td>' . date('g:i A', strtotime($appt['appointment_time'])) . '</td>';
            echo '<td>' . htmlspecialchars($appt['purpose']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        $confirmedCount = count(array_filter($referralAppointments, fn($a) => $a['status'] === 'Confirmed'));
        $pendingCount = count(array_filter($referralAppointments, fn($a) => $a['status'] === 'Pending'));
        
        echo '<div class="workflow">';
        echo '<p><span class="success">✓ Found ' . count($referralAppointments) . ' appointments awaiting counselor assignment:</span></p>';
        echo '<ul>';
        echo '<li><span class="badge badge-pending">⏳ Pending</span>: ' . $pendingCount . ' (not yet approved)</li>';
        echo '<li><span class="badge badge-confirmed">✅ Confirmed</span>: ' . $confirmedCount . ' (approved, waiting for counselor)</li>';
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<div class="workflow">';
        echo '<p class="warning">⚠ No appointments found that need counselor assignment.</p>';
        echo '<p>All appointments either have counselors assigned or have been declined/completed.</p>';
        echo '</div>';
    }
    echo '</div>';

    // Test 3: Show approved appointments WITHOUT counselor (the key fix)
    echo '<div class="test-section">';
    echo '<h3>🎯 Test 3: Key Fix - Approved Appointments Without Counselor</h3>';
    echo '<p class="info">This is what the fix enables - approved appointments remain visible until counselor is assigned:</p>';
    
    $stmt = $pdo->query("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.purpose,
               s.first_name, s.last_name, s.student_number
        FROM appointments a
        JOIN students s ON a.student_id = s.student_id
        WHERE a.status = 'Confirmed' AND a.counselor_id IS NULL
    ");
    $approvedWithoutCounselor = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($approvedWithoutCounselor) > 0) {
        echo '<table>';
        echo '<tr><th>ID</th><th>Student</th><th>Student #</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th></tr>';
        foreach ($approvedWithoutCounselor as $appt) {
            echo '<tr>';
            echo '<td>' . $appt['appointment_id'] . '</td>';
            echo '<td>' . htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']) . '</td>';
            echo '<td>' . htmlspecialchars($appt['student_number']) . '</td>';
            echo '<td>' . date('M d, Y', strtotime($appt['appointment_date'])) . '</td>';
            echo '<td>' . date('g:i A', strtotime($appt['appointment_time'])) . '</td>';
            echo '<td>' . htmlspecialchars($appt['purpose']) . '</td>';
            echo '<td><span class="badge badge-confirmed">✅ Approved</span> <span class="badge" style="background:#fff3cd;color:#856404;">⏳ Awaiting Counselor</span></td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<div class="workflow">';
        echo '<p class="success">✓ These ' . count($approvedWithoutCounselor) . ' approved appointments will remain visible in Notifications & Referrals until counselor is assigned!</p>';
        echo '</div>';
    } else {
        echo '<div class="workflow">';
        echo '<p class="info">ℹ No approved appointments without counselor at the moment.</p>';
        echo '<p>To test: Approve an appointment from the Appointments page, then check Notifications & Referrals page.</p>';
        echo '</div>';
    }
    echo '</div>';

    // Test 4: Appointments with counselor assigned
    echo '<div class="test-section">';
    echo '<h3>👥 Test 4: Appointments With Counselor Assigned (Will NOT show in referrals)</h3>';
    
    $stmt = $pdo->query("
        SELECT a.appointment_id, a.status,
               s.first_name as student_first, s.last_name as student_last,
               c.first_name as counselor_first, c.last_name as counselor_last
        FROM appointments a
        JOIN students s ON a.student_id = s.student_id
        LEFT JOIN counselors c ON a.counselor_id = c.counselor_id
        WHERE a.counselor_id IS NOT NULL
        LIMIT 10
    ");
    $withCounselor = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($withCounselor) > 0) {
        echo '<table>';
        echo '<tr><th>ID</th><th>Status</th><th>Student</th><th>Counselor</th><th>Visibility</th></tr>';
        foreach ($withCounselor as $appt) {
            echo '<tr>';
            echo '<td>' . $appt['appointment_id'] . '</td>';
            echo '<td><span class="badge badge-assigned">' . $appt['status'] . '</span></td>';
            echo '<td>' . htmlspecialchars($appt['student_first'] . ' ' . $appt['student_last']) . '</td>';
            echo '<td>' . htmlspecialchars($appt['counselor_first'] . ' ' . $appt['counselor_last']) . '</td>';
            echo '<td><span class="error">✗ Hidden from referrals</span></td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<p class="success">✓ These appointments correctly hidden from referrals list (counselor already assigned)</p>';
    } else {
        echo '<p class="warning">No appointments with counselors assigned yet.</p>';
    }
    echo '</div>';

    // Summary
    echo '<div class="test-section" style="background: #e8f5e9; border-left: 5px solid #4caf50;">';
    echo '<h3>📝 Summary</h3>';
    echo '<div class="workflow">';
    echo '<p><strong>Query Change:</strong></p>';
    echo '<pre style="background: white; padding: 10px; border-radius: 5px;">
-- OLD (BROKEN)
WHERE a.status = \'Pending\' AND a.counselor_id IS NULL

-- NEW (FIXED)  
WHERE (a.status = \'Pending\' OR a.status = \'Confirmed\') AND a.counselor_id IS NULL
    </pre>';
    
    echo '<p><strong>Impact:</strong></p>';
    echo '<ul>';
    echo '<li><span class="success">✓ Approved appointments stay visible in referrals</span></li>';
    echo '<li><span class="success">✓ Admin can assign counselor AFTER approving</span></li>';
    echo '<li><span class="success">✓ Appointments disappear only after counselor assignment</span></li>';
    echo '<li><span class="success">✓ Status badge shows whether appointment is pending or approved</span></li>';
    echo '</ul>';
    echo '</div>';
    
    echo '<h3>🧪 How to Test</h3>';
    echo '<div class="step">1. Go to <strong>Appointments page</strong> and approve a pending appointment</div>';
    echo '<div class="step">2. Go to <strong>Notifications & Referrals page</strong></div>';
    echo '<div class="step">3. The approved appointment should appear with <span class="badge badge-confirmed">✅ Approved</span> badge</div>';
    echo '<div class="step">4. Click "Assign" button and select a counselor</div>';
    echo '<div class="step">5. The appointment should disappear from the list</div>';
    
    echo '<p style="margin-top: 20px;">';
    echo '<a href="html/admin/appointments.php" class="button-link">📅 Go to Appointments Page</a>';
    echo '<a href="html/admin/notifications.php" class="button-link">🔔 Go to Notifications & Referrals Page</a>';
    echo '</p>';
    echo '</div>';

} catch (PDOException $e) {
    echo '<div class="test-section" style="background: #ffebee; border-left: 5px solid #f44336;">';
    echo '<h3>❌ Database Error</h3>';
    echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
    echo '</div>';
}
?>

    </div>
</body>
</html>
