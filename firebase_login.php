<?php
ob_start();
ob_clean();
header('Content-Type: application/json; charset=UTF-8');
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'PHP Error: ' . $errstr]);
    exit;
});
set_exception_handler(function($e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    exit;
});
session_start();

$host = getenv('MYSQL_HOST') ?: 'localhost';
$user = getenv('MYSQL_USER') ?: 'root';
$dbname = getenv('MYSQL_DATABASE') ?: 'travel_db';
$pass = getenv('MYSQL_PASSWORD') ?: '';

if (empty($host) || $host === 'localhost') {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "travel_db";
}

$conn = new mysqli($host, $user, $pass, $dbname);

if ($host !== 'localhost' && $host !== '127.0.0.1') {
    $conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
}

if ($conn->connect_error) {
    ob_clean();
    http_response_code(503);
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

$conn->set_charset("utf8");
$data = json_decode(file_get_contents("php://input"), true);
if(!$data) {
    ob_clean();
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid request']));
}
$uid = $data['uid'] ?? '';
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$photo = $data['photo'] ?? '';
if(empty($uid) || empty($email)) {
    ob_clean();
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Missing required fields']));
}
try {
    $stmt = $conn->prepare("SELECT id, firebase_uid, fullname, email, photo FROM users WHERE firebase_uid = ?");
    if(!$stmt) throw new Exception('Prepare: ' . $conn->error);
    $stmt->bind_param("s", $uid);
    if(!$stmt->execute()) throw new Exception('Execute: ' . $stmt->error);
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if(!empty($photo) && $photo !== $user['photo']) {
            $update_stmt = $conn->prepare("UPDATE users SET photo = ? WHERE firebase_uid = ?");
            if($update_stmt) {
                $update_stmt->bind_param("ss", $photo, $uid);
                $update_stmt->execute();
                $update_stmt->close();
            }
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_uid'] = $user['firebase_uid'];
        $_SESSION['user_name'] = $user['fullname'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_photo'] = !empty($photo) ? $photo : $user['photo'];
        $stmt->close();
        ob_clean();
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Login successful', 'existing_user' => true]);
        exit;
    }
    $insert_stmt = $conn->prepare("INSERT INTO users (firebase_uid, fullname, email, photo) VALUES (?, ?, ?, ?)");
    if(!$insert_stmt) throw new Exception('Insert prepare: ' . $conn->error);
    $insert_stmt->bind_param("ssss", $uid, $name, $email, $photo);
    if(!$insert_stmt->execute()) throw new Exception('Insert execute: ' . $insert_stmt->error);
    $new_user_id = $conn->insert_id;
    $_SESSION['user_id'] = $new_user_id;
    $_SESSION['user_uid'] = $uid;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_photo'] = $photo;
    $insert_stmt->close();
    $stmt->close();
    ob_clean();
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Account created successfully', 'existing_user' => false]);
    exit;
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}