<?php
header('Content-Type: application/json');
include '../db.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if(!isset($_SESSION['user_id'])){
  echo json_encode(['success' => false, 'message' => 'Not authenticated']);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user_id'];
$booking_id = $input['booking_id'] ?? null;
$destination_id = $input['destination_id'] ?? 0;
$rating = $input['rating'] ?? 0;
$title = $input['title'] ?? '';
$comment = $input['comment'] ?? '';

// Validate
if(!$destination_id || !$rating || $rating < 1 || $rating > 5) {
  echo json_encode(['success' => false, 'message' => 'Invalid input']);
  exit;
}

// Create review
$stmt = $conn->prepare("INSERT INTO reviews (user_id, destination_id, booking_id, rating, title, comment) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiss", $user_id, $destination_id, $booking_id, $rating, $title, $comment);

if($stmt->execute()) {
  // Update destination rating
  $update_stmt = $conn->prepare("UPDATE destinations SET rating = (SELECT AVG(rating) FROM reviews WHERE destination_id = ?), review_count = (SELECT COUNT(*) FROM reviews WHERE destination_id = ?) WHERE id = ?");
  $update_stmt->bind_param("iii", $destination_id, $destination_id, $destination_id);
  $update_stmt->execute();
  
  // Log activity
  $action = 'review';
  $ip = $_SERVER['REMOTE_ADDR'];
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  $log_stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, entity_type, entity_id, ip_address, user_agent) VALUES (?, ?, 'review', ?, ?, ?)");
  $log_stmt->bind_param("isiss", $user_id, $action, $conn->insert_id, $ip, $user_agent);
  $log_stmt->execute();
  
  echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to submit review']);
}
?>