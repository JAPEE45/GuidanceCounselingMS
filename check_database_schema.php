<?php
require_once 'config/database.php';

echo "<h2>Database Schema Check</h2>";
echo "<p>Checking if new columns have been added...</p>";

try {
    // Check appointments table structure
    $stmt = $pdo->query("DESCRIBE appointments");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $requiredColumns = [
        'is_minor',
        'parent_guardian_name',
        'parent_guardian_contact',
        'counseling_concerns',
        'current_medications',
        'medical_conditions',
        'student_address',
        'consent_acknowledged'
    ];
    
    echo "<h3>Appointments Table:</h3>";
    echo "<ul>";
    
    $allPresent = true;
    foreach ($requiredColumns as $col) {
        $exists = in_array($col, $columns);
        $status = $exists ? '✓' : '✗';
        $color = $exists ? 'green' : 'red';
        echo "<li style='color: {$color};'>{$status} {$col}</li>";
        if (!$exists) $allPresent = false;
    }
    echo "</ul>";
    
    // Check students table for address
    $stmt = $pdo->query("DESCRIBE students");
    $studentColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Students Table:</h3>";
    $addressExists = in_array('address', $studentColumns);
    $status = $addressExists ? '✓' : '✗';
    $color = $addressExists ? 'green' : 'red';
    echo "<p style='color: {$color};'>{$status} address column</p>";
    
    if ($allPresent && $addressExists) {
        echo "<hr>";
        echo "<h2 style='color: green;'>✓ All required columns are present!</h2>";
        echo "<p>You can proceed with using the new booking form.</p>";
    } else {
        echo "<hr>";
        echo "<h2 style='color: red;'>✗ Some columns are missing!</h2>";
        echo "<p><strong>Action Required:</strong> Please run the SQL update script in phpMyAdmin.</p>";
        echo "<p>Open: <a href='DATABASE_UPDATE_INSTRUCTIONS.md'>DATABASE_UPDATE_INSTRUCTIONS.md</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
