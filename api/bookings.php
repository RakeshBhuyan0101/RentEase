<?php
// api/bookings.php - Root endpoint
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    require __DIR__ . '/owner/getBookings.php';
} elseif ($method === 'POST') {
    require __DIR__ . '/owner/updateBookingStatus.php';
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

