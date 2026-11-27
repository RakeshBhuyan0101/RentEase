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

$userId = intval($_POST['user_id'] ?? $_GET['id'] ?? 0);
$status = strtolower(trim($_POST['status'] ?? $_GET['status'] ?? ''));

if ($userId <= 0 || !in_array($status, ['active', 'blocked'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

if ($userId === (int)$_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You cannot block your own admin account']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE users SET STATUS = ? WHERE user_id = ?");
    $stmt->bind_param("si", $status, $userId);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'User status updated to ' . strtoupper($status)
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

