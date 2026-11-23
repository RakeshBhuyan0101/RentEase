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
$customCSS = "signup.css"; 
$customJS = "signup.js"; 
include_once "navbar.php"; 
?>

<div class="signup-wrapper container">

    <h5 class="fw-semibold mb-2">Create your account</h5>
    <p class="text-secondary mb-4">Choose how you'd like to use RentEase to get started.</p>

    <div class="signup-card">
      <form id="form" >

        <!-- Account type -->
        <div class="mb-3">
          <label for="accountType" class="form-label">Account Type <span>*</span></label>
          <select id="accountType" class="form-select" required>
            <option value="landlord" selected>Property Owner (Landlord)</option>
            <option value="tenant">Tenant</option>
          </select>
        </div>

        <hr class="text-secondary-emphasis opacity-10 mb-4">

        <!-- Full name -->
        <div class="mb-4">
          <label for="fullName" class="form-label">Full Legal Name <span>*</span></label>
          <div class="hint">As per government ID (Aadhaar / PAN card)</div>
          <input type="text" id="fullName" class="form-control" placeholder="e.g. Rahul Sundaram" required>
        </div>

        <!-- Email -->
        <div class="mb-4">
          <label for="email" class="form-label">Email Address <span>*</span></label>
          <div class="hint">Used for lease deed transmission &amp; escrow bank vouchers</div>
          <input type="email" id="email" class="form-control" placeholder="rahul.sundaram@domain.in" required>
        </div>

        

        <!-- Phone -->
        <div class="mb-4">
          <label for="phone" class="form-label">Phone Number <span>*</span></label>
          <input type="tel" id="phone" class="form-control" placeholder="+91 98765 43210" required>
        </div>

        <!-- Password -->
        <div class="mb-4">
          <label for="password" class="form-label">Create Password <span>*</span></label>
          <input type="password" id="password" class="form-control" placeholder="Minimum 8 characters with numbers &amp; symbols" minlength="8" required>
        </div>
        <div id="emailError" class="text-danger mb-3"></div>
        <button type="submit" class="btn btn-dark btn-create w-100">Create Account </button>

      </form>
    </div>
      
    <p class="text-center text-secondary mt-4  " >
      Already have an account? <a href="./signin.php" class=" fw-semibold text-decoration-none green-link">Sign In here</a>
    </p>

  </div>



  <?php include_once "footer.php";?>
