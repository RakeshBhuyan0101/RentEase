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

$tenantId = $_SESSION['user_id'];
$bookingId = intval($_POST['booking_id'] ?? $_GET['id'] ?? 0);

if ($bookingId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

try {
    $stmt = $conn->prepare("
        UPDATE bookings 
        SET booking_status = 'cancelled' 
        WHERE booking_id = ? AND tenant_id = ? AND booking_status = 'pending'
    ");
    $stmt->bind_param("ii", $bookingId, $tenantId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Booking request cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cannot cancel this booking (either not pending or not found)']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

