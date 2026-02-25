<?php include 'navbar.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - TravelGo</title>
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <!-- Firebase -->
  <script src="https://www.gstatic.com/firebasejs/10.11.0/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.11.0/firebase-auth-compat.js"></script>

  <style>
    body {
      font-family: 'Poppins', Arial, sans-serif;
      background: linear-gradient(155deg, #667eea 0%, #764ba2 200%);
      min-height: 130%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-wrapper {
      max-width: 1000px;
      width: 100%;
      margin-top: 7vh;
      display: flex;
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .login-image {
      flex: 1;
      background: linear-gradient(rgba(102, 126, 234, 0.8), rgba(118, 75, 162, 0.8)),
        url('https://images.unsplash.com/photo-1488085061387-422e29b40080?w=800&q=80') center/cover;
      padding: 60px 40px;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .login-image h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .login-image p {
      font-size: 1.1rem;
      opacity: 0.9;
      line-height: 1.6;
    }

    .login-container {
      flex: 1;
      padding: 60px 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .login-container h2 {
      font-weight: 700;
      margin-bottom: 10px;
      color: #333;
      font-size: 2rem;
    }

    .login-subtitle {
      color: #6c757d;
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-control {
      padding: 12px 15px;
      border-radius: 10px;
      border: 2px solid #e9ecef;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary {
      width: 100%;
      padding: 14px;
      font-size: 1.1rem;
      font-weight: 600;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 10px;
      transition: all 0.3s;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .divider {
      text-align: center;
      margin: 25px 0;
      position: relative;
    }

    .divider::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      width: 100%;
      height: 1px;
      background: #dee2e6;
    }

    .divider span {
      position: relative;
      background: white;
      padding: 0 15px;
      color: #6c757d;
      font-size: 0.9rem;
    }

    .google-btn {
      width: 100%;
      padding: 12px;
      border: 2px solid #e9ecef;
      background: white;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .google-btn:hover {
      border-color: #667eea;
      background: #f8f9fa;
      transform: translateY(-2px);
    }

    .google-btn i {
      font-size: 1.3rem;
      background: conic-gradient(from -45deg, #4285F4 0deg 90deg, #34A853 90deg 180deg, #FBBC05 180deg 270deg, #EA4335 270deg 360deg);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .login-footer {
      text-align: center;
      margin-top: 25px;
      font-size: 0.95rem;
    }

    .login-footer a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
    }

    .login-footer a:hover {
      text-decoration: underline;
    }

    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
      transition: color 0.3s;
    }

    .password-toggle:hover {
      color: #667eea;
    }

    .position-relative {
      position: relative;
    }

    .alert {
      border-radius: 10px;
      padding: 12px 15px;
      margin-bottom: 20px;
      display: none;
    }

    .form-check-input:checked {
      background-color: #667eea;
      border-color: #667eea;
    }

    .loading-spinner {
      display: none;
      width: 20px;
      height: 20px;
      border: 3px solid #f3f3f3;
      border-top: 3px solid #667eea;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    @media (max-width: 768px) {
      .login-wrapper {
        flex-direction: column;
      }

      .login-image {
        min-height: 200px;
        padding: 40px 20px;
      }

      .login-container {
        padding: 40px 30px;
      }

      .login-image h2 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>

<body>

  <div class="login-wrapper animate__animated animate__fadeIn">
    <!-- Left Side - Branding -->
    <div class="login-image d-none d-md-flex">
      <div>
        <h2><i class="fas fa-plane-departure me-3"></i>Welcome Back!</h2>
        <p>Login to continue your journey with TravelGo. Explore amazing destinations around the world.</p>
        <div class="mt-4">
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <span>Access exclusive travel deals</span>
          </div>
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <span>Save your favorite destinations</span>
          </div>
          <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <span>Track your travel history</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-container">
      <h2 style="text-align: center; font-family:'Times New Roman', Times, serif;">Sign In</h2>
      <p style="text-align: center; font-family:'Times New Roman', Times, serif;" class="login-subtitle">Enter your credentials to access your account</p>

      <!-- Alert Messages -->
      <div class="alert alert-danger" id="errorAlert" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><span id="errorMessage"></span>
      </div>

      <div class="alert alert-success" id="successAlert" role="alert">
        <i class="fas fa-check-circle me-2"></i><span id="successMessage"></span>
      </div>

      <form id="loginForm">
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" id="email" class="form-control" placeholder="you@example.com" required autocomplete="email">
        </div>

        <div class="form-group position-relative">
          <label class="form-label">Password</label>
          <input type="password" id="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
          <i class="fas fa-eye password-toggle" id="togglePassword"></i>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe">
            <label class="form-check-label" for="rememberMe">
              Remember me
            </label>
          </div>
          <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-primary" id="loginBtn">
          <span id="loginText">Sign In</span>
          <div class="loading-spinner" id="loginSpinner"></div>
        </button>
      </form>

      <div class="divider">
        <span>OR</span>
      </div>

      <button type="button" id="googleSignInBtn" class="google-btn">
        <i class="fab fa-google"></i>
        <span>Continue with Google</span>
      </button>

      <div class="login-footer">
        <p>Don't have an account? <a href="signup.php">Create one now</a></p>
      </div>
    </div>
  </div>

  <script>
    // Firebase configuration
    const firebaseConfig = {
      apiKey: "AIzaSyDoIAiSnBx8GGNhjQkEB7j1bANx7k2l8dc",
      authDomain: "rizzauthapp.firebaseapp.com",
      databaseURL: "https://rizzauthapp-default-rtdb.asia-southeast1.firebasedatabase.app",
      projectId: "rizzauthapp",
      storageBucket: "rizzauthapp.firebasestorage.app",
      messagingSenderId: "607508317395",
      appId: "1:607508317395:web:f2f403d10915d6d2ef4026",
      measurementId: "G-2YQFBWK95F"
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const auth = firebase.auth();

    // Password toggle
    const togglePassword = document.querySelector('#togglePassword');
    togglePassword.addEventListener('click', () => {
      const pwd = document.querySelector('#password');
      const type = pwd.type === 'password' ? 'text' : 'password';
      pwd.type = type;
      togglePassword.classList.toggle('fa-eye-slash');
    });

    // Helper functions
    function showError(message) {
      const errorAlert = document.getElementById('errorAlert');
      const errorMessage = document.getElementById('errorMessage');
      errorMessage.textContent = message;
      errorAlert.style.display = 'block';
      setTimeout(() => errorAlert.style.display = 'none', 5000);
    }

    function showSuccess(message) {
      const successAlert = document.getElementById('successAlert');
      const successMessage = document.getElementById('successMessage');
      successMessage.textContent = message;
      successAlert.style.display = 'block';
      setTimeout(() => successAlert.style.display = 'none', 3000);
    }

    function setLoading(isLoading) {
      const loginBtn = document.getElementById('loginBtn');
      const loginText = document.getElementById('loginText');
      const loginSpinner = document.getElementById('loginSpinner');

      if (isLoading) {
        loginBtn.disabled = true;
        loginText.style.display = 'none';
        loginSpinner.style.display = 'inline-block';
      } else {
        loginBtn.disabled = false;
        loginText.style.display = 'inline';
        loginSpinner.style.display = 'none';
      }
    }

    // Email/password login
    document.getElementById('loginForm').addEventListener('submit', e => {
      e.preventDefault();
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;

      setLoading(true);

      auth.signInWithEmailAndPassword(email, password)
        .then(userCredential => {
          const user = userCredential.user;

          return fetch('firebase_login.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              uid: user.uid,
              name: user.displayName || email.split('@')[0],
              email: user.email,
              photo: user.photoURL || ''
            })
          });
        })
        .then(res => res.json())
        .then(data => {
          setLoading(false);
          if (data.success) {
            showSuccess('Login successful! Redirecting...');
            setTimeout(() => window.location.href = 'index.php', 1000);
          } else {
            showError('Error saving user data. Please try again.');
          }
        })
        .catch(error => {
          setLoading(false);
          let errorMessage = 'Login failed. Please try again.';

          if (error.code === 'auth/user-not-found') {
            errorMessage = 'No account found with this email.';
          } else if (error.code === 'auth/wrong-password') {
            errorMessage = 'Incorrect password. Please try again.';
          } else if (error.code === 'auth/invalid-email') {
            errorMessage = 'Invalid email address.';
          } else if (error.code === 'auth/user-disabled') {
            errorMessage = 'This account has been disabled.';
          }

          showError(errorMessage);
        });
    });

    // Google sign-in
    document.getElementById('googleSignInBtn').addEventListener('click', async () => {
      try {
        // Disable button to prevent multiple clicks
        const googleBtn = document.getElementById('googleSignInBtn');
        googleBtn.disabled = true;
        googleBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><span>Signing in...</span>';
        
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.addScope('profile');
        provider.addScope('email');
        
        // Set language
        auth.languageCode = 'en';

        console.log('Attempting Google sign-in...');
        const result = await auth.signInWithPopup(provider);
        console.log('Google sign-in successful:', result.user.email);
        
        const user = result.user;

        // Save to backend
        console.log('Saving user data to backend...');
        const response = await fetch('firebase_login.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            uid: user.uid,
            name: user.displayName || 'User',
            email: user.email,
            photo: user.photoURL || ''
          })
        });
        
        // Get response text first to debug
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        
        let data;
        try {
          data = JSON.parse(responseText);
        } catch (parseError) {
          console.error('JSON parse error:', parseError);
          console.error('Response was:', responseText.substring(0, 200));
          throw new Error(`Invalid response from server: ${responseText.substring(0, 100)}`);
        }
        
        console.log('Backend response:', data);
        
        if (data.success) {
          showSuccess('Login successful! Redirecting...');
          setTimeout(() => window.location.href = 'index.php', 1000);
        } else {
          showError(data.message || 'Error saving user data. Please try again.');
          googleBtn.disabled = false;
          googleBtn.innerHTML = '<i class="fab fa-google me-2"></i><span>Continue with Google</span>';
        }
      } catch (error) {
        console.error('Google sign-in error:', error);
        
        // Re-enable button
        const googleBtn = document.getElementById('googleSignInBtn');
        googleBtn.disabled = false;
        googleBtn.innerHTML = '<i class="fab fa-google me-2"></i><span>Continue with Google</span>';
        
        // Handle specific error codes
        if (error.code === 'auth/popup-closed-by-user') {
          showError('Sign-in cancelled. Please try again.');
        } else if (error.code === 'auth/popup-blocked') {
          showError('Pop-up blocked! Please allow pop-ups for this site and try again.');
        } else if (error.code === 'auth/network-request-failed') {
          showError('Network error. Please check your internet connection.');
        } else {
          showError('Google sign-in failed: ' + (error.message || 'Unknown error'));
          console.error('Full error details:', error);
        }
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>