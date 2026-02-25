?<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forgot_password</title>
    <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png" />
</head>
<body>
    
</body>
</html>
<?php
include 'db.php'; // Your database connection
require 'vendor/autoload.php'; // Composer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['email'])){
    $email = $_POST['email'];

    // Check if email exists in DB
    $stmt = $conn->prepare("SELECT id, fullname FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        $stmt->bind_result($user_id, $fullname);
        $stmt->fetch();

        // Generate unique token
        $token = bin2hex(random_bytes(50));

        // Store token in DB with expiry (1 hour)
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $stmt2 = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE id=?");
        $stmt2->bind_param("ssi", $token, $expires, $user_id);
        $stmt2->execute();

        // Send email
        $resetLink = "http://localhost/travel-website/reset_password.php?token=".$token;

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com"; // Gmail SMTP
            $mail->SMTPAuth = true;
            $mail->Username = "your-email@gmail.com"; // Replace with your Gmail
            $mail->Password = "your-app-password";   // Gmail App password
            $mail->SMTPSecure = "tls";
            $mail->Port = 587;

            // Recipients
            $mail->setFrom("your-email@gmail.com", "TravelGo");
            $mail->addAddress($email, $fullname);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $mail->Body = "
                <h3>Hello $fullname,</h3>
                <p>We received a request to reset your password.</p>
                <p>Click the link below to reset it:</p>
                <a href='$resetLink'>Reset Password</a>
                <p>This link will expire in 1 hour.</p>
            ";

            $mail->send();
            echo "<script>alert('Password reset link sent to your email!'); window.location='login.php';</script>";

        } catch (Exception $e) {
            echo "<script>alert('Mailer Error: {$mail->ErrorInfo}'); window.history.back();</script>";
        }

    } else {
        echo "<script>alert('Email not found.'); window.history.back();</script>";
    }

} else {
    header("Location: forgot_password.php");
    exit;
}
?>
