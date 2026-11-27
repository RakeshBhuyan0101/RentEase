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
$propertyId = intval($_POST['property_id'] ?? $_GET['id'] ?? 0);

if ($propertyId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid property ID']);
    exit;
}

// Verify property belongs to logged-in owner
$checkStmt = $conn->prepare("SELECT property_id, cover_image FROM properties WHERE property_id = ? AND owner_id = ?");
$checkStmt->bind_param("ii", $propertyId, $ownerId);
$checkStmt->execute();
$existing = $checkStmt->get_result()->fetch_assoc();
$checkStmt->close();

if (!$existing) {
    echo json_encode(['success' => false, 'message' => 'Property not found or access denied']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$city = trim($_POST['city'] ?? '');
$locality = trim($_POST['locality'] ?? '');
$fullAddress = trim($_POST['full_address'] ?? '');
$bhkType = trim($_POST['bhk_type'] ?? '2BHK');
$furnishing = trim($_POST['furnishing'] ?? 'Semi-Furnished');
$monthlyRent = floatval($_POST['monthly_rent'] ?? 0);
$securityDeposit = floatval($_POST['security_deposit'] ?? 0);
$amenities = trim($_POST['amenities'] ?? '');

if (empty($title) || empty($city) || empty($locality) || $monthlyRent <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields correctly']);
    exit;
}

$coverImageName = $existing['cover_image'];

// Handle cover image upload if a new one was provided
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
    // Update property details and reset approval_status to 'pending'
    $stmt = $conn->prepare("
        UPDATE properties SET
            title = ?,
            description = ?,
            city = ?,
            locality = ?,
            full_address = ?,
            bhk_type = ?,
            furnishing = ?,
            monthly_rent = ?,
            security_deposit = ?,
            amenities = ?,
            cover_image = ?,
            approval_status = 'pending'
        WHERE property_id = ? AND owner_id = ?
    ");
    $stmt->bind_param("sssssssdsssii", 
        $title, $description, $city, $locality, $fullAddress, 
        $bhkType, $furnishing, $monthlyRent, $securityDeposit, $amenities, 
        $coverImageName, $propertyId, $ownerId
    );
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Property updated successfully and submitted for approval',
        'property_id' => $propertyId
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to update property: ' . $e->getMessage()]);
}

