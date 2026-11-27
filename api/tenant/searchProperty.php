<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tenant') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$search = trim($_GET['search'] ?? '');
$city = trim($_GET['city'] ?? '');
$bhkType = trim($_GET['bhk_type'] ?? '');
$maxRent = floatval($_GET['max_rent'] ?? 0);

try {
    $query = "
        SELECT p.property_id, p.title, p.description, p.city, p.locality, p.full_address,
               p.bhk_type, p.furnishing, p.monthly_rent, p.security_deposit, p.amenities,
               p.cover_image, p.availability_status, p.approval_status, p.created_at,
               u.full_name AS owner_name, u.email AS owner_email
        FROM properties p
        JOIN users u ON p.owner_id = u.user_id
        WHERE p.approval_status = 'approved' AND p.availability_status = 'available'
    ";
    
    $params = [];
    $types = "";

    if ($search !== '') {
        $query .= " AND (p.title LIKE ? OR p.locality LIKE ? OR p.city LIKE ? OR p.amenities LIKE ?)";
        $searchTerm = "%" . $search . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ssss";
    }

    if ($city !== '') {
        $query .= " AND p.city = ?";
        $params[] = $city;
        $types .= "s";
    }

    if ($bhkType !== '') {
        $query .= " AND p.bhk_type = ?";
        $params[] = $bhkType;
        $types .= "s";
    }

    if ($maxRent > 0) {
        $query .= " AND p.monthly_rent <= ?";
        $params[] = $maxRent;
        $types .= "d";
    }

    $query .= " ORDER BY p.created_at DESC";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    $stmt->close();

    // Fetch distinct cities for filter dropdown
    $citiesRes = $conn->query("SELECT DISTINCT city FROM properties WHERE approval_status = 'approved' ORDER BY city ASC");
    $cities = [];
    while ($c = $citiesRes->fetch_assoc()) {
        if (!empty($c['city'])) {
            $cities[] = $c['city'];
        }
    }

    echo json_encode([
        'success' => true,
        'properties' => $properties,
        'cities' => $cities,
        'count' => count($properties)
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

