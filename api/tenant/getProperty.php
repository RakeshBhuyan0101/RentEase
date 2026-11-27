<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$propertyId = intval($_GET['id'] ?? $_GET['property_id'] ?? 0);

if ($propertyId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid property ID']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT p.*, u.full_name AS owner_name, u.email AS owner_email, u.phone AS owner_phone
        FROM properties p
        JOIN users u ON p.owner_id = u.user_id
        WHERE p.property_id = ? AND p.approval_status = 'approved'
    ");
    $stmt->bind_param("i", $propertyId);
    $stmt->execute();
    $property = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($property) {
        echo json_encode(['success' => true, 'property' => $property]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Property not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

