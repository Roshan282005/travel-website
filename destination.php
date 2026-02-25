<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TravelGo Ultimate Map + AI Assistant</title>
  <link rel="icon" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png">

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

  <!-- Google Maps API - Latest Version with Dynamic Library Loading -->
  <script>
    (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
      key: "AIzaSyDemo_key_replace_with_yours",
      v: "weekly",
      region: "US",
      language: "en"
    });
  </script>

  <style>
    :root{
      --bg-1:#0f172a;
      --accent:#0078ff;
      --glass: rgba(255,255,255,0.08);
      --card: rgba(255,255,255,0.88);
      --muted: #94a3b8;
      --glass-border: rgba(255,255,255,0.06);
      --glass-strong: rgba(255,255,255,0.12);
      --radius:16px;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body {
      font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
      margin: 0; padding: 0;
      background: radial-gradient(1200px 600px at 10% 10%, rgba(0,120,255,0.07), transparent),
                  radial-gradient(1000px 500px at 90% 90%, rgba(0,200,160,0.04), transparent),
                  linear-gradient(180deg,#f5fbff 0%, #eef9ff 40%, #f2fbf9 100%);
      color: #0b1220;
      -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
      padding-bottom:40px;
    }

    /* Search box - floating glass */
    .search-box{max-width:820px;margin:90px auto 18px;display:flex;align-items:center;gap:8px;padding:14px;background:linear-gradient(180deg,rgba(255,255,255,0.75),rgba(255,255,255,0.65));border-radius:26px;box-shadow:0 12px 40px rgba(16,24,40,0.08);backdrop-filter: blur(6px);border:1px solid var(--glass-border)}
    .search-box input{flex:1;padding:14px 16px;border-radius:18px;border:none;outline:none;font-size:16px;background:transparent}
    .search-box button{padding:12px 18px;border-radius:14px;background:linear-gradient(90deg,var(--accent),#00b0ff);color:#fff;border:none;font-weight:600;box-shadow:0 6px 20px rgba(3,102,214,0.16);cursor:pointer;transition:transform .18s ease,box-shadow .18s}
    .search-box button:hover{transform:translateY(-3px);box-shadow:0 18px 40px rgba(3,102,214,0.12)}

    /* Google Map Container */
    .map-container{max-width:1200px;margin:20px auto;border-radius:22px;overflow:hidden;position:relative;box-shadow:0 30px 80px rgba(10,20,40,0.12)}
    #map{height:600px;width:100%;transition:transform .6s cubic-bezier(.2,.9,.2,1);border-radius:10px;position:relative}
    .map-overlay{position:absolute;inset:0;background:linear-gradient(180deg,transparent,rgba(10,20,40,0.02));pointer-events:none}
    .distance-info{position:absolute;bottom:15px;left:15px;background:rgba(0,0,0,0.7);color:white;padding:12px 16px;border-radius:8px;font-weight:600;z-index:10;backdrop-filter:blur(10px)}

    /* POI filters */
    #poi-filters{max-width:1000px;margin:14px auto;text-align:center;display:none}
    #poi-filters .btn{margin:6px;background:#fff;border:1px solid var(--accent);color:var(--accent);box-shadow:0 8px 24px rgba(3,102,214,0.06)}
    #poi-filters .btn.active{background:var(--accent);color:#fff;transform:translateY(-3px)}

    /* Current location button - pulse */
    #currentLocationBtn{position:absolute;top:18px;left:18px;z-index:1200;padding:10px 12px;background:linear-gradient(90deg,var(--accent),#00b0ff);color:#fff;border:none;border-radius:14px;cursor:pointer;box-shadow:0 10px 30px rgba(3,102,214,0.16);animation:btnPulse 3s infinite}
    @keyframes btnPulse{0%{transform:scale(1)}50%{transform:scale(1.03)}100%{transform:scale(1)}}

    /* Chat */
    .chat-container{max-width:1200px;margin:28px auto;background:linear-gradient(180deg,#fff,#f8fbff);border-radius:20px;box-shadow:0 30px 80px rgba(11,18,32,0.08);display:flex;flex-direction:column;height:650px;overflow:hidden;border:1px solid rgba(0,120,255,0.1)}
    .chat-header{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:linear-gradient(90deg,#0078ff,#00b0ff);color:#fff;box-shadow:0 4px 12px rgba(0,120,255,0.15);flex-shrink:0}
    .chat-header h5{margin:0;font-weight:700;letter-spacing:.2px;font-size:18px}
    .chat-messages{flex:1;padding:16px 20px;overflow-y:auto;overflow-x:hidden;display:flex;flex-direction:column;gap:12px;background:#fafcff;min-height:0;scroll-behavior:smooth}
    .chat-messages::-webkit-scrollbar{width:8px}
    .chat-messages::-webkit-scrollbar-track{background:#f0f4ff}
    .chat-messages::-webkit-scrollbar-thumb{background:rgba(0,120,255,0.3);border-radius:4px}
    .chat-messages::-webkit-scrollbar-thumb:hover{background:rgba(0,120,255,0.5)}
    .message{max-width:78%;padding:13px 16px;border-radius:14px;line-height:1.5;animation:msgIn .32s cubic-bezier(.2,.9,.2,1);word-wrap:break-word;font-size:14px}
    .user-message{align-self:flex-end;background:linear-gradient(135deg,#0078ff 0%,#0066cc 100%);color:#fff;box-shadow:0 8px 24px rgba(0,120,255,0.2);font-weight:500;text-shadow:0 1px 2px rgba(0,0,0,0.1)}
    .user-message strong{color:#ffff00;font-weight:700}
    .user-message em{color:#e6f0ff}
    .bot-message{align-self:flex-start;background:#fff;color:#0b1220;border:2px solid rgba(0,120,255,0.2);box-shadow:0 6px 16px rgba(0,120,255,0.1)}
    .bot-message strong{color:#0056cc;font-weight:700}
    .bot-message em{color:#0078ff;font-style:italic}
    .bot-message code{background:#f0f4ff;color:#0056cc;padding:3px 8px;border-radius:6px;font-family:'Courier New',monospace;font-size:12px;border:1px solid rgba(0,120,255,0.15)}
    .bot-message a{color:#0078ff;text-decoration:none;font-weight:500}
    .bot-message a:hover{text-decoration:underline;color:#0056cc}
    @keyframes msgIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
    .chat-input{display:flex;padding:14px;border-top:1px solid rgba(0,120,255,0.1);gap:10px;background:#fff}
    .chat-input textarea{flex:1;border-radius:12px;padding:10px 12px;border:1px solid rgba(0,120,255,0.2);min-height:50px;resize:none;font-family:inherit;font-size:14px;color:#0b1220}
    .chat-input textarea::placeholder{color:#7a8fa6}
    .chat-input textarea:focus{outline:none;border-color:#0078ff;box-shadow:0 0 8px rgba(0,120,255,0.2)}
    .chat-input button{background:linear-gradient(90deg,#0078ff,#00b0ff);border:none;color:#fff;border-radius:12px;padding:10px 14px;cursor:pointer;font-weight:600;transition:all .2s;box-shadow:0 4px 12px rgba(0,120,255,0.15)}
    .chat-input button:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,120,255,0.25)}
    .chat-input button:active{transform:translateY(0)}

    .typing{font-style:italic;color:#0078ff;padding:10px 12px;background:linear-gradient(90deg,rgba(0,120,255,0.1),rgba(0,176,255,0.1));border-left:3px solid #0078ff;border-radius:8px;margin:4px 0;font-weight:500;animation:typingPulse 1.5s infinite}


    /* Quick prompts */
    .quick{background:linear-gradient(180deg,#fff,#f0f6ff);border:1px solid rgba(0,120,255,0.2);color:#0b1220;padding:8px 12px;border-radius:10px;cursor:pointer;transition:all .18s ease;font-size:13px;font-weight:500;box-shadow:0 2px 8px rgba(0,120,255,0.08)}
    .quick:hover{transform:translateY(-6px);box-shadow:0 12px 24px rgba(0,120,255,0.15);background:linear-gradient(180deg,#f0f6ff,#e6f0ff);border-color:rgba(0,120,255,0.4)}
    .quick:active{transform:translateY(-4px)}

    /* small utilities */
    .stat-box{font-size:22px;font-weight:700;animation:counterUp .6s ease both}

    /* dark mode */
    body.dark-mode{background:#061126;color:#e6eef8}
    body.dark-mode .chat-container, body.dark-mode .search-box{background:linear-gradient(180deg,rgba(255,255,255,0.03),rgba(255,255,255,0.02));border:1px solid rgba(255,255,255,0.04)}
    body.dark-mode .search-box input, body.dark-mode .chat-input textarea{background:#16213e;color:#fff}
    
    /* Dark mode chat specific styles */
    body.dark-mode .chat-header{background:linear-gradient(90deg,#1a3a7a,#0066cc);color:#fff}
    body.dark-mode .chat-messages{background:#0a1628;color:#e6eef8}
    body.dark-mode .chat-input{border-top:1px solid rgba(255,255,255,0.08);background:#0a1628}
    body.dark-mode .chat-input textarea{background:#16213e;color:#e6eef8;border:1px solid rgba(255,255,255,0.1)}
    body.dark-mode .chat-input textarea::placeholder{color:#7a8fa6}
    body.dark-mode .chat-input button{background:linear-gradient(90deg,#0078ff,#0056cc);color:#fff;box-shadow:0 6px 20px rgba(0,120,255,0.2)}
    body.dark-mode .chat-input button:hover{box-shadow:0 10px 30px rgba(0,120,255,0.3)}
    
    /* Dark mode message styles - improved contrast */
    body.dark-mode .user-message{background:linear-gradient(135deg,#0066cc 0%,#004899 100%);color:#fff;box-shadow:0 8px 24px rgba(0,120,255,0.25);text-shadow:0 1px 2px rgba(0,0,0,0.2);font-weight:500}
    body.dark-mode .user-message strong{color:#ffff99;font-weight:700}
    body.dark-mode .user-message em{color:#b3d9ff}
    body.dark-mode .bot-message{background:linear-gradient(135deg,#1a3a5a 0%,#0f1f2e 100%);color:#e6eef8;border:2px solid rgba(0,176,255,0.3);box-shadow:0 6px 20px rgba(0,120,255,0.15)}
    body.dark-mode .bot-message a{color:#00ffcc;text-decoration:underline;font-weight:500}
    body.dark-mode .bot-message a:hover{color:#00ffff;text-decoration:underline double}
    body.dark-mode .bot-message strong{color:#00ffff;font-weight:700}
    body.dark-mode .bot-message em{color:#00d4ff;font-style:italic}
    body.dark-mode .bot-message code{background:#0a1f3a;color:#00ff88;padding:3px 8px;border-radius:6px;font-family:'Courier New',monospace;font-size:12px;border:1px solid rgba(0,255,136,0.2)}
    
    /* Dark mode typing indicator */
    body.dark-mode .typing{color:#00d4ff;background:linear-gradient(90deg,rgba(0,120,255,0.15),rgba(0,176,255,0.15));border-left:3px solid #00d4ff;padding:10px 12px;border-radius:8px;margin:4px 0;font-weight:500;animation:typingPulse 1.5s infinite}
    
    /* Dark mode quick prompts */
    body.dark-mode .quick{background:linear-gradient(180deg,#1a2438,#0f1f2e);border:1px solid rgba(0,120,255,0.2);color:#e6eef8;box-shadow:0 4px 12px rgba(0,0,0,0.3)}
    body.dark-mode .quick:hover{background:linear-gradient(180deg,#1f3050,#152a3f);transform:translateY(-6px);box-shadow:0 12px 24px rgba(0,120,255,0.2)}
    
    /* Dark mode scrollbar */
    body.dark-mode .chat-messages::-webkit-scrollbar{width:8px}
    body.dark-mode .chat-messages::-webkit-scrollbar-track{background:#0a1628}
    body.dark-mode .chat-messages::-webkit-scrollbar-thumb{background:rgba(0,176,255,0.4);border-radius:4px}
    body.dark-mode .chat-messages::-webkit-scrollbar-thumb:hover{background:rgba(0,176,255,0.6)}

    /* Additional animations */
    @keyframes counterUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(8px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes typingPulse {
      0%, 100% {
        opacity: 1;
      }
      50% {
        opacity: 0.6;
      }
    }

    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
        box-shadow: 0 4px 12px rgba(0,120,255,0.2);
      }
      50% {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(0,120,255,0.4);
      }
    }

    @keyframes voiceWave {
      0%, 100% {
        transform: scaleY(0.5);
      }
      50% {
        transform: scaleY(1);
      }
    }

    @keyframes siriGlow {
      0% {
        box-shadow: 0 0 10px rgba(0,120,255,0.2);
      }
      50% {
        box-shadow: 0 0 20px rgba(0,120,255,0.4);
      }
      100% {
        box-shadow: 0 0 10px rgba(0,120,255,0.2);
      }
    }

    .footer {
      background: #222;
      color: #ccccccbb;
      padding: 25px 0;
      text-align: center;
    }

    .footer a {
      color: #fff;
      margin: 0 10px;
      transition: 0.3s;
    }

    .footer a:hover {
      color: hsla(15, 100%, 50%, 1.00);
      transform: scale(1.2);
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <!-- Destination Search -->
  <div class="search-box">
    <input type="text" id="destinationInput" placeholder="Search destination...">
    <button onclick="searchDestination()">Search</button>
  </div>

  <!-- POI Filters -->
  <div id="poi-filters">
    <button class="btn btn-outline-primary" onclick="filterPOIs('restaurant')">Restaurants</button>
    <button class="btn btn-outline-primary" onclick="filterPOIs('hotel')">Hotels</button>
    <button class="btn btn-outline-primary" onclick="filterPOIs('attraction')">Attractions</button>
    <div id="restaurant-filters">
      <button class="btn btn-sm btn-outline-secondary" onclick="filterByCuisine('italian')">Italian</button>
      <button class="btn btn-sm btn-outline-secondary" onclick="filterByCuisine('mexican')">Mexican</button>
      <button class="btn btn-sm btn-outline-secondary" onclick="filterByCuisine('indian')">Indian</button>
    </div>
  </div>

  <!-- Map -->
  <div class="map-container">
    <button id="currentLocationBtn" onclick="goToCurrentLocation()">📍 Current Location</button>
    <div id="map"></div>
  </div>

  <!-- AI Chat Assistant -->
  <div class="chat-container">
    <div class="chat-header">
      <div style="display:flex;align-items:center;gap:12px;">
        <div id="lottieSneha" style="width:48px;height:48px"></div>
        <h5 style="margin:0">💬 TravelGo AI Assistant (Sneha)</h5>
      </div>
      <div style="display:flex;align-items:center;gap:8px;">
        <select id="languageSelect" onchange="onPrefChange()" title="Select language" style="border-radius:8px;padding:6px;">
          <option value="en">EN</option>
          <option value="es">ES</option>
          <option value="fr">FR</option>
        </select>
        <button onclick="toggleDark()" title="Toggle dark mode">🌙</button>
        <button onclick="clearChat()" title="Clear chat">🗑️</button>
      </div>
    </div>

    <div style="display:flex;gap:12px;flex:1;min-height:0;overflow:hidden;">
      <div style="flex:1;display:flex;flex-direction:column;min-width:0;">
        <!-- Chat Messages Area -->
        <div class="chat-messages" id="chat-messages">
          <div class="message bot-message">
            👋 <strong>Hi! I'm Sneha</strong>, your AI travel assistant.<br>
            I can help you with:<br>
            🗺️ <strong>Routes & Directions</strong><br>
            🏨 <strong>Hotels & Accommodations</strong><br>
            📅 <strong>Trip Itineraries</strong><br>
            💬 <strong>Local Tips & Advice</strong><br><br>
            Try asking me anything or use the quick prompts on the right! 🎤
          </div>
        </div>

        <!-- Chat Input Area -->
        <div class="chat-input">
          <textarea id="userInput" placeholder="Type your question here... or click 🎤 to speak" spellcheck="true"></textarea>
          <button onclick="sendMessage()" title="Send message">📤</button>
          <button id="voiceBtn" title="Voice input">🎤</button>
        </div>
      </div>

      <!-- Right Sidebar -->
      <div style="width:280px;flex-shrink:0;display:flex;flex-direction:column;gap:10px;overflow-y:auto;padding-right:8px;">
        <!-- Quick Prompts -->
        <div style="background:#fff;padding:12px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);flex-shrink:0;">
          <strong style="color:#0078ff;display:block;margin-bottom:8px;">⚡ Quick Prompts</strong>
          <div style="display:flex;flex-wrap:wrap;gap:6px;">
            <button class="quick" onclick="quickPrompt('Show route to the selected destination')" title="Get route">🗺️ Route</button>
            <button class="quick" onclick="quickPrompt('Estimate travel time to the destination')" title="Travel time">⏱️ ETA</button>
            <button class="quick" onclick="quickPrompt('Recommend 3 hotels near destination')" title="Hotels">🏨 Hotels</button>
            <button class="quick" onclick="quickPrompt('Create a 3-day itinerary for this place')" title="Plan trip">📅 Plan</button>
            <button class="quick" onclick="quickPrompt('Find family friendly attractions')" title="Attractions">🎢 Fun</button>
            <button class="quick" onclick="quickPrompt('Save this destination to my wishlist')" title="Save">❤️ Save</button>
          </div>
        </div>

        <!-- Context Actions -->
        <div style="background:#fff;padding:12px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);flex-shrink:0;">
          <strong style="color:#0078ff;display:block;margin-bottom:8px;">🔧 Actions</strong>
          <div style="display:flex;flex-direction:column;gap:8px;">
            <button class="quick" onclick="traceLastRoute()" style="width:100%;text-align:left;" title="Show last route">↩️ Last Route</button>
            <button class="quick" onclick="showNearby('restaurant')" style="width:100%;text-align:left;" title="Nearby food">🍽️ Restaurants</button>
            <button class="quick" onclick="translateLast('en')" style="width:100%;text-align:left;" title="Translate">🌐 Translate</button>
          </div>
        </div>
      </div>
    </div>

        <div style="background:#fff;padding:10px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
          <strong>Tips</strong>
          <p style="font-size:12px;margin:6px 0 0;">Try asking: "What should I pack?" or "Cheapest time to visit?"</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Google Maps is loaded via API tag above -->

  <script>
    // ===== Modern Google Maps API v3 Setup (Latest) =====
    let userLat = 40.7128, userLon = -74.0060; // NYC Default
    let map, directionsService, directionsDisplay;
    let userMarker, destinationMarker, nearbyMarkers = [];
    let isMapInitialized = false;

    // Check if Google Maps API is loaded
    function checkGoogleMapsLoaded() {
      if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
        console.error('❌ Google Maps API not loaded. Please add a valid API key.');
        showMapError();
        return false;
      }
      return true;
    }

    // Show error message if Maps not loaded
    function showMapError() {
      const mapDiv = document.getElementById('map');
      if (mapDiv) {
        mapDiv.innerHTML = `
          <div style="display:flex;align-items:center;justify-content:center;height:100%;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;text-align:center;flex-direction:column;padding:20px">
            <div style="font-size:48px;margin-bottom:20px">🗺️</div>
            <h2>Google Maps Setup Required</h2>
            <p style="max-width:400px;margin:10px 0">
              <strong>Step 1:</strong> Get free API key from<br>
              <a href="https://console.cloud.google.com/" target="_blank" style="color:#fff;text-decoration:underline">Google Cloud Console</a>
            </p>
            <p style="max-width:400px;margin:10px 0">
              <strong>Step 2:</strong> Enable these APIs:<br>
              Maps JavaScript API | Places API | Directions API | Geocoding API
            </p>
            <p style="max-width:400px;margin:10px 0">
              <strong>Step 3:</strong> Edit <code style="background:rgba(0,0,0,0.3);padding:5px 10px;border-radius:4px">destination.php</code> line 14<br>
              Replace <code>AIzaSyDemo_key_replace_with_yours</code> with your API key
            </p>
            <p style="margin-top:20px;font-size:12px">Then refresh the page. Questions? See console logs.</p>
          </div>
        `;
      }
    }

    // Modern async map initialization
    async function initializeGoogleMap() {
      if (isMapInitialized) return;
      if (!checkGoogleMapsLoaded()) return;
      
      try {
        // Dynamically load required libraries
        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        const { Geocoder } = await google.maps.importLibrary("geocoding");
        const { PlacesService } = await google.maps.importLibrary("places");
        
        const mapOptions = {
          zoom: 13,
          center: {lat: userLat, lng: userLon},
          mapTypeControl: true,
          fullscreenControl: true,
          zoomControl: true,
          streetViewControl: true,
          mapId: "TRAVELGO_MAP_ID",
          styles: [
            {"featureType": "all", "elementType": "labels.text.fill", "stylers": [{"color": "#666666"}]},
            {"featureType": "water", "elementType": "geometry.fill", "stylers": [{"color": "#c7eff4"}]},
            {"featureType": "transit", "elementType": "labels.text.fill", "stylers": [{"color": "#0078ff"}]}
          ]
        };
        
        map = new Map(document.getElementById('map'), mapOptions);
        directionsService = new google.maps.DirectionsService();
        directionsDisplay = new google.maps.DirectionsRenderer({map: map, suppressMarkers: false});
        
        // Add user location marker with Advanced Marker Element
        const userMarkerElement = document.createElement('div');
        userMarkerElement.innerHTML = `
          <div style="width:40px;height:40px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;box-shadow:0 4px 12px rgba(0,0,0,0.3);border:3px solid white">📍</div>
        `;
        
        userMarker = new AdvancedMarkerElement({
          map: map,
          position: {lat: userLat, lng: userLon},
          title: 'Your Location',
          content: userMarkerElement
        });
        
        userMarker.addListener('click', () => {
          const infoWindow = new google.maps.InfoWindow({
            content: '<div style="text-align:center;padding:10px"><strong>📍 Your Location</strong><br>NYC</div>'
          });
          infoWindow.open(map, userMarker);
        });
        
        // Add distance display in controls
        const distanceDiv = document.createElement('div');
        distanceDiv.id = 'distance-info';
        distanceDiv.className = 'distance-info';
        distanceDiv.style.display = 'none';
        map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(distanceDiv);
        
        isMapInitialized = true;
        console.log('✅ Google Maps v3 Initialized | Libraries: Maps, Marker, Geocoding, Places');
      } catch(e) {
        console.error('Map initialization error:', e);
        showMapError();
      }
    }

    // Auto-initialize map on page load
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => { 
        if (checkGoogleMapsLoaded()) {
          initializeGoogleMap();
        }
      }, 800);
    });

    // ===== Current Location =====
    function goToCurrentLocation() {
      if (!map) initializeGoogleMap();
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
          userLat = pos.coords.latitude;
          userLon = pos.coords.longitude;
          map.setCenter({lat: userLat, lng: userLon});
          map.setZoom(13);
          userMarker.setPosition({lat: userLat, lng: userLon});
          addMessage("🌍 Moved to your current location", "bot");
          speakText("Moved to your current location.");
        });
      } else {
        alert("Geolocation not supported.");
      }
    }

    // ===== Destination Search (Modern Google Geocoding API v3) =====
    async function searchDestination() {
      const dest = document.getElementById("destinationInput").value.trim();
      if (!dest) {
        alert("Enter a destination!");
        return;
      }

      if (!map) await initializeGoogleMap();

      try {
        // Try local database first for better custom data
        const res = await fetch(`search.php?q=${encodeURIComponent(dest)}`);
        const dbData = await res.json();

        let latNum, lonNum, displayName, imageUrl;
        
        if (dbData.status === "success" && dbData.count > 0) {
          const first = dbData.results[0];
          latNum = parseFloat(first.lat) || userLat;
          lonNum = parseFloat(first.lon) || userLon;
          displayName = first.country + " - " + first.description;
          imageUrl = first.image || `https://source.unsplash.com/400x300/?${encodeURIComponent(dest)}`;
          await processDestination();
        } else {
          // Use Google Geocoding API v3 with importLibrary for highest accuracy
          try {
            const { Geocoder } = await google.maps.importLibrary("geocoding");
            const geocoder = new Geocoder();
            
            const result = await geocoder.geocode({address: dest});
            if (result.results && result.results.length > 0) {
              const loc = result.results[0].geometry.location;
              latNum = loc.lat();
              lonNum = loc.lng();
              displayName = result.results[0].formatted_address;
              imageUrl = `https://source.unsplash.com/400x300/?${encodeURIComponent(dest)}`;
              await processDestination();
            } else {
              alert("❌ Destination not found on Google Maps. Try a different search.");
            }
          } catch (geocodeError) {
            console.error("Geocoding error:", geocodeError);
            alert("Could not geocode destination");
          }
        }
        
        async function processDestination() {
          // Remove old marker
          if (destinationMarker) destinationMarker.setMap(null);
          
          // Load Advanced Marker library for modern markers
          const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
          
          // Create custom marker with gradient
          const markerElement = document.createElement('div');
          markerElement.innerHTML = `
            <div style="width:45px;height:45px;background:linear-gradient(135deg,#ff6b6b 0%,#ee5a6f 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:22px;box-shadow:0 4px 15px rgba(255,107,107,0.4);border:3px solid white;cursor:pointer;animation:pulse 2s infinite">📍</div>
          `;
          
          destinationMarker = new AdvancedMarkerElement({
            map: map,
            position: {lat: latNum, lng: lonNum},
            title: displayName,
            content: markerElement
          });
          
          // Info window with image
          const infoWindow = new google.maps.InfoWindow({
            content: `<div style="width:200px;text-align:center;"><h6 style="margin:8px 0">📍 ${displayName}</h6><img src="${imageUrl}" style="width:100%;height:120px;object-fit:cover;border-radius:8px;"></div>`
          });
          
          markerElement.addEventListener('click', () => {
            infoWindow.open(map, destinationMarker);
          });
          infoWindow.open(map, destinationMarker);
          
          // Pan and zoom smoothly
          map.panTo({lat: latNum, lng: lonNum});
          map.setZoom(14);
          
          // Get turn-by-turn directions
          const request = {
            origin: {lat: userLat, lng: userLon},
            destination: {lat: latNum, lng: lonNum},
            travelMode: google.maps.TravelMode.DRIVING,
            avoidFerries: false,
            avoidHighways: false,
            avoidTolls: false
          };
          
          directionsService.route(request, (result, status) => {
            if (status === google.maps.DirectionsStatus.OK) {
              directionsDisplay.setDirections(result);
              const leg = result.routes[0].legs[0];
              const distanceText = leg.distance.text;
              const durationText = leg.duration.text;
              
              // Show distance info on map control
              const distDiv = document.getElementById('distance-info');
              if (distDiv) {
                distDiv.innerHTML = `📍 ${distanceText} | ⏱️ ${durationText}`;
                distDiv.style.display = 'block';
              }
              
              addMessage(`✅ Route to <strong>${displayName}</strong> plotted! Distance: <strong>${distanceText}</strong> | Time: <strong>${durationText}</strong>`, 'bot');
              speakText(`Route to ${displayName} plotted. Distance: ${distanceText}.`);
            } else {
              console.error('Directions error:', status);
              addMessage('❌ Could not calculate route', 'bot');
            }
          });
          
          document.getElementById("poi-filters").style.display = "block";
        }
      } catch (err) {
        console.error("Search error:", err);
        addMessage('❌ Search error. Please try again.', 'bot');
      }
    }

    // ===== POI Filtering (Google Places API v3) =====
    async function filterPOIs(category) {
      if (!map) await initializeGoogleMap();
      
      // Clear old markers
      nearbyMarkers.forEach(m => m.setMap(null));
      nearbyMarkers = [];
      
      try {
        // Load marker library for advanced markers
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        
        const typeMap = {
          'restaurant': 'restaurant',
          'hotel': 'lodging',
          'museum': 'museum',
          'park': 'park',
          'cafe': 'cafe',
          'hospital': 'hospital',
          'police': 'police',
          'attraction': 'tourist_attraction'
        };
        
        const placeType = typeMap[category] || 'restaurant';
        const emojiMap = {restaurant: '🍽️', lodging: '🏨', museum: '🏛️', park: '🌳', cafe: '☕', hospital: '🏥', police: '🚔', tourist_attraction: '✨'};
        
        const service = new google.maps.PlacesService(map);
        const request = {
          location: {lat: userLat, lng: userLon},
          radius: 2500,
          type: placeType
        };
        
        service.nearbySearch(request, async (results, status) => {
          if (status === google.maps.places.PlacesServiceStatus.OK && results) {
            for (const place of results.slice(0, 20)) {
              // Create custom marker element
              const markerElement = document.createElement('div');
              markerElement.innerHTML = `
                <div style="width:35px;height:35px;background:linear-gradient(135deg,#FFD93D 0%,#FFC93D 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#333;font-size:18px;box-shadow:0 3px 10px rgba(255,217,61,0.4);border:2px solid white;cursor:pointer">${emojiMap[placeType]}</div>
              `;
              
              const marker = new AdvancedMarkerElement({
                map: map,
                position: place.geometry.location,
                title: place.name,
                content: markerElement
              });
              
              const distance = google.maps.geometry.spherical.computeDistanceBetween(
                new google.maps.LatLng(userLat, userLon),
                place.geometry.location
              );
              const distKm = (distance / 1000).toFixed(2);
              
              const infoWindow = new google.maps.InfoWindow({
                content: `<div style="text-align:center;padding:8px;max-width:180px;font-size:12px">
                  <strong>${emojiMap[placeType]} ${place.name}</strong>
                  <br><small>${place.vicinity}</small>
                  <br>⭐ ${place.rating || 'N/A'} | 📍 ${distKm}km
                </div>`
              });
              
              markerElement.addEventListener('click', () => {
                infoWindow.open(map, marker);
              });
              
              nearbyMarkers.push(marker);
            }
            addMessage(`✅ Showing ${Math.min(20, results.length)} nearby ${category}s!`, 'bot');
            speakText(`Showing ${category}s near you.`);
          } else {
            addMessage(`❌ Could not find ${category}s nearby.`, 'bot');
          }
        });
      } catch (err) {
        console.error('POI filtering error:', err);
        addMessage(`❌ Error finding ${category}s.`, 'bot');
      }
    }

    function filterByCuisine(cuisine) {
      addMessage(`🔍 Filtering by ${cuisine} cuisine...`, "bot");
    }

    // ===== Chat Assistant =====
    const messagesContainer = document.getElementById("chat-messages");
    const inputField = document.getElementById("userInput");
    let conversation = [];
    let isCurrentlySpeaking = false;

    // ===== Futuristic Siri-like Voice Handler =====
    function speakText(text) {
      if (!('speechSynthesis' in window)) {
        console.warn('Speech Synthesis not supported');
        return;
      }

      // Stop any ongoing speech
      if (isCurrentlySpeaking) {
        speechSynthesis.cancel();
      }

      // Create and configure utterance with Siri-like parameters
      const utter = new SpeechSynthesisUtterance(text);
      utter.lang = 'en-US';
      utter.pitch = 1.15;      // Slightly higher pitch for Siri-like voice
      utter.rate = 0.95;       // Natural speaking rate
      utter.volume = 1.0;      // Full volume

      // Get available voices and select a female voice if available
      const voices = speechSynthesis.getVoices();
      let selectedVoice = null;

      // Prefer Google, Siri, or Samantha voices
      const voicePreferences = ['Google', 'Siri', 'Samantha', 'Victoria', 'Moira'];
      for (let preference of voicePreferences) {
        selectedVoice = voices.find(v => v.name.includes(preference));
        if (selectedVoice) break;
      }

      // Fallback to any female voice
      if (!selectedVoice) {
        selectedVoice = voices.find(v => v.name.includes('female') || v.name.includes('Female'));
      }

      // Use the first voice if no preference found
      if (!selectedVoice && voices.length > 0) {
        selectedVoice = voices[0];
      }

      if (selectedVoice) {
        utter.voice = selectedVoice;
      }

      // Add visual feedback
      isCurrentlySpeaking = true;
      showVoiceIndicator('playing');

      // Handle speech events
      utter.onstart = () => {
        console.log('🎤 Siri Voice: Speaking...');
        showVoiceIndicator('playing');
      };

      utter.onend = () => {
        console.log('🎤 Siri Voice: Complete');
        isCurrentlySpeaking = false;
        showVoiceIndicator('idle');
      };

      utter.onerror = (event) => {
        console.error('🎤 Siri Voice Error:', event.error);
        isCurrentlySpeaking = false;
        showVoiceIndicator('error');
      };

      // Speak with futuristic effect
      speechSynthesis.cancel();
      speechSynthesis.speak(utter);
    }

    // Visual feedback indicator for voice
    function showVoiceIndicator(state) {
      let indicator = document.getElementById('voice-indicator');
      if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'voice-indicator';
        indicator.style.cssText = `
          position: fixed;
          bottom: 30px;
          right: 30px;
          z-index: 9999;
          padding: 12px 16px;
          border-radius: 30px;
          font-size: 13px;
          font-weight: 600;
          display: flex;
          align-items: center;
          gap: 8px;
          transition: all 0.3s ease;
          backdrop-filter: blur(10px);
          border: 1.5px solid rgba(255,255,255,0.3);
        `;
        document.body.appendChild(indicator);
      }

      if (state === 'playing') {
        indicator.innerHTML = '<span style="display:inline-block; width:10px; height:10px; background:#fff; border-radius:50%; animation:pulse 1s infinite"></span> 🔊 Siri is Speaking...';
        indicator.style.background = 'rgba(0, 150, 255, 0.9)';
        indicator.style.color = '#fff';
        indicator.style.boxShadow = '0 8px 32px rgba(0, 150, 255, 0.4)';
        indicator.style.opacity = '1';
        indicator.style.pointerEvents = 'auto';
      } else if (state === 'listening') {
        indicator.innerHTML = '<span style="display:inline-block; width:10px; height:10px; background:#fff; border-radius:50%; animation:pulse 0.8s infinite"></span> 🎤 Listening...';
        indicator.style.background = 'rgba(76, 175, 80, 0.9)';
        indicator.style.color = '#fff';
        indicator.style.boxShadow = '0 8px 32px rgba(76, 175, 80, 0.4)';
        indicator.style.opacity = '1';
        indicator.style.pointerEvents = 'auto';
      } else if (state === 'error') {
        indicator.innerHTML = '⚠️ Voice Error';
        indicator.style.background = 'rgba(255, 50, 50, 0.9)';
        indicator.style.color = '#fff';
        indicator.style.boxShadow = '0 8px 32px rgba(255, 50, 50, 0.4)';
        indicator.style.opacity = '1';
        setTimeout(() => {
          indicator.style.opacity = '0';
          indicator.style.pointerEvents = 'none';
        }, 2000);
      } else if (state === 'idle') {
        indicator.style.opacity = '0';
        indicator.style.pointerEvents = 'none';
      }
    }
    async function sendMessage() {
      const text = inputField.value.trim();
      if (!text) {
        addMessage("📝 Please enter a message to get started!", "bot");
        return;
      }
      
      // Disable input while processing
      inputField.disabled = true;
      const sendBtn = document.querySelector('[onclick="sendMessage()"]');
      if (sendBtn) sendBtn.disabled = true;
      
      // Add user message immediately (for instant feedback)
      addMessage(text, "user");
      conversation.push({
        role: "user",
        content: text
      });
      inputField.value = "";

      // Create typing indicator with better visibility
      const typing = document.createElement("div");
      typing.classList.add("typing");
      typing.innerHTML = "🤔 <em>Sneha is thinking...</em>";
      messagesContainer.appendChild(typing);
      
      // Scroll to bottom to show typing indicator
      setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      }, 0);

      try {
        const response = await fetch("chat.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            message: text,
            conversation
          })
        });
        
        if (!response.ok) {
          throw new Error(`Server error: ${response.status}`);
        }
        
        const responseText = await response.text();
        let data;
        
        try {
          data = JSON.parse(responseText);
        } catch (parseErr) {
          console.error('JSON parse error:', parseErr, 'Response:', responseText.substring(0, 100));
          throw new Error('Invalid server response');
        }
        
        typing.remove();
        
        if (data.reply) {
          addMessage(data.reply, "bot");
          // Only speak if voice is available and not too long
          if (data.voice && data.voice.length < 500) {
            speakText(data.voice);
          }
          conversation.push({ role: "assistant", content: data.reply });
          
          // Handle assistant actions with error checking
          if (data.action && typeof data.action === 'object') {
            try {
              await handleAssistantAction(data.action);
            } catch (actionErr) {
              console.error('Action error:', actionErr);
            }
          }
        } else {
          addMessage("⚠️ No response received from the assistant.", "bot");
        }
      } catch (err) {
        typing.remove();
        console.error('Chat error:', err);
        addMessage("⚠️ Chat error: " + err.message + ". Please try again.", "bot");
      } finally {
        // Re-enable input
        inputField.disabled = false;
        if (sendBtn) sendBtn.disabled = false;
        inputField.focus();
      }
    }

    function addMessage(text, sender) {
      if (!text) return;
      
      const msg = document.createElement("div");
      msg.classList.add("message", sender === "user" ? "user-message" : "bot-message");
      
      // Enhanced markdown formatting with better handling
      let formatted = String(text)
        .replace(/&/g, "&amp;")                                      // Escape &
        .replace(/</g, "&lt;")                                       // Escape <
        .replace(/>/g, "&gt;")                                       // Escape >
        .replace(/\*\*(.+?)\*\*/g, "<strong>$1</strong>")           // Bold
        .replace(/\*(.+?)\*/g, "<em>$1</em>")                       // Italic
        .replace(/`(.+?)`/g, "<code>$1</code>")                     // Code
        .replace(/\n/g, "<br>");                                     // Line breaks
      
      // Auto-linkify URLs
      formatted = formatted.replace(
        /(https?:\/\/[^\s<]+)/g, 
        '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
      );
      
      msg.innerHTML = formatted;
      
      // Ensure message container exists and is accessible
      if (!messagesContainer) {
        console.error('Messages container not found!');
        return;
      }
      
      messagesContainer.appendChild(msg);
      
      // Ensure smooth scrolling to latest message
      setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      }, 0);
      
      // Add subtle fade-in animation
      msg.style.opacity = '0';
      msg.style.animation = 'fadeInUp 0.4s ease-out forwards';
      
      // Log for debugging
      console.log(`Message added (${sender}): ${text.substring(0, 50)}...`);
    }

    function toggleDark() {
      document.body.classList.toggle("dark-mode");
    }

    function clearChat() {
      messagesContainer.innerHTML = "";
      conversation = [];
    }

    inputField.addEventListener("keypress", e => {
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });

    // ===== Futuristic Voice Recognition (Siri-like) =====
    const voiceBtn = document.getElementById("voiceBtn");
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      const recognition = new SpeechRecognition();
      recognition.lang = "en-US";
      recognition.continuous = false;
      recognition.interimResults = true;
      
      let isListening = false;

      voiceBtn.addEventListener("click", () => {
        if (!isListening) {
          recognition.start();
          isListening = true;
          voiceBtn.innerText = "🎙️ Listening...";
          voiceBtn.style.animation = 'pulse 1s infinite';
          showVoiceIndicator('playing');
          console.log('🎤 Voice Recognition: Listening started');
        } else {
          recognition.stop();
          isListening = false;
          voiceBtn.innerText = "🎤";
          voiceBtn.style.animation = 'none';
        }
      });

      recognition.onstart = () => {
        console.log('🎤 Voice Recognition: Ready to listen');
      };

      recognition.onresult = (e) => {
        let interimTranscript = '';
        for (let i = e.resultIndex; i < e.results.length; i++) {
          const transcript = e.results[i][0].transcript;
          if (e.results[i].isFinal) {
            // Final result
            console.log('🎤 Voice Recognition: Final -', transcript);
            inputField.value = transcript;
            addMessage(transcript, "user");
            setTimeout(() => sendMessage(), 100);
          } else {
            // Interim result
            interimTranscript += transcript;
          }
        }
        
        // Show interim results in input field
        if (interimTranscript) {
          inputField.value = interimTranscript;
          inputField.style.opacity = '0.7';
        }
      };

      recognition.onerror = (event) => {
        console.error('🎤 Voice Recognition Error:', event.error);
        showVoiceIndicator('error');
        voiceBtn.innerText = "⚠️ Error";
        voiceBtn.style.animation = 'none';
        setTimeout(() => {
          voiceBtn.innerText = "🎤";
        }, 2000);
      };

      recognition.onend = () => {
        console.log('🎤 Voice Recognition: Complete');
        isListening = false;
        voiceBtn.innerText = "🎤";
        voiceBtn.style.animation = 'none';
        inputField.style.opacity = '1';
        showVoiceIndicator('idle');
      };
    } else {
      voiceBtn.style.display = "none";
      console.warn('Speech Recognition not supported in this browser');
    }

    // ===== Advanced Chat Helpers =====
    const languageSelect = document.getElementById('languageSelect');

    function onPrefChange() {
      addMessage('Language set to ' + languageSelect.value.toUpperCase(), 'bot');
    }

    function quickPrompt(text) {
      inputField.value = text;
      sendMessage();
    }

    let lastRoute = null;
    function traceLastRoute() {
      if (!lastRoute) return addMessage('No route recorded yet.', 'bot');
      if (routeControl) map.removeControl(routeControl);
      routeControl = L.Routing.control({ waypoints: lastRoute, createMarker: () => null }).addTo(map);
      addMessage('Tracing last route on the map.', 'bot');
    }

    function showNearby(type) {
      filterPOIs(type);
    }

    function translateLast(lang) {
      // simple client-side demo: show a translated label
      addMessage('Translated to ' + lang + ': (demo) ' + (conversation.length? conversation[conversation.length-1].content : 'No message'), 'bot');
    }

    // Hook into route creation to record lastRoute
    const originalSearchDestination = searchDestination;
    searchDestination = async function() {
      await originalSearchDestination();
      if (routeControl && routeControl.getPlan) {
        try {
          const wps = routeControl.getWaypoints().map(w=>L.latLng(w.latLng.lat,w.latLng.lng));
          lastRoute = wps;
        } catch(e){}
      }
    }

    // ===== Assistant Action Handler with Enhanced Error Handling =====
    async function handleAssistantAction(action) {
      try {
        if (!action || typeof action !== 'object' || !action.type) {
          console.warn('Invalid action:', action);
          return;
        }
        
        const type = action.type;
        const payload = action.payload || {};
        
        console.log('Processing action:', type, payload);
        
        if (type === 'route') {
          const { lat, lon } = payload;
          if (lat && lon && !isNaN(lat) && !isNaN(lon)) {
            if (!map) initializeGoogleMap();
            
            // Remove old marker
            if (destinationMarker) destinationMarker.setMap(null);
            
            // Add destination marker
            destinationMarker = new google.maps.Marker({
              position: {lat: lat, lng: lon},
              map: map,
              title: 'Destination',
              icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
            });
            
            // Get directions using Google Directions API
            const request = {
              origin: {lat: userLat, lng: userLon},
              destination: {lat: lat, lng: lon},
              travelMode: google.maps.TravelMode.DRIVING
            };
            
            directionsService.route(request, (result, status) => {
              if (status === google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);
                const leg = result.routes[0].legs[0];
                const distanceText = leg.distance.text;
                const durationText = leg.duration.text;
                addMessage(`✅ Route plotted! Distance: <strong>${distanceText}</strong> | Duration: <strong>${durationText}</strong>`, 'bot');
                
                // Show distance info
                const distDiv = document.getElementById('distance-info');
                if (distDiv) {
                  distDiv.innerHTML = `📍 ${distanceText} | ⏱️ ${durationText}`;
                  distDiv.style.display = 'block';
                }
              } else {
                addMessage('⚠️ Could not calculate route.', 'bot');
              }
            });
            
            map.panTo({lat: lat, lng: lon});
          } else {
            addMessage('⚠️ Could not plot route - invalid coordinates.', 'bot');
          }
        } 
        else if (type === 'show_places') {
          const places = payload.places || [];
          if (Array.isArray(places) && places.length > 0) {
            if (!map) initializeGoogleMap();
            
            nearbyMarkers.forEach(m => m.setMap(null));
            nearbyMarkers = [];
            
            places.forEach(p => {
              if (p.lat && p.lon) {
                const marker = new google.maps.Marker({
                  position: {lat: p.lat, lng: p.lon},
                  map: map,
                  title: p.name || 'Location'
                });
                
                const infoWindow = new google.maps.InfoWindow({
                  content: `<div style="text-align:center;padding:10px"><strong>${p.name}</strong></div>`
                });
                
                marker.addListener('click', () => {
                  infoWindow.open(map, marker);
                });
                
                nearbyMarkers.push(marker);
              }
            });
            
            addMessage(`✅ Showing ${places.length} suggested places on the map!`, 'bot');
          } else {
            addMessage('⚠️ No places to display.', 'bot');
          }
        } 
        else if (type === 'save_wishlist') {
          const destId = payload.destination_id;
          if (destId && !isNaN(destId)) {
            try {
              const res = await fetch('api/toggle_wishlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ destination_id: destId })
              });
              
              if (res.ok) {
                const rj = await res.json();
                addMessage('❤️ ' + (rj.message || 'Added to your wishlist!'), 'bot');
              } else {
                addMessage('⚠️ Could not save to wishlist.', 'bot');
              }
            } catch (err) {
              console.error('Wishlist error:', err);
              addMessage('⚠️ Error saving to wishlist.', 'bot');
            }
          } else {
            addMessage('⚠️ Need a valid destination ID to save.', 'bot');
          }
        } 
        else if (type === 'nearby') {
          const cat = payload.category || 'restaurant';
          filterPOIs(cat);
          addMessage(`🔍 Showing nearby ${cat}s on the map!`, 'bot');
        }
        else {
          console.warn('Unknown action type:', type);
        }
      } catch (e) {
        console.error('Action handler error:', e);
      }
    }
  </script>

  <style>
    @keyframes counterUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .footer {
      background: #222;
      color: #ccccccbb;
      padding: 25px 0;
      text-align: center;
    }

    .footer a {
      color: #fff;
      margin: 0 10px;
      transition: 0.3s;
    }

    .footer a:hover {
      color: hsla(15, 100%, 50%, 1.00);
      transform: scale(1.2);
    }
  </style>
<!-- Live Stats -->
<section class="stats text-center mt-5">
  <div class="row">
    <div class="col-md-3"><div class="stat-box" id="liveUsers">0</div><p>Users Online</p></div>
    <div class="col-md-3"><div class="stat-box" id="totalTrips">1500+</div><p>Trips Booked</p></div>
    <div class="col-md-3"><div class="stat-box">120+</div><p>Countries Covered</p></div>
    <div class="col-md-3"><div class="stat-box">24/7</div><p>Customer Support</p></div>
  </div>
</section>
</div>

  <!-- Footer -->
<div class="footer mt-5">
  <p>© 2025 TravelGo | Follow us on
  <a href="#"><i class="fab fa-facebook"></i> Facebook</a> ·
  <a href="#"><i class="fab fa-twitter"></i> Twitter</a> ·
  <a href="https://www.instagram.com/white__teufel/"><i class="fab fa-instagram"></i> Instagram</a>
  <a href="https://github.com/Roshan282005"> <i class="fab fa-github">Github</i></a>
  </p>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
<script src="https://rawcdn.githack.com/bbecquet/Leaflet.RotatedMarker/master/leaflet.rotatedMarker.js"></script>

<!-- Extra animation libs: GSAP + Lottie -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.10.1/lottie.min.js"></script>

<script>
  // initialize lottie avatar
  try {
    if (window.lottie) {
      lottie.loadAnimation({
        container: document.getElementById('lottieSneha'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        // lightweight chat-bot like animation from LottieFiles
        path: 'https://assets9.lottiefiles.com/packages/lf20_j1adxtyb.json'
      });
    }
  } catch(e){console.warn('Lottie init failed', e)}

  // GSAP entrance and micro-interactions
  try {
    gsap.from('#map', {duration:1, scale:0.985, opacity:0, ease:'power3.out'});
    gsap.from('.chat-container', {duration:0.8, y:26, opacity:0, delay:0.12, ease:'power3.out'});

    // quick prompt hover polish
    document.querySelectorAll('.quick').forEach(el=>{
      el.addEventListener('mouseenter', ()=> gsap.to(el, {scale:1.06, y:-6, duration:0.22, ease:'power2.out'}));
      el.addEventListener('mouseleave', ()=> gsap.to(el, {scale:1, y:0, duration:0.22, ease:'power2.out'}));
    });

    // animate incoming messages
    const origAddMessage = window.addMessage || window.origAddMessage || null;
    if (typeof addMessage === 'function') {
      const _orig = addMessage;
      window.addMessage = function(text, sender){
        _orig(text, sender);
        const msgs = document.querySelectorAll('.chat-messages .message');
        const last = msgs[msgs.length-1];
        if (last) gsap.from(last, {y:12, opacity:0, duration:0.36, ease:'back.out(1.6)'});
      }
    }

    // subtle reaction when map moves
    if (typeof map !== 'undefined') {
      map.on('moveend', ()=> gsap.fromTo('#currentLocationBtn', {scale:1.06},{scale:1,duration:0.7, ease:'elastic.out(1,0.6)'}));
    }
  } catch(e){console.warn('GSAP init failed', e)}
</script>

  <!-- Footer -->
  <footer class="text-center py-3">&copy; 2025 TravelGo. All rights reserved.</footer>
</body>

</html>