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
        SELECT b.booking_id, b.property_id, b.move_in_date, b.lease_months, 
               b.tenant_message, b.booking_status, b.created_at,
               p.title AS property_title, p.bhk_type, p.monthly_rent,
               u.full_name AS tenant_name, u.phone AS tenant_phone, u.email AS tenant_email
        FROM bookings b
        JOIN properties p ON b.property_id = p.property_id
        JOIN users u ON b.tenant_id = u.user_id
        WHERE p.owner_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->bind_param("i", $ownerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = [];
    $counts = ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];

    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
        $counts['all']++;
        $status = strtolower($row['booking_status']);
        if (isset($counts[$status])) {
            $counts[$status]++;
        }
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'bookings' => $bookings,
        'counts' => $counts
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
