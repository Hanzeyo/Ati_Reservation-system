<?php
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
          <span class="nav-badge-count" id="navBadgeFacilityCount">4</span>
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
            <div class="user-avatar-circle">JD</div>
            <div class="user-details">
              <div class="user-name">Juan Dela Cruz</div>
              <div class="user-role">ATI Staff (CDD)</div>
            </div>
          </div>

          <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-header-info">
              <div class="dropdown-user-name">Juan Dela Cruz</div>
              <div class="dropdown-user-email"><span class="user-verified-dot"></span> ATI Personnel &bull; CDD</div>
            </div>
            <a href="profile.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>My Profile</span>
            </a>
            <a href="my_reservations.php" class="dropdown-item active">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
              <span>Facility Reservations</span>
            </a>
            <a href="booking_history.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
              </svg>
              <span>Booking History</span>
            </a>
            <a href="admin_dashboard.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
              <span>Admin Dashboard</span>
            </a>
            <div style="height: 1px; background: #e5ede7; margin: 0.35rem 0;"></div>
            <a href="index.php" class="dropdown-item danger">
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
          <div class="drawer-avatar">JD</div>
          <div class="drawer-profile-info">
            <h5>Juan Dela Cruz</h5>
            <p>ATI Staff (CDD)</p>
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
            <span class="drawer-badge-count">4</span>
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
        </div>
      </div>

      <!-- Drawer Footer -->
      <div class="drawer-footer">
        <a href="index.php" class="drawer-logout-btn">
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
          <span class="seg-count-badge">4</span>
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
          <span class="seg-count-badge blue">6</span>
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
          <span class="stat-value" id="kpiTotal">6</span>
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
          <span class="stat-value" id="kpiPending">3</span>
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
          <span class="stat-value" id="kpiApproved">2</span>
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
          <span class="stat-value" id="kpiCompleted">1</span>
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
            <div class="facility-option" data-value="function-hall" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">Function Hall</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="facility-option" data-value="training-hall" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">Training Hall A</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="facility-option" data-value="boardroom" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">Boardroom</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="facility-option" data-value="mess-hall" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">Mess Hall</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="facility-option" data-value="dormitory" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">Dormitory</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
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

      <!-- CARD 1: FUNCTION HALL (PENDING ADMIN REVIEW) -->
      <article class="res-card" data-status="pending" data-category="facility" data-venue="function-hall"
        data-ref="ATI-RES-2026-1042" data-venue-title="Function Hall" data-dates="Oct 14 - 16, 2026 (3 Days)"
        data-time="08:00 AM - 05:00 PM (Full Day)" data-pax="120 Attendees"
        data-division="Career Development Division (CDD)" data-status-text="Pending Administrative Clearance">

        <div class="res-card-media">
          <img src="assets/images/function_hall.jpg" alt="ATI Function Hall">
          <span class="venue-thumb-pill">Function Hall</span>
        </div>

        <div class="res-card-main">
          <div class="res-main-top">
            <div class="res-ref-group">
              <span class="res-ref-tag">ATI-RES-2026-1042</span>
              <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-1042" title="Copy Reference Code">
                <svg viewBox="0 0 24 24" fill="none">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </button>
              <span class="res-timestamp">&bull; Oct 02, 2026</span>
            </div>
            <div class="res-status-badge pending">
              <span class="status-dot-pulse"></span>
              <span>Pending Administrative Clearance</span>
            </div>
          </div>

          <h3 class="res-event-title">Regional Agricultural Extension Coordinators Training 2026</h3>

          <div class="res-details-chips">
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span>Oct 14 – 16, 2026 (3 Days)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>08:00 AM – 05:00 PM</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
              <span>120 Delegates</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
              <span>CDD</span>
            </div>
          </div>

          <!-- Mini Routing Stepper / Progress Bar -->
          <div class="res-mini-routing">
            <div class="routing-progress-bar">
              <div class="progress-segment filled" title="1. Request Submitted (Done)"></div>
              <div class="progress-segment filled" title="2. Division Endorsed (Done)"></div>
              <div class="progress-segment active-pulse" title="3. Admin Services Clearance (Under Evaluation)"></div>
              <div class="progress-segment" title="4. Final Approval"></div>
            </div>
            <div class="routing-status-label">
              <span class="routing-step-desc"><strong style="color: #b45309;">Step 3 of 4:</strong> Admin Services
                Clearance (Under Evaluation)</span>
              <span class="routing-note">Special Setup: 4 wireless mics &amp; projector</span>
            </div>
          </div>
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
          <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-1042">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Download</span>
          </button>
          <button type="button" class="btn-card-action outline-danger js-cancel-res">
            <svg viewBox="0 0 24 24" fill="none">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            <span>Cancel</span>
          </button>
        </div>
      </article>

      <!-- CARD 2: TRAINING HALL A (PENDING DIVISION ENDORSEMENT) -->
      <article class="res-card" data-status="pending" data-category="facility" data-venue="training-hall"
        data-ref="ATI-RES-2026-1039" data-venue-title="Training Hall A" data-dates="Oct 22 - 23, 2026 (2 Days)"
        data-time="08:30 AM - 04:30 PM" data-pax="45 Attendees"
        data-division="Partnership & Accreditation Division (PAD)" data-status-text="Pending Division Endorsement">

        <div class="res-card-media">
          <img src="assets/images/training_hall.jpg" alt="ATI Training Hall A">
          <span class="venue-thumb-pill">Training Hall A</span>
        </div>

        <div class="res-card-main">
          <div class="res-main-top">
            <div class="res-ref-group">
              <span class="res-ref-tag">ATI-RES-2026-1039</span>
              <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-1039" title="Copy Reference Code">
                <svg viewBox="0 0 24 24" fill="none">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </button>
              <span class="res-timestamp">&bull; Oct 01, 2026</span>
            </div>
            <div class="res-status-badge pending">
              <span class="status-dot-pulse"></span>
              <span>Pending Division Endorsement</span>
            </div>
          </div>

          <h3 class="res-event-title">Smart Agriculture Technologies Demonstration Workshop</h3>

          <div class="res-details-chips">
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span>Oct 22 – 23, 2026 (2 Days)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>08:30 AM – 04:30 PM</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
              <span>45 Attendees</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
              <span>PAD</span>
            </div>
          </div>

          <!-- Mini Routing Stepper / Progress Bar -->
          <div class="res-mini-routing">
            <div class="routing-progress-bar">
              <div class="progress-segment filled" title="1. Request Submitted (Done)"></div>
              <div class="progress-segment active-pulse" title="2. Division Endorsement (In Progress)"></div>
              <div class="progress-segment" title="3. Admin Clearance"></div>
              <div class="progress-segment" title="4. Final Approval"></div>
            </div>
            <div class="routing-status-label">
              <span class="routing-step-desc"><strong style="color: #b45309;">Step 2 of 4:</strong> Division Endorsement
                (Awaiting Division Chief Sign)</span>
              <span class="routing-note">Layout: Classroom style with 6 tables</span>
            </div>
          </div>
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
          <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-1039">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Download</span>
          </button>
          <button type="button" class="btn-card-action outline-danger js-cancel-res">
            <svg viewBox="0 0 24 24" fill="none">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            <span>Cancel</span>
          </button>
        </div>
      </article>

      <!-- CARD 3: BOARDROOM (APPROVED & CONFIRMED) -->
      <article class="res-card" data-status="approved" data-category="facility" data-venue="boardroom"
        data-ref="ATI-RES-2026-0985" data-venue-title="Boardroom" data-dates="Oct 08, 2026 (1 Day)"
        data-time="09:00 AM - 05:00 PM" data-pax="25 Dignitaries" data-division="Office of the Director (OD)"
        data-status-text="Confirmed & Approved">

        <div class="res-card-media">
          <img src="assets/images/boardroom.jpg" alt="ATI Boardroom">
          <span class="venue-thumb-pill">Executive Boardroom</span>
        </div>

        <div class="res-card-main">
          <div class="res-main-top">
            <div class="res-ref-group">
              <span class="res-ref-tag">ATI-RES-2026-0985</span>
              <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-0985" title="Copy Reference Code">
                <svg viewBox="0 0 24 24" fill="none">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </button>
              <span class="res-timestamp">&bull; Approved Sep 28, 2026</span>
            </div>
            <div class="res-status-badge approved">
              <span class="status-dot-pulse"></span>
              <span>Confirmed &amp; Approved</span>
            </div>
          </div>

          <h3 class="res-event-title">Executive Directorate Quarterly Planning & Strategy Session</h3>

          <div class="res-details-chips">
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span>Oct 08, 2026 (1 Day)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>09:00 AM – 05:00 PM</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
              <span>25 Attendees</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
              <span>OD</span>
            </div>
          </div>

          <!-- Mini Routing Stepper / Progress Bar -->
          <div class="res-mini-routing">
            <div class="routing-progress-bar">
              <div class="progress-segment filled" title="1. Submitted (Done)"></div>
              <div class="progress-segment filled" title="2. Endorsed (Done)"></div>
              <div class="progress-segment filled" title="3. Cleared (Done)"></div>
              <div class="progress-segment filled" title="4. Pass Issued (Done)"></div>
            </div>
            <div class="routing-status-label">
              <span class="routing-step-desc"><strong style="color: #16a34a;">Step 4 of 4:</strong> Routing Complete
                &bull; Security Pass Active</span>
              <span class="routing-note" style="color: #15803d;">&#10003; Security guards &amp; technicians
                notified</span>
            </div>
          </div>
        </div>

        <div class="res-card-side-actions">
          <button type="button" class="btn-card-action primary"
            onclick="showGatePassModal('ATI-RES-2026-0985', 'Executive Directorate Quarterly Planning & Strategy Session', 'Executive Boardroom', 'Oct 08, 2026')">
            <svg viewBox="0 0 24 24" fill="none">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Gate Pass &amp; QR</span>
          </button>
          <button type="button" class="btn-card-action secondary js-view-details">
            <svg viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>View Details</span>
          </button>
          <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-0985">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Download</span>
          </button>
        </div>
      </article>

      <!-- CARD 4: DORMITORY (APPROVED & CONFIRMED) -->
      <article class="res-card" data-status="approved" data-category="dormitory" data-venue="dormitory"
        data-ref="ATI-RES-2026-0941" data-venue-title="ATI Dormitory (Executive Wing)"
        data-dates="Oct 05 - 07, 2026 (2 Nights)" data-time="Check-in 02:00 PM • Check-out 12:00 PM"
        data-pax="8 Delegates (4 Twin Rooms)" data-division="Career Development Division (CDD)"
        data-status-text="Confirmed & Approved">

        <div class="res-card-media">
          <img src="assets/images/dormitory.jpg" alt="ATI Dormitory">
          <span class="venue-thumb-pill dorm-pill">Dormitory (Executive)</span>
        </div>

        <div class="res-card-main">
          <div class="res-main-top">
            <div class="res-ref-group">
              <span class="res-ref-tag dorm-ref">ATI-RES-2026-0941</span>
              <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-0941" title="Copy Reference Code">
                <svg viewBox="0 0 24 24" fill="none">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </button>
              <span class="res-timestamp">&bull; Approved Sep 22, 2026</span>
            </div>
            <div class="res-status-badge approved">
              <span class="status-dot-pulse"></span>
              <span>Confirmed &amp; Approved</span>
            </div>
          </div>

          <h3 class="res-event-title">Guest Lecturers & Resource Persons Lodging (Visayas & Mindanao Trainers)</h3>

          <div class="res-details-chips">
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span>Oct 05 – 07, 2026 (2 Nights)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>In: 02:00 PM • Out: 12:00 PM</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
              <span>Rooms 201-204 (Exec Wing)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
              <span>8 Resource Speakers</span>
            </div>
          </div>

          <!-- Mini Routing Stepper / Progress Bar -->
          <div class="res-mini-routing">
            <div class="routing-progress-bar">
              <div class="progress-segment filled" title="1. Submitted (Done)"></div>
              <div class="progress-segment filled" title="2. Endorsed (Done)"></div>
              <div class="progress-segment filled" title="3. Custodian Cleared (Done)"></div>
              <div class="progress-segment filled" title="4. Keys Ready (Done)"></div>
            </div>
            <div class="routing-status-label">
              <span class="routing-step-desc"><strong style="color: #16a34a;">Step 4 of 4:</strong> Keys Allocation
                Ready at Lobby Desk</span>
              <span class="routing-note">Towels &amp; linens provided. Curfew: 10:00 PM</span>
            </div>
          </div>
        </div>

        <div class="res-card-side-actions">
          <button type="button" class="btn-card-action primary"
            onclick="showGatePassModal('ATI-RES-2026-0941', 'Guest Lecturers & Resource Persons Lodging', 'ATI Dormitory Executive Wing', 'Oct 05 - 07, 2026')">
            <svg viewBox="0 0 24 24" fill="none">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Gate Pass &amp; QR</span>
          </button>
          <button type="button" class="btn-card-action secondary js-view-details">
            <svg viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>View Details</span>
          </button>
          <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-0941">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Download</span>
          </button>
        </div>
      </article>

      <!-- CARD 6: DORMITORY SUITES A & B (PENDING CUSTODIAN ALLOCATION) -->
      <article class="res-card" data-status="pending" data-category="dormitory" data-venue="dormitory"
        data-ref="ATI-BK-2026-1055" data-venue-title="Dormitory Suites A & B" data-dates="Nov 10 - 14, 2026 (4 Nights)"
        data-time="Check-in 02:00 PM • Check-out 11:00 AM" data-pax="24 Trainees (12 Twin Bed Rooms)"
        data-division="Partnership & Accreditation Division (PAD)" data-status-text="Pending Custodian Bed Allotment">

        <div class="res-card-media">
          <img src="assets/images/dormitory.jpg" alt="ATI Dormitory Suites">
          <span class="venue-thumb-pill dorm-pill">Dormitory Suites</span>
        </div>

        <div class="res-card-main">
          <div class="res-main-top">
            <div class="res-ref-group">
              <span class="res-ref-tag dorm-ref">ATI-BK-2026-1055</span>
              <button type="button" class="btn-copy-ref" data-ref="ATI-BK-2026-1055" title="Copy Reference Code">
                <svg viewBox="0 0 24 24" fill="none">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </button>
              <span class="res-timestamp">&bull; Oct 04, 2026</span>
            </div>
            <div class="res-status-badge pending">
              <span class="status-dot-pulse"></span>
              <span>Pending Custodian Bed Allotment</span>
            </div>
          </div>

          <h3 class="res-event-title">National Young Farmers Camp Participant Lodging (Batch 4)</h3>

          <div class="res-details-chips">
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span>Nov 10 – 14, 2026 (4 Nights)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>In: 02:00 PM • Out: 11:00 AM</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
              <span>Floors 3 &amp; 4 (Suites A &amp; B)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
              <span>24 Delegates (12 Rooms)</span>
            </div>
          </div>

          <!-- Mini Routing Stepper / Progress Bar -->
          <div class="res-mini-routing">
            <div class="routing-progress-bar">
              <div class="progress-segment filled" title="1. Booking Request Submitted (Done)"></div>
              <div class="progress-segment filled" title="2. Division Endorsed (Done)"></div>
              <div class="progress-segment active-pulse" title="3. Dormitory Custodian Review (Under Evaluation)"></div>
              <div class="progress-segment" title="4. Bed Allotment & Keys Release"></div>
            </div>
            <div class="routing-status-label">
              <span class="routing-step-desc"><strong style="color: #b45309;">Step 3 of 4:</strong> Dormitory Custodian
                Room & Bed Assignment in Progress</span>
              <span class="routing-note">Gender-segregated room allocation requested. Linens requested.</span>
            </div>
          </div>
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
          <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-BK-2026-1055">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Download</span>
          </button>
          <button type="button" class="btn-card-action outline-danger js-cancel-res">
            <svg viewBox="0 0 24 24" fill="none">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            <span>Cancel</span>
          </button>
        </div>
      </article>

      <!-- CARD 5: MESS HALL (COMPLETED / HISTORICAL) -->
      <article class="res-card" data-status="completed" data-category="facility" data-venue="mess-hall"
        data-ref="ATI-RES-2026-0812" data-venue-title="Mess Hall" data-dates="Sep 25, 2026 (1 Day)"
        data-time="11:00 AM - 03:00 PM" data-pax="180 Attendees" data-division="Administrative and Finance Unit"
        data-status-text="Completed">

        <div class="res-card-media">
          <img src="assets/images/mess_hall.jpg" alt="ATI Mess Hall">
          <span class="venue-thumb-pill">Mess Hall</span>
        </div>

        <div class="res-card-main">
          <div class="res-main-top">
            <div class="res-ref-group">
              <span class="res-ref-tag">ATI-RES-2026-0812</span>
              <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-0812" title="Copy Reference Code">
                <svg viewBox="0 0 24 24" fill="none">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </button>
              <span class="res-timestamp">&bull; Concluded Sep 25, 2026</span>
            </div>
            <div class="res-status-badge completed">
              <span class="status-dot-pulse"></span>
              <span>Completed &amp; Cleared</span>
            </div>
          </div>

          <h3 class="res-event-title">ATI Mid-Year General Assembly & Fellowship Gathering</h3>

          <div class="res-details-chips">
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span>Sep 25, 2026 (1 Day)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>11:00 AM – 03:00 PM</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
              <span>180 Personnel</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
              <span>Admin &amp; Finance Unit</span>
            </div>
          </div>

          <!-- Mini Routing Stepper / Progress Bar -->
          <div class="res-mini-routing">
            <div class="routing-progress-bar">
              <div class="progress-segment filled" title="1. Submitted (Done)"></div>
              <div class="progress-segment filled" title="2. Endorsed (Done)"></div>
              <div class="progress-segment filled" title="3. Executed (Done)"></div>
              <div class="progress-segment filled" title="4. Concluded (Done)"></div>
            </div>
            <div class="routing-status-label">
              <span class="routing-step-desc"><strong style="color: #64748b;">Event Concluded:</strong> Cleared &bull;
                Post-Activity Inspected</span>
              <span class="routing-note">Post-activity report: Clean &amp; undamaged</span>
            </div>
          </div>
        </div>

        <div class="res-card-side-actions">
          <button type="button" class="btn-card-action secondary js-view-details">
            <svg viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>View Summary</span>
          </button>
          <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-0812">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Download</span>
          </button>
        </div>
      </article>

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
            <tr class="res-table-row" data-status="pending" data-category="facility" data-venue="function-hall">
              <td>
                <span class="res-ref-tag">ATI-RES-2026-1042</span>
                <div style="font-size: 0.82rem; font-weight: 700; color: #174d2f; margin-top: 0.2rem;">Function Hall
                </div>
              </td>
              <td>
                <div style="font-weight: 800; color: #142e20;">Regional Agricultural Extension Coordinators Training
                  2026</div>
                <div style="font-size: 0.78rem; color: #556e5f;">Career Development Division (CDD) &bull; 120 Delegates
                </div>
              </td>
              <td>
                <div style="font-size: 0.85rem; font-weight: 700; color: #173222;">Oct 14 – 16, 2026 (3 Days)</div>
                <div style="font-size: 0.76rem; color: #617b6c;">08:00 AM – 05:00 PM</div>
              </td>
              <td>
                <span class="res-status-badge pending"><span class="status-dot-pulse"></span><span>Pending Clearance
                    (Step 3/4)</span></span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <button type="button" class="btn-card-action secondary js-view-details">Details</button>
                  <button type="button" class="btn-card-action secondary js-download-slip"
                    data-ref="ATI-RES-2026-1042">Slip</button>
                </div>
              </td>
            </tr>

            <tr class="res-table-row" data-status="pending" data-category="facility" data-venue="training-hall">
              <td>
                <span class="res-ref-tag">ATI-RES-2026-1039</span>
                <div style="font-size: 0.82rem; font-weight: 700; color: #174d2f; margin-top: 0.2rem;">Training Hall A
                </div>
              </td>
              <td>
                <div style="font-weight: 800; color: #142e20;">Smart Agriculture Technologies Demonstration Workshop
                </div>
                <div style="font-size: 0.78rem; color: #556e5f;">Partnership &amp; Accreditation Division (PAD) &bull;
                  45 Attendees</div>
              </td>
              <td>
                <div style="font-size: 0.85rem; font-weight: 700; color: #173222;">Oct 22 – 23, 2026 (2 Days)</div>
                <div style="font-size: 0.76rem; color: #617b6c;">08:30 AM – 04:30 PM</div>
              </td>
              <td>
                <span class="res-status-badge pending"><span class="status-dot-pulse"></span><span>Division Sign (Step
                    2/4)</span></span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <button type="button" class="btn-card-action secondary js-view-details">Details</button>
                  <button type="button" class="btn-card-action secondary js-download-slip"
                    data-ref="ATI-RES-2026-1039">Slip</button>
                </div>
              </td>
            </tr>

            <tr class="res-table-row" data-status="approved" data-category="facility" data-venue="boardroom">
              <td>
                <span class="res-ref-tag">ATI-RES-2026-0985</span>
                <div style="font-size: 0.82rem; font-weight: 700; color: #174d2f; margin-top: 0.2rem;">Executive
                  Boardroom</div>
              </td>
              <td>
                <div style="font-weight: 800; color: #142e20;">Executive Directorate Quarterly Planning & Strategy
                  Session</div>
                <div style="font-size: 0.78rem; color: #556e5f;">Office of the Director (OD) &bull; 25 Dignitaries</div>
              </td>
              <td>
                <div style="font-size: 0.85rem; font-weight: 700; color: #173222;">Oct 08, 2026 (1 Day)</div>
                <div style="font-size: 0.76rem; color: #617b6c;">09:00 AM – 05:00 PM</div>
              </td>
              <td>
                <span class="res-status-badge approved"><span class="status-dot-pulse"></span><span>Confirmed &amp;
                    Approved</span></span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <button type="button" class="btn-card-action primary"
                    onclick="showGatePassModal('ATI-RES-2026-0985', 'Executive Directorate Quarterly Planning & Strategy Session', 'Executive Boardroom', 'Oct 08, 2026')">Pass
                    &amp; QR</button>
                  <button type="button" class="btn-card-action secondary js-view-details">Details</button>
                </div>
              </td>
            </tr>

            <tr class="res-table-row" data-status="approved" data-category="dormitory" data-venue="dormitory">
              <td>
                <span class="res-ref-tag dorm-ref">ATI-RES-2026-0941</span>
                <div style="font-size: 0.82rem; font-weight: 700; color: #1e40af; margin-top: 0.2rem;">Dormitory
                  (Executive Wing)</div>
              </td>
              <td>
                <div style="font-weight: 800; color: #142e20;">Guest Lecturers & Resource Persons Lodging</div>
                <div style="font-size: 0.78rem; color: #556e5f;">Career Development Division (CDD) &bull; 8 Speakers (4
                  Rooms)</div>
              </td>
              <td>
                <div style="font-size: 0.85rem; font-weight: 700; color: #173222;">Oct 05 – 07, 2026 (2 Nights)</div>
                <div style="font-size: 0.76rem; color: #617b6c;">In: 02:00 PM • Out: 12:00 PM</div>
              </td>
              <td>
                <span class="res-status-badge approved"><span class="status-dot-pulse"></span><span>Keys Allocation
                    Ready</span></span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <button type="button" class="btn-card-action primary"
                    onclick="showGatePassModal('ATI-RES-2026-0941', 'Guest Lecturers & Resource Persons Lodging', 'ATI Dormitory Executive Wing', 'Oct 05 - 07, 2026')">Pass
                    &amp; QR</button>
                  <button type="button" class="btn-card-action secondary js-view-details">Details</button>
                </div>
              </td>
            </tr>

            <tr class="res-table-row" data-status="pending" data-category="dormitory" data-venue="dormitory">
              <td>
                <span class="res-ref-tag dorm-ref">ATI-BK-2026-1055</span>
                <div style="font-size: 0.82rem; font-weight: 700; color: #1e40af; margin-top: 0.2rem;">Dormitory Suites
                  A &amp; B</div>
              </td>
              <td>
                <div style="font-weight: 800; color: #142e20;">National Young Farmers Camp Participant Lodging</div>
                <div style="font-size: 0.78rem; color: #556e5f;">Partnership &amp; Accreditation Division (PAD) &bull;
                  24 Delegates (12 Rooms)</div>
              </td>
              <td>
                <div style="font-size: 0.85rem; font-weight: 700; color: #173222;">Nov 10 – 14, 2026 (4 Nights)</div>
                <div style="font-size: 0.76rem; color: #617b6c;">In: 02:00 PM • Out: 11:00 AM</div>
              </td>
              <td>
                <span class="res-status-badge pending"><span class="status-dot-pulse"></span><span>Custodian Review
                    (Step 3/4)</span></span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <button type="button" class="btn-card-action secondary js-view-details">Details</button>
                  <button type="button" class="btn-card-action secondary js-download-slip"
                    data-ref="ATI-BK-2026-1055">Slip</button>
                </div>
              </td>
            </tr>

            <tr class="res-table-row" data-status="completed" data-category="facility" data-venue="mess-hall">
              <td>
                <span class="res-ref-tag">ATI-RES-2026-0812</span>
                <div style="font-size: 0.82rem; font-weight: 700; color: #174d2f; margin-top: 0.2rem;">Mess Hall</div>
              </td>
              <td>
                <div style="font-weight: 800; color: #142e20;">ATI Mid-Year General Assembly & Fellowship Gathering
                </div>
                <div style="font-size: 0.78rem; color: #556e5f;">Administrative &amp; Finance Unit &bull; 180 Personnel
                </div>
              </td>
              <td>
                <div style="font-size: 0.85rem; font-weight: 700; color: #173222;">Sep 25, 2026 (1 Day)</div>
                <div style="font-size: 0.76rem; color: #617b6c;">11:00 AM – 03:00 PM</div>
              </td>
              <td>
                <span class="res-status-badge completed"><span class="status-dot-pulse"></span><span>Completed &amp;
                    Archived</span></span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <button type="button" class="btn-card-action secondary js-view-details">Details</button>
                  <button type="button" class="btn-card-action secondary js-download-slip"
                    data-ref="ATI-RES-2026-0812">Slip</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    </section>

    <!-- Empty State Result -->
    <div class="empty-res-state" id="emptyResState">
      <div class="empty-res-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
            </line>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
      </div>
      <h3>No Reservations Found</h3>
      <p>We couldn't find any reservation requests matching your selected filter criteria. Try adjusting your search
        query or venue selection.</p>
      <button type="button" class="btn-new-res-action"
        onclick="document.getElementById('resSearchInput').value=''; document.getElementById('resVenueFilter').value='all'; document.querySelector('.filter-tab-btn[data-filter=\'all\']').click();">
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
        <div style="margin-bottom: 1.5rem;">
          <h4
            style="font-size: 0.88rem; font-weight: 800; color: #174d2f; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
            Attached Official Documents</h4>
          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.85rem;">
            <div
              style="display: flex; align-items: center; gap: 0.65rem; background: #f7faf8; border: 1px solid #dce8e0; padding: 0.7rem 0.9rem; border-radius: 8px;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
              </svg>
              <div style="overflow: hidden;">
                <div
                  style="font-size: 0.82rem; font-weight: 700; color: #1a2f22; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  Activity_Design_Approved.pdf</div>
                <div style="font-size: 0.7rem; color: #627b6c;">1.8 MB &bull; Signed Copy</div>
              </div>
            </div>

            <div
              style="display: flex; align-items: center; gap: 0.65rem; background: #f7faf8; border: 1px solid #dce8e0; padding: 0.7rem 0.9rem; border-radius: 8px;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
              </svg>
              <div style="overflow: hidden;">
                <div
                  style="font-size: 0.82rem; font-weight: 700; color: #1a2f22; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  Special_Order_2026_CDD.pdf</div>
                <div style="font-size: 0.7rem; color: #627b6c;">640 KB &bull; Verified</div>
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

  <!-- Scripts -->
  <script src="js/my_reservations.js?v=<?php echo time(); ?>"></script>
</body>

</html>