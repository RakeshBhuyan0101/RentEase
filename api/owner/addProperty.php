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

$title = trim($_POST['title']);
$description = trim($_POST['description']);
$city = trim($_POST['city']);
$locality = trim($_POST['locality']);
$fullAddress = trim($_POST['full_address']);
$bhkType = trim($_POST['bhk_type'] ?? '2BHK');
$furnishing = trim($_POST['furnishing'] ?? 'Semi-Furnished');
$monthlyRent = floatval($_POST['monthly_rent'] ?? 0);
$securityDeposit = floatval($_POST['security_deposit'] ?? 0);
$amenities = trim($_POST['amenities']);

if (empty($title) || empty($city) || empty($locality) || $monthlyRent <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields correctly']);
    exit;
}

// Handle cover image upload
$coverImageName = 'default-apt.jpg';
if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['cover_image']['tmp_name'];
    $fileSize = $_FILES['cover_image']['size'];
    $fileExt = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($fileExt, $allowedExts)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, and WebP images are allowed']);
        exit;
    }

    if ($fileSize > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Cover image size must be under 2 MB']);
        exit;
    }

    $uploadDir = __DIR__ . '/../../uploads/properties/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $newFileName = uniqid('prop_') . '.' . $fileExt;
    if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
        $coverImageName = $newFileName;
    }
}

try {
    $stmt = $conn->prepare("
        INSERT INTO properties 
        (owner_id, title, description, city, locality, full_address, bhk_type, furnishing, monthly_rent, security_deposit, amenities, cover_image, availability_status, approval_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', 'pending')
    ");
    $stmt->bind_param("isssssssdsss", 
        $ownerId, $title, $description, $city, $locality, $fullAddress, 
        $bhkType, $furnishing, $monthlyRent, $securityDeposit, $amenities, $coverImageName
    );
    $stmt->execute();
    $newPropId = $conn->insert_id;
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Property added successfully and submitted for approval',
        'property_id' => $newPropId
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to add property: ' . $e->getMessage()]);
}
