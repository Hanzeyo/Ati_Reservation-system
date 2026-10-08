<?php
require_once __DIR__ . '/includes/init.php';
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator: Dual-Stage Approvals Queue & Endorsement Center
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Approvals Queue & Endorsement Desk | ATI Reservation Portal</title>
  <meta name="description"
    content="Official Dual-Stage Approvals Queue, Recommendation Workflow, and Institutional Clearance Engine for Agricultural Training Institute Central Office.">

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
  <link rel="stylesheet" href="css/admin_approvals.css?v=<?php echo time(); ?>">
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

        <!-- Approvals Queue: ACTIVE HERE -->
        <a href="admin_approvals.php<?= $isRecommendation ? '?role=recommendation' : '' ?>" class="sidebar-menu-item active">
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

        <a href="admin_facilities.php<?= $isRecommendation ? '?role=recommendation' : '' ?>" class="sidebar-menu-item">
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

      <!-- Topbar Header -->
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
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Administrative Governance</span>
            <h1 class="topbar-page-title">Executive Approvals Queue</h1>
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
            <span id="currentDateDisplay">October 2026</span>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="admin-content-container">

        <!-- Page Header -->
        <section class="my-res-header admin-page-header">
          <div class="my-res-header-text">
            <div class="my-res-top-badge admin-gold-badge">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <span>Dual-Stage Endorsement Engine</span>
            </div>
            <h2>Institutional Reservation Endorsement Desk</h2>
          </div>

          <div class="admin-header-actions" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; justify-content: flex-end;">
            <div class="persona-switch-buttons" style="display: flex; align-items: center; gap: 0.45rem; background: #ffffff; padding: 0.35rem 0.65rem; border-radius: 10px; border: 1.5px solid #dce8e0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
              <span style="font-size: 0.72rem; font-weight: 800; color: #627b6c; text-transform: uppercase; margin-right: 0.25rem;">Authority Level:</span>
              <button type="button" id="btnRoleRec" class="btn-persona-toggle <?= $isRecommendation ? 'active' : '' ?>" onclick="switchAdminRole('recommendation')">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Stage 1 (Recommendation)
              </button>
              <button type="button" id="btnRoleClear" class="btn-persona-toggle <?= !$isRecommendation ? 'active director' : '' ?>" onclick="switchAdminRole('clearance')">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Stage 2 (Final Clearance)
              </button>
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
              <button type="button" class="btn-system-secondary" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                <span>Print Sheet</span>
              </button>
              <a href="admin_schedule.php" class="btn-system-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Master Schedule</span>
              </a>
            </div>
          </div>
        </section>



        <!-- ==========================================================================
             KPI STAT CARDS
             ========================================================================== -->
        <section class="my-res-stats-grid admin-kpi-grid" aria-label="Approvals Queue Metrics">
          <!-- Awaiting Action -->
          <div class="my-res-stat-card admin-stat-highlight" onclick="applyApprovalsFilterTab('awaiting')">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiAwaitingCount">3</span>
                <span class="stat-pill-urgent">Needs Action</span>
              </div>
              <span class="stat-label">Awaiting My Action</span>
            </div>
          </div>

          <!-- Stage 1 Queue -->
          <div class="my-res-stat-card" onclick="applyApprovalsFilterTab('stage1')">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiStage1Count">3</span>
                <span class="stat-trend">Stage 1</span>
              </div>
              <span class="stat-label">Pending Recommendation</span>
            </div>
          </div>

          <!-- Stage 2 Queue -->
          <div class="my-res-stat-card" onclick="applyApprovalsFilterTab('stage2')">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiStage2Count">2</span>
                <span class="stat-trend positive">Stage 2</span>
              </div>
              <span class="stat-label">Pending Final Clearance</span>
            </div>
          </div>

          <!-- Cleared & Permitted -->
          <div class="my-res-stat-card" onclick="applyApprovalsFilterTab('approved')">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiApprovedCount">1</span>
                <span class="stat-trend positive">Official</span>
              </div>
              <span class="stat-label">Cleared & Permits Issued</span>
            </div>
          </div>
        </section>

        <!-- ==========================================================================
             TOOLBAR & FILTER CONTROLS
             ========================================================================== -->
        <div class="approvals-toolbar">
          <!-- Search Box -->
          <div class="approvals-search-wrapper">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="approvalsSearchInput" placeholder="Filter by Ref #, agency, applicant, or event..." oninput="renderApprovalsTable()">
          </div>

          <div class="approvals-toolbar-actions">
            <button type="button" class="btn-system-secondary" onclick="applyApprovalsFilterTab('all')" style="padding: 0.52rem 0.85rem; border-radius: 10px; height: 100%;">
              View All
            </button>
            
            <!-- Venue Category Filter -->
            <select id="approvalsVenueFilter" onchange="renderApprovalsTable()" style="padding: 0.52rem 0.85rem; border: 1.5px solid #dce8e0; border-radius: 10px; font-family: inherit; font-size: 0.82rem; font-weight: 600; color: #172c1f; background: #fff; height: 100%;">
              <option value="all">All Facility Types</option>
              <option value="halls">Training & Function Halls</option>
              <option value="dorms">Dormitory Suites</option>
            </select>
          </div>
        </div>

        <!-- ==========================================================================
             APPROVALS QUEUE DATA TABLE
             ========================================================================== -->
        <div class="approvals-table-card">
          <table class="approvals-table" id="approvalsTable">
            <thead>
              <tr>
                <th style="width: 40px; text-align: center;">
                  <input type="checkbox" id="selectAllRowsCb" onchange="toggleSelectAllRows(this.checked)" title="Select all visible">
                </th>
                <th>Request Ref & Urgency</th>
                <th>Applicant & Agency</th>
                <th>Facility & PAX</th>
                <th>Target Dates & Time</th>
                <th>Approval Progress</th>
                <th style="text-align: right;">Action Decisions</th>
              </tr>
            </thead>
            <tbody id="approvalsTableBody">
              <!-- Dynamically rendered via renderApprovalsTable() -->
            </tbody>
          </table>
        </div>

        <style>
          #approvalsTable th:last-child, #approvalsTable td:last-child {
            background: #f4fdf8;
            border-left: 1px solid #dce8e0;
          }
          #approvalsTable td:last-child {
            box-shadow: inset 3px 0 0 #a9d6bb;
          }
        </style>
      </main>
    </div>
  </div>

  <!-- ==========================================================================
       STICKY FLOATING BULK ACTIONS BAR
       ========================================================================== -->
  <div class="bulk-actions-floating-bar" id="bulkActionsBar">
    <div class="bulk-selection-count">
      <span class="bulk-selection-badge" id="bulkSelectedCount">0</span>
      <span>Selected Requests</span>
    </div>
    <div class="bulk-action-buttons">
      <button type="button" class="btn-bulk-action recommend" onclick="executeBulkEndorse()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span>Batch Recommend (Stage 1)</span>
      </button>
      <button type="button" class="btn-bulk-action clearance" onclick="executeBulkClearance()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
        <span>Batch Issue Clearance (Stage 2)</span>
      </button>
      <button type="button" class="btn-bulk-action secondary" onclick="clearRowSelections()">Cancel</button>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 1: COMPREHENSIVE DOSSIER INSPECTOR
       ========================================================================== -->
  <div class="admin-modal-overlay" id="dossierModal" style="display: none;">
    <div class="admin-modal-card" style="max-width: 840px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <div class="my-res-top-badge admin-gold-badge" style="margin-bottom: 0.2rem;">Official Booking Dossier</div>
          <h4>Reservation Packet: <span id="dossierModalRef" style="font-family: monospace; color: #174d2f;">REF</span></h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeDossierModal()">&times;</button>
      </div>

      <div class="admin-modal-body">
        <div class="dossier-grid">
          <!-- Left Column: Details -->
          <div>
            <div class="dossier-card-section" style="margin-bottom: 1rem;">
              <div class="dossier-section-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span>Applicant Information</span>
              </div>
              <div class="dossier-data-grid">
                <div class="dossier-field-item">
                  <label>Focal Officer</label>
                  <div class="field-value" id="dossierApplicantName">--</div>
                </div>
                <div class="dossier-field-item">
                  <label>Agency / Division</label>
                  <div class="field-value" id="dossierApplicantAgency">--</div>
                </div>
                <div class="dossier-field-item" style="grid-column: 1 / -1;">
                  <label>Official Contact</label>
                  <div class="field-value" id="dossierContact">--</div>
                </div>
              </div>
            </div>

            <div class="dossier-card-section">
              <div class="dossier-section-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Event Logistics & Specifications</span>
              </div>
              <div class="dossier-data-grid" style="margin-bottom: 0.85rem;">
                <div class="dossier-field-item">
                  <label>Venue / Facility</label>
                  <div class="field-value" id="dossierVenue">--</div>
                </div>
                <div class="dossier-field-item">
                  <label>Attendees Count</label>
                  <div class="field-value" id="dossierPax">--</div>
                </div>
                <div class="dossier-field-item">
                  <label>Target Dates</label>
                  <div class="field-value" id="dossierDates">--</div>
                </div>
                <div class="dossier-field-item">
                  <label>Duration / Slots</label>
                  <div class="field-value" id="dossierTimeSlot">--</div>
                </div>
              </div>
              <div class="dossier-field-item" style="margin-bottom: 0.65rem;">
                <label>Official Activity Title</label>
                <div class="field-value" id="dossierEventTitle" style="font-weight: 800; color: #174d2f;">--</div>
              </div>
              <div class="dossier-field-item" style="margin-bottom: 0.65rem;">
                <label>Requested Audio-Visual & Equipment</label>
                <div class="field-value" id="dossierEquipment" style="font-size: 0.82rem; font-weight: 500;">--</div>
              </div>
              <div class="dossier-field-item">
                <label>Special Remarks & Accommodation Notes</label>
                <div class="field-value" id="dossierNotes" style="font-size: 0.82rem; font-weight: 500; color: #557262;">--</div>
              </div>
            </div>
          </div>

          <!-- Right Column: Workflow Timeline & Decision Box -->
          <div>
            <div class="dossier-card-section">
              <div class="dossier-section-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Dual-Stage Endorsement Trail</span>
              </div>
              <div id="dossierTimelineList">
                <!-- Timeline items rendered via JS -->
              </div>
            </div>

            <!-- Action Box -->
            <div id="dossierDecisionBox">
              <!-- Rendered based on stage -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 2: RETURN FOR REVISION / DISAPPROVAL
       ========================================================================== -->
  <div class="admin-modal-overlay" id="disapproveModal" style="display: none;">
    <div class="admin-modal-card sm">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4 style="color: #b91c1c;">Return / Withhold Clearance</h4>
          <span style="font-size: 0.8rem; color: #637f6f;">Reference: <strong id="disapproveRef">REF</strong></span>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeDisapproveModal()">&times;</button>
      </div>

      <div class="admin-modal-body">
        <div class="form-group-decline" style="margin-bottom: 0.85rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Primary Grounds for Action</label>
          <select id="disapproveReasonSelect" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            <option value="Schedule Conflict with Priority DA Executive Activity">Schedule Conflict with Priority DA Executive Activity</option>
            <option value="Facility Scheduled for Preventive Engineering Maintenance">Facility Scheduled for Preventive Engineering Maintenance</option>
            <option value="Incomplete Supporting Documents / Missing Letter of Intent">Incomplete Supporting Documents / Missing Letter of Intent</option>
            <option value="PAX Exceeds Approved Fire & Safety Venue Capacity">PAX Exceeds Approved Fire & Safety Venue Capacity</option>
            <option value="Administrative Grounds / Discretion of the Directorate">Administrative Grounds / Discretion of the Directorate</option>
          </select>
        </div>

        <div class="form-group-decline" style="margin-bottom: 1.25rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Official Corrective Instructions for Applicant</label>
          <textarea id="disapproveRemarks" rows="3" placeholder="Specify required revisions, alternative available dates, or compliance remarks..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;"></textarea>
        </div>

        <div class="admin-modal-footer" style="padding: 1rem 0 0 0; background: none; border-top: 1px solid #edf3ef;">
          <button type="button" class="btn-system-secondary" onclick="closeDisapproveModal()">Cancel</button>
          <button type="button" class="btn-confirm-decline" onclick="confirmDisapproval()">Submit Official Notice</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 3: OFFICIAL PERMIT & GATE PASS PREVIEW
       ========================================================================== -->
  <div class="admin-modal-overlay" id="permitPreviewModal" style="display: none;">
    <div class="admin-modal-card" style="max-width: 780px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4>Official Reservation Permit & Campus Gate Pass</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closePermitModal()">&times;</button>
      </div>

      <div class="admin-modal-body">
        <div id="permitPrintWrapper">
          <div class="permit-document-sheet">
            <div class="permit-header">
              <img src="assets/images/ATI_Logo.png" alt="ATI Logo" class="permit-header-logo">
              <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; color: #4b5563;">Republic of the Philippines • Department of Agriculture</div>
              <h2 class="permit-title">Agricultural Training Institute</h2>
              <p class="permit-subtitle">ATI Central Office, Elliptical Road, Diliman, Quezon City</p>
              <div style="margin-top: 0.85rem; display: inline-block; background: #eaf3ed; border: 1px solid #174d2f; color: #174d2f; font-weight: 800; font-size: 0.82rem; padding: 0.25rem 0.85rem; border-radius: 999px;">
                OFFICIAL FACILITY RESERVATION PERMIT & GATE PASS
              </div>
            </div>

            <table class="permit-table">
              <tr>
                <td class="label-cell">Permit Reference ID</td>
                <td><strong id="permitModalRef" style="font-family: monospace; color: #174d2f;">--</strong></td>
              </tr>
              <tr>
                <td class="label-cell">Issuance Date</td>
                <td id="permitIssueDate">--</td>
              </tr>
              <tr>
                <td class="label-cell">Requesting Division / Agency</td>
                <td><strong id="permitAgency">--</strong> (<span id="permitApplicant">--</span>)</td>
              </tr>
              <tr>
                <td class="label-cell">Approved Activity Title</td>
                <td><strong id="permitEventTitle">--</strong></td>
              </tr>
              <tr>
                <td class="label-cell">Authorized Facility / Dorm</td>
                <td><strong id="permitVenue" style="color: #174d2f;">--</strong> (<span id="permitPax">--</span>)</td>
              </tr>
              <tr>
                <td class="label-cell">Approved Duration & Dates</td>
                <td><strong id="permitSchedule">--</strong></td>
              </tr>
            </table>

            <div class="permit-signatures-row">
              <div class="signature-block">
                <div class="signature-line">
                  <p class="signature-name">RECOMMENDING OFFICER</p>
                  <p class="signature-title">Administrative Services & Logistics Unit</p>
                  <small style="color: #6b7280; font-size: 0.7rem;">Verified Logistical Feasibility</small>
                </div>
              </div>
              <div class="signature-block">
                <div class="signature-line">
                  <p class="signature-name">CLEARANCE AUTHORITY</p>
                  <p class="signature-title">Office of the Executive Director</p>
                  <small style="color: #6b7280; font-size: 0.7rem;">Official Executive Clearance Granted</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="admin-modal-footer" style="padding: 1.25rem 0 0 0; background: none; border-top: 1px solid #edf3ef; justify-content: space-between;">
          <button type="button" class="btn-system-secondary" onclick="closePermitModal()">Close Window</button>
          <button type="button" class="btn-system-primary" onclick="printPermitDocument()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            <span>Print Official Permit (Gate Pass)</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- JavaScript Engine -->
  <script src="js/admin_approvals.js?v=<?php echo time(); ?>"></script>
</body>

</html>
