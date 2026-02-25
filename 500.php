<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>500 - Server Error | TravelGo</title>
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', Arial, sans-serif;
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }
    .error-container {
      text-align: center;
      padding: 40px;
    }
    .error-code {
      font-size: 120px;
      font-weight: 900;
      line-height: 1;
      margin-bottom: 20px;
      text-shadow: 4px 4px 8px rgba(0,0,0,0.3);
    }
    .btn-home {
      background: white;
      color: #f5576c;
      padding: 15px 40px;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s;
    }
    .btn-home:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      color: #f5576c;
    }
  </style>
</head>
<body>
  <div class="error-container">
    <div class="error-code">500</div>
    <div class="error-message">
      <i class="fas fa-exclamation-triangle fa-3x mb-4"></i>
      <h2>Internal Server Error</h2>
      <p>Something went wrong on our end. Please try again later.</p>
    </div>
    <a href="index.php" class="btn-home">
      <i class="fas fa-home me-2"></i>Back to Home
    </a>
  </div>
</body>
</html>