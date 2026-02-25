<?php
// ------------------------
// search.php - JSON API
// ------------------------

include 'db.php'; // DB connection

// ✅ Set headers for JSON + CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// ✅ Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ Suppress PHP warnings/HTML output in JSON
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// ------------------------
// DB Connection Check
// ------------------------
if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed",
        "results" => []
    ]);
    exit;
}

// ------------------------
// Get search query
// ------------------------
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Empty search query",
        "results" => []
    ]);
    exit;
}

// ------------------------
// Search destinations
// ------------------------
try {
    $stmt = $conn->prepare("
        SELECT id, country, description, image, lat, lon
        FROM destinations
        WHERE country LIKE ? OR description LIKE ?
        ORDER BY country ASC
        LIMIT 10
    ");

    if (!$stmt) {
        throw new Exception("SQL prepare failed: " . $conn->error);
    }

    $search = "%" . $q . "%";
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id" => (int)$row["id"],
            "country" => trim($row["country"]),
            "description" => trim($row["description"]),
            "lat" => isset($row["lat"]) ? (float)$row["lat"] : null,
            "lon" => isset($row["lon"]) ? (float)$row["lon"] : null,
            "image" => !empty($row["image"]) ? "assets/images/" . basename($row["image"]) : "assets/images/default.jpg"
        ];
    }

    echo json_encode([
        "status" => "success",
        "count" => count($data),
        "results" => $data
    ]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // ✅ Always return valid JSON on error
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage(),
        "results" => []
    ]);
}
?>
