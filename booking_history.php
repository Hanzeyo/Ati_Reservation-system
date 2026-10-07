<?php
/**
 * ATI Facility & Dormitory Reservation Portal
 * User Dashboard: Dormitory Booking History (Lodging, Rooms & Bed Allocations)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dormitory Booking History | ATI Reservation Portal</title>
  <meta name="description" content="Official Dormitory Lodging & Room Booking History for the Agriculture Training Institute.">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
          <h1>ATI Reservation Portal</h1>
          <p>Department of Agriculture &bull; Central Office</p>
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
            <path d="M12 5v14"></path>
            <path d="M5 12h14"></path>
          </svg>
          <span>New Reservation</span>
        </a>

        <!-- Facility Bookings (Halls) -->
        <a href="my_reservations.php" class="booking-nav-item" title="Your Bookings (Facilities & Venues)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>Your Bookings</span>
          <span class="nav-badge-count">4</span>
        </a>

        <!-- Dormitory Reservations (Lodging) - Active -->
        <a href="booking_history.php" class="booking-nav-item active" title="Your Reservations (Dormitories & Lodging)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 4v16"></path>
            <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
            <path d="M2 17h20"></path>
            <path d="M6 8v9"></path>
          </svg>
          <span>Your Reservations</span>
          <span class="nav-badge-count blue">2</span>
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
              <div class="dropdown-avatar-circle">JD</div>
              <div class="dropdown-user-meta">
                <div class="dropdown-user-name">Juan Dela Cruz</div>
                <div class="dropdown-user-email">juan.delacruz@ati.da.gov.ph</div>
              </div>
            </div>
            <a href="profile.php" class="dropdown-item">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>My Profile</span>
            </a>
            <a href="my_reservations.php" class="dropdown-item">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
              <span>Your Bookings</span>
            </a>
            <a href="booking_history.php" class="dropdown-item active">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
                <path d="M6 8v9"></path>
              </svg>
              <span>Your Reservations</span>
            </a>
            <a href="javascript:void(0)" onclick="openUserSettingsModal(); if (typeof closeProfileDropdown === 'function') closeProfileDropdown(); return false;" class="dropdown-item">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
              </svg>
              <span>Settings</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="index.php" class="dropdown-item danger">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
      <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleMobileDrawer(true)" aria-label="Open Navigation Menu" aria-expanded="false">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </div>
  </header>

  <!-- ==========================================================================
       MOBILE NAVIGATION DRAWER OVERLAY
       ========================================================================== -->
  <div class="mobile-drawer-overlay" id="mobileDrawerOverlay" onclick="if(event.target===this) toggleMobileDrawer(false);" style="display: none;">
    <div class="mobile-nav-drawer" id="mobileNavDrawer" role="dialog" aria-modal="true" aria-label="Mobile Navigation">
      
      <!-- Drawer Header -->
      <div class="drawer-header">
        <div class="drawer-brand">
          <img src="assets/images/ATI_Logo.png" alt="ATI Logo" class="drawer-logo" style="width: 36px; height: 36px; object-fit: contain;">
          <div>
            <h4>ATI Portal</h4>
            <p>Central Office</p>
          </div>
        </div>
        <button type="button" class="drawer-close-btn" id="mobileDrawerClose" onclick="toggleMobileDrawer(false)" aria-label="Close Navigation Menu">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
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
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>

          <a href="booking.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
            </div>
            <span class="drawer-link-text">New Reservation</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>

          <a href="my_reservations.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
            </div>
            <span class="drawer-link-text">Your Bookings</span>
            <span class="drawer-badge-count">4</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>

          <a href="booking_history.php" class="drawer-nav-link active">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
              </svg>
            </div>
            <span class="drawer-link-text">Your Reservations</span>
            <span class="drawer-badge-count blue">2</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>

          <a href="schedule.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <span class="drawer-link-text">Master Schedule</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>
        </div>

        <!-- Account Settings Section in Drawer -->
        <div class="drawer-nav-section">
          <div class="drawer-section-label">ACCOUNT & SETTINGS</div>
          
          <a href="profile.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <span class="drawer-link-text">My Profile</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>

          <a href="admin_dashboard.php" class="drawer-nav-link">
            <div class="drawer-link-icon">
              <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </div>
            <span class="drawer-link-text">Admin Dashboard</span>
            <svg class="drawer-arrow" viewBox="0 0 24 24" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
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

  <!-- ==========================================================================
       MAIN CONTENT CONTAINER
       ========================================================================== -->
  <main class="my-res-main-wrapper">

    <!-- Primary Category Segregation Switcher (Facility Bookings vs Dormitory Reservations) -->
    <div class="history-category-segmented-bar">
      <div class="category-segmented-container">
        <!-- Tab 1: Facility Venues & Halls (Link) -->
        <a href="my_reservations.php" class="category-segment-btn" title="Switch to Facility Bookings">
          <div class="seg-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
          </div>
          <div class="seg-text">
            <span class="seg-title">Your Bookings</span>
            <span class="seg-subtitle">Function Hall, Training Halls, Boardrooms &amp; Mess Hall</span>
          </div>
          <span class="seg-count-badge">4</span>
        </a>

        <!-- Tab 2: Dormitory Lodging & Rooms (Active) -->
        <a href="booking_history.php" class="category-segment-btn active" title="Currently Viewing Dormitory Reservations">
          <div class="seg-icon dorm-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M2 4v16"></path>
              <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
              <path d="M2 17h20"></path>
              <path d="M6 8v9"></path>
            </svg>
          </div>
          <div class="seg-text">
            <span class="seg-title">Your Reservations</span>
            <span class="seg-subtitle">Official Lodging, Trainee Rooms &amp; Bed Allocations</span>
          </div>
          <span class="seg-count-badge blue">2</span>
        </a>
      </div>
    </div>

    <!-- Header Section -->
    <section class="my-res-header">
      <div class="my-res-header-text">
        <div class="my-res-top-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M2 4v16"></path>
            <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
            <path d="M2 17h20"></path>
          </svg>
          <span>Official Dormitory Lodging &amp; Rooms</span>
        </div>
        <h2 id="pageHeadingTitle">Your Reservations</h2>
        <p id="pageHeadingSubtext">Monitor your official dormitory reservations, track room &amp; bed assignments with dormitory custodians, download lodging slips, and view room access security gate passes.</p>
      </div>

      <a href="dormitory_booking.php" class="btn-new-res-action">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Book Lodging</span>
      </a>
    </section>

    <!-- Summary KPI Stat Cards -->
    <section class="my-res-stats-grid" aria-label="Dormitory Summary Statistics">
      <!-- Total -->
      <div class="my-res-stat-card active-filter" data-filter="all">
        <div class="stat-icon-box green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 4v16"></path>
            <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
            <path d="M2 17h20"></path>
            <path d="M6 8v9"></path>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiTotal">2</span>
          <span class="stat-label">Total Bookings</span>
        </div>
      </div>

      <!-- Pending Custodian Clearance -->
      <div class="my-res-stat-card" data-filter="pending">
        <div class="stat-icon-box amber">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiPending">1</span>
          <span class="stat-label">Pending Custodian</span>
        </div>
      </div>

      <!-- Confirmed & Key Ready -->
      <div class="my-res-stat-card" data-filter="approved">
        <div class="stat-icon-box blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiApproved">1</span>
          <span class="stat-label">Keys Ready</span>
        </div>
      </div>

      <!-- Completed / Checked Out -->
      <div class="my-res-stat-card" data-filter="completed">
        <div class="stat-icon-box slate">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value" id="kpiCompleted">0</span>
          <span class="stat-label">Past / Checked Out</span>
        </div>
      </div>
    </section>

    <!-- ==========================================================================
         FILTER & SEARCH TOOLBAR
         ========================================================================== -->
    <div class="my-res-toolbar-card">
      
      <!-- Search Input -->
      <div class="search-box-wrap">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="resSearchInput" class="search-input" placeholder="Search by Booking Ref #, lodging purpose, room...">
        <button type="button" class="btn-clear-search" id="btnClearSearch" title="Clear search" style="display: none;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Controls: Wing Dropdown & View Mode Switcher -->
      <div class="toolbar-controls-right">
        <!-- Room/Wing Dropdown Filter -->
        <div class="custom-facility-dropdown" id="facilityDropdown">
          <button type="button" class="facility-dropdown-btn" id="facilityDropdownBtn" aria-haspopup="listbox" aria-expanded="false" title="Filter by Wing / Room Type">
            <span class="facility-btn-icon">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"></path>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                <path d="M2 17h20"></path>
              </svg>
            </span>
            <span class="facility-btn-label" id="facilityDropdownLabel">All Wings &amp; Suites</span>
            <svg class="facility-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
          
          <div class="facility-dropdown-menu" id="facilityDropdownMenu" role="listbox" aria-label="Filter by Wing">
            <div class="facility-option active" data-value="all" role="option" aria-selected="true">
              <span class="opt-bullet all"></span>
              <span class="opt-name">All Wings &amp; Suites</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <div class="facility-option" data-value="exec-wing" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">Executive Wing</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <div class="facility-option" data-value="suites" role="option">
              <span class="opt-bullet"></span>
              <span class="opt-name">Dormitory Suites A &amp; B</span>
              <svg class="opt-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
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
         DORMITORY BOOKINGS CONTAINER (CARDS VIEW & TABLE VIEW)
         ========================================================================== -->
    <section class="reservations-container" id="reservationsList">

      <!-- CARD 1: DORMITORY SUITES A & B (PENDING CUSTODIAN ALLOCATION) -->
      <article class="res-card" 
               data-status="pending" 
               data-category="dormitory" 
               data-venue="suites" 
               data-ref="ATI-BK-2026-1055" 
               data-venue-title="Dormitory Suites A & B"
               data-dates="Nov 10 - 14, 2026 (4 Nights)"
               data-time="Check-in 02:00 PM • Check-out 11:00 AM"
               data-pax="24 Trainees (12 Twin Bed Rooms)"
               data-division="Partnership & Accreditation Division (PAD)"
               data-status-text="Pending Custodian Bed Allotment">
        
        <div class="res-card-media">
          <img src="assets/images/dormitory.jpg" alt="ATI Dormitory Suites">
          <span class="venue-thumb-pill dorm-pill">Dormitory Suites</span>
        </div>

        <div class="res-card-main">
          <div class="res-main-top">
            <div class="res-ref-group">
              <span class="res-ref-tag dorm-ref">ATI-BK-2026-1055</span>
              <button type="button" class="btn-copy-ref" data-ref="ATI-BK-2026-1055" title="Copy Reference Code">
                <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
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
              <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <span>Nov 10 – 14, 2026 (4 Nights)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              <span>In: 02:00 PM • Out: 11:00 AM</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
              <span>Floors 3 &amp; 4 (Suites A &amp; B)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
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
              <span class="routing-step-desc"><strong style="color: #b45309;">Step 3 of 4:</strong> Dormitory Custodian Room & Bed Assignment in Progress</span>
              <span class="routing-note">Gender-segregated room allocation requested. Linens requested.</span>
            </div>
          </div>
        </div>

        <div class="res-card-side-actions">
          <button type="button" class="btn-card-action secondary js-view-details">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>View Details</span>
          </button>
          <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-BK-2026-1055">
            <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Download</span>
          </button>
          <button type="button" class="btn-card-action outline-danger js-cancel-res">
            <svg viewBox="0 0 24 24" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            <span>Cancel</span>
          </button>
        </div>
      </article>

      <!-- CARD 2: DORMITORY EXECUTIVE WING (APPROVED & CONFIRMED) -->
      <article class="res-card" 
               data-status="approved" 
               data-category="dormitory" 
               data-venue="exec-wing" 
               data-ref="ATI-RES-2026-0941" 
               data-venue-title="ATI Dormitory (Executive Wing)"
               data-dates="Oct 05 - 07, 2026 (2 Nights)"
               data-time="Check-in 02:00 PM • Check-out 12:00 PM"
               data-pax="8 Delegates (4 Twin Rooms)"
               data-division="Career Development Division (CDD)"
               data-status-text="Confirmed & Approved">
        
        <div class="res-card-media">
          <img src="assets/images/dormitory.jpg" alt="ATI Dormitory Executive Wing">
          <span class="venue-thumb-pill dorm-pill">Dormitory (Executive)</span>
        </div>

        <div class="res-card-main">
          <div class="res-main-top">
            <div class="res-ref-group">
              <span class="res-ref-tag dorm-ref">ATI-RES-2026-0941</span>
              <button type="button" class="btn-copy-ref" data-ref="ATI-RES-2026-0941" title="Copy Reference Code">
                <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
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
              <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <span>Oct 05 – 07, 2026 (2 Nights)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              <span>In: 02:00 PM • Out: 12:00 PM</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
              <span>Rooms 201-204 (Exec Wing)</span>
            </div>
            <div class="res-chip-item">
              <svg viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
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
              <span class="routing-step-desc"><strong style="color: #16a34a;">Step 4 of 4:</strong> Keys Allocation Ready at Lobby Desk</span>
              <span class="routing-note">Towels &amp; linens provided. Curfew: 10:00 PM</span>
            </div>
          </div>
        </div>

        <div class="res-card-side-actions">
          <button type="button" class="btn-card-action primary" onclick="showGatePassModal('ATI-RES-2026-0941', 'Guest Lecturers & Resource Persons Lodging', 'ATI Dormitory Executive Wing', 'Oct 05 - 07, 2026')">
            <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            <span>Gate Pass &amp; QR</span>
          </button>
          <button type="button" class="btn-card-action secondary js-view-details">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>View Details</span>
          </button>
          <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-RES-2026-0941">
            <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Download</span>
          </button>
        </div>
      </article>

      <!-- ALTERNATIVE TABLE VIEW (TOGGLEABLE) -->
      <div class="reservations-table-wrap" id="reservationsTableWrap" style="display: none;">
        <table class="res-table">
          <thead>
            <tr>
              <th>Ref &amp; Wing</th>
              <th>Lodging Purpose &amp; Unit</th>
              <th>Stay Duration &amp; Hours</th>
              <th>Custodian Status</th>
              <th style="text-align: right;">Quick Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr class="res-table-row" data-status="pending" data-category="dormitory" data-venue="suites">
              <td data-label="Ref & Wing">
                <span class="res-ref-tag dorm-ref">ATI-BK-2026-1055</span>
                <div style="font-size: 0.82rem; font-weight: 700; color: #1e40af; margin-top: 0.2rem;">Dormitory Suites A &amp; B</div>
              </td>
              <td data-label="Lodging Purpose">
                <div style="font-weight: 800; color: #142e20;">National Young Farmers Camp Participant Lodging</div>
                <div style="font-size: 0.78rem; color: #556e5f;">Partnership &amp; Accreditation Division (PAD) &bull; 24 Delegates (12 Rooms)</div>
              </td>
              <td data-label="Stay Duration">
                <div style="font-size: 0.85rem; font-weight: 700; color: #173222;">Nov 10 – 14, 2026 (4 Nights)</div>
                <div style="font-size: 0.76rem; color: #617b6c;">In: 02:00 PM • Out: 11:00 AM</div>
              </td>
              <td data-label="Custodian Status">
                <span class="res-status-badge pending"><span class="status-dot-pulse"></span><span>Custodian Review (Step 3/4)</span></span>
              </td>
              <td style="text-align: right;" data-label="Actions">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <button type="button" class="btn-card-action secondary js-view-details">Details</button>
                  <button type="button" class="btn-card-action secondary js-download-slip" data-ref="ATI-BK-2026-1055">Slip</button>
                </div>
              </td>
            </tr>

            <tr class="res-table-row" data-status="approved" data-category="dormitory" data-venue="exec-wing">
              <td data-label="Ref & Wing">
                <span class="res-ref-tag dorm-ref">ATI-RES-2026-0941</span>
                <div style="font-size: 0.82rem; font-weight: 700; color: #1e40af; margin-top: 0.2rem;">Dormitory (Executive Wing)</div>
              </td>
              <td data-label="Lodging Purpose">
                <div style="font-weight: 800; color: #142e20;">Guest Lecturers & Resource Persons Lodging</div>
                <div style="font-size: 0.78rem; color: #556e5f;">Career Development Division (CDD) &bull; 8 Speakers (4 Rooms)</div>
              </td>
              <td data-label="Stay Duration">
                <div style="font-size: 0.85rem; font-weight: 700; color: #173222;">Oct 05 – 07, 2026 (2 Nights)</div>
                <div style="font-size: 0.76rem; color: #617b6c;">In: 02:00 PM • Out: 12:00 PM</div>
              </td>
              <td data-label="Custodian Status">
                <span class="res-status-badge approved"><span class="status-dot-pulse"></span><span>Keys Allocation Ready</span></span>
              </td>
              <td style="text-align: right;" data-label="Actions">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <button type="button" class="btn-card-action primary" onclick="showGatePassModal('ATI-RES-2026-0941', 'Guest Lecturers & Resource Persons Lodging', 'ATI Dormitory Executive Wing', 'Oct 05 - 07, 2026')">Pass &amp; QR</button>
                  <button type="button" class="btn-card-action secondary js-view-details">Details</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- EMPTY STATE (SEARCH / FILTER MISMATCH) -->
      <div class="empty-reservations-box" id="emptyReservationsState" style="display: none;">
        <div class="empty-icon-wrap">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </div>
        <h3>No Dormitory Bookings Found</h3>
        <p>No dormitory lodging requests match your current filters. Try resetting the status or room search parameters.</p>
        <button type="button" class="btn-reset-filters" id="btnResetFilters">Reset All Filters</button>
      </div>

    </section>

  </main>

  <!-- ==========================================================================
       MODAL: SECURITY GATE PASS & QR CODE (FULL SCREEN ON MOBILE)
       ========================================================================== -->
  <div class="modal-backdrop" id="gatePassModal" style="display: none;">
    <div class="modal-dialog gate-pass-dialog" role="dialog" aria-modal="true">
      <div class="modal-header">
        <div class="modal-header-brand">
          <img src="assets/images/ATI_Logo.png" alt="ATI Logo">
          <div>
            <h3>Official Security Gate Pass &amp; Entry QR</h3>
            <p>Agriculture Training Institute &bull; Security &amp; Custodian Control</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" onclick="closeGatePassModal()" aria-label="Close modal">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="modal-body pass-body">
        <div class="pass-verified-ribbon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <span>AUTHORIZED ENTRY &bull; ROOM KEYS RELEASE CLEARANCE</span>
        </div>

        <div class="pass-content-split">
          <div class="pass-qr-side">
            <div class="qr-canvas-frame">
              <svg viewBox="0 0 100 100" class="simulated-qr">
                <rect x="0" y="0" width="100" height="100" fill="#ffffff" />
                <rect x="5" y="5" width="26" height="26" fill="#174d2f" />
                <rect x="8" y="8" width="20" height="20" fill="#ffffff" />
                <rect x="11" y="11" width="14" height="14" fill="#174d2f" />
                <rect x="69" y="5" width="26" height="26" fill="#174d2f" />
                <rect x="72" y="8" width="20" height="20" fill="#ffffff" />
                <rect x="75" y="11" width="14" height="14" fill="#174d2f" />
                <rect x="5" y="69" width="26" height="26" fill="#174d2f" />
                <rect x="8" y="72" width="20" height="20" fill="#ffffff" />
                <rect x="11" y="75" width="14" height="14" fill="#174d2f" />
                <rect x="36" y="8" width="6" height="6" fill="#174d2f" />
                <rect x="46" y="8" width="6" height="6" fill="#174d2f" />
                <rect x="56" y="8" width="6" height="6" fill="#174d2f" />
                <rect x="36" y="18" width="6" height="6" fill="#174d2f" />
                <rect x="50" y="24" width="8" height="8" fill="#174d2f" />
                <rect x="8" y="38" width="6" height="6" fill="#174d2f" />
                <rect x="20" y="44" width="6" height="6" fill="#174d2f" />
                <rect x="38" y="38" width="24" height="24" fill="#174d2f" />
                <rect x="42" y="42" width="16" height="16" fill="#ffffff" />
                <rect x="46" y="46" width="8" height="8" fill="#174d2f" />
                <rect x="70" y="38" width="6" height="6" fill="#174d2f" />
                <rect x="84" y="44" width="6" height="6" fill="#174d2f" />
                <rect x="36" y="70" width="6" height="6" fill="#174d2f" />
                <rect x="50" y="76" width="6" height="6" fill="#174d2f" />
                <rect x="70" y="70" width="8" height="8" fill="#174d2f" />
                <rect x="82" y="82" width="8" height="8" fill="#174d2f" />
              </svg>
            </div>
            <span class="qr-label-ref" id="passModalRefCode">ATI-RES-2026-0941</span>
            <span class="qr-subhint">Scan at Dormitory Lobby or Central Gate</span>
          </div>

          <div class="pass-info-side">
            <div class="pass-info-block">
              <span class="lbl">Lodging Purpose</span>
              <strong class="val" id="passModalTitle">Guest Lecturers & Resource Persons Lodging</strong>
            </div>

            <div class="pass-info-row-2">
              <div class="pass-info-block">
                <span class="lbl">Designated Wing &amp; Rooms</span>
                <strong class="val" id="passModalVenue">ATI Dormitory Executive Wing (Rooms 201-204)</strong>
              </div>
              <div class="pass-info-block">
                <span class="lbl">Stay Dates</span>
                <strong class="val" id="passModalDates">Oct 05 - 07, 2026</strong>
              </div>
            </div>

            <div class="pass-security-notes">
              <div class="note-item">
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Present this digital pass at the Lobby Desk to claim registered room keys.</span>
              </div>
              <div class="note-item">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span>Curfew: 10:00 PM. Visitors must sign the custodian registry logbook.</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-modal-secondary" onclick="closeGatePassModal()">Close</button>
        <button type="button" class="btn-modal-primary" onclick="window.print()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
          <span>Print / Save Pass</span>
        </button>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       MODAL: DORMITORY DETAILS
       ========================================================================== -->
  <div class="modal-backdrop" id="detailsModal" style="display: none;">
    <div class="modal-dialog" role="dialog" aria-modal="true">
      <div class="modal-header">
        <div class="modal-header-brand">
          <img src="assets/images/ATI_Logo.png" alt="ATI Logo">
          <div>
            <h3 id="detModalTitle">Dormitory Booking Details</h3>
            <p id="detModalSubtitle">Agricultural Training Institute &bull; Accommodation Record</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" onclick="closeDetailsModal()" aria-label="Close modal">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="modal-body" id="detModalBody">
        <!-- Content populated via JavaScript -->
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-modal-secondary" onclick="closeDetailsModal()">Close</button>
        <button type="button" class="btn-modal-primary" id="detBtnPrintSlip">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          <span>Download Lodging Slip</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer" aria-live="polite"></div>

  <!-- JavaScript -->
  <script src="js/my_reservations.js?v=<?php echo time(); ?>"></script>
  <script src="js/user_settings.js?v=<?php echo time(); ?>"></script>
  <script>
    function toggleMobileDrawer(open) {
      var overlay = document.getElementById('mobileDrawerOverlay');
      var toggleBtn = document.getElementById('mobileMenuToggle');
      if (!overlay) return;
      if (open) {
        overlay.style.display = 'block';
        requestAnimationFrame(function() { overlay.classList.add('show'); });
        document.body.style.overflow = 'hidden';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
      } else {
        overlay.classList.remove('show');
        document.body.style.overflow = '';
        setTimeout(function() { overlay.style.display = 'none'; }, 260);
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
      }
    }
  </script>
</body>
</html>
