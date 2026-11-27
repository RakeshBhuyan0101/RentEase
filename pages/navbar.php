<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentEase</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Common CSS -->
    <link rel="stylesheet" href="/RentEase/assets/css/style.css">

    <!-- Page Specific CSS -->
    <?php if (isset($customCSS)): ?>
        <link rel="stylesheet" href="/RentEase/assets/css/<?= htmlspecialchars($customCSS) ?>?v=<?= time() ?>">
    <?php endif; ?>
</head>

<body>
  <nav class="navbar bg-white border-bottom px-3 px-md-4">
    <a class="navbar-brand brand fw-bold fs-6 d-flex align-items-center gap-2 mb-0" href="#">
      <i class="bi bi-buildings-fill text-success" style="color: #2d6a4f !important;"></i>
      RENTEASE
    </a>

    <div class="d-flex align-items-center gap-3">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="small text-muted d-none d-sm-inline" style="font-size:13px;">
          <?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?>
          <span class="badge bg-light text-dark border ms-1" style="font-size:10px; font-weight:700;">
            <?= strtoupper(htmlspecialchars($_SESSION['role'] ?? '')) ?>
          </span>
        </span>

        <a href="/RentEase/api/auth/logout.php"
           class="btn btn-outline-dark btn-sm"
           style="border-radius:6px; font-size:12px; font-weight:600;"
           onclick="return confirm('Are you sure you want to log out?');">
          Logout
        </a>
      <?php endif; ?>
    </div>
  </nav>
