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

try {
    $stmt = $conn->prepare("
        SELECT p.property_id, p.owner_id, p.title, p.description, p.city, p.locality, 
               p.full_address, p.bhk_type, p.furnishing, p.monthly_rent, p.security_deposit, 
               p.amenities, p.cover_image, p.availability_status, p.approval_status, p.created_at,
               u.full_name AS owner_name, u.email AS owner_email, u.phone AS owner_phone
        FROM properties p
        JOIN users u ON p.owner_id = u.user_id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    $properties = [];
    $counts = ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];

    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
        $counts['all']++;
        $st = strtolower($row['approval_status']);
        if (isset($counts[$st])) {
            $counts[$st]++;
        }
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'properties' => $properties,
        'counts' => $counts
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

