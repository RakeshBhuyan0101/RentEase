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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Common CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Page Specific CSS -->
    <?php if (isset($customCSS)): ?>
        <link rel="stylesheet"
              href="../assets/css/<?= htmlspecialchars($customCSS) ?>?v=<?= time() ?>">
    <?php endif; ?>
</head>

<body>
  <nav class="navbar bg-white border-bottom px-3 px-md-4">
    <a class="navbar-brand brand fw-bold fs-6 d-flex align-items-center gap-2 mb-0"
       href="#">
      <i class="bi bi-buildings-fill logo-icon"></i>
      RENTEASE
    </a>

    <div class="d-flex align-items-center gap-3">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="small text-muted-custom d-none d-sm-inline"
              style="font-size:12px;">
          <?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?>
        </span>

        <a href="../../api/auth/logout.php"
           class="btn btn-outline-dark btn-sm"
           onclick="return confirm('Are you sure you want to log out?');">
          Logout
        </a>
      <?php else: ?>
        <span class="small text-muted-custom d-none d-sm-inline"
              style="font-size:12px;">
          Assistance  Concierge
        </span>
      <?php endif; ?>

      <span class="d-inline-flex align-items-center justify-content-center bg-black text-white rounded-3"
            style="width:36px;height:36px;">
        <i class="bi bi-person-fill fs-5"></i>
      </span>
    </div>
  </nav>