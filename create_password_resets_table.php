<?php
/**
 * Run this script once to create the password_resets table
 * Access: http://localhost/GuidanceCounselingMS/create_password_resets_table.php
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Create Password Resets Table</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { padding: 40px; background-color: #f8f9fa; }
        .container { max-width: 800px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class='container'>";

try {
    // Check if table already exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'password_resets'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "<div class='alert alert-info'>
                <h4><i class='bi bi-info-circle'></i> Table Already Exists</h4>
                <p>The <code>password_resets</code> table already exists in your database.</p>
              </div>";
        
        // Show table structure
        echo "<h5>Current Table Structure:</h5>";
        $stmt = $pdo->query("DESCRIBE password_resets");
        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr></thead><tbody>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        
    } else {
        // Create password_resets table
        $sql = "CREATE TABLE `password_resets` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `email` varchar(255) NOT NULL,
            `reset_code` varchar(10) NOT NULL,
            `expires_at` datetime NOT NULL,
            `used` tinyint(1) DEFAULT 0,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            KEY `email` (`email`),
            KEY `reset_code` (`reset_code`),
            CONSTRAINT `password_resets_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        $pdo->exec($sql);
        
        echo "<div class='alert alert-success'>
                <h4>✓ Success!</h4>
                <p>The <code>password_resets</code> table has been created successfully!</p>
              </div>";
        
        echo "<div class='alert alert-info'>
                <h5>What's Next?</h5>
                <ul>
                    <li>The forgot password feature is now fully functional</li>
                    <li>Users can reset their passwords via email verification</li>
                    <li>Verification codes are stored securely in the database</li>
                    <li>Codes expire after 15 minutes</li>
                </ul>
              </div>";
        
        // Show table structure
        echo "<h5>Created Table Structure:</h5>";
        $stmt = $pdo->query("DESCRIBE password_resets");
        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr></thead><tbody>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td><strong>{$row['Field']}</strong></td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
    
    echo "<div class='mt-4'>
            <a href='index.php' class='btn btn-primary'>Go to Login Page</a>
            <a href='#' onclick='location.reload()' class='btn btn-secondary'>Refresh</a>
          </div>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>
            <h4>Error creating table:</h4>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
            <hr>
            <p><strong>Troubleshooting:</strong></p>
            <ul>
                <li>Make sure your database connection is working</li>
                <li>Verify that the <code>users</code> table exists (required for foreign key)</li>
                <li>Check that your database user has CREATE TABLE permissions</li>
            </ul>
          </div>";
}

echo "</div>
</body>
</html>";
?>
