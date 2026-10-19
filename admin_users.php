<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/admin_guard.php';

/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator: Role-Based Access Control (RBAC) & User Management Portal
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');

$db = (new Database())->getConnection();

// Dynamic sidebar count
$pendingStmt = $db->query("SELECT COUNT(*) FROM reservations WHERE status IN ('Pending', 'Pending Endorsement', 'Pending Recommendation', 'Pending Clearance', 'Awaiting Endorsement', 'Awaiting Recommendation', 'Awaiting Clearance')");
$sidebarPendingCount = (int)($pendingStmt ? $pendingStmt->fetchColumn() : 0);

// Fetch dynamic users from MySQL database
$stmt = $db->query("SELECT id, full_name, email, contact_number, office_agency, category, role, status, created_at, deleted_at FROM users ORDER BY id ASC");
$usersList = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Dynamic KPI metrics for 3-Stage Governance
$totalActiveUsers = 0;
$activeUsers = 0;
$endorsementCount = 0;
$recommendationCount = 0;
$clearanceCount = 0;
$superAdminCount = 0;
$requestorCount = 0;
$archivedCount = 0;

foreach ($usersList as $u) {
    if (!empty($u['deleted_at'])) {
        $archivedCount++;
        continue;
    }
    $totalActiveUsers++;
    if ($u['status'] === 'active') $activeUsers++;
    
    $r = strtolower($u['role']);
    if (in_array($r, ['endorsement', 'endorser', 'division_chief'])) {
        $endorsementCount++;
    } elseif (in_array($r, ['recommendation', 'recommender', 'admin'])) {
        $recommendationCount++;
    } elseif (in_array($r, ['clearance', 'director'])) {
        $clearanceCount++;
    } elseif ($r === 'super_admin') {
        $superAdminCount++;
    } else {
        $requestorCount++;
    }
}

function getUserInitials($name) {
    $parts = explode(' ', trim($name));
    if (count($parts) > 1) {
        return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
    }
    return strtoupper(substr($name, 0, 2));
}

function getRolePresentation($role) {
    $r = strtolower($role);
    if (in_array($r, ['endorsement', 'endorser', 'division_chief'])) {
        return [
            'badgeClass' => 'status-role-endorse',
            'badgeText' => 'Endorsement Officer',
            'iconSvg' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>',
            'privilege' => 'Division Endorsement, Facilities & Venue Setups',
            'filterKey' => 'endorsement',
            'avatarClass' => 'endorsement'
        ];
    } elseif (in_array($r, ['recommendation', 'recommender', 'admin'])) {
        return [
            'badgeClass' => 'status-role-recommend',
            'badgeText' => 'Recommending Officer',
            'iconSvg' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>',
            'privilege' => 'Schedule Verification & Logistics Recommendation',
            'filterKey' => 'recommendation',
            'avatarClass' => 'recommending'
        ];
    } elseif (in_array($r, ['clearance', 'director'])) {
        return [
            'badgeClass' => 'status-role-clearance',
            'badgeText' => 'Clearance Authority',
            'iconSvg' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>',
            'privilege' => 'Final Approval & Gate Pass Authorization',
            'filterKey' => 'clearance',
            'avatarClass' => 'director'
        ];
    } elseif ($r === 'super_admin') {
        return [
            'badgeClass' => 'status-role-super',
            'badgeText' => 'Super Administrator',
            'iconSvg' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
            'privilege' => 'Master Executive Access across All Modules',
            'filterKey' => 'super_admin',
            'avatarClass' => 'super_admin'
        ];
    } else {
        return [
            'badgeClass' => 'status-role-staff',
            'badgeText' => 'Authorized Requestor',
            'iconSvg' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
            'privilege' => 'Submit Reservations & Track Clearance Status',
            'filterKey' => 'user',
            'avatarClass' => 'staff'
        ];
    }
}
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
    <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>

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
            <span id="currentDateDisplay"><?= date('l, M d, Y') ?></span>
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
              <span>Governance & Role Access Matrix</span>
            </div>
            <h2>User Management & Institutional Role Matrix</h2>
            <p>Manage the reservation approval workflow (Endorsement &rarr; Recommendation &rarr; Clearance), assign division roles, and oversee authorized staff accounts.</p>
          </div>

          <div class="admin-header-actions">
            <button type="button" class="btn-system-secondary" onclick="openAddUserModal()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
              </svg>
              <span>Add Personnel</span>
            </button>
          </div>
        </section>

        <!-- ==========================================================================
             KPI STAT CARDS (3-STAGE GOVERNANCE METRICS)
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
                <span class="stat-value" id="kpiTotalUsers"><?= $totalUsers ?></span>
                <span class="stat-trend positive" id="kpiActiveUsers"><?= $activeUsers ?> Active</span>
              </div>
              <span class="stat-label">Total System Accounts</span>
            </div>
          </div>

          <!-- Stage 1: Endorsement Authorities -->
          <div class="my-res-stat-card" onclick="filterUsersByRole('endorsement')">
            <div class="stat-icon-box blue" style="color: #0284c7; background: #e0f2fe;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiEndorsementCount"><?= $endorsementCount ?></span>
                <span class="stat-pill-urgent" style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd; animation: none;">Endorsement</span>
              </div>
              <span class="stat-label">Endorsement Officers</span>
            </div>
          </div>

          <!-- Stage 2: Recommending Officers -->
          <div class="my-res-stat-card" onclick="filterUsersByRole('recommendation')">
            <div class="stat-icon-box emerald">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiRecommendingCount"><?= $recommendationCount ?></span>
                <span class="stat-pill-urgent" style="background: #dcfce7; color: #15803d; border-color: #bbf7d0; animation: none;">Recommendation</span>
              </div>
              <span class="stat-label">Recommending Officers</span>
            </div>
          </div>

          <!-- Stage 3: Final Clearance Authorities -->
          <div class="my-res-stat-card" onclick="filterUsersByRole('clearance')">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiClearanceCount"><?= $clearanceCount ?></span>
                <span class="stat-pill-urgent" style="background: #fef3c7; color: #92400e; border-color: #fde68a; animation: none;">Clearance</span>
              </div>
              <span class="stat-label">Clearance Authorities</span>
            </div>
          </div>

          <!-- Staff Requestors & Admins -->
          <div class="my-res-stat-card" onclick="filterUsersByRole('user')">
            <div class="stat-icon-box purple" style="color: #6d28d9; background: #f3e8ff;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="7" r="4"></circle>
                <path d="M5.5 21a8.38 8.38 0 0 1 13 0"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiStaffCount"><?= $requestorCount + $superAdminCount ?></span>
                <span class="stat-trend neutral">Staff & Admins</span>
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
              <span class="tab-count-pill" id="pillCountAll"><?= $totalActiveUsers ?></span>
            </button>
            <button type="button" class="user-filter-pill" data-role="endorsement" onclick="filterUsersByRole('endorsement')">
              <span>Endorsement</span>
              <span class="tab-count-pill" id="pillCountEndorse"><?= $endorsementCount ?></span>
            </button>
            <button type="button" class="user-filter-pill" data-role="recommendation" onclick="filterUsersByRole('recommendation')">
              <span>Recommendation</span>
              <span class="tab-count-pill" id="pillCountRec"><?= $recommendationCount ?></span>
            </button>
            <button type="button" class="user-filter-pill" data-role="clearance" onclick="filterUsersByRole('clearance')">
              <span>Clearance</span>
              <span class="tab-count-pill" id="pillCountClear"><?= $clearanceCount ?></span>
            </button>
            <button type="button" class="user-filter-pill" data-role="super_admin" onclick="filterUsersByRole('super_admin')">
              <span>Super Admin</span>
              <span class="tab-count-pill" id="pillCountSuper"><?= $superAdminCount ?></span>
            </button>
            <button type="button" class="user-filter-pill" data-role="user" onclick="filterUsersByRole('user')">
              <span>Requestors / Staff</span>
              <span class="tab-count-pill" id="pillCountStaff"><?= $requestorCount ?></span>
            </button>
            <button type="button" class="user-filter-pill tab-archived" data-role="archived" onclick="filterUsersByRole('archived')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              <span>Archived / Trash</span>
              <span class="tab-count-pill" id="pillCountArchived" style="background: #fee2e2; color: #991b1b;"><?= $archivedCount ?></span>
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
              <button type="button" class="search-clear-btn" id="userSearchClear" onclick="clearUserSearch()" title="Clear search" style="display: none;" aria-label="Clear search">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <div class="select-filter-wrap">
              <select id="userStatusFilterSelect" class="venue-filter-select" onchange="handleStatusFilter(this.value)">
                <option value="all">All Statuses</option>
                <option value="active">Active Only</option>
                <option value="suspended">Suspended Only</option>
                <option value="pending">Pending Verification</option>
              </select>
            </div>
          </div>
        </div>

        <!-- ==========================================================================
             USERS & ROLES TABLE CARD (DATABASE-DRIVEN)
             ========================================================================== -->
        <div class="admin-table-card">
          <div class="admin-card-header">
            <div class="admin-card-title-group">
              <h3>Authorized Personnel Directory</h3>
              <p>Database-synchronized directory with governance clearances (Endorsement &rarr; Recommendation &rarr; Clearance).</p>
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
                  <th style="width: 22%;">User & Official Email</th>
                  <th style="width: 18%;">Office / Division</th>
                  <th style="width: 20%;">Assigned Clearance Role</th>
                  <th style="width: 16%;">Privilege Scope</th>
                  <th style="width: 8%;">Status</th>
                  <th style="width: 16%; min-width: 200px; text-align: right;">Action</th>
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

                <!-- Dynamic DB Rows -->
                <?php foreach ($usersList as $u): 
                    $pres = getRolePresentation($u['role']);
                    $initials = getUserInitials($u['full_name']);
                    $isActive = ($u['status'] === 'active');
                    $isArchived = !empty($u['deleted_at']);
                ?>
                <tr data-user-id="<?= $u['id'] ?>" 
                    data-user-role="<?= htmlspecialchars($pres['filterKey']) ?>" 
                    data-user-status="<?= htmlspecialchars($u['status']) ?>"
                    data-user-archived="<?= $isArchived ? '1' : '0' ?>"
                    class="<?= $isArchived ? 'row-user-archived' : '' ?>"
                    style="<?= $isArchived ? 'display: none;' : '' ?>">
                  <td>
                    <div class="user-avatar-cell">
                      <div class="user-avatar-badge <?= $isArchived ? 'archived' : $pres['avatarClass'] ?>">
                        <?= htmlspecialchars($initials) ?>
                        <span class="user-online-dot <?= ($isActive && !$isArchived) ? '' : 'offline' ?>"></span>
                      </div>
                      <div>
                        <div class="user-name-title" style="<?= $isArchived ? 'text-decoration: line-through; color: #6b7280;' : '' ?>">
                          <?= htmlspecialchars($u['full_name']) ?>
                        </div>
                        <span class="user-email-text"><?= htmlspecialchars($u['email']) ?></span>
                        <?php if ($isArchived): ?>
                          <span style="display: block; font-size: 0.72rem; color: #dc2626; font-weight: 600; margin-top: 2px;">
                            Archived on <?= date('M d, Y h:i A', strtotime($u['deleted_at'])) ?>
                          </span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-dept-text"><?= htmlspecialchars($u['office_agency'] ?: 'ATI Central Office') ?></span>
                    <span class="user-email-text" style="font-size: 0.73rem; color: #6e8c79;"><?= htmlspecialchars($u['category'] ?: 'ATI Personnel') ?></span>
                  </td>
                  <td>
                    <span class="user-role-badge <?= $pres['badgeClass'] ?>">
                      <?= $pres['iconSvg'] ?>
                      <?= $pres['badgeText'] ?>
                    </span>
                  </td>
                  <td>
                    <span class="privilege-summary-tag"><?= $pres['privilege'] ?></span>
                  </td>
                  <td>
                    <?php if ($isArchived): ?>
                      <span class="status-badge-chip archived" style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
                        <span class="chip-status-dot" style="background: #dc2626;"></span>
                        Archived
                      </span>
                    <?php else: ?>
                      <span class="status-badge-chip <?= $isActive ? 'active' : 'suspended' ?>">
                        <span class="chip-status-dot"></span>
                        <?= ucfirst($u['status']) ?>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td style="text-align: right;">
                    <div class="user-action-btns">
                      <?php if ($isArchived): ?>
                        <button type="button" class="btn-user-action restore" onclick="confirmRestoreUser(<?= $u['id'] ?>, '<?= addslashes(htmlspecialchars($u['full_name'])) ?>')" title="Restore user to Active list">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                          <span>Restore</span>
                        </button>
                        <button type="button" class="btn-user-action purge" onclick="confirmPermanentDeleteUser(<?= $u['id'] ?>, '<?= addslashes(htmlspecialchars($u['full_name'])) ?>')" title="Permanently delete from database">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                          <span>Purge</span>
                        </button>
                      <?php else: ?>
                        <button type="button" class="btn-user-action edit" onclick="openEditRoleModal(<?= $u['id'] ?>, '<?= addslashes(htmlspecialchars($u['full_name'])) ?>', '<?= addslashes(htmlspecialchars($u['email'])) ?>', '<?= addslashes(htmlspecialchars($pres['filterKey'])) ?>', '<?= addslashes(htmlspecialchars($u['office_agency'] ?: '')) ?>')" title="Configure clearance role">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                          <span>Role</span>
                        </button>
                        <button type="button" class="btn-user-action status-toggle <?= $isActive ? '' : 'activate' ?>" onclick="toggleUserStatus(<?= $u['id'] ?>)" title="<?= $isActive ? 'Suspend access' : 'Reactivate access' ?>">
                          <?= $isActive 
                            ? '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg><span>Suspend</span>'
                            : '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Activate</span>'
                          ?>
                        </button>
                        <?php if ($u['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                          <button type="button" class="btn-user-action purge" onclick="confirmDeleteUser(<?= $u['id'] ?>, '<?= addslashes(htmlspecialchars($u['full_name'])) ?>')" title="Delete user account (Move to Trash)">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            <span>Delete</span>
                          </button>
                        <?php endif; ?>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: CONFIGURE ROLE & PRIVILEGES (3-STAGE GOVERNANCE)
       ========================================================================== -->
  <div class="admin-modal-overlay" id="editRoleModal" style="display: none;">
    <div class="admin-modal-card sm" style="max-width: 540px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <span class="modal-badge-ref">Governance Role</span>
          <h4>Configure Access Level & Clearances</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeEditRoleModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <div class="modal-applicant-summary" style="margin-bottom: 1.25rem;">
          <div class="user-avatar-circle" id="editRoleUserAvatar">JD</div>
          <div class="applicant-meta">
            <h5 id="editRoleUserName">Personnel Name</h5>
            <p id="editRoleUserDept">Division / Office</p>
            <span class="applicant-contact" id="editRoleUserEmail">email@ati.da.gov.ph</span>
          </div>
        </div>

        <form id="editRoleForm" onsubmit="saveRoleChanges(event)">
          <input type="hidden" id="editRoleUserId" value="">
          
          <div class="form-group-decline" style="margin-bottom: 1rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.4rem;">Office / Division / Bureau</label>
            <input type="text" id="editRoleUserDeptInput" placeholder="e.g. Career Development Division (CDD)" style="width: 100%; height: 42px; padding: 0 1rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; box-sizing: border-box;" required>
          </div>

          <div class="form-group-decline" style="margin-bottom: 1rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.4rem;">Designated Approval Stage / Role</label>
            <select id="roleSelectDropdown" style="width: 100%; height: 42px; padding: 0 1rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; font-weight: 600; box-sizing: border-box;">
              <option value="endorsement">Endorsement Officer (Division Chief / Section Head)</option>
              <option value="recommendation">Recommending Officer (Admin & Logistics)</option>
              <option value="clearance">Clearance Authority (Executive Director)</option>
              <option value="super_admin">Super Administrator (Full System Control)</option>
              <option value="user">Authorized Requestor / Personnel (Standard Staff)</option>
            </select>
          </div>

          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.5rem;">Clearance Privileges & Execution Rights</label>
          <div class="permission-checkbox-grid">
            <label class="perm-checkbox-item">
              <input type="checkbox" id="permInspect" checked>
              <div class="perm-checkbox-text">
                <strong>Inspect & Verify Request Attachments</strong>
                <span>Can view uploaded activity permits and applicant credentials</span>
              </div>
            </label>

            <label class="perm-checkbox-item">
              <input type="checkbox" id="permEndorse">
              <div class="perm-checkbox-text">
                <strong>Issue Division Endorsement</strong>
                <span>Authorized to endorse division reservation requests</span>
              </div>
            </label>

            <label class="perm-checkbox-item">
              <input type="checkbox" id="permRecommend">
              <div class="perm-checkbox-text">
                <strong>Issue Logistics Recommendation</strong>
                <span>Authorized to review logistics and recommend reservations</span>
              </div>
            </label>

            <label class="perm-checkbox-item">
              <input type="checkbox" id="permClearance">
              <div class="perm-checkbox-text">
                <strong>Clearance & Gate Pass Approval</strong>
                <span>Final authority to confirm bookings and issue gate passes</span>
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
       MODAL: REGISTER NEW USER (DATABASE-SAVED)
       ========================================================================== -->
  <div class="admin-modal-overlay" id="addUserModal" style="display: none;">
    <div class="admin-modal-card sm" style="max-width: 540px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <span class="modal-badge-ref">New Registration</span>
          <h4>Register Institutional Personnel</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeAddUserModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <form id="addUserForm" onsubmit="submitNewUser(event)">
          <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            <div>
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Full Name with Title</label>
              <input type="text" id="newUserName" placeholder="e.g. Engr. Mark Joseph Rivera" required
                style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; box-sizing: border-box;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
              <div>
                <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Official Government Email</label>
                <input type="email" id="newUserEmail" placeholder="e.g. mark.rivera@ati.da.gov.ph" required
                  style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; box-sizing: border-box;">
              </div>
              <div>
                <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Contact Number</label>
                <input type="text" id="newUserPhone" placeholder="e.g. 09171234567"
                  style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; box-sizing: border-box;">
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 0.85rem;">
              <div>
                <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Assigned Division / Bureau</label>
                <input type="text" id="newUserDept" placeholder="e.g. Policy & Planning Division (PPD)" required
                  style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; box-sizing: border-box;">
              </div>
              <div>
                <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Category</label>
                <select id="newUserCategory"
                  style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; font-weight: 600; box-sizing: border-box;">
                  <option value="ATI Personnel">ATI Personnel</option>
                  <option value="Gov Agency">Gov Agency</option>
                  <option value="Private / Guest">Private / Guest</option>
                </select>
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
              <div>
                <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Initial Password</label>
                <input type="password" id="newUserPassword" placeholder="Default: AtiPass2026!"
                  style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; box-sizing: border-box;">
              </div>
              <div>
                <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Designated Governance Role</label>
                <select id="newUserRole"
                  style="width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem; font-weight: 600; box-sizing: border-box;">
                  <option value="user">Authorized Requestor (Staff / Partner)</option>
                  <option value="endorsement">Endorsement Officer</option>
                  <option value="recommendation">Recommending Officer</option>
                  <option value="clearance">Clearance Authority</option>
                  <option value="super_admin">Super Administrator</option>
                </select>
              </div>
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
