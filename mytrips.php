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

// Fetch user's bookings
$query = "SELECT b.*, d.country, d.image, d.description 
          FROM bookings b 
          JOIN destinations d ON b.destination_id = d.id 
          WHERE b.user_id = ? 
          ORDER BY b.booking_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Trips - TravelGo</title>
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
  
  <style>
    :root {
      --accent: #0d6efd;
      --glass: rgba(255,255,255,0.08);
      --card: rgba(255,255,255,0.88);
      --muted: #94a3b8;
    }

    body {
      font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
      background: radial-gradient(1200px 600px at 10% 10%, rgba(13,110,253,0.08), transparent),
                  radial-gradient(1000px 500px at 90% 90%, rgba(111,66,193,0.04), transparent),
                  linear-gradient(180deg, #f5fbff 0%, #eef9ff 40%, #f2fbf9 100%);
      padding-top: 80px;
      min-height: 100vh;
    }
    
    .page-header {
      background: linear-gradient(135deg, #0d6efd 0%, #6f42c1 100%);
      color: white;
      padding: 60px 0;
      margin-bottom: 40px;
      border-radius: 0 0 30px 30px;
      box-shadow: 0 30px 80px rgba(13,110,253,0.12);
      animation: headerSlideDown .8s cubic-bezier(.2,.9,.2,1);
    }

    @keyframes headerSlideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: none; } }

    .trip-card {
      background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(255,255,255,0.88));
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0,0,0,0.08);
      margin-bottom: 30px;
      transition: all .3s cubic-bezier(.2,.9,.2,1);
      border: 1px solid rgba(255,255,255,0.5);
      backdrop-filter: blur(8px);
    }
    
    .trip-card:hover {
      transform: translateY(-8px) scale(1.01);
      box-shadow: 0 40px 100px rgba(13,110,253,0.12);
    }
    
    .trip-image {
      height: 220px;
      object-fit: cover;
      width: 100%;
      filter: brightness(0.95);
      transition: filter .3s;
    }

    .trip-card:hover .trip-image { filter: brightness(1.05); }
    
    .trip-body {
      padding: 30px;
    }
    
    .trip-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      gap: 15px;
      margin-bottom: 20px;
    }

    .trip-header h4 { margin: 0; font-weight: 700; font-size: 1.4rem; }
    
    .status-badge {
      padding: 10px 16px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      animation: badgePulse 2s ease-in-out infinite;
    }

    @keyframes badgePulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.85; } }
    
    .status-pending {
      background: linear-gradient(90deg, #fff3cd, #ffe6b3);
      color: #856404;
    }
    
    .status-confirmed {
      background: linear-gradient(90deg, #d1ecf1, #b3e5fc);
      color: #0c5460;
    }
    
    .status-completed {
      background: linear-gradient(90deg, #d4edda, #c8e6c9);
      color: #155724;
    }
    
    .status-cancelled {
      background: linear-gradient(90deg, #f8d7da, #ffcdd2);
      color: #721c24;
    }
    
    .trip-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin: 20px 0;
      padding: 15px;
      background: linear-gradient(135deg, rgba(13,110,253,0.04), rgba(111,66,193,0.04));
      border-radius: 12px;
      border: 1px solid rgba(13,110,253,0.08);
    }

    .trip-info-item {
      display: flex;
      flex-direction: column;
    }

    .trip-info-item small {
      color: var(--muted);
      font-size: 0.8rem;
      margin-bottom: 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .trip-info-item strong {
      font-size: 1.1rem;
      color: #0b1220;
    }
    
    .filter-tabs {
      background: linear-gradient(180deg, rgba(255,255,255,0.75), rgba(255,255,255,0.65));
      border-radius: 16px;
      padding: 16px;
      margin-bottom: 40px;
      box-shadow: 0 12px 40px rgba(13,110,253,0.08);
      backdrop-filter: blur(6px);
      border: 1px solid rgba(255,255,255,0.5);
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
    }
    
    .filter-tabs .btn {
      border-radius: 12px;
      padding: 10px 16px;
      font-weight: 600;
      transition: all .2s ease;
      border: 2px solid transparent;
    }

    .filter-tabs .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(13,110,253,0.15);
    }

    .filter-tabs .btn.active {
      transform: scale(1.08);
      box-shadow: 0 12px 30px rgba(13,110,253,0.2);
    }

    .action-buttons {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 20px;
    }

    .action-buttons .btn {
      flex: 1;
      min-width: 120px;
      border-radius: 10px;
      font-weight: 600;
      transition: all .2s ease;
      padding: 8px 12px;
      font-size: 0.9rem;
    }

    .action-buttons .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .spinner-custom {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 3px solid rgba(255,255,255,0.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .modal-content {
      background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(255,255,255,0.88));
      border: 1px solid rgba(13,110,253,0.1);
      border-radius: 16px;
      box-shadow: 0 30px 80px rgba(0,0,0,0.12);
    }

    .modal-header {
      background: linear-gradient(90deg, #0d6efd, #6f42c1);
      color: white;
      border: none;
      border-radius: 16px 16px 0 0;
    }

    .rating-stars { display: flex; gap: 8px; }
    .rating-star {
      cursor: pointer;
      color: #ddd;
      transition: all .2s ease;
      font-size: 2rem;
    }

    .rating-star:hover {
      transform: scale(1.15) rotate(15deg);
      color: #ffc107;
    }

    .rating-star[data-filled='true'] {
      color: #ffc107;
      text-shadow: 0 0 10px rgba(255,193,7,0.5);
    }

    /* Checklist styles */
    .checklist-container {
      background: linear-gradient(135deg, rgba(13,110,253,0.05), rgba(111,66,193,0.03));
      border-radius: 12px;
      padding: 16px;
      border: 1px solid rgba(13,110,253,0.1);
      margin-top: 15px;
    }

    .checklist-item {
      display: flex;
      align-items: center;
      padding: 8px;
      margin: 4px 0;
      background: white;
      border-radius: 8px;
      transition: all .2s ease;
    }

    .checklist-item:hover { background: rgba(13,110,253,0.05); }

    .checklist-item input[type="checkbox"] {
      cursor: pointer;
      width: 18px;
      height: 18px;
      margin-right: 12px;
    }

    .checklist-item input[type="checkbox"]:checked + label {
      text-decoration: line-through;
      opacity: 0.6;
      color: var(--muted);
    }

    .checklist-item label {
      cursor: pointer;
      margin: 0;
      flex: 1;
      font-weight: 500;
    }
  </style>
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  
  <div class="page-header">
    <div class="container text-center">
      <h1><i class="fas fa-suitcase me-3"></i>My Trips</h1>
      <p class="lead">Manage your bookings and travel history</p>
    </div>
  </div>
  
  <div class="container">
    <!-- Filter Tabs -->
    <div class="filter-tabs text-center" data-aos="fade-up">
      <button class="btn btn-primary" onclick="filterTrips('all')">All Trips</button>
      <button class="btn btn-outline-warning" onclick="filterTrips('pending')">Pending</button>
      <button class="btn btn-outline-info" onclick="filterTrips('confirmed')">Confirmed</button>
      <button class="btn btn-outline-success" onclick="filterTrips('completed')">Completed</button>
      <button class="btn btn-outline-danger" onclick="filterTrips('cancelled')">Cancelled</button>
    </div>
    
    <!-- Trips List -->
    <div class="row" id="tripsContainer">
      <?php if($bookings->num_rows > 0): ?>
        <?php while($booking = $bookings->fetch_assoc()): ?>
          <div class="col-lg-6 trip-item" data-status="<?php echo $booking['status']; ?>" data-aos="fade-up">
            <div class="trip-card">
              <img src="assets/images/<?php echo $booking['image']; ?>" 
                   alt="<?php echo htmlspecialchars($booking['country']); ?>" 
                   class="trip-image"
                   onerror="this.src='https://source.unsplash.com/800x400/?<?php echo urlencode($booking['country']); ?>'">
              <div class="trip-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <h4><?php echo htmlspecialchars($booking['country']); ?></h4>
                  <span class="status-badge status-<?php echo $booking['status']; ?>">
                    <?php echo ucfirst($booking['status']); ?>
                  </span>
                </div>
                
                <p class="text-muted mb-3">
                  <?php echo substr($booking['description'], 0, 100); ?>...
                </p>
                
                <div class="row mb-3">
                  <div class="col-6">
                    <small class="text-muted d-block"><i class="fas fa-calendar-check me-2"></i>Check-in</small>
                    <strong><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></strong>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block"><i class="fas fa-calendar-times me-2"></i>Check-out</small>
                    <strong><?php echo $booking['return_date'] ? date('M d, Y', strtotime($booking['return_date'])) : 'N/A'; ?></strong>
                  </div>
                </div>
                
                <div class="row mb-3">
                  <div class="col-6">
                    <small class="text-muted d-block"><i class="fas fa-users me-2"></i>Travelers</small>
                    <strong><?php echo $booking['travelers']; ?> person<?php echo $booking['travelers'] > 1 ? 's' : ''; ?></strong>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block"><i class="fas fa-dollar-sign me-2"></i>Total Price</small>
                    <strong class="text-primary">$<?php echo number_format($booking['total_price'], 2); ?></strong>
                  </div>
                </div>
                
                <?php if($booking['special_requests']): ?>
                  <div class="alert alert-light mb-3">
                    <small><strong>Special Requests:</strong> <?php echo htmlspecialchars($booking['special_requests']); ?></small>
                  </div>
                <?php endif; ?>
                
                <div class="d-flex gap-2">
                  <a href="destination.php?id=<?php echo $booking['destination_id']; ?>" class="btn btn-outline-primary btn-sm flex-grow-1">
                    <i class="fas fa-eye me-2"></i>View Details
                  </a>
                  
                  <?php if($booking['status'] == 'completed'): ?>
                    <button class="btn btn-warning btn-sm" onclick="writeReview(<?php echo $booking['id']; ?>, <?php echo $booking['destination_id']; ?>)">
                      <i class="fas fa-star me-2"></i>Write Review
                    </button>
                  <?php endif; ?>
                  
                  <?php if($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                    <button class="btn btn-danger btn-sm" onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                      <i class="fas fa-times me-2"></i>Cancel
                    </button>
                  <?php endif; ?>
                </div>
                
                <small class="text-muted d-block mt-3">
                  <i class="fas fa-info-circle me-2"></i>Booked on <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                </small>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-info text-center py-5" data-aos="fade-up">
            <i class="fas fa-plane-slash fa-4x mb-3 d-block"></i>
            <h4>No trips found</h4>
            <p>You haven't booked any trips yet. Start exploring amazing destinations!</p>
            <a href="destinations.php" class="btn btn-primary mt-3">
              <i class="fas fa-search me-2"></i>Browse Destinations
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
  
  <!-- Review Modal -->
  <div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title"><i class="fas fa-star me-2"></i>Write a Review</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="reviewForm">
            <input type="hidden" id="reviewBookingId">
            <input type="hidden" id="reviewDestinationId">
            
            <div class="mb-3">
              <label class="form-label">Rating</label>
              <div class="rating-stars fs-3">
                <i class="fas fa-star rating-star" data-rating="1"></i>
                <i class="fas fa-star rating-star" data-rating="2"></i>
                <i class="fas fa-star rating-star" data-rating="3"></i>
                <i class="fas fa-star rating-star" data-rating="4"></i>
                <i class="fas fa-star rating-star" data-rating="5"></i>
              </div>
              <input type="hidden" id="ratingValue" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Review Title</label>
              <input type="text" class="form-control" id="reviewTitle" required placeholder="Summarize your experience">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Your Review</label>
              <textarea class="form-control" id="reviewComment" rows="4" required placeholder="Share your thoughts about this destination"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-warning" onclick="submitReview()">
            <i class="fas fa-paper-plane me-2"></i>Submit Review
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <footer class="bg-dark text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; 2025 TravelGo. All rights reserved.</p>
  </footer>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    AOS.init({duration: 800, once: true});
    
    function filterTrips(status) {
      const items = document.querySelectorAll('.trip-item');
      items.forEach(item => {
        if(status === 'all' || item.dataset.status === status) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    }
    
    function writeReview(bookingId, destinationId) {
      document.getElementById('reviewBookingId').value = bookingId;
      document.getElementById('reviewDestinationId').value = destinationId;
      new bootstrap.Modal(document.getElementById('reviewModal')).show();
    }
    
    function cancelBooking(bookingId) {
      if(confirm('Are you sure you want to cancel this booking?')) {
        fetch('api/cancel_booking.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({booking_id: bookingId})
        })
        .then(res => res.json())
        .then(data => {
          if(data.success) {
            alert('Booking cancelled successfully');
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        });
      }
    }
    
    // Rating stars
    const stars = document.querySelectorAll('.rating-star');
    stars.forEach(star => {
      star.addEventListener('click', function() {
        const rating = this.dataset.rating;
        document.getElementById('ratingValue').value = rating;
        stars.forEach((s, i) => {
          s.style.color = i < rating ? '#ffc107' : '#ddd';
        });
      });
    });
    
    function submitReview() {
      const bookingId = document.getElementById('reviewBookingId').value;
      const destinationId = document.getElementById('reviewDestinationId').value;
      const rating = document.getElementById('ratingValue').value;
      const title = document.getElementById('reviewTitle').value;
      const comment = document.getElementById('reviewComment').value;
      
      if(!rating) {
        alert('Please select a rating');
        return;
      }
      
      fetch('api/submit_review.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
          booking_id: bookingId,
          destination_id: destinationId,
          rating: rating,
          title: title,
          comment: comment
        })
      })
      .then(res => res.json())
      .then(data => {
        if(data.success) {
          alert('Review submitted successfully!');
          bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      });
    }
  </script>
</body>
</html>