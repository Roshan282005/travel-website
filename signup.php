<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - TravelGo</title>
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
      background: linear-gradient(150deg, #f093fb 0%, #f5576c 100%);
      min-height: 168%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .signup-wrapper {
      max-width: 900px;
      width: 100%;
      display: flex;
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .signup-image {
      flex: 1;
      background: linear-gradient(rgba(240, 147, 251, 0.8), rgba(245, 87, 108, 0.8)),
        url('https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800&q=80') center/cover;
      padding: 60px 40px;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .signup-image h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .signup-image p {
      font-size: 1.1rem;
      opacity: 0.9;
      line-height: 1.6;
    }

    .signup-container {
      flex: 1;
      padding: 60px 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      overflow-y: auto;
      max-height: 170vh;
    }

    .signup-container h2 {
      font-weight: 700;
      margin-bottom: 10px;
      color: #333;
      font-size: 2rem;
    }

    .signup-subtitle {
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
      border-color: #f093fb;
      box-shadow: 0 0 0 0.2rem rgba(240, 147, 251, 0.25);
    }

    .btn-warning {
      width: 100%;
      padding: 14px;
      font-size: 1.1rem;
      font-weight: 600;
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      border: none;
      border-radius: 10px;
      color: white;
      transition: all 0.3s;
    }

    .btn-warning:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(240, 147, 251, 0.3);
      color: white;
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
      border-color: #f093fb;
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

    .signup-footer {
      text-align: center;
      margin-top: 25px;
      font-size: 0.95rem;
    }

    .signup-footer a {
      color: #f093fb;
      text-decoration: none;
      font-weight: 600;
    }

    .signup-footer a:hover {
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
      color: #f093fb;
    }

    .position-relative {
      position: relative;
    }

    .password-strength {
      height: 4px;
      background: #e9ecef;
      border-radius: 2px;
      margin-top: 8px;
      overflow: hidden;
    }

    .password-strength-bar {
      height: 100%;
      width: 0%;
      transition: all 0.3s;
      border-radius: 2px;
    }

    .strength-weak {
      background: #dc3545;
      width: 33%;
    }

    .strength-medium {
      background: #ffc107;
      width: 66%;
    }

    .strength-strong {
      background: #28a745;
      width: 100%;
    }

    .alert {
      border-radius: 10px;
      padding: 12px 15px;
      margin-bottom: 20px;
      display: none;
    }

    .loading-spinner {
      display: none;
      width: 20px;
      height: 20px;
      border: 3px solid #f3f3f3;
      border-top: 3px solid #f093fb;
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
      .signup-wrapper {
        flex-direction: column;
      }

      .signup-image {
        min-height: 200px;
        padding: 40px 20px;
      }

      .signup-container {
        padding: 40px 30px;
      }

      .signup-image h2 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>

<body>

  <div class="signup-wrapper animate__animated animate__fadeIn">
    <!-- Left Side - Branding -->
    <div class="signup-image d-none d-md-flex">
      <div>
        <h2><i class="fas fa-globe-americas me-3"></i>Join TravelGo</h2>
        <p>Start your adventure today! Create an account to unlock exclusive features and travel experiences.</p>
        <div class="mt-4">
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <span>Book trips in seconds</span>
          </div>
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <span>Get personalized recommendations</span>
          </div>
          <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <span>Earn rewards on every booking</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side - Signup Form -->
    <div class="signup-container">
      <h2 style="text-align: center; font-family:'Times New Roman', Times, serif;">Create Account</h2>
      <p style="text-align: center; font-family:'Times New Roman', Times, serif;" class="signup-subtitle">Fill in your details to get started</p>

      <!-- Alert Messages -->
      <div class="alert alert-danger" id="errorAlert" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><span id="errorMessage"></span>
      </div>

      <div class="alert alert-success" id="successAlert" role="alert">
        <i class="fas fa-check-circle me-2"></i><span id="successMessage"></span>
      </div>

      <form id="signupForm">
        
        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" id="fullname" class="form-control" placeholder="John Doe" required autocomplete="name">
        </div>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" id="email" class="form-control" placeholder="you@example.com" required autocomplete="email">
        </div>

        <div class="form-group position-relative">
          <label class="form-label">Password</label>
          <input type="password" id="password" class="form-control" placeholder="Create a strong password" required autocomplete="new-password" minlength="6">
          <i class="fas fa-eye password-toggle" id="togglePassword"></i>
          <div class="password-strength">
            <div class="password-strength-bar" id="strengthBar"></div>
          </div>
          <small class="text-muted" id="strengthText"></small>
        </div>

        <div class="form-group position-relative">
          <label class="form-label">Confirm Password</label>
          <input type="password" id="confirm_password" class="form-control" placeholder="Re-enter your password" required autocomplete="new-password">
          <i class="fas fa-eye password-toggle" id="toggleConfirm"></i>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="termsCheck" required>
          <label class="form-check-label" for="termsCheck">
            I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
          </label>
        </div>

        <button type="submit" class="btn btn-warning" id="signupBtn">
          <span id="signupText">Create Account</span>
          <div class="loading-spinner" id="signupSpinner"></div>
        </button>
      </form>

      <div class="divider">
        <span>OR</span>
      </div>

      <button type="button" id="googleSignInBtn" class="google-btn">
        <i class="fab fa-google"></i>
        <span>Sign up with Google</span>
      </button>

      <div class="signup-footer">
        <p>Already have an account? <a href="login.php">Sign in</a></p>
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
      const signupBtn = document.getElementById('signupBtn');
      const signupText = document.getElementById('signupText');
      const signupSpinner = document.getElementById('signupSpinner');

      if (isLoading) {
        signupBtn.disabled = true;
        signupText.style.display = 'none';
        signupSpinner.style.display = 'inline-block';
      } else {
        signupBtn.disabled = false;
        signupText.style.display = 'inline';
        signupSpinner.style.display = 'none';
      }
    }

    // Password strength checker
    function checkPasswordStrength(password) {
      const strengthBar = document.getElementById('strengthBar');
      const strengthText = document.getElementById('strengthText');

      let strength = 0;
      if (password.length >= 8) strength++;
      if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
      if (/\d/.test(password)) strength++;
      if (/[^a-zA-Z\d]/.test(password)) strength++;

      strengthBar.className = 'password-strength-bar';

      if (strength === 0 || strength === 1) {
        strengthBar.classList.add('strength-weak');
        strengthText.textContent = 'Weak password';
        strengthText.style.color = '#dc3545';
      } else if (strength === 2 || strength === 3) {
        strengthBar.classList.add('strength-medium');
        strengthText.textContent = 'Medium strength';
        strengthText.style.color = '#ffc107';
      } else {
        strengthBar.classList.add('strength-strong');
        strengthText.textContent = 'Strong password';
        strengthText.style.color = '#28a745';
      }
    }

    document.getElementById('password').addEventListener('input', (e) => {
      checkPasswordStrength(e.target.value);
    });

    // Password toggle
    const togglePassword = document.querySelector('#togglePassword');
    togglePassword.addEventListener('click', () => {
      const pwd = document.querySelector('#password');
      const type = pwd.type === 'password' ? 'text' : 'password';
      pwd.type = type;
      togglePassword.classList.toggle('fa-eye-slash');
    });

    const toggleConfirm = document.querySelector('#toggleConfirm');
    toggleConfirm.addEventListener('click', () => {
      const pwd = document.querySelector('#confirm_password');
      const type = pwd.type === 'password' ? 'text' : 'password';
      pwd.type = type;
      toggleConfirm.classList.toggle('fa-eye-slash');
    });

    // Email/password sign-up
    document.getElementById('signupForm').addEventListener('submit', e => {
      e.preventDefault();
      const fullname = document.getElementById('fullname').value.trim();
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;

      if (password !== confirmPassword) {
        showError('Passwords do not match');
        return;
      }

      if (password.length < 6) {
        showError('Password must be at least 6 characters long');
        return;
      }

      setLoading(true);

      auth.createUserWithEmailAndPassword(email, password)
        .then(userCredential => {
          const user = userCredential.user;

          // Update display name
          return user.updateProfile({
            displayName: fullname
          }).then(() => user);
        })
        .then(user => {
          return fetch('firebase_login.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              uid: user.uid,
              name: fullname,
              email: user.email,
              photo: user.photoURL || ''
            })
          });
        })
        .then(res => res.json())
        .then(data => {
          setLoading(false);
          if (data.success) {
            showSuccess('Account created successfully! Redirecting...');
            setTimeout(() => window.location.href = 'index.php', 1500);
          } else {
            showError('Error saving user data. Please try again.');
          }
        })
        .catch(error => {
          setLoading(false);
          let errorMessage = 'Signup failed. Please try again.';

          if (error.code === 'auth/email-already-in-use') {
            errorMessage = 'This email is already registered. Please login.';
          } else if (error.code === 'auth/invalid-email') {
            errorMessage = 'Invalid email address.';
          } else if (error.code === 'auth/weak-password') {
            errorMessage = 'Password is too weak. Use at least 6 characters.';
          }

          showError(errorMessage);
        });
    });

    // Google Sign-In
    document.getElementById('googleSignInBtn').addEventListener('click', async () => {
      try {
        const googleBtn = document.getElementById('googleSignInBtn');
        googleBtn.disabled = true;
        googleBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><span>Signing up...</span>';
        
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.addScope('profile');
        provider.addScope('email');
        
        auth.languageCode = 'en';

        console.log('Attempting Google sign-up...');
        const result = await auth.signInWithPopup(provider);
        console.log('Google sign-up successful:', result.user.email);
        
        const user = result.user;

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
        
        // Check if response is OK
        if (!response.ok) {
          throw new Error('HTTP error ' + response.status);
        }
        
        const text = await response.text();
        console.log('Backend response text:', text);
        
        // Try to parse JSON
        let data;
        try {
          data = JSON.parse(text);
        } catch(e) {
          console.error('JSON parse error:', e, 'Response was:', text);
          throw new Error('Invalid server response: ' + text.substring(0, 100));
        }
        
        console.log('Parsed backend response:', data);
        
        if (data.success) {
          showSuccess('Account created successfully! Redirecting...');
          setTimeout(() => window.location.href = 'index.php', 1000);
        } else {
          showError(data.message || 'Error saving user data. Please try again.');
          googleBtn.disabled = false;
          googleBtn.innerHTML = '<i class="fab fa-google me-2"></i><span>Sign up with Google</span>';
        }
      } catch (error) {
        console.error('Google sign-up error:', error);
        
        const googleBtn = document.getElementById('googleSignInBtn');
        googleBtn.disabled = false;
        googleBtn.innerHTML = '<i class="fab fa-google me-2"></i><span>Sign up with Google</span>';
        
        if (error.code === 'auth/popup-closed-by-user') {
          console.log('User cancelled sign-up');
        } else if (error.code === 'auth/popup-blocked') {
          showError('Pop-up blocked! Please allow pop-ups for this site and try again.');
        } else if (error.code === 'auth/network-request-failed') {
          showError('Network error. Please check your internet connection.');
        } else {
          showError('Google sign-up failed: ' + (error.message || 'Unknown error'));
        }
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>