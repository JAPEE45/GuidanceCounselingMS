<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login first");
}

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $student = $stmt->fetch();
    
    echo "<h2>Student Data Debug</h2>";
    echo "<pre>";
    echo "Student ID: " . ($student['student_id'] ?? 'NULL') . "\n";
    echo "User ID: " . ($student['user_id'] ?? 'NULL') . "\n";
    echo "First Name: " . ($student['first_name'] ?? 'NULL') . "\n";
    echo "Last Name: " . ($student['last_name'] ?? 'NULL') . "\n";
    echo "Birthday: " . ($student['birthday'] ?? 'NULL') . "\n";
    echo "Age in DB: " . ($student['age'] ?? 'NULL') . "\n";
    echo "Gender: " . ($student['gender'] ?? 'NULL') . "\n";
    echo "Course: " . ($student['course'] ?? 'NULL') . "\n";
    echo "Year Level: " . ($student['year_level'] ?? 'NULL') . "\n";
    echo "Contact Number: " . ($student['contact_number'] ?? 'NULL') . "\n";
    
    if (!empty($student['birthday'])) {
        $birthDate = new DateTime($student['birthday']);
        $today = new DateTime();
        $calculatedAge = $today->diff($birthDate)->y;
        echo "\nCalculated Age from Birthday: " . $calculatedAge . "\n";
    } else {
        echo "\nBirthday is EMPTY - Cannot calculate age!\n";
        echo "Please go to your Profile page and set your birthday.\n";
    }
    echo "</pre>";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
