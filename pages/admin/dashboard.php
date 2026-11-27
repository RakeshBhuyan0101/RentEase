<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../signin.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../signin.php');
    exit;
}

$customCSS = "admin.css";
$customJS = "admin.js";
include_once '../navbar.php';
?>

<div class="admin-container container py-4">
  <!-- Top Breadcrumb Subtitle -->
  <div class="admin-subtitle mb-1">ADMIN CONSOLE • RentEase</div>
  <!-- Main Title -->
  <h1 class="admin-title mb-4" id="adminMainHeading">Property Approvals</h1>

  <!-- Navigation Tabs -->
  <div class="dashboard-tabs d-flex gap-4 mb-4 border-bottom">
    <button class="dash-tab active" id="tabAdminPropsBtn" onclick="switchAdminTab('properties')">
      Property Approvals <span class="badge-count" id="adminPendingPropBadge">0</span>
    </button>
    <button class="dash-tab" id="tabAdminUsersBtn" onclick="switchAdminTab('users')">
      User Management
    </button>
    <button class="dash-tab" id="tabAdminStatsBtn" onclick="switchAdminTab('stats')">
      Platform Overview
    </button>
  </div>

  <!-- ==================== TAB 1: PROPERTY APPROVALS ==================== -->
  <div id="tabAdminPropertiesSection">
    <!-- Controls Bar: Search & Filter Pills -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div class="search-box">
        <input type="text" id="adminPropSearchInput" class="form-control" placeholder="Search by title, locality or owner...">
      </div>
      <div class="filter-pills-group" id="adminPropFilterPills">
        <button class="filter-pill active" onclick="filterAdminProperties('all', this)">All</button>
        <button class="filter-pill" onclick="filterAdminProperties('pending', this)">Pending</button>
        <button class="filter-pill" onclick="filterAdminProperties('approved', this)">Approved</button>
        <button class="filter-pill" onclick="filterAdminProperties('rejected', this)">Rejected</button>
      </div>
    </div>

    <!-- Properties Table Container -->
    <div class="table-card">
      <div class="table-responsive">
        <table class="table custom-table mb-0 align-middle">
          <thead>
            <tr>
              <th style="width: 35%;">PROPERTY</th>
              <th style="width: 25%;">OWNER</th>
              <th style="width: 15%;">RENT &amp; TYPE</th>
              <th style="width: 10%;">STATUS</th>
              <th style="width: 15%; text-align: right;">ACTIONS</th>
            </tr>
          </thead>
          <tbody id="adminPropertiesTableBody">
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">Loading properties...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ==================== TAB 2: USER MANAGEMENT ==================== -->
  <div id="tabAdminUsersSection" class="d-none">
    <!-- Controls Bar: Search & Filter Pills -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div class="search-box">
        <input type="text" id="adminUserSearchInput" class="form-control" placeholder="Search by name, email or phone...">
      </div>
      <div class="filter-pills-group" id="adminUserFilterPills">
        <button class="filter-pill active" onclick="filterAdminUsers('all', this)">All</button>
        <button class="filter-pill" onclick="filterAdminUsers('tenant', this)">Tenants</button>
        <button class="filter-pill" onclick="filterAdminUsers('owner', this)">Owners</button>
        <button class="filter-pill" onclick="filterAdminUsers('active', this)">Active</button>
        <button class="filter-pill" onclick="filterAdminUsers('blocked', this)">Blocked</button>
      </div>
    </div>

    <!-- Users Table Container -->
    <div class="table-card">
      <div class="table-responsive">
        <table class="table custom-table mb-0 align-middle">
          <thead>
            <tr>
              <th style="width: 35%;">USER</th>
              <th style="width: 20%;">CONTACT</th>
              <th style="width: 15%;">ROLE</th>
              <th style="width: 15%;">STATUS</th>
              <th style="width: 15%; text-align: right;">ACTION</th>
            </tr>
          </thead>
          <tbody id="adminUsersTableBody">
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">Loading users...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ==================== TAB 3: PLATFORM OVERVIEW ==================== -->
  <div id="tabAdminStatsSection" class="d-none">
    <div class="row g-4 mb-4">
      <!-- Total Users -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon avatar-blue">
            <i class="bi bi-people-fill"></i>
          </div>
          <div class="stat-val" id="statTotalUsers">0</div>
          <div class="stat-label">Total Registered Users</div>
          <div class="small text-muted mt-2" id="statUserBreakdown">0 Tenants • 0 Owners</div>
        </div>
      </div>
      <!-- Total Properties -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon avatar-blue">
            <i class="bi bi-buildings-fill"></i>
          </div>
          <div class="stat-val" id="statTotalProps">0</div>
          <div class="stat-label">Total Properties</div>
          <div class="small text-muted mt-2" id="statPropBreakdown">0 Approved • 0 Pending</div>
        </div>
      </div>
      <!-- Pending Approvals -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon" style="background:#fef3c7; color:#b45309;">
            <i class="bi bi-clock-history"></i>
          </div>
          <div class="stat-val" id="statPendingProps">0</div>
          <div class="stat-label">Pending Approval</div>
          <div class="small text-muted mt-2">Requires admin verification</div>
        </div>
      </div>
      <!-- Total Bookings -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon avatar-purple">
            <i class="bi bi-calendar-check-fill"></i>
          </div>
          <div class="stat-val" id="statTotalBookings">0</div>
          <div class="stat-label">Total Booking Requests</div>
          <div class="small text-muted mt-2" id="statBookingBreakdown">0 Pending • 0 Approved</div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php include_once "../footer.php"; ?>
