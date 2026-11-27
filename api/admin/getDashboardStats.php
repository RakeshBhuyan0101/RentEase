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
    // 1. Users stats
    $userRes = $conn->query("
        SELECT 
            COUNT(*) AS total_users,
            SUM(CASE WHEN role = 'tenant' THEN 1 ELSE 0 END) AS total_tenants,
            SUM(CASE WHEN role = 'owner' THEN 1 ELSE 0 END) AS total_owners,
            SUM(CASE WHEN STATUS = 'blocked' THEN 1 ELSE 0 END) AS blocked_users
        FROM users
        WHERE role != 'admin'
    ");
    $userStats = $userRes->fetch_assoc();

    // 2. Properties stats
    $propRes = $conn->query("
        SELECT 
            COUNT(*) AS total_properties,
            SUM(CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END) AS pending_properties,
            SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) AS approved_properties,
            SUM(CASE WHEN approval_status = 'rejected' THEN 1 ELSE 0 END) AS rejected_properties
        FROM properties
    ");
    $propStats = $propRes->fetch_assoc();

    // 3. Bookings stats
    $bookRes = $conn->query("
        SELECT 
            COUNT(*) AS total_bookings,
            SUM(CASE WHEN booking_status = 'pending' THEN 1 ELSE 0 END) AS pending_bookings,
            SUM(CASE WHEN booking_status = 'approved' THEN 1 ELSE 0 END) AS approved_bookings
        FROM bookings
    ");
    $bookStats = $bookRes->fetch_assoc();

    echo json_encode([
        'success' => true,
        'stats' => [
            'users' => [
                'total' => (int)($userStats['total_users'] ?? 0),
                'tenants' => (int)($userStats['total_tenants'] ?? 0),
                'owners' => (int)($userStats['total_owners'] ?? 0),
                'blocked' => (int)($userStats['blocked_users'] ?? 0)
            ],
            'properties' => [
                'total' => (int)($propStats['total_properties'] ?? 0),
                'pending' => (int)($propStats['pending_properties'] ?? 0),
                'approved' => (int)($propStats['approved_properties'] ?? 0),
                'rejected' => (int)($propStats['rejected_properties'] ?? 0)
            ],
            'bookings' => [
                'total' => (int)($bookStats['total_bookings'] ?? 0),
                'pending' => (int)($bookStats['pending_bookings'] ?? 0),
                'approved' => (int)($bookStats['approved_bookings'] ?? 0)
            ]
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

