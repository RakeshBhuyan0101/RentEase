<?php
// api/properties.php - Root endpoint
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    require __DIR__ . '/owner/getProperties.php';
} elseif ($method === 'POST') {
    if (!empty($_GET['id']) || !empty($_POST['property_id'])) {
        require __DIR__ . '/owner/updateProperty.php';
    } else {
        require __DIR__ . '/owner/addProperty.php';
    }
} elseif ($method === 'DELETE') {
    require __DIR__ . '/owner/deleteProperty.php';
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

