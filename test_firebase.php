<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Firebase Test - TravelGo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://www.gstatic.com/firebasejs/10.11.0/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.11.0/firebase-auth-compat.js"></script>
</head>
<body>
  <div class="container mt-5">
    <h1>Firebase Authentication Test</h1>
    <div class="alert alert-info">
      <strong>Instructions:</strong> Click the button below to test Google sign-in. Check the console (F12) for detailed logs.
    </div>
    
    <div id="status" class="alert alert-secondary">
      Status: Not initialized
    </div>
    
    <button id="testBtn" class="btn btn-primary mb-3">Test Google Sign-In</button>
    <button id="checkAuthBtn" class="btn btn-secondary mb-3">Check Auth State</button>
    <button id="signOutBtn" class="btn btn-danger mb-3">Sign Out</button>
    
    <div class="card">
      <div class="card-header">Console Output</div>
      <div class="card-body">
        <pre id="console" style="max-height: 400px; overflow-y: auto; background: #f5f5f5; padding: 15px; border-radius: 5px;"></pre>
      </div>
    </div>
    
    <div class="card mt-3">
      <div class="card-header">User Info</div>
      <div class="card-body">
        <pre id="userInfo" style="background: #f5f5f5; padding: 15px; border-radius: 5px;">Not signed in</pre>
      </div>
    </div>
  </div>

  <script>
    const consoleEl = document.getElementById('console');
    const statusEl = document.getElementById('status');
    const userInfoEl = document.getElementById('userInfo');
    
    function log(message, type = 'info') {
      const timestamp = new Date().toLocaleTimeString();
      const color = {
        'info': 'blue',
        'success': 'green',
        'error': 'red',
        'warning': 'orange'
      }[type] || 'black';
      
      const logEntry = `[${timestamp}] ${message}\n`;
      consoleEl.innerHTML += `<span style="color: ${color}">${logEntry}</span>`;
      consoleEl.scrollTop = consoleEl.scrollHeight;
      console.log(message);
    }
    
    function updateStatus(message, type = 'secondary') {
      statusEl.className = `alert alert-${type}`;
      statusEl.textContent = `Status: ${message}`;
    }
    
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

    try {
      // Initialize Firebase
      log('Initializing Firebase...', 'info');
      firebase.initializeApp(firebaseConfig);
      const auth = firebase.auth();
      log('Firebase initialized successfully', 'success');
      updateStatus('Firebase initialized', 'success');
      
      // Auth state observer
      auth.onAuthStateChanged((user) => {
        if (user) {
          log(`User signed in: ${user.email}`, 'success');
          updateStatus('User signed in', 'success');
          userInfoEl.textContent = JSON.stringify({
            uid: user.uid,
            email: user.email,
            displayName: user.displayName,
            photoURL: user.photoURL
          }, null, 2);
        } else {
          log('No user signed in', 'info');
          updateStatus('No user signed in', 'warning');
          userInfoEl.textContent = 'Not signed in';
        }
      });
      
      // Test Google Sign-In
      document.getElementById('testBtn').addEventListener('click', async () => {
        try {
          log('Creating Google Auth Provider...', 'info');
          const provider = new firebase.auth.GoogleAuthProvider();
          provider.addScope('profile');
          provider.addScope('email');
          
          log('Opening Google sign-in popup...', 'info');
          updateStatus('Opening popup...', 'info');
          
          const result = await auth.signInWithPopup(provider);
          
          log('Sign-in successful!', 'success');
          log(`User: ${result.user.email}`, 'success');
          log(`UID: ${result.user.uid}`, 'success');
          updateStatus('Sign-in successful', 'success');
          
          // Test backend
          log('Testing backend API...', 'info');
          const response = await fetch('firebase_login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              uid: result.user.uid,
              name: result.user.displayName,
              email: result.user.email,
              photo: result.user.photoURL
            })
          });
          
          const data = await response.json();
          log(`Backend response: ${JSON.stringify(data)}`, data.success ? 'success' : 'error');
          
        } catch (error) {
          log(`Error: ${error.code} - ${error.message}`, 'error');
          updateStatus(`Error: ${error.code}`, 'danger');
          
          if (error.code === 'auth/popup-blocked') {
            alert('Popup was blocked! Please allow popups for this site.');
          }
        }
      });
      
      // Check auth state
      document.getElementById('checkAuthBtn').addEventListener('click', () => {
        const user = auth.currentUser;
        if (user) {
          log(`Current user: ${user.email}`, 'info');
          userInfoEl.textContent = JSON.stringify(user.toJSON(), null, 2);
        } else {
          log('No user currently signed in', 'warning');
        }
      });
      
      // Sign out
      document.getElementById('signOutBtn').addEventListener('click', async () => {
        try {
          await auth.signOut();
          log('Signed out successfully', 'success');
          updateStatus('Signed out', 'warning');
        } catch (error) {
          log(`Sign out error: ${error.message}`, 'error');
        }
      });
      
    } catch (error) {
      log(`Initialization error: ${error.message}`, 'error');
      updateStatus('Initialization failed', 'danger');
    }
  </script>
</body>
</html>