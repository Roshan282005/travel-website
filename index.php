<?php 
include 'db.php'; 
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TravelGo Ultimate Map</title>
<link rel="icon" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png">

<!-- CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css"/>

<style>
body { 
  font-family:'Poppins', Arial, sans-serif; 
  background:#f5f6fa; 
  scroll-behavior:smooth;
}

.hero { 
  background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
              url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=1920&q=80') center/cover no-repeat; 
  height:85vh; 
  display:flex; 
  justify-content:center; 
  align-items:center; 
  color:white; 
  text-align:center;
  position: relative;
  overflow: hidden;
}

.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, rgba(13, 110, 253, 0.3), rgba(111, 66, 193, 0.3));
  animation: gradientShift 10s ease infinite;
}

@keyframes gradientShift {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 0.6; }
}

.hero-content {
  position: relative;
  z-index: 2;
}

.hero h1 { 
  font-size:4.5rem; 
  font-weight:900; 
  animation:fadeInDown 1.2s;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
  margin-bottom: 20px;
}

.hero p { 
  font-size:1.5rem; 
  margin:20px 0; 
  animation:fadeInUp 1.5s;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

.hero .btn { 
  padding:15px 40px; 
  font-size:1.2rem; 
  animation:pulse 2s infinite;
  border-radius: 50px;
  font-weight: 600;
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}
@keyframes fadeInDown{ from{opacity:0; transform:translateY(-30px);} to{opacity:1; transform:translateY(0);} }
@keyframes fadeInUp{ from{opacity:0; transform:translateY(30px);} to{opacity:1; transform:translateY(0);} }
@keyframes pulse{ 0%{transform:scale(1);} 50%{transform:scale(1.05);} 100%{transform:scale(1);} }
.search-bar{ 
  margin:40px auto; 
  max-width:700px; 
  display:flex; 
  gap:10px; 
  position:relative;
  background: white;
  border-radius: 60px;
  padding: 8px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

.search-bar input{ 
  flex:1; 
  padding:15px 25px; 
  border-radius:50px; 
  border:none; 
  outline:none; 
  font-size: 1.1rem;
}

.search-bar button{ 
  border:none; 
  border-radius:50px; 
  background: linear-gradient(135deg, #0d6efd, #6f42c1); 
  color:white; 
  font-weight:bold; 
  padding:15px 35px; 
  transition:0.3s;
  font-size: 1.1rem;
}

.search-bar button:hover{ 
  transform:scale(1.05);
  box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
}

#map{ 
  height:450px; 
  border-radius:20px; 
  box-shadow:0 10px 30px rgba(0,0,0,0.15); 
  margin-bottom:40px;
}

.card {
  border: none;
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.card:hover{ 
  transform:translateY(-10px); 
  box-shadow:0 20px 40px rgba(0,0,0,0.2);
}

.card-img-top {
  transition: transform 0.4s;
}

.card:hover .card-img-top {
  transform: scale(1.1);
}
.stats{ 
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding:60px 0;
  color: white;
  margin-top: 60px;
}

.stat-box{ 
  font-size:3rem; 
  font-weight:800; 
  animation:counterUp 2s;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.stat-box-3d {
  perspective: 1000px;
  margin: 20px auto;
  position: relative;
}

.stat-cube {
  position: relative;
  padding: 40px;
  background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.1));
  border-radius: 20px;
  backdrop-filter: blur(10px);
  border: 2px solid rgba(255,255,255,0.3);
  box-shadow: 
    0 20px 50px rgba(0,0,0,0.3),
    inset 0 1px 0 rgba(255,255,255,0.2);
  animation: 
    cube3DRotate 6s infinite ease-in-out,
    glowPulse 2.5s infinite;
  transition: all 0.3s ease;
}

.stat-cube:hover {
  transform: scale(1.1) rotateY(10deg) rotateX(10deg);
  box-shadow: 
    0 30px 70px rgba(13, 110, 253, 0.5),
    inset 0 1px 0 rgba(255,255,255,0.3);
}

@keyframes cube3DRotate {
  0%, 100% { 
    transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg);
  }
  25% { 
    transform: rotateX(10deg) rotateY(15deg) rotateZ(5deg);
  }
  50% { 
    transform: rotateX(0deg) rotateY(30deg) rotateZ(0deg);
  }
  75% { 
    transform: rotateX(-10deg) rotateY(15deg) rotateZ(-5deg);
  }
}

@keyframes glowPulse {
  0%, 100% { 
    text-shadow: 
      0 0 10px rgba(255,255,255,0.5),
      0 0 20px rgba(13, 110, 253, 0.3),
      2px 2px 4px rgba(0,0,0,0.2);
  }
  50% { 
    text-shadow: 
      0 0 20px rgba(255,255,255,0.8),
      0 0 40px rgba(13, 110, 253, 0.6),
      2px 2px 8px rgba(0,0,0,0.3);
  }
}

.stat-item {
  position: relative;
  z-index: 2;
}

.stat-item::before {
  content: '';
  position: absolute;
  top: -50px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 100px;
  background: radial-gradient(circle, rgba(13, 110, 253, 0.2) 0%, transparent 70%);
  border-radius: 50%;
  animation: floatingGlow 4s ease-in-out infinite;
  z-index: -1;
}

@keyframes floatingGlow {
  0%, 100% { 
    transform: translateX(-50%) translateY(0px);
    opacity: 0.3;
  }
  50% { 
    transform: translateX(-50%) translateY(-20px);
    opacity: 0.6;
  }
}

.stats p {
  font-size: 1.1rem;
  opacity: 0.9;
  margin-top: 10px;
  font-weight: 500;
  letter-spacing: 0.5px;
}

.stat-label {
  animation: fadeInUp 1s ease-out forwards;
}

@keyframes counterUp{ from{opacity:0; transform:translateY(20px);} to{opacity:1; transform:translateY(0);} }
.footer{ 
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color:#fff; 
  padding:40px 0; 
  text-align:center;
}

.footer a{ 
  color:#fff; 
  margin:0 15px; 
  transition:0.3s;
  font-size: 1.5rem;
}

.footer a:hover{ 
  color:#ffc107; 
  transform:scale(1.3);
}

.profile-card {
  max-width: 550px;
  margin: 40px auto;
  padding: 30px;
  border-radius: 20px;
  background: white;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  text-align: center;
  animation: fadeInUp 0.8s;
}

.profile-card img {
  width: 120px; 
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 20px;
  border: 5px solid #0d6efd;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.section-heading {
  text-align: center;
  margin-bottom: 50px;
}

.section-heading h2 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 15px;
}

@media (max-width: 768px) {
  .hero h1 { font-size: 2.5rem; }
  .hero p { font-size: 1.2rem; }
  #map { height: 300px; }
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- Hero -->
<section class="hero">
  <div class="hero-content">
    <h1 data-aos="fade-down">Explore The World 🌍</h1>
    <p id="userLocation" data-aos="fade-up" data-aos-delay="200">Discover amazing destinations & book your next adventure</p>
    <div data-aos="fade-up" data-aos-delay="400">
      <a href="#map" class="btn btn-primary btn-lg me-3">
        <i class="fas fa-map-marked-alt me-2"></i>Start Journey
      </a>
      <a href="destinations.php" class="btn btn-outline-light btn-lg">
        <i class="fas fa-search me-2"></i>Browse Destinations
      </a>
    </div>
  </div>
</section>

<!-- ✅ Profile Section (visible only if logged in) -->
<?php if(isset($_SESSION['user_id'])): ?>
  <div class="profile-card">
    <img src="<?php echo $_SESSION['user_photo'] ?? 'assets/icons/user.png'; ?>" alt="Profile">
    <h3><?php echo $_SESSION['user_name']; ?></h3>
    <p><?php echo $_SESSION['user_email']; ?></p>
    <a href="profile.php" class="btn btn-outline-primary btn-sm"><i class="fa fa-user"></i> View Profile</a>
    <a href="logout.php" class="btn btn-danger btn-sm"><i class="fa fa-sign-out"></i> Logout</a>
  </div>
<?php endif; ?>

<div class="container mt-5">

<!-- Search -->
<div class="search-bar">
  <input type="text" id="searchInput" placeholder="Search destinations...">
  <button onclick="searchDestination()"><i class="fas fa-search"></i> Go</button>
  <ul class="list-group" id="searchResults" style="position:absolute; z-index:1000; width:100%; display:none;"></ul>
</div>

<!-- Map -->
<div id="map"></div>

<!-- Featured Carousel -->
<h2 class="mb-4">🌟 Featured Destinations</h2>
<div id="featuredCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php
    $featured = $conn->query("SELECT * FROM destinations WHERE featured=1 LIMIT 5");
    $first = true;
    while($row=$featured->fetch_assoc()){
      echo '<div class="carousel-item '.($first?'active':'').'">
      <img src="assets/images/'.$row['image'].'" class="d-block w-100" style="height:400px; object-fit:cover;">
      <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 p-3 rounded">
        <h3>'.$row['country'].' <span class="badge bg-info live-weather" data-city="'.$row['country'].'">Loading...</span></h3>
        <p>'.substr($row['description'],0,120).'...</p>
        <a href="destination.php?id='.$row['id'].'" class="btn btn-warning">Discover</a>
      </div>
      </div>'; 
      $first=false;
    }
    ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
  <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
</div>

<!-- Destination Cards -->
<div class="section-heading" data-aos="fade-up">
  <h2><i class="fas fa-globe-americas me-3"></i>Popular Destinations</h2>
  <p class="text-muted">Discover the world's most amazing places</p>
</div>
<div class="row" id="destinationsList">
<?php
$result = $conn->query("SELECT * FROM destinations");
while($row = $result->fetch_assoc()){
  echo '<div class="col-md-4 mb-4" data-aos="fade-up" data-name="'.$row['country'].'">
  <div class="card">
    <img src="assets/images/'.$row['image'].'" class="card-img-top" style="height:220px; object-fit:cover;">
    <div class="card-body">
      <h5>'.$row['country'].' <span class="badge bg-success availability">Checking...</span></h5>
      <p>'.substr($row['description'],0,100).'...</p>
      <a href="destination.php?id='.$row['id'].'" class="btn btn-primary">View More</a>
    </div>
  </div>
  </div>';
}
?>
</div>

<!-- Live Stats with 3D Animations -->
<section class="stats text-center mt-5">
  <div class="row">
    <div class="col-md-3 stat-item" data-aos="flip-left">
      <div class="stat-box-3d" id="liveUsers3D">
        <div class="stat-box stat-cube" id="liveUsers">0</div>
        <p class="stat-label">Users Online</p>
      </div>
    </div>
    <div class="col-md-3 stat-item" data-aos="flip-left" data-aos-delay="100">
      <div class="stat-box-3d" id="totalTrips3D">
        <div class="stat-box stat-cube" id="totalTrips">1500+</div>
        <p class="stat-label">Trips Booked</p>
      </div>
    </div>
    <div class="col-md-3 stat-item" data-aos="flip-left" data-aos-delay="200">
      <div class="stat-box-3d" id="countriesCovered3D">
        <div class="stat-box stat-cube" id="countriesCovered">120+</div>
        <p class="stat-label">Countries Covered</p>
      </div>
    </div>
    <div class="col-md-3 stat-item" data-aos="flip-left" data-aos-delay="300">
      <div class="stat-box-3d" id="support3D">
        <div class="stat-box stat-cube" id="supportBox">24/7</div>
        <p class="stat-label">Customer Support</p>
      </div>
    </div>
  </div>
  <!-- 3D Canvas Background -->
  <canvas id="statsCanvas3D" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;"></canvas>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<script>
// 3D Background Animation for Stats Section
function initStats3D() {
  const canvas = document.getElementById('statsCanvas3D');
  if (!canvas) return;
  
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(75, window.innerWidth / 500, 0.1, 1000);
  const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
  
  renderer.setSize(window.innerWidth, 500);
  renderer.setPixelRatio(window.devicePixelRatio);
  renderer.setClearColor(0x000000, 0.1);
  
  camera.position.z = 50;
  
  // Create floating particles
  const particleGeometry = new THREE.BufferGeometry();
  const particleCount = 100;
  const positionArray = new Float32Array(particleCount * 3);
  const velocityArray = new Float32Array(particleCount * 3);
  
  for (let i = 0; i < particleCount * 3; i += 3) {
    positionArray[i] = (Math.random() - 0.5) * 200;
    positionArray[i + 1] = (Math.random() - 0.5) * 200;
    positionArray[i + 2] = (Math.random() - 0.5) * 200;
    
    velocityArray[i] = (Math.random() - 0.5) * 0.5;
    velocityArray[i + 1] = (Math.random() - 0.5) * 0.5;
    velocityArray[i + 2] = (Math.random() - 0.5) * 0.5;
  }
  
  particleGeometry.setAttribute('position', new THREE.BufferAttribute(positionArray, 3));
  particleGeometry.setAttribute('velocity', new THREE.BufferAttribute(velocityArray, 3));
  
  const particleMaterial = new THREE.PointsMaterial({
    size: 2,
    color: 0x6f42c1,
    transparent: true,
    opacity: 0.6,
    sizeAttenuation: true
  });
  
  const particles = new THREE.Points(particleGeometry, particleMaterial);
  scene.add(particles);
  
  // Create floating geometric shapes
  const geometries = [
    new THREE.IcosahedronGeometry(5, 4),
    new THREE.OctahedronGeometry(5),
    new THREE.TetrahedronGeometry(5)
  ];
  
  const shapes = [];
  const colors = [0x0d6efd, 0x6f42c1, 0x17a2b8];
  
  for (let i = 0; i < 3; i++) {
    const material = new THREE.MeshPhongMaterial({
      color: colors[i],
      emissive: colors[i],
      emissiveIntensity: 0.2,
      wireframe: true,
      transparent: true,
      opacity: 0.4
    });
    
    const mesh = new THREE.Mesh(geometries[i], material);
    mesh.position.set(
      (i - 1) * 40,
      Math.sin(i) * 20,
      -30 + i * 10
    );
    mesh.rotation.set(Math.random(), Math.random(), Math.random());
    
    scene.add(mesh);
    shapes.push({
      mesh,
      rotationSpeed: { x: Math.random() * 0.003, y: Math.random() * 0.003, z: Math.random() * 0.003 },
      positionSpeed: { x: Math.random() * 0.02 - 0.01, y: Math.random() * 0.02 - 0.01 }
    });
  }
  
  // Lighting
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
  const pointLight = new THREE.PointLight(0x0d6efd, 1, 100);
  pointLight.position.set(30, 30, 30);
  const pointLight2 = new THREE.PointLight(0x6f42c1, 0.8, 100);
  pointLight2.position.set(-30, -30, 30);
  
  scene.add(ambientLight, pointLight, pointLight2);
  
  // Animation loop
  function animate() {
    requestAnimationFrame(animate);
    
    // Update particles
    const positions = particleGeometry.attributes.position.array;
    const velocities = particleGeometry.attributes.velocity.array;
    
    for (let i = 0; i < positions.length; i += 3) {
      positions[i] += velocities[i];
      positions[i + 1] += velocities[i + 1];
      positions[i + 2] += velocities[i + 2];
      
      if (Math.abs(positions[i]) > 100) velocities[i] *= -1;
      if (Math.abs(positions[i + 1]) > 100) velocities[i + 1] *= -1;
      if (Math.abs(positions[i + 2]) > 100) velocities[i + 2] *= -1;
    }
    
    particleGeometry.attributes.position.needsUpdate = true;
    
    // Update shapes
    shapes.forEach((shape, idx) => {
      shape.mesh.rotation.x += shape.rotationSpeed.x;
      shape.mesh.rotation.y += shape.rotationSpeed.y;
      shape.mesh.rotation.z += shape.rotationSpeed.z;
      
      shape.mesh.position.x += shape.positionSpeed.x;
      shape.mesh.position.y += Math.sin(Date.now() * 0.001 + idx) * 0.02;
    });
    
    renderer.render(scene, camera);
  }
  
  animate();
  
  // Handle window resize
  window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / 500;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, 500);
  });
}

// Initialize 3D when page loads
setTimeout(initStats3D, 500);
</script>

<script>
AOS.init();

let map, userMarker, routingControl, clusterGroup = L.markerClusterGroup();
let userCoords=[0,0];

// Init map
function initMap(lat, lon){
  const userIcon = L.icon({iconUrl:'assets/icons/arrow-icon.png', iconSize:[40,40], iconAnchor:[20,20]});
  map = L.map('map').setView([lat, lon], 6);
  let street=L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);
  let satellite=L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',{maxZoom:19});
  L.control.layers({"Street":street,"Satellite":satellite}).addTo(map);
  clusterGroup.addTo(map);
  userMarker = L.marker([lat, lon], {icon:userIcon}).addTo(map).bindPopup("You are here").openPopup();
}

// Watch user location
if(navigator.geolocation){
  navigator.geolocation.watchPosition(pos=>{
    userCoords=[pos.coords.latitude,pos.coords.longitude];
    if(!map){ initMap(userCoords[0],userCoords[1]); }
    else { userMarker.setLatLng(userCoords); map.setView(userCoords,8); }
    document.getElementById("userLocation").innerText=`You are browsing from Lat:${userCoords[0].toFixed(2)}, Lon:${userCoords[1].toFixed(2)}`;
  }, err=>console.error(err), {enableHighAccuracy:true});
}

// ✅ Search Destination with backend proxy
async function searchDestination(){
  let dest=document.getElementById("searchInput").value.trim();
  if(!dest) return alert("Enter a destination");

  try {
    const res = await fetch(`search.php?q=${encodeURIComponent(dest)}`);
    let dbData = {};
    try { dbData = await res.json(); } catch(e){ console.warn("Invalid JSON from PHP"); }

    let latNum, lonNum, displayName, imageUrl;

    if(dbData.status==="success" && dbData.count>0){
      const d=dbData.results[0];
      displayName = d.country+" - "+d.description;
      imageUrl = d.image ? "assets/images/"+d.image : `https://source.unsplash.com/400x300/?${encodeURIComponent(dest)}`;
      latNum = d.lat ? parseFloat(d.lat) : userCoords[0]+0.02;
      lonNum = d.lon ? parseFloat(d.lon) : userCoords[1]+0.02;
    } else {
      const osmRes = await fetch(`geocode.php?q=${encodeURIComponent(dest)}`);
      const data = await osmRes.json();
      if(!data || !data.length){ alert("Destination not found."); return; }
      latNum = parseFloat(data[0].lat);
      lonNum = parseFloat(data[0].lon);
      displayName = data[0].display_name;
      imageUrl = `https://source.unsplash.com/400x300/?${encodeURIComponent(dest)}`;
    }

    if(routingControl) map.removeControl(routingControl);
    routingControl=L.Routing.control({
      waypoints:[ L.latLng(userCoords[0],userCoords[1]), L.latLng(latNum,lonNum) ],
      routeWhileDragging:true
    }).addTo(map);
    map.flyTo([latNum,lonNum],8);

    let destMarker=L.marker([latNum,lonNum],{icon:L.icon({iconUrl:'assets/icons/destination-icon.png',iconSize:[35,35]})})
    .bindPopup(`<div style="width:200px;"><h6>📍 ${displayName}</h6><img src="${imageUrl}" style="width:100%;height:120px;object-fit:cover;border-radius:8px;"></div>`).addTo(map);
    clusterGroup.addLayer(destMarker);

  } catch(err){ alert("Search failed: "+err.message); console.error(err); }
}

// Weather
document.querySelectorAll('.live-weather').forEach(span=>{
  let city=span.getAttribute('data-city');
  fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=YOUR_API_KEY&units=metric`)
  .then(res=>res.json()).then(data=>{ span.innerText = data.main?data.main.temp+"°C":"N/A"; });
});

// Animate live users counter with 3D effect
function animateCounter(element, start, end, duration = 2000) {
  let current = start;
  const increment = (end - start) / (duration / 16);
  const counter = setInterval(() => {
    current += increment;
    if (current >= end) {
      current = end;
      clearInterval(counter);
      setTimeout(() => animateCounter(element, end, start + Math.floor(Math.random() * 30), 2000), 3000);
    }
    element.textContent = Math.floor(current);
  }, 16);
}

// Start live users animation
const liveUsersEl = document.getElementById('liveUsers');
if (liveUsersEl) {
  animateCounter(liveUsersEl, 0, Math.floor(Math.random() * 50) + 20);
}

// Wishlist toggle
function toggleWishlist(destinationId) {
  <?php if(isset($_SESSION['user_id'])): ?>
    fetch('api/toggle_wishlist.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({destination_id: destinationId})
    })
    .then(res => res.json())
    .then(data => {
      if(data.success) {
        alert(data.message);
      }
    });
  <?php else: ?>
    alert('Please login to save destinations');
    window.location.href = 'login.php';
  <?php endif; ?>
}
</script>
</body>
</html>
