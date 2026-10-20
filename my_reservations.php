<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth_guard.php';
/**
 * ATI Facility & Dormitory Reservation Portal
 * User Dashboard: My Reservations (Venues & Dormitories)
 */
$typeParam = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
if (in_array($typeParam, ['dorm', 'dormitory', 'dorms', 'booking', 'bookings'])) {
  $activeType = 'dormitory';
} elseif (in_array($typeParam, ['facility', 'facilities', 'halls', 'reservation', 'reservations'])) {
  $activeType = 'facility';
} else {
  $activeType = 'all';
}

$userId = $_SESSION['user_id'] ?? 1;

// Fetch reservations for the current user joined with facilities and rooms
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
          WHERE r.user_id = :user_id AND r.deleted_at IS NULL
          ORDER BY r.id DESC";

$stmt = $db->prepare($query);
$stmt->execute([':user_id' => $userId]);
$reservationsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch active facilities from database for dynamic dropdown filter
$facFilterStmt = $db->query("SELECT id, name, type FROM facilities WHERE status != 'inactive' AND deleted_at IS NULL ORDER BY name ASC");
$filterFacilities = $facFilterStmt ? $facFilterStmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Calculate dynamic stats
$statsTotal = count($reservationsList);
$statsPending = 0;
$statsApproved = 0;
$statsCompleted = 0;
$statsCancelled = 0;

foreach ($reservationsList as $resItem) {
    $rawSt = $resItem['status'] ?? 'Pending';
    if (in_array($rawSt, ['Pending', 'Pending Endorsement', 'Pending Recommendation', 'Pending Clearance'])) {
        $statsPending++;
    } elseif ($rawSt === 'Approved') {
        $statsApproved++;
    } elseif ($rawSt === 'Completed') {
        $statsCompleted++;
    } elseif ($rawSt === 'Cancelled') {
        $statsCancelled++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php
  if ($activeType === 'facility')
    echo 'My Facility Reservations | ATI Reservation Portal';
  elseif ($activeType === 'dormitory')
    echo 'My Booking History | ATI Reservation Portal';
  else
    echo 'My Reservations | ATI Reservation Portal';
  ?></title>
  <meta name="description"
    content="Manage and track your official facility reservations and dormitory room bookings for the Agricultural Training Institute.">

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

<body class="my-res-body">

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

        <!-- My Reservations - Active -->
        <a href="my_reservations.php" class="booking-nav-item active" title="My Reservations">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>My Reservations</span>
          <span class="nav-badge-count" id="navBadgeFacilityCount"><?= $statsTotal ?></span>
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
            <div class="user-avatar-circle"><?= getUserInitials($_SESSION['full_name']) ?></div>
            <div class="user-details">
              <div class="user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
              <div class="user-role"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role']))) ?></div>
            </div>
          </div>

          <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-header-info">
              <div class="dropdown-user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
              <div class="dropdown-user-email"><span class="user-verified-dot"></span> <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role']))) ?></div>
            </div>
            <a href="profile.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>My Profile</span>
            </a>
            <a href="booking_history.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
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
          <div class="drawer-avatar"><?= getUserInitials($_SESSION['full_name'] ?? 'Juan Dela Cruz') ?></div>
          <div class="drawer-profile-info">
            <h5><?= htmlspecialchars($_SESSION['full_name'] ?? 'Juan Dela Cruz') ?></h5>
            <p><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role'] ?? 'User'))) ?></p>
            <span class="drawer-badge">Verified Personnel</span>
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

          <a href="my_reservations.php" class="drawer-nav-link active">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
            </div>
            <span class="drawer-link-text">My Reservations</span>
            <span class="drawer-badge-count"><?= $statsTotal ?></span>
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

        <!-- Account Settings Section -->
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

  <script>
    function toggleMobileDrawer(open) {
      var overlay = document.getElementById('mobileDrawerOverlay');
      var toggleBtn = document.getElementById('mobileMenuToggle');
      if (!overlay) return;

      if (open) {
        overlay.style.display = 'block';
        void overlay.offsetHeight; // trigger reflow
        overlay.classList.add('show');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
      } else {
        overlay.classList.remove('show');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        setTimeout(function () {
          if (!overlay.classList.contains('show')) {
            overlay.style.display = 'none';
          }
        }, 280);
      }
    }

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        var overlay = document.getElementById('mobileDrawerOverlay');
        if (overlay && overlay.classList.contains('show')) {
          toggleMobileDrawer(false);
        }
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 900) {
        var overlay = document.getElementById('mobileDrawerOverlay');
        if (overlay && overlay.classList.contains('show')) {
          toggleMobileDrawer(false);
        }
      }
    });
  </script>

  <!-- ==========================================================================
       MAIN CONTENT
       ========================================================================== -->
  <main class="my-res-main-wrapper">
    <!-- Primary Category Segregation Switcher (Facility Reservations vs Booking History) -->
    <div class="history-category-segmented-bar">
      <div class="category-segmented-container">
        <!-- Tab 1: Active Reservations (Active) -->
        <a href="my_reservations.php" class="category-segment-btn active" title="View Active Reservations">
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
          <span class="seg-count-badge"><?= $statsPending + $statsApproved ?></span>
        </a>

        <!-- Tab 2: Booking History (Link to History Archive) -->
        <a href="booking_history.php" class="category-segment-btn" title="View Booking History">
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
          <span class="seg-count-badge blue"><?= $statsCompleted + $statsCancelled ?></span>
        </a>
      </div>
    </div>

    <!-- Header Section -->
    <section class="my-res-header">
      <div class="my-res-header-text">
        <div class="my-res-top-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span id="pageTopBadgeText">Official Reservations &amp; Venues</span>
        </div>
        <h2 id="pageHeadingTitle">My Reservations</h2>
        <p id="pageHeadingSubtext">Track the status of your active reservations, monitor live administrative routing
          clearances, download official slips, and access gate passes.</p>
      </div>

      <a href="booking.php?category=halls" class="btn-new-res-action">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Reserve Venue</span>
      </a>
    </section>

    <!-- Summary KPI Stat Cards -->
    <section class="my-res-stats-grid" aria-label="Reservation Summary Statistics">
      <!-- Total -->
      <div class="my-res-stat-card active-filter" data-filter="all">
        <div class="stat-icon-box green">
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiTotal"><?= $statsTotal ?></span>
          <span class="stat-label">Total Reservations</span>
        </div>
      </div>

      <!-- Pending -->
      <div class="my-res-stat-card" data-filter="pending">
        <div class="stat-icon-box amber">
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiPending"><?= $statsPending ?></span>
          <span class="stat-label">Pending Clearance</span>
        </div>
      </div>

      <!-- Confirmed -->
      <div class="my-res-stat-card" data-filter="approved">
        <div class="stat-icon-box blue">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiApproved"><?= $statsApproved ?></span>
          <span class="stat-label">Confirmed &amp; Active</span>
        </div>
      </div>

      <!-- Completed -->
      <div class="my-res-stat-card" data-filter="completed">
        <div class="stat-icon-box slate">
          <svg viewBox="0 0 24 24" fill="none">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiCompleted"><?= $statsCompleted ?></span>
          <span class="stat-label">Completed Events</span>
        </div>
      </div>
    </section>

    <!-- Filter & Search Toolbar (Cleaned, Non-Redundant, Custom Styled) -->
    <div class="my-res-toolbar-card">
      <!-- Search Input (Prominent & Responsive) -->
      <div class="search-box-wrap">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="resSearchInput" class="search-input" placeholder="Search ref, title, or division...">
        <button type="button" class="btn-clear-search" id="btnClearSearch" title="Clear search" style="display: none;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Controls: Custom Facility Dropdown & View Switcher -->
      <div class="toolbar-controls-right">
        <!-- Custom Styled Facility Dropdown (No ugly OS select menu) -->
        <div class="custom-facility-dropdown" id="facilityDropdown">
          <button type="button" class="facility-dropdown-btn" id="facilityDropdownBtn" aria-haspopup="listbox"
            aria-expanded="false" title="Filter by Facility">
            <span class="facility-btn-icon">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
            </span>
            <span class="facility-btn-label" id="facilityDropdownLabel">All Facilities</span>
            <svg class="facility-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="2.2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>

          <div class="facility-dropdown-menu" id="facilityDropdownMenu" role="listbox" aria-label="Filter by Facility">
            <div class="facility-option active" data-value="all" role="option" aria-selected="true">
              <span class="opt-bullet all"></span>
              <span class="opt-name">All Facilities</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>

            <div class="facility-option" data-value="facility" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">All Function &amp; Training Halls</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>

            <div class="facility-option" data-value="dormitory" role="option">
              <span class="opt-bullet dorm-bullet"></span>
              <span class="opt-name">All Dormitories &amp; Lodgings</span>
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
         RESERVATIONS CONTAINER (CARDS VIEW & TABLE VIEW)
         ========================================================================== -->
    <section class="reservations-container" id="reservationsList">

      <?php if (empty($reservationsList)): ?>
        <!-- Shown if user has zero total reservations in database -->
        <div class="empty-res-state" id="zeroReservationsState" style="display: block; width: 100%; text-align: center; padding: 3.5rem 1.5rem; background: #fff; border-radius: 14px; border: 1.5px dashed #d0ded4;">
          <div class="empty-res-icon" style="margin: 0 auto 1.25rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#175432" width="48" height="48" stroke-width="1.8">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <h3 style="font-size: 1.35rem; font-weight: 800; color: #173822; margin-bottom: 0.5rem;">No Reservations Yet</h3>
          <p style="color: #557262; max-width: 460px; margin: 0 auto 1.5rem; font-size: 0.92rem;">You haven't submitted any reservation requests yet. Click below to book a training hall, function venue, or dormitory lodging.</p>
          <a href="booking.php" class="btn-new-res-action" style="display: inline-flex; text-decoration: none;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Create First Reservation</span>
          </a>
        </div>
      <?php else: ?>

        <?php foreach ($reservationsList as $item): 
          $statusLower = strtolower($item['status'] ?? 'pending');
          $isDorm = ($item['facility_type'] === 'dormitories') || !empty($item['room_id']);
          $category = $isDorm ? 'dormitory' : 'facility';
          
          $venueName = $item['facility_name'] ?? 'Facility';
          $facilityImgRaw = trim($item['facility_image'] ?? '');
          
          $slug = 'function-hall';
          if (stripos($venueName, 'boardroom') !== false) {
              $slug = 'boardroom';
          } elseif (stripos($venueName, 'training') !== false) {
              $slug = 'training-hall';
          } elseif (stripos($venueName, 'mess') !== false) {
              $slug = 'mess-hall';
          } elseif (stripos($venueName, 'serrano') !== false) {
              $slug = 'serrano-hall';
          } elseif ($isDorm) {
              $slug = 'dormitory';
          }

          // Prioritize actual facility image from database
          $venueThumb = '';
          if (!empty($facilityImgRaw)) {
              if (file_exists(__DIR__ . '/' . ltrim($facilityImgRaw, '/\\'))) {
                  $venueThumb = ltrim($facilityImgRaw, '/\\');
              } elseif (file_exists(__DIR__ . '/assets/images/' . ltrim($facilityImgRaw, '/\\'))) {
                  $venueThumb = 'assets/images/' . ltrim($facilityImgRaw, '/\\');
              } elseif (str_starts_with($facilityImgRaw, 'http://') || str_starts_with($facilityImgRaw, 'https://')) {
                  $venueThumb = $facilityImgRaw;
              } else {
                  $venueThumb = $facilityImgRaw;
              }
          }

          // Fallback if no uploaded or custom image exists
          if (empty($venueThumb)) {
              if (stripos($venueName, 'boardroom') !== false) {
                  $venueThumb = 'assets/images/boardroom.jpg';
              } elseif (stripos($venueName, 'training') !== false) {
                  $venueThumb = 'assets/images/training_hall.jpg';
              } elseif (stripos($venueName, 'mess') !== false) {
                  $venueThumb = 'assets/images/mess_hall.jpg';
              } elseif (stripos($venueName, 'serrano') !== false) {
                  $venueThumb = 'assets/images/serrano_hall.jpg';
              } elseif ($isDorm) {
                  $venueThumb = 'assets/images/dormitory.jpg';
              } else {
                  $venueThumb = 'assets/images/function_hall.jpg';
              }
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
          $createdDateText = date('M d, Y', strtotime($item['created_at']));

          $rawStatus = $item['status'] ?? 'Pending';
          $statusLower = strtolower($rawStatus);
          
          $filterStatus = 'pending';
          if ($rawStatus === 'Approved') {
              $filterStatus = 'approved';
              $statusBadgeClass = 'approved';
              $statusBadgeText = 'Confirmed & Approved';
          } elseif ($rawStatus === 'Completed') {
              $filterStatus = 'completed';
              $statusBadgeClass = 'completed';
              $statusBadgeText = 'Completed & Archived';
          } elseif ($rawStatus === 'Cancelled') {
              $filterStatus = 'cancelled';
              $statusBadgeClass = 'cancelled';
              $statusBadgeText = 'Cancelled by Requestor';
          } elseif ($rawStatus === 'Rejected') {
              $filterStatus = 'rejected';
              $statusBadgeClass = 'rejected';
              $statusBadgeText = 'Disapproved / Returned';
          } elseif ($rawStatus === 'Pending Recommendation') {
              $statusBadgeClass = 'pending recommendation';
              $statusBadgeText = 'Stage 2: Awaiting Recommendation';
          } elseif ($rawStatus === 'Pending Clearance') {
              $statusBadgeClass = 'pending clearance';
              $statusBadgeText = 'Stage 3: Awaiting Clearance';
          } else {
              $statusBadgeClass = 'pending endorsement';
              $statusBadgeText = 'Stage 1: Awaiting Endorsement';
          }

          // Compute 3-Stage Governance Stepper States
          $s1Done = in_array($rawStatus, ['Pending Recommendation', 'Pending Clearance', 'Approved', 'Completed']) || !empty($item['endorsed_at']);
          $s2Done = in_array($rawStatus, ['Pending Clearance', 'Approved', 'Completed']) || !empty($item['recommended_at']);
          $s3Done = in_array($rawStatus, ['Approved', 'Completed']) || !empty($item['cleared_at']);

          $isRejected = ($rawStatus === 'Rejected');
          $isCancelled = ($rawStatus === 'Cancelled');

          if ($isRejected) {
              if ($s2Done) {
                  $step1State = 'done';
                  $step2State = 'done';
                  $step3State = 'rejected';
                  $currentStageLabel = 'Stage 3: Disapproved';
                  $currentStageColor = '#dc2626';
              } elseif ($s1Done) {
                  $step1State = 'done';
                  $step2State = 'rejected';
                  $step3State = 'pending';
                  $currentStageLabel = 'Stage 2: Returned';
                  $currentStageColor = '#dc2626';
              } else {
                  $step1State = 'rejected';
                  $step2State = 'pending';
                  $step3State = 'pending';
                  $currentStageLabel = 'Stage 1: Returned';
                  $currentStageColor = '#dc2626';
              }
          } elseif ($isCancelled) {
              $step1State = $s1Done ? 'done' : 'cancelled';
              $step2State = $s2Done ? 'done' : 'cancelled';
              $step3State = $s3Done ? 'done' : 'cancelled';
              $currentStageLabel = 'Request Cancelled';
              $currentStageColor = '#64748b';
          } elseif ($rawStatus === 'Approved') {
              $step1State = 'done';
              $step2State = 'done';
              $step3State = 'done';
              $currentStageLabel = 'Governance Clearance Complete';
              $currentStageColor = '#16a34a';
          } elseif ($rawStatus === 'Completed') {
              $step1State = 'done';
              $step2State = 'done';
              $step3State = 'done';
              $currentStageLabel = 'Concluded & Archived';
              $currentStageColor = '#475569';
          } elseif ($rawStatus === 'Pending Clearance') {
              $step1State = 'done';
              $step2State = 'done';
              $step3State = 'active';
              $currentStageLabel = 'Stage 3: Directorate Clearance Active';
              $currentStageColor = '#047857';
          } elseif ($rawStatus === 'Pending Recommendation') {
              $step1State = 'done';
              $step2State = 'active';
              $step3State = 'pending';
              $currentStageLabel = 'Stage 2: Logistics Recommendation Active';
              $currentStageColor = '#2563eb';
          } else {
              $step1State = 'active';
              $step2State = 'pending';
              $step3State = 'pending';
              $currentStageLabel = 'Stage 1: Division Endorsement Active';
              $currentStageColor = '#d97706';
          }
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
          data-venue-title="<?= htmlspecialchars($venueName . (!empty($item['room_number']) ? ' - Room ' . $item['room_number'] : '')) ?>" 
          data-venue-image="<?= htmlspecialchars($venueThumb) ?>"
          data-dates="<?= htmlspecialchars($formattedDates) ?>"
          data-time="<?= htmlspecialchars($timeSlotText) ?>" 
          data-pax="<?= htmlspecialchars($paxText) ?>"
          data-division="<?= htmlspecialchars($divisionText) ?>" 
          data-applicant-name="<?= htmlspecialchars($item['full_name'] ?? 'Applicant') ?>"
          data-created-at="<?= htmlspecialchars(!empty($item['created_at']) ? date('M d, Y • h:i A', strtotime($item['created_at'])) : 'Recently Submitted') ?>"
          data-status-text="<?= htmlspecialchars($statusBadgeText) ?>"
          data-doc-path="<?= htmlspecialchars($item['document_path'] ?? '') ?>"
          data-special-notes="<?= htmlspecialchars($item['special_notes'] ?? '') ?>"
          data-remarks="<?= htmlspecialchars($item['admin_remarks'] ?? '') ?>"
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
                <span class="res-timestamp">&bull; <?= htmlspecialchars($createdDateText) ?></span>
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
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span><?= htmlspecialchars($paxText) ?></span>
              </div>
              <div class="res-chip-item">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                </svg>
                <span><?= htmlspecialchars($divisionText) ?></span>
              </div>
            </div>

            <!-- 3-STAGE GOVERNANCE ROUTING TRACKER -->
            <div class="res-mini-routing">
              <div class="routing-stepper-header">
                <div class="stepper-header-left">
                  <span class="stepper-badge-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                  </span>
                  <span class="stepper-title">GOVERNANCE ROUTING TRACKER</span>
                </div>
                <span class="stepper-stage-chip" style="color: <?= $currentStageColor ?>; border-color: <?= $currentStageColor ?>44; background: <?= $currentStageColor ?>14;">
                  <?= htmlspecialchars($currentStageLabel) ?>
                </span>
              </div>

              <div class="routing-stepper-track">
                <!-- STEP 1: Endorsement -->
                <div class="stepper-step step-<?= $step1State ?>" title="Stage 1: Division Chief Endorsement">
                  <div class="step-circle">
                    <?php if ($step1State === 'done'): ?>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <?php elseif ($step1State === 'active'): ?>
                      <span class="step-pulse-dot"></span>
                      <span class="step-num">1</span>
                    <?php elseif ($step1State === 'rejected'): ?>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <?php else: ?>
                      <span class="step-num">1</span>
                    <?php endif; ?>
                  </div>
                  <div class="step-labels">
                    <span class="step-name">1. Endorsement</span>
                    <span class="step-desc">Division Chief</span>
                  </div>
                </div>

                <div class="stepper-connector <?= $step1State === 'done' ? 'completed' : ($step2State === 'active' ? 'active' : '') ?>"></div>

                <!-- STEP 2: Recommendation -->
                <div class="stepper-step step-<?= $step2State ?>" title="Stage 2: Property & Logistics Recommendation">
                  <div class="step-circle">
                    <?php if ($step2State === 'done'): ?>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <?php elseif ($step2State === 'active'): ?>
                      <span class="step-pulse-dot"></span>
                      <span class="step-num">2</span>
                    <?php elseif ($step2State === 'rejected'): ?>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <?php else: ?>
                      <span class="step-num">2</span>
                    <?php endif; ?>
                  </div>
                  <div class="step-labels">
                    <span class="step-name">2. Recommendation</span>
                    <span class="step-desc">Logistics &amp; Property</span>
                  </div>
                </div>

                <div class="stepper-connector <?= $step2State === 'done' ? 'completed' : ($step3State === 'active' ? 'active' : '') ?>"></div>

                <!-- STEP 3: Clearance -->
                <div class="stepper-step step-<?= $step3State ?>" title="Stage 3: Directorate Final Clearance">
                  <div class="step-circle">
                    <?php if ($step3State === 'done'): ?>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <?php elseif ($step3State === 'active'): ?>
                      <span class="step-pulse-dot"></span>
                      <span class="step-num">3</span>
                    <?php elseif ($step3State === 'rejected'): ?>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <?php else: ?>
                      <span class="step-num">3</span>
                    <?php endif; ?>
                  </div>
                  <div class="step-labels">
                    <span class="step-name">3. Clearance</span>
                    <span class="step-desc">Directorate IV</span>
                  </div>
                </div>
              </div>

              <!-- Contextual Action Note -->
              <div class="routing-status-label">
                <?php if ($rawStatus === 'Approved'): ?>
                  <span class="routing-step-desc"><strong style="color: #15803d;">Governance Complete:</strong> Approved on Master Schedule &bull; Security Gate Pass ready</span>
                <?php elseif ($rawStatus === 'Cancelled'): ?>
                  <span class="routing-step-desc"><strong style="color: #64748b;">Notice:</strong> Booking request cancelled by applicant</span>
                <?php elseif ($rawStatus === 'Completed'): ?>
                  <span class="routing-step-desc"><strong style="color: #475569;">Concluded:</strong> Event successfully executed &bull; Turn-over complete</span>
                <?php elseif ($rawStatus === 'Rejected'): ?>
                  <span class="routing-step-desc"><strong style="color: #b91c1c;">Notice:</strong> Disapproved or returned for revision (click View Details for feedback)</span>
                <?php elseif ($rawStatus === 'Pending Clearance'): ?>
                  <span class="routing-step-desc"><strong style="color: #047857;">Under Review:</strong> Endorsed &amp; Recommended &bull; Forwarded to Director IV for Final Clearance</span>
                <?php elseif ($rawStatus === 'Pending Recommendation'): ?>
                  <span class="routing-step-desc"><strong style="color: #2563eb;">Under Review:</strong> Endorsed by Division &bull; Property &amp; Facilities Unit inspecting logistics</span>
                <?php else: ?>
                  <span class="routing-step-desc"><strong style="color: #b45309;">Under Review:</strong> Awaiting Originating Division Chief endorsement approval</span>
                <?php endif; ?>

                <?php if (!empty($item['document_path'])): ?>
                  <span class="routing-note">Attachment: <a href="<?= htmlspecialchars($item['document_path']) ?>" target="_blank" style="color: #174d2f; font-weight: 700; text-decoration: underline;">View Upload</a></span>
                <?php endif; ?>
              </div>
            </div>

            <?php if ($rawStatus === 'Rejected'): ?>
              <!-- Prominent Decline / Return Notice Alert Box -->
              <div class="res-decline-card-alert">
                <div class="decline-alert-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                  </svg>
                </div>
                <div class="decline-alert-body">
                  <div class="decline-alert-header">
                    <strong class="decline-alert-title">Notice of Disapproval / Return:</strong>
                    <span class="decline-alert-badge">Action Required</span>
                  </div>
                  <p class="decline-alert-text">
                    <?= !empty($item['admin_remarks']) ? nl2br(htmlspecialchars($item['admin_remarks'])) : 'This reservation request was returned or disapproved on official administrative grounds. Please view details or contact administration.' ?>
                  </p>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <div class="res-card-side-actions">
            <?php if ($statusLower === 'approved'): ?>
              <button type="button" class="btn-card-action primary"
                onclick="showGatePassModal('<?= htmlspecialchars($item['reference_no']) ?>', '<?= htmlspecialchars(addslashes($eventTitle)) ?>', '<?= htmlspecialchars(addslashes($venueName)) ?>', '<?= htmlspecialchars(addslashes($formattedDates)) ?>')">
                <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                <span>Pass &amp; QR</span>
              </button>
            <?php endif; ?>
            <button type="button" class="btn-card-action secondary js-view-details">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
              </svg>
              <span>View Details</span>
            </button>
            <button type="button" class="btn-card-action secondary js-download-slip" data-ref="<?= htmlspecialchars($item['reference_no']) ?>">
              <svg viewBox="0 0 24 24" fill="none">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
              </svg>
              <span>Print Slip</span>
            </button>
            <?php if ($statusLower === 'pending' || $statusLower === 'approved'): ?>
              <button type="button" class="btn-card-action outline-danger js-cancel-res">
                <svg viewBox="0 0 24 24" fill="none">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                <span>Cancel</span>
              </button>
            <?php endif; ?>
          </div>
        </article>
        <?php endforeach; ?>

      <?php endif; ?>

      <!-- ALTERNATIVE TABLE VIEW (TOGGLEABLE) -->
      <div class="reservations-table-wrap" id="reservationsTableWrap" style="display: none;">
        <table class="res-table">
          <thead>
            <tr>
              <th>Ref &amp; Facility</th>
              <th>Activity Title &amp; Unit</th>
              <th>Schedule Window</th>
              <th>Routing Status</th>
              <th style="text-align: right;">Quick Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservationsList as $item): 
              $rawStatus = $item['status'] ?? 'Pending';
              $statusLower = strtolower($rawStatus);
              
              $filterStatus = 'pending';
              if ($rawStatus === 'Approved') {
                  $filterStatus = 'approved';
                  $statusBadgeClass = 'approved';
                  $statusBadgeText = 'Confirmed & Approved';
              } elseif ($rawStatus === 'Completed') {
                  $filterStatus = 'completed';
                  $statusBadgeClass = 'completed';
                  $statusBadgeText = 'Completed & Archived';
              } elseif ($rawStatus === 'Cancelled') {
                  $filterStatus = 'cancelled';
                  $statusBadgeClass = 'cancelled';
                  $statusBadgeText = 'Cancelled by Requestor';
              } elseif ($rawStatus === 'Rejected') {
                  $filterStatus = 'rejected';
                  $statusBadgeClass = 'rejected';
                  $statusBadgeText = 'Disapproved / Returned';
              } elseif ($rawStatus === 'Pending Recommendation') {
                  $statusBadgeClass = 'pending recommendation';
                  $statusBadgeText = 'Stage 2: Recommendation';
              } elseif ($rawStatus === 'Pending Clearance') {
                  $statusBadgeClass = 'pending clearance';
                  $statusBadgeText = 'Stage 3: Clearance';
              } else {
                  $statusBadgeClass = 'pending endorsement';
                  $statusBadgeText = 'Stage 1: Endorsement';
              }

              $isDorm = ($item['facility_type'] === 'dormitories') || !empty($item['room_id']);
              $category = $isDorm ? 'dormitory' : 'facility';
              $venueName = $item['facility_name'] ?? 'Facility';
              $slug = 'function-hall';
              if (stripos($venueName, 'boardroom') !== false) $slug = 'boardroom';
              elseif (stripos($venueName, 'training') !== false) $slug = 'training-hall';
              elseif (stripos($venueName, 'mess') !== false) $slug = 'mess-hall';
              elseif ($isDorm) $slug = 'dormitory';

              $startTs = strtotime($item['start_date']);
              $endTs = strtotime($item['end_date'] ?: $item['start_date']);
              $formattedDates = ($startTs === $endTs) ? date('M d, Y', $startTs) : date('M d', $startTs) . ' – ' . date('M d, Y', $endTs);
              $eventTitle = $item['event_title'] ?: 'Official Institutional Activity';
              $divisionText = $item['office_agency'] ?: 'ATI Personnel';
              $paxText = ($item['pax_count'] ?: 0) . ' Pax';
              $timeSlotText = $item['time_slot'] ?: '08:00 AM – 05:00 PM';
            ?>
            <tr class="res-table-row" 
                data-status="<?= htmlspecialchars($filterStatus) ?>" 
                data-raw-status="<?= htmlspecialchars($rawStatus) ?>" 
                data-category="<?= htmlspecialchars($category) ?>" 
                data-venue="<?= htmlspecialchars($slug) ?>"
                data-ref="<?= htmlspecialchars($item['reference_no']) ?>">
              <td>
                <span class="res-ref-tag <?= $isDorm ? 'dorm-ref' : '' ?>"><?= htmlspecialchars($item['reference_no']) ?></span>
                <div style="font-size: 0.82rem; font-weight: 700; color: #174d2f; margin-top: 0.2rem;"><?= htmlspecialchars($venueName) ?></div>
              </td>
              <td>
                <div style="font-weight: 800; color: #142e20;"><?= htmlspecialchars($eventTitle) ?></div>
                <div style="font-size: 0.78rem; color: #556e5f;"><?= htmlspecialchars($divisionText) ?> &bull; <?= htmlspecialchars($paxText) ?></div>
              </td>
              <td>
                <div style="font-size: 0.85rem; font-weight: 700; color: #173222;"><?= htmlspecialchars($formattedDates) ?></div>
                <div style="font-size: 0.76rem; color: #617b6c;"><?= htmlspecialchars($timeSlotText) ?></div>
              </td>
              <td>
                <span class="res-status-badge <?= htmlspecialchars($statusBadgeClass) ?>">
                  <span class="status-dot-pulse"></span>
                  <span><?= htmlspecialchars($statusBadgeText) ?></span>
                </span>
                <?php if ($rawStatus === 'Rejected' && !empty($item['admin_remarks'])): ?>
                  <div class="table-decline-note" title="<?= htmlspecialchars($item['admin_remarks']) ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span><?= htmlspecialchars($item['admin_remarks']) ?></span>
                  </div>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <?php if ($statusLower === 'approved'): ?>
                    <button type="button" class="btn-card-action primary"
                      onclick="showGatePassModal('<?= htmlspecialchars($item['reference_no']) ?>', '<?= htmlspecialchars(addslashes($eventTitle)) ?>', '<?= htmlspecialchars(addslashes($venueName)) ?>', '<?= htmlspecialchars(addslashes($formattedDates)) ?>')">Pass</button>
                  <?php endif; ?>
                  <button type="button" class="btn-card-action secondary js-view-details">Details</button>
                  <button type="button" class="btn-card-action secondary js-download-slip" data-ref="<?= htmlspecialchars($item['reference_no']) ?>">Slip</button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Empty State Result (Filters / Search) -->
    <div class="empty-res-state" id="emptyResState" style="display: none;">
      <div class="empty-res-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </div>
      <h3>No Reservations Found</h3>
      <p>We couldn't find any reservation requests matching your selected filter criteria. Try adjusting your search query or venue selection.</p>
      <button type="button" class="btn-new-res-action" id="btnResetResFilters">
        <span>Reset Filters</span>
      </button>
    </div>
  </main>

  <!-- ==========================================================================
       MODAL 1: VIEW DETAILS & OFFICIAL ROUTING SLIP
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
        <div
          style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
          <span
            style="font-size: 0.82rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; background: rgba(255, 255, 255, 0.2); padding: 0.25rem 0.75rem; border-radius: 9999px;"
            id="modalResVenue">Function Hall</span>
          <span class="res-status-badge" id="modalResStatusBadge"></span>
        </div>
        <h3 id="modalResTitle" style="font-size: 1.5rem; color: #ffffff; line-height: 1.3;">Event Title</h3>
        <p style="font-size: 0.85rem; opacity: 0.85; margin-top: 0.35rem;">Reference Code: <strong id="modalResRef"
            style="color: #fde047;">ATI-RES-2026-1042</strong></p>
      </div>

      <div class="modal-content-area">
        <!-- Event Particulars Grid -->
        <div class="modal-meta-grid">
          <div class="modal-meta-item">
            <div class="modal-meta-label">Schedule & Duration</div>
            <div class="modal-meta-val" id="modalResDates">Oct 14 - 16, 2026</div>
          </div>
          <div class="modal-meta-item">
            <div class="modal-meta-label">Time Window</div>
            <div class="modal-meta-val" id="modalResTime">08:00 AM - 05:00 PM</div>
          </div>
          <div class="modal-meta-item">
            <div class="modal-meta-label">Requesting Unit / Division</div>
            <div class="modal-meta-val" id="modalResDivision">Career Development Division</div>
          </div>
          <div class="modal-meta-item">
            <div class="modal-meta-label">Estimated Attendees</div>
            <div class="modal-meta-val" id="modalResPax">120 Delegates</div>
          </div>
        </div>

        <!-- Attached Documents -->
        <div style="margin-bottom: 1.5rem;" id="modalDocContainer">
          <h4
            style="font-size: 0.88rem; font-weight: 800; color: #174d2f; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
            Attached Supporting Documents</h4>
          <div id="modalDocAttachment" style="display: flex; align-items: center; gap: 0.65rem; background: #f7faf8; border: 1.5px solid #dce8e0; padding: 0.75rem 1rem; border-radius: 8px;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            <div style="overflow: hidden; flex: 1;">
              <div id="modalDocFileName" style="font-size: 0.85rem; font-weight: 700; color: #1a2f22; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                No document attached
              </div>
              <div id="modalDocMeta" style="font-size: 0.72rem; color: #627b6c;">Official Request Letter / Attachment</div>
            </div>
            <a href="#" id="modalDocLink" target="_blank" class="btn-card-action secondary" style="padding: 0.35rem 0.75rem; font-size: 0.78rem;">
              View Document
            </a>
          </div>
        </div>

        <!-- Special Notes / Administrative Remarks if any -->
        <div id="modalNotesSection" style="margin-bottom: 1.5rem; display: none; background: #f9fbf9; border-left: 3px solid #175432; padding: 0.85rem 1rem; border-radius: 6px;">
          <div style="font-size: 0.75rem; font-weight: 800; color: #175432; text-transform: uppercase; margin-bottom: 0.25rem;">Special Notes & Logistics</div>
          <div id="modalNotesContent" style="font-size: 0.85rem; color: #2e4738;"></div>
        </div>
        <div id="modalRemarksSection" style="margin-bottom: 1.5rem; display: none; background: #fefce8; border-left: 3px solid #eab308; padding: 0.85rem 1rem; border-radius: 6px;">
          <div style="font-size: 0.75rem; font-weight: 800; color: #854d0e; text-transform: uppercase; margin-bottom: 0.25rem;">Admin Remarks & Feedback</div>
          <div id="modalRemarksContent" style="font-size: 0.85rem; color: #713f12;"></div>
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

        <!-- Modal Footer Actions -->
        <div class="modal-footer-actions">
          <button type="button" class="btn-card-action secondary js-close-res-modal">Close</button>
          <button type="button" class="btn-card-action primary js-download-slip"
            onclick="showToast('Printing official routing pass...');">
            <svg viewBox="0 0 24 24" fill="none">
              <polyline points="6 9 6 2 18 2 18 9"></polyline>
              <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
              <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span>Print Official Slip</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 2: OFFICIAL GATE PASS & QR CODE
       ========================================================================== -->
  <div class="modal-overlay" id="gatePassModal" role="dialog" aria-modal="true">
    <div class="modal-card" style="max-width: 540px;">
      <button type="button" class="modal-close-btn js-close-res-modal" aria-label="Close modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <div class="modal-header-banner" style="text-align: center; padding: 1.75rem;">
        <span
          style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; background: rgba(255, 255, 255, 0.2); padding: 0.25rem 0.85rem; border-radius: 9999px;">ATI
          Security & Venue Access Pass</span>
        <h3 style="font-size: 1.35rem; color: #ffffff; margin-top: 0.5rem;" id="gpVenueName">Executive Boardroom</h3>
        <p style="font-size: 0.85rem; opacity: 0.88;" id="gpEventTitle">Executive Directorate Quarterly Planning Session
        </p>
      </div>

      <div class="modal-content-area" style="padding: 1.75rem;">
        <div class="gatepass-qr-card">
          <!-- Stylized QR Code SVG -->
          <div class="qr-code-box">
            <svg viewBox="0 0 100 100" fill="#174d2f">
              <rect x="0" y="0" width="30" height="30" rx="3" fill="#174d2f"></rect>
              <rect x="5" y="5" width="20" height="20" rx="2" fill="#ffffff"></rect>
              <rect x="10" y="10" width="10" height="10" rx="1" fill="#174d2f"></rect>

              <rect x="70" y="0" width="30" height="30" rx="3" fill="#174d2f"></rect>
              <rect x="75" y="5" width="20" height="20" rx="2" fill="#ffffff"></rect>
              <rect x="80" y="10" width="10" height="10" rx="1" fill="#174d2f"></rect>

              <rect x="0" y="70" width="30" height="30" rx="3" fill="#174d2f"></rect>
              <rect x="5" y="75" width="20" height="20" rx="2" fill="#ffffff"></rect>
              <rect x="10" y="80" width="10" height="10" rx="1" fill="#174d2f"></rect>

              <!-- Central QR matrices -->
              <rect x="36" y="8" width="8" height="8" rx="1"></rect>
              <rect x="48" y="16" width="8" height="8" rx="1"></rect>
              <rect x="36" y="24" width="8" height="8" rx="1"></rect>
              <rect x="40" y="40" width="20" height="20" rx="2"></rect>
              <rect x="12" y="44" width="8" height="8" rx="1"></rect>
              <rect x="24" y="44" width="8" height="8" rx="1"></rect>
              <rect x="68" y="44" width="8" height="8" rx="1"></rect>
              <rect x="80" y="44" width="8" height="8" rx="1"></rect>
              <rect x="36" y="68" width="8" height="8" rx="1"></rect>
              <rect x="48" y="76" width="8" height="8" rx="1"></rect>
              <rect x="68" y="68" width="8" height="8" rx="1"></rect>
              <rect x="80" y="80" width="8" height="8" rx="1"></rect>
            </svg>
          </div>

          <div
            style="font-family: monospace; font-size: 1.1rem; font-weight: 800; color: #174d2f; letter-spacing: 0.05em;"
            id="gpRefCode">ATI-RES-2026-0985</div>
          <div style="font-size: 0.78rem; color: #556c5e; margin-top: 0.2rem;">Scan upon arrival at Central Office
            Guardhouse / Venue Entrance</div>
        </div>

        <div
          style="background: #edf8f1; padding: 0.9rem 1.1rem; border-radius: 10px; border-left: 3px solid #175432; font-size: 0.82rem; color: #284433; margin-bottom: 1.25rem;">
          <strong>Authorized Schedule:</strong> <span id="gpEventDates">Oct 08, 2026</span><br>
          <em>Please present this digital pass or printed copy along with your official government ID.</em>
        </div>

        <div class="modal-footer-actions">
          <button type="button" class="btn-card-action secondary js-close-res-modal">Close</button>
          <button type="button" class="btn-card-action primary" onclick="showToast('Gate pass saved to device.');">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Save / Download Pass</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 3: CANCEL RESERVATION CONFIRMATION
       ========================================================================== -->
  <div class="modal-overlay" id="cancelConfirmModal" role="dialog" aria-modal="true">
    <div class="modal-card" style="max-width: 500px; position: relative;">
      <button type="button" class="modal-close-btn js-close-res-modal" aria-label="Close modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <div style="padding: 1.75rem 2rem;">
        <div
          style="width: 52px; height: 52px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
          </svg>
        </div>

        <h3 style="font-size: 1.3rem; font-weight: 800; color: #182c20; margin-bottom: 0.5rem;">Cancel Reservation
          Request?</h3>
        <p style="font-size: 0.88rem; color: #556c5e; line-height: 1.45; margin-bottom: 1.25rem;">
          Are you sure you want to cancel request <strong id="cancelResRef"
            style="color: #174d2f;">ATI-RES-2026-1042</strong> for <span id="cancelResTitle">this activity</span>? This
          will release the scheduled slot back to the master availability board.
        </p>

        <div style="margin-bottom: 1.5rem;">
          <label for="cancelReasonSelect"
            style="display: block; font-size: 0.78rem; font-weight: 800; color: #3b5042; text-transform: uppercase; margin-bottom: 0.4rem;">Reason
            for Cancellation</label>
          <select id="cancelReasonSelect"
            style="width: 100%; padding: 0.7rem 0.9rem; border: 1.5px solid #d5e2d8; border-radius: 8px; font-family: inherit; font-size: 0.88rem; color: #192b21; outline: none;">
            <option value="Schedule adjustment / postponement">Schedule adjustment / postponed by division</option>
            <option value="Change of preferred venue or facility">Change of preferred venue / room size</option>
            <option value="Alternative online / virtual platform used">Event shifted to virtual (Zoom/Teams)</option>
            <option value="Other administrative directives">Other official administrative directive</option>
          </select>
        </div>

        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
          <button type="button" class="btn-card-action secondary js-close-res-modal">Keep Reservation</button>
          <button type="button" class="btn-card-action outline-danger" id="btnConfirmCancel"
            style="background: #dc2626; color: #ffffff; border-color: #dc2626;">
            <span>Confirm Cancellation</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL 4: OFFICIAL RESERVATION SLIP (PRINTABLE SHEET)
       ========================================================================== -->
  <div class="modal-overlay" id="slipModal" role="dialog" aria-modal="true">
    <div class="modal-card" style="max-width: 780px;">
      <button type="button" class="modal-close-btn js-close-res-modal" aria-label="Close modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <div class="modal-content-area" style="padding: 2rem;">
        <!-- Printable Document Container -->
        <div id="printableSlipArea" style="background: #ffffff; color: #111827; font-family: 'Plus Jakarta Sans', sans-serif;">
          <!-- Slip Header -->
          <div style="text-align: center; border-bottom: 2px solid #175432; padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
            <img src="assets/images/ATI_Logo.png" alt="ATI Logo" style="height: 60px; margin-bottom: 0.5rem; object-fit: contain;">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: #4b5563; font-weight: 700;">Republic of the Philippines • Department of Agriculture</div>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: #175432; margin: 0.2rem 0;">AGRICULTURAL TRAINING INSTITUTE</h2>
            <p style="font-size: 0.78rem; color: #6b7280; margin: 0;">ATI Central Office, Elliptical Road, Diliman, Quezon City</p>
            <div style="display: inline-block; background: #eaf3ed; border: 1.5px solid #175432; color: #175432; font-weight: 800; font-size: 0.82rem; padding: 0.35rem 1rem; border-radius: 9999px; margin-top: 0.85rem;">
              OFFICIAL FACILITY &amp; DORMITORY RESERVATION SLIP
            </div>
          </div>

          <!-- Slip Details Grid -->
          <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem; margin-bottom: 1.5rem;">
            <tbody>
              <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #4b5563; width: 30%;">Reference Number:</td>
                <td style="padding: 0.65rem 0.5rem; font-weight: 800; font-family: monospace; color: #175432; font-size: 1rem;" id="slipRefCode">--</td>
              </tr>
              <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #4b5563;">Status of Request:</td>
                <td style="padding: 0.65rem 0.5rem; font-weight: 800;" id="slipStatusText">--</td>
              </tr>
              <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #4b5563;">Activity / Event Title:</td>
                <td style="padding: 0.65rem 0.5rem; font-weight: 800; color: #111827;" id="slipEventTitle">--</td>
              </tr>
              <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #4b5563;">Venue / Assigned Facility:</td>
                <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #175432;" id="slipVenueName">--</td>
              </tr>
              <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #4b5563;">Target Schedule Dates:</td>
                <td style="padding: 0.65rem 0.5rem; font-weight: 700;" id="slipScheduleDates">--</td>
              </tr>
              <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #4b5563;">Time Slot / Duration:</td>
                <td style="padding: 0.65rem 0.5rem;" id="slipTimeSlot">--</td>
              </tr>
              <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #4b5563;">Requestor / Division:</td>
                <td style="padding: 0.65rem 0.5rem;" id="slipApplicantDivision">--</td>
              </tr>
              <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #4b5563;">Number of Attendees (PAX):</td>
                <td style="padding: 0.65rem 0.5rem;" id="slipPaxCount">--</td>
              </tr>
              <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #4b5563;">Special Notes / Setup:</td>
                <td style="padding: 0.65rem 0.5rem; color: #4b5563;" id="slipNotes">Standard venue arrangement</td>
              </tr>
            </tbody>
          </table>

          <!-- Signatures Section -->
          <div style="margin-top: 2.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div style="text-align: center;">
              <div style="border-bottom: 1.5px solid #111827; height: 35px; margin-bottom: 0.4rem;"></div>
              <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Requesting Officer</div>
              <div style="font-size: 0.72rem; color: #6b7280;">Applicant Signature over Printed Name</div>
            </div>
            <div style="text-align: center;">
              <div style="border-bottom: 1.5px solid #111827; height: 35px; margin-bottom: 0.4rem;"></div>
              <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Facility Custodian / Admin</div>
              <div style="font-size: 0.72rem; color: #6b7280;">Logistics &amp; Scheduling Clearance</div>
            </div>
          </div>
        </div>

        <!-- Slip Action Buttons -->
        <div class="modal-footer-actions" style="margin-top: 1.75rem;">
          <button type="button" class="btn-card-action secondary js-close-res-modal">Close</button>
          <button type="button" class="btn-card-action primary" onclick="printSlipDocument()">
            <svg viewBox="0 0 24 24" fill="none">
              <polyline points="6 9 6 2 18 2 18 9"></polyline>
              <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
              <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span>Print Official Slip (PDF)</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    @media print {
      body * {
        visibility: hidden !important;
      }
      #printableSlipArea, #printableSlipArea * {
        visibility: visible !important;
      }
      #printableSlipArea {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 20px !important;
      }
    }
  </style>

  <!-- Scripts -->
  <script src="js/my_reservations.js?v=<?php echo time(); ?>"></script>
</body>

</html>