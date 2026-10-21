<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/includes/facility_helpers.php';
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Super Administrator: Facilities & Dormitories Master Management Portal
 */
$currentRole = isset($_GET['role']) && $_GET['role'] === 'recommendation' ? 'recommendation' : 'clearance';
$isRecommendation = ($currentRole === 'recommendation');

$db = (new Database())->getConnection();

// Dynamic sidebar counts
$pendingStmt = $db->query("SELECT COUNT(*) FROM reservations WHERE status IN ('Pending', 'Awaiting Endorsement', 'Awaiting Recommendation', 'Awaiting Clearance')");
$pendingCount = (int)$pendingStmt->fetchColumn();

$userCountStmt = $db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
$totalUsersCount = (int)$userCountStmt->fetchColumn();

// Fetch all facilities from DB
$stmt = $db->query("SELECT * FROM facilities ORDER BY id ASC");
$facilitiesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$facilitiesData = [];
$totalAssets = count($facilitiesList);
$availableCount = 0;
$occupiedCount = 0;
$maintenanceCount = 0;
$totalPax = 0;
$totalBeds = 0;

// Active reservations today for occupied status and occupant information
$today = date('Y-m-d');
$occStmt = $db->prepare("SELECT r.id AS reservation_id,
                                r.facility_id,
                                r.room_id,
                                r.reference_no,
                                r.event_title,
                                r.start_date,
                                r.end_date,
                                r.time_slot,
                                r.pax_count,
                                r.status,
                                rm.room_number,
                                rm.floor AS room_floor,
                                u.full_name AS client_name,
                                u.contact_number AS client_contact,
                                u.email AS client_email,
                                u.office_agency AS client_agency
                         FROM reservations r
                         LEFT JOIN rooms rm ON r.room_id = rm.id
                         LEFT JOIN users u ON r.user_id = u.id
                         WHERE r.status = 'Approved'
                           AND r.start_date <= ? AND r.end_date >= ?
                           AND r.deleted_at IS NULL
                         ORDER BY r.id DESC");
$occStmt->execute([$today, $today]);
$occupiedRaw = $occStmt->fetchAll(PDO::FETCH_ASSOC);

$occupiedFacilityMap = [];
foreach ($occupiedRaw as $occItem) {
    $fidKey = (int)$occItem['facility_id'];
    if (!isset($occupiedFacilityMap[$fidKey])) {
        $occupiedFacilityMap[$fidKey] = [];
    }
    $occupiedFacilityMap[$fidKey][] = $occItem;
}

foreach ($facilitiesList as $row) {
    $fid = (string)$row['id'];
    $fidInt = (int)$row['id'];
    $isArchived = !empty($row['deleted_at']);
    $isMaint = ($row['status'] === 'maintenance');
    $facilityOccupants = $occupiedFacilityMap[$fidInt] ?? [];
    $isOccupied = !empty($facilityOccupants);
    $primaryOccupant = $isOccupied ? $facilityOccupants[0] : null;

    $status = 'available';
    $statusLabel = 'Available';
    if ($isArchived) {
        $status = 'archived';
        $statusLabel = 'Archived / Inactive';
    } elseif ($isMaint) {
        $status = 'maintenance';
        $statusLabel = 'Under Maintenance';
        $maintenanceCount++;
    } elseif ($isOccupied) {
        $status = 'occupied';
        $statusLabel = 'Occupied (Active Today)';
        $occupiedCount++;
    } else {
        $availableCount++;
    }

    $pax = (int)preg_replace('/[^0-9]/', '', $row['capacity'] ?? '0');
    if ($pax === 0 && is_numeric($row['capacity'] ?? null)) {
        $pax = (int)$row['capacity'];
    }
    $beds = (int)($row['beds'] ?? 0);
    if (!$isArchived) {
        $totalPax += $pax;
        $totalBeds += $beds;
    }

    $featuresArray = array_filter(array_map('trim', explode(',', $row['features'] ?? '')));
    if (empty($featuresArray)) {
        $featuresArray = ['Airconditioned', 'Standard Equipment'];
    }

    $categoryLabel = 'Training & Function Hall';
    if ($row['type'] === 'dorms') {
        $categoryLabel = 'Dormitory Suite';
    } elseif ($row['type'] === 'dining') {
        $categoryLabel = 'Dining Facility';
    }

    $facilitiesData[$fid] = [
        'id' => $fid,
        'db_id' => (int)$row['id'],
        'name' => $row['name'],
        'category' => $row['type'],
        'categoryLabel' => $categoryLabel,
        'pax' => $pax,
        'beds' => $beds,
        'cooling_type' => !empty($row['cooling_type']) ? $row['cooling_type'] : ($row['type'] === 'dorms' ? 'Fan Cooled (Non-Aircon)' : 'Full Climate Control'),
        'rate' => $row['rate'] ?? '',
        'location' => $row['location'] ?? 'ATI Central Office',
        'image' => !empty($row['image']) ? $row['image'] : 'assets/images/function_hall.jpg',
        'features' => array_values($featuresArray),
        'status' => $status,
        'statusLabel' => $statusLabel,
        'maintenance' => $isMaint,
        'maintenanceReason' => $row['maintenance_reason'] ?? '',
        'estResumeDate' => $row['est_resume_date'] ?? '',
        'notes' => $row['notes'] ?? $row['description'] ?? '',
        'layouts' => $row['layouts'] ?? '',
        'gallery' => getFacilityGallery($row),
        'isArchived' => $isArchived,
        'deletedAt' => $isArchived ? date('M d, Y • h:i A', strtotime($row['deleted_at'])) : null,
        'isOccupied' => $isOccupied,
        'occupants' => $facilityOccupants,
        'primaryOccupant' => $primaryOccupant
    ];
}

$totalActiveAssets = count(array_filter($facilitiesData, fn($f) => !$f['isArchived']));
$archivedCount = count(array_filter($facilitiesData, fn($f) => $f['isArchived']));
$hallsCount = count(array_filter($facilitiesData, fn($f) => $f['category'] === 'halls' && !$f['isArchived']));
$dormsCount = count(array_filter($facilitiesData, fn($f) => $f['category'] === 'dorms' && !$f['isArchived']));
$diningCount = count(array_filter($facilitiesData, fn($f) => $f['category'] === 'dining' && !$f['isArchived']));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facilities & Dormitories Registry | ATI Reservation Portal</title>
  <meta name="description"
    content="Official Facilities and Dormitory Master Registry and Asset Management for Agricultural Training Institute Central Office.">

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
  <link rel="stylesheet" href="css/facility_gallery.css?v=<?php echo time(); ?>">
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
            <span class="topbar-breadcrumb">ATI Portal &rsaquo; Super Administrator &rsaquo; Asset Management</span>
            <h1 class="topbar-page-title">Facilities & Dormitories Master Registry</h1>
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
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
              <span>Institutional Assets & Accommodation Control</span>
            </div>
            <h2>Facility & Dormitory Asset Management</h2>
            <p>Configure hall capacities, manage dormitory bed inventories, inspect real-time operational availability, and implement administrative maintenance holds across ATI Central Office.</p>
          </div>

          <div class="admin-header-actions">
            <button type="button" class="btn-system-primary" onclick="openAddFacilityModal()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              <span>+ Register New Venue / Room</span>
            </button>
          </div>
        </section>

        <!-- ==========================================================================
             KPI STAT CARDS (FACILITY & DORM ASSET METRICS)
             ========================================================================== -->
        <section class="my-res-stats-grid admin-kpi-grid" aria-label="Facility Statistics">
          <!-- Total Monitored Facilities -->
          <div class="my-res-stat-card active-filter" onclick="filterFacilitiesByCategory('all')">
            <div class="stat-icon-box green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiTotalFacilities"><?= $totalActiveAssets ?></span>
                <span class="stat-trend positive"><?= $archivedCount > 0 ? "{$archivedCount} In Archive" : "100% Operational" ?></span>
              </div>
              <span class="stat-label">Active Monitored Assets</span>
            </div>
          </div>

          <!-- Ready / Available -->
          <div class="my-res-stat-card" onclick="filterFacilitiesByCategory('all')">
            <div class="stat-icon-box blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiAvailableFacilities"><?= $availableCount ?></span>
                <span class="stat-trend positive">Ready to Book</span>
              </div>
              <span class="stat-label">Available for Reservations</span>
            </div>
          </div>

          <!-- Occupied / In Session -->
          <div class="my-res-stat-card" onclick="filterFacilitiesByCategory('all')">
            <div class="stat-icon-box amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiOccupiedFacilities"><?= $occupiedCount ?></span>
                <span class="stat-trend neutral"><?= $occupiedCount > 0 ? 'Active Session' : 'No Active Session' ?></span>
              </div>
              <span class="stat-label">Currently Occupied</span>
            </div>
          </div>

          <!-- Maintenance Holds -->
          <div class="my-res-stat-card" onclick="filterFacilitiesByCategory('maintenance')">
            <div class="stat-icon-box red">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiMaintenanceFacilities"><?= $maintenanceCount ?></span>
                <span class="stat-trend negative">Admin Hold</span>
              </div>
              <span class="stat-label">Under Maintenance</span>
            </div>
          </div>

          <!-- Total Accommodation Capacity -->
          <div class="my-res-stat-card">
            <div class="stat-icon-box gold">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value-row">
                <span class="stat-value" id="kpiTotalCapacity" style="font-size: 1.15rem;"><?= $totalPax ?> PAX<?= $totalBeds > 0 ? " / {$totalBeds} Beds" : "" ?></span>
              </div>
              <span class="stat-label">Total Institution Capacity</span>
            </div>
          </div>
        </section>

        <!-- ==========================================================================
             ASSET FILTER & SEARCH CONTROLS
             ========================================================================== -->
        <div class="facility-mgmt-toolbar">
          <div class="facility-search-wrapper">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="facilitySearchInput" placeholder="Filter by venue name, building, equipment, pax..." oninput="handleFacilitySearch(this.value)">
          </div>

          <div class="facility-toolbar-actions">
            <div class="facility-filter-group">
              <button type="button" class="facility-filter-tab active" data-category="all" onclick="filterFacilitiesByCategory('all', this)">All Assets (<?= $totalActiveAssets ?>)</button>
              <button type="button" class="facility-filter-tab" data-category="halls" onclick="filterFacilitiesByCategory('halls', this)">Training &amp; Function Halls (<?= $hallsCount ?>)</button>
              <button type="button" class="facility-filter-tab" data-category="dorms" onclick="filterFacilitiesByCategory('dorms', this)">Dormitory Suites (<?= $dormsCount ?>)</button>
              <button type="button" class="facility-filter-tab" data-category="dining" onclick="filterFacilitiesByCategory('dining', this)">Dining Pavilions (<?= $diningCount ?>)</button>
              <button type="button" class="facility-filter-tab" data-category="maintenance" onclick="filterFacilitiesByCategory('maintenance', this)">Maintenance Holds (<?= $maintenanceCount ?>)</button>
              <button type="button" class="facility-filter-tab" data-category="archived" onclick="filterFacilitiesByCategory('archived', this)" style="<?= $archivedCount > 0 ? 'color: #b91c1c; font-weight: 700;' : '' ?>">Trash / Archive (<?= $archivedCount ?>)</button>
            </div>

            <!-- View Mode Switcher -->
            <div class="view-mode-toggle" title="Switch Display Layout">
              <button type="button" class="view-mode-btn active" id="btnViewGrid" onclick="setViewMode('grid')">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Grid</span>
              </button>
              <button type="button" class="view-mode-btn" id="btnViewTable" onclick="setViewMode('table')">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                <span>Table</span>
              </button>
            </div>
          </div>
        </div>

        <!-- ==========================================================================
             ASSET CARDS GRID (DEFAULT VIEW)
             ========================================================================== -->
        <div class="facility-cards-grid" id="facilityCardsGrid">

          <?php if (empty($facilitiesData)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; background: #fff; border-radius: 16px; border: 1.5px dashed #c8dcd0;">
              <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 50%; background: #eef7f2; margin-bottom: 0.85rem;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="8" x2="12" y2="12"></line>
                  <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
              </div>
              <h4 style="font-size: 1.15rem; font-weight: 700; color: #172c1f; margin: 0 0 0.4rem 0;">No facilities found</h4>
              <p style="color: #637f6f; font-size: 0.9rem; margin: 0 0 1.25rem 0;">Click "+ Register New Venue / Room" above to register your first institutional facility.</p>
              <button type="button" class="btn-create-facility" onclick="openNewFacilityModal()">+ Register New Venue / Room</button>
            </div>
          <?php else: ?>
            <?php foreach ($facilitiesData as $fid => $item): 
              $isMaint = $item['maintenance'];
              $isArchived = $item['isArchived'];
              $statusClass = $isArchived ? 'state-archived' : ($item['status'] === 'maintenance' ? 'state-maintenance' : ($item['status'] === 'occupied' ? 'state-occupied' : 'state-available'));
              $typeBadge = $item['category'] === 'dorms' ? 'Dormitory Suite' : ($item['category'] === 'dining' ? 'Dining Pavilion' : 'Function & Training Hall');
              $capDisplay = $item['pax'] . ' Participants';
              if ($item['beds'] > 0) {
                $capDisplay = $item['pax'] . ' PAX (' . $item['beds'] . ' Beds)';
              }
            ?>
            <div class="facility-card-item <?= $isArchived ? 'facility-card-archived' : '' ?>" data-id="<?= htmlspecialchars($fid) ?>" data-category="<?= htmlspecialchars($item['category']) ?>" data-maintenance="<?= $isMaint ? 'true' : 'false' ?>" data-archived="<?= $isArchived ? 'true' : 'false' ?>">
              <div class="facility-card-media">
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="facility-card-img" onerror="this.src='assets/images/function_hall.jpg'">
                <span class="facility-type-badge"><?= htmlspecialchars($typeBadge) ?></span>
                <span class="facility-status-pill-overlay <?= $statusClass ?>"><?= htmlspecialchars($item['statusLabel']) ?></span>
              </div>
              <div class="facility-card-body">
                <div class="facility-card-header-row">
                  <h3 class="facility-card-title"><?= htmlspecialchars($item['name']) ?></h3>
                </div>
                <div class="facility-location-tag">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                  <span><?= htmlspecialchars($item['location']) ?></span>
                </div>
                <div class="facility-specs-grid">
                  <div class="spec-cell-item">
                    <span class="spec-label">Capacity</span>
                    <strong class="spec-val spec-capacity-val"><?= htmlspecialchars($capDisplay) ?></strong>
                  </div>
                  <div class="spec-cell-item">
                    <span class="spec-label">Standard Rate</span>
                    <strong class="spec-val spec-rate-val"><?= htmlspecialchars(!empty($item['rate']) ? $item['rate'] : 'Institutional') ?></strong>
                  </div>
                </div>

                <?php if ($item['status'] === 'occupied' && !empty($item['primaryOccupant'])): 
                  $occ = $item['primaryOccupant'];
                  $occCount = count($item['occupants']);
                  $occPayloadJson = htmlspecialchars(json_encode([
                    'facilityName' => $item['name'],
                    'facilityType' => $item['categoryLabel'],
                    'occupants' => $item['occupants'],
                    'primary' => $occ
                  ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');
                ?>
                  <div class="facility-occupant-preview-card" onclick="openOccupantModal(<?= $occPayloadJson ?>)" title="Click to view full occupant &amp; reservation details">
                    <div class="f-occ-header">
                      <span class="f-occ-pulse-dot"></span>
                      <span class="f-occ-status-text"><?= $item['category'] === 'dorms' ? ($occCount > 1 ? "{$occCount} Rooms Occupied Today" : "Room {$occ['room_number']} Occupied") : "Active Event / In Session" ?></span>
                      <span class="f-occ-ref">#<?= htmlspecialchars($occ['reference_no']) ?></span>
                    </div>
                    <div class="f-occ-event-title" title="<?= htmlspecialchars($occ['event_title']) ?>">
                      <?= htmlspecialchars($occ['event_title']) ?>
                    </div>
                    <div class="f-occ-client-row">
                      <span class="f-occ-client-name" title="<?= htmlspecialchars($occ['client_name']) ?>">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <strong><?= htmlspecialchars($occ['client_name']) ?></strong>
                      </span>
                      <?php if (!empty($occ['client_contact'])): ?>
                        <span class="f-occ-client-phone" title="Contact Number">
                          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                          <?= htmlspecialchars($occ['client_contact']) ?>
                        </span>
                      <?php elseif (!empty($occ['client_agency'])): ?>
                        <span class="f-occ-client-agency">
                          <?= htmlspecialchars($occ['client_agency']) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                    <?php if ($item['category'] !== 'dorms' && (!empty($occ['time_slot']) || !empty($occ['pax_count']))): ?>
                      <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.35rem; font-size: 0.72rem; color: #475569;">
                        <?php if (!empty($occ['time_slot'])): ?>
                          <span style="background: #fef3c7; color: #92400e; padding: 2px 7px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 3px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <?= htmlspecialchars($occ['time_slot']) ?>
                          </span>
                        <?php endif; ?>
                        <?php if (!empty($occ['pax_count'])): ?>
                          <span style="background: #ecfdf5; color: #065f46; padding: 2px 7px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 3px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <?= htmlspecialchars($occ['pax_count']) ?> PAX
                          </span>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                    <div class="f-occ-click-hint">
                      <span>View Occupant Details</span>
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                  </div>
                <?php endif; ?>

                <div class="facility-features-wrap">
                  <?php foreach (array_slice($item['features'], 0, 5) as $feat): ?>
                    <span class="feature-pill-tag"><?= htmlspecialchars($feat) ?></span>
                  <?php endforeach; ?>
                </div>
                <div class="facility-card-footer">
                  <?php if ($isArchived): ?>
                    <div class="facility-card-footer-top">
                      <div class="facility-maintenance-quick-toggle">
                        <span style="font-size: 0.76rem; color: #b91c1c; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                          Archived Asset
                        </span>
                      </div>
                      <div style="font-size: 0.72rem; color: #718096; font-weight: 600;">
                        <?= htmlspecialchars($item['deletedAt']) ?>
                      </div>
                    </div>
                    <div class="card-action-btns">
                      <button type="button" class="btn-card-action btn-restore-facility" onclick="confirmRestoreFacility('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')" title="Restore Facility to Active Catalog">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                        <span>Restore</span>
                      </button>
                      <button type="button" class="btn-card-action btn-purge-facility" onclick="confirmPermanentDeleteFacility('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')" title="Permanently Delete Facility from Database">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        <span>Purge</span>
                      </button>
                    </div>
                  <?php else: ?>
                    <div class="facility-card-footer-top">
                      <div class="facility-maintenance-quick-toggle">
                        <label class="switch-toggle" title="Toggle Maintenance Mode">
                          <input type="checkbox" <?= $isMaint ? 'checked' : '' ?> onchange="onMaintenanceSwitchChange('<?= htmlspecialchars($fid) ?>', this.checked)">
                          <span class="slider-round"></span>
                        </label>
                        <span class="maintenance-label">Maintenance Mode</span>
                      </div>
                    </div>
                    <div class="card-action-btns">
                      <?php
                        $galModalPayload = [
                          'id' => $fid,
                          'dbId' => $item['db_id'],
                          'name' => $item['name'],
                          'type' => $item['category'],
                          'isDorm' => ($item['category'] === 'dorms'),
                          'rate' => $item['rate'],
                          'capacity' => $capDisplay,
                          'beds' => $item['beds'],
                          'cooling_type' => $item['cooling_type'],
                          'location' => $item['location'],
                          'description' => $item['notes'],
                          'features' => $item['features'],
                          'image' => $item['image'],
                          'photos' => $item['gallery']
                        ];
                        $jsonGalPayload = htmlspecialchars(json_encode($galModalPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');
                      ?>
                      <button type="button" class="btn-card-action" onclick="openFacilityGalleryModal(<?= $jsonGalPayload ?>)" title="Preview Client Showcase &amp; Multi-Photo Gallery">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <span>Photos (<?= count($item['gallery']) ?>)</span>
                      </button>
                      <?php if ($item['category'] === 'dorms'): ?>
                        <button type="button" class="btn-card-action btn-manage-rooms" onclick="openManageRoomsModal('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')" title="Manage Individual Dormitory Rooms">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                          <span>Rooms</span>
                        </button>
                      <?php else: ?>
                        <button type="button" class="btn-card-action btn-manage-setups" onclick="openManageSetupsModal('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')" title="Manage Venue Setups & Floor Plans">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M3 9h18M9 21V9"></path></svg>
                          <span>Setups</span>
                        </button>
                      <?php endif; ?>
                      <button type="button" class="btn-card-action" onclick="openEditFacilityModal('<?= htmlspecialchars($fid) ?>')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Edit</span>
                      </button>
                      <button type="button" class="btn-card-action btn-delete-facility" onclick="confirmDeleteFacility('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')" title="Move to Trash / Archive">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                      </button>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- ==========================================================================
             FACILITY TABLE VIEW (ALTERNATIVE VIEW)
             ========================================================================== -->
        <div class="facility-table-view-container" id="facilityTableView">
          <div class="admin-table-wrapper">
            <table class="admin-table" id="facilityMasterTable">
              <thead>
                <tr>
                  <th>Facility / Venue Name</th>
                  <th>Category</th>
                  <th>Capacity</th>
                  <th>Featured Amenities</th>
                  <th>Maintenance Hold</th>
                  <th>Status</th>
                  <th style="text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody id="facilityTableBody">
                <?php if (empty($facilitiesData)): ?>
                  <tr>
                    <td colspan="7" style="text-align: center; padding: 3.5rem 1rem; color: #637f6f;">
                      <div style="display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px; border-radius: 50%; background: #eef7f2; margin-bottom: 0.75rem;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10"></circle>
                          <line x1="12" y1="8" x2="12" y2="12"></line>
                          <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                      </div>
                      <h4 style="font-size: 1.05rem; font-weight: 700; color: #172c1f; margin: 0 0 0.35rem 0;">No facilities found</h4>
                      <p style="color: #637f6f; font-size: 0.85rem; margin: 0;">There are currently no facilities matching this view.</p>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($facilitiesData as $fid => $item): ?>
                    <tr data-id="<?= htmlspecialchars($fid) ?>" data-category="<?= htmlspecialchars($item['category']) ?>" data-maintenance="<?= $item['maintenance'] ? 'true' : 'false' ?>" data-archived="<?= $item['isArchived'] ? 'true' : 'false' ?>" class="<?= $item['isArchived'] ? 'row-archived' : '' ?>">
                      <td>
                        <div class="facility-table-info-cell">
                          <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="facility-table-thumb" onerror="this.src='assets/images/function_hall.jpg'">
                          <div class="facility-table-info-text">
                            <strong><?= htmlspecialchars($item['name']) ?></strong>
                            <span><?= htmlspecialchars($item['location']) ?></span>
                            <?php if ($item['isArchived']): ?>
                              <span style="font-size: 0.72rem; color: #dc2626; font-weight: 700;">Archived on <?= htmlspecialchars($item['deletedAt']) ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="event-type-chip <?= $item['category'] === 'dorms' ? 'schedule' : ($item['category'] === 'dining' ? 'maintenance' : 'approval') ?>">
                          <?= htmlspecialchars(strtoupper($item['category'])) ?>
                        </span>
                      </td>
                      <td>
                        <strong><?= $item['pax'] ?> PAX</strong>
                        <?php if ($item['beds'] > 0): ?>
                          <br><small style="color: #637f6f;"><?= $item['beds'] ?> Beds</small>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="facility-features-wrap" style="margin: 0;">
                          <?php foreach (array_slice($item['features'], 0, 3) as $f): ?>
                            <span class="feature-pill-tag"><?= htmlspecialchars($f) ?></span>
                          <?php endforeach; ?>
                        </div>
                      </td>
                      <td>
                        <?php if ($item['isArchived']): ?>
                          <span style="font-size: 0.78rem; color: #64748b;">Archived</span>
                        <?php else: ?>
                          <label class="switch-toggle">
                            <input type="checkbox" <?= $item['maintenance'] ? 'checked' : '' ?> onchange="onMaintenanceSwitchChange('<?= htmlspecialchars($fid) ?>', this.checked)">
                            <span class="slider-round"></span>
                          </label>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($item['isArchived']): ?>
                          <span class="status-badge-chip chip-declined">Archived (Trash)</span>
                        <?php elseif ($item['status'] === 'available'): ?>
                          <span class="status-badge-chip chip-confirmed">Available</span>
                        <?php elseif ($item['status'] === 'occupied'): 
                          $occ = $item['primaryOccupant'] ?? [];
                          $occPayloadJson = htmlspecialchars(json_encode([
                            'facilityName' => $item['name'],
                            'facilityType' => $item['categoryLabel'],
                            'occupants' => $item['occupants'],
                            'primary' => $occ
                          ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');
                        ?>
                          <div class="facility-table-occ-wrap" onclick="openOccupantModal(<?= $occPayloadJson ?>)" title="Click to view occupant details">
                            <span class="status-badge-chip chip-pending" style="display: inline-flex; align-items: center; gap: 4px;">
                              <span class="f-occ-pulse-dot" style="width: 6px; height: 6px;"></span>
                              Occupied
                            </span>
                            <?php if (!empty($occ)): ?>
                              <div style="font-size: 0.74rem; color: #172c1f; font-weight: 700; margin-top: 3px; max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($occ['client_name']) ?>">
                                <?= htmlspecialchars($occ['client_name']) ?>
                              </div>
                              <div style="font-size: 0.68rem; color: #637f6f; max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($occ['event_title']) ?>">
                                <?= htmlspecialchars($occ['event_title']) ?>
                              </div>
                            <?php endif; ?>
                          </div>
                        <?php else: ?>
                          <span class="status-badge-chip chip-declined">Maintenance Hold</span>
                        <?php endif; ?>
                      </td>
                      <td style="text-align: right; white-space: nowrap;">
                        <?php if ($item['isArchived']): ?>
                          <button type="button" class="btn-card-action btn-restore-facility" onclick="confirmRestoreFacility('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')" title="Restore Facility to Active Catalog">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                            <span>Restore</span>
                          </button>
                          <button type="button" class="btn-card-action btn-purge-facility" onclick="confirmPermanentDeleteFacility('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')" title="Permanently Delete Facility from Database">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            <span>Purge</span>
                          </button>
                        <?php else: ?>
                          <?php if ($item['category'] === 'dorms'): ?>
                            <button type="button" class="btn-card-action btn-manage-rooms" style="margin-right: 0.35rem;" onclick="openManageRoomsModal('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')">Rooms</button>
                          <?php else: ?>
                            <button type="button" class="btn-card-action btn-manage-setups" style="margin-right: 0.35rem;" onclick="openManageSetupsModal('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')">Setups</button>
                          <?php endif; ?>
                          <button type="button" class="btn-card-action" onclick="openEditFacilityModal('<?= htmlspecialchars($fid) ?>')">Configure</button>
                          <button type="button" class="btn-card-action btn-delete-facility" onclick="confirmDeleteFacility('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')" title="Move to Trash / Archive">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                          </button>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: EDIT FACILITY SPECIFICATIONS
       ========================================================================== -->
  <div class="admin-modal-overlay" id="editFacilityModal" style="display: none;">
    <div class="admin-modal-card">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4>Configure Facility Specifications</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeEditFacilityModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <form id="editFacilityForm" onsubmit="saveFacilityChanges(event)" style="display: flex; flex-direction: column; flex: 1; min-height: 0; margin: 0; overflow: hidden;">
        <div class="admin-modal-body" style="padding: 1.5rem 1.75rem; overflow-y: auto; flex: 1;">
          <input type="hidden" id="editFacilityId">

          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Official Facility / Room Name</label>
              <input type="text" id="editFacilityName" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Asset Category</label>
              <select id="editFacilityCategory" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
                <option value="halls">Training & Function Hall</option>
                <option value="dorms">Dormitory Suite</option>
                <option value="dining">Dining Facility</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Participant Capacity (PAX)</label>
              <input type="number" id="editFacilityPax" min="1" max="1000" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Total Bed Inventory (For Dorms)</label>
              <input type="number" id="editFacilityBeds" min="0" max="200" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Standard Rate</label>
              <div class="peso-input-group"><span class="peso-input-addon">₱</span><input type="text" id="editFacilityRate" class="peso-input-field" placeholder="5,000 / day"></div>
            </div>
          </div>

          <!-- Facility Photograph Upload & Selector -->
          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Facility Featured Photograph</label>
            <div style="display: flex; gap: 1rem; align-items: center; background: #fbfdfc; border: 1.5px dashed #c9ded0; border-radius: 10px; padding: 0.85rem 1rem;">
              <div style="position: relative; width: 100px; height: 70px; border-radius: 8px; overflow: hidden; border: 1.5px solid #dce8e0; flex-shrink: 0; background: #eaf1ec;">
                <img id="editFacilityImgPreview" src="assets/images/function_hall.jpg" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
              <div style="flex: 1;">
                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.4rem; flex-wrap: wrap;">
                  <label for="editFacilityFileInput" class="btn-card-action" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.78rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <span>Upload Local File</span>
                  </label>
                  <input type="file" id="editFacilityFileInput" accept="image/*" style="display: none;" onchange="handleFacilityFilePreview(this, 'editFacilityImgPreview', 'editFacilityImageSrc')">
                  <input type="hidden" id="editFacilityImageSrc" value="">

                  <select id="editFacilityPresetSelect" onchange="applyPresetImage(this.value, 'editFacilityImgPreview', 'editFacilityImageSrc')" style="padding: 0.38rem 0.65rem; border: 1.2px solid #dce8e0; border-radius: 6px; font-family: inherit; font-size: 0.78rem; color: #2e4738;">
                    <option value="">-- Choose official photo preset --</option>
                    <option value="assets/images/carandang_room.jpg">Carandang Room (Executive Boardroom)</option>
                    <option value="assets/images/orosa_hall.jpg">Orosa Hall</option>
                    <option value="assets/images/rdec_training_1.jpg">RDEC Training Hall 1</option>
                    <option value="assets/images/rdec_training_2.jpg">RDEC Training Hall 2</option>
                    <option value="assets/images/function_hall.jpg">Function Hall</option>
                    <option value="assets/images/serrano_hall.jpg">Serrano Hall</option>
                    <option value="assets/images/dorm_suite_1.png">Suite 1 (2nd Floor)</option>
                    <option value="assets/images/dorm_suite_2.png">Suite 2 (3rd Floor)</option>
                    <option value="assets/images/dorm_vip_suite.jpg">VIP Suite (4th Floor)</option>
                    <option value="assets/images/dormitory.jpg">Dormitory Suites (Overview)</option>
                    <option value="assets/images/ATI_building.jpg">ATI Main Building Facade</option>
                  </select>
                </div>
                <small style="color: #657b6f; font-size: 0.74rem;">Select local photo from your device or pick an official ATI library preset.</small>
              </div>
            </div>
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Building & Floor Location</label>
            <input type="text" id="editFacilityLocation" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Standard Amenities & Equipment (Comma-separated)</label>
            <input type="text" id="editFacilityFeatures" placeholder="Airconditioned, Projector, Sound System, Wi-Fi" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
          </div>

          <!-- Multi-Photo Gallery & Angle Captions Manager -->
          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Multi-Photo Gallery &amp; Angle Captions</label>
            <div class="gallery-manager-wrap">
              <div class="gallery-manager-toolbar">
                <div class="gallery-toolbar-left">
                  <label for="editFacilityGalleryFiles" class="btn-card-action" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.78rem; background: #eaf5ee; border-color: #a3d7b4; color: #174d2f; font-weight: 700;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <span>Upload Multiple Photos</span>
                  </label>
                  <input type="file" id="editFacilityGalleryFiles" multiple accept="image/*" style="display: none;" onchange="handleGalleryFilesSelected(this, 'edit')">

                  <select id="editFacilityGalleryPreset" onchange="handleGalleryPresetSelected(this, 'edit')" style="padding: 0.38rem 0.65rem; border: 1.2px solid #dce8e0; border-radius: 6px; font-family: inherit; font-size: 0.78rem; color: #2e4738;">
                    <option value="">+ Add official preset photo...</option>
                    <option value="assets/images/carandang_room.jpg|Executive Stage &amp; Meeting Setup">Carandang Room (Executive Boardroom)</option>
                    <option value="assets/images/boardroom.jpg|Executive Presentation Podium &amp; Digital Display">Boardroom Podium &amp; Screen</option>
                    <option value="assets/images/orosa_hall.jpg|Orosa Function Venue &amp; Banquet Setup">Orosa Hall</option>
                    <option value="assets/images/rdec_training_1.jpg|Training Hall Dual Presentation Screens">RDEC Training Hall 1</option>
                    <option value="assets/images/rdec_training_2.jpg|Classroom Workshop Workstations">RDEC Training Hall 2</option>
                    <option value="assets/images/function_hall.jpg|Attendee Ergonomic Seating &amp; Surround Audio">Function Hall Seating</option>
                    <option value="assets/images/serrano_hall.jpg|Serrano Multi-Purpose Auditorium Setup">Serrano Hall</option>
                    <option value="assets/images/dorm_suite_1.png|Study Workstation &amp; Aircon Cooling">Dorm Suite 1 (Study Area)</option>
                    <option value="assets/images/dorm_suite_2.png|Secured Storage Lockers &amp; Wardrobes">Dorm Suite 2 (Lockers)</option>
                    <option value="assets/images/dorm_vip_suite.jpg|Ensuite Restroom &amp; Common Lounge">VIP Suite (Lounge &amp; Bath)</option>
                    <option value="assets/images/dormitory.jpg|Dormitory Hallway &amp; Reception Access">Dormitory Hallway</option>
                    <option value="assets/images/ATI_building.jpg|ATI Campus Arrival Wing &amp; Facade">ATI Main Building Facade</option>
                  </select>
                </div>
                <span class="fg-photos-count-badge" id="editGalleryCountBadge" style="font-size: 0.74rem; font-weight: 700; color: #355742; background: #eef6f1; padding: 0.25rem 0.6rem; border-radius: 999px;">0 Photos</span>
              </div>

              <!-- Gallery Photos Card Track -->
              <div class="gallery-items-grid" id="editGalleryItemsGrid">
                <!-- Populated via renderGalleryEditor('edit') -->
              </div>
            </div>
            <small style="color: #657b6f; font-size: 0.74rem; display: block; margin-top: 0.35rem;">Upload multiple photos from your device or pick presets. You can customize captions for each photo angle.</small>
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.5rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Overview &amp; Purpose (Client Description)</label>
            <textarea id="editFacilityNotes" rows="3" placeholder="Provide a brief overview of the venue purpose, capacity highlights, and features displayed to clients..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;"></textarea>
            <small style="color: #657b6f; font-size: 0.74rem;">This text is displayed under "Overview &amp; Purpose" in the client showcase modal.</small>
          </div>
        </div>

        <div class="admin-modal-footer">
          <button type="button" class="btn-system-secondary" onclick="closeEditFacilityModal()">Cancel</button>
          <button type="submit" id="btnSaveFacilitySpecs" class="btn-system-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            <span>Save Specifications</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: REGISTER NEW FACILITY
       ========================================================================== -->
  <div class="admin-modal-overlay" id="addFacilityModal" style="display: none;">
    <div class="admin-modal-card">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4>Register New Institutional Facility</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeAddFacilityModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <form id="addFacilityForm" onsubmit="handleAddNewFacility(event)" style="display: flex; flex-direction: column; flex: 1; min-height: 0; margin: 0; overflow: hidden;">
        <div class="admin-modal-body" style="padding: 1.5rem 1.75rem; overflow-y: auto; flex: 1;">
          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Venue / Room Title</label>
              <input type="text" id="addFacilityName" placeholder="e.g. Training Hall B (Agri-Fisheries)" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Asset Category</label>
              <select id="addFacilityCategory" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
                <option value="halls">Training & Function Hall</option>
                <option value="dorms">Dormitory Suite</option>
                <option value="dining">Dining Facility</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Participant Capacity (PAX)</label>
              <input type="number" id="addFacilityPax" min="1" max="1000" placeholder="50" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Bed Capacity (If Dormitory)</label>
              <input type="number" id="addFacilityBeds" min="0" max="200" placeholder="0" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            </div>

            <div class="form-group-decline">
              <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Standard Rate</label>
              <div class="peso-input-group"><span class="peso-input-addon">₱</span><input type="text" id="addFacilityRate" class="peso-input-field" placeholder="5,000 / day"></div>
            </div>
          </div>

          <!-- Add Facility Photograph Upload & Selector -->
          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Facility Featured Photograph</label>
            <div style="display: flex; gap: 1rem; align-items: center; background: #fbfdfc; border: 1.5px dashed #c9ded0; border-radius: 10px; padding: 0.85rem 1rem;">
              <div style="position: relative; width: 100px; height: 70px; border-radius: 8px; overflow: hidden; border: 1.5px solid #dce8e0; flex-shrink: 0; background: #eaf1ec;">
                <img id="addFacilityImgPreview" src="assets/images/function_hall.jpg" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
              <div style="flex: 1;">
                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.4rem; flex-wrap: wrap;">
                  <label for="addFacilityFileInput" class="btn-card-action" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.78rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <span>Upload Local File</span>
                  </label>
                  <input type="file" id="addFacilityFileInput" accept="image/*" style="display: none;" onchange="handleFacilityFilePreview(this, 'addFacilityImgPreview', 'addFacilityImageSrc')">
                  <input type="hidden" id="addFacilityImageSrc" value="assets/images/function_hall.jpg">

                  <select id="addFacilityPresetSelect" onchange="applyPresetImage(this.value, 'addFacilityImgPreview', 'addFacilityImageSrc')" style="padding: 0.38rem 0.65rem; border: 1.2px solid #dce8e0; border-radius: 6px; font-family: inherit; font-size: 0.78rem; color: #2e4738;">
                    <option value="assets/images/carandang_room.jpg">Carandang Room (Executive Boardroom)</option>
                    <option value="assets/images/orosa_hall.jpg">Orosa Hall</option>
                    <option value="assets/images/rdec_training_1.jpg">RDEC Training Hall 1</option>
                    <option value="assets/images/rdec_training_2.jpg">RDEC Training Hall 2</option>
                    <option value="assets/images/function_hall.jpg">Function Hall</option>
                    <option value="assets/images/serrano_hall.jpg">Serrano Hall</option>
                    <option value="assets/images/dorm_suite_1.png">Suite 1 (2nd Floor)</option>
                    <option value="assets/images/dorm_suite_2.png">Suite 2 (3rd Floor)</option>
                    <option value="assets/images/dorm_vip_suite.jpg">VIP Suite (4th Floor)</option>
                    <option value="assets/images/dormitory.jpg">Dormitory Suites (Overview)</option>
                    <option value="assets/images/ATI_building.jpg">ATI Main Building Facade</option>
                  </select>
                </div>
                <small style="color: #657b6f; font-size: 0.74rem;">Upload image or select photo preset.</small>
              </div>
            </div>
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Building Location / Floor</label>
            <input type="text" id="addFacilityLocation" placeholder="e.g. Training Annex Building, 2nd Floor" required style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Included Equipment (Comma-separated)</label>
            <input type="text" id="addFacilityFeatures" placeholder="Airconditioned, Smart TV, Whiteboard, High-speed Wi-Fi" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
          </div>

          <!-- Multi-Photo Gallery & Angle Captions Manager -->
          <div class="form-group-decline" style="margin-bottom: 0.85rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">
              Multi-Photo Gallery &amp; Angle Captions (Optional)
            </label>
            <div class="gallery-manager-wrap">
              <div class="gallery-manager-toolbar">
                <div class="gallery-toolbar-left">
                  <label for="addFacilityGalleryFiles" class="btn-card-action" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.78rem; background: #eaf5ee; border-color: #a3d7b4; color: #174d2f; font-weight: 700;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <span>Upload Multiple Photos</span>
                  </label>
                  <input type="file" id="addFacilityGalleryFiles" multiple accept="image/*" style="display: none;" onchange="handleGalleryFilesSelected(this, 'add')">

                  <select id="addFacilityGalleryPreset" onchange="handleGalleryPresetSelected(this, 'add')" style="padding: 0.38rem 0.65rem; border: 1.2px solid #dce8e0; border-radius: 6px; font-family: inherit; font-size: 0.78rem; color: #2e4738;">
                    <option value="">+ Add official preset photo...</option>
                    <option value="assets/images/carandang_room.jpg|Executive Stage &amp; Meeting Setup">Carandang Room (Executive Boardroom)</option>
                    <option value="assets/images/boardroom.jpg|Executive Presentation Podium &amp; Digital Display">Boardroom Podium &amp; Screen</option>
                    <option value="assets/images/orosa_hall.jpg|Orosa Function Venue &amp; Banquet Setup">Orosa Hall</option>
                    <option value="assets/images/rdec_training_1.jpg|Training Hall Dual Presentation Screens">RDEC Training Hall 1</option>
                    <option value="assets/images/rdec_training_2.jpg|Classroom Workshop Workstations">RDEC Training Hall 2</option>
                    <option value="assets/images/function_hall.jpg|Attendee Ergonomic Seating &amp; Surround Audio">Function Hall Seating</option>
                    <option value="assets/images/serrano_hall.jpg|Serrano Multi-Purpose Auditorium Setup">Serrano Hall</option>
                    <option value="assets/images/dorm_suite_1.png|Study Workstation &amp; Aircon Cooling">Dorm Suite 1 (Study Area)</option>
                    <option value="assets/images/dorm_suite_2.png|Secured Storage Lockers &amp; Wardrobes">Dorm Suite 2 (Lockers)</option>
                    <option value="assets/images/dorm_vip_suite.jpg|Ensuite Restroom &amp; Common Lounge">VIP Suite (Lounge &amp; Bath)</option>
                    <option value="assets/images/dormitory.jpg|Dormitory Hallway &amp; Reception Access">Dormitory Hallway</option>
                    <option value="assets/images/ATI_building.jpg|ATI Campus Arrival Wing &amp; Facade">ATI Main Building Facade</option>
                  </select>
                </div>
                <span class="fg-photos-count-badge" id="addGalleryCountBadge" style="font-size: 0.74rem; font-weight: 700; color: #355742; background: #eef6f1; padding: 0.25rem 0.6rem; border-radius: 999px;">0 Photos</span>
              </div>

              <!-- Gallery Photos Card Track -->
              <div class="gallery-items-grid" id="addGalleryItemsGrid">
                <!-- Populated via renderGalleryEditor('add') -->
              </div>
            </div>
            <small style="color: #657b6f; font-size: 0.74rem; display: block; margin-top: 0.35rem;">Upload multiple photos or pick presets. Leave empty to use intelligent library presets.</small>
          </div>

          <div class="form-group-decline" style="margin-bottom: 0.5rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Overview &amp; Purpose (Client Description)</label>
            <textarea id="addFacilityNotes" rows="3" placeholder="Provide a brief overview of the venue purpose, capacity highlights, and features displayed to clients..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;"></textarea>
            <small style="color: #657b6f; font-size: 0.74rem;">This text is displayed under "Overview &amp; Purpose" in the client showcase modal.</small>
          </div>
        </div>

        <div class="admin-modal-footer">
          <button type="button" class="btn-system-secondary" onclick="closeAddFacilityModal()">Cancel</button>
          <button type="submit" class="btn-system-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Register Asset</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: DORMITORY ROOMS MANAGEMENT
       ========================================================================== -->
  <div class="admin-modal-overlay" id="manageRoomsModal" style="display: none;">
    <div class="admin-modal-card rooms-modal">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <span style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #174d2f; letter-spacing: 0.5px;">Institutional Dormitory Inventory</span>
          <h4 id="manageRoomsFacilityName" style="color: #153321; margin: 0.15rem 0 0 0;">Manage Dormitory Rooms</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeManageRoomsModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body" style="padding: 1.25rem 1.75rem 1.75rem 1.75rem;">
        <input type="hidden" id="currentManageFacilityId">

        <!-- KPI Status Summary -->
        <div class="rooms-kpi-summary">
          <div class="room-kpi-pill total">
            <span class="kpi-num" id="roomKpiTotal">0</span>
            <span class="kpi-lbl">Total Rooms</span>
          </div>
          <div class="room-kpi-pill available">
            <span class="kpi-num" id="roomKpiAvailable">0</span>
            <span class="kpi-lbl">Available</span>
          </div>
          <div class="room-kpi-pill occupied">
            <span class="kpi-num" id="roomKpiOccupied">0</span>
            <span class="kpi-lbl">Occupied Today</span>
          </div>
          <div class="room-kpi-pill maint">
            <span class="kpi-num" id="roomKpiMaintenance">0</span>
            <span class="kpi-lbl">Maintenance</span>
          </div>
        </div>

        <!-- Quick Add Room Form -->
        <div class="room-quick-add-box">
          <div class="room-quick-add-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Quick Register New Room</span>
          </div>
          <form id="quickAddRoomForm" onsubmit="handleQuickAddRoom(event)" class="room-quick-add-fields">
            <input type="text" id="newRoomNumber" placeholder="Room No. (e.g. 107)" required style="width: 140px;">
            <select id="newRoomFloor" style="flex: 1; min-width: 150px;">
              <option value="1st Floor">1st Floor</option>
              <option value="2nd Floor">2nd Floor</option>
              <option value="3rd Floor">3rd Floor</option>
              <option value="Executive Wing">Executive Wing</option>
              <option value="Annex Wing">Annex Wing</option>
            </select>
            <button type="submit" class="btn-add-room-submit" id="btnSubmitAddRoom">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              <span>Add Room</span>
            </button>
          </form>
        </div>

        <!-- Rooms List Grid -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem;">
          <h5 style="margin: 0; font-size: 0.85rem; font-weight: 700; color: #2e4738;">Registered Rooms & Live Status</h5>
          <span style="font-size: 0.74rem; color: #637f6f;">Toggle switch to put room on Maintenance Hold</span>
        </div>

        <div class="room-tiles-grid" id="manageRoomsGrid">
          <!-- Dynamically populated via loadFacilityRooms() -->
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: VENUE SETUPS & FLOOR PLANS MANAGEMENT
       ========================================================================== -->
  <div class="admin-modal-overlay" id="manageSetupsModal" style="display: none;">
    <div class="admin-modal-card rooms-modal" style="max-width: 860px;">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <span style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #1d4ed8; letter-spacing: 0.5px;">Institutional Floor Plans & Layouts</span>
          <h4 id="manageSetupsFacilityName" style="color: #153321; margin: 0.15rem 0 0 0;">Manage Venue Setups</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeManageSetupsModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body" style="padding: 1.25rem 1.75rem 1.75rem 1.75rem;">
        <input type="hidden" id="currentManageSetupsFacilityId">

        <!-- KPI Status Summary -->
        <div class="rooms-kpi-summary">
          <div class="room-kpi-pill total">
            <span class="kpi-num" id="setupKpiTotal" style="color: #1d4ed8;">0</span>
            <span class="kpi-lbl">Total Setups</span>
          </div>
          <div class="room-kpi-pill available">
            <span class="kpi-num" id="setupKpiAvailable" style="color: #16a34a;">0</span>
            <span class="kpi-lbl">Available</span>
          </div>
          <div class="room-kpi-pill maint">
            <span class="kpi-num" id="setupKpiReserved" style="color: #dc2626;">0</span>
            <span class="kpi-lbl">Reserved / Closed</span>
          </div>
          <div class="room-kpi-pill" style="background: #f0fdf4; border-color: #bbf7d0;">
            <span class="kpi-num" id="setupKpiMaxCap" style="color: #15803d; font-size: 1.1rem;">0 PAX</span>
            <span class="kpi-lbl">Venue Standard Cap</span>
          </div>
        </div>

        <!-- Quick Add Setup Form -->
        <div class="room-quick-add-box" style="background: #f8fafc; border-color: #cbd5e1; margin-bottom: 1.25rem; padding: 0.85rem 1rem;">
          <div class="room-quick-add-title" style="color: #0f172a; margin-bottom: 0.5rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span style="font-weight: 700;">Add New Layout Setup / Floor Plan</span>
          </div>
          <form id="quickAddSetupForm" onsubmit="handleQuickAddSetup(event)" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
            <select id="newSetupPresetSelect" onchange="onSetupPresetChange(this.value)" style="padding: 0.55rem 0.65rem; border: 1.2px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem; font-family: inherit; background: #ffffff;">
              <option value="custom">-- Custom Setup --</option>
              <option value="theater">Theater Setup</option>
              <option value="classroom">Classroom Setup</option>
              <option value="workshop">Workshop Pods</option>
              <option value="banquet">Banquet Dining</option>
              <option value="ushape">U-Shape Workshop</option>
              <option value="conference">Conference Setup</option>
              <option value="roundtable">Round Table Discussion</option>
              <option value="videoconf">Hybrid Video-Conference</option>
              <option value="exhibition">Open Exhibition Booths</option>
            </select>
            <input type="text" id="newSetupName" placeholder="Setup Name (e.g. Theater Setup)" required style="flex: 1; min-width: 140px; padding: 0.55rem 0.75rem; border: 1.2px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem;">
            <input type="text" id="newSetupCap" placeholder="Cap (e.g. 50 PAX)" style="width: 110px; padding: 0.55rem 0.75rem; border: 1.2px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem;">
            <input type="text" id="newSetupDesc" placeholder="Description (e.g. Row seating facing main presentation screen)" style="flex: 2; min-width: 200px; padding: 0.55rem 0.75rem; border: 1.2px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem;">
            <button type="submit" class="btn-add-room-submit" style="background: #2563eb; padding: 0.55rem 0.95rem; border: none; border-radius: 8px; color: #fff; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              <span>Add Setup</span>
            </button>
          </form>
        </div>

        <!-- Setup Cards List Grid -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem;">
          <h5 style="margin: 0; font-size: 0.85rem; font-weight: 700; color: #1e293b;">Active Floor Plans & Setups (Live on Public Portal)</h5>
          <span style="font-size: 0.74rem; color: #64748b;">Toggle switch to set setup as Available or Reserved</span>
        </div>

        <div class="setups-tiles-grid" id="manageSetupsGrid">
          <!-- Dynamically populated via loadFacilitySetups() -->
        </div>

        <!-- Modal Footer Actions -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 0.75rem;">
          <button type="button" class="btn-system-secondary" onclick="resetSetupsToDefaults()" style="font-size: 0.82rem; display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            <span>Reset to Standard 4 Presets</span>
          </button>
          <div style="display: flex; gap: 0.5rem;">
            <button type="button" class="btn-system-secondary" onclick="closeManageSetupsModal()">Cancel</button>
            <button type="button" class="btn-system-primary" onclick="saveFacilitySetups()" id="btnSaveSetupsSubmit" style="background: #2563eb; border-color: #2563eb;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Save Setups Changes</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: MAINTENANCE HOLD JUSTIFICATION
       ========================================================================== -->
  <div class="admin-modal-overlay" id="maintenanceModal" style="display: none;">
    <div class="admin-modal-card sm">
      <div class="admin-modal-header">
        <div class="modal-title-wrap">
          <h4 style="color: #b91c1c;">Activate Administrative Maintenance Hold</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeMaintenanceModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body">
        <div class="maintenance-alert-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
          <p>
            Placing <strong id="maintModalFacilityName">Facility</strong> on Maintenance Hold will automatically block reservation submissions on the public booking portal and Master Schedule.
          </p>
        </div>

        <div class="form-group-decline" style="margin-bottom: 0.85rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Maintenance Ground / Reason</label>
          <select id="maintReasonSelect" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
            <option value="Airconditioning Servicing & Preventive Maintenance">Airconditioning Servicing & Preventive Maintenance</option>
            <option value="Audio-Visual / Electrical Wiring Upgrade">Audio-Visual / Electrical Wiring Upgrade</option>
            <option value="Deep Sanitization & Pest Fumigation">Deep Sanitization & Pest Fumigation</option>
            <option value="Floor Waxing & Architectural Refurbishment">Floor Waxing & Architectural Refurbishment</option>
            <option value="Institutional Executive Discretion">Institutional Executive Discretion</option>
          </select>
        </div>

        <div class="form-group-decline" style="margin-bottom: 0.85rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Estimated Completion Date</label>
          <input type="date" id="maintEstResumeDate" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
        </div>

        <div class="form-group-decline" style="margin-bottom: 0.5rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Work Order Remarks (Optional)</label>
          <textarea id="maintRemarksInput" rows="2" placeholder="e.g. Work Order #2026-44 issued to General Services Unit..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;"></textarea>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" class="btn-system-secondary" onclick="closeMaintenanceModal()">Cancel</button>
        <button type="button" class="btn-confirm-decline" onclick="confirmMaintenanceHold()">Activate Maintenance Hold</button>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: ACTIVE OCCUPANT & REAL-TIME EVENT DETAILS
       ========================================================================== -->
  <div class="admin-modal-overlay" id="facilityOccupantModal" role="dialog" aria-modal="true" aria-labelledby="occupantModalFacilityName" style="display: none;">
    <div class="admin-modal-card" style="max-width: 620px;">
      <div class="admin-modal-header" style="background: linear-gradient(135deg, #174d2f, #2e7d32); color: #ffffff; padding: 1.25rem 1.5rem;">
        <div class="modal-title-wrap">
          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="f-occ-pulse-dot" style="background: #4ade80;"></span>
            <span style="font-size: 0.74rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; color: #bbf7d0;" id="occupantModalStatusBadge">Active Occupant (Today)</span>
          </div>
          <h4 id="occupantModalFacilityName" style="color: #ffffff; margin-top: 4px; font-size: 1.2rem; font-weight: 800; margin-bottom: 0;">Facility Name</h4>
        </div>
        <button type="button" class="admin-modal-close" onclick="closeOccupantModal()" aria-label="Close dialog" style="color: #ffffff; background: rgba(255,255,255,0.18); border: none;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="admin-modal-body" id="occupantModalBody" style="padding: 1.5rem; max-height: 70vh; overflow-y: auto;">
        <!-- Dynamic Content injected via JS -->
      </div>

      <div class="admin-modal-footer" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-top: 1px solid #eef3f0;">
        <button type="button" class="btn-system-secondary" onclick="closeOccupantModal()">Close</button>
        <a id="btnOccupantGoToBooking" href="#" class="btn-system-primary" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; padding: 0.55rem 1.15rem; font-size: 0.84rem;">
          <span>Open in Reservations</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
        </a>
      </div>
    </div>
  </div>

  <!-- Facility Multi-Photo Gallery & Specification Showcase Modal -->
  <?php include __DIR__ . '/includes/facility_gallery_modal.php'; ?>

  <!-- Toast Container -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- Scripts -->
  <script>
    window.initialFacilitiesData = <?= json_encode((object)$facilitiesData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  </script>
  <script src="js/facility_gallery.js?v=<?php echo time(); ?>"></script>
  <script src="js/admin_facilities.js?v=<?php echo time(); ?>"></script>
</body>

</html>
