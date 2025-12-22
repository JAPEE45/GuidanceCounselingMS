<?php
require_once 'config/database.php';

try {
    // Add email_verified column
    $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `email_verified` TINYINT(1) DEFAULT 0 AFTER `role`");
    
    // Add verification_token column
    $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `verification_token` VARCHAR(64) NULL AFTER `email_verified`");
    
    // Add index
    $pdo->exec("ALTER TABLE `users` ADD INDEX IF NOT EXISTS `idx_verification_token` (`verification_token`)");
    
    echo json_encode([
        'success' => true,
        'message' => 'Email verification columns added successfully!'
    ]);
} catch (PDOException $e) {
    // Check if columns already exist
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo json_encode([
            'success' => true,
            'message' => 'Columns already exist!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
?>
