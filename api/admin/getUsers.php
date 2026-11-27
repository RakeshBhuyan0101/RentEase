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
        SELECT user_id, full_name, email, phone, role, STATUS, created_at
        FROM users
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    $counts = ['all' => 0, 'tenant' => 0, 'owner' => 0, 'active' => 0, 'blocked' => 0];

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
        $counts['all']++;

        $r = strtolower($row['role']);
        if (isset($counts[$r])) {
            $counts[$r]++;
        }

        $s = strtolower($row['STATUS']);
        if (isset($counts[$s])) {
            $counts[$s]++;
        }
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'users' => $users,
        'counts' => $counts
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

