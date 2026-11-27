<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$propertyId = intval($_POST['property_id'] ?? $_GET['id'] ?? 0);
$status = strtolower(trim($_POST['status'] ?? $_GET['status'] ?? ''));

if ($propertyId <= 0 || !in_array($status, ['approved', 'rejected', 'pending'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE properties SET approval_status = ? WHERE property_id = ?");
    $stmt->bind_param("si", $status, $propertyId);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Property status updated to ' . strtoupper($status)
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

