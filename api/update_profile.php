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
$fullname = $input['fullname'] ?? '';
$photo = $input['photo'] ?? '';

if(!$fullname) {
  echo json_encode(['success' => false, 'message' => 'Name is required']);
  exit;
}

// Update profile
$stmt = $conn->prepare("UPDATE firebase_users SET fullname = ?, photo = ? WHERE id = ?");
$stmt->bind_param("ssi", $fullname, $photo, $user_id);

if($stmt->execute()) {
  // Update session
  $_SESSION['user_name'] = $fullname;
  $_SESSION['user_photo'] = $photo;
  
  echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
?>