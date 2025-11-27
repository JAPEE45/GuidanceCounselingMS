<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $contactNumber = $_POST['contactNumber'];
    $department = $_POST['department'] ?? '';
    $course = $_POST['course'];
    $yearLevel = $_POST['yearLevel'];

    try {
        // First check if student record exists
        $checkStmt = $pdo->prepare("SELECT student_id, first_name, last_name FROM students WHERE user_id = ?");
        $checkStmt->execute([$user_id]);
        $existingStudent = $checkStmt->fetch();
        
        if (!$existingStudent) {
            echo json_encode([
                'success' => false, 
                'message' => 'Student record not found for user_id: ' . $user_id . '. Please contact admin.',
                'debug' => 'No student record linked to this user account'
            ]);
            exit;
        }
        
        // Now perform the update
        $stmt = $pdo->prepare("
            UPDATE students 
            SET first_name = ?, middle_name = ?, last_name = ?, 
                birthday = ?, gender = ?, contact_number = ?, 
                department = ?, course = ?, year_level = ?
            WHERE user_id = ?
        ");
        $result = $stmt->execute([
            $firstName, $middleName, $lastName, 
            $birthday, $gender, $contactNumber, 
            $department, $course, $yearLevel, 
            $user_id
        ]);

        // Check if any rows were affected
        $rowCount = $stmt->rowCount();
        
        if ($rowCount > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Profile updated successfully!', 
                'rows_affected' => $rowCount
            ]);
        } else {
            echo json_encode([
                'success' => true, 
                'message' => 'No changes detected (data is the same)', 
                'rows_affected' => 0,
                'info' => 'Student exists but no fields were changed'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
