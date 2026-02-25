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
$destination_id = $input['destination_id'] ?? 0;

if(!$destination_id) {
  echo json_encode(['success' => false, 'message' => 'Invalid destination']);
  exit;
}

// Check if already in wishlist
$check_stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND destination_id = ?");
$check_stmt->bind_param("ii", $user_id, $destination_id);
$check_stmt->execute();
$exists = $check_stmt->get_result()->num_rows > 0;

if($exists) {
  // Remove from wishlist
  $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND destination_id = ?");
  $stmt->bind_param("ii", $user_id, $destination_id);
  $stmt->execute();
  echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist']);
} else {
  // Add to wishlist
  $stmt = $conn->prepare("INSERT INTO wishlist (user_id, destination_id) VALUES (?, ?)");
  $stmt->bind_param("ii", $user_id, $destination_id);
  $stmt->execute();
  
  // Log activity
  $action = 'wishlist';
  $ip = $_SERVER['REMOTE_ADDR'];
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  $log_stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, entity_type, entity_id, ip_address, user_agent) VALUES (?, ?, 'destination', ?, ?, ?)");
  $log_stmt->bind_param("isiss", $user_id, $action, $destination_id, $ip, $user_agent);
  $log_stmt->execute();
  
  echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist']);
}
?>