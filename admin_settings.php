<?php
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Comprehensive Institutional System Settings & Governance Portal
 */

date_default_timezone_set('Asia/Manila');
$currentDateFormatted = date('D, M d, Y \bull; h:i:s A');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Settings &amp; Governance - ATI Central Office</title>
  <meta name="description" content="Administrative system settings, operational policies, pricing matrices, automated routing, and backup governance for the ATI Reservation System.">

  <!-- Core & Unified Admin Styles -->
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/my_reservations.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/admin_dashboard.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/admin_settings.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
</head>

<body class="admin-portal-body">

  <div class="admin-layout-container">

    <!-- ==========================================================================
         SIDEBAR NAVIGATION
         ========================================================================== -->
    <aside class="admin-sidebar" id="adminSidebar">
      <!-- Sidebar Header / Institutional Brand -->
      <div class="admin-sidebar-header">
        <a href="admin_dashboard.php" class="admin-brand-link">
          <img src="assets/images/ATI_Logo.png" alt="ATI Logo" class="admin-logo-img">
          <div class="admin-brand-titles">
            <h4>ATI Portal</h4>
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

        <a href="admin_approvals.php" class="sidebar-menu-item">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="16" y1="13" x2="8" y2="13"></line>
              <line x1="16" y1="17" x2="8" y2="17"></line>
            </svg>
          </div>
          <span class="menu-label">Approvals Queue</span>
          <span class="sidebar-count-badge amber">5</span>
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

        <div class="sidebar-section-title">ACCESS &amp; GOVERNANCE</div>

        <a href="admin_users.php" class="sidebar-menu-item">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <span class="menu-label">User &amp; Role Access</span>
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
          <span class="menu-label">Facilities &amp; Dorms</span>
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

        <a href="admin_settings.php" class="sidebar-menu-item active">
          <div class="menu-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="3"></circle>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
          </div>
          <span class="menu-label">System Settings</span>
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
          <div class="sidebar-avatar clear">DIR</div>
          <div class="sidebar-user-info">
            <h5>Executive Admin</h5>
            <p>Central Operations &amp; IT</p>
          </div>
        </div>
        <a href="index.php" class="sidebar-signout-btn" title="Sign Out">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
        </a>
      </div>
    </aside>

    <!-- ==========================================================================
         MAIN WRAPPER & TOPBAR
         ========================================================================== -->
    <div class="admin-main-wrap">

      <!-- Topbar Header -->
      <header class="admin-topbar">
        <div class="admin-topbar-left">
          <button type="button" class="admin-sidebar-toggle-btn" id="adminSidebarToggle" aria-label="Toggle Sidebar Navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>

          <div class="topbar-title-wrap">
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Governance &amp; Administration</span>
            <h1 class="topbar-page-title">Institutional System Settings</h1>
          </div>
        </div>

        <div class="admin-topbar-right">
          <!-- Date Badge with Live Clock -->
          <div class="topbar-date-pill">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span id="currentDateDisplay"><?php echo $currentDateFormatted; ?></span>
          </div>
        </div>
      </header>

      <!-- Main Scrollable Body -->
      <main class="admin-content-container">

        <div class="settings-content-wrapper">

          <!-- Overview Hero Banner -->
          <section class="settings-hero-card">
            <div class="settings-hero-info">
              <h2>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <circle cx="12" cy="12" r="3"></circle>
                  <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                <span>System Administration &amp; Operational Policies</span>
              </h2>
              <p>Configure institutional operating hours, advance booking limits, dormitory and venue pricing matrices, approval routing, automated communication dispatchers, and database backup snapshots.</p>
            </div>

            <div class="settings-hero-badges">
              <span class="settings-badge-pill active">
                <span class="dot"></span>
                System Status: Live Online
              </span>
              <span class="settings-badge-pill">
                Enterprise v3.4.2
              </span>
            </div>
          </section>

          <!-- Settings Tab Buttons -->
          <nav class="settings-tabs-container" aria-label="Settings Categories">
            <button type="button" class="settings-tab-btn active" onclick="switchSettingsTab('general', this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
              <span>General &amp; Agency</span>
            </button>
            <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('policies', this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <span>Booking Policies</span>
            </button>
            <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('pricing', this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
              <span>Pricing &amp; Rate Matrix</span>
            </button>
            <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('workflow', this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
              <span>Routing &amp; Approval</span>
            </button>
            <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('notifications', this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
              <span>Notifications &amp; Alerts</span>
            </button>
            <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('security', this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
              <span>Security &amp; Backup</span>
            </button>
          </nav>

          <!-- ==================================================================
               TAB 1: GENERAL & AGENCY PROFILE
               ================================================================== -->
          <div class="settings-panel active" id="panel-general">
            <section class="settings-section-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                    <span>Agency Identity &amp; System Parameters</span>
                  </h3>
                  <p>Basic organization profile, intranet address, and regional timezone.</p>
                </div>
                <span class="settings-header-tag">Core Parameters</span>
              </div>

              <div class="settings-fields-grid">
                <div class="settings-field-group">
                  <label class="settings-field-label" for="setAgencyName">Official Agency Name</label>
                  <input type="text" id="setAgencyName" class="settings-input" value="Agricultural Training Institute - Central Office">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setSystemName">System Portal Title</label>
                  <input type="text" id="setSystemName" class="settings-input" value="ATI Facility &amp; Dormitory Reservation Portal">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setIntranetUrl">Intranet Base URL</label>
                  <input type="text" id="setIntranetUrl" class="settings-input" value="http://localhost/Ati_Reservation-system/">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setOperatingDays">Official Work Schedule</label>
                  <input type="text" id="setOperatingDays" class="settings-input" value="Mon - Fri (8:00 AM – 5:00 PM)">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setOperatingHoursStart">Daily Opening Time</label>
                  <input type="time" id="setOperatingHoursStart" class="settings-input" value="08:00">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setOperatingHoursEnd">Daily Closing Time</label>
                  <input type="time" id="setOperatingHoursEnd" class="settings-input" value="17:00">
                </div>
              </div>
            </section>

            <!-- Maintenance Mode Card -->
            <section class="settings-section-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <span>System Maintenance Control</span>
                  </h3>
                  <p>Temporarily place the client reservation portal into scheduled IT maintenance mode.</p>
                </div>
                <span class="settings-header-tag" style="color: #b45309; background: #fef3c7; border-color: #fde68a;">Operational Mode</span>
              </div>

              <div class="settings-switch-row" style="margin-bottom: 1rem;">
                <div class="settings-switch-info">
                  <h5>Enable System Maintenance Mode</h5>
                  <p>When enabled, client staff will see the maintenance notice banner and new booking submissions will be paused.</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="setMaintenanceMode">
                  <span class="slider-round"></span>
                </label>
              </div>

              <div class="settings-field-group full-width">
                <label class="settings-field-label" for="setMaintenanceMessage">Maintenance Alert Notice Banner</label>
                <textarea id="setMaintenanceMessage" class="settings-textarea" rows="2">The ATI Reservation Portal is undergoing scheduled IT maintenance. Normal booking operations will resume at 1:00 PM.</textarea>
              </div>
            </section>
          </div>

          <!-- ==================================================================
               TAB 2: BOOKING POLICIES & ADVANCE WINDOWS
               ================================================================== -->
          <div class="settings-panel" id="panel-policies">
            <section class="settings-section-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span>Advance Windows &amp; Scheduling Constraints</span>
                  </h3>
                  <p>Define rules on how far in advance personnel may file requests and duration caps.</p>
                </div>
                <span class="settings-header-tag">Policies</span>
              </div>

              <div class="settings-fields-grid">
                <div class="settings-field-group">
                  <label class="settings-field-label" for="setMinAdvanceDays">
                    <span>Minimum Advance Notice (Days)</span>
                    <span class="settings-field-hint">e.g. 3 days prior</span>
                  </label>
                  <input type="number" id="setMinAdvanceDays" class="settings-input" min="1" max="30" value="3">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setMaxAdvanceDays">
                    <span>Maximum Advance Booking Window</span>
                    <span class="settings-field-hint">e.g. 90 days ahead</span>
                  </label>
                  <input type="number" id="setMaxAdvanceDays" class="settings-input" min="15" max="365" value="90">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setMaxConsecutiveDays">
                    <span>Max Consecutive Event Duration</span>
                    <span class="settings-field-hint">Days per request</span>
                  </label>
                  <input type="number" id="setMaxConsecutiveDays" class="settings-input" min="1" max="14" value="5">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setAutoCancelHours">
                    <span>Auto-Expire Inactive Requests</span>
                    <span class="settings-field-hint">Hours without action</span>
                  </label>
                  <input type="number" id="setAutoCancelHours" class="settings-input" min="12" max="120" value="48">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setSlotHoldMinutes">
                    <span>Slot Checkout Hold Timeout</span>
                    <span class="settings-field-hint">Minutes held during draft</span>
                  </label>
                  <input type="number" id="setSlotHoldMinutes" class="settings-input" min="5" max="60" value="15">
                </div>
              </div>

              <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem;">
                <div class="settings-switch-row">
                  <div class="settings-switch-info">
                    <h5>Require Director Clearance for Weekend / Holiday Events</h5>
                    <p>Saturday and Sunday bookings will automatically be flagged for Executive Director sign-off.</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="setWeekendClearance" checked>
                    <span class="slider-round"></span>
                  </label>
                </div>

                <div class="settings-switch-row">
                  <div class="settings-switch-info">
                    <h5>Allow Concurrent Hall Reservations per Division</h5>
                    <p>Permit a single division to file simultaneous bookings for multiple halls on the same date.</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="setConcurrentRequests">
                    <span class="slider-round"></span>
                  </label>
                </div>
              </div>
            </section>
          </div>

          <!-- ==================================================================
               TAB 3: PRICING & RATE MATRIX (VIP 5TH & 6TH FLOOR CONFIGURED!)
               ================================================================== -->
          <div class="settings-panel" id="panel-pricing">
            <section class="settings-section-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 4v16"></path><path d="M2 8h18a2 2 0 0 1 2 2v10"></path><path d="M2 17h20"></path><path d="M6 8v9"></path></svg>
                    <span>Dormitory Lodging Rates (Per Head / Night)</span>
                  </h3>
                  <p>Institutional rates for standard trainee bunks (Floors 1–4) and VIP Executive Suites (Floors 5–6).</p>
                </div>
                <span class="settings-header-tag" style="background: #fef3c7; color: #92400e; border-color: #fde68a;">Official Rate Order</span>
              </div>

              <div class="settings-fields-grid">
                <div class="settings-field-group">
                  <label class="settings-field-label" for="setTraineeDormRate">
                    <span>Trainee Dormitory (Floors 1 – 4)</span>
                    <span class="badge-standard-pill">Sampaguita, Ilang-Ilang, Gumamela, Rosal</span>
                  </label>
                  <div class="settings-input-with-affix">
                    <span class="settings-input-affix">₱</span>
                    <input type="number" id="setTraineeDormRate" class="settings-input" value="500" step="50">
                  </div>
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setVipDormRate">
                    <span>VIP Executive Suite (Floors 5 &amp; 6)</span>
                    <span class="badge-vip-pill">Waling-Waling &bull; Dama de Noche</span>
                  </label>
                  <div class="settings-input-with-affix">
                    <span class="settings-input-affix">₱</span>
                    <input type="number" id="setVipDormRate" class="settings-input" value="800" step="50">
                  </div>
                </div>
              </div>
            </section>

            <!-- Function Halls & Facilities Rates -->
            <section class="settings-section-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                    <span>Function Halls &amp; Meeting Spaces Rate Matrix</span>
                  </h3>
                  <p>Daily and half-day rental fees for ATI training halls and conference facilities.</p>
                </div>
                <span class="settings-header-tag">Facility Schedule</span>
              </div>

              <table class="settings-rate-table">
                <thead>
                  <tr>
                    <th>Facility Space</th>
                    <th>Capacity</th>
                    <th>Full Day (8 hrs)</th>
                    <th>Half Day (4 hrs)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="rate-facility-meta">
                        <span class="rate-facility-name">Serrano Function Hall</span>
                        <span class="rate-facility-desc">Main Institutional Auditorium &bull; 2nd Floor</span>
                      </div>
                    </td>
                    <td><strong>150 PAX</strong></td>
                    <td>
                      <div class="rate-input-wrap">
                        <span class="rate-currency-prefix">₱</span>
                        <input type="number" id="setSerranoFull" class="rate-num-input" value="8000" step="100">
                      </div>
                    </td>
                    <td>
                      <div class="rate-input-wrap">
                        <span class="rate-currency-prefix">₱</span>
                        <input type="number" id="setSerranoHalf" class="rate-num-input" value="4500" step="100">
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="rate-facility-meta">
                        <span class="rate-facility-name">Executive Boardroom</span>
                        <span class="rate-facility-desc">Executive Conference Suite &bull; High-Tech Video Rig</span>
                      </div>
                    </td>
                    <td><strong>25 PAX</strong></td>
                    <td>
                      <div class="rate-input-wrap">
                        <span class="rate-currency-prefix">₱</span>
                        <input type="number" id="setBoardroomFull" class="rate-num-input" value="6000" step="100">
                      </div>
                    </td>
                    <td>
                      <div class="rate-input-wrap">
                        <span class="rate-currency-prefix">₱</span>
                        <input type="number" id="setBoardroomHalf" class="rate-num-input" value="3500" step="100">
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="rate-facility-meta">
                        <span class="rate-facility-name">4-H Learning Center</span>
                        <span class="rate-facility-desc">Interactive Training Hall &bull; Modular Seating</span>
                      </div>
                    </td>
                    <td><strong>80 PAX</strong></td>
                    <td>
                      <div class="rate-input-wrap">
                        <span class="rate-currency-prefix">₱</span>
                        <input type="number" id="setFourHFull" class="rate-num-input" value="5000" step="100">
                      </div>
                    </td>
                    <td>
                      <div class="rate-input-wrap">
                        <span class="rate-currency-prefix">₱</span>
                        <input type="number" id="setFourHHalf" class="rate-num-input" value="3000" step="100">
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="rate-facility-meta">
                        <span class="rate-facility-name">Training Hall A &amp; B</span>
                        <span class="rate-facility-desc">Workshop Room &bull; Ground Floor</span>
                      </div>
                    </td>
                    <td><strong>60 PAX</strong></td>
                    <td>
                      <div class="rate-input-wrap">
                        <span class="rate-currency-prefix">₱</span>
                        <input type="number" id="setTrainingHallFull" class="rate-num-input" value="4000" step="100">
                      </div>
                    </td>
                    <td>
                      <div class="rate-input-wrap">
                        <span class="rate-currency-prefix">₱</span>
                        <input type="number" id="setTrainingHallHalf" class="rate-num-input" value="2500" step="100">
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>

              <div style="margin-top: 1.5rem;">
                <h4 style="margin: 0 0 0.85rem 0; font-size: 0.92rem; color: #173824;">Equipment Add-on Rental Rates (Per Event Day)</h4>
                <div class="settings-fields-grid">
                  <div class="settings-field-group">
                    <label class="settings-field-label" for="setAddonLedWall">LED Video Wall Rig</label>
                    <div class="settings-input-with-affix">
                      <span class="settings-input-affix">₱</span>
                      <input type="number" id="setAddonLedWall" class="settings-input" value="3500">
                    </div>
                  </div>

                  <div class="settings-field-group">
                    <label class="settings-field-label" for="setAddonSoundRig">Stage Wireless Mics &amp; Audio Rig</label>
                    <div class="settings-input-with-affix">
                      <span class="settings-input-affix">₱</span>
                      <input type="number" id="setAddonSoundRig" class="settings-input" value="1500">
                    </div>
                  </div>

                  <div class="settings-field-group">
                    <label class="settings-field-label" for="setAddonZoomHybrid">Hybrid Zoom Video Rig</label>
                    <div class="settings-input-with-affix">
                      <span class="settings-input-affix">₱</span>
                      <input type="number" id="setAddonZoomHybrid" class="settings-input" value="2000">
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>

          <!-- ==================================================================
               TAB 4: ROUTING & APPROVAL WORKFLOW
               ================================================================== -->
          <div class="settings-panel" id="panel-workflow">
            <section class="settings-section-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    <span>Multi-Stage Governance &amp; Action Routing</span>
                  </h3>
                  <p>Configure administrative recommendation sign-offs and Directorate IV clearance procedures.</p>
                </div>
                <span class="settings-header-tag">Approval Engine</span>
              </div>

              <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                <div class="settings-switch-row">
                  <div class="settings-switch-info">
                    <h5>Enforce Two-Stage Routing Approval Flow</h5>
                    <p>Stage 1: Recommending Officer (Logistics Verification) &rarr; Stage 2: Directorate IV Clearance (Final Order).</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="setTwoStageApproval" checked>
                    <span class="slider-round"></span>
                  </label>
                </div>

                <div class="settings-switch-row">
                  <div class="settings-switch-info">
                    <h5>Auto-Route Function Hall Inquiries to Administrative Section</h5>
                    <p>Directs venue requests to Administrative &amp; Finance Unit (AFU) coordinators automatically.</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="setAutoRouteHalls" checked>
                    <span class="slider-round"></span>
                  </label>
                </div>

                <div class="settings-switch-row">
                  <div class="settings-switch-info">
                    <h5>Auto-Route Dormitory Inquiries to Lodging Supervisor</h5>
                    <p>Directs trainee bunks and VIP room allocations directly to the Building Custodian queue.</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="setAutoRouteDorms" checked>
                    <span class="slider-round"></span>
                  </label>
                </div>

                <div class="settings-switch-row">
                  <div class="settings-switch-info">
                    <h5>Strict Capacity Threshold Enforcement</h5>
                    <p>Prohibit reservation filing if the specified participant count exceeds the hall's maximum legal capacity.</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="setStrictCapacity" checked>
                    <span class="slider-round"></span>
                  </label>
                </div>

                <div class="settings-switch-row">
                  <div class="settings-switch-info">
                    <h5>Automatic Official Gate Pass &amp; QR Code Issuance</h5>
                    <p>Generate a printable institutional security gate pass PDF upon final Director IV approval.</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="setAutoGatePass" checked>
                    <span class="slider-round"></span>
                  </label>
                </div>
              </div>
            </section>
          </div>

          <!-- ==================================================================
               TAB 5: AUTOMATED NOTIFICATIONS & DISPATCH
               ================================================================== -->
          <div class="settings-panel" id="panel-notifications">
            <section class="settings-section-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span>Government Email &amp; SMTP Gateway</span>
                  </h3>
                  <p>Outgoing notification server configuration for approval alerts and gate passes.</p>
                </div>
                <button type="button" class="btn-mini-test" onclick="testEmailDispatch()">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 2 11 13"></polyline><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                  <span>Send Test Email</span>
                </button>
              </div>

              <div class="settings-switch-row" style="margin-bottom: 1.25rem;">
                <div class="settings-switch-info">
                  <h5>Enable Automated Email Dispatch</h5>
                  <p>Send email status updates to applicant and division heads on each approval milestone.</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="setEmailActive" checked>
                  <span class="slider-round"></span>
                </label>
              </div>

              <div class="settings-fields-grid">
                <div class="settings-field-group">
                  <label class="settings-field-label" for="setSenderEmail">System Sender Address</label>
                  <input type="email" id="setSenderEmail" class="settings-input" value="noreply@ati.da.gov.ph">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setSmtpHost">SMTP Relay Host</label>
                  <input type="text" id="setSmtpHost" class="settings-input" value="smtp.ati.da.gov.ph">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setSmtpPort">SMTP Port</label>
                  <input type="number" id="setSmtpPort" class="settings-input" value="587">
                </div>
              </div>
            </section>

            <!-- SMS Gateway Card -->
            <section class="settings-section-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    <span>SMS Alert Broadcast Gateway</span>
                  </h3>
                  <p>Send instant text message confirmations to mobile phones of liaison staff.</p>
                </div>
                <button type="button" class="btn-mini-test" onclick="testSmsGateway()">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                  <span>Test SMS Handshake</span>
                </button>
              </div>

              <div class="settings-switch-row" style="margin-bottom: 1.25rem;">
                <div class="settings-switch-info">
                  <h5>Enable Mobile SMS Alerts</h5>
                  <p>Dispatch SMS updates when a booking is confirmed, rescheduled, or declined.</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="setSmsActive" checked>
                  <span class="slider-round"></span>
                </label>
              </div>

              <div class="settings-fields-grid">
                <div class="settings-field-group">
                  <label class="settings-field-label" for="setSmsSenderId">Masked Sender ID</label>
                  <input type="text" id="setSmsSenderId" class="settings-input" value="ATI-NOTIF" maxlength="11">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setDigestTime">Daily Approver Digest Time</label>
                  <input type="time" id="setDigestTime" class="settings-input" value="07:30">
                </div>
              </div>
            </section>
          </div>

          <!-- ==================================================================
               TAB 6: SECURITY, BACKUP & GOVERNANCE
               ================================================================== -->
          <div class="settings-panel" id="panel-security">
            <section class="settings-section-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <span>Security Policies &amp; Access Controls</span>
                  </h3>
                  <p>Civil service security standards, session idle timeouts, and 2FA authentication.</p>
                </div>
                <span class="settings-header-tag">Access Protocol</span>
              </div>

              <div class="settings-fields-grid">
                <div class="settings-field-group">
                  <label class="settings-field-label" for="setPasswordExpiryDays">Password Expiration (Days)</label>
                  <input type="number" id="setPasswordExpiryDays" class="settings-input" min="30" max="180" value="90">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setSessionTimeoutMinutes">Session Inactivity Timeout (Minutes)</label>
                  <input type="number" id="setSessionTimeoutMinutes" class="settings-input" min="5" max="120" value="30">
                </div>

                <div class="settings-field-group">
                  <label class="settings-field-label" for="setAuditRetentionDays">Audit Trail Retention (Days)</label>
                  <input type="number" id="setAuditRetentionDays" class="settings-input" min="60" max="730" value="180">
                </div>
              </div>

              <div class="settings-switch-row" style="margin-top: 1.25rem;">
                <div class="settings-switch-info">
                  <h5>Enforce Two-Factor Authentication (2FA) for All Administrative Roles</h5>
                  <p>Mandatory email OTP authentication before accessing executive review desks.</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="setRequire2FA" checked>
                  <span class="slider-round"></span>
                </label>
              </div>
            </section>

            <!-- Data Backup & Disaster Recovery -->
            <section class="settings-section-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Database Backup &amp; Disaster Recovery Snapshots</span>
                  </h3>
                  <p>Generate, export, or restore system state, reservations archive, and configurations.</p>
                </div>
                <span class="settings-header-tag" style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;">Data Integrity</span>
              </div>

              <p style="font-size: 0.85rem; color: #556c5e; line-height: 1.5; margin: 0 0 1.25rem 0;">
                All system operational settings and active reservation snapshots can be exported as standard JSON backups for offline archiving or disaster recovery.
              </p>

              <div style="display: flex; gap: 0.85rem; flex-wrap: wrap;">
                <button type="button" class="btn-settings-backup" onclick="downloadDatabaseBackup()">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                  <span>Download Full Database Backup (.json)</span>
                </button>

                <button type="button" class="btn-settings-backup" onclick="exportSystemConfig()" style="background: #f8faf8; color: #374151; border-color: #d1d5db;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                  <span>Export System Config Only (.json)</span>
                </button>

                <button type="button" class="btn-settings-backup" onclick="triggerImportConfig()" style="background: #eff6ff; color: #1e40af; border-color: #bfdbfe;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                  <span>Import / Restore Config</span>
                </button>

                <input type="file" id="importConfigFile" accept=".json" style="display: none;" onchange="handleConfigFileChosen(event)">
              </div>
            </section>
          </div>

          <!-- ==================================================================
               STICKY SAVE & ACTION FOOTER
               ================================================================== -->
          <footer class="settings-sticky-footer">
            <div class="settings-footer-left">
              <button type="button" class="btn-settings-reset" onclick="confirmFactoryReset()">
                Restore Factory Defaults
              </button>
            </div>

            <div class="settings-footer-right">
              <button type="button" class="btn-settings-save" id="btnSaveAllSettings" onclick="saveAllSystemSettings()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                  <polyline points="17 21 17 13 7 13 7 21"></polyline>
                  <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span>Save All Changes</span>
              </button>
            </div>
          </footer>

        </div>

      </main>

    </div>

  </div>

  <!-- Reset Confirmation Modal -->
  <div class="settings-modal-backdrop" id="resetConfirmModal" style="display: none;" onclick="if(event.target===this) closeResetModal();">
    <div class="settings-modal-box">
      <h4>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        <span>Reset to Official Defaults?</span>
      </h4>
      <p>This will restore all pricing matrices, advance reservation policies, and notification rules to the official institutional ATI defaults. Custom rates and policies will be overwritten.</p>
      <div class="settings-modal-actions">
        <button type="button" class="btn-settings-reset" onclick="closeResetModal()">Cancel</button>
        <button type="button" class="btn-settings-save" style="background: #b91c1c;" onclick="executeFactoryReset()">Confirm Reset</button>
      </div>
    </div>
  </div>

  <!-- Toast Container -->
  <div class="admin-toast-container" id="settingsToastContainer"></div>

  <!-- Scripts -->
  <script src="js/admin_settings.js?v=<?php echo time(); ?>"></script>
</body>

</html>
