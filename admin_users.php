<?php
require_once __DIR__ . '/includes/init.php';
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator: Role-Based Access Control (RBAC) & User Management Portal
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User & Role Management (RBAC) | ATI Reservation Portal</title>
  <meta name="description"
    content="Role-Based Access Control and Institutional User Directory for Agricultural Training Institute Central Office.">

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
  <link rel="stylesheet" href="css/admin_users.css?v=<?php echo time(); ?>">
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

        <a href="admin_users.php" class="sidebar-menu-item active">
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
          <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()" aria-label="Toggle Sidebar Navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>

          <div class="topbar-title-wrap">
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Super Administrator &rsaquo; Access & Governance</span>
            <h1 class="topbar-page-title">Role-Based Access Control (RBAC)</h1>
          </div>
        </div>

        <div class="admin-topbar-right">
          <!-- Quick Search -->
          <div class="topbar-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="topbarSearchInput" placeholder="Quick search personnel..." oninput="handleUserSearch(this.value)" autocomplete="off">
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
              <span>Security & Clearance Governance</span>
            </div>
            <h2>User Management & Institutional Role Matrix</h2>
            <p>Manage administrative clearances, assign Recommending and Approval authorities, and oversee authorized staff and external agency accounts for ATI Central Office.</p>
          </div>

          <div class="admin-header-actions">
            <button type="button" class="btn-system-secondary" onclick="openAddUserModal()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
              </svg>
              <span>Add User</span>
            </button>
          </div>
        </section>

        <!-- ==========================================================================
             KPI STAT CARDS (USER DIRECTORY)
             ========================================================================== -->
        <section class="my-res-stats-grid admin-kpi-grid" aria-label="RBAC Statistics">
          <!-- Total Accounts -->
          <div class="my-res-stat-card active-filter" onclick="filterUsersByRole('all')">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiTotalUsers">18</span>
                <span class="stat-trend positive">17 Active</span>
              </div>
              <span class="stat-label">Total System Accounts</span>
            </div>
          </div>

          <!-- Final Clearance Authorities (Stage 2) -->
          <div class="my-res-stat-card" onclick="filterUsersByRole('clear')">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiDirectorCount">2</span>
                <span class="stat-pill-urgent" style="background: #fef3c7; color: #92400e; border-color: #fde68a; animation: none;">Stage 2</span>
              </div>
              <span class="stat-label">Final Clearance Authorities</span>
            </div>
          </div>

          <!-- Recommending Officers (Stage 1) -->
          <div class="my-res-stat-card" onclick="filterUsersByRole('rec')">
            <div class="stat-icon-box emerald">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiRecommendingCount">4</span>
                <span class="stat-trend positive">Division Chiefs</span>
              </div>
              <span class="stat-label">Stage 1: Recommending Officers</span>
            </div>
          </div>

          <!-- Staff Requestors & External Partners -->
          <div class="my-res-stat-card" onclick="filterUsersByRole('staff')">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="7" r="4"></circle>
                <path d="M5.5 21a8.38 8.38 0 0 1 13 0"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiStaffCount">12</span>
                <span class="stat-trend neutral">Staff & Partners</span>
              </div>
              <span class="stat-label">Authorized Requestors</span>
            </div>
          </div>
        </section>

        <!-- ==========================================================================
             TOOLBAR: SEARCH, ROLE PILLS & STATUS FILTER
             ========================================================================== -->
        <div class="my-res-toolbar-card admin-toolbar-card">
          <!-- Filter Tabs -->
          <div class="filter-tabs-group" role="tablist">
            <button type="button" class="user-filter-pill active" data-role="all" onclick="filterUsersByRole('all')">
              <span>All Accounts</span>
              <span class="tab-count-pill" id="pillCountAll">18</span>
            </button>
            <button type="button" class="user-filter-pill" data-role="clear" onclick="filterUsersByRole('clear')">
              <span>Stage 2: Final Clearance</span>
              <span class="tab-count-pill" id="pillCountClear">2</span>
            </button>
            <button type="button" class="user-filter-pill" data-role="rec" onclick="filterUsersByRole('rec')">
              <span>Stage 1: Recommending</span>
              <span class="tab-count-pill" id="pillCountRec">4</span>
            </button>
            <button type="button" class="user-filter-pill" data-role="staff" onclick="filterUsersByRole('staff')">
              <span>Division Staff</span>
              <span class="tab-count-pill" id="pillCountStaff">8</span>
            </button>
            <button type="button" class="user-filter-pill" data-role="external" onclick="filterUsersByRole('external')">
              <span>External Partners</span>
              <span class="tab-count-pill" id="pillCountExt">4</span>
            </button>
          </div>

          <!-- Controls Right -->
          <div class="toolbar-controls-right">
            <div class="search-box-wrap admin-search-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <input type="text" id="userSearchInput" class="search-input" placeholder="Search by name, email, or division..."
                oninput="handleUserSearch(this.value)" autocomplete="off">
              <button type="button" class="search-clear-btn" id="userSearchClear" onclick="clearUserSearch()" title="Clear search" style="display: none;">&times;</button>
            </div>

            <div class="select-filter-wrap">
              <select id="userStatusFilterSelect" class="venue-filter-select" onchange="handleStatusFilter(this.value)">
                <option value="all">All Statuses</option>
                <option value="active">Active Only</option>
                <option value="suspended">Suspended</option>
                <option value="pending">Pending Verification</option>
              </select>
            </div>
          </div>
        </div>

        <!-- ==========================================================================
             USERS & ROLES TABLE CARD
             ========================================================================== -->
        <div class="admin-table-card">
          <div class="admin-card-header">
            <div class="admin-card-title-group">
              <h3>Authorized Personnel Directory</h3>
              <p>Review authorized credentials, configure clearance privileges, or manage account access states.</p>
            </div>
            <div class="admin-card-header-badge">
              <span class="indicator-live-dot"></span>
              Live Access Governance
            </div>
          </div>

          <div class="admin-table-responsive">
            <table class="admin-data-table" id="usersTable">
              <thead>
                <tr>
                  <th style="width: 25%;">User & Official Email</th>
                  <th style="width: 20%;">Office / Division</th>
                  <th style="width: 22%;">Assigned Clearance Role</th>
                  <th style="width: 17%;">Privilege Scope</th>
                  <th style="width: 8%;">Status</th>
                  <th style="width: 8%; text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody id="usersTableBody">
                <!-- Empty search state row -->
                <tr id="userNoResultsRow" style="display: none;">
                  <td colspan="6" class="admin-empty-table-state">
                    <div class="empty-state-content">
                      <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#8ca394" stroke-width="1.8">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                      </svg>
                      <h4>No Personnel Matching Search</h4>
                      <p>No user records matched your search query or selected role filters.</p>
                      <button type="button" class="btn-system-secondary" style="font-size: 0.82rem; padding: 0.45rem 1.15rem;" onclick="resetAllUserFilters()">Reset Filters</button>
                    </div>
                  </td>
                </tr>

                <!-- User 1: Director IV -->
                <tr data-user-id="USR-1001" data-user-role="clear" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge director">
                        RR
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Clearance Authority</div>
                        <span class="user-email-text">director@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">Office of the Director (OD)</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-clearance">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                      Stage 2: Final Clearance Authority
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Final Approval & Gate Pass Authorization</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1001', 'Clearance Authority', 'director@ati.da.gov.ph', 'clear', 'Office of the Director')" title="Configure clearance role">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 2: Assistant Director -->
                <tr data-user-id="USR-1002" data-user-role="clear" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge director">
                        AA
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Antonieta J. Arceo</div>
                        <span class="user-email-text">asst.director@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">Office of the Assistant Director</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-clearance">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                      Stage 2: Final Clearance Authority
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Delegated Executive Approver</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1002', 'Antonieta J. Arceo', 'asst.director@ati.da.gov.ph', 'clear', 'Office of the Assistant Director')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 3: Chief CDD (Recommending) -->
                <tr data-user-id="USR-1003" data-user-role="rec" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge recommending">
                        JD
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Engr. Juan Dela Cruz</div>
                        <span class="user-email-text">juan.delacruz@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">Career Development Division (CDD)</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-recommend">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      Stage 1: Recommending Officer
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Schedule Verification & Recommending Endorsement</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1003', 'Engr. Juan Dela Cruz', 'juan.delacruz@ati.da.gov.ph', 'rec', 'Career Development Division')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1003')" title="Suspend access">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 4: Chief Legal Service -->
                <tr data-user-id="USR-1004" data-user-role="rec" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge recommending">
                        BC
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Atty. Bernardo Castro</div>
                        <span class="user-email-text">bernardo.castro@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">DA - Legal Service Office</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-recommend">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      Stage 1: Recommending Officer
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Legal Clearance & Document Endorsement</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1004', 'Atty. Bernardo Castro', 'bernardo.castro@ati.da.gov.ph', 'rec', 'DA - Legal Service Office')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1004')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 5: Chief AFD -->
                <tr data-user-id="USR-1005" data-user-role="rec" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge recommending">
                        RS
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Rowena Santos</div>
                        <span class="user-email-text">rowena.santos@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">Administrative & Finance Division (AFD)</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-recommend">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      Stage 1: Recommending Officer
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Financial Clearance & Endorsement</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1005', 'Rowena Santos', 'rowena.santos@ati.da.gov.ph', 'rec', 'Administrative & Finance Division')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1005')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 6: Chief Budget -->
                <tr data-user-id="USR-1006" data-user-role="rec" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge recommending">
                        LB
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Liza Bautista</div>
                        <span class="user-email-text">liza.bautista@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">Budget & Accounting Section</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-recommend">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                      Stage 1: Recommending Officer
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Budget Allocation Verification</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1006', 'Liza Bautista', 'liza.bautista@ati.da.gov.ph', 'rec', 'Budget & Accounting Section')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1006')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 7: GSU Head -->
                <tr data-user-id="USR-1007" data-user-role="gsu" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge gsu">
                        DM
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Arch. Danilo Mendoza</div>
                        <span class="user-email-text">danilo.mendoza@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">General Services Unit (GSU)</span>
                  </td>
                  <td>
                    <span class="user-role-badge" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                      Facility & Maintenance Officer
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Venue Maintenance Hold & Physical Inspection</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1007', 'Arch. Danilo Mendoza', 'danilo.mendoza@ati.da.gov.ph', 'gsu', 'General Services Unit')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1007')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 8: Staff ISD -->
                <tr data-user-id="USR-1008" data-user-role="staff" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge staff">
                        CL
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Carmela Lim</div>
                        <span class="user-email-text">carmela.lim@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">Information Services Division (ISD)</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-staff">
                      Division Personnel / Requestor
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Submit Reservations & Track Endorsements</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1008', 'Carmela Lim', 'carmela.lim@ati.da.gov.ph', 'staff', 'Information Services Division')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1008')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 9: Staff National Rice Program -->
                <tr data-user-id="USR-1009" data-user-role="staff" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge staff">
                        GV
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Grace Valenzuela</div>
                        <span class="user-email-text">grace.valenzuela@da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">DA - National Rice Program</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-staff">
                      Division Personnel / Requestor
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Submit Reservations & Track Endorsements</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1009', 'Grace Valenzuela', 'grace.valenzuela@da.gov.ph', 'staff', 'DA - National Rice Program')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1009')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 10: Staff PAD -->
                <tr data-user-id="USR-1010" data-user-role="staff" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge staff">
                        ER
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Eduardo Ramos</div>
                        <span class="user-email-text">eduardo.ramos@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">Partnerships & Accreditation (PAD)</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-staff">
                      Division Personnel / Requestor
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Submit Reservations & Track Endorsements</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1010', 'Eduardo Ramos', 'eduardo.ramos@ati.da.gov.ph', 'staff', 'Partnerships & Accreditation')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1010')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 11: Dormitory Supervisor -->
                <tr data-user-id="USR-1011" data-user-role="gsu" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge gsu">
                        DA
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Dexter Alcantara</div>
                        <span class="user-email-text">dexter.alcantara@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">ATI Dormitory Management Unit</span>
                  </td>
                  <td>
                    <span class="user-role-badge" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                      Facility & Dormitory Staff
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Dormitory Bed Allotment & Check-In Log</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1011', 'Dexter Alcantara', 'dexter.alcantara@ati.da.gov.ph', 'gsu', 'ATI Dormitory Management Unit')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1011')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 12: External BPI -->
                <tr data-user-id="USR-1012" data-user-role="external" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge external">
                        MS
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Dr. Maria Santos</div>
                        <span class="user-email-text">maria.santos@bpi.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">Bureau of Plant Industry (BPI)</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-external">
                      External Agency Partner
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">External Facility Booking Applicant</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1012', 'Dr. Maria Santos', 'maria.santos@bpi.da.gov.ph', 'external', 'Bureau of Plant Industry')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1012')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 13: External PhilRice -->
                <tr data-user-id="USR-1013" data-user-role="external" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge external">
                        RP
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Ramon Pascual</div>
                        <span class="user-email-text">ramon.pascual@philrice.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">PhilRice - Extension Division</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-external">
                      External Agency Partner
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">External Facility Booking Applicant</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1013', 'Ramon Pascual', 'ramon.pascual@philrice.gov.ph', 'external', 'PhilRice Extension')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1013')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 14: External BAR -->
                <tr data-user-id="USR-1014" data-user-role="external" data-user-status="active">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge external">
                        PG
                        <span class="user-online-dot"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Patricia Mae Gomez</div>
                        <span class="user-email-text">pmgomez@bar.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">DA - Bureau of Agricultural Research</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-external">
                      External Agency Partner
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">External Facility Booking Applicant</span>
                  </td>
                  <td>
                    <span class="status-badge-chip active">
                      <span class="chip-status-dot"></span>
                      Active
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1014', 'Patricia Mae Gomez', 'pmgomez@bar.gov.ph', 'external', 'DA - BAR')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('USR-1014')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        <span>Suspend</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 15: External Pending Verification -->
                <tr data-user-id="USR-1015" data-user-role="external" data-user-status="pending">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge external">
                        JM
                        <span class="user-online-dot offline"></span>
                      </div>
                      <div>
                        <div class="user-name-title">External Requestor</div>
                        <span class="user-email-text">external.requestor@rfo3.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">DA Regional Field Office III</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-external">
                      External Agency Partner
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Pending Document Accreditation</span>
                  </td>
                  <td>
                    <span class="status-badge-chip pending">
                      <span class="chip-status-dot"></span>
                      Pending
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1015', 'External Requestor', 'external.requestor@rfo3.da.gov.ph', 'external', 'DA RFO III')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- User 16: Staff Suspended -->
                <tr data-user-id="USR-1016" data-user-role="staff" data-user-status="suspended">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge gsu">
                        GO
                        <span class="user-online-dot offline"></span>
                      </div>
                      <div>
                        <div class="user-name-title">Gilbert Ocampo</div>
                        <span class="user-email-text">gilbert.ocampo@ati.da.gov.ph</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text">Security & Facilities Oversight</span>
                  </td>
                  <td>
                    <span class="user-role-badge status-role-staff">
                      Facility & Dormitory Staff
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag">Access Suspended by Super Admin</span>
                  </td>
                  <td>
                    <span class="status-badge-chip suspended">
                      <span class="chip-status-dot"></span>
                      Suspended
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('USR-1016', 'Gilbert Ocampo', 'gilbert.ocampo@ati.da.gov.ph', 'staff', 'Security & Facilities')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Role</span>
                      </button>
                      <button type="button" class="btn-user-action status-toggle activate" onclick="toggleUserStatus('USR-1016')" title="Reactivate user access">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Activate</span>
                      </button>
                    </div>
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
       MODAL: CONFIGURE ROLE & PRIVILEGES
       ========================================================================== -->
  <div class="admin-modal-overlay" id="editRoleModal" style="display: none;">
    <div class="admin-modal-card sm" style="max-width: 540px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <span class="modal-badge-ref">RBAC Clearance</span>
          <h4>Configure Access Level & Permissions</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeEditRoleModal()">&times;</button>
      </div>

      <div class="admin-modal-body">
        <div class="modal-applicant-summary" style="margin-bottom: 1.25rem;">
          <div class="user-avatar-circle" id="editRoleUserAvatar">JD</div>
          <div class="applicant-meta">
            <h5 id="editRoleUserName">Engr. Juan Dela Cruz</h5>
            <p id="editRoleUserDept">Career Development Division (CDD)</p>
            <span class="applicant-contact" id="editRoleUserEmail">juan.delacruz@ati.da.gov.ph</span>
          </div>
        </div>

        <form id="editRoleForm" onsubmit="saveRoleChanges(event)">
          <div class="form-group-decline" style="margin-bottom: 1rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.4rem;">Designated Clearance Level</label>
            <select id="roleSelectDropdown" style="width: 100%; height: 42px; padding: 0 1rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; font-weight: 600;">
              <option value="clear">Final Clearance Authority</option>
              <option value="rec">Recommending Officer</option>
              <option value="gsu">Facility & Maintenance Officer (GSU)</option>
              <option value="staff">Division Personnel / Requestor (ATI Staff)</option>
              <option value="external">External Agency Partner (BPI, PhilRice, Guest)</option>
            </select>
          </div>

          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.5rem;">Clearance Privileges & Execution Rights</label>
          <div class="permission-checkbox-grid">
            <label class="perm-checkbox-item">
              <input type="checkbox" id="permInspect" checked>
              <div class="perm-checkbox-text">
                <strong>Inspect & Verify Request Attachments</strong>
                <span>Can view signed endorsement letters and applicant credentials</span>
              </div>
            </label>

            <label class="perm-checkbox-item">
              <input type="checkbox" id="permRecommend" checked>
              <div class="perm-checkbox-text">
                <strong>Issue Stage 1 Recommendation</strong>
                <span>Authorized to recommend reservations forward to Director IV</span>
              </div>
            </label>

            <label class="perm-checkbox-item">
              <input type="checkbox" id="permClearance">
              <div class="perm-checkbox-text">
                <strong>Executive Final Clearance & Gate Pass Approval</strong>
                <span>Final authority to confirm bookings and issue security gate passes</span>
              </div>
            </label>

            <label class="perm-checkbox-item">
              <input type="checkbox" id="permMaintenance">
              <div class="perm-checkbox-text">
                <strong>Facility Maintenance & Schedule Hold</strong>
                <span>Can place auditoriums or dormitories under maintenance hold</span>
              </div>
            </label>

            <label class="perm-checkbox-item">
              <input type="checkbox" id="permUserMgmt">
              <div class="perm-checkbox-text">
                <strong>System User & RBAC Management</strong>
                <span>Can configure accounts, roles, and administrative permissions</span>
              </div>
            </label>
          </div>

          <div class="admin-modal-footer" style="padding-top: 1.5rem; margin-top: 1.5rem; border-top: 1px solid #edf3ef;">
            <button type="button" class="btn-system-secondary" onclick="closeEditRoleModal()">Cancel</button>
            <button type="submit" class="btn-approve-modal">Save Role Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: REGISTER NEW USER
       ========================================================================== -->
  <div class="admin-modal-overlay" id="addUserModal" style="display: none;">
    <div class="admin-modal-card sm" style="max-width: 520px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <span class="modal-badge-ref">New Registration</span>
          <h4>Register Institutional Personnel</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeAddUserModal()">&times;</button>
      </div>

      <div class="admin-modal-body">
        <form id="addUserForm" onsubmit="submitNewUser(event)">
          <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            <div>
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Full Name with Title</label>
              <input type="text" id="newUserName" placeholder="e.g. Engr. Mark Joseph Rivera" required
                style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; box-sizing: border-box;">
            </div>

            <div>
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Official Government Email</label>
              <input type="email" id="newUserEmail" placeholder="e.g. mark.rivera@ati.da.gov.ph" required
                style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; box-sizing: border-box;">
            </div>

            <div>
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Assigned Division / Bureau</label>
              <input type="text" id="newUserDept" placeholder="e.g. Policy & Planning Division (PPD)" required
                style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; box-sizing: border-box;">
            </div>

            <div>
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Designated Clearance Level</label>
              <select id="newUserRole"
                style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; font-weight: 600; box-sizing: border-box;">
                <option value="staff">Division Personnel / Requestor (Standard Staff)</option>
                <option value="rec">Recommending Officer</option>
                <option value="clear">Final Clearance Authority</option>
                <option value="gsu">Facility & Maintenance Officer (GSU)</option>
                <option value="external">External Agency Partner (DA Bureaus / Visitors)</option>
              </select>
            </div>
          </div>

          <div class="admin-modal-footer" style="padding-top: 1.5rem; margin-top: 1.5rem; border-top: 1px solid #edf3ef;">
            <button type="button" class="btn-system-secondary" onclick="closeAddUserModal()">Cancel</button>
            <button type="submit" class="btn-approve-modal">Complete Registration</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- Scripts -->
  <script src="js/admin_users.js?v=<?php echo time(); ?>"></script>
</body>

</html>
