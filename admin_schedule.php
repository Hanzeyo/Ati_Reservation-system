<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/admin_guard.php';
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator: Master Schedule & Calendar Operations Portal
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');

$db = (new Database())->getConnection();

// Ensure schedule_blocks table exists
$db->exec("CREATE TABLE IF NOT EXISTS schedule_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ref VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    facility_id VARCHAR(50) NOT NULL,
    facility_name VARCHAR(150) NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason VARCHAR(255) NULL,
    memo_ref VARCHAR(100) NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Dynamic sidebar counts
$pendingStmt = $db->query("SELECT COUNT(*) FROM reservations WHERE status IN ('Pending', 'Pending Endorsement', 'Pending Recommendation', 'Pending Clearance', 'Awaiting Endorsement', 'Awaiting Recommendation', 'Awaiting Clearance') AND deleted_at IS NULL");
$pendingCount = (int)$pendingStmt->fetchColumn();

$userCountStmt = $db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
$totalUsersCount = (int)$userCountStmt->fetchColumn();

// Fetch facilities ordered by customized display sequence
$facStmt = $db->query("SELECT id, name, type, status, maintenance_reason, est_resume_date FROM facilities WHERE deleted_at IS NULL ORDER BY display_order ASC, id ASC");
$facilitiesList = $facStmt->fetchAll(PDO::FETCH_ASSOC);

// Segregate facilities by category (Halls, Dorms, Guesthouses)
$hallsList = [];
$dormsList = [];
$guesthousesList = [];

foreach ($facilitiesList as $f) {
    $fType = strtolower($f['type'] ?? '');
    if ($fType === 'guesthouses') {
        $guesthousesList[] = $f;
    } elseif ($fType === 'dormitories' || $fType === 'dorms') {
        $dormsList[] = $f;
    } else {
        $hallsList[] = $f;
    }
}

// Fetch all reservations
$resQuery = "SELECT r.*, 
                    f.name AS facility_name, 
                    f.type AS facility_type,
                    rm.room_number,
                    u.full_name AS applicant_name,
                    u.email AS applicant_email,
                    u.contact_number AS applicant_phone,
                    u.office_agency AS applicant_agency,
                    end_u.full_name AS endorsed_by_name,
                    rec_u.full_name AS recommended_by_name,
                    clr_u.full_name AS cleared_by_name
             FROM reservations r
             LEFT JOIN facilities f ON r.facility_id = f.id
             LEFT JOIN rooms rm ON r.room_id = rm.id
             LEFT JOIN users u ON r.user_id = u.id
             LEFT JOIN users end_u ON r.endorsed_by = end_u.id
             LEFT JOIN users rec_u ON r.recommended_by = rec_u.id
             LEFT JOIN users clr_u ON r.cleared_by = clr_u.id
             WHERE r.status NOT IN ('Rejected', 'Cancelled')
               AND r.deleted_at IS NULL
             ORDER BY r.start_date ASC";

$stmt = $db->query($resQuery);
$dbReservations = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch schedule blocks
$blockStmt = $db->query("SELECT * FROM schedule_blocks ORDER BY start_date ASC");
$dbBlocks = $blockStmt ? $blockStmt->fetchAll(PDO::FETCH_ASSOC) : [];

$calendarEvents = [];

// 1. Process reservations aligned with 3-Stage Governance Workflow & Category Types
foreach ($dbReservations as $row) {
    $st = $row['status'] ?? 'Pending';
    
    $isGuesthouse = ($row['facility_type'] === 'guesthouses') || str_starts_with($row['reference_no'] ?? '', 'ATI-GH');
    $isDorm = !$isGuesthouse && (($row['facility_type'] === 'dormitories' || $row['facility_type'] === 'dorms') || !empty($row['room_id']));
    $category = $isGuesthouse ? 'guesthouses' : ($isDorm ? 'dormitories' : 'halls');

    // 3-Stage Status Classification
    if (in_array($st, ['Approved', 'Completed'])) {
        $colorClass = 'status-cleared';
        $statusKey = 'cleared';
        $statusLabel = 'Approved & Confirmed (Master Schedule)';
    } elseif (in_array($st, ['Pending Clearance', 'Awaiting Clearance'])) {
        $colorClass = 'status-pending-clear';
        $statusKey = 'pending_clear';
        $statusLabel = 'Stage 3: Pending Directorate Clearance';
    } elseif (in_array($st, ['Pending Recommendation', 'Awaiting Recommendation'])) {
        $colorClass = 'status-pending-rec';
        $statusKey = 'pending_rec';
        $statusLabel = 'Stage 2: Pending Property Recommendation';
    } else {
        $colorClass = 'status-pending-endorsement';
        $statusKey = 'pending_endorsement';
        $statusLabel = 'Stage 1: Pending Division Endorsement';
    }

    $facName = $row['facility_name'] ?: ($isGuesthouse ? 'Guesthouse Villa' : ($isDorm ? 'ATI Dormitory' : 'General Facility'));
    if (!empty($row['room_number'])) {
        $facName .= $isGuesthouse ? " - {$row['room_number']}" : " (Room {$row['room_number']})";
    }

    $timeSlot = !empty($row['time_slot']) ? $row['time_slot'] : '08:00 AM - 05:00 PM';
    $applicantName = $row['applicant_name'] ?: 'Official Applicant';
    $agency = $row['applicant_agency'] ?: 'Department of Agriculture';

    $venueTag = 'HALL';
    if ($isGuesthouse) {
        $venueTag = 'VILLA';
    } elseif ($isDorm) {
        $venueTag = 'DORM';
    } elseif (stripos($facName, 'boardroom') !== false) {
        $venueTag = 'BOARD';
    } elseif (stripos($facName, 'training') !== false) {
        $venueTag = 'TRAIN';
    } elseif (stripos($facName, 'mess') !== false) {
        $venueTag = 'MESS';
    }

    $calendarEvents[] = [
        'ref' => $row['reference_no'],
        'title' => !empty($row['event_title']) ? $row['event_title'] : 'Official Activity',
        'facility' => (string)$row['facility_id'],
        'facilityName' => $facName,
        'facilityType' => $row['facility_type'] ?? 'halls',
        'category' => $category,
        'isGuesthouse' => $isGuesthouse,
        'isDorm' => $isDorm,
        'startDate' => $row['start_date'],
        'endDate' => !empty($row['end_date']) ? $row['end_date'] : $row['start_date'],
        'time' => $timeSlot,
        'applicant' => $applicantName,
        'agency' => $agency,
        'email' => $row['applicant_email'] ?: '',
        'phone' => $row['applicant_phone'] ?: '',
        'pax' => (int)($row['pax_count'] ?? 0),
        'rawStatus' => $st,
        'status' => $statusKey,
        'statusLabel' => $statusLabel,
        'colorClass' => $colorClass,
        'venueTag' => $venueTag,
        'stage1Reviewer' => !empty($row['endorsed_by_name']) ? "Endorsed: {$row['endorsed_by_name']}" : 'Pending Endorsement',
        'stage2Reviewer' => !empty($row['recommended_by_name']) ? "Recommended: {$row['recommended_by_name']}" : 'Pending Recommendation',
        'stage3Reviewer' => !empty($row['cleared_by_name']) ? "Cleared: {$row['cleared_by_name']}" : 'Pending Directorate Clearance'
    ];
}

// 2. Process administrative blocks
foreach ($dbBlocks as $blk) {
    $calendarEvents[] = [
        'ref' => $blk['ref'],
        'title' => $blk['title'],
        'facility' => (string)$blk['facility_id'],
        'facilityName' => $blk['facility_name'] ?: 'All ATI Facilities (Blackout)',
        'facilityType' => 'all',
        'category' => 'all',
        'isGuesthouse' => false,
        'isDorm' => false,
        'startDate' => $blk['start_date'],
        'endDate' => !empty($blk['end_date']) ? $blk['end_date'] : $blk['start_date'],
        'time' => 'Full Day Administrative Hold',
        'applicant' => 'Executive Directorate',
        'agency' => 'Office of the Director / Admin Services',
        'email' => 'admin@ati.da.gov.ph',
        'phone' => 'Loc 101',
        'pax' => 0,
        'rawStatus' => 'Blackout',
        'status' => 'blackout',
        'statusLabel' => 'Administrative Hold (' . ($blk['reason'] ?: 'Hold') . ')',
        'colorClass' => 'status-blackout',
        'venueTag' => 'HOLD',
        'stage1Reviewer' => !empty($blk['memo_ref']) ? "Memo: {$blk['memo_ref']}" : 'Executive Administrative Order',
        'stage2Reviewer' => 'Logistics Hold Verification',
        'stage3Reviewer' => 'Official Calendar Blackout'
    ];
}

// 3. Process facilities with active maintenance hold
foreach ($facilitiesList as $fac) {
    if ($fac['status'] === 'maintenance') {
        $calendarEvents[] = [
            'ref' => 'MAINT-FAC-' . $fac['id'],
            'title' => 'Maintenance Hold: ' . $fac['name'],
            'facility' => (string)$fac['id'],
            'facilityName' => $fac['name'],
            'facilityType' => $fac['type'] ?? 'halls',
            'category' => ($fac['type'] === 'guesthouses') ? 'guesthouses' : (($fac['type'] === 'dorms' || $fac['type'] === 'dormitories') ? 'dormitories' : 'halls'),
            'isGuesthouse' => ($fac['type'] === 'guesthouses'),
            'isDorm' => ($fac['type'] === 'dorms' || $fac['type'] === 'dormitories'),
            'startDate' => date('Y-m-01'),
            'endDate' => !empty($fac['est_resume_date']) ? $fac['est_resume_date'] : date('Y-m-t'),
            'time' => 'Facility Under Maintenance',
            'applicant' => 'General Services Unit',
            'agency' => 'Administrative Services',
            'email' => 'gsu@ati.da.gov.ph',
            'phone' => 'Loc 104',
            'pax' => 0,
            'rawStatus' => 'Maintenance',
            'status' => 'blackout',
            'statusLabel' => 'Facility Maintenance Hold (' . ($fac['maintenance_reason'] ?: 'Scheduled Servicing') . ')',
            'colorClass' => 'status-blackout',
            'venueTag' => 'MAINT',
            'stage1Reviewer' => 'Facility Maintenance Directive',
            'stage2Reviewer' => 'General Services Unit Inspection',
            'stage3Reviewer' => 'Logistics Maintenance Authorization'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Master Schedule & Calendar Operations | ATI Reservation Portal</title>
  <meta name="description"
    content="Official Master Schedule and Calendar Operations Desk for Agricultural Training Institute Central Office.">

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
  <link rel="stylesheet" href="css/admin_schedule.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
</head>

<body class="admin-app-body" data-current-role="<?= htmlspecialchars($currentRole) ?>">

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
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Super Administrator &rsaquo; Calendar Operations</span>
            <h1 class="topbar-page-title">Master Schedule & Calendar Directorate</h1>
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
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
              </svg>
              <span>Institutional Master Calendar & Asset Occupancy</span>
            </div>
            <h2>Master Schedule & Calendar Operations Desk</h2>
            <p>Real-time institution-wide calendar across all training halls, executive boardrooms, dormitory wings, and guesthouses &amp; executive villas. Monitor confirmed bookings, review pending reservation collisions, and apply administrative date blocks or maintenance blackouts.</p>
          </div>

          <div class="admin-header-actions" style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
            <button type="button" class="btn-weather-control" onclick="openWeatherSuspensionModal()" style="background: linear-gradient(135deg, #174d2f 0%, #1a5c38 100%); color: #ffffff; border: 1px solid #0f3320; border-radius: 8px; padding: 0.6rem 1.1rem; font-weight: 700; font-size: 0.84rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 4px 14px rgba(23, 77, 47, 0.3); transition: all 0.2s ease;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                <path d="M19 16.9A5 5 0 0 0 18 7h-1.26a8 8 0 1 0-11.62 9"></path>
                <polyline points="13 11 9 17 15 17 11 23"></polyline>
              </svg>
              <span>Inclement Weather Suspension</span>
            </button>
            <button type="button" class="btn-system-primary" onclick="openDateBlockModal()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
              </svg>
              <span>+ Block Dates / Maintenance Hold</span>
            </button>
          </div>
        </section>

        <!-- ==========================================================================
             KPI STAT CARDS (MASTER SCHEDULE METRICS)
             ========================================================================== -->
        <section class="my-res-stats-grid admin-kpi-grid" aria-label="Schedule Statistics">
          <!-- Total Confirmed Events -->
          <div class="my-res-stat-card" onclick="filterCalendarByStatus('cleared', this)" style="cursor: pointer;" title="Show confirmed & cleared bookings (Click again to show all)">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiConfirmedMonth">0 Events</span>
                <span class="stat-trend positive">Fully Cleared</span>
              </div>
              <span class="stat-label">Confirmed this Month</span>
            </div>
          </div>

          <!-- Dual-Approval Queue in Calendar -->
          <div class="my-res-stat-card" onclick="filterCalendarByStatus('pending', this)" style="cursor: pointer;" title="Show tentative pending holds (Click again to show all)">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiPendingMonth">0 Pending</span>
                <span class="stat-trend neutral">Review Queue</span>
              </div>
              <span class="stat-label">Tentative Hold in Calendar</span>
            </div>
          </div>

          <!-- Monthly Facility Occupancy -->
          <div class="my-res-stat-card active-filter" onclick="filterCalendarByStatus('all', this)" style="cursor: pointer;" title="Show all calendar entries & occupancy rate">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiOccupancyRate">0%</span>
                <span class="stat-trend positive">Demand Index</span>
              </div>
              <span class="stat-label">Average Venue Occupancy</span>
            </div>
          </div>

          <!-- Open Booking Windows -->
          <div class="my-res-stat-card" id="cardOpenBookingDates" onclick="highlightOpenBookingDates(this)" style="cursor: pointer;" title="Highlight available open booking dates on the calendar">
            <div class="stat-icon-box purple">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiOpenDays">0 Days</span>
                <span class="stat-trend positive" id="kpiOpenTrend">Available</span>
              </div>
              <span class="stat-label">Open Booking Dates</span>
            </div>
          </div>

          <!-- Blackout & Maintenance Holds -->
          <div class="my-res-stat-card" style="cursor: pointer;" onclick="openManageHoldsModal()" title="View and manage active administrative date blocks">
            <div class="stat-icon-box red">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiActiveHoldsCount">0 Holds</span>
                <span class="stat-trend negative" style="cursor: pointer;">Manage & Lift &rsaquo;</span>
              </div>
              <span class="stat-label">Administrative Holds</span>
            </div>
          </div>
        </section>

        <!-- ==========================================================================
             CALENDAR TOOLBAR & MONTH NAVIGATOR
             ========================================================================== -->
        <div class="admin-calendar-toolbar">
          <div class="month-nav-group">
            <button type="button" class="nav-arrow-btn" onclick="changeMonth(-1)" aria-label="Previous Month">&lsaquo;</button>
            <span class="current-month-display" id="currentMonthDisplay">October 2026</span>
            <button type="button" class="nav-arrow-btn" onclick="changeMonth(1)" aria-label="Next Month">&rsaquo;</button>
            <button type="button" class="btn-today-pill" onclick="goToToday()">Today</button>
          </div>

          <!-- Status Legend Bar (Aligned with 3-Stage Governance Workflow) -->
          <div class="status-legend-bar">
            <div class="legend-item" title="Directorate cleared & confirmed schedule">
              <span class="legend-dot green"></span>
              <span>Confirmed &amp; Cleared</span>
            </div>
            <div class="legend-item" title="Recommended by Logistics, awaiting Directorate clearance">
              <span class="legend-dot teal"></span>
              <span>Stage 3: Pending Clearance</span>
            </div>
            <div class="legend-item" title="Endorsed by Division, awaiting Logistics recommendation">
              <span class="legend-dot blue"></span>
              <span>Stage 2: Pending Recommendation</span>
            </div>
            <div class="legend-item" title="Initial submission, awaiting Division Chief endorsement">
              <span class="legend-dot orange"></span>
              <span>Stage 1: Pending Endorsement</span>
            </div>
            <div class="legend-item" title="Executive administrative hold or facility maintenance">
              <span class="legend-dot red"></span>
              <span>Administrative Hold</span>
            </div>
          </div>
        </div>

        <!-- Primary Category Filter Segmented Tabs -->
        <div class="category-filter-segmented" role="tablist" aria-label="Facility Category Filter">
          <button type="button" class="cat-seg-btn active" onclick="filterCalendarByCategory('all', this)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>All Categories (<?= count($facilitiesList) ?>)</span>
          </button>
          <button type="button" class="cat-seg-btn" onclick="filterCalendarByCategory('halls', this)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            </svg>
            <span>Training &amp; Function Halls (<?= count($hallsList) ?>)</span>
          </button>
          <button type="button" class="cat-seg-btn dorm-seg-btn" onclick="filterCalendarByCategory('dormitories', this)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M2 4v16"></path>
              <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
              <path d="M2 17h20"></path>
              <path d="M6 8v9"></path>
            </svg>
            <span>Dormitories &amp; Lodgings (<?= count($dormsList) ?>)</span>
          </button>
          <button type="button" class="cat-seg-btn guesthouse-seg-btn" onclick="filterCalendarByCategory('guesthouses', this)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Guesthouses &amp; Villas (<?= count($guesthousesList) ?>)</span>
          </button>
        </div>

        <!-- Facility Filter Pills Row -->
        <div class="admin-calendar-filter-row">
          <div class="facility-filter-pills" id="facilityFilterPills">
            <button type="button" class="fac-pill active" data-category="all" onclick="filterCalendarByFacility('all', this)">All Facilities</button>
            <?php foreach ($facilitiesList as $f): 
              $fType = strtolower($f['type'] ?? '');
              $catClass = ($fType === 'guesthouses') ? 'guesthouses' : (($fType === 'dormitories' || $fType === 'dorms') ? 'dormitories' : 'halls');
              $bulletClass = ($fType === 'guesthouses') ? 'guesthouse' : (($fType === 'dormitories' || $fType === 'dorms') ? 'dorm' : 'hall');
            ?>
              <button type="button" class="fac-pill" data-category="<?= htmlspecialchars($catClass) ?>" onclick="filterCalendarByFacility('<?= htmlspecialchars($f['id']) ?>', this)">
                <span class="cat-pill-bullet <?= $bulletClass ?>"></span>
                <span><?= htmlspecialchars($f['name']) ?></span>
              </button>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ==========================================================================
             MASTER CALENDAR BOARD (INTERACTIVE GRID)
             ========================================================================== -->
        <div class="admin-calendar-board">
          <!-- Weekday Headers -->
          <div class="board-weekdays-row">
            <div class="weekday-header-cell">Sun</div>
            <div class="weekday-header-cell">Mon</div>
            <div class="weekday-header-cell">Tue</div>
            <div class="weekday-header-cell">Wed</div>
            <div class="weekday-header-cell">Thu</div>
            <div class="weekday-header-cell">Fri</div>
            <div class="weekday-header-cell">Sat</div>
          </div>

          <!-- Dynamic Days Grid -->
          <div class="board-days-grid" id="boardDaysGrid"></div>
        </div>

      </main>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: EVENT DETAILS & APPROVALS LINK / LIFT BLOCK
       ========================================================================== -->
  <div class="admin-modal-overlay" id="adminEventDetailsModal" style="display: none;">
    <div class="admin-modal-card">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <span class="modal-badge-ref" id="modalEventRef">R-2026-0891</span>
          <h4 id="modalEventTitle">National TOT on Climate-Resilient Agriculture</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeAdminEventDetailsModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <div class="modal-applicant-summary" style="margin-bottom: 1rem;">
          <div class="user-avatar-circle" id="modalEventAvatarCircle" style="background: #174d2f; color: #fff;">EV</div>
          <div class="applicant-meta">
            <h5 id="modalEventAgency">Career Development Division (CDD)</h5>
            <p id="modalEventFacility">Serrano Function Hall</p>
            <span class="applicant-contact" id="modalEventAttendees">85 Participants</span>
          </div>
        </div>

        <div class="modal-section-grid">
          <div class="modal-data-box">
            <label>Scheduled Dates</label>
            <span id="modalEventDate">Oct 12 - 14, 2026</span>
          </div>
          <div class="modal-data-box">
            <label>Time Window</label>
            <span id="modalEventTime">08:00 AM - 05:00 PM</span>
          </div>
        </div>

        <div class="modal-data-box" style="margin-top: 0.85rem;">
          <label>Operational Review Status</label>
          <div style="font-weight: 700; color: #153321; font-size: 0.9rem;" id="modalEventStatus">Stage 1 Pending Recommendation</div>
          <small style="color: #557262; font-size: 0.78rem; display: block; margin-top: 0.25rem;" id="modalEventReviewers">Recommending Authority | Pending Stage 1 Review</small>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeAdminEventDetailsModal()">Close</button>
        <!-- Danger button for lifting date blocks -->
        <button type="button" class="btn-confirm-decline" id="btnModalLiftBlock" style="display: none;" onclick="confirmLiftCurrentBlock()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
          </svg>
          <span>Lift / Remove Date Block</span>
        </button>
        <a href="admin_reservations.php" class="btn-system-primary" id="btnModalOpenDesk">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
          <span>Open in Approvals Desk</span>
        </a>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: MANAGE & LIFT ACTIVE HOLDS LIST
       ========================================================================== -->
  <div class="admin-modal-overlay" id="manageHoldsModal" style="display: none;">
    <div class="admin-modal-card">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4 style="color: #991b1b;">Active Administrative Date Blocks & Holds</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeManageHoldsModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <p style="font-size: 0.88rem; color: #4e6758; margin-bottom: 1rem;">
          The following dates are currently blocked from public reservation. Click <strong>"Lift Block"</strong> on any hold below to immediately release the dates back to available on the master calendar.
        </p>

        <div id="activeHoldsListContainer" style="display: flex; flex-direction: column; gap: 0.85rem; max-height: 380px; overflow-y: auto;">
          <!-- Dynamically populated by JS -->
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: ADMIN DATE BLOCK / BLACKOUT HOLD
       ========================================================================== -->
  <div class="admin-modal-overlay" id="adminDateBlockModal" style="display: none;">
    <div class="admin-modal-card sm">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4 style="color: #991b1b;">Apply Administrative Date Blackout / Hold</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeDateBlockModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <div class="blackout-modal-warning">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <p>
            Placing an administrative blackout hold will mark selected calendar dates as unavailable for public reservations across the portal.
          </p>
        </div>

        <form id="dateBlockForm" onsubmit="handleSaveDateBlock(event)">
          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Event Title / Blackout Description</label>
            <input type="text" id="blockTitle" placeholder="e.g. National Agriculture Summit Preparation" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Target Facility / Scope</label>
            <select id="blockFacilitySelect" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
              <option value="all">All ATI Facilities (Institution-Wide Blackout)</option>
              <?php if (!empty($hallsList)): ?>
                <optgroup label="Training &amp; Function Halls">
                  <?php foreach ($hallsList as $f): ?>
                    <option value="<?= htmlspecialchars($f['id']) ?>"><?= htmlspecialchars($f['name']) ?></option>
                  <?php endforeach; ?>
                </optgroup>
              <?php endif; ?>
              <?php if (!empty($dormsList)): ?>
                <optgroup label="Dormitories &amp; Lodgings">
                  <?php foreach ($dormsList as $f): ?>
                    <option value="<?= htmlspecialchars($f['id']) ?>"><?= htmlspecialchars($f['name']) ?></option>
                  <?php endforeach; ?>
                </optgroup>
              <?php endif; ?>
              <?php if (!empty($guesthousesList)): ?>
                <optgroup label="Guesthouses &amp; Executive Villas">
                  <?php foreach ($guesthousesList as $f): ?>
                    <option value="<?= htmlspecialchars($f['id']) ?>"><?= htmlspecialchars($f['name']) ?></option>
                  <?php endforeach; ?>
                </optgroup>
              <?php endif; ?>
            </select>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Start Date</label>
              <input type="date" id="blockStartDate" min="<?= date('Y-m-d') ?>" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">End Date</label>
              <input type="date" id="blockEndDate" min="<?= date('Y-m-d') ?>" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Official Authority / Ground</label>
            <select id="blockReasonSelect" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
              <option value="VIP Institutional State Activity">VIP Institutional State Activity</option>
              <option value="Department of Agriculture National Event">Department of Agriculture National Event</option>
              <option value="Periodic Facility Maintenance & Cleaning">Periodic Facility Maintenance & Cleaning</option>
              <option value="Official Public Holiday / Skeletal Workforce">Official Public Holiday / Skeletal Workforce</option>
            </select>
          </div>

          <div class="form-group-decline" style="margin-bottom: 1.25rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Official Memo Circular Ref # (Optional)</label>
            <input type="text" id="blockMemoRef" placeholder="e.g. ATI-MC-2026-08" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
          </div>

          <div class="admin-modal-footer" style="padding: 1rem 0 0 0; background: none; border-top: 1px solid #edf3ef;">
            <button type="button" class="btn-system-secondary" onclick="closeDateBlockModal()">Cancel</button>
            <button type="submit" class="btn-confirm-decline">Apply Calendar Blackout</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- Inclement Weather Suspension Modal Component -->
  <?php include __DIR__ . '/includes/admin_weather_modal.php'; ?>

  <!-- Scripts -->
  <script>
    const initialDbCalendarEvents = <?= json_encode($calendarEvents, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  </script>
  <script src="js/admin_schedule.js?v=<?php echo time(); ?>"></script>
</body>

</html>

