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
$propertyId = intval($_POST['property_id'] ?? 0);
$moveInDate = trim($_POST['move_in_date'] ?? '');
$leaseMonths = intval($_POST['lease_months'] ?? 11);
$tenantMessage = trim($_POST['tenant_message'] ?? '');

if ($propertyId <= 0 || empty($moveInDate)) {
    echo json_encode(['success' => false, 'message' => 'Please select a valid move-in date']);
    exit;
}

try {
    // 1. Verify property is approved and available
    $checkStmt = $conn->prepare("SELECT property_id, title FROM properties WHERE property_id = ? AND approval_status = 'approved'");
    $checkStmt->bind_param("i", $propertyId);
    $checkStmt->execute();
    $prop = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();

    if (!$prop) {
        echo json_encode(['success' => false, 'message' => 'This property is not currently available for booking']);
        exit;
    }

    // 2. Check for active pending request from this tenant
    $existStmt = $conn->prepare("SELECT booking_id FROM bookings WHERE property_id = ? AND tenant_id = ? AND booking_status IN ('pending', 'approved')");
    $existStmt->bind_param("ii", $propertyId, $tenantId);
    $existStmt->execute();
    $existing = $existStmt->get_result()->fetch_assoc();
    $existStmt->close();

    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'You already have an active or pending booking for this property']);
        exit;
    }

    // 3. Insert booking
    $stmt = $conn->prepare("
        INSERT INTO bookings (property_id, tenant_id, move_in_date, lease_months, tenant_message, booking_status)
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->bind_param("iisis", $propertyId, $tenantId, $moveInDate, $leaseMonths, $tenantMessage);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Booking request submitted successfully! The owner will review your request.'
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

