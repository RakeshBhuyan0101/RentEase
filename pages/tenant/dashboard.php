<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../signin.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'tenant') {
    header('Location: ../signin.php');
    exit;
}

$customCSS = "tenant.css";
$customJS = "tenant.js";
include_once '../navbar.php';
?>

<div class="tenant-container container py-4">
  <!-- Top Breadcrumb Subtitle -->
  <div class="tenant-subtitle mb-1">TENANT PORTAL • RentEase</div>
  <!-- Main Title -->
  <h1 class="tenant-title mb-4" id="tenantMainHeading">Explore Properties</h1>

  <!-- Navigation Tabs -->
  <div class="dashboard-tabs d-flex gap-4 mb-4 border-bottom">
    <button class="dash-tab active" id="tabTenantExploreBtn" onclick="switchTenantTab('explore')">
      Explore Properties
    </button>
    <button class="dash-tab" id="tabTenantBookingsBtn" onclick="switchTenantTab('bookings')">
      My Bookings <span class="badge-count" id="tenantBookingCountBadge">0</span>
    </button>
  </div>

  <!-- ==================== TAB 1: EXPLORE PROPERTIES ==================== -->
  <div id="tabTenantExploreSection">
    <!-- Filter Bar Card -->
    <div class="filter-bar-card mb-4">
      <div class="row g-2 align-items-center">
        <!-- Search Keyword -->
        <div class="col-12 col-md-4">
          <input type="text" id="tenantSearchInput" class="form-control" placeholder="Search by title, locality or landmark...">
        </div>
        <!-- City Filter -->
        <div class="col-6 col-md-2">
          <select id="tenantCityFilter" class="form-select">
            <option value="">All Cities</option>
          </select>
        </div>
        <!-- BHK Type Filter -->
        <div class="col-6 col-md-2">
          <select id="tenantBhkFilter" class="form-select">
            <option value="">All BHK</option>
            <option value="1RK">1RK</option>
            <option value="1BHK">1BHK</option>
            <option value="2BHK">2BHK</option>
            <option value="3BHK">3BHK</option>
            <option value="4BHK+">4BHK+</option>
          </select>
        </div>
        <!-- Max Budget Filter -->
        <div class="col-6 col-md-2">
          <select id="tenantBudgetFilter" class="form-select">
            <option value="0">Any Budget</option>
            <option value="15000">Up to ₹15,000</option>
            <option value="25000">Up to ₹25,000</option>
            <option value="50000">Up to ₹50,000</option>
            <option value="100000">Up to ₹1,00,000</option>
          </select>
        </div>
        <!-- Clear Filters Button -->
        <div class="col-6 col-md-2 text-end">
          <button class="btn btn-outline-secondary w-100" id="btnResetFilters" style="border-radius: 8px; font-size: 13.5px; padding: 9px 12px;">
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Properties Count Display -->
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div class="fw-bold fs-6 text-dark" id="tenantResultsText">Showing verified properties</div>
    </div>

    <!-- Properties Grid -->
    <div class="row g-4" id="tenantPropertiesGrid">
      <div class="col-12 text-center py-5 text-muted">Loading properties...</div>
    </div>
  </div>

  <!-- ==================== TAB 2: MY BOOKINGS ==================== -->
  <div id="tabTenantBookingsSection" class="d-none">
    <!-- Filter Pills for Bookings -->
    <div class="mb-4">
      <div class="filter-pills-group" id="tenantBookingFilterPills">
        <button class="filter-pill active" onclick="filterTenantBookings('all', this)">All</button>
        <button class="filter-pill" onclick="filterTenantBookings('pending', this)">Pending</button>
        <button class="filter-pill" onclick="filterTenantBookings('approved', this)">Approved</button>
        <button class="filter-pill" onclick="filterTenantBookings('rejected', this)">Rejected</button>
        <button class="filter-pill" onclick="filterTenantBookings('cancelled', this)">Cancelled</button>
      </div>
    </div>

    <!-- Bookings Grid -->
    <div class="row g-4" id="tenantBookingsGrid">
      <div class="col-12 text-center py-5 text-muted">Loading your bookings...</div>
    </div>
  </div>

</div>

<!-- ==================== MODAL: BOOK PROPERTY ==================== -->
<div class="modal fade" id="bookPropertyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.12);">
      <div class="modal-body p-4">
        
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h4 class="fw-bold mb-1 fs-5">Request to Book</h4>
            <div class="text-muted small" id="modalPropTitle">Property Name</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="p-3 mb-3" style="background:#edf3fb; border-radius: 8px;">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">Monthly Rent:</span>
            <span class="fw-bold fs-5 text-dark" id="modalPropRent">₹0/mo</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-1">
            <span class="text-muted small">Security Deposit:</span>
            <span class="fw-semibold small text-dark" id="modalPropDeposit">₹0</span>
          </div>
        </div>

        <form id="bookPropertyForm">
          <input type="hidden" id="bookPropId" value="">

          <!-- Move-in Date -->
          <div class="mb-3">
            <label class="form-label small fw-semibold text-dark" for="bookMoveInDate">Expected Move-in Date</label>
            <input type="date" id="bookMoveInDate" class="form-control" required style="border-radius: 6px; padding: 10px;">
          </div>

          <!-- Lease Duration -->
          <div class="mb-3">
            <label class="form-label small fw-semibold text-dark" for="bookLeaseMonths">Preferred Lease Duration</label>
            <select id="bookLeaseMonths" class="form-select" style="border-radius: 6px; padding: 10px;">
              <option value="6">6 Months</option>
              <option value="11" selected>11 Months (Standard)</option>
              <option value="24">24 Months</option>
            </select>
          </div>

          <!-- Note / Message to Owner -->
          <div class="mb-4">
            <label class="form-label small fw-semibold text-dark" for="bookMessage">Message to Owner (Optional)</label>
            <textarea id="bookMessage" class="form-control" rows="3" placeholder="Tell the owner a bit about yourself, profession, or shifting preferences..." style="border-radius: 6px; font-size: 13.5px;"></textarea>
          </div>

          <!-- Submit Buttons -->
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold" id="btnSubmitBooking" style="border-radius: 8px; background-color: #2d6a4f; border-color: #2d6a4f;">
              Submit Booking Request
            </button>
            <button type="button" class="btn btn-light py-2 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">
              Cancel
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php include_once "../footer.php"; ?>
