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
$booking_id = $input['booking_id'] ?? 0;

if(!$booking_id) {
  echo json_encode(['success' => false, 'message' => 'Invalid booking']);
  exit;
}

// Verify booking belongs to user
$check_stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
$check_stmt->bind_param("ii", $booking_id, $user_id);
$check_stmt->execute();

if($check_stmt->get_result()->num_rows == 0) {
  echo json_encode(['success' => false, 'message' => 'Booking not found']);
  exit;
}

// Update booking status
$stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
$stmt->bind_param("i", $booking_id);

if($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to cancel booking']);
}
?>