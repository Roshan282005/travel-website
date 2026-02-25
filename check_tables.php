<?php
include 'db.php';

// Check if tables exist
$tables = ['bookings', 'reviews', 'wishlist', 'notifications', 'user_preferences', 'activity_log', 'payment_transactions'];

echo "<h2>Database Table Status</h2>";
echo "<ul>";

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<li>✅ <strong>$table</strong> - EXISTS</li>";
    } else {
        echo "<li>❌ <strong>$table</strong> - MISSING</li>";
    }
}

echo "</ul>";

// Try to get table structure for bookings if it exists
$result = $conn->query("SHOW COLUMNS FROM bookings");
if ($result) {
    echo "<h3>Bookings Table Structure:</h3>";
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    echo "</pre>";
}

$conn->close();
?>
