<?php
/**
 * Add missing created_at columns to tables for consistency
 */

include 'db.php';

echo "===== Adding Missing Columns =====\n\n";

// Check if created_at exists in bookings, if not add it
$checkResult = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='bookings' AND COLUMN_NAME='created_at' AND TABLE_SCHEMA=DATABASE()");

if ($checkResult->num_rows === 0) {
    // Column doesn't exist, add it
    if ($conn->query("ALTER TABLE bookings ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER booking_date") === TRUE) {
        echo "✅ Added 'created_at' column to 'bookings' table\n";
    } else {
        echo "❌ Error adding column: " . $conn->error . "\n";
    }
} else {
    echo "✅ Column 'created_at' already exists in 'bookings'\n";
}

echo "\n===== Verification =====\n";

// Verify bookings table structure
$result = $conn->query("DESCRIBE bookings");
if ($result) {
    echo "✅ Bookings table columns:\n";
    while($row = $result->fetch_assoc()) {
        $nullable = $row['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
        echo "  - {$row['Field']}: {$row['Type']} ($nullable)\n";
    }
} else {
    echo "❌ Error: " . $conn->error . "\n";
}

$conn->close();
echo "\n✅ Table structure updated!\n";
?>
