<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - TravelGo</title>
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png" />

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Animate.css for animations -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <!-- Custom Styles -->
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(to right, #f7f9fc, #e3f2fd);
      color: #333;
    }

    /* Hero Section */
    .hero {
      background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1470&q=80') no-repeat center center/cover;
      height: 50vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.6);
      border-radius: 12px;
      margin-bottom: 40px;
    }
    .hero h1 {
      font-size: 3rem;
      font-weight: 700;
      animation: fadeInDown 2s ease-in-out;
    }

    /* Section Headings */
    h2.section-title {
      font-weight: 700;
      margin-bottom: 30px;
      text-align: center;
      position: relative;
    }
    h2.section-title::after {
      content: '';
      display: block;
      width: 60px;
      height: 4px;
      background: #00aaff;
      margin: 10px auto 0;
      border-radius: 2px;
    }

    /* Form Card */
    .contact-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      padding: 40px;
      max-width: 600px;
      margin: auto;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .contact-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    /* Inputs & Button */
    .form-control {
      border-radius: 10px;
      padding: 15px;
      transition: all 0.3s;
    }
    .form-control:focus {
      box-shadow: 0 0 10px rgba(0, 170, 255, 0.5);
      border-color: #00aaff;
    }
    .btn-custom {
      background: #00aaff;
      border: none;
      padding: 12px 25px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s;
    }
    .btn-custom:hover {
      background: #007bb5;
      transform: translateY(-3px);
    }

    /* Scroll Animation */
    .fade-up {
      opacity: 0;
      transform: translateY(50px);
      transition: all 1s ease-out;
    }
    .fade-up.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* Footer Styling */
    footer {
      background: #0d6efd;
      color: white;
      padding: 30px 0;
      text-align: center;
      margin-top: 50px;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- Hero Section -->
<div class="hero">
  <h1 class="animate__animated animate__fadeInDown">Get in Touch with Us</h1>
</div>

<!-- Contact Form Section -->
<div class="container mb-5 fade-up">
  <h2 class="section-title animate__animated animate__fadeInUp">Contact Us</h2>
  
  <div class="contact-card animate__animated animate__fadeInUp animate__delay-1s">
    <form action="enquiry.php" method="post">
      <div class="mb-3">
        <input type="text" name="name" class="form-control" placeholder="Your Name" required>
      </div>
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Your Email" required>
      </div>
      <div class="mb-3">
        <textarea name="message" class="form-control" rows="5" placeholder="Your Message" required></textarea>
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-custom">Send Message</button>
      </div>
    </form>
  </div>
</div>

<!-- Footer -->
<footer>
  &copy; 2025 TravelGo. All rights reserved.
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Intersection Observer for Scroll Animations -->
<script>
  const fadeElements = document.querySelectorAll('.fade-up');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if(entry.isIntersecting){
        entry.target.classList.add('visible');
      }
    });
  }, { threshold: 0.1 });
  fadeElements.forEach(el => observer.observe(el));
</script>

</body>
</html>
