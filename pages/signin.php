<?php 
    session_start();
    if (isset($_SESSION['user_id'])) {
      $role = $_SESSION['role'];
      $dashboards  = [
          'tenant' => './tenant/dashboard.php',
          'owner' => './owner/dashboard.php',
          'admin' => './admin/dashboard.php'
      ];
      header('Location:' . ($dashboards[$role] ?? './signin.php'));
      exit;
  }

    $customCSS = "signin.css";
    $customJS = "signin.js";
    include_once "navbar.php";
?>

<main class="login-wrapper">
  <div class="login-card">

    <!-- Heading -->
    <div class="text-center mb-4">
      <div class="logo-box d-inline-flex align-items-center justify-content-center mb-3">
        <i class="bi bi-house-door"></i>
      </div>
      <h3 class="fw-bold">Welcome back to RentEase</h3>
      <p class="text-secondary small mb-0">Enter your email and password to access your account</p>
    </div>

    <form id="signinForm">
      <div id="passworderrormsg" class="text-center text-danger mb-3"></div>

      <!-- Email -->
      <div class="mb-3">
        <label for="email" class="form-label fw-medium small">Email Address</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" id="email" class="form-control ps-1" placeholder="name@example.com">
        </div>
        <div id="emailerrormsg" class="text-danger small mt-1"></div>
      </div>

      <!-- Password -->
      <div class="mb-3">
        <label for="password" class="form-label fw-medium small">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" id="password" class="form-control ps-1" placeholder="Enter your password">
          <span class="input-group-text" id="togglePassword" role="button">
            <i class="bi bi-eye"></i>
          </span>
        </div>
      </div>

      
      <button type="submit" class="btn btn-signin w-100">Sign In </button>
    </form>

    <p class="text-center text-secondary small mt-4 mb-0">
      Don't have an account? <a href="signup.php" class="green-link">Sign up for RentEase</a>
    </p>

  </div>
</main>

<?php include_once "footer.php"; ?>
p for RentEase</a>
    </p>

  </div>
</main>

<?php include_once "footer.php"; ?>
