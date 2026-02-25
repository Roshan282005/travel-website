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
$booking_date = $input['booking_date'] ?? '';
$return_date = $input['return_date'] ?? null;
$travelers = $input['travelers'] ?? 1;
$total_price = $input['total_price'] ?? 0;
$payment_method = $input['payment_method'] ?? '';
$special_requests = $input['special_requests'] ?? '';

// Validate inputs
if(!$destination_id || !$booking_date || !$travelers || !$payment_method) {
  echo json_encode(['success' => false, 'message' => 'Missing required fields']);
  exit;
}

// Create booking
$stmt = $conn->prepare("INSERT INTO bookings (user_id, destination_id, booking_date, return_date, travelers, total_price, payment_method, special_requests, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("iissidss", $user_id, $destination_id, $booking_date, $return_date, $travelers, $total_price, $payment_method, $special_requests);

if($stmt->execute()) {
  $booking_id = $conn->insert_id;
  
  // Log activity
  $action = 'booking';
  $ip = $_SERVER['REMOTE_ADDR'];
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  $log_stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, entity_type, entity_id, ip_address, user_agent) VALUES (?, ?, 'booking', ?, ?, ?)");
  $log_stmt->bind_param("isiss", $user_id, $action, $booking_id, $ip, $user_agent);
  $log_stmt->execute();
  
  // Create notification
  $notif_title = "Booking Confirmed";
  $notif_message = "Your booking has been created successfully. Booking ID: #" . $booking_id;
  $notif_type = "booking";
  $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
  $notif_stmt->bind_param("isss", $user_id, $notif_title, $notif_message, $notif_type);
  $notif_stmt->execute();
  
  echo json_encode(['success' => true, 'booking_id' => $booking_id, 'message' => 'Booking created successfully']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
}
?>