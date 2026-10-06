<?php
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator: Facilities & Dormitories Master Management Portal
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facilities & Dormitories Registry | ATI Reservation Portal</title>
  <meta name="description"
    content="Official Facilities and Dormitory Master Registry and Asset Management for Agricultural Training Institute Central Office.">

  <!-- Google Fonts (Terno with Portal) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/booking.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/my_reservations.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/admin_dashboard.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/admin_facilities.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
</head>

<body class="admin-app-body" data-current-role="<?= htmlspecialchars($currentRole) ?>">

  <div class="admin-layout-container">

    <!-- ==========================================================================
         1. LEFT SIDEBAR NAVIGATION (TERNO ATI BRAND THEME)
         ========================================================================== -->
    <aside class="admin-sidebar" id="adminSidebar">
      <!-- Sidebar Brand Header -->
      <div class="admin-sidebar-header">
        <a href="admin_dashboard.php<?= $isRecommendation ? '?role=recommendation' : '' ?>" class="admin-sidebar-brand">
          <div class="admin-sidebar-logo-box">
            <img src="assets/images/ATI_Logo.png" alt="Agricultural Training Institute Logo" class="admin-sidebar-logo">
          </div>
          <div class="admin-sidebar-brand-text">
            <h3>ATI Portal</h3>
            <p>Central Office</p>
          </div>
        </a>
        <div class="admin-badge-executive">
          <span class="executive-dot"></span>
          Super Admin
        </div>
      </div>

      <!-- Navigation Section -->
      <nav class="admin-sidebar-menu" aria-label="Admin Navigation">
        <div class="sidebar-section-title">CORE COMMAND</div>

        <a href="admin_dashboard.php<?= $isRecommendation ? '?role=recommendation' : '' ?>" class="sidebar-menu-item">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
          </div>
          <span class="menu-label">Dashboard</span>
        </a>

        <a href="admin_approvals.php<?= $isRecommendation ? '?role=recommendation' : '' ?>" class="sidebar-menu-item">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="16" y1="13" x2="8" y2="13"></line>
              <line x1="16" y1="17" x2="8" y2="17"></line>
            </svg>
          </div>
          <span class="menu-label">Approvals Queue</span>
          <span class="sidebar-count-badge amber" id="sidebarPendingCount">5</span>
        </a>

        <a href="admin_reservations.php" class="sidebar-menu-item">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="8" y1="6" x2="21" y2="6"></line>
              <line x1="8" y1="12" x2="21" y2="12"></line>
              <line x1="8" y1="18" x2="21" y2="18"></line>
              <line x1="3" y1="6" x2="3.01" y2="6"></line>
              <line x1="3" y1="12" x2="3.01" y2="12"></line>
              <line x1="3" y1="18" x2="3.01" y2="18"></line>
            </svg>
          </div>
          <span class="menu-label">All Reservations</span>
        </a>

        <div class="sidebar-section-title">ACCESS & GOVERNANCE</div>

        <a href="admin_users.php<?= $isRecommendation ? '?role=recommendation' : '' ?>" class="sidebar-menu-item">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <span class="menu-label">User & Role Access</span>
          <span class="sidebar-count-badge green">18</span>
        </a>

        <div class="sidebar-section-title">FACILITY MANAGEMENT</div>

        <a href="admin_facilities.php<?= $isRecommendation ? '?role=recommendation' : '' ?>" class="sidebar-menu-item active">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
          </div>
          <span class="menu-label">Facilities & Dorms</span>
        </a>

        <a href="admin_schedule.php<?= $isRecommendation ? '?role=recommendation' : '' ?>" class="sidebar-menu-item">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <span class="menu-label">Master Schedule</span>
        </a>

        <a href="admin_audit.php<?= $isRecommendation ? '?role=recommendation' : '' ?>" class="sidebar-menu-item">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 20h9"></path>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
          </div>
          <span class="menu-label">Audit Trail Log</span>
        </a>

        <div class="sidebar-section-title">PORTAL SWITCH</div>

        <a href="home.php" class="sidebar-menu-item client-switch">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
              <polyline points="10 17 15 12 10 7"></polyline>
              <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
          </div>
          <span class="menu-label">Client / Staff Portal</span>
        </a>
      </nav>

      <!-- Sidebar User Profile Footer -->
      <div class="admin-sidebar-footer">
        <div class="sidebar-user-card">
          <div class="sidebar-avatar <?= $isRecommendation ? 'rec' : 'clear' ?>">
            <?= $isRecommendation ? 'RO' : 'DIR' ?>
          </div>
          <div class="sidebar-user-info">
            <h5><?= $isRecommendation ? 'Recommending Officer' : 'Clearance Authority' ?></h5>
            <p><?= $isRecommendation ? 'Chief, Admin Services (Stage 1)' : 'Director IV &bull; Super Admin (Stage 2)' ?></p>
          </div>
        </div>
        <a href="index.php" class="sidebar-signout-btn" title="Sign Out">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span>Sign Out</span>
        </a>
      </div>
    </aside>

    <!-- Overlay for Mobile Sidebar -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar(false)"></div>

    <!-- ==========================================================================
         2. MAIN WORKSPACE AREA (RIGHT SIDE)
         ========================================================================== -->
    <div class="admin-workspace">

      <!-- Executive Top Bar -->
      <header class="admin-topbar">
        <div class="admin-topbar-left">
          <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()" aria-label="Toggle Sidebar Navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>

          <div class="topbar-title-wrap">
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Super Administrator &rsaquo; Asset Management</span>
            <h1 class="topbar-page-title">Facilities & Dormitories Master Registry</h1>
          </div>
        </div>

        <div class="admin-topbar-right">
          <!-- Quick Search -->
          <div class="topbar-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="topbarSearchInput" placeholder="Quick search venues, halls, dorms..." oninput="handleFacilitySearch(this.value)" autocomplete="off">
          </div>

          <!-- Date Badge -->
          <div class="topbar-date-pill">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span id="currentDateDisplay">Monday, Oct 05, 2026</span>
          </div>
        </div>
      </header>

      <!-- Main Scrollable Body (Fluid Full-Width) -->
      <main class="admin-content-container">

        <!-- Header Section -->
        <section class="my-res-header admin-page-header">
          <div class="my-res-header-text">
            <div class="my-res-top-badge admin-gold-badge">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
              <span>Institutional Assets & Accommodation Control</span>
            </div>
            <h2>Facility & Dormitory Asset Management</h2>
            <p>Configure hall capacities, manage dormitory bed inventories, inspect real-time operational availability, and implement administrative maintenance holds across ATI Central Office.</p>
          </div>

          <div class="admin-header-actions">
            <button type="button" class="btn-system-primary" onclick="openAddFacilityModal()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              <span>+ Register New Venue / Room</span>
            </button>

            <button type="button" class="btn-system-secondary" onclick="exportFacilityInventoryCSV()">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span>Export Asset Inventory</span>
            </button>
          </div>
        </section>

        <!-- ==========================================================================
             KPI STAT CARDS (FACILITY & DORM ASSET METRICS)
             ========================================================================== -->
        <section class="my-res-stats-grid admin-kpi-grid" aria-label="Facility Statistics">
          <!-- Total Monitored Facilities -->
          <div class="my-res-stat-card active-filter" onclick="filterFacilitiesByCategory('all')">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiTotalFacilities">6</span>
                <span class="stat-trend positive">100% Operational</span>
              </div>
              <span class="stat-label">Total Monitored Assets</span>
            </div>
          </div>

          <!-- Ready / Available -->
          <div class="my-res-stat-card" onclick="filterFacilitiesByCategory('all')">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiAvailableFacilities">4</span>
                <span class="stat-trend positive">Ready to Book</span>
              </div>
              <span class="stat-label">Available for Reservations</span>
            </div>
          </div>

          <!-- Occupied / In Session -->
          <div class="my-res-stat-card" onclick="filterFacilitiesByCategory('all')">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiOccupiedFacilities">1</span>
                <span class="stat-trend neutral">Active Session</span>
              </div>
              <span class="stat-label">Currently Occupied</span>
            </div>
          </div>

          <!-- Maintenance Holds -->
          <div class="my-res-stat-card" onclick="filterFacilitiesByCategory('maintenance')">
            <div class="stat-icon-box red">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiMaintenanceFacilities">1</span>
                <span class="stat-trend negative">Admin Hold</span>
              </div>
              <span class="stat-label">Under Maintenance</span>
            </div>
          </div>

          <!-- Total Accommodation Capacity -->
          <div class="my-res-stat-card">
            <div class="stat-icon-box gold">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiTotalCapacity" style="font-size: 1.15rem;">445 PAX / 56 Beds</span>
              </div>
              <span class="stat-label">Total Institution Capacity</span>
            </div>
          </div>
        </section>

        <!-- ==========================================================================
             ASSET FILTER & SEARCH CONTROLS
             ========================================================================== -->
        <div class="facility-mgmt-toolbar">
          <div class="facility-search-wrapper">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="facilitySearchInput" placeholder="Filter by venue name, building, equipment, pax..." oninput="handleFacilitySearch(this.value)">
          </div>

          <div class="facility-toolbar-actions">
            <div class="facility-filter-group">
              <button type="button" class="facility-filter-tab active" data-category="all" onclick="filterFacilitiesByCategory('all', this)">All Assets (6)</button>
              <button type="button" class="facility-filter-tab" data-category="halls" onclick="filterFacilitiesByCategory('halls', this)">Training & Function Halls (3)</button>
              <button type="button" class="facility-filter-tab" data-category="dorms" onclick="filterFacilitiesByCategory('dorms', this)">Dormitory Suites (2)</button>
              <button type="button" class="facility-filter-tab" data-category="dining" onclick="filterFacilitiesByCategory('dining', this)">Dining Pavilions (1)</button>
              <button type="button" class="facility-filter-tab" data-category="maintenance" onclick="filterFacilitiesByCategory('maintenance', this)">Maintenance Holds (1)</button>
            </div>

            <!-- View Mode Switcher -->
            <div class="view-mode-toggle" title="Switch Display Layout">
              <button type="button" class="view-mode-btn active" id="btnViewGrid" onclick="setViewMode('grid')">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Grid</span>
              </button>
              <button type="button" class="view-mode-btn" id="btnViewTable" onclick="setViewMode('table')">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                <span>Table</span>
              </button>
            </div>
          </div>
        </div>

        <!-- ==========================================================================
             ASSET CARDS GRID (DEFAULT VIEW)
             ========================================================================== -->
        <div class="facility-cards-grid" id="facilityCardsGrid">

          <!-- Card 1: Serrano Function Hall -->
          <div class="facility-card-item" data-id="serrano" data-category="halls" data-maintenance="false">
            <div class="facility-card-media">
              <img src="assets/images/function_hall.jpg" alt="Serrano Function Hall" class="facility-card-img">
              <span class="facility-type-badge">Function Hall</span>
              <span class="facility-status-pill-overlay state-occupied">Occupied (Oct 06-07)</span>
            </div>
            <div class="facility-card-body">
              <div class="facility-card-header-row">
                <h3 class="facility-card-title">Serrano Function Hall</h3>
              </div>
              <div class="facility-location-tag">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span>Main Training Building, 1st Floor</span>
              </div>
              <div class="facility-specs-grid">
                <div class="spec-cell-item">
                  <span class="spec-label">Capacity</span>
                  <strong class="spec-val spec-capacity-val">120 Participants</strong>
                </div>
                <div class="spec-cell-item">
                  <span class="spec-label">Access Mode</span>
                  <strong class="spec-val">Institutional & Public</strong>
                </div>
              </div>
              <div class="facility-features-wrap">
                <span class="feature-pill-tag">Airconditioned</span>
                <span class="feature-pill-tag">Dual Projectors</span>
                <span class="feature-pill-tag">Sound System</span>
                <span class="feature-pill-tag">Wireless Mics</span>
                <span class="feature-pill-tag">Stage Rostrum</span>
                <span class="feature-pill-tag">Fiber Wi-Fi</span>
              </div>
              <div class="facility-card-footer">
                <div class="facility-maintenance-quick-toggle">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="onMaintenanceSwitchChange('serrano', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span>Maintenance</span>
                </div>
                <div class="card-action-btns">
                  <button type="button" class="btn-card-action" onclick="openEditFacilityModal('serrano')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    <span>Edit</span>
                  </button>
                  <a href="schedule.php" class="btn-card-action" title="View in Master Schedule">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: 4-H Learning Center -->
          <div class="facility-card-item" data-id="four_h" data-category="halls" data-maintenance="false">
            <div class="facility-card-media">
              <img src="assets/images/training_hall.jpg" alt="4-H Learning Center" class="facility-card-img">
              <span class="facility-type-badge">Training Hall</span>
              <span class="facility-status-pill-overlay state-available">Available</span>
            </div>
            <div class="facility-card-body">
              <div class="facility-card-header-row">
                <h3 class="facility-card-title">4-H Learning Center</h3>
              </div>
              <div class="facility-location-tag">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span>4-H Youth Building, 2nd Floor</span>
              </div>
              <div class="facility-specs-grid">
                <div class="spec-cell-item">
                  <span class="spec-label">Capacity</span>
                  <strong class="spec-val spec-capacity-val">80 Participants</strong>
                </div>
                <div class="spec-cell-item">
                  <span class="spec-label">Access Mode</span>
                  <strong class="spec-val">Configurable Modular</strong>
                </div>
              </div>
              <div class="facility-features-wrap">
                <span class="feature-pill-tag">Airconditioned</span>
                <span class="feature-pill-tag">75" 4K Smart TV</span>
                <span class="feature-pill-tag">Moveable Tables</span>
                <span class="feature-pill-tag">Whiteboards</span>
                <span class="feature-pill-tag">PA System</span>
              </div>
              <div class="facility-card-footer">
                <div class="facility-maintenance-quick-toggle">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="onMaintenanceSwitchChange('four_h', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span>Maintenance</span>
                </div>
                <div class="card-action-btns">
                  <button type="button" class="btn-card-action" onclick="openEditFacilityModal('four_h')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    <span>Edit</span>
                  </button>
                  <a href="schedule.php" class="btn-card-action" title="View in Master Schedule">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 3: Executive Boardroom -->
          <div class="facility-card-item" data-id="boardroom" data-category="halls" data-maintenance="false">
            <div class="facility-card-media">
              <img src="assets/images/boardroom.jpg" alt="Executive Boardroom" class="facility-card-img">
              <span class="facility-type-badge">Boardroom</span>
              <span class="facility-status-pill-overlay state-available">Available</span>
            </div>
            <div class="facility-card-body">
              <div class="facility-card-header-row">
                <h3 class="facility-card-title">Executive Boardroom</h3>
              </div>
              <div class="facility-location-tag">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span>Director's Wing, 3rd Floor</span>
              </div>
              <div class="facility-specs-grid">
                <div class="spec-cell-item">
                  <span class="spec-label">Capacity</span>
                  <strong class="spec-val spec-capacity-val">25 PAX</strong>
                </div>
                <div class="spec-cell-item">
                  <span class="spec-label">Access Mode</span>
                  <strong class="spec-val">Executive / Director</strong>
                </div>
              </div>
              <div class="facility-features-wrap">
                <span class="feature-pill-tag">Hybrid Teleconferencing</span>
                <span class="feature-pill-tag">PTZ Cameras</span>
                <span class="feature-pill-tag">Executive Leather</span>
                <span class="feature-pill-tag">Private Restroom</span>
                <span class="feature-pill-tag">VIP Holding</span>
              </div>
              <div class="facility-card-footer">
                <div class="facility-maintenance-quick-toggle">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="onMaintenanceSwitchChange('boardroom', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span>Maintenance</span>
                </div>
                <div class="card-action-btns">
                  <button type="button" class="btn-card-action" onclick="openEditFacilityModal('boardroom')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    <span>Edit</span>
                  </button>
                  <a href="schedule.php" class="btn-card-action" title="View in Master Schedule">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 4: ATI Mess Hall & Dining Pavilion -->
          <div class="facility-card-item" data-id="mess_hall" data-category="dining" data-maintenance="false">
            <div class="facility-card-media">
              <img src="assets/images/mess_hall.jpg" alt="ATI Mess Hall" class="facility-card-img">
              <span class="facility-type-badge">Dining Pavilion</span>
              <span class="facility-status-pill-overlay state-available">Available</span>
            </div>
            <div class="facility-card-body">
              <div class="facility-card-header-row">
                <h3 class="facility-card-title">ATI Mess Hall & Dining Pavilion</h3>
              </div>
              <div class="facility-location-tag">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span>Services Annex, Ground Floor</span>
              </div>
              <div class="facility-specs-grid">
                <div class="spec-cell-item">
                  <span class="spec-label">Capacity</span>
                  <strong class="spec-val spec-capacity-val">150 Diners</strong>
                </div>
                <div class="spec-cell-item">
                  <span class="spec-label">Access Mode</span>
                  <strong class="spec-val">Buffet & Catering</strong>
                </div>
              </div>
              <div class="facility-features-wrap">
                <span class="feature-pill-tag">Buffet Counters</span>
                <span class="feature-pill-tag">Ceiling Fans</span>
                <span class="feature-pill-tag">Water Stations</span>
                <span class="feature-pill-tag">Handwashing Sinks</span>
                <span class="feature-pill-tag">Sound System</span>
              </div>
              <div class="facility-card-footer">
                <div class="facility-maintenance-quick-toggle">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="onMaintenanceSwitchChange('mess_hall', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span>Maintenance</span>
                </div>
                <div class="card-action-btns">
                  <button type="button" class="btn-card-action" onclick="openEditFacilityModal('mess_hall')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    <span>Edit</span>
                  </button>
                  <a href="schedule.php" class="btn-card-action" title="View in Master Schedule">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 5: Dormitory Suite A -->
          <div class="facility-card-item" data-id="dorm_a" data-category="dorms" data-maintenance="false">
            <div class="facility-card-media">
              <img src="assets/images/dormitory.jpg" alt="Dormitory Suite A" class="facility-card-img">
              <span class="facility-type-badge">Dormitory (Male)</span>
              <span class="facility-status-pill-overlay state-occupied">Reserved (Oct 15-18)</span>
            </div>
            <div class="facility-card-body">
              <div class="facility-card-header-row">
                <h3 class="facility-card-title">Dormitory Suite A (Male Wing)</h3>
              </div>
              <div class="facility-location-tag">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span>Dormitory Building, Wing A (2nd Floor)</span>
              </div>
              <div class="facility-specs-grid">
                <div class="spec-cell-item">
                  <span class="spec-label">Capacity</span>
                  <strong class="spec-val spec-capacity-val">24 Beds (6 Quads)</strong>
                </div>
                <div class="spec-cell-item">
                  <span class="spec-label">Access Mode</span>
                  <strong class="spec-val">Overnight Delegates</strong>
                </div>
              </div>
              <div class="facility-features-wrap">
                <span class="feature-pill-tag">Spring Mattresses</span>
                <span class="feature-pill-tag">En-suite Bathroom</span>
                <span class="feature-pill-tag">Hot Water Showers</span>
                <span class="feature-pill-tag">Steel Lockers</span>
                <span class="feature-pill-tag">Study Lounge</span>
                <span class="feature-pill-tag">Free Wi-Fi</span>
              </div>
              <div class="facility-card-footer">
                <div class="facility-maintenance-quick-toggle">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="onMaintenanceSwitchChange('dorm_a', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span>Maintenance</span>
                </div>
                <div class="card-action-btns">
                  <button type="button" class="btn-card-action" onclick="openEditFacilityModal('dorm_a')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    <span>Edit</span>
                  </button>
                  <a href="schedule.php" class="btn-card-action" title="View in Master Schedule">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 6: Dormitory Suite B -->
          <div class="facility-card-item" data-id="dorm_b" data-category="dorms" data-maintenance="true">
            <div class="facility-card-media">
              <img src="assets/images/dormitory.jpg" alt="Dormitory Suite B" class="facility-card-img">
              <span class="facility-type-badge">Dormitory (Female)</span>
              <span class="facility-status-pill-overlay state-maintenance">Under Maintenance</span>
            </div>
            <div class="facility-card-body">
              <div class="facility-card-header-row">
                <h3 class="facility-card-title">Dormitory Suite B (Female Wing)</h3>
              </div>
              <div class="facility-location-tag">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span>Dormitory Building, Wing B (3rd Floor)</span>
              </div>
              <div class="facility-specs-grid">
                <div class="spec-cell-item">
                  <span class="spec-label">Capacity</span>
                  <strong class="spec-val spec-capacity-val">32 Beds (8 Quads)</strong>
                </div>
                <div class="spec-cell-item">
                  <span class="spec-label">Access Mode</span>
                  <strong class="spec-val">Overnight Delegates</strong>
                </div>
              </div>
              <div class="facility-features-wrap">
                <span class="feature-pill-tag">Spring Mattresses</span>
                <span class="feature-pill-tag">En-suite Bathroom</span>
                <span class="feature-pill-tag">Hot Water Showers</span>
                <span class="feature-pill-tag">Steel Lockers</span>
                <span class="feature-pill-tag">24/7 Security</span>
              </div>
              <div class="facility-card-footer">
                <div class="facility-maintenance-quick-toggle">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" checked onchange="onMaintenanceSwitchChange('dorm_b', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span>Maintenance</span>
                </div>
                <div class="card-action-btns">
                  <button type="button" class="btn-card-action" onclick="openEditFacilityModal('dorm_b')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    <span>Edit</span>
                  </button>
                  <a href="schedule.php" class="btn-card-action" title="View in Master Schedule">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                  </a>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- ==========================================================================
             FACILITY TABLE VIEW (ALTERNATIVE VIEW)
             ========================================================================== -->
        <div class="facility-table-view-container" id="facilityTableView">
          <div class="admin-table-wrapper">
            <table class="admin-table" id="facilityMasterTable">
              <thead>
                <tr>
                  <th>Facility / Venue Name</th>
                  <th>Category</th>
                  <th>Capacity</th>
                  <th>Featured Amenities</th>
                  <th>Maintenance Hold</th>
                  <th>Status</th>
                  <th style="text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody id="facilityTableBody">
                <!-- Row 1 -->
                <tr data-id="serrano" data-category="halls" data-maintenance="false">
                  <td>
                    <div class="facility-table-info-cell">
                      <img src="assets/images/function_hall.jpg" alt="Serrano Function Hall" class="facility-table-thumb">
                      <div class="facility-table-info-text">
                        <strong>Serrano Function Hall</strong>
                        <span>Main Training Building, 1st Floor</span>
                      </div>
                    </div>
                  </td>
                  <td><span class="event-type-chip approval">FUNCTION HALL</span></td>
                  <td><strong>120 PAX</strong></td>
                  <td><span class="feature-pill-tag">Projectors</span> <span class="feature-pill-tag">Sound System</span> <span class="feature-pill-tag">Wi-Fi</span></td>
                  <td>
                    <label class="switch-toggle">
                      <input type="checkbox" onchange="onMaintenanceSwitchChange('serrano', this.checked)">
                      <span class="slider-round"></span>
                    </label>
                  </td>
                  <td><span class="status-badge-chip chip-pending">Occupied (Oct 06-07)</span></td>
                  <td style="text-align: right;">
                    <button type="button" class="btn-card-action" onclick="openEditFacilityModal('serrano')">Configure</button>
                  </td>
                </tr>

                <!-- Row 2 -->
                <tr data-id="four_h" data-category="halls" data-maintenance="false">
                  <td>
                    <div class="facility-table-info-cell">
                      <img src="assets/images/training_hall.jpg" alt="4-H Learning Center" class="facility-table-thumb">
                      <div class="facility-table-info-text">
                        <strong>4-H Learning Center</strong>
                        <span>4-H Youth Building, 2nd Floor</span>
                      </div>
                    </div>
                  </td>
                  <td><span class="event-type-chip approval">TRAINING HALL</span></td>
                  <td><strong>80 PAX</strong></td>
                  <td><span class="feature-pill-tag">75" 4K TV</span> <span class="feature-pill-tag">Modular Tables</span></td>
                  <td>
                    <label class="switch-toggle">
                      <input type="checkbox" onchange="onMaintenanceSwitchChange('four_h', this.checked)">
                      <span class="slider-round"></span>
                    </label>
                  </td>
                  <td><span class="status-badge-chip chip-confirmed">Available</span></td>
                  <td style="text-align: right;">
                    <button type="button" class="btn-card-action" onclick="openEditFacilityModal('four_h')">Configure</button>
                  </td>
                </tr>

                <!-- Row 3 -->
                <tr data-id="boardroom" data-category="halls" data-maintenance="false">
                  <td>
                    <div class="facility-table-info-cell">
                      <img src="assets/images/boardroom.jpg" alt="Executive Boardroom" class="facility-table-thumb">
                      <div class="facility-table-info-text">
                        <strong>Executive Boardroom</strong>
                        <span>Director's Wing, 3rd Floor</span>
                      </div>
                    </div>
                  </td>
                  <td><span class="event-type-chip approval">BOARDROOM</span></td>
                  <td><strong>25 PAX</strong></td>
                  <td><span class="feature-pill-tag">Hybrid Teleconf</span> <span class="feature-pill-tag">PTZ Cameras</span></td>
                  <td>
                    <label class="switch-toggle">
                      <input type="checkbox" onchange="onMaintenanceSwitchChange('boardroom', this.checked)">
                      <span class="slider-round"></span>
                    </label>
                  </td>
                  <td><span class="status-badge-chip chip-confirmed">Available</span></td>
                  <td style="text-align: right;">
                    <button type="button" class="btn-card-action" onclick="openEditFacilityModal('boardroom')">Configure</button>
                  </td>
                </tr>

                <!-- Row 4 -->
                <tr data-id="mess_hall" data-category="dining" data-maintenance="false">
                  <td>
                    <div class="facility-table-info-cell">
                      <img src="assets/images/mess_hall.jpg" alt="ATI Mess Hall" class="facility-table-thumb">
                      <div class="facility-table-info-text">
                        <strong>ATI Mess Hall & Dining Pavilion</strong>
                        <span>Services Annex, Ground Floor</span>
                      </div>
                    </div>
                  </td>
                  <td><span class="event-type-chip maintenance">DINING HALL</span></td>
                  <td><strong>150 PAX</strong></td>
                  <td><span class="feature-pill-tag">Buffet Counters</span> <span class="feature-pill-tag">Water Stations</span></td>
                  <td>
                    <label class="switch-toggle">
                      <input type="checkbox" onchange="onMaintenanceSwitchChange('mess_hall', this.checked)">
                      <span class="slider-round"></span>
                    </label>
                  </td>
                  <td><span class="status-badge-chip chip-confirmed">Available</span></td>
                  <td style="text-align: right;">
                    <button type="button" class="btn-card-action" onclick="openEditFacilityModal('mess_hall')">Configure</button>
                  </td>
                </tr>

                <!-- Row 5 -->
                <tr data-id="dorm_a" data-category="dorms" data-maintenance="false">
                  <td>
                    <div class="facility-table-info-cell">
                      <img src="assets/images/dormitory.jpg" alt="Dormitory Suite A" class="facility-table-thumb">
                      <div class="facility-table-info-text">
                        <strong>Dormitory Suite A (Male Wing)</strong>
                        <span>Dormitory Building, Wing A (2nd Floor)</span>
                      </div>
                    </div>
                  </td>
                  <td><span class="event-type-chip schedule">DORMITORY</span></td>
                  <td><strong>24 Beds</strong><br><small style="color: #637f6f;">24 PAX</small></td>
                  <td><span class="feature-pill-tag">En-suite Bath</span> <span class="feature-pill-tag">Hot Showers</span></td>
                  <td>
                    <label class="switch-toggle">
                      <input type="checkbox" onchange="onMaintenanceSwitchChange('dorm_a', this.checked)">
                      <span class="slider-round"></span>
                    </label>
                  </td>
                  <td><span class="status-badge-chip chip-pending">Reserved (Oct 15-18)</span></td>
                  <td style="text-align: right;">
                    <button type="button" class="btn-card-action" onclick="openEditFacilityModal('dorm_a')">Configure</button>
                  </td>
                </tr>

                <!-- Row 6 -->
                <tr data-id="dorm_b" data-category="dorms" data-maintenance="true">
                  <td>
                    <div class="facility-table-info-cell">
                      <img src="assets/images/dormitory.jpg" alt="Dormitory Suite B" class="facility-table-thumb">
                      <div class="facility-table-info-text">
                        <strong>Dormitory Suite B (Female Wing)</strong>
                        <span>Dormitory Building, Wing B (3rd Floor)</span>
                      </div>
                    </div>
                  </td>
                  <td><span class="event-type-chip schedule">DORMITORY</span></td>
                  <td><strong>32 Beds</strong><br><small style="color: #637f6f;">32 PAX</small></td>
                  <td><span class="feature-pill-tag">En-suite Bath</span> <span class="feature-pill-tag">Steel Lockers</span></td>
                  <td>
                    <label class="switch-toggle">
                      <input type="checkbox" checked onchange="onMaintenanceSwitchChange('dorm_b', this.checked)">
                      <span class="slider-round"></span>
                    </label>
                  </td>
                  <td><span class="status-badge-chip chip-declined">Maintenance Hold</span></td>
                  <td style="text-align: right;">
                    <button type="button" class="btn-card-action" onclick="openEditFacilityModal('dorm_b')">Configure</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: EDIT FACILITY SPECIFICATIONS
       ========================================================================== -->
  <div class="admin-modal-overlay" id="editFacilityModal" style="display: none;">
    <div class="admin-modal-card">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4>Configure Facility Specifications</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeEditFacilityModal()">&times;</button>
      </div>

      <div class="admin-modal-body">
        <form id="editFacilityForm" onsubmit="saveFacilityChanges(event)">
          <input type="hidden" id="editFacilityId">

          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Official Facility / Room Name</label>
              <input type="text" id="editFacilityName" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Asset Category</label>
              <select id="editFacilityCategory" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
                <option value="halls">Training & Function Hall</option>
                <option value="dorms">Dormitory Suite</option>
                <option value="dining">Dining Facility</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Participant Capacity (PAX)</label>
              <input type="number" id="editFacilityPax" min="1" max="1000" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Total Bed Inventory (For Dorms)</label>
              <input type="number" id="editFacilityBeds" min="0" max="200" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>
          </div>

          <!-- Facility Photograph Upload & Selector -->
          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Facility Featured Photograph</label>
            <div style="display: flex; gap: 1rem; align-items: center; background: #fbfdfc; border: 1.5px dashed #c9ded0; border-radius: 10px; padding: 0.85rem 1rem;">
              <div style="position: relative; width: 100px; height: 70px; border-radius: 8px; overflow: hidden; border: 1.5px solid #dce8e0; flex-shrink: 0; background: #eaf1ec;">
                <img id="editFacilityImgPreview" src="assets/images/function_hall.jpg" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
              <div style="flex: 1;">
                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.4rem; flex-wrap: wrap;">
                  <label for="editFacilityFileInput" class="btn-card-action" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.78rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <span>Upload Local File</span>
                  </label>
                  <input type="file" id="editFacilityFileInput" accept="image/*" style="display: none;" onchange="handleFacilityFilePreview(this, 'editFacilityImgPreview', 'editFacilityImageSrc')">
                  <input type="hidden" id="editFacilityImageSrc" value="">

                  <select id="editFacilityPresetSelect" onchange="applyPresetImage(this.value, 'editFacilityImgPreview', 'editFacilityImageSrc')" style="padding: 0.38rem 0.65rem; border: 1.2px solid #dce8e0; border-radius: 6px; font-family: inherit; font-size: 0.78rem; color: #2e4738;">
                    <option value="">-- Choose official photo preset --</option>
                    <option value="assets/images/function_hall.jpg">Function Hall (Serrano)</option>
                    <option value="assets/images/training_hall.jpg">Training Hall (4-H Center)</option>
                    <option value="assets/images/boardroom.jpg">Executive Boardroom</option>
                    <option value="assets/images/dormitory.jpg">Dormitory Suites</option>
                    <option value="assets/images/mess_hall.jpg">Dining Pavilion / Mess Hall</option>
                    <option value="assets/images/ATI_building.jpg">ATI Main Building Facade</option>
                  </select>
                </div>
                <small style="color: #657b6f; font-size: 0.74rem;">Select local photo from your device or pick an official ATI library preset.</small>
              </div>
            </div>
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Building & Floor Location</label>
            <input type="text" id="editFacilityLocation" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Standard Amenities & Equipment (Comma-separated)</label>
            <input type="text" id="editFacilityFeatures" placeholder="Airconditioned, Projector, Sound System, Wi-Fi" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
          </div>

          <div class="form-group-decline" style="margin-bottom: 1.25rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Administrative Operational Notes</label>
            <textarea id="editFacilityNotes" rows="2" placeholder="Special restrictions, key custodians, or technical equipment remarks..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;"></textarea>
          </div>

          <div class="admin-modal-footer" style="padding: 1rem 0 0 0; background: none; border-top: 1px solid #edf3ef;">
            <button type="button" class="btn-system-secondary" onclick="closeEditFacilityModal()">Cancel</button>
            <button type="submit" class="btn-system-primary">Save Specifications</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: REGISTER NEW FACILITY
       ========================================================================== -->
  <div class="admin-modal-overlay" id="addFacilityModal" style="display: none;">
    <div class="admin-modal-card">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4>Register New Institutional Facility</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeAddFacilityModal()">&times;</button>
      </div>

      <div class="admin-modal-body">
        <form id="addFacilityForm" onsubmit="handleAddNewFacility(event)">
          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Venue / Room Title</label>
              <input type="text" id="addFacilityName" placeholder="e.g. Training Hall B (Agri-Fisheries)" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Asset Category</label>
              <select id="addFacilityCategory" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
                <option value="halls">Training & Function Hall</option>
                <option value="dorms">Dormitory Suite</option>
                <option value="dining">Dining Facility</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Participant Capacity (PAX)</label>
              <input type="number" id="addFacilityPax" min="1" max="1000" placeholder="50" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Bed Capacity (If Dormitory)</label>
              <input type="number" id="addFacilityBeds" min="0" max="200" placeholder="0" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>
          </div>

          <!-- Add Facility Photograph Upload & Selector -->
          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Facility Featured Photograph</label>
            <div style="display: flex; gap: 1rem; align-items: center; background: #fbfdfc; border: 1.5px dashed #c9ded0; border-radius: 10px; padding: 0.85rem 1rem;">
              <div style="position: relative; width: 100px; height: 70px; border-radius: 8px; overflow: hidden; border: 1.5px solid #dce8e0; flex-shrink: 0; background: #eaf1ec;">
                <img id="addFacilityImgPreview" src="assets/images/function_hall.jpg" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
              <div style="flex: 1;">
                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.4rem; flex-wrap: wrap;">
                  <label for="addFacilityFileInput" class="btn-card-action" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.78rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <span>Upload Local File</span>
                  </label>
                  <input type="file" id="addFacilityFileInput" accept="image/*" style="display: none;" onchange="handleFacilityFilePreview(this, 'addFacilityImgPreview', 'addFacilityImageSrc')">
                  <input type="hidden" id="addFacilityImageSrc" value="assets/images/function_hall.jpg">

                  <select id="addFacilityPresetSelect" onchange="applyPresetImage(this.value, 'addFacilityImgPreview', 'addFacilityImageSrc')" style="padding: 0.38rem 0.65rem; border: 1.2px solid #dce8e0; border-radius: 6px; font-family: inherit; font-size: 0.78rem; color: #2e4738;">
                    <option value="assets/images/function_hall.jpg">Function Hall (Serrano)</option>
                    <option value="assets/images/training_hall.jpg">Training Hall (4-H Center)</option>
                    <option value="assets/images/boardroom.jpg">Executive Boardroom</option>
                    <option value="assets/images/dormitory.jpg">Dormitory Suites</option>
                    <option value="assets/images/mess_hall.jpg">Dining Pavilion / Mess Hall</option>
                    <option value="assets/images/ATI_building.jpg">ATI Main Building Facade</option>
                  </select>
                </div>
                <small style="color: #657b6f; font-size: 0.74rem;">Upload image or select photo preset.</small>
              </div>
            </div>
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Building Location / Floor</label>
            <input type="text" id="addFacilityLocation" placeholder="e.g. Training Annex Building, 2nd Floor" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
          </div>

          <div class="form-group-decline" style="margin-bottom: 1.25rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Included Equipment (Comma-separated)</label>
            <input type="text" id="addFacilityFeatures" placeholder="Airconditioned, Smart TV, Whiteboard, High-speed Wi-Fi" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
          </div>

          <div class="admin-modal-footer" style="padding: 1rem 0 0 0; background: none; border-top: 1px solid #edf3ef;">
            <button type="button" class="btn-system-secondary" onclick="closeAddFacilityModal()">Cancel</button>
            <button type="submit" class="btn-system-primary">Register Asset</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: MAINTENANCE HOLD JUSTIFICATION
       ========================================================================== -->
  <div class="admin-modal-overlay" id="maintenanceModal" style="display: none;">
    <div class="admin-modal-card sm">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4 style="color: #b91c1c;">Activate Administrative Maintenance Hold</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeMaintenanceModal()">&times;</button>
      </div>

      <div class="admin-modal-body">
        <div class="maintenance-alert-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
          <p>
            Placing <strong id="maintModalFacilityName">Facility</strong> on Maintenance Hold will automatically block reservation submissions on the public booking portal and Master Schedule.
          </p>
        </div>

        <div class="form-group-decline" style="margin-bottom: 0.85rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Maintenance Ground / Reason</label>
          <select id="maintReasonSelect" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            <option value="Airconditioning Servicing & Preventive Maintenance">Airconditioning Servicing & Preventive Maintenance</option>
            <option value="Audio-Visual / Electrical Wiring Upgrade">Audio-Visual / Electrical Wiring Upgrade</option>
            <option value="Deep Sanitization & Pest Fumigation">Deep Sanitization & Pest Fumigation</option>
            <option value="Floor Waxing & Architectural Refurbishment">Floor Waxing & Architectural Refurbishment</option>
            <option value="Institutional Executive Discretion">Institutional Executive Discretion</option>
          </select>
        </div>

        <div class="form-group-decline" style="margin-bottom: 0.85rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Estimated Completion Date</label>
          <input type="date" id="maintEstResumeDate" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
        </div>

        <div class="form-group-decline" style="margin-bottom: 1.25rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Work Order Remarks (Optional)</label>
          <textarea id="maintRemarksInput" rows="2" placeholder="e.g. Work Order #2026-44 issued to General Services Unit..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;"></textarea>
        </div>

        <div class="admin-modal-footer" style="padding: 1rem 0 0 0; background: none; border-top: 1px solid #edf3ef;">
          <button type="button" class="btn-system-secondary" onclick="closeMaintenanceModal()">Cancel</button>
          <button type="button" class="btn-confirm-decline" onclick="confirmMaintenanceHold()">Activate Maintenance Hold</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Container -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- Scripts -->
  <script src="js/admin_facilities.js?v=<?php echo time(); ?>"></script>
</body>

</html>
