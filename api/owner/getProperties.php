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

try {
    $stmt = $conn->prepare("
        SELECT property_id, title, description, city, locality, full_address, 
               bhk_type, furnishing, monthly_rent, security_deposit, amenities, 
               cover_image, availability_status, approval_status, created_at
        FROM properties
        WHERE owner_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $ownerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $properties = [];
    $counts = ['all' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0];

    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
        $counts['all']++;
        $status = strtolower($row['approval_status']);
        if (isset($counts[$status])) {
            $counts[$status]++;
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
