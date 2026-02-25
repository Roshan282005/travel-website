<?php 
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT firebase_uid, fullname, email, photo, created_at FROM firebase_users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Redirect if user not found
if(!$user) {
  echo "<script>alert('User profile not found. Please log in again.'); window.location.href = 'login.php';</script>";
  exit;
}

// Fetch user statistics
$stats_query = "SELECT 
  (SELECT COUNT(*) FROM bookings WHERE user_id = ?) as total_bookings,
  (SELECT COUNT(*) FROM reviews WHERE user_id = ?) as total_reviews,
  (SELECT COUNT(*) FROM wishlist WHERE user_id = ?) as wishlist_count";
$stmt_stats = $conn->prepare($stats_query);
$stmt_stats->bind_param("iii", $user_id, $user_id, $user_id);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();

// Default stats if query fails
if(!$stats) {
  $stats = array('total_bookings' => 0, 'total_reviews' => 0, 'wishlist_count' => 0);
}

// Fetch recent activity
$activity_query = "SELECT * FROM activity_log WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
$stmt_activity = $conn->prepare($activity_query);
$stmt_activity->bind_param("i", $user_id);
$stmt_activity->execute();
$activities = $stmt_activity->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - TravelGo</title>
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
  
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      padding-top: 80px;
      min-height: 100vh;
    }
    
    .profile-header {
      background: linear-gradient(135deg, #0d6efd 0%, #6f42c1 100%);
      color: white;
      padding: 40px 0;
      margin-bottom: 30px;
      border-radius: 0 0 30px 30px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .profile-avatar {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      border: 5px solid white;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      object-fit: cover;
      margin-bottom: 20px;
    }
    
    .stat-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      transition: transform 0.3s, box-shadow 0.3s;
      border: none;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .stat-number {
      font-size: 2.5rem;
      font-weight: bold;
      background: linear-gradient(135deg, #0d6efd, #6f42c1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .stat-label {
      color: #6c757d;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    
    .activity-item {
      background: white;
      border-radius: 12px;
      padding: 15px 20px;
      margin-bottom: 15px;
      border-left: 4px solid #0d6efd;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
      transition: all 0.3s;
    }
    
    .activity-item:hover {
      transform: translateX(5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #0d6efd, #6f42c1);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
    }
    
    .edit-btn {
      background: linear-gradient(135deg, #ffc107, #ff9800);
      border: none;
      padding: 10px 30px;
      border-radius: 25px;
      color: white;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .edit-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
      color: white;
    }
    
    .section-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 25px;
      position: relative;
      padding-bottom: 10px;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: 0;
      width: 60px;
      height: 4px;
      background: linear-gradient(135deg, #0d6efd, #6f42c1);
      border-radius: 2px;
    }
    
    .badge-custom {
      padding: 8px 15px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .animated {
      animation: fadeInUp 0.6s ease-out;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  
  <!-- Profile Header -->
  <div class="profile-header">
    <div class="container text-center">
      <img src="<?php echo $user['photo'] ?? 'https://ui-avatars.com/api/?name='.urlencode($user['fullname']).'&size=150&background=0d6efd&color=fff'; ?>" 
           alt="Profile" class="profile-avatar" id="profileAvatar">
      <h2 class="mb-2"><?php echo htmlspecialchars($user['fullname']); ?></h2>
      <p class="mb-3"><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($user['email']); ?></p>
      <p class="mb-3"><small><i class="fas fa-calendar-alt me-2"></i>Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></small></p>
      <button class="edit-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">
        <i class="fas fa-edit me-2"></i>Edit Profile
      </button>
    </div>
  </div>
  
  <div class="container">
    <div class="row">
      <!-- Statistics -->
      <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
        <div class="stat-card text-center">
          <i class="fas fa-plane-departure fa-3x mb-3" style="color: #0d6efd;"></i>
          <div class="stat-number"><?php echo $stats['total_bookings']; ?></div>
          <div class="stat-label">Total Trips</div>
        </div>
      </div>
      
      <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card text-center">
          <i class="fas fa-star fa-3x mb-3" style="color: #ffc107;"></i>
          <div class="stat-number"><?php echo $stats['total_reviews']; ?></div>
          <div class="stat-label">Reviews Written</div>
        </div>
      </div>
      
      <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-card text-center">
          <i class="fas fa-heart fa-3x mb-3" style="color: #e91e63;"></i>
          <div class="stat-number"><?php echo $stats['wishlist_count']; ?></div>
          <div class="stat-label">Wishlist Items</div>
        </div>
      </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-5" data-aos="fade-up">
      <div class="col-12">
        <h3 class="section-title">Quick Actions</h3>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <a href="mytrips.php" class="btn btn-outline-primary w-100 py-3">
          <i class="fas fa-suitcase me-2"></i>My Trips
        </a>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <a href="wishlist.php" class="btn btn-outline-danger w-100 py-3">
          <i class="fas fa-heart me-2"></i>Wishlist
        </a>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <a href="destinations.php" class="btn btn-outline-success w-100 py-3">
          <i class="fas fa-search me-2"></i>Browse Destinations
        </a>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <a href="index.php#map" class="btn btn-outline-info w-100 py-3">
          <i class="fas fa-map-marked-alt me-2"></i>Explore Map
        </a>
      </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="row mb-5" data-aos="fade-up">
      <div class="col-12">
        <h3 class="section-title">Recent Activity</h3>
        <?php if($activities->num_rows > 0): ?>
          <?php while($activity = $activities->fetch_assoc()): ?>
            <div class="activity-item d-flex align-items-center">
              <div class="activity-icon">
                <i class="fas fa-<?php 
                  echo match($activity['action']) {
                    'booking' => 'plane',
                    'review' => 'star',
                    'wishlist' => 'heart',
                    'login' => 'sign-in-alt',
                    default => 'info-circle'
                  };
                ?>"></i>
              </div>
              <div class="flex-grow-1">
                <strong><?php echo ucfirst($activity['action']); ?></strong>
                <small class="text-muted d-block">
                  <i class="fas fa-clock me-1"></i><?php echo date('M d, Y - h:i A', strtotime($activity['created_at'])); ?>
                </small>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No recent activity found. Start exploring destinations!
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Edit Profile Modal -->
  <div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Profile</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editProfileForm">
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" id="editFullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" id="editEmail" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
              <small class="text-muted">Email cannot be changed</small>
            </div>
            <div class="mb-3">
              <label class="form-label">Profile Photo URL</label>
              <input type="url" class="form-control" id="editPhoto" value="<?php echo htmlspecialchars($user['photo'] ?? ''); ?>" placeholder="https://example.com/photo.jpg">
            </div>
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>Changes will be saved to your profile
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveProfile()">
            <i class="fas fa-save me-2"></i>Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; 2025 TravelGo. All rights reserved.</p>
  </footer>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    AOS.init({
      duration: 800,
      once: true
    });
    
    function saveProfile() {
      const fullname = document.getElementById('editFullname').value;
      const photo = document.getElementById('editPhoto').value;
      
      // Send AJAX request to update profile
      fetch('api/update_profile.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          fullname: fullname,
          photo: photo
        })
      })
      .then(response => response.json())
      .then(data => {
        if(data.success) {
          alert('Profile updated successfully!');
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        alert('Error updating profile');
        console.error(error);
      });
    }
  </script>
</body>
</html>