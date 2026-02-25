<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Simple admin check (in production, use proper role-based access)
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}

// Fetch statistics
$stats = [];
$stats['total_users'] = $conn->query("SELECT COUNT(*) as count FROM firebase_users")->fetch_assoc()['count'];
$stats['total_bookings'] = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$stats['total_destinations'] = $conn->query("SELECT COUNT(*) as count FROM destinations")->fetch_assoc()['count'];
$stats['total_reviews'] = $conn->query("SELECT COUNT(*) as count FROM reviews")->fetch_assoc()['count'];
$stats['total_revenue'] = $conn->query("SELECT SUM(total_price) as total FROM bookings WHERE payment_status = 'paid'")->fetch_assoc()['total'] ?? 0;

// Recent bookings
$recent_bookings = $conn->query("SELECT b.*, d.country, u.fullname FROM bookings b JOIN destinations d ON b.destination_id = d.id JOIN firebase_users u ON b.user_id = u.id ORDER BY b.created_at DESC LIMIT 10");

// Booking stats by status
$booking_stats = $conn->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");

// Top destinations
$top_destinations = $conn->query("SELECT d.country, COUNT(b.id) as booking_count, AVG(r.rating) as avg_rating FROM destinations d LEFT JOIN bookings b ON d.id = b.destination_id LEFT JOIN reviews r ON d.id = r.destination_id GROUP BY d.id ORDER BY booking_count DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - TravelGo</title>
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
  
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f8f9fa;
    }
    
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 250px;
      background: linear-gradient(135deg, #0d6efd 0%, #6f42c1 100%);
      color: white;
      padding: 20px;
      overflow-y: auto;
    }
    
    .sidebar h3 {
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 2px solid rgba(255,255,255,0.2);
    }
    
    .sidebar .nav-link {
      color: rgba(255,255,255,0.8);
      padding: 12px 15px;
      margin-bottom: 10px;
      border-radius: 8px;
      transition: all 0.3s;
    }
    
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
      background: rgba(255,255,255,0.2);
      color: white;
    }
    
    .main-content {
      margin-left: 250px;
      padding: 30px;
    }
    
    .stat-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      transition: all 0.3s;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }
    
    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin-bottom: 15px;
    }
    
    .stat-number {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 5px;
    }
    
    .stat-label {
      color: #6c757d;
      font-size: 0.9rem;
    }
    
    .chart-container {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h3><i class="fas fa-chart-line me-2"></i>Admin Panel</h3>
    <nav class="nav flex-column">
      <a class="nav-link active" href="#dashboard"><i class="fas fa-home me-2"></i>Dashboard</a>
      <a class="nav-link" href="admin.php"><i class="fas fa-envelope me-2"></i>Enquiries</a>
      <a class="nav-link" href="#bookings"><i class="fas fa-calendar-check me-2"></i>Bookings</a>
      <a class="nav-link" href="#destinations"><i class="fas fa-map-marked-alt me-2"></i>Destinations</a>
      <a class="nav-link" href="#users"><i class="fas fa-users me-2"></i>Users</a>
      <a class="nav-link" href="#reviews"><i class="fas fa-star me-2"></i>Reviews</a>
      <a class="nav-link" href="index.php"><i class="fas fa-arrow-left me-2"></i>Back to Site</a>
    </nav>
  </div>
  
  <!-- Main Content -->
  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1><i class="fas fa-tachometer-alt me-3"></i>Dashboard Overview</h1>
      <div>
        <span class="text-muted me-3"><i class="fas fa-calendar me-2"></i><?php echo date('F d, Y'); ?></span>
        <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
      </div>
    </div>
    
    <!-- Statistics -->
    <div class="row">
      <div class="col-lg-3 col-md-6">
        <div class="stat-card">
          <div class="stat-icon" style="background: #e3f2fd; color: #1976d2;">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-number text-primary"><?php echo number_format($stats['total_users']); ?></div>
          <div class="stat-label">Total Users</div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6">
        <div class="stat-card">
          <div class="stat-icon" style="background: #fff3e0; color: #f57c00;">
            <i class="fas fa-suitcase"></i>
          </div>
          <div class="stat-number text-warning"><?php echo number_format($stats['total_bookings']); ?></div>
          <div class="stat-label">Total Bookings</div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6">
        <div class="stat-card">
          <div class="stat-icon" style="background: #e8f5e9; color: #388e3c;">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="stat-number text-success">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
          <div class="stat-label">Total Revenue</div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6">
        <div class="stat-card">
          <div class="stat-icon" style="background: #fce4ec; color: #c2185b;">
            <i class="fas fa-star"></i>
          </div>
          <div class="stat-number text-danger"><?php echo number_format($stats['total_reviews']); ?></div>
          <div class="stat-label">Total Reviews</div>
        </div>
      </div>
    </div>
    
    <!-- Charts -->
    <div class="row mt-4">
      <div class="col-lg-6">
        <div class="chart-container">
          <h5 class="mb-4"><i class="fas fa-chart-pie me-2"></i>Booking Status</h5>
          <canvas id="bookingStatusChart"></canvas>
        </div>
      </div>
      
      <div class="col-lg-6">
        <div class="chart-container">
          <h5 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Top Destinations</h5>
          <canvas id="topDestinationsChart"></canvas>
        </div>
      </div>
    </div>
    
    <!-- Recent Bookings -->
    <div class="chart-container">
      <h5 class="mb-4"><i class="fas fa-history me-2"></i>Recent Bookings</h5>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer</th>
              <th>Destination</th>
              <th>Date</th>
              <th>Travelers</th>
              <th>Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while($booking = $recent_bookings->fetch_assoc()): ?>
              <tr>
                <td>#<?php echo $booking['id']; ?></td>
                <td><?php echo htmlspecialchars($booking['fullname']); ?></td>
                <td><?php echo htmlspecialchars($booking['country']); ?></td>
                <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                <td><?php echo $booking['travelers']; ?></td>
                <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                <td>
                  <span class="badge bg-<?php 
                    echo match($booking['status']) {
                      'pending' => 'warning',
                      'confirmed' => 'info',
                      'completed' => 'success',
                      'cancelled' => 'danger',
                      default => 'secondary'
                    };
                  ?>"><?php echo ucfirst($booking['status']); ?></span>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
    // Booking Status Chart
    const bookingStatusCtx = document.getElementById('bookingStatusChart').getContext('2d');
    <?php 
    $status_data = [];
    while($stat = $booking_stats->fetch_assoc()) {
      $status_data[$stat['status']] = $stat['count'];
    }
    ?>
    new Chart(bookingStatusCtx, {
      type: 'doughnut',
      data: {
        labels: <?php echo json_encode(array_keys($status_data)); ?>,
        datasets: [{
          data: <?php echo json_encode(array_values($status_data)); ?>,
          backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#dc3545']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
    
    // Top Destinations Chart
    const topDestCtx = document.getElementById('topDestinationsChart').getContext('2d');
    <?php 
    $dest_labels = [];
    $dest_counts = [];
    while($dest = $top_destinations->fetch_assoc()) {
      $dest_labels[] = $dest['country'];
      $dest_counts[] = $dest['booking_count'];
    }
    ?>
    new Chart(topDestCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($dest_labels); ?>,
        datasets: [{
          label: 'Bookings',
          data: <?php echo json_encode($dest_counts); ?>,
          backgroundColor: 'rgba(13, 110, 253, 0.8)',
          borderColor: 'rgba(13, 110, 253, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</body>
</html>