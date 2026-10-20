<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth_guard.php';

/**
 * ATI Facility & Dormitory Reservation Portal
 * User Dashboard: Booking History (Completed Activities & Cancelled Bookings)
 */

$userId = $_SESSION['user_id'] ?? 0;

// Fetch count of Active Reservations for the tab switcher badge
$activeCountStmt = $db->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = :user_id AND status NOT IN ('Completed', 'Cancelled', 'Rejected') AND deleted_at IS NULL");
$activeCountStmt->execute([':user_id' => $userId]);
$activeCount = (int)$activeCountStmt->fetchColumn();

// Fetch History Reservations for the current user joined with facilities and rooms
$query = "SELECT r.*, 
                 f.name AS facility_name, 
                 f.type AS facility_type, 
                 f.image AS facility_image,
                 rm.room_number, 
                 rm.floor AS room_floor,
                 u.full_name, 
                 u.office_agency,
                 u_end.full_name AS endorser_name,
                 u_rec.full_name AS recommender_name,
                 u_clr.full_name AS clearer_name
          FROM reservations r
          LEFT JOIN facilities f ON r.facility_id = f.id
          LEFT JOIN rooms rm ON r.room_id = rm.id
          LEFT JOIN users u ON r.user_id = u.id
          LEFT JOIN users u_end ON r.endorsed_by = u_end.id
          LEFT JOIN users u_rec ON r.recommended_by = u_rec.id
          LEFT JOIN users u_clr ON r.cleared_by = u_clr.id
          WHERE r.user_id = :user_id 
            AND r.status IN ('Completed', 'Cancelled', 'Rejected')
            AND r.deleted_at IS NULL
          ORDER BY r.id DESC";

$stmt = $db->prepare($query);
$stmt->execute([':user_id' => $userId]);
$historyList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch active facilities from database for dynamic dropdown filter
$facFilterStmt = $db->query("SELECT id, name, type FROM facilities WHERE status != 'inactive' AND deleted_at IS NULL ORDER BY name ASC");
$filterFacilities = $facFilterStmt ? $facFilterStmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Calculate dynamic stats for KPI summary
$statsHistoryTotal = count($historyList);
$statsCompleted = 0;
$statsCancelled = 0;

foreach ($historyList as $item) {
    $st = $item['status'] ?? '';
    if ($st === 'Completed') {
        $statsCompleted++;
    } elseif (in_array($st, ['Cancelled', 'Rejected'])) {
        $statsCancelled++;
    }
}
$statsArchive = $statsHistoryTotal;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking History | ATI Reservation Portal</title>
  <meta name="description"
    content="Official Booking History for concluded facility events, completed dormitory lodgings, and cancelled reservations for the Agriculture Training Institute.">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/my_reservations.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/mobile-drawer.css?v=<?php echo time(); ?>">
</head>

<body class="my-res-body booking-history-page">

  <!-- ==========================================================================
       TOPBAR NAVIGATION
       ========================================================================== -->
  <header class="booking-topbar">
    <div class="booking-topbar-container">
      <!-- Brand Logo & Title -->
      <a href="home.php" class="booking-brand" style="text-decoration: none;" title="ATI Reservation Portal">
        <img src="assets/images/ATI_Logo.png" alt="Agricultural Training Institute Logo" class="booking-brand-logo">
        <div class="booking-brand-text">
          <h1>Agricultural Training Institute</h1>
          <p>Facility and Dormitory Reservation System</p>
        </div>
      </a>

      <!-- Navigation Links -->
      <nav class="booking-nav-center" aria-label="Portal Navigation">
        <!-- Home -->
        <a href="home.php" class="booking-nav-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>Home</span>
        </a>

        <!-- New Reservation -->
        <a href="booking.php" class="booking-nav-item">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
          </svg>
          <span>New Reservation</span>
        </a>

        <!-- My Reservations -->
        <a href="my_reservations.php" class="booking-nav-item active" title="My Reservations & History">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>My Reservations</span>
          <span class="nav-badge-count"><?= $activeCount ?></span>
        </a>

        <!-- Master Schedule -->
        <a href="schedule.php" class="booking-nav-item">
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
          <span>Master Schedule</span>
        </a>

        <!-- User Profile Badge & Dropdown -->
        <div class="user-profile-menu">
          <div class="user-profile-badge" id="userProfileBadge" role="button" aria-haspopup="true">
            <div class="user-avatar-circle"><?= getUserInitials($_SESSION['full_name'] ?? 'User') ?></div>
            <div class="user-details">
              <div class="user-name"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Official User') ?></div>
              <div class="user-role"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role'] ?? 'user'))) ?></div>
            </div>
          </div>

          <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-header-info">
              <div class="dropdown-user-name"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Official User') ?></div>
              <div class="dropdown-user-email"><span class="user-verified-dot"></span> <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role'] ?? 'user'))) ?></div>
            </div>
            <a href="profile.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>My Profile</span>
            </a>
            <a href="booking_history.php" class="dropdown-item active">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>Booking History</span>
            </a>
            <?php if (isAdmin()): ?>
            <a href="admin_dashboard.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
              <span>Admin Dashboard</span>
            </a>
            <?php endif; ?>
            <div style="height: 1px; background: #e5ede7; margin: 0.35rem 0;"></div>
            <a href="logout.php" class="dropdown-item danger">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
              <span>Sign Out</span>
            </a>
          </div>
        </div>
      </nav>

      <!-- Mobile Hamburger Menu Button -->
      <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleMobileDrawer(true)"
        aria-label="Open Navigation Menu" aria-expanded="false">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </div>
  </header>

  <!-- ==========================================================================
       MOBILE NAVIGATION DRAWER OVERLAY
       ========================================================================== -->
  <div class="mobile-drawer-overlay" id="mobileDrawerOverlay"
    onclick="if(event.target===this) toggleMobileDrawer(false);" style="display: none;">
    <div class="mobile-nav-drawer" id="mobileNavDrawer" role="dialog" aria-modal="true" aria-label="Mobile Navigation">

      <!-- Drawer Header -->
      <div class="drawer-header">
        <div class="drawer-brand">
          <img src="assets/images/ATI_Logo.png" alt="ATI Logo" class="drawer-logo"
            style="width: 36px; height: 36px; object-fit: contain;">
          <div>
            <h4>ATI Portal</h4>
            <p>Central Office</p>
          </div>
        </div>
        <button type="button" class="drawer-close-btn" id="mobileDrawerClose" onclick="toggleMobileDrawer(false)"
          aria-label="Close Navigation Menu">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Drawer Body -->
      <div class="drawer-body">
        <!-- User Profile Card -->
        <div class="drawer-profile-card">
          <div class="drawer-avatar"><?= getUserInitials($_SESSION['full_name'] ?? 'User') ?></div>
          <div class="drawer-profile-info">
            <h5><?= htmlspecialchars($_SESSION['full_name'] ?? 'Official User') ?></h5>
            <p><?= htmlspecialchars($_SESSION['office_agency'] ?? 'ATI Central Office') ?></p>
            <span class="drawer-badge"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role'] ?? 'user'))) ?></span>
          </div>
        </div>

        <!-- Main Navigation Section -->
        <div class="drawer-nav-section">
          <div class="drawer-section-label">PORTAL NAVIGATION</div>

          <a href="home.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
            </div>
            <span class="drawer-link-text">Home</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <a href="booking.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </div>
            <span class="drawer-link-text">New Reservation</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <a href="my_reservations.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
            </div>
            <span class="drawer-link-text">Active Reservations</span>
            <span class="drawer-badge-count"><?= $currentUserResCount ?></span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <a href="booking_history.php" class="drawer-nav-link active">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <span class="drawer-link-text">Booking History</span>
            <span class="drawer-badge-count blue">6</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <a href="schedule.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </div>
            <span class="drawer-link-text">Master Schedule</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>
        </div>

        <!-- Account Settings Section in Drawer -->
        <div class="drawer-nav-section">
          <div class="drawer-section-label">ACCOUNT & SETTINGS</div>

          <a href="profile.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
            <span class="drawer-link-text">My Profile</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>

          <?php if (isAdmin()): ?>
          <a href="admin_dashboard.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
            </div>
            <span class="drawer-link-text">Admin Dashboard</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Drawer Footer -->
      <div class="drawer-footer">
        <a href="logout.php" class="drawer-logout-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span>Sign Out</span>
        </a>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MAIN CONTENT CONTAINER
       ========================================================================== -->
  <main class="my-res-main-wrapper">

    <!-- Primary Category Segregation Switcher (Active Reservations vs Booking History) -->
    <div class="history-category-segmented-bar">
      <div class="category-segmented-container">
        <!-- Tab 1: Active Reservations -->
        <a href="my_reservations.php" class="category-segment-btn" title="View Active Reservations">
          <div class="seg-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
          </div>
          <div class="seg-text">
            <span class="seg-title">Active Reservations</span>
            <span class="seg-subtitle">Pending Clearances &amp; Upcoming Confirmed Schedules</span>
          </div>
          <span class="seg-count-badge"><?= $activeCount ?></span>
        </a>

        <!-- Tab 2: Booking History (Active on this page) -->
        <a href="booking_history.php" class="category-segment-btn active" title="View Booking History">
          <div class="seg-icon history-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
          <div class="seg-text">
            <span class="seg-title">Booking History</span>
            <span class="seg-subtitle">Completed Activities &amp; Cancelled Requests Archive</span>
          </div>
          <span class="seg-count-badge blue"><?= $statsHistoryTotal ?></span>
        </a>
      </div>
    </div>

    <!-- Page Header Section -->
    <section class="my-res-header">
      <div class="my-res-header-text">
        <div class="my-res-top-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
          <span>Official Concluded &amp; Cancelled Records Archive</span>
        </div>
        <h2>Booking History</h2>
        <p>Review past completed facility activities, concluded dormitory stays, and cancelled or disapproved reservations. Access post-activity records, completion certificates, and historical slips.</p>
      </div>

      <a href="booking.php" class="btn-new-res-action">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>New Reservation</span>
      </a>
    </section>

    <!-- Summary KPI Stat Cards -->
    <section class="my-res-stats-grid" aria-label="Booking History Summary Statistics">
      <!-- Total History Records -->
      <div class="my-res-stat-card active-filter" data-filter="all">
        <div class="stat-icon-box blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiTotal"><?= $statsHistoryTotal ?></span>
          <span class="stat-label">Total History Records</span>
        </div>
      </div>

      <!-- Completed Events & Stays -->
      <div class="my-res-stat-card" data-filter="completed">
        <div class="stat-icon-box green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiCompleted"><?= $statsCompleted ?></span>
          <span class="stat-label">Completed &amp; Concluded</span>
        </div>
      </div>

      <!-- Cancelled / Disapproved Requests -->
      <div class="my-res-stat-card" data-filter="cancelled">
        <div class="stat-icon-box red">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiCancelled"><?= $statsCancelled ?></span>
          <span class="stat-label">Cancelled / Disapproved</span>
        </div>
      </div>

      <!-- Archived Downloadable Slips -->
      <div class="my-res-stat-card" data-filter="all">
        <div class="stat-icon-box slate">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiArchive"><?= $statsArchive ?></span>
          <span class="stat-label">Archived Official Slips</span>
        </div>
      </div>
    </section>

    <!-- Filter & Search Toolbar (Card Wrapper & Aligned Controls) -->
    <div class="my-res-toolbar-card">

      <!-- Search Input -->
      <div class="search-box-wrap">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="resSearchInput" class="search-input"
          placeholder="Search ref, activity, venue, or status...">
        <button type="button" class="btn-clear-search" id="btnClearSearch" title="Clear search" style="display: none;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Controls: Category/Status Dropdown & View Mode Switcher -->
      <div class="toolbar-controls-right">
        <!-- Dropdown Filter -->
        <div class="custom-facility-dropdown" id="facilityDropdown">
          <button type="button" class="facility-dropdown-btn" id="facilityDropdownBtn" aria-haspopup="listbox"
            aria-expanded="false" title="Filter by Category / Status">
            <span class="facility-btn-icon">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </span>
            <span class="facility-btn-label" id="facilityDropdownLabel">All History Records</span>
            <svg class="facility-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="2.2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>

          <div class="facility-dropdown-menu" id="facilityDropdownMenu" role="listbox" aria-label="Filter by History Type">
            <div class="facility-option active" data-value="all" role="option" aria-selected="true">
              <span class="opt-bullet all"></span>
              <span class="opt-name">All History Records</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="facility-option" data-value="completed" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">Completed Activities Only</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="facility-option" data-value="cancelled" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">Cancelled / Disapproved Only</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="facility-option" data-value="facility" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">Facility Venues &amp; Halls</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="facility-option" data-value="dormitory" role="option">
              <span class="opt-bullet dorm-bullet"></span>
              <span class="opt-name">Dormitories &amp; Lodgings</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>

            <?php if (!empty($filterFacilities)): ?>
              <div style="height: 1px; background: #e5ede7; margin: 4px 8px;"></div>
              <?php foreach ($filterFacilities as $f): 
                $fType = strtolower($f['type'] ?? '');
                $isDormType = ($fType === 'dormitories' || $fType === 'dorms');
                $bulletClass = $isDormType ? 'dorm-bullet' : '';
              ?>
                <div class="facility-option" data-value="<?= htmlspecialchars($f['name']) ?>" data-facility-id="<?= (int)$f['id'] ?>" role="option">
                  <span class="opt-bullet <?= $bulletClass ?>"></span>
                  <span class="opt-name"><?= htmlspecialchars($f['name']) ?></span>
                  <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- View Mode Switcher -->
        <div class="view-mode-toggle" role="group" aria-label="View format toggle">
          <button type="button" class="view-mode-btn active" id="btnViewCards" title="Card View" aria-pressed="true">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Cards</span>
          </button>
          <button type="button" class="view-mode-btn" id="btnViewTable" title="Table View" aria-pressed="false">
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
        </div>
      </div>
    </div>

    <!-- ==========================================================================
         BOOKING HISTORY CONTAINER (CARDS VIEW & TABLE VIEW)
         ========================================================================== -->
    <section class="reservations-container" id="reservationsList">

      <?php if (empty($historyList)): ?>
        <!-- Shown if user has zero history records in database -->
        <div class="empty-res-state" id="zeroReservationsState" style="display: block; width: 100%; text-align: center; padding: 3.5rem 1.5rem; background: #fff; border-radius: 14px; border: 1.5px dashed #d0ded4; margin-bottom: 2rem;">
          <div class="empty-res-icon" style="margin: 0 auto 1.25rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#175432" width="48" height="48" stroke-width="1.8">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
          <h3 style="font-size: 1.35rem; font-weight: 800; color: #173822; margin-bottom: 0.5rem;">No Booking History Found</h3>
          <p style="color: #557262; max-width: 460px; margin: 0 auto 1.5rem; font-size: 0.92rem;">You do not have any concluded, completed, or cancelled reservations in your archive yet.</p>
          <a href="my_reservations.php" class="btn-new-res-action" style="display: inline-flex; text-decoration: none;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            </svg>
            <span>View Active Reservations</span>
          </a>
        </div>
      <?php else: ?>

        <?php foreach ($historyList as $item): 
          $rawStatus = $item['status'] ?? 'Completed';
          $isCompleted = ($rawStatus === 'Completed');
          $isCancelled = ($rawStatus === 'Cancelled');
          $isRejected = ($rawStatus === 'Rejected');
          
          $filterStatus = $isCompleted ? 'completed' : 'cancelled';
          $isDorm = (($item['facility_type'] ?? '') === 'dormitories') || !empty($item['room_id']);
          $category = $isDorm ? 'dormitory' : 'facility';

          $venueName = $item['facility_name'] ?: ($isDorm ? 'ATI Dormitory' : 'Official Venue');
          $facilityImgRaw = trim($item['facility_image'] ?? '');
          
          $slug = 'facility';
          if ($isDorm) {
              $slug = 'dormitory';
          } elseif (stripos($venueName, 'boardroom') !== false) {
              $slug = 'boardroom';
          } elseif (stripos($venueName, 'training') !== false) {
              $slug = 'training-hall';
          } elseif (stripos($venueName, 'mess') !== false) {
              $slug = 'mess-hall';
          } else {
              $slug = 'function-hall';
          }

          $venueThumb = '';
          if (!empty($facilityImgRaw)) {
              if (file_exists(__DIR__ . '/' . ltrim($facilityImgRaw, '/\\'))) {
                  $venueThumb = ltrim($facilityImgRaw, '/\\');
              } elseif (file_exists(__DIR__ . '/assets/images/' . ltrim($facilityImgRaw, '/\\'))) {
                  $venueThumb = 'assets/images/' . ltrim($facilityImgRaw, '/\\');
              } else {
                  $venueThumb = $facilityImgRaw;
              }
          }
          if (empty($venueThumb)) {
              $venueThumb = $isDorm ? 'assets/images/dormitory.jpg' : 'assets/images/function_hall.jpg';
          }

          $startTs = strtotime($item['start_date']);
          $endTs = strtotime($item['end_date'] ?: $item['start_date']);
          $daysCount = max(1, round(($endTs - $startTs) / 86400) + 1);
          $daysSuffix = $daysCount . ($daysCount > 1 ? ' Days' : ' Day');
          if ($startTs === $endTs) {
              $formattedDates = date('M d, Y', $startTs) . " (1 Day)";
          } else {
              $formattedDates = date('M d', $startTs) . ' – ' . date('M d, Y', $endTs) . " ({$daysSuffix})";
          }

          $paxText = ($item['pax_count'] ?: 0) . ' Attendees';
          $divisionText = $item['office_agency'] ?: 'ATI Personnel';
          $eventTitle = $item['event_title'] ?: 'Official Institutional Activity';
          $timeSlotText = $item['time_slot'] ?: '08:00 AM – 05:00 PM';
          
          $concludedDateTs = !empty($item['updated_at']) ? strtotime($item['updated_at']) : strtotime($item['created_at']);
          $concludedDateText = date('M d, Y', $concludedDateTs);

          if ($isCompleted) {
              $statusBadgeClass = 'completed';
              $statusBadgeText = 'Completed & Concluded';
              $statusPrefix = 'Concluded ' . $concludedDateText;
          } elseif ($isCancelled) {
              $statusBadgeClass = 'cancelled';
              $statusBadgeText = 'Cancelled by Requestor';
              $statusPrefix = 'Cancelled ' . $concludedDateText;
          } else {
              $statusBadgeClass = 'cancelled';
              $statusBadgeText = 'Disapproved / Returned';
              $statusPrefix = 'Disapproved ' . $concludedDateText;
          }

          $venueTitleFull = $venueName . (!empty($item['room_number']) ? ' - Room ' . $item['room_number'] : '');
        ?>
        <!-- RESERVATION CARD -->
        <article class="res-card" 
          data-status="<?= htmlspecialchars($filterStatus) ?>" 
          data-raw-status="<?= htmlspecialchars($rawStatus) ?>" 
          data-category="<?= htmlspecialchars($category) ?>" 
          data-venue="<?= htmlspecialchars($slug) ?>"
          data-venue-id="<?= (int)($item['facility_id'] ?? 0) ?>"
          data-venue-name="<?= htmlspecialchars($venueName) ?>"
          data-ref="<?= htmlspecialchars($item['reference_no']) ?>" 
          data-venue-title="<?= htmlspecialchars($venueTitleFull) ?>" 
          data-venue-image="<?= htmlspecialchars($venueThumb) ?>"
          data-dates="<?= htmlspecialchars($formattedDates) ?>" 
          data-time="<?= htmlspecialchars($timeSlotText) ?>" 
          data-pax="<?= htmlspecialchars($paxText) ?>" 
          data-division="<?= htmlspecialchars($divisionText) ?>" 
          data-applicant-name="<?= htmlspecialchars($item['full_name'] ?? 'Applicant') ?>"
          data-created-at="<?= htmlspecialchars(!empty($item['created_at']) ? date('M d, Y • h:i A', strtotime($item['created_at'])) : 'Archived Record') ?>"
          data-status-text="<?= htmlspecialchars($statusBadgeText) ?>"
          data-special-notes="<?= htmlspecialchars($item['special_notes'] ?? '') ?>"
          data-remarks="<?= htmlspecialchars($item['admin_remarks'] ?? '') ?>"
          data-doc-path="<?= htmlspecialchars($item['document_path'] ?? '') ?>"
          data-endorsed-by="<?= htmlspecialchars($item['endorser_name'] ?? '') ?>"
          data-endorsed-at="<?= htmlspecialchars(!empty($item['endorsed_at']) ? date('M d, Y • h:i A', strtotime($item['endorsed_at'])) : '') ?>"
          data-endorsement-remarks="<?= htmlspecialchars($item['endorsement_remarks'] ?? '') ?>"
          data-recommended-by="<?= htmlspecialchars($item['recommender_name'] ?? '') ?>"
          data-recommended-at="<?= htmlspecialchars(!empty($item['recommended_at']) ? date('M d, Y • h:i A', strtotime($item['recommended_at'])) : '') ?>"
          data-recommendation-remarks="<?= htmlspecialchars($item['recommendation_remarks'] ?? '') ?>"
          data-recommendation-doc="<?= htmlspecialchars($item['recommendation_doc_path'] ?? '') ?>"
          data-cleared-by="<?= htmlspecialchars($item['clearer_name'] ?? '') ?>"
          data-cleared-at="<?= htmlspecialchars(!empty($item['cleared_at']) ? date('M d, Y • h:i A', strtotime($item['cleared_at'])) : '') ?>"
          data-clearance-remarks="<?= htmlspecialchars($item['clearance_remarks'] ?? '') ?>"
          data-clearance-doc="<?= htmlspecialchars($item['clearance_doc_path'] ?? '') ?>">

          <div class="res-card-media">
            <img src="<?= htmlspecialchars($venueThumb) ?>" alt="<?= htmlspecialchars($venueName) ?>" onerror="this.onerror=null; this.src='<?= $isDorm ? 'assets/images/dormitory.jpg' : 'assets/images/function_hall.jpg' ?>';">
            <span class="venue-thumb-pill <?= $isDorm ? 'dorm-pill' : '' ?>"><?= htmlspecialchars($venueName) ?></span>
          </div>

          <div class="res-card-main">
            <div class="res-main-top">
              <div class="res-ref-group">
                <span class="res-ref-tag <?= $isDorm ? 'dorm-ref' : '' ?>"><?= htmlspecialchars($item['reference_no']) ?></span>
                <button type="button" class="btn-copy-ref" data-ref="<?= htmlspecialchars($item['reference_no']) ?>" title="Copy Reference Code">
                  <svg viewBox="0 0 24 24" fill="none">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                  </svg>
                </button>
                <span class="res-timestamp">&bull; <?= htmlspecialchars($statusPrefix) ?></span>
              </div>
              <div class="res-status-badge <?= htmlspecialchars($statusBadgeClass) ?>">
                <span class="status-dot-pulse"></span>
                <span><?= htmlspecialchars($statusBadgeText) ?></span>
              </div>
            </div>

            <h3 class="res-event-title"><?= htmlspecialchars($eventTitle) ?></h3>

            <div class="res-details-chips">
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span><?= htmlspecialchars($formattedDates) ?></span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span><?= htmlspecialchars($timeSlotText) ?></span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                </svg>
                <span><?= htmlspecialchars($venueTitleFull) ?></span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span><?= htmlspecialchars($paxText) ?> &bull; <?= htmlspecialchars($divisionText) ?></span>
              </div>
            </div>

            <?php if ($isCompleted): ?>
              <div class="res-concluded-summary">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= !empty($item['admin_remarks']) ? htmlspecialchars($item['admin_remarks']) : 'Activity successfully conducted. Facility inspection cleared &amp; venue turned over. Record permanently archived.' ?></span>
              </div>
            <?php else: ?>
              <div class="res-cancellation-notice">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span><strong><?= $isCancelled ? 'Cancellation Notice:' : 'Disapproval Notice:' ?></strong> <?= !empty($item['admin_remarks']) ? htmlspecialchars($item['admin_remarks']) : ($isCancelled ? 'Reservation was cancelled by the applicant. Schedule was released back to availability.' : 'Application was disapproved / returned during administrative review.') ?></span>
              </div>
            <?php endif; ?>
          </div>

          <div class="res-card-side-actions">
            <button type="button" class="btn-card-action secondary js-view-details">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
              </svg>
              <span>View Details</span>
            </button>
            <button type="button" class="btn-card-action secondary js-download-slip" data-ref="<?= htmlspecialchars($item['reference_no']) ?>" title="Download Record Slip">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span><?= $isCompleted ? 'Archived Slip' : 'Notice Slip' ?></span>
            </button>
          </div>
        </article>
        <?php endforeach; ?>

        <!-- ======================================================================
             ALTERNATIVE TABLE VIEW (TOGGLEABLE)
             ====================================================================== -->
        <div class="reservations-table-wrap" id="reservationsTableWrap" style="display: none;">
          <table class="res-table">
            <thead>
              <tr>
                <th>Ref &amp; Category</th>
                <th>Activity / Lodging Purpose</th>
                <th>Concluded Date &amp; Hours</th>
                <th>Archive Status</th>
                <th style="text-align: right;">Quick Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($historyList as $item): 
                $rawStatus = $item['status'] ?? 'Completed';
                $isCompleted = ($rawStatus === 'Completed');
                $isCancelled = ($rawStatus === 'Cancelled');
                $filterStatus = $isCompleted ? 'completed' : 'cancelled';
                $isDorm = (($item['facility_type'] ?? '') === 'dormitories') || !empty($item['room_id']);
                $category = $isDorm ? 'dormitory' : 'facility';
                $venueName = $item['facility_name'] ?: ($isDorm ? 'ATI Dormitory' : 'Official Venue');
                $venueTitleFull = $venueName . (!empty($item['room_number']) ? ' - Room ' . $item['room_number'] : '');
                $startTs = strtotime($item['start_date']);
                $endTs = strtotime($item['end_date'] ?: $item['start_date']);
                $formattedDates = ($startTs === $endTs) ? date('M d, Y', $startTs) : (date('M d', $startTs) . ' – ' . date('M d, Y', $endTs));
                $timeSlotText = $item['time_slot'] ?: '08:00 AM – 05:00 PM';
                $eventTitle = $item['event_title'] ?: 'Official Institutional Activity';
                $divisionText = $item['office_agency'] ?: 'ATI Personnel';
                $paxText = ($item['pax_count'] ?: 0) . ' Attendees';

                if ($isCompleted) {
                    $statusBadgeClass = 'completed';
                    $statusBadgeText = 'Completed & Concluded';
                } elseif ($isCancelled) {
                    $statusBadgeClass = 'cancelled';
                    $statusBadgeText = 'Cancelled by Requestor';
                } else {
                    $statusBadgeClass = 'cancelled';
                    $statusBadgeText = 'Disapproved / Returned';
                }
              ?>
              <tr class="res-table-row" 
                data-status="<?= htmlspecialchars($filterStatus) ?>" 
                data-raw-status="<?= htmlspecialchars($rawStatus) ?>" 
                data-category="<?= htmlspecialchars($category) ?>" 
                data-venue="<?= htmlspecialchars($isDorm ? 'dormitory' : 'facility') ?>"
                data-ref="<?= htmlspecialchars($item['reference_no']) ?>"
                data-venue-title="<?= htmlspecialchars($venueTitleFull) ?>"
                data-dates="<?= htmlspecialchars($formattedDates) ?>"
                data-time="<?= htmlspecialchars($timeSlotText) ?>"
                data-pax="<?= htmlspecialchars($paxText) ?>"
                data-division="<?= htmlspecialchars($divisionText) ?>"
                data-status-text="<?= htmlspecialchars($statusBadgeText) ?>"
                data-special-notes="<?= htmlspecialchars($item['special_notes'] ?? '') ?>"
                data-remarks="<?= htmlspecialchars($item['admin_remarks'] ?? '') ?>"
                data-doc-path="<?= htmlspecialchars($item['document_path'] ?? '') ?>">
                <td data-label="Ref & Venue">
                  <span class="res-ref-tag <?= $isDorm ? 'dorm-ref' : '' ?>"><?= htmlspecialchars($item['reference_no']) ?></span>
                  <div style="font-size: 0.82rem; font-weight: 700; color: #175432; margin-top: 0.2rem;"><?= htmlspecialchars($venueTitleFull) ?></div>
                </td>
                <td data-label="Activity Purpose">
                  <div style="font-weight: 800; color: #142e20;"><?= htmlspecialchars($eventTitle) ?></div>
                  <div style="font-size: 0.78rem; color: #556e5f;"><?= htmlspecialchars($divisionText) ?> &bull; <?= htmlspecialchars($paxText) ?></div>
                </td>
                <td data-label="Concluded Date">
                  <div style="font-size: 0.85rem; font-weight: 700; color: #173222;"><?= htmlspecialchars($formattedDates) ?></div>
                  <div style="font-size: 0.76rem; color: #617b6c;"><?= htmlspecialchars($timeSlotText) ?></div>
                </td>
                <td data-label="Archive Status">
                  <span class="res-status-badge <?= htmlspecialchars($statusBadgeClass) ?>"><span class="status-dot-pulse"></span><span><?= htmlspecialchars($statusBadgeText) ?></span></span>
                </td>
                <td style="text-align: right;" data-label="Actions">
                  <div style="display: inline-flex; gap: 0.4rem;">
                    <button type="button" class="btn-card-action secondary js-view-details">Details</button>
                    <button type="button" class="btn-card-action secondary js-download-slip" data-ref="<?= htmlspecialchars($item['reference_no']) ?>">Slip</button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- EMPTY STATE (SEARCH / FILTER MISMATCH) -->
      <div class="empty-reservations-box" id="emptyReservationsState" style="display: none;">
        <div class="empty-icon-wrap">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </div>
        <h3>No Booking History Found</h3>
        <p>No past reservation records match your selected filter criteria. Try adjusting the search query or status filter.</p>
        <button type="button" class="btn-reset-filters" id="btnResetFilters"
          onclick="document.getElementById('resSearchInput').value=''; document.querySelector('.my-res-stat-card[data-filter=\'all\']').click();">Reset All Filters</button>
      </div>

    </section>

  </main>

  <!-- ==========================================================================
       MODAL: RESERVATION DETAILS & OFFICIAL ARCHIVE ROUTING SLIP
       ========================================================================== -->
  <div class="modal-overlay" id="resDetailsModal" role="dialog" aria-modal="true">
    <div class="modal-card">
      <button type="button" class="modal-close-btn js-close-res-modal" aria-label="Close modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <div class="modal-header-banner">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
          <span style="font-size: 0.82rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; background: rgba(255, 255, 255, 0.2); padding: 0.25rem 0.75rem; border-radius: 9999px;"
            id="modalResVenue">Function Hall</span>
          <span class="res-status-badge" id="modalResStatusBadge"></span>
        </div>
        <h3 id="modalResTitle" style="font-size: 1.5rem; color: #ffffff; line-height: 1.3;">Activity Title</h3>
        <p style="font-size: 0.85rem; opacity: 0.85; margin-top: 0.35rem;">Reference Code: <strong id="modalResRef"
            style="color: #fde047;">ATI-RES-2026-0812</strong></p>
      </div>

      <div class="modal-content-area">
        <!-- Event Particulars Grid -->
        <div class="modal-meta-grid">
          <div class="modal-meta-item">
            <div class="modal-meta-label">Schedule & Duration</div>
            <div class="modal-meta-val" id="modalResDates">Sep 14 - 16, 2026</div>
          </div>
          <div class="modal-meta-item">
            <div class="modal-meta-label">Time Window</div>
            <div class="modal-meta-val" id="modalResTime">08:00 AM - 05:00 PM</div>
          </div>
          <div class="modal-meta-item">
            <div class="modal-meta-label">Requesting Unit / Division</div>
            <div class="modal-meta-val" id="modalResDivision">Partnership &amp; Accreditation Division</div>
          </div>
          <div class="modal-meta-item">
            <div class="modal-meta-label">Estimated Attendees</div>
            <div class="modal-meta-val" id="modalResPax">45 Participants</div>
          </div>
        </div>

        <!-- Attached Documents -->
        <div style="margin-bottom: 1.5rem;">
          <h4 style="font-size: 0.88rem; font-weight: 800; color: #174d2f; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
            Archived Official Documents</h4>
          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.85rem;">
            <div style="display: flex; align-items: center; gap: 0.65rem; background: #f7faf8; border: 1px solid #dce8e0; padding: 0.7rem 0.9rem; border-radius: 8px;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
              </svg>
              <div style="overflow: hidden;">
                <div style="font-size: 0.82rem; font-weight: 700; color: #1a2f22; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  Post_Activity_Clearance.pdf</div>
                <div style="font-size: 0.7rem; color: #627b6c;">1.4 MB &bull; Archived Copy</div>
              </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.65rem; background: #f7faf8; border: 1px solid #dce8e0; padding: 0.7rem 0.9rem; border-radius: 8px;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
              </svg>
              <div style="overflow: hidden;">
                <div style="font-size: 0.82rem; font-weight: 700; color: #1a2f22; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  Special_Order_Archived.pdf</div>
                <div style="font-size: 0.7rem; color: #627b6c;">520 KB &bull; Verified</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Official Routing Trail Card -->
        <div class="modal-routing-card">
          <div class="routing-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span>Official Government Action Routing Trail</span>
          </div>
          <div id="modalRoutingList">
            <!-- Dynamically populated via JS -->
          </div>
        </div>
      </div>

      <div class="modal-footer-bar">
        <button type="button" class="btn-modal-cancel js-close-res-modal">Close Window</button>
        <button type="button" class="btn-modal-print js-download-slip" id="modalBtnDownloadSlip">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          <span>Download Record Slip</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer" aria-live="polite"></div>

  <!-- JavaScript -->
  <script src="js/my_reservations.js?v=<?php echo time(); ?>"></script>
  <script>
    function toggleMobileDrawer(open) {
      var overlay = document.getElementById('mobileDrawerOverlay');
      var toggleBtn = document.getElementById('mobileMenuToggle');
      if (!overlay) return;
      if (open) {
        overlay.style.display = 'block';
        requestAnimationFrame(function () { overlay.classList.add('show'); });
        document.body.style.overflow = 'hidden';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
      } else {
        overlay.classList.remove('show');
        document.body.style.overflow = '';
        setTimeout(function () { overlay.style.display = 'none'; }, 260);
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
      }
    }
  </script>
</body>

</html>