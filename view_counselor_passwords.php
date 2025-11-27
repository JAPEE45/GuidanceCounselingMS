<?php
require_once 'config/database.php';

// Get the most recently added counselor
$stmt = $pdo->query("
    SELECT c.counselor_id, c.first_name, c.last_name, u.email, u.user_id, c.created_at
    FROM counselors c
    JOIN users u ON c.user_id = u.user_id
    ORDER BY c.counselor_id DESC
    LIMIT 5
");
$counselors = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Recent Counselor Accounts</h2>";
echo "<p>These are the 5 most recently created counselor accounts:</p>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr style='background-color: #f0f0f0;'>";
echo "<th>ID</th><th>Name</th><th>Email</th><th>Action</th>";
echo "</tr>";

foreach ($counselors as $c) {
    echo "<tr>";
    echo "<td>" . $c['counselor_id'] . "</td>";
    echo "<td>" . htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) . "</td>";
    echo "<td>" . htmlspecialchars($c['email']) . "</td>";
    echo "<td><a href='reset_counselor_password.php?user_id=" . $c['user_id'] . "'>Reset Password</a></td>";
    echo "</tr>";
}

echo "</table>";
echo "<hr>";
echo "<p><strong>Note:</strong> Click 'Reset Password' to generate a new password for a counselor account.</p>";
?>
