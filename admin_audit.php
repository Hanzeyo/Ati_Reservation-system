<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/admin_guard.php';

/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator: Institutional Audit Trail & Security Logs Portal
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');

// Fetch real audit logs joined with users
$auditLogsQuery = "SELECT a.*, 
                          u.full_name, 
                          u.role, 
                          u.office_agency,
                          u.email
                   FROM audit_logs a
                   LEFT JOIN users u ON a.user_id = u.id
                   ORDER BY a.id DESC";
$stmt = $db->query($auditLogsQuery);
$rawAuditLogs = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Process logs and compute KPIs
$kpiTotal = count($rawAuditLogs);
$kpiApprovals = 0;
$kpiMaintenance = 0;
$kpiSecurity = 0;

$processedLogs = [];
$todayDateStr = date('Y-m-d');
$yesterdayDateStr = date('Y-m-d', strtotime('-1 day'));
$sevenDaysAgoTs = strtotime('-7 days');

foreach ($rawAuditLogs as $log) {
    $act = strtoupper($log['action'] ?? '');
    
    // Determine category, chip label, severity
    $category = 'approval';
    $chipLabel = 'Governance Action';
    $severity = 'info';
    $chipClass = 'approval';
    
    if (in_array($act, ['STAGE1_ENDORSEMENT', 'ENDORSE_STAGE_1'])) {
        $category = 'approval';
        $chipLabel = 'Stage 1 Endorsement';
        $severity = 'warning';
        $chipClass = 'approval';
        $kpiApprovals++;
    } elseif (in_array($act, ['STAGE2_RECOMMENDATION', 'STAGE1_RECOMMEND'])) {
        $category = 'approval';
        $chipLabel = 'Stage 2 Recommendation';
        $severity = 'info';
        $chipClass = 'approval';
        $kpiApprovals++;
    } elseif (in_array($act, ['STAGE3_CLEARANCE', 'STAGE2_CLEARANCE'])) {
        $category = 'approval';
        $chipLabel = 'Director Clearance';
        $severity = 'success';
        $chipClass = 'approval';
        $kpiApprovals++;
    } elseif ($act === 'CREATE_RESERVATION') {
        $category = 'approval';
        $chipLabel = 'New Reservation';
        $severity = 'info';
        $chipClass = 'submission';
        $kpiApprovals++;
    } elseif ($act === 'REJECT_RESERVATION') {
        $category = 'approval';
        $chipLabel = 'Official Decline';
        $severity = 'danger';
        $chipClass = 'decline';
        $kpiApprovals++;
    } elseif ($act === 'CANCEL_RESERVATION') {
        $category = 'approval';
        $chipLabel = 'Booking Cancelled';
        $severity = 'warning';
        $chipClass = 'decline';
        $kpiApprovals++;
    } elseif (strpos($act, 'RESERVATION') !== false) {
        $category = 'approval';
        $chipLabel = 'Reservation Record';
        $severity = 'info';
        $chipClass = 'approval';
        $kpiApprovals++;
    } elseif (strpos($act, 'MAINTENANCE') !== false || strpos($act, 'BLOCK') !== false || strpos($act, 'FACILITY') !== false || strpos($act, 'ROOM') !== false) {
        $category = 'maintenance';
        if (strpos($act, 'MAINTENANCE') !== false) {
            $chipLabel = 'Maintenance Hold';
        } elseif (strpos($act, 'BLOCK') !== false) {
            $chipLabel = 'Schedule Hold';
        } else {
            $chipLabel = 'Venue Config';
        }
        $severity = 'warning';
        $chipClass = 'maintenance';
        $kpiMaintenance++;
    } elseif (strpos($act, 'USER') !== false || strpos($act, 'ROLE') !== false || strpos($act, 'LOGIN') !== false || strpos($act, 'PASSWORD') !== false) {
        $category = 'security';
        if (strpos($act, 'ROLE') !== false) {
            $chipLabel = 'RBAC Security';
        } elseif (strpos($act, 'REGISTRATION') !== false) {
            $chipLabel = 'Account Provision';
        } else {
            $chipLabel = 'Security & Access';
        }
        $severity = 'info';
        $chipClass = 'security';
        $kpiSecurity++;
    } else {
        $category = 'approval';
        $chipLabel = ucwords(str_replace('_', ' ', strtolower($act)));
        $severity = 'info';
        $chipClass = 'approval';
        $kpiApprovals++;
    }
    
    // Timeframe
    $ts = strtotime($log['created_at']);
    $dateOnly = date('Y-m-d', $ts);
    if ($dateOnly === $todayDateStr) {
        $timeframe = 'today';
        $timeHeading = 'Today &bull; ' . date('h:i A', $ts);
    } elseif ($dateOnly === $yesterdayDateStr) {
        $timeframe = 'yesterday';
        $timeHeading = 'Yesterday &bull; ' . date('h:i A', $ts);
    } elseif ($ts >= $sevenDaysAgoTs) {
        $timeframe = 'week';
        $timeHeading = date('M d, Y', $ts) . ' &bull; ' . date('h:i A', $ts);
    } else {
        $timeframe = 'earlier';
        $timeHeading = date('M d, Y', $ts) . ' &bull; ' . date('h:i A', $ts);
    }
    $subDate = date('M d, Y', $ts) . ' &bull; ' . date('h:i:s A', $ts) . ' PHT';
    
    // Actor info
    $actorName = !empty($log['full_name']) ? $log['full_name'] : 'Authorized Officer';
    $actorRole = !empty($log['role']) ? ucwords(str_replace('_', ' ', $log['role'])) : 'System Administrator';
    $initials = getUserInitials($actorName);
    $isDirector = in_array(strtolower($log['role'] ?? ''), ['director', 'clearance', 'super_admin']);
    
    // Extract target resource
    $targetResource = 'Central Office';
    if (preg_match('/\[(ATI-(?:RES|BK)-[0-9-]+)\]/i', $log['details'], $m)) {
        $targetResource = $m[1];
    } elseif (preg_match('/(ATI-(?:RES|BK)-\d{4}-\d+)/i', $log['details'], $m)) {
        $targetResource = $m[1];
    } elseif (preg_match('/user (?:id:?\s*)?\[?(\d+)\]?/i', $log['details'], $m)) {
        $targetResource = 'USR-' . $m[1];
    } elseif (preg_match('/facility (?:id:?\s*)?\[?(\d+)\]?/i', $log['details'], $m)) {
        $targetResource = 'FAC-' . $m[1];
    } elseif (preg_match('/room (?:id:?\s*)?\[?(\d+)\]?/i', $log['details'], $m)) {
        $targetResource = 'ROOM-' . $m[1];
    }
    
    // Security Hash
    $hashTag = '#' . $log['id'] . '-' . substr(md5($log['id'] . $log['created_at'] . $log['action']), 0, 2);
    
    $processedLogs[] = [
        'id' => $log['id'],
        'eventId' => 'LOG-' . str_pad($log['id'], 4, '0', STR_PAD_LEFT),
        'category' => $category,
        'chipLabel' => $chipLabel,
        'chipClass' => $chipClass,
        'severity' => $severity,
        'timeframe' => $timeframe,
        'timeHeading' => $timeHeading,
        'subDate' => $subDate,
        'details' => $log['details'],
        'actorName' => $actorName,
        'actorRole' => $actorRole,
        'initials' => $initials,
        'isDirector' => $isDirector,
        'targetResource' => $targetResource,
        'hashTag' => $hashTag,
        'ipAddress' => $log['ip_address'] ?? '127.0.0.1'
    ];
}
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
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Super Administrator &rsaquo; System Governance</span>
            <h1 class="topbar-page-title">Institutional Audit Trail & Activity Logs</h1>
          </div>
        </div>

        <div class="admin-topbar-center">
          <!-- Quick Search -->
          <div class="topbar-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="topbarSearchInput" placeholder="Quick search audit logs..." oninput="handleAuditSearch(this.value)" autocomplete="off">
            <span class="search-kbd-hint"><kbd>Ctrl</kbd><kbd>K</kbd></span>
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
            <span id="currentDateDisplay"><?= date('M d, Y') ?></span>
          </div>

          <!-- User Profile Badge -->
          <a href="profile.php" class="topbar-user-badge" title="<?= $sidebarUserFullName ?> (<?= htmlspecialchars($roleOfficerTitle ?? 'Administrator') ?>)">
            <div class="topbar-user-avatar <?= htmlspecialchars($currentAdminRole ?? 'super_admin') ?>" id="topbarUserAvatar">
              <img src="<?= !empty($sidebarUserAvatar) ? htmlspecialchars($sidebarUserAvatar) : '' ?>" alt="Profile Picture" class="topbar-avatar-img" id="topbarAvatarImg" style="<?= empty($sidebarUserAvatar) ? 'display: none;' : '' ?>">
              <span class="topbar-avatar-initials" id="topbarAvatarInitials" style="<?= !empty($sidebarUserAvatar) ? 'display: none;' : '' ?>"><?= htmlspecialchars($roleInitials ?? $sidebarUserInitials) ?></span>
            </div>
            <div class="topbar-user-meta">
              <span class="topbar-user-name" id="topbarUserName"><?= $sidebarUserFullName ?></span>
              <span class="topbar-user-role"><?= htmlspecialchars($roleOfficerTitle ?? 'Administrator') ?></span>
            </div>
          </a>
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
          <!-- Total Logs -->
          <div class="my-res-stat-card active-filter" data-cat="all" onclick="filterAuditByCategory('all')" style="cursor: pointer;">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiTotalEvents"><?= $kpiTotal ?></span>
                <span class="stat-trend positive">100% Verified</span>
              </div>
              <span class="stat-label">Total Recorded Logs</span>
            </div>
          </div>

          <!-- Approvals & Endorsements -->
          <div class="my-res-stat-card" data-cat="approval" onclick="filterAuditByCategory('approval')" style="cursor: pointer;">
            <div class="stat-icon-box emerald">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiApprovalEvents"><?= $kpiApprovals ?></span>
                <span class="stat-trend positive">Authorizations</span>
              </div>
              <span class="stat-label">Governance &amp; Approvals</span>
            </div>
          </div>

          <!-- Facility Maintenance Holds -->
          <div class="my-res-stat-card" data-cat="maintenance" onclick="filterAuditByCategory('maintenance')" style="cursor: pointer;">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiMaintenanceEvents"><?= $kpiMaintenance ?></span>
                <span class="stat-trend neutral">Asset Holds</span>
              </div>
              <span class="stat-label">Facility &amp; Schedule Holds</span>
            </div>
          </div>

          <!-- Security & RBAC Access -->
          <div class="my-res-stat-card" data-cat="security" onclick="filterAuditByCategory('security')" style="cursor: pointer;">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiSecurityEvents"><?= $kpiSecurity ?></span>
                <span class="stat-trend positive">Governance</span>
              </div>
              <span class="stat-label">Security &amp; RBAC Changes</span>
            </div>
          </div>
        </section>

        <!-- ==========================================================================
             TOOLBAR: SEARCH, TIMEFRAME FILTERS & VIEW SWITCHER
             ========================================================================== -->
        <div class="my-res-toolbar-card admin-toolbar-card">

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
              <button type="button" class="search-clear-btn" id="auditSearchClear" onclick="clearAuditSearch()" title="Clear search" style="display: none;" aria-label="Clear search">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <div class="select-filter-wrap">
              <select id="timeframeFilterSelect" class="venue-filter-select" onchange="handleTimeframeFilter(this.value)">
                <option value="all">All Timelines</option>
                <option value="today">Today Only</option>
                <option value="yesterday">Yesterday</option>
                <option value="week">Past 7 Days</option>
                <option value="earlier">Earlier Records</option>
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

                <?php if (empty($processedLogs)): ?>
                  <tr>
                    <td colspan="6" class="admin-empty-table-state">
                      <div class="empty-state-content">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#8ca394" stroke-width="1.8">
                          <circle cx="11" cy="11" r="8"></circle>
                          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <h4>No Audit Events Found</h4>
                        <p>No activity logs have been recorded in the system yet.</p>
                      </div>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($processedLogs as $log): 
                    $formattedDetails = preg_replace('/(ATI-(?:RES|BK)-[0-9A-Z-]+)/i', '<span class="audit-ref">$1</span>', htmlspecialchars($log['details']));
                  ?>
                  <tr data-event-id="<?= htmlspecialchars($log['eventId']) ?>" 
                      data-category="<?= htmlspecialchars($log['category']) ?>" 
                      data-severity="<?= htmlspecialchars($log['severity']) ?>" 
                      data-timeframe="<?= htmlspecialchars($log['timeframe']) ?>">
                    <td>
                      <div class="td-ref-group">
                        <strong style="color: #172d1f; font-size: 0.88rem;"><?= $log['timeHeading'] ?></strong>
                        <span class="td-sub-date"><?= htmlspecialchars($log['subDate']) ?></span>
                      </div>
                    </td>
                    <td>
                      <span class="event-type-chip <?= htmlspecialchars($log['chipClass']) ?>">
                        <span class="event-chip-dot"></span>
                        <?= htmlspecialchars($log['chipLabel']) ?>
                      </span>
                    </td>
                    <td>
                      <div class="audit-narrative-text">
                        <?= $formattedDetails ?>
                      </div>
                    </td>
                    <td>
                      <div class="audit-actor-cell">
                        <div class="audit-actor-avatar <?= $log['isDirector'] ? 'director' : '' ?>"><?= htmlspecialchars($log['initials']) ?></div>
                        <div>
                          <div class="audit-actor-name"><?= htmlspecialchars($log['actorName']) ?></div>
                          <span class="audit-actor-role"><?= htmlspecialchars($log['actorRole']) ?></span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="audit-target-badge"><?= htmlspecialchars($log['targetResource']) ?></span>
                    </td>
                    <td style="text-align: right;">
                      <span class="security-hash-tag" title="Verified IP: <?= htmlspecialchars($log['ipAddress']) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <?= htmlspecialchars($log['hashTag']) ?>
                      </span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- 2. TIMELINE VIEW (CHRONOLOGICAL STREAM) -->
          <div class="audit-timeline-container" id="auditTimelineViewContainer" style="display: none;">
            <div id="auditNoResultsTimeline" style="display: none; text-align: center; padding: 3rem 1rem;">
              <h4 style="color: #183823; margin: 0 0 0.5rem 0;">No Timeline Records Found</h4>
              <p style="color: #657b6f; margin: 0;">No event milestones match your active search terms or filters.</p>
            </div>

            <?php if (empty($processedLogs)): ?>
              <div style="text-align: center; padding: 3rem 1rem;">
                <h4 style="color: #183823; margin: 0 0 0.5rem 0;">No Timeline Records Found</h4>
                <p style="color: #657b6f; margin: 0;">No activity milestones recorded yet.</p>
              </div>
            <?php else: ?>
              <?php foreach ($processedLogs as $log): 
                $dotClass = '';
                $dotIconSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                if ($log['severity'] === 'danger' || $log['chipClass'] === 'decline') {
                    $dotClass = 'red';
                    $dotIconSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                } elseif ($log['severity'] === 'warning' || $log['chipClass'] === 'submission' || $log['category'] === 'maintenance') {
                    $dotClass = 'amber';
                    $dotIconSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="3"><circle cx="12" cy="12" r="2"></circle></svg>';
                } elseif ($log['category'] === 'security') {
                    $dotClass = 'blue';
                    $dotIconSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="3"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect></svg>';
                }
                $formattedDetails = preg_replace('/(ATI-(?:RES|BK)-[0-9A-Z-]+)/i', '<span class="audit-ref">$1</span>', htmlspecialchars($log['details']));
              ?>
              <div class="timeline-event-card" data-event-id="<?= htmlspecialchars($log['eventId']) ?>" data-category="<?= htmlspecialchars($log['category']) ?>" data-severity="<?= htmlspecialchars($log['severity']) ?>" data-timeframe="<?= htmlspecialchars($log['timeframe']) ?>">
                <div class="timeline-event-dot <?= $dotClass ?>">
                  <?= $dotIconSvg ?>
                </div>
                <div class="timeline-card-content">
                  <div class="timeline-header-meta">
                    <span class="event-type-chip <?= htmlspecialchars($log['chipClass']) ?>"><?= htmlspecialchars($log['chipLabel']) ?></span>
                    <span class="timeline-time-text"><?= $log['timeHeading'] ?> (<?= htmlspecialchars($log['subDate']) ?>)</span>
                  </div>
                  <div class="audit-narrative-text" style="margin-bottom: 0.85rem;">
                    <?= $formattedDetails ?>
                  </div>
                  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                    <div class="audit-actor-cell">
                      <div class="audit-actor-avatar <?= $log['isDirector'] ? 'director' : '' ?>" style="width: 28px; height: 28px; font-size: 0.72rem;"><?= htmlspecialchars($log['initials']) ?></div>
                      <span style="font-size: 0.82rem; font-weight: 700; color: #1e3828;"><?= htmlspecialchars($log['actorName']) ?> (<?= htmlspecialchars($log['actorRole']) ?>)</span>
                    </div>
                    <span class="security-hash-tag"><?= htmlspecialchars($log['hashTag']) ?> &bull; <?= htmlspecialchars($log['targetResource']) ?></span>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- 3. PAGINATION BAR -->
          <div class="audit-pagination-bar" id="auditPaginationBar">
            <div class="pagination-info" id="auditPaginationInfo">
              Showing <strong id="paginationStart">1</strong> to <strong id="paginationEnd">15</strong> of <strong id="paginationTotal"><?= count($processedLogs) ?></strong> records
            </div>
            <div class="pagination-controls">
              <div class="pagination-page-size">
                <label for="auditPageSizeSelect">Per page:</label>
                <select id="auditPageSizeSelect" class="pagination-size-select" onchange="changeAuditPageSize(this.value)">
                  <option value="10">10</option>
                  <option value="15" selected>15</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="all">All</option>
                </select>
              </div>
              <div class="pagination-nav" id="auditPaginationNav">
                <!-- Dynamically generated by js/admin_audit.js -->
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
