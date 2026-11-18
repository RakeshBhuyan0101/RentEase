<?php include_once "navbar.php";?>

<!-- Main -->
<main class="container py-4 py-md-5" style="max-width: 760px;">

  <!-- Badges -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <span class="pill rounded-pill px-3 py-1"><i class="bi bi-patch-check me-1"></i>SECURED VIA DIGILOCKER ID
      GATEWAY</span>
    <span class="small-label d-none d-sm-inline"><i class="bi bi-circle-fill text-success me-1"
        style="font-size:6px;"></i>System Operational</span>
  </div>

  <!-- Heading -->
  <h1 class="fw-bold mb-2" style="font-size: 2rem;">Create your RentEase Account</h1>
  <p class="text-muted-custom mb-4" style="max-width: 460px; font-size: 14px;">
    Select your account archetype to access verified urban tenancy or institutional landlord management.
  </p>

  <!-- Step 1 -->
  <div class="d-flex justify-content-between mb-3">
    <span class="small-label">Step 01 • Account Archetype</span>
    <span class="small-label d-none d-sm-inline fw-normal">Exclusive Access Portals</span>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-6">
      <div class="type-card active" id="tenantCard" onclick="selectType('tenant')">
        <div class="p-3">
          <div class="d-flex justify-content-between mb-3">
            <div class="icon-box"><i class="bi bi-key-fill"></i></div>
            <div class="radio-dot"><i class="bi bi-check"></i></div>
          </div>
          <h6 class="fw-bold mb-1">Tenant (Renter)</h6>
          <p class="text-muted-custom mb-0" style="font-size:11px;">Browse verified listings, submit 11-month lease
            bookings, and protect your deposit via RBI-compliant escrow.</p>
        </div>
        <div class="required"><i class="bi bi-person-vcard me-1"></i> Required: Full Name, Mobile (+91 OTP), Email</div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="type-card" id="landlordCard" onclick="selectType('landlord')">
        <div class="p-3">
          <div class="d-flex justify-content-between mb-3">
            <div class="icon-box"><i class="bi bi-building-fill"></i></div>
            <div class="radio-dot"></div>
          </div>
          <h6 class="fw-bold mb-1">Property Owner (Landlord)</h6>
          <p class="text-muted-custom mb-0" style="font-size:11px;">List apartments, verify deeds with Kaveri/BBMP
            integration, review tenant requests, and collect digital rents.</p>
        </div>
        <div class="required"><i class="bi bi-file-earmark-text me-1"></i> Required: PAN / Property PIN, Contact Data
        </div>
      </div>
    </div>
  </div>

  <!-- Admin info -->
  <div class="info-box p-3 mb-4 d-flex gap-2">
    <i class="bi bi-info-circle-fill fs-6"></i>
    <span><strong>Looking for Admin access?</strong> Platform Admin accounts are pre-seeded or provisioned by municipal
      IT credentials and cannot be self-registered through this portal.</span>
  </div>

  <!-- Step 2 form -->
  <form class="form-card p-3 p-md-4 mb-4">
    <div class="d-flex justify-content-between mb-4">
      <span class="small-label">Step 02 • Verification Details</span>
      <span class="small text-success fw-semibold" style="font-size:11px;"><i class="bi bi-lock me-1"></i>256-Bit TLS
        Bank Encrypted</span>
    </div>

    <!-- Name -->
    <div class="mb-4">
      <label class="form-label" for="name">Full Legal Name <span class="text-danger">*</span></label>
      <div class="position-relative">
        <input type="text" id="name" class="form-control" placeholder="e.g. Rohini Anand" required>
        <span class="hint position-absolute top-50 end-0 translate-middle-y me-3 d-none d-sm-inline">As per Aadhaar /
          PAN</span>
      </div>
    </div>

    <!-- Email -->
    <div class="mb-4">
      <label class="form-label" for="email">Corporate or Personal Email <span class="text-danger">*</span></label>
      <input type="email" id="email" class="form-control" placeholder="rohini.anand@domain.in" required>
    </div>

    <!-- Mobile -->
    <div class="mb-4">
      <label class="form-label" for="mobile">Mobile Number <span class="text-danger">*</span></label>
      <div class="input-group">
        <span class="input-group-text">+91</span>
        <input type="tel" id="mobile" class="form-control" placeholder="98765 43210" required>
      </div>
    </div>

    <!-- Password -->
    <div class="mb-3">
      <div class="d-flex justify-content-between">
        <label class="form-label" for="password">Account Password <span class="text-danger">*</span></label>
        <span class="hint">Minimum 8 characters</span>
      </div>
      <div class="position-relative">
        <input type="password" id="password" class="form-control pe-5" placeholder="••••••••••••" minlength="8"
          required>
        <button type="button" class="toggle-pass" onclick="togglePass()"><i class="bi bi-eye-fill fs-6"
            id="eyeIcon"></i></button>
      </div>

    </div>

    <!-- Role isolation -->
    <div class="role-box p-3 mt-4 mb-4 d-flex gap-2">
      <i class="bi bi-shield-check fs-6"></i>
      <span><strong>Role Isolation Active:</strong> Tenant and Landlord portals remain strictly compartmentalized to
        safeguard privacy and transactional parity.</span>
    </div>

    <button type="submit" class="btn btn-main w-100">Complete Registration &amp; Access Dashboard <i
        class="bi bi-arrow-right ms-2"></i></button>

    <p class="text-center small text-muted-custom mt-4 mb-0">
      Already have an active verified account? <a href="#" class="link-dark-u">Sign In here</a>
    </p>
  </form>

</main>

<?php include_once "footer.php";?>