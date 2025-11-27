<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login first");
}

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>Your Student Data</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    
    if ($student) {
        foreach ($student as $key => $value) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($key) . "</strong></td>";
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='2'>No student record found!</td></tr>";
    }
    
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>Birthday Analysis:</h3>";
    if ($student && !empty($student['birthday'])) {
        echo "<p>Birthday value: <strong>" . htmlspecialchars($student['birthday']) . "</strong></p>";
        
        $birthDate = new DateTime($student['birthday']);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        
        echo "<p>Calculated Age: <strong>" . $age . " years old</strong></p>";
    } else {
        echo "<p style='color: red;'><strong>Birthday is EMPTY or NULL!</strong></p>";
        echo "<p>You need to set your birthday in your profile.</p>";
    }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
