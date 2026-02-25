<?php
include 'db.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Get user_id from session
$user_id = $_SESSION['user_id'] ?? 1;

// Check if user already exists
$check = $conn->prepare("SELECT id FROM firebase_users WHERE id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$result = $check->get_result();

if($result->num_rows > 0) {
  echo "User already exists in firebase_users table.";
} else {
  // Create test user
  $firebase_uid = "test_user_" . $user_id;
  $fullname = "Test User";
  $email = "testuser@example.com";
  $photo = "https://ui-avatars.com/api/?name=Test+User&size=150&background=0d6efd&color=fff";
  
  $stmt = $conn->prepare("INSERT INTO firebase_users (id, firebase_uid, fullname, email, photo) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issss", $user_id, $firebase_uid, $fullname, $email, $photo);
  
  if($stmt->execute()) {
    echo "✅ Test user created successfully!<br>";
    echo "User ID: " . $user_id . "<br>";
    echo "Email: " . $email . "<br>";
    echo "Name: " . $fullname . "<br><br>";
    echo "<a href='profile.php'>Go to Profile</a>";
  } else {
    echo "❌ Error creating user: " . $stmt->error;
  }
}
?>
