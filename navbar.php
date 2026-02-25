<?php 
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Get current page name for active link highlight
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark" id="mainNav">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand fw-bold fs-3 animate-brand" href="index.php">
      <i class="fas fa-plane-departure"></i> TravelGo
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Links -->
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">

        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='index.php') ? 'active' : '' ?>" href="index.php">Home</a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= ($current_page=='destination.php') ? 'active' : '' ?>" href="destination.php" role="button" data-bs-toggle="dropdown">
            Destinations
          </a>
          <ul class="dropdown-menu shadow-lg border-0 rounded-3 p-3 animate-dropdown">
            <li><a class="dropdown-item" href="destination.php?id=1">Europe</a></li>
            <li><a class="dropdown-item" href="destination.php?id=2">Asia</a></li>
            <li><a class="dropdown-item" href="destination.php?id=3">America</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item fw-bold text-primary" href="destination.php">View All →</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='about.php') ? 'active' : '' ?>" href="about.php">About</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='contact.php') ? 'active' : '' ?>" href="contact.php">Contact</a>
        </li>

        <!-- ✅ Profile / Auth -->
        <?php if(isset($_SESSION['user_id'])): ?>
          <!-- User Logged In -->
          <li class="nav-item dropdown ms-lg-3">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
              <img src="<?= $_SESSION['user_photo'] ?? 'assets/icons/user.png'; ?>" 
                   class="rounded-circle me-2" width="35" height="35" style="object-fit:cover;">
              <span><?= $_SESSION['user_name'] ?? 'User'; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-3 animate-dropdown">
              <li><a class="dropdown-item" href="profile.php"><i class="fa fa-user me-2"></i> My Profile</a></li>
              <li><a class="dropdown-item" href="mytrips.php"><i class="fa fa-suitcase me-2"></i> My Trips</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa fa-sign-out me-2"></i> Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <!-- Not Logged In -->
          <li class="nav-item ms-lg-3">
            <a href="login.php" class="btn btn-outline-light ripple rounded-pill px-3 me-2">Login</a>
            <a href="signup.php" class="btn btn-warning ripple rounded-pill px-3">Sign Up</a>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<style>
/* Navbar Base */
#mainNav {
  transition: all 0.4s ease-in-out;
  background: rgba(13, 110, 253, 0.15);
  backdrop-filter: blur(12px);
  padding: 18px 0;
  z-index: 1050;
}
#mainNav.scrolled {
  background: linear-gradient(90deg, rgba(13, 110, 253, 0.95), rgba(0, 123, 255, 0.95));
  box-shadow: 0 6px 18px rgba(0,0,0,0.25);
  padding: 10px 0;
}

/* Brand Animation */
.animate-brand {
  animation: slideIn 1s ease forwards;
  opacity: 0;
}
@keyframes slideIn {
  from {transform: translateX(-50px); opacity: 0;}
  to {transform: translateX(0); opacity: 1;}
}

/* Nav Links */
.nav-link {
  position: relative;
  font-weight: 500;
  margin-left: 12px;
  margin-right: 12px;
  color: #fff !important;
  transition: 0.3s;
}
.nav-link::after {
  content: "";
  position: absolute;
  width: 0;
  height: 2px;
  left: 50%;
  bottom: -5px;
  background: #ffc107;
  transition: 0.4s ease;
  transform: translateX(-50%);
}
.nav-link:hover,
.nav-link.active {
  color: #ffc107 !important;
  text-shadow: 0 0 8px #ffc107;
}
.nav-link:hover::after,
.nav-link.active::after {
  width: 70%;
}

/* Dropdown Animations */
.animate-dropdown {
  animation: dropdownFade 0.4s ease;
}
@keyframes dropdownFade {
  from {opacity: 0; transform: translateY(15px);}
  to {opacity: 1; transform: translateY(0);}
}
.dropdown-item {
  transition: all 0.3s ease;
}
.dropdown-item:hover {
  background: #f8f9fa;
  border-radius: 6px;
  transform: scale(1.05);
  color: #0d6efd;
}

/* Ripple Effect for Buttons */
.ripple {
  position: relative;
  overflow: hidden;
}
.ripple::after {
  content: "";
  position: absolute;
  background: rgba(255,255,255,0.6);
  border-radius: 50%;
  transform: scale(0);
  width: 100px;
  height: 100px;
  opacity: 0;
  pointer-events: none;
  animation: none;
}
.ripple:active::after {
  animation: rippleEffect 0.6s linear;
  opacity: 1;
}
@keyframes rippleEffect {
  from {transform: scale(0); opacity: 0.7;}
  to {transform: scale(3); opacity: 0;}
}

/* Mobile Nav Improvements */
.navbar-toggler {
  border: none;
  outline: none;
}
.navbar-toggler:focus {
  box-shadow: none;
}
</style>

<script>
// Change navbar background on scroll
window.addEventListener("scroll", function(){
  let nav = document.getElementById("mainNav");
  if(window.scrollY > 50){
    nav.classList.add("scrolled");
  } else {
    nav.classList.remove("scrolled");
  }
});
</script>
