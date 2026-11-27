<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'owner') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$ownerId = $_SESSION['user_id'];
$propertyId = intval($_POST['property_id'] ?? $_GET['id'] ?? 0);

if ($propertyId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid property ID']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM properties WHERE property_id = ? AND owner_id = ?");
    $stmt->bind_param("ii", $propertyId, $ownerId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Property deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Property not found or access denied']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

