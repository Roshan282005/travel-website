<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - TravelGo</title>
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
      height: 60vh;
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

    /* Card Section */
    .feature-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
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

    /* Animated Fade-in on scroll */
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
  <h1 class="animate__animated animate__fadeInDown">Explore the World with TravelGo</h1>
</div>

<!-- About Section -->
<div class="container mb-5">
  <h2 class="section-title animate__animated animate__fadeInUp">About Us</h2>
  <p class="text-center fs-5 animate__animated animate__fadeInUp animate__delay-1s">
    Welcome to TravelGo! We specialize in providing the best travel experiences worldwide. 
    Our mission is to make your dream vacations a reality with personalized guidance, insider tips, 
    and hassle-free bookings. Explore, plan, and create unforgettable memories with us!
  </p>
</div>

<!-- Features Section -->
<div class="container mb-5">
  <h2 class="section-title animate__animated animate__fadeInUp">Why Choose Us</h2>
  <div class="row g-4">
    <div class="col-md-4 fade-up">
      <div class="feature-card p-4 text-center">
        <img src="https://img.icons8.com/ios-filled/100/00aaff/globe.png" alt="Global Destinations" class="mb-3">
        <h5>Global Destinations</h5>
        <p>Discover amazing places across the globe with detailed guides and recommendations.</p>
      </div>
    </div>
    <div class="col-md-4 fade-up">
      <div class="feature-card p-4 text-center">
        <img src="https://img.icons8.com/ios-filled/100/00aaff/airplane-take-off.png" alt="Easy Booking" class="mb-3">
        <h5>Easy Booking</h5>
        <p>Plan and book your trips seamlessly with our user-friendly platform and travel support.</p>
      </div>
    </div>
    <div class="col-md-4 fade-up">
      <div class="feature-card p-4 text-center">
        <img src="https://img.icons8.com/ios-filled/100/00aaff/compass.png" alt="Expert Guidance" class="mb-3">
        <h5>Expert Guidance</h5>
        <p>Our travel experts provide personalized advice to make every trip extraordinary.</p>
      </div>
    </div>
  </div>
</div>

<!-- Team Section -->
<div class="container mb-5">
  <h2 class="section-title animate__animated animate__fadeInUp">Meet Our Team</h2>
  <div class="row g-4">
    <div class="col-md-3 fade-up">
      <div class="feature-card text-center p-3">
        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="rounded-circle mb-3" width="120" alt="Team Member">
        <h6>Jane Doe</h6>
        <small>Travel Expert</small>
      </div>
    </div>
    <div class="col-md-3 fade-up">
      <div class="feature-card text-center p-3">
        <img src="https://randomuser.me/api/portraits/men/46.jpg" class="rounded-circle mb-3" width="120" alt="Team Member">
        <h6>John Smith</h6>
        <small>Tour Planner</small>
      </div>
    </div>
    <div class="col-md-3 fade-up">
      <div class="feature-card text-center p-3">
        <img src="https://randomuser.me/api/portraits/women/65.jpg" class="rounded-circle mb-3" width="120" alt="Team Member">
        <h6>Emily Clark</h6>
        <small>Customer Support</small>
      </div>
    </div>
    <div class="col-md-3 fade-up">
      <div class="feature-card text-center p-3">
        <img src="https://randomuser.me/api/portraits/men/65.jpg" class="rounded-circle mb-3" width="120" alt="Team Member">
        <h6>Michael Lee</h6>
        <small>Marketing Head</small>
      </div>
    </div>
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
