<?php
// Database setup script to create all tables

include 'db.php';

// Read the SQL file
$sql = file_get_contents('database_enhanced.sql');

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

$successful = 0;
$failed = 0;
$errors = [];

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) {
        continue; // Skip empty lines and comments
    }
    
    if ($conn->multi_query($statement)) {
        $successful++;
        // Clear all results from multi_query
        while ($conn->more_results() && $conn->next_result()) {
            if ($result = $conn->use_result()) {
                $result->free();
            }
        }
    } else {
        $failed++;
        $errors[] = "Error: " . $conn->error . " | Statement: " . substr($statement, 0, 50);
    }
}

echo "Database Setup Complete!\n";
echo "Successful statements: " . $successful . "\n";
echo "Failed statements: " . $failed . "\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo $error . "\n";
    }
}

$conn->close();
?>
