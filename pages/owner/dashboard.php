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
$customCSS = "owner.css";
$customJS = "owner.js";
include_once '../navbar.php';
?>

<div class="owner-container container py-4">
  <!-- Top Breadcrumb Subtitle -->
  <div class="owner-subtitle mb-1">OWNER DASHBOARD • RentEase</div>
  <!-- Main Title -->
  <h1 class="owner-title mb-4" id="pageMainHeading">My Properties</h1>
  <!-- Navigation Tabs -->
  <div class="dashboard-tabs d-flex gap-4 mb-4 border-bottom">
    <button class="dash-tab active" id="tabPropertiesBtn" onclick="switchTab('properties')">
      My Properties
    </button>
    <button class="dash-tab" id="tabBookingsBtn" onclick="switchTab('bookings')">
      Booking Requests <span class="badge-count">2</span>
    </button>
  </div>
  <!-- ==================== TAB 1: MY PROPERTIES ==================== -->
  <div id="tabPropertiesSection">
    
    <!-- Controls Bar: Search & Filter Pills -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
      <!-- Search Input -->
      <div class="search-box">
        <input type="text" id="propSearchInput" class="form-control" placeholder="Search by title, city or locality...">
      </div>
      <!-- Filter Pills -->
      <div class="filter-pills-group">
        <button class="filter-pill active" onclick="filterProperties('all', this)">All (3)</button>
        <button class="filter-pill" onclick="filterProperties('approved', this)">Approved (2)</button>
        <button class="filter-pill" onclick="filterProperties('pending', this)">Pending (1)</button>
        <button class="filter-pill" onclick="filterProperties('rejected', this)">Rejected (0)</button>
      </div>
    </div>
    <!-- Add Property Trigger Button -->
    <button class="btn btn-dark btn-add-prop mb-4" id="btnAddPropTrigger" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
      + Add Property
    </button>
    <!-- Sub Heading with Units Badge -->
    <div class="d-flex align-items-center gap-2 mb-3">
      <h3 class="fw-bold fs-5 mb-0">My Properties</h3>
      <span class="units-badge" id="propUnitsBadge">0 UNITS</span>
    </div>
    <!-- Properties Table Container -->
    <div class="table-card">
      <div class="table-responsive">
        <table class="table custom-table mb-0 align-middle">
          <thead>
            <tr>
              <th style="width: 40%;">PROPERTY</th>
              <th style="width: 20%;">TYPE</th>
              <th style="width: 20%;">RENT</th>
              <th style="width: 12%;">APPROVAL</th>
              <th style="width: 8%; text-align: right;"></th>
            </tr>
          </thead>
          <tbody id="propertiesTableBody">
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">Loading your properties...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- ==================== TAB 2: BOOKING REQUESTS ==================== -->
  <div id="tabBookingsSection" class="d-none">
    
    <!-- Filter Pills for Bookings -->
    <div class="mb-4">
      <div class="filter-pills-group">
        <button class="filter-pill active" onclick="filterBookings('pending', this)">Pending (2)</button>
        <button class="filter-pill" onclick="filterBookings('approved', this)">Approved (1)</button>
        <button class="filter-pill" onclick="filterBookings('rejected', this)">Rejected (1)</button>
        <button class="filter-pill" onclick="filterBookings('all', this)">All (4)</button>
      </div>
    </div>
    <!-- Booking Requests Grid -->
    <div class="row g-4" id="bookingsGrid">
      <div class="col-12 text-center py-5 text-muted">Loading booking requests...</div>
    </div>
  </div>
</div>
<!-- ==================== MODAL: ADD PROPERTY ==================== -->
<div class="modal fade" id="addPropertyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content custom-modal-content">
      <div class="modal-body p-4 p-md-5">
        
        <h3 class="fw-bold mb-4 fs-4" id="propModalTitle">Add Property</h3>
        <form id="addPropertyForm">
          <input type="hidden" id="editPropId" name="property_id" value="">
          <!-- Title -->
          <div class="mb-3">
            <label class="form-label" for="addPropTitle">Title</label>
            <input type="text" id="addPropTitle" class="form-control" placeholder="Palm Meadows Villa 4BHK" required>
          </div>
          <!-- Description -->
          <div class="mb-3">
            <label class="form-label" for="addPropDesc">Description</label>
            <textarea id="addPropDesc" class="form-control" rows="4"></textarea>
          </div>
          <!-- City & Locality -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label" for="addPropCity">City</label>
              <input type="text" id="addPropCity" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="addPropLocality">Locality</label>
              <input type="text" id="addPropLocality" class="form-control" required>
            </div>
          </div>
          <!-- Full Address -->
          <div class="mb-3">
            <label class="form-label" for="addPropAddress">Full address</label>
            <input type="text" id="addPropAddress" class="form-control" required>
          </div>
          <!-- BHK Type & Furnishing -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label" for="addPropBhk">BHK type</label>
              <select id="addPropBhk" class="form-select">
                <option value="1RK">1RK</option>
                <option value="1BHK">1BHK</option>
                <option value="2BHK" selected>2BHK</option>
                <option value="3BHK">3BHK</option>
                <option value="4BHK+">4BHK+</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="addPropFurnish">Furnishing</label>
              <select id="addPropFurnish" class="form-select">
                <option value="Unfurnished">Unfurnished</option>
                <option value="Semi-Furnished" selected>Semi-furnished</option>
                <option value="Fully-Furnished">Fully furnished</option>
              </select>
            </div>
          </div>
          <!-- Rent & Deposit -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label" for="addPropRent">Monthly rent (₹)</label>
              <input type="number" id="addPropRent" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="addPropDeposit">Security deposit (₹)</label>
              <input type="number" id="addPropDeposit" class="form-control" required>
            </div>
          </div>
          <!-- Amenities -->
          <div class="mb-3">
            <label class="form-label" for="addPropAmenities">Amenities</label>
            <input type="text" id="addPropAmenities" class="form-control" placeholder="WiFi, Parking, AC, Lift">
          </div>
          <!-- Cover Image Upload Box -->
          <div class="mb-3">
            <label class="form-label">Cover image</label>
            <div class="upload-dropzone" id="coverUploadDropzone" onclick="document.getElementById('coverImageInput').click()">
              <span id="coverUploadText">Click to upload cover image</span>
              <input type="file" id="coverImageInput" class="d-none" accept="image/*">
            </div>
          </div>
          <!-- More Photos Upload Box -->
          <div class="mb-4">
            <label class="form-label">More photos (optional)</label>
            <div class="upload-dropzone" id="morePhotosDropzone" onclick="document.getElementById('morePhotosInput').click()">
              <span id="morePhotosText">Click to upload multiple photos</span>
              <input type="file" id="morePhotosInput" class="d-none" multiple accept="image/*">
            </div>
          </div>
          <!-- Approval Status Notice -->
          <div class="d-flex align-items-center gap-2 mb-4 text-muted small">
            <span>Approval status:</span>
            <span class="badge-status badge-pending">PENDING</span>
            <span>· new listings start as pending</span>
          </div>
          <!-- Buttons -->
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-save-prop" id="btnSaveProp">Save Property</button>
            <button type="button" class="btn btn-cancel-prop" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include_once "../footer.php"; ?>
