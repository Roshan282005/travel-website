<?php
// Azure App Service configuration
// This file loads database credentials from environment variables or Key Vault

$host = getenv('MYSQL_HOST') ?: 'localhost';
$user = getenv('MYSQL_USER') ?: 'root';
$dbname = getenv('MYSQL_DATABASE') ?: 'travel_db';

// Get password from environment variable (set via Key Vault reference in App Service)
$pass = getenv('MYSQL_PASSWORD') ?: '';

// For local development
if (empty($host) || $host === 'localhost') {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "travel_db";
}

$conn = new mysqli($host, $user, $pass, $dbname);

// Enable SSL for Azure MySQL if not local
if ($host !== 'localhost' && $host !== '127.0.0.1') {
    $conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
}

if ($conn->connect_error) {
    http_response_code(503);
    error_log("Database Connection Failed: " . $conn->connect_error);
    die(json_encode([
        'error' => 'Database Connection Failed',
        'message' => 'Unable to connect to database. Please try again later.',
        'host' => $host
    ]));
}

// Set charset
$conn->set_charset("utf8");