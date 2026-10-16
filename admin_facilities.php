<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/admin_guard.php';
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

// Active reservations today for occupied status
$today = date('Y-m-d');
$occStmt = $db->prepare("SELECT DISTINCT facility_id FROM reservations WHERE status IN ('Approved', 'Completed') AND start_date <= ? AND end_date >= ?");
$occStmt->execute([$today, $today]);
$occupiedFacilityIds = $occStmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($facilitiesList as $row) {
    $fid = (string)$row['id'];
    $isMaint = ($row['status'] === 'maintenance');
    $isOccupied = in_array($row['id'], $occupiedFacilityIds);

    $status = 'available';
    $statusLabel = 'Available';
    if ($isMaint) {
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
    $totalPax += $pax;
    $totalBeds += $beds;

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
        'rate' => $row['rate'] ?? '',
        'location' => $row['location'] ?? 'ATI Central Office',
        'image' => !empty($row['image']) ? $row['image'] : 'assets/images/function_hall.jpg',
        'features' => array_values($featuresArray),
        'status' => $status,
        'statusLabel' => $statusLabel,
        'maintenance' => $isMaint,
        'maintenanceReason' => $row['maintenance_reason'] ?? '',
        'estResumeDate' => $row['est_resume_date'] ?? '',
        'notes' => $row['notes'] ?? $row['description'] ?? ''
    ];
}

$hallsCount = count(array_filter($facilitiesData, fn($f) => $f['category'] === 'halls'));
$dormsCount = count(array_filter($facilitiesData, fn($f) => $f['category'] === 'dorms'));
$diningCount = count(array_filter($facilitiesData, fn($f) => $f['category'] === 'dining'));
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
          <!-- Quick Search -->
          <div class="topbar-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="topbarSearchInput" placeholder="Quick search venues, halls, dorms..." oninput="handleFacilitySearch(this.value)" autocomplete="off">
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

            <button type="button" class="btn-system-secondary" onclick="exportFacilityInventoryCSV()">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span>Export Asset Inventory</span>
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
                <span class="stat-value" id="kpiTotalFacilities"><?= $totalAssets ?></span>
                <span class="stat-trend positive">100% Operational</span>
              </div>
              <span class="stat-label">Total Monitored Assets</span>
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
              <button type="button" class="facility-filter-tab active" data-category="all" onclick="filterFacilitiesByCategory('all', this)">All Assets (<?= $totalAssets ?>)</button>
              <button type="button" class="facility-filter-tab" data-category="halls" onclick="filterFacilitiesByCategory('halls', this)">Training &amp; Function Halls (<?= $hallsCount ?>)</button>
              <button type="button" class="facility-filter-tab" data-category="dorms" onclick="filterFacilitiesByCategory('dorms', this)">Dormitory Suites (<?= $dormsCount ?>)</button>
              <button type="button" class="facility-filter-tab" data-category="dining" onclick="filterFacilitiesByCategory('dining', this)">Dining Pavilions (<?= $diningCount ?>)</button>
              <button type="button" class="facility-filter-tab" data-category="maintenance" onclick="filterFacilitiesByCategory('maintenance', this)">Maintenance Holds (<?= $maintenanceCount ?>)</button>
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
              $statusClass = $item['status'] === 'maintenance' ? 'state-maintenance' : ($item['status'] === 'occupied' ? 'state-occupied' : 'state-available');
              $typeBadge = $item['category'] === 'dorms' ? 'Dormitory Suite' : ($item['category'] === 'dining' ? 'Dining Pavilion' : 'Function & Training Hall');
              $capDisplay = $item['pax'] . ' Participants';
              if ($item['beds'] > 0) {
                $capDisplay = $item['pax'] . ' PAX (' . $item['beds'] . ' Beds)';
              }
            ?>
            <div class="facility-card-item" data-id="<?= htmlspecialchars($fid) ?>" data-category="<?= htmlspecialchars($item['category']) ?>" data-maintenance="<?= $isMaint ? 'true' : 'false' ?>">
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
                <div class="facility-features-wrap">
                  <?php foreach (array_slice($item['features'], 0, 5) as $feat): ?>
                    <span class="feature-pill-tag"><?= htmlspecialchars($feat) ?></span>
                  <?php endforeach; ?>
                </div>
                <div class="facility-card-footer">
                  <div class="facility-maintenance-quick-toggle">
                    <label class="switch-toggle" title="Toggle Maintenance Mode">
                      <input type="checkbox" <?= $isMaint ? 'checked' : '' ?> onchange="onMaintenanceSwitchChange('<?= htmlspecialchars($fid) ?>', this.checked)">
                      <span class="slider-round"></span>
                    </label>
                    <span>Maintenance</span>
                  </div>
                  <div class="card-action-btns">
                    <?php if ($item['category'] === 'dorms'): ?>
                      <button type="button" class="btn-card-action btn-manage-rooms" onclick="openManageRoomsModal('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')" title="Manage Individual Dormitory Rooms">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <span>Rooms</span>
                      </button>
                    <?php endif; ?>
                    <button type="button" class="btn-card-action" onclick="openEditFacilityModal('<?= htmlspecialchars($fid) ?>')">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                      <span>Edit</span>
                    </button>
                    <a href="schedule.php" class="btn-card-action" title="View in Master Schedule">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                    </a>
                  </div>
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
                    <tr data-id="<?= htmlspecialchars($fid) ?>" data-category="<?= htmlspecialchars($item['category']) ?>" data-maintenance="<?= $item['maintenance'] ? 'true' : 'false' ?>">
                      <td>
                        <div class="facility-table-info-cell">
                          <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="facility-table-thumb" onerror="this.src='assets/images/function_hall.jpg'">
                          <div class="facility-table-info-text">
                            <strong><?= htmlspecialchars($item['name']) ?></strong>
                            <span><?= htmlspecialchars($item['location']) ?></span>
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
                        <label class="switch-toggle">
                          <input type="checkbox" <?= $item['maintenance'] ? 'checked' : '' ?> onchange="onMaintenanceSwitchChange('<?= htmlspecialchars($fid) ?>', this.checked)">
                          <span class="slider-round"></span>
                        </label>
                      </td>
                      <td>
                        <?php if ($item['status'] === 'available'): ?>
                          <span class="status-badge-chip chip-confirmed">Available</span>
                        <?php elseif ($item['status'] === 'occupied'): ?>
                          <span class="status-badge-chip chip-pending"><?= htmlspecialchars($item['statusLabel']) ?></span>
                        <?php else: ?>
                          <span class="status-badge-chip chip-declined">Maintenance Hold</span>
                        <?php endif; ?>
                      </td>
                      <td style="text-align: right;">
                        <?php if ($item['category'] === 'dorms'): ?>
                          <button type="button" class="btn-card-action btn-manage-rooms" style="margin-right: 0.35rem;" onclick="openManageRoomsModal('<?= htmlspecialchars($fid) ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>')">Rooms</button>
                        <?php endif; ?>
                        <button type="button" class="btn-card-action" onclick="openEditFacilityModal('<?= htmlspecialchars($fid) ?>')">Configure</button>
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

          <div class="form-group-decline" style="margin-bottom: 0.5rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Administrative Operational Notes</label>
            <textarea id="editFacilityNotes" rows="2" placeholder="Special restrictions, key custodians, or technical equipment remarks..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;"></textarea>
          </div>
        </div>

        <div class="admin-modal-footer">
          <button type="button" class="btn-system-secondary" onclick="closeEditFacilityModal()">Cancel</button>
          <button type="submit" class="btn-system-primary">
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

          <div class="form-group-decline" style="margin-bottom: 0.5rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #153321; display: block; margin-bottom: 0.35rem;">Included Equipment (Comma-separated)</label>
            <input type="text" id="addFacilityFeatures" placeholder="Airconditioned, Smart TV, Whiteboard, High-speed Wi-Fi" style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.88rem;">
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

  <!-- Toast Container -->
  <div class="admin-toast-container" id="adminToastContainer"></div>

  <!-- Scripts -->
  <script>
    window.initialFacilitiesData = <?= json_encode((object)$facilitiesData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  </script>
  <script src="js/admin_facilities.js?v=<?php echo time(); ?>"></script>
</body>

</html>
