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
$bookingId = intval($_POST['booking_id'] ?? 0);
$status = strtolower(trim($_POST['status'] ?? ''));

if (!in_array($status, ['approved', 'rejected', 'pending'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $stmt = $conn->prepare("
        UPDATE bookings b
        JOIN properties p ON b.property_id = p.property_id
        SET b.booking_status = ?
        WHERE b.booking_id = ? AND p.owner_id = ?
    ");
    $stmt->bind_param("sii", $status, $bookingId, $ownerId);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Booking status updated to ' . $status]);
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
