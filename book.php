<?php 
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}

$destination_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch destination details
$stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$destination = $stmt->get_result()->fetch_assoc();

if(!$destination){
  header("Location: destinations.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book <?php echo htmlspecialchars($destination['country']); ?> - TravelGo</title>
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      padding-top: 80px;
      min-height: 100vh;
    }
    
    .booking-container {
      max-width: 1200px;
      margin: 30px auto;
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .destination-preview {
      background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), 
                  url('assets/images/<?php echo $destination['image']; ?>') center/cover;
      color: white;
      padding: 80px 40px;
      text-align: center;
    }
    
    .booking-form-section {
      padding: 40px;
    }
    
    .price-summary {
      background: linear-gradient(135deg, #0d6efd 0%, #6f42c1 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-top: 20px;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .traveler-card {
      background: #f8f9fa;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 15px;
    }
    
    .payment-method {
      cursor: pointer;
      border: 2px solid #dee2e6;
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 15px;
      transition: all 0.3s;
    }
    
    .payment-method:hover {
      border-color: #0d6efd;
      background: #f8f9fa;
    }
    
    .payment-method.selected {
      border-color: #0d6efd;
      background: #e7f1ff;
    }
    
    .payment-method input[type="radio"] {
      margin-right: 10px;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  
  <div class="booking-container">
    <!-- Destination Preview -->
    <div class="destination-preview">
      <h1><i class="fas fa-map-marker-alt me-3"></i><?php echo htmlspecialchars($destination['country']); ?></h1>
      <p class="lead"><?php echo htmlspecialchars($destination['description']); ?></p>
      <div class="mt-3">
        <?php if($destination['rating']): ?>
          <span class="badge bg-warning text-dark fs-5">
            <i class="fas fa-star"></i> <?php echo $destination['rating']; ?>
          </span>
          <small class="ms-2">(<?php echo $destination['review_count']; ?> reviews)</small>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Booking Form -->
    <div class="booking-form-section">
      <form id="bookingForm">
        <input type="hidden" name="destination_id" value="<?php echo $destination_id; ?>">
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
        
        <div class="row">
          <div class="col-lg-8">
            <!-- Travel Dates -->
            <h4 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Travel Dates</h4>
            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <label class="form-label">Check-in Date</label>
                <input type="date" class="form-control" name="booking_date" id="checkIn" required 
                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Check-out Date</label>
                <input type="date" class="form-control" name="return_date" id="checkOut" required>
              </div>
            </div>
            
            <!-- Travelers -->
            <h4 class="mb-4"><i class="fas fa-users me-2"></i>Travelers</h4>
            <div class="mb-4">
              <label class="form-label">Number of Travelers</label>
              <select class="form-select" name="travelers" id="travelers" required onchange="calculatePrice()">
                <option value="">Select number of travelers</option>
                <?php for($i=1; $i<=10; $i++): ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?> person<?php echo $i>1?'s':''; ?></option>
                <?php endfor; ?>
              </select>
            </div>
            
            <!-- Special Requests -->
            <h4 class="mb-4"><i class="fas fa-comment me-2"></i>Special Requests</h4>
            <div class="mb-4">
              <textarea class="form-control" name="special_requests" rows="4" 
                        placeholder="Any special requests or requirements?"></textarea>
            </div>
            
            <!-- Payment Method -->
            <h4 class="mb-4"><i class="fas fa-credit-card me-2"></i>Payment Method</h4>
            <div class="payment-methods mb-4">
              <div class="payment-method" onclick="selectPayment('credit_card')">
                <label class="w-100">
                  <input type="radio" name="payment_method" value="credit_card" required>
                  <i class="fas fa-credit-card fa-2x me-3"></i>
                  <span class="fs-5">Credit/Debit Card</span>
                </label>
              </div>
              
              <div class="payment-method" onclick="selectPayment('paypal')">
                <label class="w-100">
                  <input type="radio" name="payment_method" value="paypal" required>
                  <i class="fab fa-paypal fa-2x me-3"></i>
                  <span class="fs-5">PayPal</span>
                </label>
              </div>
              
              <div class="payment-method" onclick="selectPayment('bank_transfer')">
                <label class="w-100">
                  <input type="radio" name="payment_method" value="bank_transfer" required>
                  <i class="fas fa-university fa-2x me-3"></i>
                  <span class="fs-5">Bank Transfer</span>
                </label>
              </div>
            </div>
            
            <!-- Terms -->
            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" id="termsCheck" required>
              <label class="form-check-label" for="termsCheck">
                I agree to the <a href="#" target="_blank">Terms and Conditions</a> and <a href="#" target="_blank">Privacy Policy</a>
              </label>
            </div>
          </div>
          
          <!-- Price Summary -->
          <div class="col-lg-4">
            <div class="price-summary sticky-top" style="top: 90px;">
              <h4 class="mb-4"><i class="fas fa-file-invoice-dollar me-2"></i>Price Summary</h4>
              
              <div class="d-flex justify-content-between mb-3">
                <span>Base Price (per person)</span>
                <strong>$<span id="basePrice">500</span></strong>
              </div>
              
              <div class="d-flex justify-content-between mb-3">
                <span>Travelers</span>
                <strong><span id="numTravelers">0</span></strong>
              </div>
              
              <div class="d-flex justify-content-between mb-3">
                <span>Number of Nights</span>
                <strong><span id="numNights">0</span></strong>
              </div>
              
              <hr class="border-white">
              
              <div class="d-flex justify-content-between mb-3">
                <span>Subtotal</span>
                <strong>$<span id="subtotal">0</span></strong>
              </div>
              
              <div class="d-flex justify-content-between mb-3">
                <span>Service Fee (5%)</span>
                <strong>$<span id="serviceFee">0</span></strong>
              </div>
              
              <div class="d-flex justify-content-between mb-3">
                <span>Taxes (8%)</span>
                <strong>$<span id="taxes">0</span></strong>
              </div>
              
              <hr class="border-white">
              
              <div class="d-flex justify-content-between fs-4 mb-4">
                <strong>Total</strong>
                <strong>$<span id="totalPrice">0</span></strong>
              </div>
              
              <button type="submit" class="btn btn-warning w-100 btn-lg">
                <i class="fas fa-lock me-2"></i>Confirm & Pay
              </button>
              
              <small class="d-block text-center mt-3 opacity-75">
                <i class="fas fa-shield-alt me-1"></i>Secure payment processing
              </small>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <footer class="bg-dark text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; 2025 TravelGo. All rights reserved.</p>
  </footer>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const basePrice = 500;
    
    // Set min checkout date
    document.getElementById('checkIn').addEventListener('change', function() {
      const checkInDate = new Date(this.value);
      const minCheckOut = new Date(checkInDate);
      minCheckOut.setDate(minCheckOut.getDate() + 1);
      document.getElementById('checkOut').min = minCheckOut.toISOString().split('T')[0];
      calculatePrice();
    });
    
    document.getElementById('checkOut').addEventListener('change', calculatePrice);
    
    function selectPayment(method) {
      document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
      event.currentTarget.classList.add('selected');
      document.querySelector(`input[value="${method}"]`).checked = true;
    }
    
    function calculatePrice() {
      const checkIn = document.getElementById('checkIn').value;
      const checkOut = document.getElementById('checkOut').value;
      const travelers = parseInt(document.getElementById('travelers').value) || 0;
      
      let nights = 0;
      if(checkIn && checkOut) {
        const date1 = new Date(checkIn);
        const date2 = new Date(checkOut);
        nights = Math.ceil((date2 - date1) / (1000 * 60 * 60 * 24));
      }
      
      const subtotal = basePrice * travelers * Math.max(nights, 1);
      const serviceFee = subtotal * 0.05;
      const taxes = subtotal * 0.08;
      const total = subtotal + serviceFee + taxes;
      
      document.getElementById('basePrice').textContent = basePrice;
      document.getElementById('numTravelers').textContent = travelers;
      document.getElementById('numNights').textContent = nights;
      document.getElementById('subtotal').textContent = subtotal.toFixed(2);
      document.getElementById('serviceFee').textContent = serviceFee.toFixed(2);
      document.getElementById('taxes').textContent = taxes.toFixed(2);
      document.getElementById('totalPrice').textContent = total.toFixed(2);
    }
    
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const totalPrice = document.getElementById('totalPrice').textContent;
      formData.append('total_price', totalPrice);
      
      const data = Object.fromEntries(formData);
      
      fetch('api/create_booking.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(result => {
        if(result.success) {
          alert('Booking created successfully! Redirecting to payment...');
          window.location.href = 'mytrips.php';
        } else {
          alert('Error: ' + result.message);
        }
      })
      .catch(error => {
        alert('Booking failed. Please try again.');
        console.error(error);
      });
    });
  </script>
</body>
</html>