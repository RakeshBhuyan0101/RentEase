<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

$email = strtolower(trim($_POST['email'] ?? ''));
$submittedPassword = $_POST['password'] ?? '';

if ($email === '' || $submittedPassword === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Email and password are required'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Enter a valid email address'
    ]);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

try {
    $stmt = $conn->prepare(
        'SELECT user_id, full_name, email, password_hash, role, STATUS
         FROM users
         WHERE email = ?
         LIMIT 1'
    );

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);
        exit;
    }

    if ($user['STATUS'] !== 'active') {
        echo json_encode([
            'success' => false,
            'message' => 'Your account is blocked'
        ]);
        exit;
    }

    // Strictly verify using hashing (password_verify)
    if (!password_verify($submittedPassword, $user['password_hash'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    $redirectUrl = './signin.php';

    switch ($user['role']) {
        case 'tenant':
            $redirectUrl = './tenant/dashboard.php';
            break;

        case 'owner':
            $redirectUrl = './owner/dashboard.php';
            break;

        case 'admin':
            $redirectUrl = './admin/dashboard.php';
            break;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'redirectUrl' => $redirectUrl
    ]);

} catch (Throwable $e) {
    error_log('Signin error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Unable to sign in'
    ]);
}
?>
