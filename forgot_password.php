<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password - TravelGo</title>
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    body { font-family: Arial, sans-serif; background: linear-gradient(135deg,#6f42c1,#0d6efd); height:100vh; }
    .forgot-container { max-width: 450px; margin: auto; margin-top:10%; background:#fff; padding:40px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.2); }
    .forgot-container h2 { font-weight:700; margin-bottom:20px; text-align:center; }
    .form-control:focus { box-shadow:none; border-color:#6f42c1; }
    .btn-primary { width:100%; padding:12px; font-size:1.1rem; }
    .forgot-footer { text-align:center; margin-top:15px; }
    .forgot-footer a { text-decoration:none; color:#6f42c1; font-weight:500; }
    .forgot-footer a:hover { text-decoration:underline; }
  </style>
</head>
<body>

<div class="forgot-container">
  <h2>Forgot Your Password?</h2>
  <p class="text-center">Enter your email to receive a password reset link.</p>
  
  <form action="process_forgot.php" method="POST">
    <div class="mb-3">
      <label for="email" class="form-label">Email address</label>
      <input type="email" name="email" id="email" class="form-control" placeholder="Enter your registered email" required>
    </div>
    <button type="submit" class="btn btn-primary">Send Reset Link</button>
  </form>

  <div class="forgot-footer mt-3">
    <p>Remembered your password? <a href="login.php">Login</a></p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
