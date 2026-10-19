<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/admin_guard.php';

/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator Executive Dashboard (Sidebar Layout - Terno with ATI Portal)
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');

// 1. Fetch all reservations joined with facilities, rooms, and applicants
$resQuery = "SELECT r.*, 
                    f.name AS facility_name, 
                    f.type AS facility_type,
                    rm.room_number,
                    rm.floor AS room_floor,
                    u.full_name AS applicant_name,
                    u.email AS applicant_email,
                    u.contact_number AS applicant_phone,
                    u.office_agency AS applicant_agency
             FROM reservations r
             LEFT JOIN facilities f ON r.facility_id = f.id
             LEFT JOIN rooms rm ON r.room_id = rm.id
             LEFT JOIN users u ON r.user_id = u.id
             WHERE r.deleted_at IS NULL
             ORDER BY r.id DESC";

$stmt = $db->query($resQuery);
$allReservations = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

$totalReservations = count($allReservations);
$pendingReservations = 0;
$approvedReservations = 0;
$hallsCount = 0;
$dormsCount = 0;
$totalPaxServed = 0;

$dashboardReservationsData = [];

foreach ($allReservations as $row) {
  $st = $row['status'] ?? 'Pending';
  $isPending = in_array($st, ['Pending', 'Pending Endorsement', 'Pending Recommendation', 'Pending Clearance']);
  $isApproved = in_array($st, ['Approved', 'Completed']);

  if ($isPending)
    $pendingReservations++;
  if ($isApproved) {
    $approvedReservations++;
    $totalPaxServed += (int) ($row['pax_count'] ?? 0);
  }

  $isDorm = ($row['facility_type'] === 'dormitories' || !empty($row['room_number']));
  if ($isDorm) {
    $dormsCount++;
  } else {
    $hallsCount++;
  }

  $ref = $row['reference_no'];
  $startDateTimestamp = strtotime($row['start_date']);
  $startDateStr = date('M d, Y', $startDateTimestamp);
  if (!empty($row['end_date']) && $row['end_date'] !== $row['start_date']) {
    $datesStr = date('M d', $startDateTimestamp) . ' – ' . date('M d, Y', strtotime($row['end_date']));
  } else {
    $datesStr = $startDateStr;
  }

  $venueDisplay = $row['facility_name'] ?: 'Official Facility';
  if (!empty($row['room_number'])) {
    $venueDisplay .= ' (Room ' . $row['room_number'] . ')';
  }

  $dashboardReservationsData[$ref] = [
    'ref' => $ref,
    'applicant' => $row['applicant_name'] ?: 'Official Guest',
    'agency' => $row['applicant_agency'] ?: 'ATI Central Office',
    'email' => $row['applicant_email'] ?: 'N/A',
    'phone' => $row['applicant_phone'] ?: 'N/A',
    'venue' => $venueDisplay,
    'schedule' => $datesStr . ' (' . ($row['time_slot'] ?: 'Full Day') . ')',
    'pax' => (int) ($row['pax_count'] ?? 0) . ' PAX',
    'eventTitle' => $row['event_title'] ?: 'Official Institutional Activity',
    'equipment' => $row['special_notes'] ?: 'Standard Logistics & Venue Equipment',
    'status' => strtolower($isApproved ? 'approved' : ($st === 'Rejected' ? 'rejected' : 'pending')),
    'rawStatus' => $st,
    'category' => $isDorm ? 'dorms' : 'halls',
    'dateFiled' => !empty($row['created_at']) ? date('M d, Y • h:i A', strtotime($row['created_at'])) : date('M d, Y • h:i A')
  ];
}

// 2. Fetch User count
$userCountStmt = $db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
$totalUsersCount = $userCountStmt ? (int) $userCountStmt->fetchColumn() : 0;

// 3. Fetch Real Monitored Facilities with Live Status
$today = date('Y-m-d');
$facStmt = $db->query("SELECT * FROM facilities ORDER BY id ASC");
$monitoredFacilities = $facStmt ? $facStmt->fetchAll(PDO::FETCH_ASSOC) : [];
$facilitiesCount = count($monitoredFacilities);

// Fetch approved reservations covering today (active)
$activeResStmt = $db->prepare("SELECT r.facility_id, r.room_id, r.reference_no, r.event_title, r.start_date, r.end_date, r.time_slot, u.full_name AS applicant_name 
                               FROM reservations r
                               LEFT JOIN users u ON r.user_id = u.id
                               WHERE r.status IN ('Approved', 'Completed') 
                                 AND r.start_date <= :today 
                                 AND (r.end_date >= :today OR r.end_date IS NULL)
                                 AND r.deleted_at IS NULL");
$activeResStmt->execute([':today' => $today]);
$activeTodayList = $activeResStmt->fetchAll(PDO::FETCH_ASSOC);

$facilityActiveToday = [];
foreach ($activeTodayList as $res) {
    $fid = (int)$res['facility_id'];
    if (!isset($facilityActiveToday[$fid])) {
        $facilityActiveToday[$fid] = $res;
    }
}

// Fetch upcoming approved reservations
$upcResStmt = $db->prepare("SELECT r.facility_id, r.room_id, r.reference_no, r.event_title, r.start_date, r.end_date, r.time_slot, u.full_name AS applicant_name 
                            FROM reservations r
                            LEFT JOIN users u ON r.user_id = u.id
                            WHERE r.status IN ('Approved', 'Completed') 
                              AND r.start_date > :today 
                              AND r.deleted_at IS NULL
                            ORDER BY r.start_date ASC");
$upcResStmt->execute([':today' => $today]);
$upcomingList = $upcResStmt->fetchAll(PDO::FETCH_ASSOC);

$facilityUpcoming = [];
foreach ($upcomingList as $res) {
    $fid = (int)$res['facility_id'];
    if (!isset($facilityUpcoming[$fid])) {
        $facilityUpcoming[$fid] = $res;
    }
}

// Compute dynamic facility status data
$processedFacilities = [];
$totalAvailableFacilities = 0;
$totalOccupiedFacilities = 0;
$totalMaintenanceFacilities = 0;

foreach ($monitoredFacilities as $fac) {
    $fid = (int)$fac['id'];
    $isMaint = ($fac['status'] === 'maintenance');
    $isActiveToday = isset($facilityActiveToday[$fid]);
    $hasUpcoming = isset($facilityUpcoming[$fid]);

    if ($isMaint) {
        $stateKey = 'maintenance';
        $stateBadgeClass = 'state-maintenance';
        $stateLabel = 'Maintenance Hold';
        $totalMaintenanceFacilities++;
    } elseif ($isActiveToday) {
        $stateKey = 'occupied';
        $stateBadgeClass = 'state-occupied';
        $todayRes = $facilityActiveToday[$fid];
        $timeStr = !empty($todayRes['time_slot']) ? ' (' . $todayRes['time_slot'] . ')' : '';
        $stateLabel = 'Occupied Today' . $timeStr;
        $totalOccupiedFacilities++;
    } elseif ($hasUpcoming) {
        $stateKey = 'reserved';
        $stateBadgeClass = 'state-occupied';
        $upRes = $facilityUpcoming[$fid];
        $upStart = date('M d', strtotime($upRes['start_date']));
        $upEnd = (!empty($upRes['end_date']) && $upRes['end_date'] !== $upRes['start_date']) ? '–' . date('d', strtotime($upRes['end_date'])) : '';
        $stateLabel = 'Reserved (' . $upStart . $upEnd . ')';
        $totalAvailableFacilities++;
    } else {
        $stateKey = 'available';
        $stateBadgeClass = 'state-available';
        $stateLabel = 'Available';
        $totalAvailableFacilities++;
    }

    $img = !empty($fac['image']) && file_exists(__DIR__ . '/' . $fac['image']) ? $fac['image'] : 'assets/images/function_hall.jpg';
    if (!empty($fac['image']) && strpos($fac['image'], 'uploads/') !== false) {
        $img = $fac['image'];
    }

    $processedFacilities[] = array_merge($fac, [
        'stateKey' => $stateKey,
        'stateBadgeClass' => $stateBadgeClass,
        'stateLabel' => $stateLabel,
        'isMaint' => $isMaint,
        'displayImage' => $img
    ]);
}

// 4. Fetch Recent Audit Logs
$auditStmt = $db->query("SELECT a.*, u.full_name FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 8");
$recentAuditLogs = $auditStmt ? $auditStmt->fetchAll(PDO::FETCH_ASSOC) : [];

// 5. Calculations
$occupancyRate = ($totalReservations > 0) ? round(($approvedReservations / $totalReservations) * 100, 1) : 0;
$estimatedUtilization = number_format(max($totalPaxServed * 850, 45000));
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
    <?php
    $sidebarPendingCount = $pendingReservations;
    include __DIR__ . '/includes/admin_sidebar.php';
    ?>

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
          <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()"
            aria-label="Toggle Sidebar Navigation">
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
              <span>Admin Portal</span>
            </div>
            <h2>Facility & Dormitory Reservation Overview</h2>
            <p>Review and act on pending institutional requests, oversee auditorium & dormitory occupancy, and maintain
              live facility schedules for ATI Central Office.</p>
          </div>
        </section>

        <!-- ==========================================================================
             KPI STAT CARDS (TERNO WITH SYSTEM DESIGN)
             ========================================================================== -->
        <section class="my-res-stats-grid admin-kpi-grid" aria-label="Executive KPI Overview">
          <!-- Total Reservations -->
          <div class="my-res-stat-card active-filter" data-kpi="total" onclick="applyTabFilter('all', true)"
            title="Click to view all reservations">
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
                <span class="stat-value" id="kpiTotal"><?= $totalReservations ?></span>
                <span class="stat-trend positive">Live</span>
              </div>
              <span class="stat-label">Total Reservations</span>
            </div>
          </div>

          <!-- Pending Approvals -->
          <div class="my-res-stat-card admin-stat-highlight" data-kpi="pending"
            onclick="applyTabFilter('pending', true)" title="Click to filter pending approval requests">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiPending"><?= $pendingReservations ?></span>
                <span class="stat-pill-urgent">Needs Action</span>
              </div>
              <span class="stat-label">Pending Approval Requests</span>
            </div>
          </div>

          <!-- Venue Occupancy -->
          <div class="my-res-stat-card" data-kpi="occupancy" id="cardOccupancyKpi" onclick="openOccupancyModal()"
            title="Click to open occupancy analytics & graphs">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiOccupancy"><?= $occupancyRate ?>%</span>
                <span class="stat-trend neutral"><?= $approvedReservations ?> Approved</span>
              </div>
              <span class="stat-label">Venue & Dorm Occupancy</span>
            </div>
          </div>

          <!-- Monthly Revenue / Utilization -->
          <div class="my-res-stat-card" data-kpi="revenue"
            onclick="showAdminToast('Estimated Utilization: ₱<?= $estimatedUtilization ?> based on booked attendees.', 'info')"
            title="Click to view monthly utilization summary">
            <div class="stat-icon-box emerald">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M8.5 17V7h4.5a3 3 0 0 1 0 6H8.5"></path>
                <line x1="6" y1="9.5" x2="15.5" y2="9.5"></line>
                <line x1="6" y1="12" x2="15.5" y2="12"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiRevenue">₱<?= $estimatedUtilization ?></span>
                <span class="stat-trend positive">Live Est.</span>
              </div>
              <span class="stat-label">Estimated Facility Utilization</span>
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
              <span class="tab-count-pill" id="tabCountAll"><?= $totalReservations ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="pending" onclick="applyTabFilter('pending')">
              <span>Pending Review</span>
              <span class="tab-count-pill tab-count-pending" id="tabCountPending"><?= $pendingReservations ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="approved" onclick="applyTabFilter('approved')">
              <span>Approved</span>
              <span class="tab-count-pill" id="tabCountApproved"><?= $approvedReservations ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="halls" onclick="applyTabFilter('halls')">
              <span>Function Halls</span>
              <span class="tab-count-pill" id="tabCountHalls"><?= $hallsCount ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="dorms" onclick="applyTabFilter('dorms')">
              <span>Dormitories</span>
              <span class="tab-count-pill" id="tabCountDorms"><?= $dormsCount ?></span>
            </button>
          </div>

          <!-- Controls Right -->
          <div class="toolbar-controls-right">
            <div class="search-box-wrap admin-search-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <input type="text" id="adminSearchInput" class="search-input"
                placeholder="Search applicant, agency, venue, or ref #..." oninput="handleAdminSearch(this.value)"
                autocomplete="off">
              <button type="button" class="search-clear-btn" id="adminSearchClear" onclick="clearAdminSearch()"
                title="Clear search" style="display: none;" aria-label="Clear search">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"
                  stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <div class="select-filter-wrap">
              <select id="venueFilterSelect" class="venue-filter-select" onchange="handleVenueSelectFilter(this.value)">
                <option value="all">All Venues &amp; Rooms</option>
                <?php foreach ($monitoredFacilities as $f): ?>
                  <option value="<?= htmlspecialchars($f['name']) ?>"><?= htmlspecialchars($f['name']) ?></option>
                <?php endforeach; ?>
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
                      <button type="button" class="btn-system-secondary"
                        style="font-size: 0.82rem; padding: 0.45rem 1.15rem;" onclick="resetAllAdminFilters()">Reset
                        Filters</button>
                    </div>
                  </td>
                </tr>

                <?php if (empty($allReservations)): ?>
                  <tr>
                    <td colspan="7" class="admin-empty-table-state" style="padding: 3rem 1rem;">
                      <div class="empty-state-content">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#8ca394" stroke-width="1.8">
                          <circle cx="11" cy="11" r="8"></circle>
                          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <h4>No Reservations Recorded</h4>
                        <p>There are currently no facility reservations in the system.</p>
                      </div>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($allReservations as $row):
                    $ref = htmlspecialchars($row['reference_no'] ?? '');
                    $applicantName = htmlspecialchars($row['applicant_name'] ?? 'Official Guest');
                    $applicantAgency = htmlspecialchars($row['applicant_agency'] ?? 'ATI Central Office');
                    $st = $row['status'] ?? 'Pending';
                    $isApproved = in_array($st, ['Approved', 'Completed']);
                    $isRejected = in_array($st, ['Rejected', 'Cancelled']);
                    $isStage1 = in_array($st, ['Pending', 'Pending Endorsement', 'Awaiting Endorsement']);
                    $isStage2 = in_array($st, ['Pending Recommendation', 'Awaiting Recommendation']);
                    $isStage3 = in_array($st, ['Pending Clearance', 'Awaiting Clearance']);
                    $isPending = ($isStage1 || $isStage2 || $isStage3);
                    $statusKey = $isApproved ? 'approved' : ($isRejected ? 'rejected' : 'pending');
                    $isDorm = (($row['facility_type'] ?? '') === 'dormitories' || !empty($row['room_number']));
                    $categoryKey = $isDorm ? 'dorms' : 'halls';
                    $venueName = $row['facility_name'] ?: 'Official Facility';
                    if (!empty($row['room_number'])) {
                      $venueName .= ' (Room ' . $row['room_number'] . ')';
                    }
                    $venueNameEsc = htmlspecialchars($venueName);

                    $startDateTimestamp = strtotime($row['start_date'] ?? 'now');
                    $startDateStr = date('M d, Y', $startDateTimestamp);
                    if (!empty($row['end_date']) && $row['end_date'] !== $row['start_date']) {
                      $datesStr = date('M d', $startDateTimestamp) . ' – ' . date('M d, Y', strtotime($row['end_date']));
                    } else {
                      $datesStr = $startDateStr;
                    }
                    $createdStr = !empty($row['created_at']) ? date('M d, Y • h:i A', strtotime($row['created_at'])) : date('M d, Y • h:i A');
                    $timeSlotStr = htmlspecialchars($row['time_slot'] ?: 'Full Day');
                    $pax = (int) ($row['pax_count'] ?? 0);
                    ?>
                    <tr data-ref="<?= $ref ?>" data-status="<?= $statusKey ?>"
                      data-stage="<?= $isStage1 ? 'stage1' : ($isStage2 ? 'stage2' : ($isStage3 ? 'stage3' : ($isApproved ? 'approved' : 'rejected'))) ?>"
                      data-category="<?= $categoryKey ?>" data-venue="<?= $venueNameEsc ?>">
                      <td data-label="Reference & Date">
                        <div class="td-ref-group">
                          <span class="td-ref-id"><?= $ref ?></span>
                          <span class="td-sub-date"><?= $createdStr ?></span>
                        </div>
                      </td>
                      <td data-label="Applicant & Org">
                        <div class="td-user-group">
                          <div class="user-avatar-circle sm"><?= htmlspecialchars(getUserInitials($applicantName)) ?></div>
                          <div class="td-user-details">
                            <span class="td-name"><?= $applicantName ?></span>
                            <span class="td-org"><?= $applicantAgency ?></span>
                          </div>
                        </div>
                      </td>
                      <td data-label="Requested Facility">
                        <div class="td-facility-pill <?= $categoryKey === 'dorms' ? 'dorm' : 'hall' ?>">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                          </svg>
                          <span><?= $venueNameEsc ?></span>
                        </div>
                      </td>
                      <td data-label="Schedule / Duration">
                        <div class="td-schedule-text">
                          <strong><?= htmlspecialchars($datesStr) ?></strong>
                          <span><?= $timeSlotStr ?></span>
                        </div>
                      </td>
                      <td data-label="Attendees (PAX)">
                        <div class="td-pax-count">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                          </svg>
                          <span><?= $pax ?> PAX</span>
                        </div>
                      </td>
                      <td data-label="Current Status">
                        <?php if ($isApproved): ?>
                          <span class="status-pill status-approved">
                            <span class="status-dot"></span>
                            Confirmed & Approved
                          </span>
                        <?php elseif ($isRejected): ?>
                          <span class="status-pill status-cancelled">
                            <span class="status-dot"></span>
                            Declined
                          </span>
                        <?php elseif ($isStage1): ?>
                          <span class="status-pill status-stage1">
                            <span class="status-dot"></span>
                            Awaiting Endorsement
                          </span>
                        <?php elseif ($isStage2): ?>
                          <span class="status-pill status-stage2">
                            <span class="status-dot"></span>
                            Awaiting Recommendation
                          </span>
                        <?php elseif ($isStage3): ?>
                          <span class="status-pill status-stage3">
                            <span class="status-dot"></span>
                            Awaiting Clearance
                          </span>
                        <?php else: ?>
                          <span class="status-pill status-pending">
                            <span class="status-dot"></span>
                            Pending Review
                          </span>
                        <?php endif; ?>
                      </td>
                      <td data-label="Actions" style="text-align: right;">
                        <div class="action-buttons-wrap">
                          <?php if ($isStage1): ?>
                            <?php if ($currentAdminRole === 'endorsement' || $currentAdminRole === 'super_admin'): ?>
                              <button type="button" class="btn-table-action endorse"
                                onclick="endorseReservation('<?= $ref ?>', '<?= addslashes($applicantName) ?>')"
                                title="Endorse Request (Stage 1)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                  stroke-width="2.5">
                                  <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Endorse</span>
                              </button>
                              <button type="button" class="btn-table-action decline"
                                onclick="openDeclineModal('<?= $ref ?>', '<?= addslashes($applicantName) ?>')"
                                title="Decline Request (Stage 1)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                  stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                  <line x1="18" y1="6" x2="6" y2="18"></line>
                                  <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                <span>Decline</span>
                              </button>
                            <?php else: ?>
                              <span
                                style="font-size:0.75rem; font-weight:700; color:#b45309; background:#fffbeb; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #fde68a;">Awaiting
                                Endorsement</span>
                            <?php endif; ?>
                          <?php elseif ($isStage2): ?>
                            <?php if ($currentAdminRole === 'recommendation' || $currentAdminRole === 'super_admin'): ?>
                              <button type="button" class="btn-table-action recommend"
                                onclick="recommendReservation('<?= $ref ?>', '<?= addslashes($applicantName) ?>')"
                                title="Recommend Request (Stage 2)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                  stroke-width="2.5">
                                  <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Recommend</span>
                              </button>
                              <button type="button" class="btn-table-action decline"
                                onclick="openDeclineModal('<?= $ref ?>', '<?= addslashes($applicantName) ?>')"
                                title="Decline Request (Stage 2)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                  stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                  <line x1="18" y1="6" x2="6" y2="18"></line>
                                  <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                <span>Decline</span>
                              </button>
                            <?php else: ?>
                              <span
                                style="font-size:0.75rem; font-weight:700; color:#1d4ed8; background:#eff6ff; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #bfdbfe;">Awaiting
                                Recommendation</span>
                            <?php endif; ?>
                          <?php elseif ($isStage3): ?>
                            <?php if ($currentAdminRole === 'clearance' || $currentAdminRole === 'super_admin'): ?>
                              <button type="button" class="btn-table-action approve"
                                onclick="clearanceReservation('<?= $ref ?>', '<?= addslashes($applicantName) ?>')"
                                title="Grant Director Clearance & Permit (Stage 3)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                  stroke-width="2.5">
                                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                                <span>Approve</span>
                              </button>
                              <button type="button" class="btn-table-action decline"
                                onclick="openDeclineModal('<?= $ref ?>', '<?= addslashes($applicantName) ?>')"
                                title="Decline Request (Stage 3)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                  stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                  <line x1="18" y1="6" x2="6" y2="18"></line>
                                  <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                <span>Decline</span>
                              </button>
                            <?php else: ?>
                              <span
                                style="font-size:0.75rem; font-weight:700; color:#047857; background:#ecfdf5; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #a7f3d0;">Awaiting
                                Clearance</span>
                            <?php endif; ?>
                          <?php elseif ($isApproved): ?>
                            <span class="badge-approved-note">Approved by Director</span>
                          <?php else: ?>
                            <span class="badge-declined-note">Declined</span>
                          <?php endif; ?>
                          <button type="button" class="btn-table-action view"
                            onclick="viewReservationDetails('<?= $ref ?>')" title="View Booking Form">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2">
                              <circle cx="11" cy="11" r="8"></circle>
                              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
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
                <h3>Facilities &amp; Dormitories Master Status</h3>
                <p>Monitor real-time occupancy and toggle administrative maintenance holds.</p>
              </div>
              <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span class="count-tag-pill"><?= $facilitiesCount ?> Monitored Assets &bull; <?= $totalAvailableFacilities ?> Available</span>
                <a href="admin_facilities.php" class="btn-audit-portal-link" title="Open Facilities &amp; Dormitories Registry">
                  <span>Manage Registry</span>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </a>
              </div>
            </div>

            <div class="facility-status-list">
              <?php if (empty($processedFacilities)): ?>
                <div style="padding: 2.5rem 1rem; text-align: center; color: #627b6c; font-size: 0.9rem;">
                  <p style="margin: 0 0 0.5rem 0; font-weight: 600;">No facilities registered in the database yet.</p>
                  <a href="admin_facilities.php" class="btn-system-primary" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; text-decoration: none; display: inline-flex;">Manage Facilities</a>
                </div>
              <?php else: ?>
                <?php foreach ($processedFacilities as $fac): ?>
                  <div class="facility-item-row" data-venue-id="<?= $fac['id'] ?>">
                    <img src="<?= htmlspecialchars($fac['displayImage']) ?>" alt="<?= htmlspecialchars($fac['name']) ?>" class="facility-img-thumb"
                      style="width: 55px; height: 55px; border-radius: 8px; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.06);"
                      onerror="this.src='assets/images/function_hall.jpg'">
                    <div class="facility-item-info">
                      <div class="facility-item-title-row">
                        <strong><?= htmlspecialchars($fac['name']) ?></strong>
                        <span class="facility-state-badge <?= $fac['stateBadgeClass'] ?>" id="badge-<?= $fac['id'] ?>"><?= htmlspecialchars($fac['stateLabel']) ?></span>
                      </div>
                      <div class="facility-specs">
                        <?php if ($fac['type'] === 'dorms'): ?>
                          <span>Capacity: <?= (int)$fac['capacity'] ?> PAX (<?= (int)$fac['beds'] ?> Beds)</span> &bull; <span><?= htmlspecialchars($fac['features'] ?: 'WiFi &bull; En-suite Bath') ?></span>
                        <?php else: ?>
                          <span>Capacity: <?= (int)$fac['capacity'] ?> PAX</span> &bull; <span><?= htmlspecialchars($fac['features'] ?: 'Airconditioned &bull; Sound System') ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="facility-toggle-wrap">
                      <label class="switch-toggle" title="Toggle Maintenance Mode">
                        <input type="checkbox" <?= $fac['isMaint'] ? 'checked' : '' ?>
                          onchange="toggleFacilityMaintenance('<?= $fac['id'] ?>', '<?= addslashes(htmlspecialchars($fac['name'])) ?>', this.checked)">
                        <span class="slider-round"></span>
                      </label>
                      <span class="toggle-text">Maintenance</span>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
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
                <a href="admin_audit.php" class="btn-audit-portal-link"
                  title="Open Dedicated Institutional Audit Trail Portal">
                  <span>View Full Audit Portal</span>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </a>
              </div>
            </div>

            <div class="audit-stream-wrapper" id="auditStream">
              <?php if (!empty($recentAuditLogs)): ?>
                <?php foreach ($recentAuditLogs as $log):
                  $logAction = $log['action'] ?? '';
                  $logColor = 'blue';
                  if (strpos($logAction, 'APPROVE') !== false || strpos($logAction, 'CLEARANCE') !== false)
                    $logColor = 'green';
                  elseif (strpos($logAction, 'RECOMMEND') !== false || strpos($logAction, 'STAGE') !== false || strpos($logAction, 'SUBMISSION') !== false)
                    $logColor = 'amber';
                  elseif (strpos($logAction, 'REJECT') !== false || strpos($logAction, 'CANCEL') !== false)
                    $logColor = 'amber';
                  $logTimeStr = date('M d, Y • h:i A', strtotime($log['created_at']));
                  ?>
                  <div class="audit-item">
                    <div class="audit-dot <?= $logColor ?>"></div>
                    <div class="audit-content">
                      <p><?= htmlspecialchars($log['details']) ?></p>
                      <span class="audit-time"><?= $logTimeStr ?> &bull; Executed by
                        <?= htmlspecialchars($log['full_name'] ?: 'Super Admin') ?></span>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div style="padding: 1.5rem; text-align: center; color: #6b7280; font-size: 0.85rem;">
                  No institutional audit records logged yet.
                </div>
              <?php endif; ?>
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
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
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
            <span class="applicant-contact" id="modalApplicantContact">juan.delacruz@ati.da.gov.ph &bull; +63 917 123
              4567</span>
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
          <span id="modalEquipment">Projector & Screen, 4 Wireless Microphones, Stage Rostrum, Classroom Type
            Seating</span>
        </div>

        <div class="modal-data-box" style="margin-top: 1rem;">
          <label>Official Endorsement Attachment</label>
          <div class="attachment-preview-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            <div class="attachment-meta">
              <strong>Official_Request_Letter_Signed_CDD.pdf</strong>
              <small>Verified Signed PDF &bull; 1.4 MB</small>
            </div>
            <button type="button" class="btn-preview-file"
              onclick="showAdminToast('Downloading verified attachment...')">View Letter</button>
          </div>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeReservationModal()">Close Review</button>
        <button type="button" class="btn-decline-modal" id="btnModalDecline" onclick="modalTriggerDecline()">Decline
          Request</button>
        <button type="button" class="btn-approve-modal" id="btnModalApprove" onclick="modalTriggerApprove()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
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
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <p style="font-size: 0.92rem; color: #4b6354; margin-bottom: 1rem;">
          Please state the official administrative reason for declining reservation <strong
            id="declineModalRef">R-2026-0891</strong> by <span id="declineModalApplicant">Applicant</span>.
        </p>

        <div class="form-group-decline">
          <label
            style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.4rem;">Select
            Justification</label>
          <select id="declineReasonSelect"
            style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit;">
            <option value="Conflict with Official ATI Institutional Activity">Conflict with Official ATI Institutional
              Activity</option>
            <option value="Venue Under Scheduled Maintenance">Venue Under Scheduled Maintenance</option>
            <option value="Exceeds Maximum Venue Capacity">Exceeds Maximum Venue Capacity</option>
            <option value="Incomplete Supporting Documents / Request Letter">Incomplete Supporting Documents / Request
              Letter</option>
            <option value="Other Administrative Ground">Other Administrative Ground</option>
          </select>
        </div>

        <div class="form-group-decline" style="margin-top: 1rem;">
          <label
            style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.4rem;">Executive
            Remarks / Instructions to Applicant</label>
          <textarea id="declineRemarks" rows="3"
            placeholder="Enter remarks to be sent via official notification to the applicant..."
            style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;"></textarea>
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
  <div class="admin-modal-overlay" id="occupancyModal" style="display: none;"
    onclick="if(event.target===this) closeOccupancyModal()">
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
              <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: #556f60;">Real-time capacity utilization,
                statistics, and booking trend breakdowns</p>
            </div>
          </div>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeOccupancyModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
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
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: 76.4%; background: #174d2f;"></div>
            </div>
          </div>

          <div class="occ-metric-card">
            <span class="occ-metric-lbl">Function Halls</span>
            <div class="occ-metric-val">82.5%</div>
            <div class="occ-metric-sub"><strong>99</strong> / 120 Hall PAX</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: 82.5%; background: #2563eb;"></div>
            </div>
          </div>

          <div class="occ-metric-card">
            <span class="occ-metric-lbl">Dormitory Suites</span>
            <div class="occ-metric-val">69.0%</div>
            <div class="occ-metric-sub"><strong>69</strong> / 100 Dorm Beds</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: 69%; background: #d97706;"></div>
            </div>
          </div>

          <div class="occ-metric-card">
            <span class="occ-metric-lbl">Peak Load Day</span>
            <div class="occ-metric-val" style="color: #b45309;">94.2%</div>
            <div class="occ-metric-sub">Thursday, Oct 15 &bull; 207 PAX</div>
            <div class="occ-bar-track">
              <div class="occ-bar-fill" style="width: 94.2%; background: #ef4444;"></div>
            </div>
          </div>
        </div>

        <!-- 2. Controls & Switcher Toolbar -->
        <div class="occ-graph-toolbar">
          <div class="occ-toggle-group">
            <button type="button" class="occ-btn-toggle active" id="btnToggleBarGraph"
              onclick="switchOccupancyGraph('bar')">
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
            <label for="occTimeRangeSelect"
              style="font-size: 0.8rem; font-weight: 700; color: #4b6354;">Timeline:</label>
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
                <line x1="60" y1="130" x2="710" y2="130" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1">
                </line>
                <line x1="60" y1="175" x2="710" y2="175" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1">
                </line>
                <line x1="60" y1="220" x2="710" y2="220" stroke="#d5e3d8" stroke-width="1.5"></line>

                <!-- 80% Benchmark Line -->
                <line x1="60" y1="76" x2="710" y2="76" stroke="#eab308" stroke-dasharray="6 3" stroke-width="1.8">
                </line>
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
                  <text x="113" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Serrano
                    Hall</text>
                  <text x="113" y="255" font-size="10" fill="#698572" text-anchor="middle">108/120 PAX</text>
                </g>

                <!-- Bar 2: Training Hall A (85%) -> height = 153. y = 67 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="190" y="67" width="56" height="153" rx="6" fill="url(#gradHall)" class="bar-rect"></rect>
                  <text x="218" y="59" font-size="12" font-weight="800" fill="#174d2f" text-anchor="middle">85.0%</text>
                  <text x="218" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Training
                    Hall</text>
                  <text x="218" y="255" font-size="10" fill="#698572" text-anchor="middle">51/60 PAX</text>
                </g>

                <!-- Bar 3: Executive Boardroom (72%) -> height = 129.6. y = 90.4 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="295" y="90" width="56" height="130" rx="6" fill="url(#gradHall)" class="bar-rect"></rect>
                  <text x="323" y="82" font-size="12" font-weight="800" fill="#174d2f" text-anchor="middle">72.0%</text>
                  <text x="323" y="240" font-size="11" font-weight="700" fill="#2d4233"
                    text-anchor="middle">Boardroom</text>
                  <text x="323" y="255" font-size="10" fill="#698572" text-anchor="middle">18/25 PAX</text>
                </g>

                <!-- Bar 4: Mess Hall Dining (80%) -> height = 144. y = 76 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="400" y="76" width="56" height="144" rx="6" fill="url(#gradHall)" class="bar-rect"></rect>
                  <text x="428" y="68" font-size="12" font-weight="800" fill="#174d2f" text-anchor="middle">80.0%</text>
                  <text x="428" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Mess
                    Hall</text>
                  <text x="428" y="255" font-size="10" fill="#698572" text-anchor="middle">80/100 PAX</text>
                </g>

                <!-- Bar 5: Dorm Suite A (65%) -> height = 117. y = 103 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="505" y="103" width="56" height="117" rx="6" fill="url(#gradDorm)" class="bar-rect"></rect>
                  <text x="533" y="95" font-size="12" font-weight="800" fill="#d97706" text-anchor="middle">65.0%</text>
                  <text x="533" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Dorm Suite
                    A</text>
                  <text x="533" y="255" font-size="10" fill="#698572" text-anchor="middle">39/60 Beds</text>
                </g>

                <!-- Bar 6: Dorm Suite B (70%) -> height = 126. y = 94 -->
                <g class="occ-bar-group" tabindex="0">
                  <rect x="610" y="94" width="56" height="126" rx="6" fill="url(#gradDorm)" class="bar-rect"></rect>
                  <text x="638" y="86" font-size="12" font-weight="800" fill="#d97706" text-anchor="middle">70.0%</text>
                  <text x="638" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Dorm Suite
                    B</text>
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
              <span class="occ-legend-pill"><span class="occ-dot" style="background: #174d2f;"></span> Daily Occupancy
                Rate (%)</span>
              <span class="occ-legend-pill"><span class="occ-target-line"></span> Monthly Average (76.4%)</span>
            </div>

            <!-- SVG Line Graph -->
            <div class="occ-svg-container">
              <svg viewBox="0 0 740 280" class="occ-line-svg" preserveAspectRatio="xMidYMid meet">
                <!-- Grid Lines -->
                <line x1="60" y1="40" x2="710" y2="40" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1"></line>
                <line x1="60" y1="85" x2="710" y2="85" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1"></line>
                <line x1="60" y1="130" x2="710" y2="130" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1">
                </line>
                <line x1="60" y1="175" x2="710" y2="175" stroke="#e8efe9" stroke-dasharray="4 4" stroke-width="1">
                </line>
                <line x1="60" y1="220" x2="710" y2="220" stroke="#d5e3d8" stroke-width="1.5"></line>

                <!-- Average Line (76.4%) -> y = 220 - (0.764 * 180) = 82.5 -->
                <line x1="60" y1="82.5" x2="710" y2="82.5" stroke="#eab308" stroke-dasharray="6 3" stroke-width="1.8">
                </line>
                <text x="712" y="85" font-size="10" fill="#b45309" font-weight="700">76.4% Avg</text>

                <!-- Y Axis Labels -->
                <text x="45" y="44" font-size="11" fill="#789080" text-anchor="end">100%</text>
                <text x="45" y="89" font-size="11" fill="#789080" text-anchor="end">75%</text>
                <text x="45" y="134" font-size="11" fill="#789080" text-anchor="end">50%</text>
                <text x="45" y="179" font-size="11" fill="#789080" text-anchor="end">25%</text>
                <text x="45" y="224" font-size="11" fill="#789080" text-anchor="end">0%</text>

                <!-- Gradient Area Under Curve -->
                <!-- Points: Mon(105, 108) Tue(200, 86) Wed(295, 70) Thu(390, 50) Fri(485, 79) Sat(580, 115) Sun(675, 144) -->
                <path d="M 105 108 L 200 86 L 295 70 L 390 50 L 485 79 L 580 115 L 675 144 L 675 220 L 105 220 Z"
                  fill="url(#gradLineArea)"></path>

                <!-- The Curved Trend Line -->
                <path d="M 105 108 L 200 86 L 295 70 L 390 50 L 485 79 L 580 115 L 675 144" fill="none" stroke="#174d2f"
                  stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path>

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
                  <text x="390" y="34" font-size="12" font-weight="800" fill="#b45309" text-anchor="middle">94.2%
                    (Peak)</text>
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
                  <text x="580" y="101" font-size="11" font-weight="700" fill="#174d2f"
                    text-anchor="middle">58.0%</text>
                  <text x="580" y="240" font-size="11" font-weight="700" fill="#2d4233" text-anchor="middle">Sat</text>
                  <text x="580" y="255" font-size="10" fill="#698572" text-anchor="middle">Oct 10</text>
                </g>

                <!-- Sun Oct 11: 42.0% -> y = 144 -->
                <g class="occ-pt-group">
                  <circle cx="675" cy="144" r="6" fill="#ffffff" stroke="#174d2f" stroke-width="3"></circle>
                  <text x="675" y="130" font-size="11" font-weight="700" fill="#174d2f"
                    text-anchor="middle">42.0%</text>
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
          <h5
            style="margin: 0 0 0.75rem; font-size: 0.95rem; font-weight: 800; color: #174d2f; display: flex; align-items: center; justify-content: space-between;">
            <span>Monitored Facility Utilization Breakdown</span>
            <span style="font-size: 0.75rem; font-weight: 600; color: #60796b;">Total <?= $facilitiesCount ?> Institutional Assets</span>
          </h5>

          <div class="occ-table-wrap">
            <table class="occ-breakdown-table">
              <thead>
                <tr>
                  <th>Facility / Venue</th>
                  <th>Category</th>
                  <th>Capacity / Headcount</th>
                  <th>Current Availability Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($processedFacilities)): ?>
                  <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem; color: #627b6c;">No facilities recorded.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($processedFacilities as $fac): 
                    $isDorm = ($fac['type'] === 'dorms');
                    $capStr = $isDorm ? ((int)$fac['capacity'] . ' PAX (' . (int)$fac['beds'] . ' Beds)') : ((int)$fac['capacity'] . ' PAX');
                    $statusBadge = '<span class="occ-status-badge active">' . htmlspecialchars($fac['stateLabel']) . '</span>';
                    if ($fac['stateKey'] === 'maintenance') {
                      $statusBadge = '<span class="occ-status-badge" style="background:#fef2f2; color:#b91c1c; border-color:#fecaca;">Maintenance Hold</span>';
                    } elseif ($fac['stateKey'] === 'available') {
                      $statusBadge = '<span class="occ-status-badge" style="background:#ecfdf5; color:#047857; border-color:#a7f3d0;">Available</span>';
                    } elseif ($fac['stateKey'] === 'occupied') {
                      $statusBadge = '<span class="occ-status-badge active" style="background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe;">' . htmlspecialchars($fac['stateLabel']) . '</span>';
                    } elseif ($fac['stateKey'] === 'reserved') {
                      $statusBadge = '<span class="occ-status-badge" style="background:#fffbeb; color:#b45309; border-color:#fde68a;">' . htmlspecialchars($fac['stateLabel']) . '</span>';
                    }
                  ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($fac['name']) ?></strong></td>
                    <td><span class="occ-tag <?= $isDorm ? 'dorm' : 'hall' ?>"><?= $isDorm ? 'Dormitory Suite' : 'Function Hall' ?></span></td>
                    <td><?= $capStr ?></td>
                    <td><?= $statusBadge ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeOccupancyModal()">Close Analytics</button>
        <button type="button" class="btn-preview-file" onclick="exportOccupancyReport()"
          style="display: inline-flex; align-items: center; gap: 0.4rem;">
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
    <script>
      window.dashboardReservationsData = <?= json_encode($dashboardReservationsData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
      window.currentUserRole = <?= json_encode($currentAdminRole) ?>;
      window.currentActiveRole = <?= json_encode($currentRole) ?>;
    </script>
    <script src="js/admin_dashboard.js?v=<?php echo time(); ?>"></script>
</body>

</html>