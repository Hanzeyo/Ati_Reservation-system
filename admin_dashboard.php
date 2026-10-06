<?php
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator Executive Dashboard (Sidebar Layout - Terno with ATI Portal)
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Dashboard | ATI Facility Reservation Portal</title>
  <meta name="description"
    content="Super Administrator Executive Dashboard for Agricultural Training Institute. Review reservations, oversee facility occupancy, and manage institutional bookings.">

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
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
</head>

<body class="admin-app-body">

  <div class="admin-layout-container">

    <!-- ==========================================================================
         1. LEFT SIDEBAR NAVIGATION (TERNO ATI BRAND THEME)
         ========================================================================== -->
    <aside class="admin-sidebar" id="adminSidebar">
      <!-- Sidebar Brand Header -->
      <div class="admin-sidebar-header">
        <a href="admin_dashboard.php" class="admin-sidebar-brand">
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

        <a href="admin_dashboard.php" class="sidebar-menu-item active">
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

        <a href="admin_users.php" class="sidebar-menu-item">
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

        <a href="admin_facilities.php" class="sidebar-menu-item">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
          </div>
          <span class="menu-label">Facilities & Dorms</span>
        </a>

        <a href="admin_schedule.php" class="sidebar-menu-item">
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

        <a href="admin_audit.php" class="sidebar-menu-item">
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
            <p><?= $isRecommendation ? 'Stage 1: Admin & Logistics' : 'Stage 2: Directorate Clearance' ?></p>
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
          <!-- Toggle Sidebar Button -->
          <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()" aria-label="Toggle Sidebar Navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>

          <div class="topbar-title-wrap">
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Super Administrator</span>
            <h1 class="topbar-page-title">Executive Command Center</h1>
          </div>
        </div>

        <div class="admin-topbar-right">


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

      <!-- Main Scrollable Body (TERNO WITH PORTAL) -->
      <main class="admin-content-container">

        <!-- Header Section -->
        <section class="my-res-header admin-page-header">
          <div class="my-res-header-text">
            <div class="my-res-top-badge admin-gold-badge">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <span>Director IV Executive Administration</span>
            </div>
            <h2>Facility & Dormitory Reservation Overview</h2>
            <p>Review and act on pending institutional requests, oversee auditorium & dormitory occupancy, and maintain live facility schedules for ATI Central Office.</p>
          </div>

          <div class="admin-header-actions">
            <button type="button" class="btn-system-secondary" id="btnExportData" onclick="exportReservationData()">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span>Export Official Report</span>
            </button>
          </div>
        </section>

        <!-- ==========================================================================
             KPI STAT CARDS (TERNO WITH SYSTEM DESIGN)
             ========================================================================== -->
        <section class="my-res-stats-grid admin-kpi-grid" aria-label="Executive KPI Overview">
          <!-- Total Reservations -->
          <div class="my-res-stat-card active-filter" data-kpi="total" onclick="applyTabFilter('all', true)" title="Click to view all reservations">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiTotal">128</span>
                <span class="stat-trend positive">+14% MoM</span>
              </div>
              <span class="stat-label">Total Reservations</span>
            </div>
          </div>

          <!-- Pending Approvals -->
          <div class="my-res-stat-card admin-stat-highlight" data-kpi="pending" onclick="applyTabFilter('pending', true)" title="Click to filter pending approval requests">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiPending">5</span>
                <span class="stat-pill-urgent">Needs Action</span>
              </div>
              <span class="stat-label">Pending Approval Requests</span>
            </div>
          </div>

          <!-- Venue Occupancy -->
          <div class="my-res-stat-card" data-kpi="occupancy" id="cardOccupancyKpi" onclick="openOccupancyModal()" title="Click to open occupancy analytics & graphs">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiOccupancy">76.4%</span>
                <span class="stat-trend neutral">168 / 220 PAX</span>
              </div>
              <span class="stat-label">Venue & Dorm Occupancy</span>
            </div>
          </div>

          <!-- Monthly Revenue / Utilization -->
          <div class="my-res-stat-card" data-kpi="revenue" onclick="showAdminToast('October 2026 Estimated Utilization: ₱284,500 across all institutional facilities.', 'info')" title="Click to view monthly utilization summary">
            <div class="stat-icon-box emerald">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M8.5 17V7h4.5a3 3 0 0 1 0 6H8.5"></path>
                <line x1="6" y1="9.5" x2="15.5" y2="9.5"></line>
                <line x1="6" y1="12" x2="15.5" y2="12"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiRevenue">₱284,500</span>
                <span class="stat-trend positive">Oct 2026</span>
              </div>
              <span class="stat-label">Estimated Monthly Utilization</span>
            </div>
          </div>
        </section>

        <!-- ==========================================================================
             FILTER & SEARCH TOOLBAR (MATCHED WITH my_reservations.php)
             ========================================================================== -->
        <div class="my-res-toolbar-card admin-toolbar-card" id="reservationsSection">
          <!-- Filter Tabs -->
          <div class="filter-tabs-group" role="tablist">
            <button type="button" class="filter-tab-btn active" data-filter="all" onclick="applyTabFilter('all')">
              <span>All Requests</span>
              <span class="tab-count-pill" id="tabCountAll">128</span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="pending" onclick="applyTabFilter('pending')">
              <span>Pending Review</span>
              <span class="tab-count-pill tab-count-pending" id="tabCountPending">5</span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="approved" onclick="applyTabFilter('approved')">
              <span>Approved</span>
              <span class="tab-count-pill" id="tabCountApproved">114</span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="halls" onclick="applyTabFilter('halls')">
              <span>Function Halls</span>
              <span class="tab-count-pill" id="tabCountHalls">80</span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="dorms" onclick="applyTabFilter('dorms')">
              <span>Dormitories</span>
              <span class="tab-count-pill" id="tabCountDorms">48</span>
            </button>
          </div>

          <!-- Controls Right -->
          <div class="toolbar-controls-right">
            <div class="search-box-wrap admin-search-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <input type="text" id="adminSearchInput" class="search-input" placeholder="Search applicant, agency, venue, or ref #..."
                oninput="handleAdminSearch(this.value)" autocomplete="off">
              <button type="button" class="search-clear-btn" id="adminSearchClear" onclick="clearAdminSearch()" title="Clear search" style="display: none;" aria-label="Clear search">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <div class="select-filter-wrap">
              <select id="venueFilterSelect" class="venue-filter-select" onchange="handleVenueSelectFilter(this.value)">
                <option value="all">All Venues & Rooms</option>
                <option value="Serrano Hall">Serrano Hall</option>
                <option value="4-H Learning Center">4-H Learning Center</option>
                <option value="Executive Boardroom">Executive Boardroom</option>
                <option value="Training Hall A">Training Hall A</option>
                <option value="Dormitory Suite">Dormitory Suites</option>
              </select>
            </div>
          </div>
        </div>

        <!-- ==========================================================================
             RESERVATIONS QUEUE TABLE (SYSTEM CARD DESIGN)
             ========================================================================== -->
        <div class="admin-table-card">
          <div class="admin-card-header">
            <div class="admin-card-title-group">
              <h3>Action & Routing Approval Queue</h3>
              <p>Review submissions, verify attachments, and execute official Director IV approvals.</p>
            </div>
            <div class="admin-card-header-badge">
              <span class="indicator-live-dot"></span>
              Live Institutional Feed
            </div>
          </div>

          <div class="admin-table-responsive">
            <table class="admin-data-table" id="adminReservationsTable">
              <thead>
                <tr>
                  <th>Reference & Date</th>
                  <th>Applicant & Organization</th>
                  <th>Requested Facility</th>
                  <th>Schedule / Duration</th>
                  <th>Attendees (PAX)</th>
                  <th>Current Status</th>
                  <th style="text-align: right;">Executive Actions</th>
                </tr>
              </thead>
              <tbody id="adminTableBody">
                <!-- Empty search state row -->
                <tr id="adminNoResultsRow" style="display: none;">
                  <td colspan="7" class="admin-empty-table-state">
                    <div class="empty-state-content">
                      <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#8ca394" stroke-width="1.8">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                      </svg>
                      <h4>No Matching Reservations Found</h4>
                      <p>No records matched your search query or selected filters.</p>
                      <button type="button" class="btn-system-secondary" style="font-size: 0.82rem; padding: 0.45rem 1.15rem;" onclick="resetAllAdminFilters()">Reset Filters</button>
                    </div>
                  </td>
                </tr>

                <!-- Row 1: Pending -->
                <tr data-ref="R-2026-0891" data-status="pending" data-category="halls" data-venue="Serrano Hall">
                  <td data-label="Reference & Date">
                    <div class="td-ref-group">
                      <span class="td-ref-id">R-2026-0891</span>
                      <span class="td-sub-date">Oct 05, 2026 &bull; 08:30 AM</span>
                    </div>
                  </td>
                  <td data-label="Applicant & Org">
                    <div class="td-user-group">
                      <div class="user-avatar-circle sm">JD</div>
                      <div class="td-user-details">
                        <span class="td-name">Engr. Juan Dela Cruz</span>
                        <span class="td-org">ATI - Career Dev Division (CDD)</span>
                      </div>
                    </div>
                  </td>
                  <td data-label="Requested Facility">
                    <div class="td-facility-pill hall">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                      <span>Serrano Hall</span>
                    </div>
                  </td>
                  <td data-label="Schedule / Duration">
                    <div class="td-schedule-text">
                      <strong>Oct 12 – Oct 14, 2026</strong>
                      <span>8:00 AM – 5:00 PM (3 Days)</span>
                    </div>
                  </td>
                  <td data-label="Attendees (PAX)">
                    <div class="td-pax-count">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                      <span>85 PAX</span>
                    </div>
                  </td>
                  <td data-label="Current Status">
                    <span class="status-pill status-pending">
                      <span class="status-dot"></span>
                      Pending Review
                    </span>
                  </td>
                  <td data-label="Actions" style="text-align: right;">
                    <div class="action-buttons-wrap">
                      <button type="button" class="btn-table-action approve" onclick="approveReservation('R-2026-0891', 'Engr. Juan Dela Cruz', 'Serrano Hall')" title="Approve Request">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Approve</span>
                      </button>
                      <button type="button" class="btn-table-action decline" onclick="openDeclineModal('R-2026-0891', 'Engr. Juan Dela Cruz')" title="Decline Request">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        <span>Decline</span>
                      </button>
                      <button type="button" class="btn-table-action view" onclick="viewReservationDetails('R-2026-0891')" title="View Booking Form">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Row 2: Pending (Dormitory) -->
                <tr data-ref="R-2026-0892" data-status="pending" data-category="dorms" data-venue="Dormitory Suite">
                  <td data-label="Reference & Date">
                    <div class="td-ref-group">
                      <span class="td-ref-id">R-2026-0892</span>
                      <span class="td-sub-date">Oct 05, 2026 &bull; 09:15 AM</span>
                    </div>
                  </td>
                  <td data-label="Applicant & Org">
                    <div class="td-user-group">
                      <div class="user-avatar-circle sm gold">MS</div>
                      <div class="td-user-details">
                        <span class="td-name">Dr. Maria Santos</span>
                        <span class="td-org">Bureau of Plant Industry (BPI)</span>
                      </div>
                    </div>
                  </td>
                  <td data-label="Requested Facility">
                    <div class="td-facility-pill dorm">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                      <span>Dormitory Suite A & B</span>
                    </div>
                  </td>
                  <td data-label="Schedule / Duration">
                    <div class="td-schedule-text">
                      <strong>Oct 15 – Oct 18, 2026</strong>
                      <span>Check-in 2:00 PM (4 Days)</span>
                    </div>
                  </td>
                  <td data-label="Attendees (PAX)">
                    <div class="td-pax-count">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                      <span>32 Delegates</span>
                    </div>
                  </td>
                  <td data-label="Current Status">
                    <span class="status-pill status-pending">
                      <span class="status-dot"></span>
                      Pending Review
                    </span>
                  </td>
                  <td data-label="Actions" style="text-align: right;">
                    <div class="action-buttons-wrap">
                      <button type="button" class="btn-table-action approve" onclick="approveReservation('R-2026-0892', 'Dr. Maria Santos', 'Dormitory Suite A & B')" title="Approve Request">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Approve</span>
                      </button>
                      <button type="button" class="btn-table-action decline" onclick="openDeclineModal('R-2026-0892', 'Dr. Maria Santos')" title="Decline Request">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        <span>Decline</span>
                      </button>
                      <button type="button" class="btn-table-action view" onclick="viewReservationDetails('R-2026-0892')" title="View Booking Form">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Row 3: Pending -->
                <tr data-ref="R-2026-0893" data-status="pending" data-category="halls" data-venue="Executive Boardroom">
                  <td data-label="Reference & Date">
                    <div class="td-ref-group">
                      <span class="td-ref-id">R-2026-0893</span>
                      <span class="td-sub-date">Oct 05, 2026 &bull; 10:45 AM</span>
                    </div>
                  </td>
                  <td data-label="Applicant & Org">
                    <div class="td-user-group">
                      <div class="user-avatar-circle sm">AB</div>
                      <div class="td-user-details">
                        <span class="td-name">Atty. Bernardo Castro</span>
                        <span class="td-org">DA - Legal Service Office</span>
                      </div>
                    </div>
                  </td>
                  <td data-label="Requested Facility">
                    <div class="td-facility-pill hall">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                      <span>Executive Boardroom</span>
                    </div>
                  </td>
                  <td data-label="Schedule / Duration">
                    <div class="td-schedule-text">
                      <strong>Oct 08, 2026</strong>
                      <span>1:00 PM – 5:00 PM (Half-Day)</span>
                    </div>
                  </td>
                  <td data-label="Attendees (PAX)">
                    <div class="td-pax-count">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                      <span>18 PAX</span>
                    </div>
                  </td>
                  <td data-label="Current Status">
                    <span class="status-pill status-pending">
                      <span class="status-dot"></span>
                      Pending Review
                    </span>
                  </td>
                  <td data-label="Actions" style="text-align: right;">
                    <div class="action-buttons-wrap">
                      <button type="button" class="btn-table-action approve" onclick="approveReservation('R-2026-0893', 'Atty. Bernardo Castro', 'Executive Boardroom')" title="Approve Request">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Approve</span>
                      </button>
                      <button type="button" class="btn-table-action decline" onclick="openDeclineModal('R-2026-0893', 'Atty. Bernardo Castro')" title="Decline Request">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        <span>Decline</span>
                      </button>
                      <button type="button" class="btn-table-action view" onclick="viewReservationDetails('R-2026-0893')" title="View Booking Form">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Row 4: Pending -->
                <tr data-ref="R-2026-0894" data-status="pending" data-category="halls" data-venue="4-H Learning Center">
                  <td data-label="Reference & Date">
                    <div class="td-ref-group">
                      <span class="td-ref-id">R-2026-0894</span>
                      <span class="td-sub-date">Oct 05, 2026 &bull; 11:10 AM</span>
                    </div>
                  </td>
                  <td data-label="Applicant & Org">
                    <div class="td-user-group">
                      <div class="user-avatar-circle sm">RP</div>
                      <div class="td-user-details">
                        <span class="td-name">Ramon Pascual</span>
                        <span class="td-org">PhilRice - Extension Division</span>
                      </div>
                    </div>
                  </td>
                  <td data-label="Requested Facility">
                    <div class="td-facility-pill hall">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                      <span>4-H Learning Center</span>
                    </div>
                  </td>
                  <td data-label="Schedule / Duration">
                    <div class="td-schedule-text">
                      <strong>Oct 20 – Oct 22, 2026</strong>
                      <span>8:00 AM – 5:00 PM (3 Days)</span>
                    </div>
                  </td>
                  <td data-label="Attendees (PAX)">
                    <div class="td-pax-count">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                      <span>60 PAX</span>
                    </div>
                  </td>
                  <td data-label="Current Status">
                    <span class="status-pill status-pending">
                      <span class="status-dot"></span>
                      Pending Review
                    </span>
                  </td>
                  <td data-label="Actions" style="text-align: right;">
                    <div class="action-buttons-wrap">
                      <button type="button" class="btn-table-action approve" onclick="approveReservation('R-2026-0894', 'Ramon Pascual', '4-H Learning Center')" title="Approve Request">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Approve</span>
                      </button>
                      <button type="button" class="btn-table-action decline" onclick="openDeclineModal('R-2026-0894', 'Ramon Pascual')" title="Decline Request">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        <span>Decline</span>
                      </button>
                      <button type="button" class="btn-table-action view" onclick="viewReservationDetails('R-2026-0894')" title="View Booking Form">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Row 5: Pending (Dormitory) -->
                <tr data-ref="R-2026-0895" data-status="pending" data-category="dorms" data-venue="Dormitory Suite">
                  <td data-label="Reference & Date">
                    <div class="td-ref-group">
                      <span class="td-ref-id">R-2026-0895</span>
                      <span class="td-sub-date">Oct 05, 2026 &bull; 11:40 AM</span>
                    </div>
                  </td>
                  <td data-label="Applicant & Org">
                    <div class="td-user-group">
                      <div class="user-avatar-circle sm">CL</div>
                      <div class="td-user-details">
                        <span class="td-name">Carmela Lim</span>
                        <span class="td-org">ATI - Information Services (ISD)</span>
                      </div>
                    </div>
                  </td>
                  <td data-label="Requested Facility">
                    <div class="td-facility-pill dorm">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                      <span>Dormitory Executive Suite</span>
                    </div>
                  </td>
                  <td data-label="Schedule / Duration">
                    <div class="td-schedule-text">
                      <strong>Oct 25 – Oct 27, 2026</strong>
                      <span>Check-in 1:00 PM (3 Days)</span>
                    </div>
                  </td>
                  <td data-label="Attendees (PAX)">
                    <div class="td-pax-count">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                      <span>14 Delegates</span>
                    </div>
                  </td>
                  <td data-label="Current Status">
                    <span class="status-pill status-pending">
                      <span class="status-dot"></span>
                      Pending Review
                    </span>
                  </td>
                  <td data-label="Actions" style="text-align: right;">
                    <div class="action-buttons-wrap">
                      <button type="button" class="btn-table-action approve" onclick="approveReservation('R-2026-0895', 'Carmela Lim', 'Dormitory Executive Suite')" title="Approve Request">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Approve</span>
                      </button>
                      <button type="button" class="btn-table-action decline" onclick="openDeclineModal('R-2026-0895', 'Carmela Lim')" title="Decline Request">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        <span>Decline</span>
                      </button>
                      <button type="button" class="btn-table-action view" onclick="viewReservationDetails('R-2026-0895')" title="View Booking Form">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Row 6: Approved (Sample confirmed record) -->
                <tr data-ref="R-2026-0888" data-status="approved" data-category="halls" data-venue="Serrano Hall">
                  <td data-label="Reference & Date">
                    <div class="td-ref-group">
                      <span class="td-ref-id">R-2026-0888</span>
                      <span class="td-sub-date">Oct 02, 2026 &bull; 02:20 PM</span>
                    </div>
                  </td>
                  <td data-label="Applicant & Org">
                    <div class="td-user-group">
                      <div class="user-avatar-circle sm">GV</div>
                      <div class="td-user-details">
                        <span class="td-name">Grace Valenzuela</span>
                        <span class="td-org">DA - National Rice Program</span>
                      </div>
                    </div>
                  </td>
                  <td data-label="Requested Facility">
                    <div class="td-facility-pill hall">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                      <span>Serrano Hall</span>
                    </div>
                  </td>
                  <td data-label="Schedule / Duration">
                    <div class="td-schedule-text">
                      <strong>Oct 06 – Oct 07, 2026</strong>
                      <span>8:00 AM – 5:00 PM (2 Days)</span>
                    </div>
                  </td>
                  <td data-label="Attendees (PAX)">
                    <div class="td-pax-count">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                      <span>100 PAX</span>
                    </div>
                  </td>
                  <td data-label="Current Status">
                    <span class="status-pill status-approved">
                      <span class="status-dot"></span>
                      Confirmed & Approved
                    </span>
                  </td>
                  <td data-label="Actions" style="text-align: right;">
                    <div class="action-buttons-wrap">
                      <span class="badge-approved-note">Approved by Director</span>
                      <button type="button" class="btn-table-action view" onclick="viewReservationDetails('R-2026-0888')" title="View Booking Form">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ==========================================================================
             LOWER SPLIT: FACILITY STATUS & AUDIT LOGS
             ========================================================================== -->
        <div class="admin-bottom-grid" id="facilityManagementSection">
          <!-- Left Card: Facility Quick Status & Maintenance Mode -->
          <div class="admin-card-container">
            <div class="admin-card-header">
              <div class="admin-card-title-group">
                <h3>Facilities & Dormitories Master Status</h3>
                <p>Monitor real-time occupancy and toggle administrative maintenance holds.</p>
              </div>
              <span class="count-tag-pill">6 Monitored Assets</span>
            </div>

            <div class="facility-status-list">
              <!-- Serrano Hall -->
              <div class="facility-item-row" data-venue-id="serrano">
                <img src="assets/images/serrano_hall.jpg" alt="Serrano Hall" class="facility-img-thumb" style="width: 55px; height: 55px; border-radius: 8px; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.06);">
                <div class="facility-item-info">
                  <div class="facility-item-title-row">
                    <strong>Serrano Function Hall</strong>
                    <span class="facility-state-badge state-occupied" id="badge-serrano">Occupied (Oct 6-7)</span>
                  </div>
                  <div class="facility-specs">
                    <span>Capacity: 120 PAX</span> &bull; <span>Airconditioned &bull; Sound System</span>
                  </div>
                </div>
                <div class="facility-toggle-wrap">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="toggleFacilityMaintenance('serrano', 'Serrano Function Hall', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span class="toggle-text">Maintenance</span>
                </div>
              </div>

              <!-- 4-H Center -->
              <div class="facility-item-row" data-venue-id="four_h">
                <img src="assets/images/four_h_center.jpg" alt="4-H Learning Center" class="facility-img-thumb" style="width: 55px; height: 55px; border-radius: 8px; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.06);">
                <div class="facility-item-info">
                  <div class="facility-item-title-row">
                    <strong>4-H Learning Center</strong>
                    <span class="facility-state-badge state-available" id="badge-four_h">Available</span>
                  </div>
                  <div class="facility-specs">
                    <span>Capacity: 80 PAX</span> &bull; <span>Smart TV &bull; Whiteboards</span>
                  </div>
                </div>
                <div class="facility-toggle-wrap">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="toggleFacilityMaintenance('four_h', '4-H Learning Center', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span class="toggle-text">Maintenance</span>
                </div>
              </div>

              <!-- Executive Boardroom -->
              <div class="facility-item-row" data-venue-id="boardroom">
                <img src="assets/images/boardroom.jpg" alt="Executive Boardroom" class="facility-img-thumb" style="width: 55px; height: 55px; border-radius: 8px; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.06);">
                <div class="facility-item-info">
                  <div class="facility-item-title-row">
                    <strong>Executive Boardroom</strong>
                    <span class="facility-state-badge state-available" id="badge-boardroom">Available</span>
                  </div>
                  <div class="facility-specs">
                    <span>Capacity: 25 PAX</span> &bull; <span>Hybrid Teleconference Ready</span>
                  </div>
                </div>
                <div class="facility-toggle-wrap">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="toggleFacilityMaintenance('boardroom', 'Executive Boardroom', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span class="toggle-text">Maintenance</span>
                </div>
              </div>

              <!-- Dormitory Suite A -->
              <div class="facility-item-row" data-venue-id="dorm_a">
                <img src="assets/images/dormitory.jpg" alt="Dormitory Suite A" class="facility-img-thumb" style="width: 55px; height: 55px; border-radius: 8px; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.06);">
                <div class="facility-item-info">
                  <div class="facility-item-title-row">
                    <strong>Dormitory Suite A (Male Wing)</strong>
                    <span class="facility-state-badge state-occupied" id="badge-dorm_a">Reserved (Oct 15-18)</span>
                  </div>
                  <div class="facility-specs">
                    <span>Capacity: 24 Beds</span> &bull; <span>En-suite Bath &bull; WiFi</span>
                  </div>
                </div>
                <div class="facility-toggle-wrap">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="toggleFacilityMaintenance('dorm_a', 'Dormitory Suite A', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span class="toggle-text">Maintenance</span>
                </div>
              </div>

              <!-- Dormitory Suite B -->
              <div class="facility-item-row" data-venue-id="dorm_b">
                <img src="assets/images/dormitory.jpg" alt="Dormitory Suite B" class="facility-img-thumb" style="width: 55px; height: 55px; border-radius: 8px; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.06);">
                <div class="facility-item-info">
                  <div class="facility-item-title-row">
                    <strong>Dormitory Suite B (Female Wing)</strong>
                    <span class="facility-state-badge state-available" id="badge-dorm_b">Available</span>
                  </div>
                  <div class="facility-specs">
                    <span>Capacity: 24 Beds</span> &bull; <span>En-suite Bath &bull; WiFi</span>
                  </div>
                </div>
                <div class="facility-toggle-wrap">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="toggleFacilityMaintenance('dorm_b', 'Dormitory Suite B', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span class="toggle-text">Maintenance</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Card: System Activity & Real-Time Audit Log -->
          <div class="admin-card-container" id="auditTrailSection">
            <div class="admin-card-header">
              <div class="admin-card-title-group">
                <h3>Institutional Audit Trail</h3>
                <p>Chronological security logs of reservations, approvals, and system state modifications.</p>
              </div>
              <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                <span class="count-tag-pill">Real-time Stream</span>
                <a href="admin_audit.php" class="btn-audit-portal-link" title="Open Dedicated Institutional Audit Trail Portal">
                  <span>View Full Audit Portal</span>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
              </div>
            </div>

            <div class="audit-stream-wrapper" id="auditStream">
              <div class="audit-item">
                <div class="audit-dot green"></div>
                <div class="audit-content">
                  <p><strong>Approved:</strong> Reservation <span class="audit-ref">R-2026-0888</span> by <em>DA - National Rice Program</em> was confirmed by the Clearance Authority.</p>
                  <span class="audit-time">Today &bull; 10:14 AM</span>
                </div>
              </div>

              <div class="audit-item">
                <div class="audit-dot amber"></div>
                <div class="audit-content">
                  <p><strong>New Submission:</strong> <em>Engr. Juan Dela Cruz (CDD)</em> submitted reservation <span class="audit-ref">R-2026-0891</span> for Serrano Hall (85 PAX).</p>
                  <span class="audit-time">Today &bull; 08:30 AM</span>
                </div>
              </div>

              <div class="audit-item">
                <div class="audit-dot blue"></div>
                <div class="audit-content">
                  <p><strong>Schedule Update:</strong> Master Calendar synced with DA Central Office Agricultural Extension Summit dates.</p>
                  <span class="audit-time">Yesterday &bull; 04:45 PM</span>
                </div>
              </div>

              <div class="audit-item">
                <div class="audit-dot green"></div>
                <div class="audit-content">
                  <p><strong>Maintenance Completed:</strong> Air conditioning maintenance completed for 4-H Learning Center by General Services Unit.</p>
                  <span class="audit-time">Oct 03, 2026 &bull; 03:00 PM</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: RESERVATION DETAILS & DOCUMENT REVIEW
       ========================================================================== -->
  <div class="admin-modal-overlay" id="reservationDetailsModal" style="display: none;">
    <div class="admin-modal-card">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <span class="modal-badge-ref" id="modalRefId">R-2026-0891</span>
          <h4>Official Reservation Application Form</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeReservationModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <div class="modal-applicant-summary">
          <div class="user-avatar-circle" id="modalAvatar">JD</div>
          <div class="applicant-meta">
            <h5 id="modalApplicantName">Engr. Juan Dela Cruz</h5>
            <p id="modalApplicantOrg">ATI - Career Development Division (CDD)</p>
            <span class="applicant-contact" id="modalApplicantContact">juan.delacruz@ati.da.gov.ph &bull; +63 917 123 4567</span>
          </div>
        </div>

        <div class="modal-section-grid">
          <div class="modal-data-box">
            <label>Requested Venue</label>
            <span id="modalVenueName">Serrano Function Hall</span>
          </div>
          <div class="modal-data-box">
            <label>Schedule & Duration</label>
            <span id="modalSchedule">October 12 – 14, 2026 (3 Days)</span>
          </div>
          <div class="modal-data-box">
            <label>Expected Headcount</label>
            <span id="modalPax">85 Participants</span>
          </div>
          <div class="modal-data-box">
            <label>Nature of Event</label>
            <span id="modalEventTitle">National TOT on Climate-Resilient Agriculture</span>
          </div>
        </div>

        <div class="modal-data-box" style="margin-top: 1rem;">
          <label>Requested Equipment & Setup</label>
          <span id="modalEquipment">Projector & Screen, 4 Wireless Microphones, Stage Rostrum, Classroom Type Seating</span>
        </div>

        <div class="modal-data-box" style="margin-top: 1rem;">
          <label>Official Endorsement Attachment</label>
          <div class="attachment-preview-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <div class="attachment-meta">
              <strong>Official_Request_Letter_Signed_CDD.pdf</strong>
              <small>Verified Signed PDF &bull; 1.4 MB</small>
            </div>
            <button type="button" class="btn-preview-file" onclick="showAdminToast('Downloading verified attachment...')">View Letter</button>
          </div>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeReservationModal()">Close Review</button>
        <button type="button" class="btn-decline-modal" id="btnModalDecline" onclick="modalTriggerDecline()">Decline Request</button>
        <button type="button" class="btn-approve-modal" id="btnModalApprove" onclick="modalTriggerApprove()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <span>Approve & Endorse</span>
        </button>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: DECLINE RESERVATION WITH JUSTIFICATION
       ========================================================================== -->
  <div class="admin-modal-overlay" id="declineModal" style="display: none;">
    <div class="admin-modal-card sm">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4 style="color: #991b1b;">Decline Reservation Request</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeDeclineModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <p style="font-size: 0.92rem; color: #4b6354; margin-bottom: 1rem;">
          Please state the official administrative reason for declining reservation <strong id="declineModalRef">R-2026-0891</strong> by <span id="declineModalApplicant">Applicant</span>.
        </p>

        <div class="form-group-decline">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.4rem;">Select Justification</label>
          <select id="declineReasonSelect" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit;">
            <option value="Conflict with Official ATI Institutional Activity">Conflict with Official ATI Institutional Activity</option>
            <option value="Venue Under Scheduled Maintenance">Venue Under Scheduled Maintenance</option>
            <option value="Exceeds Maximum Venue Capacity">Exceeds Maximum Venue Capacity</option>
            <option value="Incomplete Supporting Documents / Request Letter">Incomplete Supporting Documents / Request Letter</option>
            <option value="Other Administrative Ground">Other Administrative Ground</option>
          </select>
        </div>

        <div class="form-group-decline" style="margin-top: 1rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.4rem;">Executive Remarks / Instructions to Applicant</label>
          <textarea id="declineRemarks" rows="3" placeholder="Enter remarks to be sent via official notification to the applicant..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;"></textarea>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeDeclineModal()">Cancel</button>
        <button type="button" class="btn-confirm-decline" onclick="submitDeclineAction()">Confirm & Send Notice</button>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: OCCUPANCY & UTILIZATION ANALYTICS (BAR & LINE GRAPH)
       ========================================================================== -->
  <div class="admin-modal-overlay" id="occupancyModal" style="display: none;" onclick="if(event.target===this) closeOccupancyModal()">
    <div class="admin-modal-card occupancy-analytics-modal">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <div style="display: flex; align-items: center; gap: 0.6rem;">
            <div class="stat-icon-box blue" style="width: 32px; height: 32px; font-size: 0.9rem;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
            </div>
            <div>
              <h4 style="margin: 0; font-size: 1.2rem; color: #174d2f;">Venue & Dormitory Occupancy Analytics</h4>
              <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: #556f60;">Real-time capacity utilization, statistics, and booking trend breakdowns</p>
            </div>
          </div>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeOccupancyModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body occupancy-modal-scroll">
        <!-- 1. Top Summary Metric Cards -->
        <div class="occ-metrics-grid">
          <div class="occ-metric-card primary">
            <span class="occ-metric-lbl">Overall Occupancy</span>
            <div class="occ-metric-val">76.4%</div>
            <div class="occ-metric-sub"><strong>168</strong> / 220 Total PAX</div>
            <div class="occ-bar-track"><div class="occ-bar-fill" style="width: 76.4%; background: #174d2f;"></div></div>
          </div>

          <div class="occ-metric-card">
            <span class="occ-metric-lbl">Function Halls</span>
            <div class="occ-metric-val">82.5%</div>
            <div class="occ-metric-sub"><strong>99</strong> / 120 Hall PAX</div>
            <div class="occ-bar-track"><div class="occ-bar-fill" style="width: 82.5%; background: #2563eb;"></div></div>
          </div>

          <div class="occ-metric-card">
            <span class="occ-metric-lbl">Dormitory Suites</span>
            <div class="occ-metric-val">69.0%</div>
            <div class="occ-metric-sub"><strong>69</strong> / 100 Dorm Beds</div>
            <div class="occ-bar-track"><div class="occ-bar-fill" style="width: 69%; background: #d97706;"></div></div>
          </div>

          <div class="occ-metric-card">
            <span class="occ-metric-lbl">Peak Load Day</span>
            <div class="occ-metric-val" style="color: #b45309;">94.2%</div>
            <div class="occ-metric-sub">Thursday, Oct 15 &bull; 207 PAX</div>
            <div class="occ-bar-track"><div class="occ-bar-fill" style="width: 94.2%; background: #ef4444;"></div></div>
          </div>
        </div>

        <!-- 2. Controls & Switcher Toolbar -->
        <div class="occ-graph-toolbar">
          <div class="occ-toggle-group">
            <button type="button" class="occ-btn-toggle active" id="btnToggleBarGraph" onclick="switchOccupancyGraph('bar')">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
              <span>Bar Graph (By Venue)</span>
            </button>
            <button type="button" class="occ-btn-toggle" id="btnToggleLineGraph" onclick="switchOccupancyGraph('line')">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
              </svg>
              <span>Line Graph (Trend %)</span>
            </button>
          </div>

          <div class="occ-range-group">
            <label for="occTimeRangeSelect" style="font-size: 0.8rem; font-weight: 700; color: #4b6354;">Timeline:</label>
            <select id="occTimeRangeSelect" class="occ-range-select" onchange="updateOccupancyTimeRange(this.value)">
              <option value="week" selected>Current Week (Oct 5 – Oct 11, 2026)</option>
              <option value="month">Full Month of October 2026</option>
              <option value="q4">Quarter 4 Projection (Oct – Dec)</option>
            </select>
          </div>
        </div>

        <!-- 3. Graph Container -->
        <div class="occ-chart-surface">
          <!-- VIEW A: BAR GRAPH (Facility Capacity Breakdown) -->
          <div id="occupancyBarGraphView" class="occ-graph-pane">
            <div class="occ-chart-caption">
              <span class="occ-legend-pill"><span class="occ-dot hall"></span> Function Halls</span>
              <span class="occ-legend-pill"><span class="occ-dot dorm"></span> Dormitory Suites</span>
              <span class="occ-legend-pill"><span class="occ-target-line"></span> Benchmark Target (80%)</span>
            </div>

            <!-- SVG Bar Graph -->
            <div class="occ-svg-container">
              <svg viewBox="0 0 740 280" class="occ-bar-svg" preserveAspectRatio="xMidYMid meet">
                <!-- Grid Lines -->
                <line x1="60" y1="40" x2="710" y2="40" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1"></line>
                <line x1="60" y1="85" x2="710" y2="85" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1"></line>
                <line x1="60" y1="130" x2="710" y2="130" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1"></line>
                <line x1="60" y1="175" x2="710" y2="175" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1"></line>
                <line x1="60" y1="220" x2="710" y2="220" stroke="#d5e3d8" stroke-width="1.5"></line>

                <!-- 80% Benchmark Line -->
                <line x1="60" y1="76" x2="710" y2="76" stroke="#eab308" stroke-dasharray="6 3" stroke-width="1.8"></line>
                <text x="712" y="79" font-size="10" fill="#b45309" font-weight="700">80% Target</text>

                <!-- Y Axis Labels -->
                <text x="45" y="44" font-size="11" fill="#789080" text-anchor="end">100%</text>
                <text x="45" y="89" font-size="11" fill="#789080" text-anchor="end">75%</text>
                <text x="45" y="134" font-size="11" fill="#789080" text-anchor="end">50%</text>
                <text x="45" y="179" font-size="11" fill="#789080" text-anchor="end">25%</text>
                <text x="45" y="224" font-size="11" fill="#789080" text-anchor="end">0%</text>

                <!-- Bar 1: Serrano Hall (90%) -> height = 90% of 180 = 162. y = 220 - 162 = 58 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="85" y="58" width="56" height="162" rx="6" fill="url(#gradHall)" class="bar-rect"></rect>
                  <text x="113" y="50" font-size="12" font-weight="800" fill="#174d2f" text-anchor="middle">90.0%</text>
                  <text x="113" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Serrano Hall</text>
                  <text x="113" y="255" font-size="10" fill="#698572" text-anchor="middle">108/120 PAX</text>
                </g>

                <!-- Bar 2: Training Hall A (85%) -> height = 153. y = 67 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="190" y="67" width="56" height="153" rx="6" fill="url(#gradHall)" class="bar-rect"></rect>
                  <text x="218" y="59" font-size="12" font-weight="800" fill="#174d2f" text-anchor="middle">85.0%</text>
                  <text x="218" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Training Hall</text>
                  <text x="218" y="255" font-size="10" fill="#698572" text-anchor="middle">51/60 PAX</text>
                </g>

                <!-- Bar 3: Executive Boardroom (72%) -> height = 129.6. y = 90.4 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="295" y="90" width="56" height="130" rx="6" fill="url(#gradHall)" class="bar-rect"></rect>
                  <text x="323" y="82" font-size="12" font-weight="800" fill="#174d2f" text-anchor="middle">72.0%</text>
                  <text x="323" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Boardroom</text>
                  <text x="323" y="255" font-size="10" fill="#698572" text-anchor="middle">18/25 PAX</text>
                </g>

                <!-- Bar 4: Mess Hall Dining (80%) -> height = 144. y = 76 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="400" y="76" width="56" height="144" rx="6" fill="url(#gradHall)" class="bar-rect"></rect>
                  <text x="428" y="68" font-size="12" font-weight="800" fill="#174d2f" text-anchor="middle">80.0%</text>
                  <text x="428" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Mess Hall</text>
                  <text x="428" y="255" font-size="10" fill="#698572" text-anchor="middle">80/100 PAX</text>
                </g>

                <!-- Bar 5: Dorm Suite A (65%) -> height = 117. y = 103 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="505" y="103" width="56" height="117" rx="6" fill="url(#gradDorm)" class="bar-rect"></rect>
                  <text x="533" y="95" font-size="12" font-weight="800" fill="#d97706" text-anchor="middle">65.0%</text>
                  <text x="533" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Dorm Suite A</text>
                  <text x="533" y="255" font-size="10" fill="#698572" text-anchor="middle">39/60 Beds</text>
                </g>

                <!-- Bar 6: Dorm Suite B (70%) -> height = 126. y = 94 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="610" y="94" width="56" height="126" rx="6" fill="url(#gradDorm)" class="bar-rect"></rect>
                  <text x="638" y="86" font-size="12" font-weight="800" fill="#d97706" text-anchor="middle">70.0%</text>
                  <text x="638" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Dorm Suite B</text>
                  <text x="638" y="255" font-size="10" fill="#698572" text-anchor="middle">28/40 Beds</text>
                </g>

                <!-- Gradients -->
                <defs>
                  <linearGradient id="gradHall" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#1b5e33"></stop>
                    <stop offset="100%" stop-color="#2a824b"></stop>
                  </linearGradient>
                  <linearGradient id="gradDorm" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#d97706"></stop>
                    <stop offset="100%" stop-color="#f59e0b"></stop>
                  </linearGradient>
                </defs>
              </svg>
            </div>
          </div>

          <!-- VIEW B: LINE GRAPH (Occupancy Percentage Trend) -->
          <div id="occupancyLineGraphView" class="occ-graph-pane" style="display: none;">
            <div class="occ-chart-caption">
              <span class="occ-legend-pill"><span class="occ-dot" style="background: #174d2f;"></span> Daily Occupancy Rate (%)</span>
              <span class="occ-legend-pill"><span class="occ-target-line"></span> Monthly Average (76.4%)</span>
            </div>

            <!-- SVG Line Graph -->
            <div class="occ-svg-container">
              <svg viewBox="0 0 740 280" class="occ-line-svg" preserveAspectRatio="xMidYMid meet">
                <!-- Grid Lines -->
                <line x1="60" y1="40" x2="710" y2="40" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1"></line>
                <line x1="60" y1="85" x2="710" y2="85" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1"></line>
                <line x1="60" y1="130" x2="710" y2="130" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1"></line>
                <line x1="60" y1="175" x2="710" y2="175" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1"></line>
                <line x1="60" y1="220" x2="710" y2="220" stroke="#d5e3d8" stroke-width="1.5"></line>

                <!-- Average Line (76.4%) -> y = 220 - (0.764 * 180) = 82.5 -->
                <line x1="60" y1="82.5" x2="710" y2="82.5" stroke="#eab308" stroke-dasharray="6 3" stroke-width="1.8"></line>
                <text x="712" y="85" font-size="10" fill="#b45309" font-weight="700">76.4% Avg</text>

                <!-- Y Axis Labels -->
                <text x="45" y="44" font-size="11" fill="#789080" text-anchor="end">100%</text>
                <text x="45" y="89" font-size="11" fill="#789080" text-anchor="end">75%</text>
                <text x="45" y="134" font-size="11" fill="#789080" text-anchor="end">50%</text>
                <text x="45" y="179" font-size="11" fill="#789080" text-anchor="end">25%</text>
                <text x="45" y="224" font-size="11" fill="#789080" text-anchor="end">0%</text>

                <!-- Gradient Area Under Curve -->
                <!-- Points: Mon(105, 108) Tue(200, 86) Wed(295, 70) Thu(390, 50) Fri(485, 79) Sat(580, 115) Sun(675, 144) -->
                <path d="M 105 108 L 200 86 L 295 70 L 390 50 L 485 79 L 580 115 L 675 144 L 675 220 L 105 220 Z" fill="url(#gradLineArea)"></path>

                <!-- The Curved Trend Line -->
                <path d="M 105 108 L 200 86 L 295 70 L 390 50 L 485 79 L 580 115 L 675 144" fill="none" stroke="#174d2f" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path>

                <!-- Data Points & Labels -->
                <!-- Mon Oct 5: 62.0% -> y = 220 - (0.62 * 180) = 108.4 -->
                <g class="occ-pt-group">
                  <circle cx="105" cy="108" r="6" fill="#ffffff" stroke="#174d2f" stroke-width="3"></circle>
                  <text x="105" y="94" font-size="11" font-weight="700" fill="#174d2f" text-anchor="middle">62.0%</text>
                  <text x="105" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Mon</text>
                  <text x="105" y="255" font-size="10" fill="#698572" text-anchor="middle">Oct 5</text>
                </g>

                <!-- Tue Oct 6: 74.4% -> y = 86 -->
                <g class="occ-pt-group">
                  <circle cx="200" cy="86" r="6" fill="#ffffff" stroke="#174d2f" stroke-width="3"></circle>
                  <text x="200" y="72" font-size="11" font-weight="700" fill="#174d2f" text-anchor="middle">74.4%</text>
                  <text x="200" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Tue</text>
                  <text x="200" y="255" font-size="10" fill="#698572" text-anchor="middle">Oct 6 (Today)</text>
                </g>

                <!-- Wed Oct 7: 83.2% -> y = 70 -->
                <g class="occ-pt-group">
                  <circle cx="295" cy="70" r="6" fill="#ffffff" stroke="#174d2f" stroke-width="3"></circle>
                  <text x="295" y="56" font-size="11" font-weight="700" fill="#174d2f" text-anchor="middle">83.2%</text>
                  <text x="295" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Wed</text>
                  <text x="295" y="255" font-size="10" fill="#698572" text-anchor="middle">Oct 7</text>
                </g>

                <!-- Thu Oct 8: 94.2% Peak -> y = 50 -->
                <g class="occ-pt-group">
                  <circle cx="390" cy="50" r="7.5" fill="#f59e0b" stroke="#b45309" stroke-width="3"></circle>
                  <text x="390" y="34" font-size="12" font-weight="800" fill="#b45309" text-anchor="middle">94.2% (Peak)</text>
                  <text x="390" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Thu</text>
                  <text x="390" y="255" font-size="10" fill="#698572" text-anchor="middle">Oct 8</text>
                </g>

                <!-- Fri Oct 9: 78.5% -> y = 79 -->
                <g class="occ-pt-group">
                  <circle cx="485" cy="79" r="6" fill="#ffffff" stroke="#174d2f" stroke-width="3"></circle>
                  <text x="485" y="65" font-size="11" font-weight="700" fill="#174d2f" text-anchor="middle">78.5%</text>
                  <text x="485" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Fri</text>
                  <text x="485" y="255" font-size="10" fill="#698572" text-anchor="middle">Oct 9</text>
                </g>

                <!-- Sat Oct 10: 58.0% -> y = 115 -->
                <g class="occ-pt-group">
                  <circle cx="580" cy="115" r="6" fill="#ffffff" stroke="#174d2f" stroke-width="3"></circle>
                  <text x="580" y="101" font-size="11" font-weight="700" fill="#174d2f" text-anchor="middle">58.0%</text>
                  <text x="580" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Sat</text>
                  <text x="580" y="255" font-size="10" fill="#698572" text-anchor="middle">Oct 10</text>
                </g>

                <!-- Sun Oct 11: 42.0% -> y = 144 -->
                <g class="occ-pt-group">
                  <circle cx="675" cy="144" r="6" fill="#ffffff" stroke="#174d2f" stroke-width="3"></circle>
                  <text x="675" y="130" font-size="11" font-weight="700" fill="#174d2f" text-anchor="middle">42.0%</text>
                  <text x="675" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Sun</text>
                  <text x="675" y="255" font-size="10" fill="#698572" text-anchor="middle">Oct 11</text>
                </g>

                <defs>
                  <linearGradient id="gradLineArea" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#174d2f" stop-opacity="0.25"></stop>
                    <stop offset="100%" stop-color="#174d2f" stop-opacity="0.02"></stop>
                  </linearGradient>
                </defs>
              </svg>
            </div>
          </div>
        </div>

        <!-- 4. Detailed Facility Asset Breakdown Table -->
        <div class="occ-breakdown-section">
          <h5 style="margin: 0 0 0.75rem; font-size: 0.95rem; font-weight: 800; color: #174d2f; display: flex; align-items: center; justify-content: space-between;">
            <span>Monitored Facility Utilization Breakdown</span>
            <span style="font-size: 0.75rem; font-weight: 600; color: #60796b;">Total 6 Institutional Assets</span>
          </h5>

          <div class="occ-table-wrap">
            <table class="occ-breakdown-table">
              <thead>
                <tr>
                  <th>Facility / Venue</th>
                  <th>Category</th>
                  <th>Booked / Max Capacity</th>
                  <th>Occupancy Rate</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Serrano Function Hall</strong></td>
                  <td><span class="occ-tag hall">Function Hall</span></td>
                  <td>108 / 120 PAX</td>
                  <td>
                    <div class="occ-tbl-rate">
                      <div class="occ-tbl-bar"><div class="occ-tbl-fill" style="width: 90%; background: #174d2f;"></div></div>
                      <strong>90.0%</strong>
                    </div>
                  </td>
                  <td><span class="occ-status-badge high">High Demand</span></td>
                </tr>
                <tr>
                  <td><strong>Training Hall A (AV Room)</strong></td>
                  <td><span class="occ-tag hall">Training Hall</span></td>
                  <td>51 / 60 PAX</td>
                  <td>
                    <div class="occ-tbl-rate">
                      <div class="occ-tbl-bar"><div class="occ-tbl-fill" style="width: 85%; background: #174d2f;"></div></div>
                      <strong>85.0%</strong>
                    </div>
                  </td>
                  <td><span class="occ-status-badge active">Active Load</span></td>
                </tr>
                <tr>
                  <td><strong>Executive Boardroom</strong></td>
                  <td><span class="occ-tag hall">Executive Hall</span></td>
                  <td>18 / 25 PAX</td>
                  <td>
                    <div class="occ-tbl-rate">
                      <div class="occ-tbl-bar"><div class="occ-tbl-fill" style="width: 72%; background: #2563eb;"></div></div>
                      <strong>72.0%</strong>
                    </div>
                  </td>
                  <td><span class="occ-status-badge active">Reserved</span></td>
                </tr>
                <tr>
                  <td><strong>Mess Hall & Dining Area</strong></td>
                  <td><span class="occ-tag hall">Dining Hall</span></td>
                  <td>80 / 100 PAX</td>
                  <td>
                    <div class="occ-tbl-rate">
                      <div class="occ-tbl-bar"><div class="occ-tbl-fill" style="width: 80%; background: #174d2f;"></div></div>
                      <strong>80.0%</strong>
                    </div>
                  </td>
                  <td><span class="occ-status-badge active">Active Catering</span></td>
                </tr>
                <tr>
                  <td><strong>Dormitory Suite A (Trainee Wing)</strong></td>
                  <td><span class="occ-tag dorm">Dormitory</span></td>
                  <td>39 / 60 Beds</td>
                  <td>
                    <div class="occ-tbl-rate">
                      <div class="occ-tbl-bar"><div class="occ-tbl-fill" style="width: 65%; background: #d97706;"></div></div>
                      <strong>65.0%</strong>
                    </div>
                  </td>
                  <td><span class="occ-status-badge dorm">Checked In</span></td>
                </tr>
                <tr>
                  <td><strong>Dormitory Suite B (VIP Wing)</strong></td>
                  <td><span class="occ-tag dorm">Dormitory</span></td>
                  <td>28 / 40 Beds</td>
                  <td>
                    <div class="occ-tbl-rate">
                      <div class="occ-tbl-bar"><div class="occ-tbl-fill" style="width: 70%; background: #d97706;"></div></div>
                      <strong>70.0%</strong>
                    </div>
                  </td>
                  <td><span class="occ-status-badge dorm">Checked In</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeOccupancyModal()">Close Analytics</button>
        <button type="button" class="btn-preview-file" onclick="exportOccupancyReport()" style="display: inline-flex; align-items: center; gap: 0.4rem;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          <span>Export Analytics Report</span>
        </button>
      </div>
    </div>
  <!-- ==========================================================================
       TOAST NOTIFICATION CONTAINER
       ========================================================================== -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- Scripts -->
  <script src="js/admin_dashboard.js?v=<?php echo time(); ?>"></script>
</body>

</html>
