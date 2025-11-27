<?php
require_once 'config/database.php';

echo "=== Counselors ===\n";
$stmt = $pdo->query("SELECT * FROM counselors");
$counselors = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($counselors as $c) {
    echo "ID: {$c['counselor_id']}, UserID: {$c['user_id']}, Name: {$c['first_name']} {$c['last_name']}\n";
}

echo "\n=== Appointments ===\n";
$stmt = $pdo->query("SELECT * FROM appointments");
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($appointments as $a) {
    echo "ID: {$a['appointment_id']}, CounselorID: {$a['counselor_id']}, Date: {$a['appointment_date']}, Status: {$a['status']}\n";
}
?>
