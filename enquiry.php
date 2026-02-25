<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $msg = $_POST['message'];

    $sql = "INSERT INTO enquiries (name, email, message) VALUES ('$name','$email','$msg')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Enquiry submitted successfully!'); window.location='index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
