<?php
require_once 'config/database.php';

try {
    $stmt = $pdo->prepare("UPDATE appointments SET counselor_id = 2 WHERE appointment_id = 2");
    $stmt->execute();
    echo "Updated Appointment 2 to be assigned to Counselor 2 (Jasper).\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
