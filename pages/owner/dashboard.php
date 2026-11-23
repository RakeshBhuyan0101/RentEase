<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../signin.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'owner') {
    header('Location: ../signin.php');
    exit;
}

include_once '../navbar.php';
echo "Owner page";
?>