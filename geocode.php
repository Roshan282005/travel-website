<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if (!$q) {
    echo json_encode([]);
    exit;
}

// Use Nominatim API
$url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($q) . "&limit=1";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'TravelGoApp/1.0'); // Nominatim requires a user-agent
$response = curl_exec($ch);
curl_close($ch);

// Return JSON to frontend
echo $response;
