<?php
/**
 * Create Missing Tables - TravelGo Database Setup
 * This script creates the firebase_users table and any other missing tables
 */

include 'db.php';

// Tables to create
$tables = [
    'firebase_users' => "
        CREATE TABLE IF NOT EXISTS firebase_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            firebase_uid VARCHAR(100) NOT NULL UNIQUE,
            fullname VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            photo TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            reset_token VARCHAR(255) DEFAULT NULL,
            reset_expires DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_firebase_uid (firebase_uid),
            INDEX idx_email (email)
        )
    ",
    'users' => "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            firebase_uid VARCHAR(100) UNIQUE,
            username VARCHAR(100),
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255),
            fullname VARCHAR(100),
            photo TEXT,
            phone VARCHAR(20),
            address VARCHAR(255),
            city VARCHAR(100),
            country VARCHAR(100),
            bio TEXT,
            language VARCHAR(10) DEFAULT 'en',
            currency VARCHAR(10) DEFAULT 'USD',
            theme VARCHAR(10) DEFAULT 'light',
            two_factor_enabled BOOLEAN DEFAULT FALSE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_login DATETIME,
            INDEX idx_firebase_uid (firebase_uid),
            INDEX idx_email (email)
        )
    ",
    'bookings' => "
        CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            destination_id INT,
            hotel_name VARCHAR(255),
            check_in DATE NOT NULL,
            check_out DATE NOT NULL,
            guests INT DEFAULT 1,
            rooms INT DEFAULT 1,
            total_price DECIMAL(10, 2),
            status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
            payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
            booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            notes TEXT,
            FOREIGN KEY (user_id) REFERENCES firebase_users(id) ON DELETE CASCADE
        )
    ",
    'reviews' => "
        CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            destination_id INT,
            title VARCHAR(255),
            rating INT CHECK (rating >= 1 AND rating <= 5),
            review_text TEXT,
            helpful_count INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES firebase_users(id) ON DELETE CASCADE
        )
    ",
    'wishlist' => "
        CREATE TABLE IF NOT EXISTS wishlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            destination_id INT,
            added_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_destination (user_id, destination_id),
            FOREIGN KEY (user_id) REFERENCES firebase_users(id) ON DELETE CASCADE
        )
    ",
    'activity_log' => "
        CREATE TABLE IF NOT EXISTS activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(255),
            details JSON,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES firebase_users(id) ON DELETE SET NULL,
            INDEX idx_user_date (user_id, created_at)
        )
    ",
    'notifications' => "
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255),
            message TEXT,
            type ENUM('booking', 'review', 'wishlist', 'system') DEFAULT 'system',
            is_read BOOLEAN DEFAULT FALSE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES firebase_users(id) ON DELETE CASCADE
        )
    ",
    'user_preferences' => "
        CREATE TABLE IF NOT EXISTS user_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            language VARCHAR(10) DEFAULT 'en',
            currency VARCHAR(10) DEFAULT 'USD',
            theme VARCHAR(10) DEFAULT 'light',
            notifications_enabled BOOLEAN DEFAULT TRUE,
            email_alerts BOOLEAN DEFAULT TRUE,
            sms_alerts BOOLEAN DEFAULT FALSE,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES firebase_users(id) ON DELETE CASCADE
        )
    ",
    'payment_transactions' => "
        CREATE TABLE IF NOT EXISTS payment_transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            booking_id INT,
            amount DECIMAL(10, 2) NOT NULL,
            currency VARCHAR(10) DEFAULT 'USD',
            payment_method ENUM('credit_card', 'paypal', 'stripe', 'wallet') DEFAULT 'credit_card',
            transaction_id VARCHAR(255) UNIQUE,
            status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES firebase_users(id) ON DELETE CASCADE,
            INDEX idx_status (status)
        )
    "
];

// Create tables
$createdCount = 0;
$failedCount = 0;

echo "===== Creating Missing Tables =====\n\n";

foreach ($tables as $tableName => $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "✅ Table '$tableName' created/verified successfully\n";
        $createdCount++;
    } else {
        echo "❌ Error with table '$tableName': " . $conn->error . "\n";
        $failedCount++;
    }
}

echo "\n===== Summary =====\n";
echo "✅ Successful: $createdCount\n";
echo "❌ Failed: $failedCount\n";

// Verify firebase_users table
echo "\n===== Verifying firebase_users Table =====\n";
$result = $conn->query("DESCRIBE firebase_users");
if ($result) {
    echo "✅ firebase_users table structure:\n";
    while($row = $result->fetch_assoc()) {
        echo "  - {$row['Field']}: {$row['Type']}\n";
    }
} else {
    echo "❌ Error verifying table: " . $conn->error . "\n";
}

$conn->close();
echo "\n✅ Database setup complete! You can now use profile.php\n";
?>
