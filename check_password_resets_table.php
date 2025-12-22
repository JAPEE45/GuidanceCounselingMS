<?php
require_once 'config/database.php';

header('Content-Type: application/json');

try {
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'password_resets'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo json_encode([
            'success' => false,
            'error' => 'password_resets table does not exist',
            'solution' => 'Please run create_password_resets_table.php first'
        ]);
        exit;
    }
    
    // Get table structure
    $stmt = $pdo->query("DESCRIBE password_resets");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Count existing records
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM password_resets");
    $count = $stmt->fetch()['count'];
    
    echo json_encode([
        'success' => true,
        'table_exists' => true,
        'structure' => $structure,
        'record_count' => $count
    ], JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
