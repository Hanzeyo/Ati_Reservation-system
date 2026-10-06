<?php
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator: Institutional Audit Trail & Security Logs Portal
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Institutional Audit Trail Log | ATI Reservation Portal</title>
  <meta name="description"
    content="Official Audit Trail and Security Activity Log for Agricultural Training Institute Central Office.">

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
  <link rel="stylesheet" href="css/admin_audit.css?v=<?php echo time(); ?>">
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

        <a href="admin_dashboard.php" class="sidebar-menu-item">
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

        <a href="admin_audit.php" class="sidebar-menu-item active">
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
          <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()" aria-label="Toggle Sidebar Navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>

          <div class="topbar-title-wrap">
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Super Administrator &rsaquo; System Governance</span>
            <h1 class="topbar-page-title">Institutional Audit Trail & Activity Logs</h1>
          </div>
        </div>

        <div class="admin-topbar-right">
          <!-- Quick Search -->
          <div class="topbar-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="topbarSearchInput" placeholder="Quick search audit logs..." oninput="handleAuditSearch(this.value)" autocomplete="off">
            <span class="search-kbd-hint"><kbd>Ctrl</kbd><kbd>K</kbd></span>
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
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <span>Regulatory Compliance & Institutional Audit</span>
            </div>
            <h2>Chronological Audit Trail & Security Ledger</h2>
            <p>Immutable audit trail documenting official reservation approvals, endorsement routing, facility maintenance toggles, and administrative account credential modifications for ATI Central Office.</p>
          </div>

          <div class="admin-header-actions">
            <button type="button" class="btn-system-secondary" onclick="exportAuditLogCSV()">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span>Export Official Audit CSV</span>
            </button>
          </div>
        </section>

        <!-- ==========================================================================
             KPI STAT CARDS (AUDIT LEDGER)
             ========================================================================== -->
        <section class="my-res-stats-grid admin-kpi-grid" aria-label="Audit Statistics">
          <!-- Total Events -->
          <div class="my-res-stat-card active-filter" onclick="filterAuditByCategory('all')">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiTotalEvents">10</span>
                <span class="stat-trend positive">100% Verified</span>
              </div>
              <span class="stat-label">Total Recorded Logs</span>
            </div>
          </div>

          <!-- Approvals & Endorsements -->
          <div class="my-res-stat-card" onclick="filterAuditByCategory('approval')">
            <div class="stat-icon-box emerald">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiApprovalEvents">4</span>
                <span class="stat-trend positive">Authorizations</span>
              </div>
              <span class="stat-label">Director Clearances</span>
            </div>
          </div>

          <!-- Facility Maintenance Holds -->
          <div class="my-res-stat-card" onclick="filterAuditByCategory('maintenance')">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiMaintenanceEvents">3</span>
                <span class="stat-trend neutral">Asset Holds</span>
              </div>
              <span class="stat-label">Facility & Schedule Holds</span>
            </div>
          </div>

          <!-- Security & RBAC Access -->
          <div class="my-res-stat-card" onclick="filterAuditByCategory('security')">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiSecurityEvents">3</span>
                <span class="stat-trend positive">Governance</span>
              </div>
              <span class="stat-label">Security & RBAC Changes</span>
            </div>
          </div>
        </section>

        <!-- ==========================================================================
             TOOLBAR: SEARCH, CATEGORY PILLS, FILTERS & VIEW SWITCHER
             ========================================================================== -->
        <div class="my-res-toolbar-card admin-toolbar-card">
          <!-- Filter Tabs -->
          <div class="filter-tabs-group" role="tablist">
            <button type="button" class="user-filter-pill active" data-cat="all" onclick="filterAuditByCategory('all')">
              <span>All Logs</span>
              <span class="tab-count-pill" id="countPillAll">10</span>
            </button>
            <button type="button" class="user-filter-pill" data-cat="approval" onclick="filterAuditByCategory('approval')">
              <span>Approvals & Endorsements</span>
              <span class="tab-count-pill" id="countPillApp">4</span>
            </button>
            <button type="button" class="user-filter-pill" data-cat="maintenance" onclick="filterAuditByCategory('maintenance')">
              <span>Facility Maintenance</span>
              <span class="tab-count-pill" id="countPillMaint">3</span>
            </button>
            <button type="button" class="user-filter-pill" data-cat="security" onclick="filterAuditByCategory('security')">
              <span>Security & Roles</span>
              <span class="tab-count-pill" id="countPillSec">3</span>
            </button>
          </div>

          <!-- Controls Right -->
          <div class="toolbar-controls-right">
            <!-- View Switcher -->
            <div class="audit-view-switcher">
              <button type="button" class="btn-view-toggle active" id="btnViewTable" onclick="switchAuditView('table')" title="Tabular Grid View">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="8" y1="6" x2="21" y2="6"></line>
                  <line x1="8" y1="12" x2="21" y2="12"></line>
                  <line x1="8" y1="18" x2="21" y2="18"></line>
                  <line x1="3" y1="6" x2="3.01" y2="6"></line>
                  <line x1="3" y1="12" x2="3.01" y2="12"></line>
                  <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
                <span>Table</span>
              </button>
              <button type="button" class="btn-view-toggle" id="btnViewTimeline" onclick="switchAuditView('timeline')" title="Visual Timeline View">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Timeline</span>
              </button>
            </div>

            <div class="search-box-wrap admin-search-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <input type="text" id="auditSearchInput" class="search-input" placeholder="Search by reference, actor, or detail..."
                oninput="handleAuditSearch(this.value)" autocomplete="off">
              <button type="button" class="search-clear-btn" id="auditSearchClear" onclick="clearAuditSearch()" title="Clear search" style="display: none;">&times;</button>
            </div>

            <div class="select-filter-wrap">
              <select id="timeframeFilterSelect" class="venue-filter-select" onchange="handleTimeframeFilter(this.value)">
                <option value="all">All Timelines</option>
                <option value="today">Today Only</option>
                <option value="yesterday">Yesterday</option>
                <option value="oct03">Earlier this Month</option>
              </select>
            </div>
          </div>
        </div>

        <!-- ==========================================================================
             AUDIT LEDGER CARD (TABLE VIEW & TIMELINE VIEW)
             ========================================================================== -->
        <div class="admin-table-card">
          <div class="admin-card-header">
            <div class="admin-card-title-group">
              <h3>Official Audit Trail Records</h3>
              <p>Cryptographically timestamped activity records for security, auditing, and official oversight.</p>
            </div>
            <div class="admin-card-header-badge">
              <span class="indicator-live-dot"></span>
              Immutable Ledger Stream
            </div>
          </div>

          <!-- 1. TABLE VIEW -->
          <div class="admin-table-responsive" id="auditTableViewContainer">
            <table class="admin-data-table" id="auditTable">
              <thead>
                <tr>
                  <th style="width: 17%;">Timestamp & Date</th>
                  <th style="width: 14%;">Category</th>
                  <th style="width: 32%;">Action Details & Narrative</th>
                  <th style="width: 18%;">Acting Authority</th>
                  <th style="width: 11%;">Target Resource</th>
                  <th style="width: 8%; text-align: right;">Security Hash</th>
                </tr>
              </thead>
              <tbody id="auditTableBody">
                <!-- Empty search state row -->
                <tr id="auditNoResultsRow" style="display: none;">
                  <td colspan="6" class="admin-empty-table-state">
                    <div class="empty-state-content">
                      <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#8ca394" stroke-width="1.8">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                      </svg>
                      <h4>No Audit Events Found</h4>
                      <p>No activity logs matched your search terms or active filters.</p>
                      <button type="button" class="btn-system-secondary" style="font-size: 0.82rem; padding: 0.45rem 1.15rem;" onclick="resetAllAuditFilters()">Reset Filters</button>
                    </div>
                  </td>
                </tr>

                <!-- Event 1: Approval -->
                <tr data-event-id="LOG-8812" data-category="approval" data-severity="success" data-timeframe="today">
                  <td>
                    <div class="td-ref-group">
                      <strong style="color: #172d1f; font-size: 0.88rem;">Today &bull; 10:14 AM</strong>
                      <span class="td-sub-date">Oct 05, 2026 &bull; 10:14:22 PHT</span>
                    </div>
                  </td>
                  <td>
                    <span class="event-type-chip approval">
                      <span class="event-chip-dot"></span>
                      Director Approval
                    </span>
                  </td>
                  <td>
                    <div class="audit-narrative-text">
                      Director IV issued official Stage 2 approval for reservation <span class="audit-ref">R-2026-0888</span> submitted by <em>DA - National Rice Program</em>. Gate pass endorsement generated.
                    </div>
                  </td>
                  <td>
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar director">RR</div>
                      <div>
                        <div class="audit-actor-name">Clearance Authority</div>
                        <span class="audit-actor-role">Director IV &bull; Super Admin</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="audit-target-badge">Serrano Hall</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="security-hash-tag" title="Verified SHA-256 Checksum">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      #8812-a7
                    </span>
                  </td>
                </tr>

                <!-- Event 2: New Submission -->
                <tr data-event-id="LOG-8811" data-category="submission" data-severity="warning" data-timeframe="today">
                  <td>
                    <div class="td-ref-group">
                      <strong style="color: #172d1f; font-size: 0.88rem;">Today &bull; 08:30 AM</strong>
                      <span class="td-sub-date">Oct 05, 2026 &bull; 08:30:14 PHT</span>
                    </div>
                  </td>
                  <td>
                    <span class="event-type-chip submission">
                      <span class="event-chip-dot"></span>
                      New Submission
                    </span>
                  </td>
                  <td>
                    <div class="audit-narrative-text">
                      New facility reservation application submitted for <span class="audit-ref">R-2026-0891</span> by <em>Career Dev Division (CDD)</em>. Expected headcount: 85 PAX. Endorsement letter attached.
                    </div>
                  </td>
                  <td>
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar">JD</div>
                      <div>
                        <div class="audit-actor-name">Engr. Juan Dela Cruz</div>
                        <span class="audit-actor-role">Chief, CDD &bull; Applicant</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="audit-target-badge">Serrano Hall</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="security-hash-tag">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      #8811-c2
                    </span>
                  </td>
                </tr>

                <!-- Event 3: RBAC Role Modification -->
                <tr data-event-id="LOG-8810" data-category="security" data-severity="info" data-timeframe="today">
                  <td>
                    <div class="td-ref-group">
                      <strong style="color: #172d1f; font-size: 0.88rem;">Today &bull; 08:15 AM</strong>
                      <span class="td-sub-date">Oct 05, 2026 &bull; 08:15:45 PHT</span>
                    </div>
                  </td>
                  <td>
                    <span class="event-type-chip security">
                      <span class="event-chip-dot"></span>
                      RBAC Security
                    </span>
                  </td>
                  <td>
                    <div class="audit-narrative-text">
                      User clearance credentials reconfigured for <em>Atty. Bernardo Castro</em>. Elevated to <strong>Stage 1: Recommending Officer</strong> with Legal Document Review privileges.
                    </div>
                  </td>
                  <td>
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar director">RR</div>
                      <div>
                        <div class="audit-actor-name">Clearance Authority</div>
                        <span class="audit-actor-role">Super Administrator</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="audit-target-badge">USR-1004</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="security-hash-tag">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      #8810-f1
                    </span>
                  </td>
                </tr>

                <!-- Event 4: Schedule Update -->
                <tr data-event-id="LOG-8809" data-category="maintenance" data-severity="info" data-timeframe="yesterday">
                  <td>
                    <div class="td-ref-group">
                      <strong style="color: #172d1f; font-size: 0.88rem;">Yesterday &bull; 04:45 PM</strong>
                      <span class="td-sub-date">Oct 04, 2026 &bull; 16:45:10 PHT</span>
                    </div>
                  </td>
                  <td>
                    <span class="event-type-chip schedule">
                      <span class="event-chip-dot"></span>
                      Calendar Sync
                    </span>
                  </td>
                  <td>
                    <div class="audit-narrative-text">
                      Institutional Master Calendar synchronized with DA Central Office Agricultural Extension Summit schedule. 3 reserved dates blocked.
                    </div>
                  </td>
                  <td>
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar system">SYS</div>
                      <div>
                        <div class="audit-actor-name">Automated Calendar Daemon</div>
                        <span class="audit-actor-role">Central Office Sync Service</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="audit-target-badge">Master Calendar</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="security-hash-tag">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      #8809-e8
                    </span>
                  </td>
                </tr>

                <!-- Event 5: Maintenance Complete -->
                <tr data-event-id="LOG-8808" data-category="maintenance" data-severity="success" data-timeframe="oct03">
                  <td>
                    <div class="td-ref-group">
                      <strong style="color: #172d1f; font-size: 0.88rem;">Oct 03, 2026 &bull; 03:00 PM</strong>
                      <span class="td-sub-date">Oct 03, 2026 &bull; 15:00:00 PHT</span>
                    </div>
                  </td>
                  <td>
                    <span class="event-type-chip maintenance">
                      <span class="event-chip-dot"></span>
                      Maintenance Done
                    </span>
                  </td>
                  <td>
                    <div class="audit-narrative-text">
                      Air conditioning preventative servicing completed for <em>4-H Learning Center</em> by General Services Unit. Facility restored to <strong>Available</strong> status.
                    </div>
                  </td>
                  <td>
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar">DM</div>
                      <div>
                        <div class="audit-actor-name">Arch. Danilo Mendoza</div>
                        <span class="audit-actor-role">Head, General Services (GSU)</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="audit-target-badge">4-H Learning Ctr</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="security-hash-tag">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      #8808-d4
                    </span>
                  </td>
                </tr>

                <!-- Event 6: Maintenance Hold -->
                <tr data-event-id="LOG-8807" data-category="maintenance" data-severity="warning" data-timeframe="oct03">
                  <td>
                    <div class="td-ref-group">
                      <strong style="color: #172d1f; font-size: 0.88rem;">Oct 03, 2026 &bull; 09:30 AM</strong>
                      <span class="td-sub-date">Oct 03, 2026 &bull; 09:30:18 PHT</span>
                    </div>
                  </td>
                  <td>
                    <span class="event-type-chip maintenance">
                      <span class="event-chip-dot"></span>
                      Maintenance Hold
                    </span>
                  </td>
                  <td>
                    <div class="audit-narrative-text">
                      Administrative maintenance hold placed on <em>4-H Learning Center</em> for sound system inspection and HVAC maintenance. Bookings temporarily paused.
                    </div>
                  </td>
                  <td>
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar">DM</div>
                      <div>
                        <div class="audit-actor-name">Arch. Danilo Mendoza</div>
                        <span class="audit-actor-role">General Services Unit</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="audit-target-badge">4-H Learning Ctr</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="security-hash-tag">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      #8807-b3
                    </span>
                  </td>
                </tr>

                <!-- Event 7: Recommending Endorsement -->
                <tr data-event-id="LOG-8806" data-category="approval" data-severity="success" data-timeframe="oct03">
                  <td>
                    <div class="td-ref-group">
                      <strong style="color: #172d1f; font-size: 0.88rem;">Oct 02, 2026 &bull; 04:15 PM</strong>
                      <span class="td-sub-date">Oct 02, 2026 &bull; 16:15:30 PHT</span>
                    </div>
                  </td>
                  <td>
                    <span class="event-type-chip approval">
                      <span class="event-chip-dot"></span>
                      Stage 1 Endorsement
                    </span>
                  </td>
                  <td>
                    <div class="audit-narrative-text">
                      Stage 1 recommendation issued for reservation <span class="audit-ref">R-2026-0888</span>. Request verified compliant with ATI Guidelines on Venue Allocation. Forwarded to Director IV.
                    </div>
                  </td>
                  <td>
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar">RS</div>
                      <div>
                        <div class="audit-actor-name">Rowena Santos</div>
                        <span class="audit-actor-role">Recommending Officer &bull; AFD</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="audit-target-badge">R-2026-0888</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="security-hash-tag">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      #8806-9c
                    </span>
                  </td>
                </tr>

                <!-- Event 8: Account Created -->
                <tr data-event-id="LOG-8805" data-category="security" data-severity="info" data-timeframe="oct03">
                  <td>
                    <div class="td-ref-group">
                      <strong style="color: #172d1f; font-size: 0.88rem;">Oct 02, 2026 &bull; 11:20 AM</strong>
                      <span class="td-sub-date">Oct 02, 2026 &bull; 11:20:00 PHT</span>
                    </div>
                  </td>
                  <td>
                    <span class="event-type-chip security">
                      <span class="event-chip-dot"></span>
                      Account Provision
                    </span>
                  </td>
                  <td>
                    <div class="audit-narrative-text">
                      New authorized external partner account registered for <em>Dr. Maria Santos</em> (Bureau of Plant Industry). Identity verification completed.
                    </div>
                  </td>
                  <td>
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar director">RR</div>
                      <div>
                        <div class="audit-actor-name">Clearance Authority</div>
                        <span class="audit-actor-role">Super Administrator</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="audit-target-badge">USR-1012</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="security-hash-tag">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      #8805-7a
                    </span>
                  </td>
                </tr>

                <!-- Event 9: Account Suspended -->
                <tr data-event-id="LOG-8804" data-category="security" data-severity="danger" data-timeframe="oct03">
                  <td>
                    <div class="td-ref-group">
                      <strong style="color: #172d1f; font-size: 0.88rem;">Oct 01, 2026 &bull; 05:40 PM</strong>
                      <span class="td-sub-date">Oct 01, 2026 &bull; 17:40:12 PHT</span>
                    </div>
                  </td>
                  <td>
                    <span class="event-type-chip decline">
                      <span class="event-chip-dot"></span>
                      Access Suspended
                    </span>
                  </td>
                  <td>
                    <div class="audit-narrative-text">
                      Account access suspended for <em>Gilbert Ocampo</em> (Security & Facilities Oversight) by Super Administrator pending institutional clearance review.
                    </div>
                  </td>
                  <td>
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar director">RR</div>
                      <div>
                        <div class="audit-actor-name">Clearance Authority</div>
                        <span class="audit-actor-role">Super Administrator</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="audit-target-badge">USR-1016</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="security-hash-tag">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      #8804-5e
                    </span>
                  </td>
                </tr>

                <!-- Event 10: Decline with Justification -->
                <tr data-event-id="LOG-8803" data-category="approval" data-severity="danger" data-timeframe="oct03">
                  <td>
                    <div class="td-ref-group">
                      <strong style="color: #172d1f; font-size: 0.88rem;">Sep 30, 2026 &bull; 03:10 PM</strong>
                      <span class="td-sub-date">Sep 30, 2026 &bull; 15:10:48 PHT</span>
                    </div>
                  </td>
                  <td>
                    <span class="event-type-chip decline">
                      <span class="event-chip-dot"></span>
                      Official Decline
                    </span>
                  </td>
                  <td>
                    <div class="audit-narrative-text">
                      Reservation application <span class="audit-ref">R-2026-0875</span> declined by Director IV. Reason: <em>Schedule conflict with National Extension Directorate Summit</em>. Notice sent to applicant.
                    </div>
                  </td>
                  <td>
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar director">RR</div>
                      <div>
                        <div class="audit-actor-name">Clearance Authority</div>
                        <span class="audit-actor-role">Director IV &bull; Super Admin</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="audit-target-badge">R-2026-0875</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="security-hash-tag">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      #8803-3d
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- 2. TIMELINE VIEW (CHRONOLOGICAL STREAM) -->
          <div class="audit-timeline-container" id="auditTimelineViewContainer" style="display: none;">
            <div id="auditNoResultsTimeline" style="display: none; text-align: center; padding: 3rem 1rem;">
              <h4 style="color: #183823; margin: 0 0 0.5rem 0;">No Timeline Records Found</h4>
              <p style="color: #657b6f; margin: 0;">No event milestones match your active search terms or filters.</p>
            </div>

            <!-- Card 1 -->
            <div class="timeline-event-card" data-event-id="LOG-8812" data-category="approval" data-severity="success" data-timeframe="today">
              <div class="timeline-event-dot">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="timeline-card-content">
                <div class="timeline-header-meta">
                  <span class="event-type-chip approval">Director Final Approval</span>
                  <span class="timeline-time-text">Today &bull; 10:14 AM (10:14:22 PHT)</span>
                </div>
                <div class="audit-narrative-text" style="margin-bottom: 0.85rem;">
                  Director IV issued official Stage 2 approval for reservation <span class="audit-ref">R-2026-0888</span> submitted by <em>DA - National Rice Program</em>. Security gate pass endorsement generated.
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                  <div class="audit-actor-cell">
                    <div class="audit-actor-avatar director" style="width: 28px; height: 28px; font-size: 0.72rem;">RR</div>
                    <span style="font-size: 0.82rem; font-weight: 700; color: #1e3828;">Clearance Authority</span>
                  </div>
                  <span class="security-hash-tag">#8812-a7 &bull; Serrano Hall</span>
                </div>
              </div>
            </div>

            <!-- Card 2 -->
            <div class="timeline-event-card" data-event-id="LOG-8811" data-category="submission" data-severity="warning" data-timeframe="today">
              <div class="timeline-event-dot amber">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="3"><circle cx="12" cy="12" r="2"></circle></svg>
              </div>
              <div class="timeline-card-content">
                <div class="timeline-header-meta">
                  <span class="event-type-chip submission">New Reservation Submission</span>
                  <span class="timeline-time-text">Today &bull; 08:30 AM (08:30:14 PHT)</span>
                </div>
                <div class="audit-narrative-text" style="margin-bottom: 0.85rem;">
                  New facility reservation application submitted for <span class="audit-ref">R-2026-0891</span> by <em>Career Dev Division (CDD)</em>. Expected headcount: 85 PAX. Endorsement letter attached.
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                  <div class="audit-actor-cell">
                    <div class="audit-actor-avatar" style="width: 28px; height: 28px; font-size: 0.72rem;">JD</div>
                    <span style="font-size: 0.82rem; font-weight: 700; color: #1e3828;">Engr. Juan Dela Cruz (Chief, CDD)</span>
                  </div>
                  <span class="security-hash-tag">#8811-c2 &bull; Serrano Hall</span>
                </div>
              </div>
            </div>

            <!-- Card 3 -->
            <div class="timeline-event-card" data-event-id="LOG-8810" data-category="security" data-severity="info" data-timeframe="today">
              <div class="timeline-event-dot blue">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="3"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect></svg>
              </div>
              <div class="timeline-card-content">
                <div class="timeline-header-meta">
                  <span class="event-type-chip security">RBAC Security Privilege Update</span>
                  <span class="timeline-time-text">Today &bull; 08:15 AM (08:15:45 PHT)</span>
                </div>
                <div class="audit-narrative-text" style="margin-bottom: 0.85rem;">
                  User clearance credentials reconfigured for <em>Atty. Bernardo Castro</em>. Elevated to <strong>Stage 1: Recommending Officer</strong> with Legal Document Review privileges.
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                  <div class="audit-actor-cell">
                    <div class="audit-actor-avatar director" style="width: 28px; height: 28px; font-size: 0.72rem;">RR</div>
                    <span style="font-size: 0.82rem; font-weight: 700; color: #1e3828;">Clearance Authority</span>
                  </div>
                  <span class="security-hash-tag">#8810-f1 &bull; Target USR-1004</span>
                </div>
              </div>
            </div>

            <!-- Card 4 -->
            <div class="timeline-event-card" data-event-id="LOG-8809" data-category="maintenance" data-severity="info" data-timeframe="yesterday">
              <div class="timeline-event-dot blue">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="3"><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
              <div class="timeline-card-content">
                <div class="timeline-header-meta">
                  <span class="event-type-chip schedule">Central Master Calendar Synchronized</span>
                  <span class="timeline-time-text">Yesterday &bull; 04:45 PM (16:45:10 PHT)</span>
                </div>
                <div class="audit-narrative-text" style="margin-bottom: 0.85rem;">
                  Institutional Master Calendar synchronized with DA Central Office Agricultural Extension Summit schedule. 3 reserved dates blocked.
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                  <div class="audit-actor-cell">
                    <div class="audit-actor-avatar system" style="width: 28px; height: 28px; font-size: 0.72rem;">SYS</div>
                    <span style="font-size: 0.82rem; font-weight: 700; color: #1e3828;">Automated Calendar Daemon</span>
                  </div>
                  <span class="security-hash-tag">#8809-e8 &bull; Master Calendar</span>
                </div>
              </div>
            </div>

            <!-- Card 5 -->
            <div class="timeline-event-card" data-event-id="LOG-8808" data-category="maintenance" data-severity="success" data-timeframe="oct03">
              <div class="timeline-event-dot">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="timeline-card-content">
                <div class="timeline-header-meta">
                  <span class="event-type-chip maintenance">Facility Maintenance Servicing Completed</span>
                  <span class="timeline-time-text">Oct 03, 2026 &bull; 03:00 PM</span>
                </div>
                <div class="audit-narrative-text" style="margin-bottom: 0.85rem;">
                  Air conditioning preventative servicing completed for <em>4-H Learning Center</em> by General Services Unit. Facility restored to <strong>Available</strong> status.
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                  <div class="audit-actor-cell">
                    <div class="audit-actor-avatar" style="width: 28px; height: 28px; font-size: 0.72rem;">DM</div>
                    <span style="font-size: 0.82rem; font-weight: 700; color: #1e3828;">Arch. Danilo Mendoza (GSU Head)</span>
                  </div>
                  <span class="security-hash-tag">#8808-d4 &bull; 4-H Learning Ctr</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- Scripts -->
  <script src="js/admin_audit.js?v=<?php echo time(); ?>"></script>
</body>

</html>
