<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/admin_guard.php';
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator Executive Dashboard (Sidebar Layout - Terno with ATI Portal)
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');

// Fetch all reservations joined with facilities, rooms, and applicants
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
             ORDER BY r.id DESC";

$stmt = $db->query($resQuery);
$allReservations = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

$totalActiveReservations = 0;
$pendingReservations = 0;
$approvedReservations = 0;
$completedReservations = 0;
$hallsCount = 0;
$dormsCount = 0;
$archivedReservationsCount = 0;

$reservationsData = [];

foreach ($allReservations as $row) {
    $isArchived = !empty($row['deleted_at']);
    $st = $row['status'] ?? 'Pending';
    $isPending = in_array($st, ['Pending', 'Pending Endorsement', 'Pending Recommendation', 'Pending Clearance']);
    $isApproved = ($st === 'Approved');
    $isCompleted = ($st === 'Completed');
    $isRejected = in_array($st, ['Rejected', 'Cancelled']);

    $isDorm = (($row['facility_type'] ?? '') === 'dormitories' || !empty($row['room_number']));

    if ($isArchived) {
        $archivedReservationsCount++;
    } else {
        $totalActiveReservations++;
        if ($isPending) $pendingReservations++;
        if ($isApproved) $approvedReservations++;
        if ($isCompleted) $completedReservations++;
        if ($isDorm) {
            $dormsCount++;
        } else {
            $hallsCount++;
        }
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

    $isStage1 = in_array($st, ['Pending', 'Pending Endorsement', 'Awaiting Endorsement']);
    $isStage2 = in_array($st, ['Pending Recommendation', 'Awaiting Recommendation']);
    $isStage3 = in_array($st, ['Pending Clearance', 'Awaiting Clearance']);
    $stageKey = $isCompleted ? 'completed' : ($isApproved ? 'approved' : ($isRejected ? 'rejected' : ($isStage1 ? 'stage1' : ($isStage2 ? 'stage2' : 'stage3'))));

    $normalizedStatus = $isCompleted ? 'completed' : ($isApproved ? 'approved' : ($isRejected ? 'rejected' : 'pending'));

    $reservationsData[$ref] = [
        'id' => (int)$row['id'],
        'ref' => $ref,
        'applicant' => $row['applicant_name'] ?: 'Official Guest',
        'agency' => $row['applicant_agency'] ?: 'ATI Central Office',
        'email' => $row['applicant_email'] ?: 'N/A',
        'phone' => $row['applicant_phone'] ?: 'N/A',
        'venue' => $venueDisplay,
        'schedule' => $datesStr . ' (' . ($row['time_slot'] ?: 'Full Day') . ')',
        'rawDates' => $datesStr,
        'timeSlot' => $row['time_slot'] ?: 'Full Day (8:00 AM – 5:00 PM)',
        'pax' => (int)($row['pax_count'] ?? 0) . ' PAX',
        'paxCount' => (int)($row['pax_count'] ?? 0),
        'eventTitle' => $row['event_title'] ?: 'Official Institutional Activity',
        'equipment' => $row['special_notes'] ?: 'Standard Logistics & Venue Equipment',
        'documentPath' => $row['document_path'] ?: '',
        'status' => $normalizedStatus,
        'stage' => $stageKey,
        'rawStatus' => $st,
        'category' => $isDorm ? 'dorms' : 'halls',
        'dateFiled' => !empty($row['created_at']) ? date('M d, Y • h:i A', strtotime($row['created_at'])) : date('M d, Y • h:i A'),
        'isArchived' => $isArchived,
        'deletedAt' => $row['deleted_at'] ? date('M d, Y • h:i A', strtotime($row['deleted_at'])) : null
    ];
}
$totalReservations = $totalActiveReservations;

// Fetch User count for sidebar
$userCountStmt = $db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
$totalUsersCount = $userCountStmt ? (int)$userCountStmt->fetchColumn() : 0;

// Fetch unique facilities for filter
$facStmt = $db->query("SELECT * FROM facilities ORDER BY name ASC");
$facilitiesList = $facStmt ? $facStmt->fetchAll(PDO::FETCH_ASSOC) : [];
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
          <!-- Quick Search -->
          <div class="topbar-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="topbarSearchInput" placeholder="Quick search reservations..." oninput="handleAdminSearch(this.value)" autocomplete="off">
            <button type="button" class="search-clear-btn" id="topbarSearchClear" onclick="clearAdminSearch()" title="Clear search" style="display: none;" aria-label="Clear search">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
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

      <!-- Main Scrollable Body (TERNO WITH PORTAL) -->
      <main class="admin-content-container">

        <!-- Header Section -->
        <section class="my-res-header admin-page-header">
          <div class="my-res-header-text">
            <h2>All Reservations</h2>
            <p>Complete master list of all reservation requests across ATI Central Office facilities and dormitories.</p>
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
            <button type="button" class="filter-tab-btn" data-filter="completed" onclick="applyTabFilter('completed')">
              <span>Completed</span>
              <span class="tab-count-pill" id="tabCountCompleted"><?= $completedReservations ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="halls" onclick="applyTabFilter('halls')">
              <span>Function Halls</span>
              <span class="tab-count-pill" id="tabCountHalls"><?= $hallsCount ?></span>
            </button>
            <button type="button" class="filter-tab-btn" data-filter="dorms" onclick="applyTabFilter('dorms')">
              <span>Dormitories</span>
              <span class="tab-count-pill" id="tabCountDorms"><?= $dormsCount ?></span>
            </button>
            <button type="button" class="filter-tab-btn tab-archived" data-filter="archived" onclick="applyTabFilter('archived')">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              <span>Archived / Trash</span>
              <span class="tab-count-pill tab-count-archived" id="tabCountArchived"><?= $archivedReservationsCount ?></span>
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
                <?php foreach ($facilitiesList as $fac): ?>
                  <option value="<?= htmlspecialchars($fac['name']) ?>"><?= htmlspecialchars($fac['name']) ?></option>
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
                  <th>Attendees</th>
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
                      <h4>No reservation found</h4>
                      <p>No records matched your search query or selected filters.</p>
                      <button type="button" class="btn-system-secondary" style="font-size: 0.82rem; padding: 0.45rem 1.15rem;" onclick="resetAllAdminFilters()">Reset Filters</button>
                    </div>
                  </td>
                </tr>
                <?php if (empty($reservationsData)): ?>
                <tr>
                  <td colspan="7" class="admin-empty-table-state" style="text-align: center; padding: 3.5rem 1rem;">
                    <div class="empty-state-content">
                      <div style="display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px; border-radius: 50%; background: #eef7f2; margin-bottom: 0.75rem;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10"></circle>
                          <line x1="12" y1="8" x2="12" y2="12"></line>
                          <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                      </div>
                      <h4 style="font-size: 1.05rem; font-weight: 700; color: #172c1f; margin: 0 0 0.35rem 0;">No reservation found</h4>
                      <p style="color: #637f6f; font-size: 0.85rem; margin: 0;">There are currently no reservations recorded in the database.</p>
                    </div>
                  </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($reservationsData as $item): 
                  $initials = getUserInitials($item['applicant']);
                  $isArchived = !empty($item['isArchived']);
                ?>
                <tr data-stage="<?=$item['stage']?>" 
                    data-ref="<?= htmlspecialchars($item['ref']) ?>" 
                    data-res-id="<?= $item['id'] ?>"
                    data-status="<?= htmlspecialchars($item['status']) ?>" 
                    data-category="<?= htmlspecialchars($item['category']) ?>" 
                    data-venue="<?= htmlspecialchars($item['venue']) ?>"
                    data-archived="<?= $isArchived ? '1' : '0' ?>"
                    class="<?= $isArchived ? 'row-reservation-archived' : '' ?>"
                    style="<?= $isArchived ? 'display: none;' : '' ?>">
                  <td>
                    <div class="td-ref-group">
                      <span class="td-ref-id"><?= htmlspecialchars($item['ref']) ?></span>
                      <span class="td-sub-date"><?= htmlspecialchars($item['dateFiled']) ?></span>
                    </div>
                  </td>
                  <td>
                    <div class="td-user-group">
                      <div class="user-avatar-circle sm"><?= htmlspecialchars($initials) ?></div>
                      <div class="td-user-details">
                        <span class="td-name"><?= htmlspecialchars($item['applicant']) ?></span>
                        <span class="td-org"><?= htmlspecialchars($item['agency']) ?></span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="td-facility-pill <?= $item['category'] === 'dorms' ? 'dorm' : 'hall' ?>">
                      <?php if ($item['category'] === 'dorms'): ?>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12h18M3 18h18M5 12V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6"></path></svg>
                      <?php else: ?>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                      <?php endif; ?>
                      <span><?= htmlspecialchars($item['venue']) ?></span>
                    </div>
                  </td>
                  <td>
                    <div class="td-schedule-text">
                      <strong><?= htmlspecialchars($item['rawDates']) ?></strong>
                      <span><?= htmlspecialchars($item['timeSlot']) ?></span>
                    </div>
                  </td>
                  <td>
                    <div class="td-pax-count">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                      <span><?= htmlspecialchars($item['pax']) ?></span>
                    </div>
                  </td>
                  <td>
                    <?php if ($isArchived): ?>
                      <span class="status-pill status-archived" style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
                        <span class="status-dot" style="background: #dc2626;"></span>
                        Archived / Trash
                      </span>
                      <?php if (!empty($item['deletedAt'])): ?>
                        <span style="display: block; font-size: 0.71rem; color: #dc2626; font-weight: 600; margin-top: 3px;">
                          Archived: <?= $item['deletedAt'] ?>
                        </span>
                      <?php endif; ?>
                    <?php elseif ($item['stage'] === 'completed' || $item['rawStatus'] === 'Completed'): ?>
                      <span class="status-pill status-completed">
                        <span class="status-dot"></span>
                        Completed &amp; Concluded
                      </span>
                    <?php elseif ($item['stage'] === 'approved'): ?>
                      <span class="status-pill status-approved">
                        <span class="status-dot"></span>
                        Confirmed &amp; Approved
                      </span>
                    <?php elseif ($item['stage'] === 'rejected'): ?>
                      <span class="status-pill status-cancelled">
                        <span class="status-dot"></span>
                        Declined
                      </span>
                    <?php elseif ($item['stage'] === 'stage1'): ?>
                      <span class="status-pill status-stage1">
                        <span class="status-dot"></span>
                        Awaiting Endorsement
                      </span>
                    <?php elseif ($item['stage'] === 'stage2'): ?>
                      <span class="status-pill status-stage2">
                        <span class="status-dot"></span>
                        Awaiting Recommendation
                      </span>
                    <?php elseif ($item['stage'] === 'stage3'): ?>
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
                  <td style="text-align: right;">
                    <div class="action-buttons-wrap">
                      <?php if ($isArchived): ?>
                        <button type="button" class="btn-table-action restore" onclick="confirmRestoreReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Restore Reservation to Active Queue">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                          <span>Restore</span>
                        </button>
                        <?php if ($currentAdminRole === 'super_admin'): ?>
                          <button type="button" class="btn-table-action purge" onclick="confirmPermanentDeleteReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Permanently delete reservation from database">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            <span>Purge</span>
                          </button>
                        <?php endif; ?>
                        <button type="button" class="btn-table-action view" onclick="viewReservationDetails('<?= htmlspecialchars($item['ref']) ?>')" title="View Booking Form">
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                      <?php else: ?>
                        <?php if ($item['stage'] === 'stage1'): ?>
                          <?php if ($currentAdminRole === 'endorsement' || $currentAdminRole === 'super_admin'): ?>
                            <button type="button" class="btn-table-action endorse" onclick="endorseReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Endorse Request (Stage 1)">
                              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                              <span>Endorse</span>
                            </button>
                            <button type="button" class="btn-table-action decline" onclick="openDeclineModal('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Decline Request (Stage 1)">
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                          <?php else: ?>
                            <span style="font-size:0.75rem; font-weight:700; color:#b45309; background:#fffbeb; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #fde68a;">Awaiting Endorsement</span>
                          <?php endif; ?>
                        <?php elseif ($item['stage'] === 'stage2'): ?>
                          <?php if ($currentAdminRole === 'recommendation' || $currentAdminRole === 'super_admin'): ?>
                            <button type="button" class="btn-table-action recommend" onclick="recommendReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Recommend Request (Stage 2)">
                              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                              <span>Recommend</span>
                            </button>
                            <button type="button" class="btn-table-action decline" onclick="openDeclineModal('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Decline Request (Stage 2)">
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                          <?php else: ?>
                            <span style="font-size:0.75rem; font-weight:700; color:#1d4ed8; background:#eff6ff; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #bfdbfe;">Awaiting Recommendation</span>
                          <?php endif; ?>
                        <?php elseif ($item['stage'] === 'stage3'): ?>
                          <?php if ($currentAdminRole === 'clearance' || $currentAdminRole === 'super_admin'): ?>
                            <button type="button" class="btn-table-action approve" onclick="clearanceReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Grant Director Clearance (Stage 3)">
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                              <span>Approve</span>
                            </button>
                            <button type="button" class="btn-table-action decline" onclick="openDeclineModal('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Decline Request (Stage 3)">
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                          <?php else: ?>
                            <span style="font-size:0.75rem; font-weight:700; color:#047857; background:#ecfdf5; padding:0.25rem 0.55rem; border-radius:9999px; border:1px solid #a7f3d0;">Awaiting Clearance</span>
                          <?php endif; ?>
                        <?php elseif ($item['stage'] === 'completed' || $item['rawStatus'] === 'Completed'): ?>
                          <span class="badge-completed-note">Completed</span>
                        <?php elseif ($item['stage'] === 'approved'): ?>
                          <button type="button" class="btn-table-action complete" onclick="confirmCompleteReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Mark Reservation as Completed &amp; Concluded">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Complete</span>
                          </button>
                          <span class="badge-approved-note">Approved</span>
                        <?php else: ?>
                          <span class="badge-declined-note">Declined</span>
                        <?php endif; ?>

                        <button type="button" class="btn-table-action view" onclick="viewReservationDetails('<?= htmlspecialchars($item['ref']) ?>')" title="View Booking Form">
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>

                        <button type="button" class="btn-table-action delete-res" onclick="confirmDeleteReservation('<?= htmlspecialchars($item['ref']) ?>', '<?= htmlspecialchars(addslashes($item['applicant'])) ?>')" title="Move Reservation to Trash (Soft Delete)">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
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
          <label>Official Supporting Document / Letter of Intent</label>
          <div class="attachment-preview-box" id="modalDocBox">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <div class="attachment-meta">
              <strong id="modalDocTitle">Official_Request_Document.pdf</strong>
              <small id="modalDocSub">Verified Document Attachment</small>
            </div>
            <a id="btnModalPreviewDoc" href="#" target="_blank" class="btn-preview-file" style="text-decoration: none;">View Document</a>
            <span id="modalNoDocText" style="display:none; font-size:0.8rem; color:#8a9e92; font-style:italic;">No document attached</span>
          </div>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeReservationModal()">Close Review</button>
        <button type="button" class="btn-complete-modal" id="btnModalComplete" onclick="modalTriggerComplete()" style="display: none;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <span>Mark as Completed</span>
        </button>
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
       TOAST NOTIFICATION CONTAINER
       ========================================================================== -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- Scripts -->
  <script>
    window.initialReservationsData = <?= json_encode((object)$reservationsData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  </script>
  <script src="js/admin_reservations.js?v=<?php echo time(); ?>"></script>
</body>

</html>
