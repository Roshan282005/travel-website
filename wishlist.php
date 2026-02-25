<?php 
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items
$query = "SELECT w.*, d.country, d.image, d.description, d.price_range, d.rating 
          FROM wishlist w 
          JOIN destinations d ON w.destination_id = d.id 
          WHERE w.user_id = ? 
          ORDER BY w.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Wishlist - TravelGo</title>
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
    
    .page-header {
      background: linear-gradient(135deg, #e91e63 0%, #f06292 100%);
      color: white;
      padding: 60px 0;
      margin-bottom: 40px;
      border-radius: 0 0 30px 30px;
    }
    
    .wishlist-card {
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      transition: all 0.3s;
    }
    
    .wishlist-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .wishlist-image {
      height: 250px;
      object-fit: cover;
      width: 100%;
      position: relative;
    }
    
    .remove-btn {
      position: absolute;
      top: 15px;
      right: 15px;
      background: white;
      border: none;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      color: #e91e63;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
      z-index: 10;
    }
    
    .remove-btn:hover {
      background: #e91e63;
      color: white;
      transform: scale(1.1);
    }
    
    .wishlist-body {
      padding: 25px;
    }
    
    .price-badge {
      background: linear-gradient(135deg, #0d6efd, #6f42c1);
      color: white;
      padding: 8px 15px;
      border-radius: 20px;
      font-weight: 600;
    }
    
    .rating-badge {
      background: #ffc107;
      color: #000;
      padding: 5px 12px;
      border-radius: 15px;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  
  <div class="page-header">
    <div class="container text-center">
      <h1><i class="fas fa-heart me-3"></i>My Wishlist</h1>
      <p class="lead">Save your dream destinations for later</p>
    </div>
  </div>
  
  <div class="container">
    <div class="row">
      <?php if($wishlist->num_rows > 0): ?>
        <?php while($item = $wishlist->fetch_assoc()): ?>
          <div class="col-lg-4 col-md-6" data-aos="fade-up">
            <div class="wishlist-card">
              <div style="position: relative;">
                <img src="assets/images/<?php echo $item['image']; ?>" 
                     alt="<?php echo htmlspecialchars($item['country']); ?>" 
                     class="wishlist-image"
                     onerror="this.src='https://source.unsplash.com/800x600/?<?php echo urlencode($item['country']); ?>'">
                <button class="remove-btn" onclick="removeFromWishlist(<?php echo $item['destination_id']; ?>)">
                  <i class="fas fa-heart"></i>
                </button>
              </div>
              
              <div class="wishlist-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <h4><?php echo htmlspecialchars($item['country']); ?></h4>
                  <?php if($item['rating']): ?>
                    <span class="rating-badge">
                      <i class="fas fa-star"></i> <?php echo $item['rating']; ?>
                    </span>
                  <?php endif; ?>
                </div>
                
                <p class="text-muted mb-3">
                  <?php echo substr($item['description'], 0, 120); ?>...
                </p>
                
                <?php if($item['price_range']): ?>
                  <div class="mb-3">
                    <small class="text-muted"><i class="fas fa-dollar-sign me-2"></i>Price Range</small><br>
                    <span class="price-badge"><?php echo $item['price_range']; ?></span>
                  </div>
                <?php endif; ?>
                
                <div class="d-flex gap-2">
                  <a href="destination.php?id=<?php echo $item['destination_id']; ?>" class="btn btn-outline-primary flex-grow-1">
                    <i class="fas fa-eye me-2"></i>View Details
                  </a>
                  <a href="book.php?id=<?php echo $item['destination_id']; ?>" class="btn btn-primary">
                    <i class="fas fa-plane me-2"></i>Book Now
                  </a>
                </div>
                
                <small class="text-muted d-block mt-3">
                  <i class="fas fa-clock me-2"></i>Added <?php echo date('M d, Y', strtotime($item['created_at'])); ?>
                </small>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-info text-center py-5" data-aos="fade-up">
            <i class="fas fa-heart-broken fa-4x mb-3 d-block text-muted"></i>
            <h4>Your wishlist is empty</h4>
            <p>Start adding destinations you'd love to visit!</p>
            <a href="destinations.php" class="btn btn-primary mt-3">
              <i class="fas fa-search me-2"></i>Explore Destinations
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
  
  <footer class="bg-dark text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; 2025 TravelGo. All rights reserved.</p>
  </footer>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    AOS.init({duration: 800, once: true});
    
    function removeFromWishlist(destinationId) {
      if(confirm('Remove this destination from your wishlist?')) {
        fetch('api/toggle_wishlist.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({destination_id: destinationId})
        })
        .then(res => res.json())
        .then(data => {
          if(data.success) {
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        });
      }
    }
  </script>
</body>
</html>